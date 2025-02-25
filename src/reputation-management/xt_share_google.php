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

/*GMB includes*/
require __DIR__.'/../google-api-php-client-2.1.3/vendor/autoload.php';
require __DIR__.'/../google-api-php-client-2.1.3/mybusiness/mybusiness.php';
require __DIR__.'/../google-api-php-client-2.1.3/vendor/google/auth/src/OAuth2.php';

$data = $_POST;
//echo json_encode($_POST); exit;

$credentials_f = __DIR__.'/../google-api-php-client-2.1.3/client_id.json';

$db_link = new MysqliDb($servername, $username, $password,"facebook_post");

$account = $db_link->where("a.store_id", $data["storeid"], "=")
						->where("a.client", $data["client"], "=")
						->join("facebook_post.gmb_accounts b","a.parent_account=b.account_id","INNER")
						->get("facebook_post.gmb_locations a", 1, "a.*, b.refresh_token,b.email");

if( !count($account)){
	echo json_encode(["success"=>false,
					"error_code"=>"gmb_access_needed", 
					"error_msg"=>"Something went wrong posting on Google My Business. Please, contact your Account Rep."]);
	exit;
}						
$gmb_location = $account[0];

$client = new Google_Client();
$client->setApplicationName(APPLICATION_NAME);
$client->setDeveloperKey(DEVELOPER_TOKEN);
$client->setAuthConfig($credentials_f);  
$client->setScopes("https://www.googleapis.com/auth/plus.business.manage");
$client->setSubject($account[0]["email"]);   
$token = $client->refreshToken($account[0]["refresh_token"]);
$client->authorize();
$mybusinessService = new Google_Service_Mybusiness($client);

$reviews = $mybusinessService->accounts_locations_reviews;

$locationName = "accounts/".$account[0]["account_id"]."/locations/".$account[0]["location_id"];

if($data["xt"] == "reply"){
	try {
			$location = $mybusinessService->accounts_locations->get($locationName);				
			$reviewId = $data["review_id"];
			$reviewReply = new Google_Service_Mybusiness_ReviewReply();
			$comment = $data["review_answer"];
			$reviewReply->setComment($comment);
			$updatedReviewReply = $reviews->updateReply($location->name . "/reviews/" . $reviewId, $reviewReply);
		
	} catch (Exception $ex) {
		$error = ["success"=>false,
					"error_code"=>"google_my_business_expection",
					"error_msg"=>"Something went wrong responding review on Google My Business. Please, try again."
				];
	}
	
} 
if(isset($updatedReviewReply) && !isset($error) ){
		 $db_link->where("id",$data["review_id"] ,"=")
				->update("facebook_post.gmb_reviews", array("reply_comment"=>$comment, "reply_date"=> $updatedReviewReply->getUpdateTime()));
}
				
echo isset($error)?json_encode($error):json_encode(["success"=>true, "success_msg"=>"Respond review successfully posted on Google My Business."]);
exit;

