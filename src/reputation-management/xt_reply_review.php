<?php
	date_default_timezone_set('America/New_York');
	session_start();
	include_once ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
	include_once ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");
	
	$data = $_POST;
	unset($_SESSION["error"],$_SESSION['success']);
	
	if($data["txt_reply"] == ""){
		header('location: /reputation-management/review-stream.php');
		exit;
	}
	
	$data_post = [
					'review_id'		=> ltrim(base64_decode($data['review_id']),$client),
					'review_answer' => $data['txt_reply'],
					"storeid"		=>$_SESSION["storeid"],
					"client"		=>$_SESSION["client"],
					'xt'			=> 'reply'
				];
					
	if($data['review_portal'] == 'facebook'){
		$url = 	'http://site24.das-group.com/fb_reviews/review_stream/xt_share_facebook.php';
	}
	
	if($data['review_portal'] == 'google'){		
		$url = 'http://site24.das-group.com/fb_reviews/review_stream/xt_share_google.php';
	}


	//var_dump($data); exit;
	$ch = curl_init($url);	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch, CURLOPT_POST,count($data_post));
	curl_setopt($ch, CURLOPT_FRESH_CONNECT,1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($data_post));
	$response = curl_exec($ch);
	
	if(!curl_errno($ch)){		
		$content = json_decode($response, true);	
		if($content['success']){
			$_SESSION['success'] = $_SESSION['success'].$content['success_msg']."<br>";		
		}
	}	
	header('location: /reputation-management/review-stream.php');
	exit;

	
