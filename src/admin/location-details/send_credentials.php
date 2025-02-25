<?php
exit;
session_start();
error_reporting(E_ALL & ~E_NOTICE);
include_once ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include_once ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");

$locations = $db->rawQuery("SELECT a.storeid,companyname,b.email,b.user_password,b.name FROM `locationlist` a left join storelogin b on a.storeid = b.storeid where suspend = 0 and user_password is not null");
foreach($locations as $location){
	$subject = "Welcome to Local ".CLIENT_NAME."!";
	$email_template = file_get_contents($_SERVER['DOCUMENT_ROOT']."/emails/new-user.php");

	$email_template = str_replace("%%NAME%%", $location['name'], $email_template);
	$email_template = str_replace("%%USERNAME%%", $location['email'], $email_template);
	$email_template = str_replace("%%PASSWORD%%", $location['user_password'], $email_template);
	$email_template = str_replace("%%CLIENT_URL%%", CLIENT_URL, $email_template);
	$email_template = str_replace("%%LOCAL_CLIENT_URL%%", LOCAL_CLIENT_URL, $email_template);
	$email_template = str_replace("%%CLIENT_NAME%%", CLIENT_NAME, $email_template);
	$email_template = str_replace("%%YEAR%%", date("Y"), $email_template);

	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
	$headers .= 'From: <noreply@tworld.com>' . "\r\n";
	$headers .= 'BCC: seema@das-group.com' . "\r\n";
	$send_to = $location['email'];
	//mail('sicwing@das-group.com',$subject,$email_template,$headers);
	mail($send_to,$subject,$email_template,$headers);
}