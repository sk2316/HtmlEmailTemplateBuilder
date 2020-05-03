<?php 
	
	session_start();

	$GLOBALS['FOLDER_ID'] = '';
	$GLOBALS['HTML_DATA'] = '';


	function storeData($HTML){
		if($HTML != null || $HTML != ''){
			$_SESSION['HTMLValue'] = $HTML;
			return true;
		}else{
			// echo "no data";
			return false;
		}
	}	

	function createFolderForEmailTemplates(){
		$state = $_SESSION['state'];
		$access_token = $state->token;
		$instance_url = $state->instanceURL;

		$data = [	
					"Name"			=> "HTML Email Template Builder",
					"DeveloperName" => "HtmlEmailTemplateBuilder",
					"AccessType" 	=> "Public",
					"Type" 			=> "Email"
				];

		
		$query_url = $instance_url.'/services/data/v48.0/sobjects/Folder/';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $query_url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: OAuth '.$access_token, 'Content-type: application/json'));

		$response = curl_exec($ch)
		    or die("Query API call failed: '$query_url'");

		$responseArray = json_decode($response, true);
		print_r($responseArray);
		if((int)$responseArray['success'] == 1){
			$GLOBALS['FOLDER_ID'] = $responseArray['Id'];
			startUploading($GLOBALS['HTML_DATA']);
		}else{
			echo "Error creating Folder";
		}
	}

	function checkForOurFolder(){
		$state = $_SESSION['state'];
		$access_token = $state->token;
		$instance_url = $state->instanceURL;

		$query_url = $instance_url.'/services/data/v48.0/query';
		$query_url .= '?q='.urlencode('select id, name, DeveloperName from Folder where DeveloperName = \'HtmlEmailTemplateBuilder\' ');

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $query_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: OAuth '.$access_token));
		
		$reponse = curl_exec($ch)
		    or die("Query API call failed: '$query_url'");

		
		$responseArray = json_decode($reponse, true);


		if((int)$responseArray['totalSize'] == 0){
			return false; 
		}else{
			foreach ($responseArray['records'] as $record) {
				$GLOBALS['FOLDER_ID'] = (string)$record['Id'];
			}
			return true;
		}
	}

	function startUploading($HTML){
		$GLOBALS['HTML_DATA'] = $HTML;
		if(!checkForOurFolder()){
			createFolderForEmailTemplates();
		}else{
			$state = $_SESSION['state'];
			$access_token = $state->token;
			$instance_url = $state->instanceURL;

			$data = [
						'Name' 			=> 	'Test Template'.date("d-m-Y H:i:s"),
						'DeveloperName' =>	'TestTemplate'.rand(),
						'Body' 			=> 	$HTML,
						'HtmlValue' 	=>	$HTML,
						'IsActive'  	=> 	true,
						'FolderId'		=> 	$GLOBALS['FOLDER_ID'],
						'TemplateType'	=> 	'Custom'
					];

			
			$query_url = $instance_url.'/services/data/v48.0/sobjects/EmailTemplate/';

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $query_url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: OAuth '.$access_token, 'Content-type: application/json'));

			$response = curl_exec($ch)
			    or die("Query API call failed: '$query_url'");

			$responseArray = json_decode($response, true);
			?>
			<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
			<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.0/jquery.min.js"></script>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
			<script>
				$.confirm({
				    title: 'Hooray!!',
				    content: 'Email Template Saved to Salesforce!',
				    buttons: {
				        confirm: function () {
				            window.location.href = '/EmailTemp';
				        }
				    }
				});
			</script>



			<?php
		}
	}

?>