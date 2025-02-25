<?php
die;
set_time_limit(300);
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/ClassDasMC.php");

if(isset($_SESSION["user_role_name"])){
	
	$cols = Array ("storeid","companyname","address","city","state","zip","email","loyalty_promotions_key");
				   
	$locationList = $db->Where("storeid", $_SESSION['storeid'])->getOne("locationlist", $cols);
	
	if(empty($locationList['loyalty_promotions_key'])){
		pageRedirect("Not key provided.", "error", "/promote/create-campaign.php");
	}else{
		$mc_api_key = $locationList['loyalty_promotions_key'];
	}

	$mc = new Das_MC($mc_api_key);
	
	$mcAccountInfo = $mc->getAccountInfo();
	$account_id = $mcAccountInfo['account_id'];
	
	if(!$account_id){
		pageRedirect("Your Mailchimp API key is invalid.", "error", "/promote/create-campaign.php");
	}		
	
	$campaignAction = $mc->actionsCampaign("f27dabb5bf","send");
	
	echo '<pre>'; print_r($campaignAction); echo '</pre>';
	exit; 
	
}else{
	pageRedirect("You must be authorized to view this page.", "error", "/promote/");
}
?>