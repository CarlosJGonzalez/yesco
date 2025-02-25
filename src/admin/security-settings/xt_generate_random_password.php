<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");

if(!$_SESSION["email"] && $_SESSION["user_role_name"] != "admin_root"){
	pageRedirect("Access denied: You must be authorized to view this page.", "error", "/");
	exit;
}

$check_box_generate_password = $_POST['switch_generate_password'];
$password_without_hash = '';

if(!empty($check_box_generate_password )){
	if($check_box_generate_password  == '1')
		$password_without_hash = randomPassword();

	echo $password_without_hash;
}
?>