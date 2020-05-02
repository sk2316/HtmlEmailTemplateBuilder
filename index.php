<?php
function include_script(string $url, string $version='', string $path=''){
	if(filter_var($url, FILTER_VALIDATE_URL)===false)
	{
		if(empty($path))
			$path = $url;
			
		$version = preg_replace("/[^0-9\.\_]/Ui","",$version);
		
		$id = '';
		if(file_exists($path) || file_exists(dirname(__FILE__).$path) || file_exists(dirname(__FILE__).'/'.$path))
		{
			$id = sprintf("?version=%d.%d.%d",filemtime($path),filesize($path),strlen($path));
		}
		else
		{
			if(!empty($version))
				$id = sprintf("?version=%s.%d",$version,strlen($url));
		}
	}
	else
	{
		if(!empty($version))
				$id = sprintf("?version=%s.%d",$version,strlen($url));
	}
	
	$url = $url.$id;
	
	printf('<script src="%s"></script>%s',$url,PHP_EOL);
}

function include_style(string $url, string $version='', string $path=''){
	if(filter_var($url, FILTER_VALIDATE_URL)===false)
	{
		if(empty($path))
			$path = $url;
			
		$version = preg_replace("/[^0-9\.\_]/Ui","",$version);
		
		$id = '';
		if(file_exists($path) || file_exists(dirname(__FILE__).$path) || file_exists(dirname(__FILE__).'/'.$path))
		{
			$id = sprintf("?version=%d.%d.%d",filemtime($path),filesize($path),strlen($path));
		}
		else
		{
			if(!empty($version))
				$id = sprintf("?version=%s.%d",$version,strlen($url));
		}
	}
	else
	{
		if(!empty($version))
				$id = sprintf("?version=%s.%d",$version,strlen($url));
	}
	
	$url = $url.$id;
	
	printf('<link href="%s" rel="stylesheet">%s',$url,PHP_EOL);
}
?><!DOCTYPE html>
<html lang="en">
  <head>
	  <script type="text/javascript">
	  (function(base, search, replace){
        
        window.start_time = Math.round(new Date().getTime()/1000);
          
        var extend = function(a,b){
            for(var key in b)
                if(b.hasOwnProperty(key))
                    a[key] = b[key];
            return a;
        }, refactor = function(){
            
            if(!replace)
                replace = true;
            
            var elements = extend({
                    script : 'src',
                    img    : 'src',
                    link   : 'href',
                    a      : 'href',
                }, search),
                generateID = function (min, max) {
                    min = min || 0;
                    max = max || 0;

                    if(
						min===0
						|| max===0
						|| !(typeof(min) === "number"
						|| min instanceof Number)
						|| !(typeof(max) === "number"
						|| max instanceof Number)
					) 
                        return Math.floor(Math.random() * 999999) + 1;
                    else
                        return Math.floor(Math.random() * (max - min + 1)) + min;
                };
			
			var baseURL = window.location.protocol + '//' + window.location.hostname + base;

			if (localStorage.getItem("session_id"))
			{
				window.session_id = localStorage.getItem("session_id");
			}
			else
			{
				var generate = new Date().getTime() + '-' + generateID(10000,99999) + '' + generateID(100000,999999) + '' + generateID(1000000,9999999) + '' + generateID(10000000,99999999);
				window.session_id = generate;
				localStorage.setItem("session_id",generate);
			}
            
            localStorage.setItem("baseURL",baseURL);
            window.base = baseURL;
            
			for(tag in elements)
			{
				var list = document.getElementsByTagName(tag)
					listMax = list.length;
				if(listMax>0)
				{
					for(i=0; i<listMax; i++)
					{
						var src = list[i].getAttribute(elements[tag]);
						if(
							!(/^(((a|o|s|t)?f|ht)tps?|s(cp|sh)|as2|chrome|about|javascript)\:(\/\/)?([a-z0-9]+)?/gi.test(src))
							&& !(/^#\S+$/gi.test(src))
							&& '' != src
							&& null != src
							&& replace
						)
						{
							src = baseURL + '/' + src;
							list[i].setAttribute('src',src);
						}
					}
				}
			}
			
		}
		document.addEventListener("DOMContentLoaded", function() {
			refactor();
		});
    }('/EmailTemp'));

    if (localStorage.getItem("baseURL")){
        window.base = localStorage.getItem("baseURL");
	}
	if (localStorage.getItem("session_id")){
        window.session_id = localStorage.getItem("session_id");
	}
	/* ]]> */
    </script>
    
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>Email-Teamplet Creator</title>
    <?=include_style('assets/css/style-sk.css'); ?>
    <?=include_style('assets/css/bootstrap.min.css'); ?>
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
	<?=include_style('assets/css/bootstrap-colorpicker.min.css'); ?>
    <?=include_style('assets/css/bootstrap-slider.min.css'); ?>
    <?=include_style('assets/plugins/medium-editor/medium-editor.min.css'); ?>
	<?=include_style('assets/plugins/medium-editor/template.min.css'); ?>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/spectrum/1.8.0/spectrum.min.css">
    <?=include_style('assets/css/style.css'); ?>
    
    <!-- Font Awesome icons (free version)-->
        <script src="https://use.fontawesome.com/releases/v5.12.1/js/all.js" crossorigin="anonymous"></script>
        <!-- Google fonts-->
        <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css" />
        <link href="https://fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic" rel="stylesheet" type="text/css" />
        <!-- Core theme CSS (includes Bootstrap)-->

     
  </head>
  <body  id="page-top">
    <!-- Navigation-->
        <nav class="navbar navbar-expand-lg bg-secondary text-uppercase fixed-top nav-fixed custom-bg scroller" id="mainNav" style="margin-bottom: 0; border-radius: 0;">
            <div class="container">
                <a class="navbar-brand js-scroll-trigger" href="#page-top">Html Email Builder</a><button class="navbar-toggler navbar-toggler-right text-uppercase font-weight-bold bg-primary text-white rounded" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">Menu <i class="fas fa-bars"></i></button>
                <div class="collapse navbar-collapse" id="navbarResponsive">
                    <ul class="navbar-nav ml-auto nav-options" style="float: right;">
                        <li class="nav-item mx-0 mx-lg-1"><a class="nav-link py-3 px-0 px-lg-3 rounded js-scroll-trigger" href="#choose-template">Templates</a></li>
                        <li class="nav-item mx-0 mx-lg-1"><a class="nav-link py-3 px-0 px-lg-3 rounded js-scroll-trigger" href="#about">About</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        <!-- Masthead-->
        <header class="masthead bg-primary text-white text-center hidden" style="background-image: linear-gradient(135deg, #0061f2 0%, rgba(105, 0, 199, 0.8) 100%);">
            <div class="container d-flex align-items-center flex-column">
                <!-- Masthead Avatar Image--><img class="masthead-avatar mb-5" src="assets/img/drawkit-content-man-color.svg" alt="" /><!-- Masthead Heading-->
                <h1 class="masthead-heading text-uppercase mb-0">Html Email Builder</h1>
                <!-- Icon Divider-->
                <div class="divider-custom divider-light">
                    <div class="divider-custom-line"></div>
                    <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
                    <div class="divider-custom-line"></div>
                </div>
            </div>
        </header>
        
    <div class="" style="display: flex; height: 100%;">
    	<!-- Portfolio Section-->
        <section class="page-section portfolio" id="choose-template" style="width: 100%;">
            <div class="container">
                <!-- Portfolio Section Heading-->
                <h2 class="page-section-heading text-center text-uppercase text-secondary mb-0">Templates</h2>
                <!-- Icon Divider-->
                <div class="divider-custom">
                    <div class="divider-custom-line"></div>
                    <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
                    <div class="divider-custom-line"></div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <button style="width: 100%;" class="choose" type="button" data-id="no-sidebar"><img src="assets/img/no-sidebar.jpg" class="img-responsive" alt=""><span>No Sidebar (wide)</span></button>
                    </div>
                    <div class="col-md-3">
                        <button style="width: 100%;" class="choose" type="button" data-id="left-sidebar"><img src="assets/img/left-sidebar.jpg" class="img-responsive" alt=""><span>Left Sidebar</span></button>
                    </div>
                    <div class="col-md-3">
                        <button style="width: 100%;" class="choose" type="button" data-id="right-sidebar"><img src="assets/img/right-sidebar.jpg" class="img-responsive" alt=""><span>Right Sidebar</span></button>
                    </div>
                    <div class="col-md-3">
                        <button style="width: 100%;" class="choose" type="button" data-id="both-sidebar"><img src="assets/img/both-sidebar.jpg" class="img-responsive" alt=""><span>Both Sidebars</span></button>
                    </div>
                </div>
            </div>
        </section>
        <div class="container-sidebar hidden" id="option-tabs">

            <div id="get-options" class="text-center">
                <h4>TOOLBOX</h4>
                <div style="width: 50%;padding: 0px 0px 0px 0px;"  class="get-options choose" data-id="title" id="title"><span class="glyphicon glyphicon-text-size"></span><div>Heading</div></div>
                <div style="width: 50%;padding: 0px 0px 0px 0px;" class="get-options choose" data-id="content" id="content"><span class="glyphicon glyphicon-list-alt"></span><div>Text</div></div>
                <div style="width: 50%;padding: 0px 0px 0px 0px;" class="get-options choose" data-id="image" id="image"><span class="glyphicon glyphicon-picture"></span><div>Image</div></div>
                <div style="width: 50%;padding: 0px 0px 0px 0px;" class="get-options choose" data-id="video" id="video"><span class="glyphicon glyphicon-play"></span><div>Video</div></div>
                <div style="width: 50%;padding: 0px 0px 0px 0px;" class="get-options choose" data-id="link" id="link"><span class="glyphicon glyphicon-link"></span><div>Link</div></div>
                <div style="width: 50%;padding: 0px 0px 0px 0px;" class="get-options choose" data-id="divider" id="divider"><span class="glyphicon glyphicon-minus"></span><div>Divider</div></div>
             <div style="width: 50%;padding: 0px 0px 0px 0px;" class="get-options choose" data-id="quote" id="quote"><span class="glyphicon glyphicon-comment"></span><div>Blockquote</div></div> 
                <div id="editor"></div>
                <ul id="attach-data" class="list-group"></ul>
            </div>
            
        </div>
        <div class="container-content hidden" id="mail-template" style="padding-top: 50px; background-color: #e1e1e1;">
            Content
        </div>
        
    </div>

	<div id="modal" class="reset-this"></div>
    <button class="btn btn-lg btn-success btn-materialize btn-left-bottom btn-left-bottom-1 hidden" type="button" id="preview" title="Preview" data-toggle="tooltip" data-placement="top" data-trigger="hover"><span class="glyphicon glyphicon-zoom-in"></span></button>
      
    <form method="post" enctype="multipart/form-data" class="btn btn-lg btn-primary btn-materialize btn-left-bottom btn-left-bottom-2 hidden" type="button" id="attachment" title="Attachment 7Mb Max" data-toggle="tooltip" data-placement="top" data-trigger="hover" style="-webkit-appearance:none;"><span class="glyphicon glyphicon-paperclip"></span><input type="file" name="attachment[]"></form>
      
    <button class="btn btn-lg btn-default btn-materialize btn-left-bottom btn-left-bottom-3 hidden" type="button" id="setting" title="Layout Options" data-toggle="tooltip" data-placement="top" data-trigger="hover"><span class="fa fa-cog fa-spin"></span></button>      
      
    <div id="alerts"></div>
      
    <div class="tools tools-left" id="settings">
        <div class="tools-header">
            <button type="button" class="close" data-dismiss="tools" aria-label="Close"><span aria-hidden="true">×</span></button>
            <h4><span class="fa fa-cog fa-spin"></span>Settings</h4>
        </div>
        <div class="tools-body">
            <h5 class="text-left option-title">Layout</h5>
            <div class="form-horizontal">


                <div class="form-group">
                    <label for="body-layout-bkg-color-form" class="col-sm-6 control-label text-left">Background Color:</label>
                    <div class="col-sm-6">
                        <div id="body-layout-bkg-color" class="input-group colorpicker-component">
                            <span class="input-group-addon"><i></i></span>
                            <input type="text" value="" class="form-control input-sm" id="body-layout-bkg-color-form">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="body-layout-bkg-color-form" class="col-sm-6 control-label text-left">Body Color:</label>
                    <div class="col-sm-6">
                        <div id="body-layout-bkg-color-body" class="input-group colorpicker-component">
                            <span class="input-group-addon"><i></i></span>
                            <input type="text" value="" class="form-control input-sm" id="body-layout-bkg-color-body-form">
                        </div>
                    </div>
                </div>

            </div>

            <h5 class="text-left option-title">Header Section</h5>
            <div class="form-horizontal">

                <div class="form-group">
                    <label for="head-bkg-color-form" class="col-sm-6 control-label text-left">Background Color:</label>
                    <div class="col-sm-6">
                        <div id="head-bkg-color" class="input-group colorpicker-component">
                            <span class="input-group-addon"><i></i></span>
                            <input type="text" value="" class="form-control input-sm" id="head-bkg-color-form">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="head-height" class="col-sm-4 control-label text-left">Height:</label>
                    <div class="col-sm-8 text-right">
                        <input type="text" class="form-control input-sm" id="head-height" data-slider-min="0" data-slider-max="1000" data-slider-step="10" data-slider-value="0">&nbsp;&nbsp;&nbsp;<small>Height: <span id="head-height-val">auto</span></small>
                    </div>
                </div>

            </div>

            <div id="dd-body-exists">
                <h5 class="text-left option-title">Content Section</h5>
                <div class="form-horizontal">

                    <div class="form-group">
                        <label for="content-bkg-color-form" class="col-sm-6 control-label text-left">Background Color:</label>
                        <div class="col-sm-6">
                            <div id="content-bkg-color" class="input-group colorpicker-component">
                                <span class="input-group-addon"><i></i></span>
                                <input type="text" value="" class="form-control input-sm" id="content-bkg-color-form">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="content-height" class="col-sm-4 control-label text-left">Height:</label>
                        <div class="col-sm-8 text-right">
                            <input type="text" class="form-control input-sm" id="content-height" data-slider-min="0" data-slider-max="1000" data-slider-step="10" data-slider-value="0">&nbsp;&nbsp;&nbsp;<small>Height: <span id="content-height-val">auto</span></small>
                        </div>
                    </div>

                </div>
            </div>

            <div id="dd-sidebar-left-exists">
                <h5 class="text-left option-title">Left Sidebar Section</h5>
                <div class="form-horizontal">

                    <div class="form-group">
                        <label for="left-bkg-color-form" class="col-sm-6 control-label text-left">Background Color:</label>
                        <div class="col-sm-6">
                            <div id="left-bkg-color" class="input-group colorpicker-component">
                                <span class="input-group-addon"><i></i></span>
                                <input type="text" value="" class="form-control input-sm" id="left-bkg-color-form">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="left-height" class="col-sm-4 control-label text-left">Height:</label>
                        <div class="col-sm-8 text-right">
                            <input type="text" class="form-control input-sm" id="left-height" data-slider-min="0" data-slider-max="1000" data-slider-step="10" data-slider-value="0">&nbsp;&nbsp;&nbsp;<small>Height: <span id="left-height-val">auto</span></small>
                        </div>
                    </div>

                </div>
            </div>

            <div id="dd-sidebar-right-exists">
                <h5 class="text-left option-title">Right Sidebar Section</h5>
                <div class="form-horizontal">

                    <div class="form-group">
                        <label for="right-bkg-color-form" class="col-sm-6 control-label text-left">Background Color:</label>
                        <div class="col-sm-6">
                            <div id="right-bkg-color" class="input-group colorpicker-component">
                                <span class="input-group-addon"><i></i></span>
                                <input type="text" value="" class="form-control input-sm" id="right-bkg-color-form">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="right-height" class="col-sm-4 control-label text-left">Height:</label>
                        <div class="col-sm-8 text-right">
                            <input type="text" class="form-control input-sm" id="right-height" data-slider-min="0" data-slider-max="1000" data-slider-step="10" data-slider-value="0">&nbsp;&nbsp;&nbsp;<small>Height: <span id="right-height-val">auto</span></small>
                        </div>
                    </div>

                </div>
            </div>

            <h5 class="text-left option-title">Footer Section</h5>
            <div class="form-horizontal">

                <div class="form-group">
                    <label for="footer-bkg-color-form" class="col-sm-6 control-label text-left">Background Color:</label>
                    <div class="col-sm-6">
                        <div id="footer-bkg-color" class="input-group colorpicker-component">
                            <span class="input-group-addon"><i></i></span>
                            <input type="text" value="" class="form-control input-sm" id="footer-bkg-color-form">
                        </div>
                    </div>
                </div> 

                <div class="form-group">
                    <label for="footer-height" class="col-sm-4 control-label text-left">Height:</label>
                    <div class="col-sm-8 text-right">
                        <input type="text" class="form-control input-sm" id="footer-height" data-slider-min="0" data-slider-max="1000" data-slider-step="10" data-slider-value="0">&nbsp;&nbsp;&nbsp;<small>Height: <span id="footer-height-val">auto</span></small>
                    </div>
                </div>
            </div>
        </div>
        <div class="tools-footer">
            <div class="button-group text-center">
                <button class="btn btn-success btn-sm" data-dismiss="tools" type="button" id="send-message"><span class="glyphicon glyphicon-ok"></span>Save</button>
                <button class="btn btn-warning btn-sm" type="button" id="test"><span class="glyphicon glyphicon-envelope"></span> Send to Salesforce</button>
                <button class="btn btn-danger btn-sm" type="button" id="delete"><span class="glyphicon glyphicon-remove-sign"></span> Delete</button>
            </div>
        </div>
    </div>
     <!-- About Section-->
        <section class="page-section bg-primary text-white mb-0 hidden" id="about" style="background-image: linear-gradient(135deg, #0061f2 0%, rgba(105, 0, 199, 0.8) 100%);">
            <div class="container">
                <!-- About Section Heading-->
                <h2 class="page-section-heading text-center text-uppercase text-white">About</h2>
                <!-- Icon Divider-->
                <div class="divider-custom divider-light">
                    <div class="divider-custom-line"></div>
                    <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
                    <div class="divider-custom-line"></div>
                </div>
                <!-- About Section Content-->
                <div class="row">
                    <div class="col-lg-4 ml-auto"><p class="lead">Freelancer is a free bootstrap theme created by Start Bootstrap. The download includes the complete source files including HTML, CSS, and JavaScript as well as optional SASS stylesheets for easy customization.</p></div>
                    <div class="col-lg-4 mr-auto"><p class="lead">You can create your own custom avatar for the masthead, change the icon in the dividers, and add your email address to the contact form to make it fully functional!</p></div>
                </div>
                
            </div>
        </section>
        <!-- Contact Section-->
         <!-- Footer-->
        <footer class="footer text-center">
            <div class="container">
                <div class="row">
                    <!-- Footer Location-->
                    <div class="col-lg-4 mb-5 mb-lg-0">
                        <h4 class="text-uppercase mb-4">Location</h4>
                        <p class="lead mb-0">Bareilly</p>
                    </div>
                    <!-- Footer Social Icons-->
                    <div class="col-lg-4 mb-5 mb-lg-0">
                        <h4 class="text-uppercase mb-4">Around the Web</h4>
                        <a class="btn btn-outline-light btn-social mx-1" href="#"><i class="fab fa-fw fa-facebook-f"></i></a><a class="btn btn-outline-light btn-social mx-1" href="#"><i class="fab fa-fw fa-twitter"></i></a><a class="btn btn-outline-light btn-social mx-1" href="#"><i class="fab fa-fw fa-linkedin-in"></i></a><a class="btn btn-outline-light btn-social mx-1" href="#"><i class="fab fa-fw fa-dribbble"></i></a>
                    </div>
                    <!-- Footer About Text-->
                    <div class="col-lg-4">
                        <h4 class="text-uppercase mb-4">About Team</h4>
                        <p class="lead mb-0">Made by Delevlopers name Sourabh Kumar, Ajai Kumar, Sharatchi and Vivek.</p>
                    </div>
                </div>
            </div>
        </footer>
        <!-- Copyright Section-->
        <section class="copyright py-4 text-center text-white">
            <div class="container"><small>Copyright © bareillyteambeta 2020</small></div>
        </section>
    <script src="https://use.fontawesome.com/86c8941095.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/bootbox.min.js"></script>
    <script src="assets/js/debounce.js"></script>
    <script src="assets/js/bootstrap-colorpicker.min.js"></script>
    <script src="assets/js/bootstrap-slider.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/spectrum/1.8.0/spectrum.min.js"></script>
    <script src="//cdn.jsdelivr.net/medium-editor/latest/js/medium-editor.min.js"></script>
    <?=include_script('assets/js/creative.tools.js'); ?>
	<?=include_script('assets/js/html2canvas.js'); ?>
	<?=include_script('assets/js/image-edit.js'); ?>
    <?=include_script('assets/js/script-sk.js'); ?>
	<?=include_script('assets/js/editor.js'); ?>
    <script>
        $(window).scroll(function() {    
            var scroll = $(window).scrollTop();

             //>=, not <=
            if (scroll >= 500) {
                //clearHeader, not clearheader - caps H
                $(".scroller").removeClass("custom-bg");
            }
            else {
                $(".scroller").addClass("custom-bg");
            }
        });
    </script>
  </body>
</html>
