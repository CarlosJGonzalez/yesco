<?php
session_start();
include_once ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include_once ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php"); 

$email = $name = $subject = $msg = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" ) {

	//Get store representative id
	$db->where("storeid",$_SESSION['storeid']);
	$location = $db->getOne("locationlist", 'rep');
	$rep_id = $location['rep'];
	
	//Selects the email and email_notification from the representative of the selected storeid 
	$sql_rep_users =  "SELECT strl.email, strl.email_notification FROM ".$_SESSION['database'].".storelogin strl, ".$_SESSION['database'].".reps rep WHERE strl.email = rep.email AND strl.status = 1 AND rep.id = '".$rep_id."'";
	$rep_users = $db->rawQuery($sql_rep_users);
	
	//If the rep users have at least one email, it will store them. 
	if (!empty($rep_users)){
		
		//Gets the email from the rep
		if(!empty($rep_users[0]['email_notification'])){
			$assign_to = $rep_users[0]['email_notification'];
		}elseif(!empty($rep_users[0]['email']) && filter_var($rep_users[0]['email'], FILTER_VALIDATE_EMAIL)){
			$assign_to = $rep_users[0]['email'];
		}
	}

	$email 	  = clear_input($_POST['email']);
	$name 	  = clear_input($_POST['name']);
	$subject  = clear_input($_POST['subject']);
	$msg      = clear_input($_POST['msg']);

	$store_id = $_SESSION['client'].'-'.$_SESSION['storeid'];

	$subject_email = "Support: Local ".CLIENT_NAME." ".$_SESSION['storeid'];

	$body = '#assign'.' '.$assign_to;
	$body .= "\n"."Support Email  "."\n";
	$body .= "Form: Local ".CLIENT_NAME." ".$_SESSION['storeid']."\n";
	$body .= "Name: ".$name."\n";
	$body .= "Email: ".$email."\n";
	$body .= "Subject: ".$subject."\n";
	$body .= "Message: ".$msg."\n";

	$headers .= "From: ".$name." <".$email.">\r\n";
	$headers .= "Sender:".$name." <".$email.">\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
	$headers .= 'Bcc: sicwing@das-group.com' . "\r\n";

	$id = mail ("support@das-group.com", $subject_email, $body, $headers);

	if(!$id){
		pageRedirect('Please check your information and try again.', 'error', '/support/');
	}else{
		pageRedirect('Your information was successfully sent.', 'success', '/support/');	
	}
}

function clear_input($data) {
  	$data = trim($data);
  	$data = stripslashes($data);
  	$data = htmlspecialchars($data);
  	return $data;
}
?>