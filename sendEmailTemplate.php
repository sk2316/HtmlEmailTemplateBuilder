<?php	
	include 'index2.php';
    error_reporting(E_ALL & ~E_NOTICE);
    ini_set('display_errors', 1);
	
    session_start();


    
    // Define our State class
    class State 
    {   
        public $passthroughState1;  // Arbitary state we want to pass to the Authentication request
        public $passthroughState2;  // Arbitary state we want to pass to the Authentication request
 
        public $code;               // Authentication code received from Salesforce
        public $token;              // Session token
        public $refreshToken;       // Refresh token
        public $instanceURL;        // Salesforce Instance URL
        public $userId;             // Current User Id
         
        public $codeVerifier;       // 128 bytes of random data used to secure the request
 
        public $error;              // Error code
        public $errorDescription;   // Error description
 
        /**
         * Constructor - Takes 2 pieces of optional state we want to preserve through the request
         */
        function __construct($state1 = "", $state2 = "")
        {
            // Initialise arbitary state
            $this->passthroughState1 = $state1;
            $this->passthroughState2 = $state2;
 
            // Initialise remaining state
            $this->code = "";
            $this->token = "";
            $this->refreshToken = "";
            $this->instanceURL = "";
            $this->userId = "";
             
            $this->error = "";
            $this->errorDescription = "";
 
            // Generate 128 bytes of random data
            $this->codeVerifier = bin2hex(openssl_random_pseudo_bytes(128));


        }


        /**
         * Helper function to populate state following a call back from Salesforce
         */
        function loadStateFromRequest()
        {
            $stateString = "";
 
            // If we've arrived via a GET request, we can assume it's a callback from Salesforce OAUTH
            // so attempt to load the state from the parameters in the request
            if ($_SERVER["REQUEST_METHOD"] == "GET") 
            {
                $this->code = $this->sanitizeInput($_GET["code"]);
                $this->error = $this->sanitizeInput($_GET["error"]);
                $this->errorDescription = $this->sanitizeInput($_GET["error_description"]);
                $stateString = $this->sanitizeInput($_GET["state"]);
 
                // If we have a state string, then deserialize this into state as it's been passed
                // to the salesforce request and back
                if ($stateString)
                {
                    $this->deserializeStateString($stateString);
                }
            }
        }
 
        /**
         * Helper function to sanitize any input and prevent injection attacks
         */
        function sanitizeInput($data) 
        {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }
 
        /**
         * Helper function to serialize our arbitary state we want to send accross the request
         */
        function serializeStateString()
        {
            $stateArray = array("passthroughState1" => $this->passthroughState1, 
                                "passthroughState2" => $this->passthroughState2
                                );
 
            return rawurlencode(base64_encode(serialize($stateArray)));
        }
 
        /**
         * Helper function to deserialize our arbitary state passed back in the callback
         */
        function deserializeStateString($stateString)
        {
            $stateArray = unserialize(base64_decode(rawurldecode($stateString)));
 
            $this->passthroughState1 = $stateArray["passthroughState1"];
            $this->passthroughState2 = $stateArray["passthroughState2"];
        }
 
        /**
         * Helper function to generate the code challenge for the code verifier
         */
        function generateCodeChallenge()
        {
            $hash = pack('H*', hash("SHA256", $this->generateCodeVerifier()));
 
            return $this->base64url_encode($hash);
        }
 
        /**
         * Helper function to generate the code verifier
         */
        function generateCodeVerifier()
        {
            return $this->base64url_encode(pack('H*', $this->codeVerifier));
        }
 
        /**
         * Helper function to Base64URL encode as per https://tools.ietf.org/html/rfc4648#section-5
         */
        function base64url_encode($string)
        {
            return strtr(rtrim(base64_encode($string), '='), '+/', '-_');
        }
 
        /**
         * Helper function to display the current state values
         */
        function debugState($message = NULL)
        {
            if ($message != NULL)
            {
                echo "<pre>$message</pre>";
            }
         }
    }
 
    // If we have not yet initialised state, are resetting or are Authenticating then Initialise State
    // and store in a session variable.
    if ($_SESSION['state'] == NULL || $_POST["reset"])
    {
        $_SESSION['state'] = new State('ippy', 'dippy');
    }
 
    $state = $_SESSION['state'];
 
    // Attempt to load the state from the page request
    $state->loadStateFromRequest();
 
    // if an error is present, render the error
    if ($state->error != NULL)
    {
        renderError();      
    }

    
    ?>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">

        <input type="submit" name="login_via_code" class="btn btn-success loginViaAuthenticationBtn" value="Login via Authentication Code" style="display: none;" />
    </form>
    <?php


     if ($_POST["authenticate"]) // Authenticate button clicked
    {   
        if(storeData($_POST['HTMLBody'])){
            doOAUTH();
        }else{echo "Error occured!";}
    }
    else if ($_POST["login_via_code"])  // Login via Authentication Code button clicked
    {
        if (!loginViaAuthenticationCode())
        {
            renderError();
            return;
        }else{
            startUploading($_SESSION['HTMLValue']);

        }
        
    }
    else    // Otherwise render the page
    {
        renderPartialData();
    }
 
    /*
    * function to display some of the data on screen while other data in being loaded in backgroung
    */
    function renderPartialData($userDataHTML = NULL){

        $state = $_SESSION['state'];

?>          

                
<?php        
                    
        if ($userDataHTML)
        {
            echo $userDataHTML;

        }
    }

    
    /**
     * Redirect page to Salesforce to authenticate
     */
    function doOAUTH()
    {
        $state = $_SESSION['state'];
        // Set the Authentication URL
        // Note we pass in the code challenge

        if($_POST["type_of_org"] == 'production'){
            $_SESSION["orgType"] = "production";
            $href = "https://login.salesforce.com/services/oauth2/authorize?response_type=code";    

        }else if($_POST["type_of_org"] == 'sandbox'){
            $_SESSION["orgType"] = "sandbox";
            $href = "https://test.salesforce.com/services/oauth2/authorize?response_type=code";
        }
        

        // $href = "https://login.salesforce.com/services/oauth2/authorize?response_type=code"; 
        $href .= "&client_id=" . getClientId() . 
                "&redirect_uri=" . getCallBackURL() . 
                "&scope=api refresh_token" . 
                "&prompt=consent" . 
                "&code_challenge=" . $state->generateCodeChallenge() .
                "&state=" . $state->serializeStateString();
 
        // Wipe out arbitary state values to demonstrate passing additional state to salesforce and back
        $state->passthroughState1 = NULL;
        $state->passthroughState2 = NULL;

        // Perform the redirect
        header("location: $href");
    }
 
    /**
     * Login via an Authentication Code
     */
    function loginViaAuthenticationCode()
    {
        $state = $_SESSION['state'];
 
        // Create the Field array to pass to the post request
        // Note we pass in the code verifier and the authentication code
        $fields = array('grant_type' => 'authorization_code', 
                        'client_id' => getClientId(),
                        'client_secret' => getClientSecret(),
                        'redirect_uri' => getCallBackURL(),
                        'code_verifier' => $state->generateCodeVerifier(),
                        'code' => $state->code,
                        );
         
        // perform the login to Salesforce
        return doLogin($fields, false);
    }
 
    /**
     * Login to Salesforce to get a Session Token using CURL
     */

    function doLogin($fields, $isViaRefreshToken)
    {
        $state = $_SESSION['state'];

        if($_SESSION["orgType"] == 'production'){
            $postURL = 'https://login.salesforce.com/services/oauth2/token';
        }else if($_SESSION["orgType"] == 'sandbox'){
            $postURL = 'https://test.salesforce.com/services/oauth2/token';
        }
        
 
        // Header options
        $headerOpts = array('Content-type: application/x-www-form-urlencoded');
 
        // Create the params for the POST request from the supplied fields  
        $params = "";
         
        foreach($fields as $key=>$value) 
        { 
            $params .= $key . '=' . $value . '&';
        }
 
        $params = rtrim($params, '&');
 
        // Open the connection
        $ch = curl_init();
 
        // Set the url, number of POST vars, POST data etc
        curl_setopt($ch, CURLOPT_URL, $postURL);
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headerOpts);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 
        // Execute POST
        $result = curl_exec($ch);
 
        // Close the connection
        curl_close($ch);
 
        //record the results into state
        $typeString = gettype($result);
        $resultArray = json_decode($result, true);
 
        $state->error = $resultArray["error"];
        $state->errorDescription = $resultArray["error_description"];
 
        // If there are any errors return false
        if ($state->error != null)
        {
            return false;
        }
 
        $state->instanceURL = $resultArray["instance_url"];
        $state->token = $resultArray["access_token"];
 
        // If we are logging in via an Authentication Code, we want to store the 
        // resulting Refresh Token
        if (!$isViaRefreshToken)
        {
            $state->refreshToken = $resultArray["refresh_token"];
        }
 
        // Extract the user Id
        if ($resultArray["id"] != null)
        {
            $trailingSlashPos = strrpos($resultArray["id"], '/');
 
            $state->userId = substr($resultArray["id"], $trailingSlashPos + 1);
        }
 
        // verify the signature
        $baseString = $resultArray["id"] . $resultArray["issued_at"];
        $signature = base64_encode(hash_hmac('SHA256', $baseString, getClientSecret(), true));
 
        if ($signature != $resultArray["signature"])
        {
            $state->error = 'Invalid Signature';
            $state->errorDescription = 'Failed to verify OAUTH signature.';
 
            return false;
        }
 
        return true;
    }
 

    /**
     * Helper function to render an Error
     */
    function renderError()
    {
        $state = $_SESSION['state'];
 
        echo '<div class="error"><span class="error_msg">' . $state->error . '</span> <span class="error_desc">' . $state->errorDescription . '</span></div>';
    }
 
    /**
     * Get the hard coded Client Id for the Conected Application
     */
    function getClientId()
    {
        // return "3MVG9G9pzCUSkzZuwNy2sUFMBRAu9r5GGQVC_h0M.nFfAcXgnBJ.t1dtRvReZlXrj.xChH6FrmRKJ7JT8yWe9";
        return "3MVG9G9pzCUSkzZuwNy2sUFMBRGqEFeIZdE8HTdlvWlMIy8hlI8oct5tFfZeUc5adCklZIbVHD696jgKDIBIm";
    }
 
    /**
     * Get the hard coded Client Secret for the Conected Application
     */
    function getClientSecret()
    {
        // return "3F389C366C84CCE862377D3759F388BD48AA1AFCAD3F82B9F0BE47AA30E225DF";
        return "86F704E7155FC060F86BFB4027D47C0502B20058E82339C0CFD40395DBC399EA";
    }
 
    /**
     * Get the Call back URL (the current php script)
     */
    function getCallBackURL()
    {
        $callbackURL = ($_SERVER['HTTPS'] == NULL || $_SERVER['HTTPS'] == false ? "http://" : "https://") .
            $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
 
        return $callbackURL;
    }
?>

<head>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.0/jquery.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>

	<script>
		$(document).ready(function(){
			var url = window.location.href;
	        url = new URL(url);
	        

	        var codePresent = url.searchParams.get("code");
	        var stateNotNull = url.searchParams.get("state");

	        if(codePresent != null || stateNotNull != null){
	            $(".loginViaAuthenticationBtn").click();
	        }
		});
	</script>
</head>
