<?php 
include ($_SERVER['DOCUMENT_ROOT']."/includes/head.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/ClassDasConstantContact.php");
if(!(roleHasPermission('show_promote_link', $_SESSION['role_permissions']))){
	$_SESSION['error'] = "Sorry! You must be authorized to see this page.";
	header('location: /');
	exit;
}

//Only for test purpose
/*$active_location['constant_contact_api_key'] = 'j3bn9adcxrgg2jvxd6nmg75b';
$active_location['constant_contact_access_token'] = '138e5b8a-ad09-419b-92f7-399d64875e4f';*/

if(empty($active_location['constant_contact_api_key']) || empty($active_location['constant_contact_access_token'])){
	$_SESSION['error'] = "Please enter a valid api key and token.";
	header('location: /settings/promote/');
	exit;
}else{
	$cc_api_key = $active_location['constant_contact_api_key'];
	$cc_access_token = $active_location['constant_contact_access_token'];
}

//ClassDasConstantContact 
$cc = new Das_ConstantContact($cc_api_key, $cc_access_token);

$campaign_id_url = $_GET['id'];
				
$db->where("campaign_id",$campaign_id_url);
$campaign = $db->getOne("promote_campaigns");

if($db->count>0){
	$campaign_details = $cc->getCampaign($campaign['campaign_id']);
	$previewOfEmailCampaign = $cc->previewOfEmailCampaign($campaign['campaign_id']);

	print_r($previewOfEmailCampaign['preview_email_content']);
}else{
	print_r('');
}
?>