<?php
die;
set_time_limit(300);
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/ClassDasMC.php");

if(isset($_SESSION["user_role_name"]) == "admin_root"){
	
	$storeid = '30313';
	$storeid = (isset($_SESSION['storeid']) && !empty($_SESSION['storeid'])) ? $_SESSION['storeid'] : $storeid;
	
	$cols = Array ("storeid","companyname","address","city","state","zip","email","loyalty_promotions_key");
				   
	$locationList = $db->Where("storeid", $storeid)->getOne("locationlist", $cols);
	
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
	
	// Print Mc account info
	echo '<pre>'; print_r("=============== ACCOUNT INFO ==============="); echo '</pre>';
	echo '<pre>'; print_r($mcAccountInfo); echo '</pre>';
	
	// Get all lists
	echo '<pre>'; print_r("=============== ALL LISTS ==============="); echo '</pre>';
	//$lists = $mc->getLists();
	//echo '<pre>'; print_r($lists); echo '</pre>';
	
	// Get an specific list
	echo '<pre>'; print_r("=============== Specific List ==============="); echo '</pre>';
	//$list = $mc->getList('c55d005f45');
	//echo '<pre>'; print_r($list); echo '</pre>';
	
	// Get members from a list
	echo '<pre>'; print_r("=============== Unsubscribe members from a list ==============="); echo '</pre>';
	//$parameters = json_encode(array ('status' => 'subscribed'));
	//$members = $mc->getMembers('c55d005f45', $parameters);
	//echo '<pre>'; print_r($members); echo '</pre>';
	
	// Get specific member
	echo '<pre>'; print_r("=============== Get Specific Member ==============="); echo '</pre>';
	//$member = $mc->getMember('c55d005f45','hugh.jacobs@wistv.com');
	//echo '<pre>'; print_r($member); echo '</pre>';
	
	
	echo '<pre>'; print_r("=============== ALL INTERETS CATEGORIES ==============="); echo '</pre>';
	//$listid = "c55d005f45";
	//$ic = $mc->getInterestCategories($listid);
	//echo '<pre>'; print_r($ic); echo '</pre>';
	
	echo '<pre>'; print_r("=============== ALL INTERESTS ==============="); echo '</pre>';
	//$interestCategories = $ic['categories'][0]['id'];
	//$result = $mc->existInterestName($listid,$interestCategories,$locationList['companyname']);
	//echo '<pre>'; print_r($result); echo '</pre>';
	
	echo '<pre>'; print_r("=============== CAMPAING INFO ==============="); echo '</pre>';
	//$campaignid = "009db3fa49";
	//$campaign_info = $mc->getCampaign($campaignid);
	//echo '<pre>'; print_r($campaign_info); echo '</pre>';
	
	exit;  
	
}else{
	pageRedirect("You must be authorized to view this page.", "error", "/promote/");
}
?>