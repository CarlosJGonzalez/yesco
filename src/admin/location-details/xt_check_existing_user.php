<?php
session_start();
include_once ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");

if(!$_SESSION["email"]){
	pageRedirect("Access denied: You must be authorized to view this page.", "error", "/");
	exit;
}

$user_email = $_POST['user_email'];

$db->where("email", $user_email , "=");
$db->getOne("storelogin");

if($db->count > 0)
	echo "Username already in use. The location will be added to this user.";
else
	echo "";