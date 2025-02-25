<?php
	date_default_timezone_set('America/New_York');
	session_start();
	include_once ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
	include_once ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");
	$upload_dir = $_SERVER['DOCUMENT_ROOT'] ."/img/reviews/";
	unset($_SESSION['error'],$_SESSION['success']);
	if(!file_exists($upload_dir)){
		mkdir($upload_dir, 0775, TRUE);
	}
	if( ($_POST['canvas_context'] == '') || !count($_POST['share']) ){	
		header('location: /reputation-management/review-stream.php');
		exit;
	}
	
	$data = $_POST['canvas_context'];
	list($type, $data) = explode(';', $data);
    list(, $data)      = explode(',', $data);
	$file =  $_SESSION['storeid'].round(microtime(true)) .'.png';
    $data = base64_decode($data);
	$result = file_put_contents($upload_dir.$file, $data);
	
	if(!$result){
		$_SESSION['error'] = 'Something went wrong. Please, try again.';
		header('location: /reputation-management/review-stream.php');
		exit;
		
	}
	
	$urls = [];
	if(array_search('facebook',$_POST['share']) !== FALSE){
		$urls[] = 'http://site24.das-group.com/fb_reviews/review_stream/xt_share_facebook.php';
	}
	if(array_search('google',$_POST['share']) !== FALSE){
		$urls[] = 'http://site24.das-group.com/fb_reviews/review_stream/xt_share_google.php';
	}
	if(array_search("twitter",$_POST['share']) !== FALSE){
		$urls[] = 'http://site24.das-group.com/fb_reviews/review_stream/xt_share_twitter.php';
	}
	
	$data_post = [	
					"file_path" =>$upload_dir.$file,
					"img_url"	=> getFullUrl()."/img/reviews/$file",
					"storeid"	=>$_SESSION["storeid"],
					"client"	=>$_SESSION["client"],
					"xt"		=>"share"
				];

					
	$channels = [];
	$mh = curl_multi_init();

	foreach ($urls as $key => $url) {
		// initiate individual channel
		$channels[$key] = curl_init();
		curl_setopt_array($channels[$key], array(
							CURLOPT_URL => $url,
							CURLOPT_RETURNTRANSFER => true,
							CURLOPT_FOLLOWLOCATION => true,
							//CURLOPT_HEADER=>true,
							CURLOPT_POST => count($data_post),
							CURLOPT_FRESH_CONNECT => true,
							CURLOPT_POSTFIELDS => http_build_query($data_post)	,
							CURLOPT_VERBOSE=> 1
			));

		// add channel to multihandler
		curl_multi_add_handle($mh, $channels[$key]);
	}
	// execute - if there is an active connection then keep looping
		
	do {
		curl_multi_exec($mh, $running);	
		curl_multi_select($mh);
	} while ($running > 0);
	// echo the content, remove the handlers, then close them
	foreach (array_keys($channels) as $key ) {
		/*echo curl_getinfo($channels[$key], CURLINFO_HTTP_CODE)."\n";
		echo curl_getinfo($channels[$key], CURLINFO_EFFECTIVE_URL)."\n";
		echo curl_error($channels[$key])."\n";*/		
		$content = json_decode(curl_multi_getcontent($channels[$key]), true);
		if($content["error_msg"] && ($content["error_msg"] != "")){
			$_SESSION['error'] = $_SESSION['error'].$content["error_msg"]."<br>";
		}else if($content["success"]){
			$_SESSION['success'] = $_SESSION['success'].$content["success_msg"]."<br>";		
		}	
		
		curl_multi_remove_handle($mh, $channels[$key]);
		curl_close($channels[$key]);
	}
	curl_multi_close($mh);
	header('location: /reputation-management/review-stream.php');
	exit;