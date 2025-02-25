<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");
include $_SERVER['DOCUMENT_ROOT'].'/includes/ClassDasCampaign.php';

$campaigns_info = new Das_Campaign($db,$token_api,$_SESSION['client']);
$action = '';

if(isset($_GET['id']) && $_GET['action']=="delete"){
	$action = 'Deleted a Campaign';
	
	if($campaigns_info->deleteCampaign($_GET['id']))
		$success = 1;
}else if(!isset($_POST['id'])){
	$action = 'Added a Campaign';
	
	if($campaigns_info->addCampaign($_POST))
		$success = 1;
}else if(isset($_POST['id'])){
	$action = 'Updated a Campaign';
	
	if($campaigns_info->updateCampaign($_POST))
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
	$_SESSION['error'] = "There was an error saving your changes.";
	header("location:/admin/campaign/campaign-info.php");
	exit;
}