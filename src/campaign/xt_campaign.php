<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");
include $_SERVER['DOCUMENT_ROOT'].'/includes/ClassDasCampaign.php';

$campaigns_info = new Das_Campaign($db,$token_api,$_SESSION['client'],$_SESSION['storeid']);

if(isset($_GET['id']) && $_GET['action']=="delete"){
	if($campaigns_info->deleteCampaign($_GET['id']))
		$success = 1;
}else if(!isset($_POST['id'])){
	if($campaigns_info->addCampaign($_POST))
		$success = 1;
}else if(isset($_POST['id'])){
	
	if($campaigns_info->updateCampaign($_POST))
		$success = 1;
}
if($success == 1){
	$_SESSION['success'] = "Your changes have been saved.";
	header("location:/campaign/campaign-info.php");
	exit;
}else{
	$_SESSION['error'] = "There was an error saving your changes.";
	header("location:/campaign/campaign-info.php");
	exit;
}