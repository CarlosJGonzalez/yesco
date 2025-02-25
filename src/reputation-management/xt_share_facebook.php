<?php
// required headers
header("Content-Type: application/json; charset=UTF-8");


// include database and object files

include_once __DIR__."/../includes/MysqliDb.php";
	
include_once __DIR__."/../connect.php";

include_once __DIR__."/../functions.php";
	
require_once __DIR__."/../facebook-ads-api/vendor/autoload.php";

use FacebookAds\Api;

$path_cert = __DIR__."/../facebook-ads-api/vendor/facebook/php-ads-sdk/fb_ca_chain_bundle.crt";

$data = $_POST;	

$db_link = new MysqliDb($servername, $username, $password,"facebook_post");
	
$storeObj = $db_link->where("store_id", $data["storeid"], "=")
					->where("client", $data["client"]."-%", "LIKE")
					->get("fb_pages", 1, "*");

if( !count($storeObj)){
	echo json_encode(["success"=>false,
					"error_code"=>"facebook_access_needed", 
					"error_msg"=>"Something went wrong posting on Facebook. Please, contact your Account Rep."]);
	exit;
}
					
Api::init(FB_API_KEY,FB_SECRET_TOKEN,APP_SECRET_TOKEN)->getHttpClient()->setCaBundlePath($path_cert);

$adsAPI = Api::instance();

$ext_token_validation = $adsAPI->call("/debug_token", "GET",  array("input_token"=>FB_ACCESS_TOKEN,
													"access_token"=>APP_SECRET_TOKEN));

$id_page = $storeObj[0]["id_page"];
$page_access_token = $storeObj[0]["access_token"];	
									
if(!($is_user_token_valid = debugFBAccessToken($ext_token_validation))){
	$error = ["type"=>'user_token_not_valid'];
}
Api::init(FB_API_KEY,FB_SECRET_TOKEN,FB_ACCESS_TOKEN)->getHttpClient()->setCaBundlePath($path_cert);
$adsAPI = Api::instance();
$page_token_validation = $adsAPI->call("/debug_token", "GET",  array("input_token"=>$page_access_token,
													"access_token"=>FB_ACCESS_TOKEN));

if($is_user_token_valid && !($is_page_token_valid = debugFBAccessToken($page_token_validation))){	
	
	try{
		$rq = $adsAPIGlobal->call("/".$id_page, 'GET', ['fields'=>'access_token']);		
		$page_access_token =  $rq->getContent()['access_token'];
		$is_page_token_valid = true;		
		
	}catch (FacebookAds\Exception $exc) {
		$error_type='page_token_not_valid';
    }	
}

Api::init(FB_API_KEY,FB_SECRET_TOKEN,$page_access_token)->getHttpClient()->setCaBundlePath($path_cert);
$adsAPI = Api::instance();

if($data["xt"] == "share"){
	$endpoint = "/$id_page/photos";	
	$post_data = ["url"=>$data["img_url"],
			//"scheduled_publish_time"=> strtotime("2018-05-31 16:00:00"),
			"published"=>true,
			//"access_token" => $page_access_token
	];	
	$error_text = "Something went wrong sharing review on Facebook. Please, try again.";
	$success_msg = "Share review successfully posted on Facebook.";
}
if($data["xt"] == "reply"){	
	$endpoint = "/".$data["review_id"]."/comments";	
	$post_data = ["message"=>$data["review_answer"]];	
	$error_text = "Something went wrong responding review on Facebook. Please, try again.";
	$success_msg = "Respond review successfully posted on Facebook.";

}

try{
		if($is_user_token_valid && $is_page_token_valid){
			$rsp = $adsAPI->call($endpoint, 'POST', $post_data);
			$id = $rsp->getContent()["id"];
		}
		
	}catch (FacebookAds\Exception $exc) {	
		$error_type = 'facebook_post_exception';
    }
									
if( ($data["xt"] == "reply" ) && isset($id) && ($id != "") && !isset($error_type)){
	$db_link->where("id",$data["review_id"], "=")
			->update("facebook_post.fb_reviews", ["answer"=>$data["review_answer"], 
												"answer_id"=>$rsp->getContent()["id"], 
												"answer_date"=>date("Y-m-d H:i:s")]);
}
echo isset($error_type)?json_encode(["success"=>false,"error_code"=>$error_type, "error_msg"=>$error_text]):json_encode(["success"=>true, "success_msg"=>$success_msg ]);

