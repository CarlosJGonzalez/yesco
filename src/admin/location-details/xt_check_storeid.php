<?php
session_start();
include_once ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");

if(!$_SESSION["email"]){
	pageRedirect("Access denied: You must be authorized to view this page.", "error", "/");
	exit;
}

$storeid = $_POST['storeid'];

$db->where("storeid", $storeid , "=");
$storeid_selected = $db->getOne("locationlist");

if($db->count > 0)
	echo "Store ID already in use.";
else
	echo "";