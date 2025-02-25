<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");

$storeid = $_POST['storeid']; 
$email = $_POST['user_email']; 
$user_view = $_POST['user_view'];
$notifications = $_POST['notifications_check'];
$emails = explode(",", $_POST['email_notification']);

if(!empty($user_view) && !empty($_POST['email_notification'])){
	$valid_emails = get_emails_list($emails, "valid");
	$invalid_emails = get_emails_list($emails, "invalid");

	if((empty($valid_emails)) || (!empty($valid_emails) && !empty($invalid_emails))){
		$invalid_emails_to_insert = implode(",", $invalid_emails);
		echo '<div class="alert alert-danger" role="alert">Email(s) must be valid. Wrong email(s): '.$invalid_emails_to_insert.'</div>';
		exit;
	}elseif(!empty($valid_emails)){
		$valid_emails_to_insert = implode(",", $valid_emails);
		$data = array("email_notification"=>$valid_emails_to_insert, "notifications"=>$notifications);

		if($user_view == 'user'){
			$db->where ('storeid', $storeid);
			$notifications_settings_ok = $db->update ('locationlist', $data);
		}else{
			$db->where ('email', $email);
			$notifications_settings_ok = $db->update ('storelogin', $data);
		}

		if($notifications_settings_ok){
			echo '<div class="alert alert-success" role="alert">Your notification settings were successfully updated.</div>';
		}else{
			echo '<div class="alert alert-danger" role="alert">There was an error updating your notification settings.</div>';
		}
	}
}else{
	echo '<div class="alert alert-danger" role="alert">All inputs must be filled.</div>';
}
?>