<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once __DIR__."/../includes/MysqliDb.php";	
include_once __DIR__."/../connect.php";
include_once __DIR__."/../functions.php";

/*Twitter API*/
require  __DIR__.'/../twitteroauth/twitterAPI.php';

$data = $_POST;	
//echo json_encode($_POST); exit;

$db_link = new MysqliDb($servername, $username, $password,"facebook_post");

$account = $db_link->where("a.store_id", $data["storeid"], "=")
					->where("a.client", $data["client"]."-%", "LIKE")
					->get("facebook_post.twitter_accounts a", 1, "a.*");

if( !count($account )){
	$error_type = "twitter_access_token_needed";
	$error_msg = "Something went wrong sharing review on Twitter. Please, contact your Account Rep.";
}					

 $twitter = new Tweet();  
 $twitter->switchAccount($account[0]["oauth_token"],
                    $account[0]["oauth_token_secret"]);
 
$upload = $twitter->uploadMedia($data["file_path"]);
 if(isset($upload->media_id_string)){
	 
	$media_id = $upload->media_id_string;
	$expires = $upload->expires_after_secs;
	$elem = ["status"=>"#QualityRepairs #FixItFast"];
	if(($media_id !== FALSE) || ($twitter->setMedia(["media_id" => $media_id]) !== FALSE)){
		 $tweet = $twitter->sendTweet($elem); 
		if(isset($tweet->id_str))
			$success = true;
	}else{
		$error_type = "twitter_media_exception";
		$error_msg = "Something went wrong sharing review on Twitter. Please, try again.";
		$success = false;
	}
	
 }
 
 echo (isset($success) && $success)? json_encode(["success"=>true, "success_msg" => "Share review successfully posted on Twitter."]):json_encode(["success"=>false, "error_code"=>$error_type, "error_msg"=>$error_msg]);
 exit;