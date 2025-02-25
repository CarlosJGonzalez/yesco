<?php
die;
set_time_limit(300);
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/ClassDasMC.php");

if(isset($_SESSION["user_role_name"]) == "admin_root"){
	$default_mailchimp_key = 'ceb03920033b5bd26b89dcf74c75298e-us19';
	$mc = new Das_MC($default_mailchimp_key);
	
	$sql_locs = "SELECT campaignid, storeid 
				 FROM `signarama`.`email_campaigns` 
				 WHERE `campaignid` IS NOT NULL 
				 AND `template_id` 
				 IS NOT NULL 
				 LIMIT 0,1000";
	$locations = $db->rawQuery($sql_locs);

	foreach($locations as $locationList){
		$storeid = $locationList['storeid'];
		$campaignid = $locationList['campaignid'];
		
		$campaign_info = $mc->getCampaign($campaignid);
		if(!$campaign_info['id'] && $campaign_info['status'] != 'sent') continue;
		
		echo '<pre>'; print_r("=============== CAMPAING INFO ==============="); echo '</pre>';
		echo '<pre>'; print_r($storeid); echo '</pre>';
		echo '<pre>'; print_r($campaign_info); echo '</pre>';
	}
}else{
	pageRedirect("You must be authorized to view this page.", "error", "/promote/");
}
?>