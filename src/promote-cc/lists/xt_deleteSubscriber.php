<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/ClassDasConstantContact.php");

if(isset($_SESSION["user_role_name"])){
	
	$locationList = $db->where("storeid",$_SESSION['storeid'])->getOne("locationlist");
	
	//Only for test purpose
	$locationList['constant_contact_api_key'] = 'j3bn9adcxrgg2jvxd6nmg75b';
	$locationList['constant_contact_access_token'] = '138e5b8a-ad09-419b-92f7-399d64875e4f';

	if(empty($locationList['constant_contact_api_key']) || empty($locationList['constant_contact_access_token'])){
		$_SESSION['error'] = "Please enter a valid api key and token.";
		header('location: /settings/promote/');
		exit;
	}else{
		$cc_api_key = $locationList['constant_contact_api_key'];
		$cc_access_token = $locationList['constant_contact_access_token'];
	}

	//ClassDasConstantContact 
	$cc = new Das_ConstantContact($cc_api_key, $cc_access_token);

	$ids = $db->escape($_GET['id']);
	$ids = explode(",",$ids);
	$listid = $db->escape($_GET['listid']);

	$contacts_failed = array();
	$error_msg = '';
	
	foreach($ids as $id){
		$contactDeleted = $cc->deleteContact($id);
		
		if($contactDeleted['is_error']){
			array_push($contacts_failed, "failed");
			$error_msg .= $contactDeleted['error_info']['error_message'].'<br>';
		}
	}
	
	if (in_array("failed", $contacts_failed)) {
		pageRedirect("Sorry, there was an error saving your changes. ".$error_msg, "error", "/promote-cc/lists/members.php?id=".$listid);
	}else {
		pageRedirect("Your changes have been successfully saved.", "success", "/promote-cc/lists/members.php?id=".$listid);
	}
	
}else{
	pageRedirect("You must be authorized to view this page.", "error", "/promote-cc/lists/members.php?id=".$listid);
}