<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");
include $_SERVER['DOCUMENT_ROOT'].'/includes/ClassDasCampaign.php';

$campaigns_info = new Das_Campaign($db,$token_api,$_SESSION['client']);
$action = '';

if(isset($_GET['id']) && $_GET['action'] == "deleteCTN"){
	$action = 'Deleted a CTN';
	
	if($campaigns_info->deleteCTN($_GET['id']))
		$success = 1;
}else if(isset($_GET['id']) && $_GET['action'] == "delete"){	
	$action = 'Deleted a Portal';

	if($campaigns_info->deletePortal($_GET['id']))
		$success = 1;
}else if(isset($_POST['id']) && $_POST['action'] == 'change_ctn' ){
	$action = 'Changed a CTN';
	
	$info_portal = $campaigns_info->changeCTN($_POST);
	
	if( isset($info_portal['is_error']) && $info_portal['is_error'] ){

		$_SESSION['error'] = isset($info_portal['data']['error']) ? $info_portal['data']['error'] : "There was an error saving your information.";
		header("location:/admin/campaign/campaign-info.php");
		exit;
	}else{
		$success = 1;
	}

}else if(!isset($_POST['id']) && !isset($_POST['action']) ){

	if($campaigns_info->existCampId($_POST['campid'])){
		$_SESSION['error'] = "There was an error saving your changes.The Campaign ID is Unique.";
		header("location:/admin/campaign/campaign-info.php");
		exit;
	}
	
	$action = 'Added a Portal';
	
	$info_portal = $campaigns_info->addPortal($_POST);
	
	if( isset($info_portal['is_error']) && $info_portal['is_error'] ){

		$_SESSION['error'] = isset($info_portal['data']['error']) ? $info_portal['data']['error'] : "There was an error saving your information.";
		header("location:/admin/campaign/campaign-info.php");
		exit;
	}else{
		$success = 1;
	}		

}else if(isset($_POST['id']) && !isset($_POST['action'])){
	
	$action = 'Updated a CTN';
	
	if($campaigns_info->updateCTN($_POST))
		$success = 1;
}else if(isset($_POST['id']) && $_POST['action'] == 'update_portal' ){

	$action = 'Updated a Campid';

	if($campaigns_info->updatePortal($_POST))
		$success = 1;
}

if($success == 1){
	$data = (count($_POST) > 0) ? $_POST : $_GET;
	
	$dataAct = array("username"=>$_SESSION['email'],
					 "storeid"=>$_SESSION['client'],
					 "updates"=>json_encode($data),
					 "section"=>"campaign-management",
					 "details"=>$action
				 );
					 
	track_activity($dataAct);
	
	$_SESSION['success'] = "Your changes have been saved.";
	header("location:/admin/campaign/campaign-info.php");
	exit;
}else{
	$_SESSION['error'] = "There was an error saving your information.";
	header("location:/admin/campaign/campaign-info.php");
	exit;
}