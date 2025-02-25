<?php
session_start();
include_once ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include_once ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");

require_once ($_SERVER['DOCUMENT_ROOT']."/includes/DasApiSDK/vendor/autoload.php");
use Das\Client;

if(!$_SESSION["email"] && ($_SESSION["user_role_name"] != "admin_root" || $_SESSION["user_role_name"] != "admin_rep")){
	pageRedirect("Access denied: You must be authorized to view this page.", "error", "/");
}

$store_id = $_POST['storeid'];
$update_data = array();
$text = '';
$email_text = '';
$dataTrack = array();
$dataAct = array();
$username = $_SESSION['email'];

if($_POST['value']=="disable"){
	$text = "Enable";
	$email_text = "Disabled";
	
	$update_data = Array (
		'suspend' => '1',
		'adfundmember' => 'N'
	);
	
	$data_suspended_locs = Array (
		'storeid'=> $store_id
	);

	$db->insert ('inactive_locations', $data_suspended_locs);
	
	$clientObj = new Client($token_api);
	$clientObj->action( array( 
							"storeid"=> $store_id,
							"client"=> $_SESSION['client'],
							"action" => 2
						));
	
	$dataTrack = Array (
		"storeid"=>$store_id,
		'suspend' => '1',
		'adfundmember' => 'N'
	);
	
	$dataAct = array("username"=>$username,
					 "storeid"=>$_SESSION['storeid'],
					 "updates"=>json_encode($dataTrack),
					 "section"=>"profile",
					 "details"=>"Disabled a location. Id: ".$store_id
					 );
	
	 track_activity($dataAct);
}else{
	$text = "Disable";
	$email_text = "Enabled";
	
	$update_data = Array (
		'suspend' => '0',
		'adfundmember' => 'Y'
	);
	
	$db->where ('storeid', $store_id);
	$delete = $db->delete ('inactive_locations');
	
	$dataTrack = Array (
		"storeid"=>$store_id,
		'suspend' => '0',
		'adfundmember' => 'Y'
	);
	
	$dataAct = array("username"=>$username,
					 "storeid"=>$_SESSION['storeid'],
					 "updates"=>json_encode($dataTrack),
					 "section"=>"profile",
					 "details"=>"Enabled a location. Id: ".$store_id
					 );
	
	 track_activity($dataAct);
}

$db->where ('storeid', $store_id);
$update = $db->update ('locationlist', $update_data);

if ($update){
	//Get rep user information
	$rep_user_info = getRepUserInfo($store_id);
	
	//Get location information
	$cols = Array ("companyname", "email");
	$locationList = $db->where("storeid",$store_id)->getOne("locationlist", $cols);
	
	$company_name = $locationList['companyname'];
	$primary_loc_email = $locationList['email'];
	
	//Sends email to the location rep
	$subject = "A location was ".$email_text."!";
	$email_template = file_get_contents($_SERVER['DOCUMENT_ROOT']."/emails/disable-location.php");
	$email_template = str_replace("%%SUBJECT%%", $subject, $email_template);
	$email_template = str_replace("%%UPDATED_BY%%", $username, $email_template);
	$email_template = str_replace("%%STORE_ID%%", $store_id, $email_template);
	$email_template = str_replace("%%BUSINESS_NAME%%", $company_name, $email_template);
	$email_template = str_replace("%%PRIMARY_LOC_EMAIL%%", $primary_loc_email, $email_template);
	$email_template = str_replace("%%URL_TOKEN_ACCESS%%", LOCAL_CLIENT_URL.'xt_login.php?token='.$rep_user_info['data']['token'], $email_template);
	$email_template = str_replace("%%CLIENT_URL%%", CLIENT_URL, $email_template);
	$email_template = str_replace("%%LOCAL_CLIENT_URL%%", LOCAL_CLIENT_URL, $email_template);
	$email_template = str_replace("%%CLIENT_NAME%%", CLIENT_NAME, $email_template);
	$email_template = str_replace("%%YEAR%%", date("Y"), $email_template);
	
	$data = Array (
		'copy_hidden'=> 'sicwing@das-group.com',
		'subject'    => $subject,
		'from' 	     => 'DAS Group <noreply@das-group.com>',
		'sender'     => 'DAS Group <noreply@das-group.com>',
		'body' 	     => $email_template,
		'copy' 	     => 'michael@das-group.com',
		'storeid' 	 => $_SESSION['client'].'-'.$store_id,
		'to' 	     => $rep_user_info['data']['to']
	);

	$db->insert ('emails_send.emails', $data);
	
	$fpUpdates = ["dastoken" => "DAS%])p6Eu8SUuqN9U",
				  "storeid" =>$_POST['storeid'],
				  "action" => "modify",
				  "table" => "locationlist"];
	
	if(count($fpUpdates) && count($update_data) && $update){
		
		//$urlUpdate = "https://fullypromoted.com/xt_cupdate.php/?".http_build_query(array_merge($update_data,$fpUpdates));
		$urlUpdate = CLIENT_URL."xt_cupdate.php/?".http_build_query(array_merge($update_data,$fpUpdates));

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $urlUpdate);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$response_curl = curl_exec($ch);

		$response_curl = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		curl_close($ch);
	}
	
	echo $text;
}