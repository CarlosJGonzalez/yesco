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
	
	// Print Add domain to account
	echo '<pre>'; print_r("=============== Add domain to account ==============="); echo '</pre>';
	//$parameters = json_encode(array ('verification_email' => 'dev@das-group.com'));
	//$added_acct = $mc->addDomainToAccount($parameters);
	//echo '<pre>'; print_r($added_acct); echo '</pre>';
	
	// Verify domain
	echo '<pre>'; print_r("=============== VERIFY DOMAIN ==============="); echo '</pre>';
	//$parameters = json_encode(array ('code' => 'b59bc78d7608'));
	//$verified_domain = $mc->verifyDomain('das-group.com', $parameters);
	//echo '<pre>'; print_r($verified_domain); echo '</pre>';

	exit;  
	
}else{
	pageRedirect("You must be authorized to view this page.", "error", "/promote/");
}
?>