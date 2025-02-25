<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/ClassDasMC.php");

if(isset($_SESSION["user_role_name"])){

	$cols = Array ("storeid", "loyalty_promotions_key");
	$locationList = $db->Where("storeid", $_SESSION['storeid'])->getOne("locationlist", $cols);

	if(empty($locationList['loyalty_promotions_key'])){
		$_SESSION['error'] = "Please enter a key.";
		header('location: /settings/promote/');
		exit;
	}else{
		$mc_api_key = $locationList['loyalty_promotions_key'];
	}

	$mc = new Das_MC($mc_api_key);

	$ids = $db->escape($_GET['id']);
	$ids = explode(",",$ids);
	$listid = $db->escape($_GET['listid']);

	foreach($ids as $id){
		$mc->deleteMember($listid,$id);
	}
	$_SESSION['success'] = "Your changes have been successfully saved.";
	header("Location:/promote/lists/members.php?id=".$listid);
	exit;

}else{
	pageRedirect("You must be authorized to view this page.", "error", "/promote-cc/lists/members.php?id=".$listid);
}