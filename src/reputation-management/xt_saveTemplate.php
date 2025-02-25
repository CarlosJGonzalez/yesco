<?php
date_default_timezone_set('America/New_York');
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/connect.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/functions.php';
session_start();

$owner_name =filter_var($_POST['name'], FILTER_SANITIZE_STRING);
$company_name =filter_var($_POST['companyname'], FILTER_SANITIZE_STRING);
$body =filter_var($_POST['body'], FILTER_SANITIZE_STRING);
$heading =filter_var($_POST['header'], FILTER_SANITIZE_STRING);
$num_resend =filter_var($_POST['num_resend'], FILTER_SANITIZE_NUMBER_INT);

$url_redirect = '/reputation-management/';

if (isset($_POST['optionSendEmial'])) {
	$optionSendEmial=$_POST['optionSendEmial'];
	$optionSendEmial=array_unique($optionSendEmial);
	$num_resend=count($optionSendEmial);
	sort($optionSendEmial);
	$optionSendEmial=json_encode($optionSendEmial);
}else{
	$msg= "Sorry, there was an error saving your template. Please check Number of resend.";
	pageRedirect($msg, 'error', $url_redirect);
}

if($_POST['default']==1){
	$data_ins = array(
						'storeid'=>$_SESSION['storeid'],
						'date'=>$db->now(),
						'owner_name'=>$owner_name,
						'company_name'=>$company_name,
						'heading'=>$heading,
						'body'=>$body,
						'num_resend'=>$num_resend,
						'info_send_email'=>$optionSendEmial,

					);
	$id = $db->insert ('review_template', $data_ins);
	if($id){
		track_activity(array('updates'=>json_encode($data_ins),'section'=>'reputation-management','details'=>'Insert New Template'));
		$msg="Your template has been saved.";
		pageRedirect($msg, 'success', $url_redirect);
		
	}else{
		$msg= "Sorry, there was an error saving your template.";
		pageRedirect($msg, 'error', $url_redirect);
	}
		
}else{
	$data_ins = array(
						'info_send_email'=>$optionSendEmial,
						'num_resend'=>$num_resend,
						'owner_name'=>$owner_name,
						'company_name'=>$company_name,
						'body'=>$body,
						'heading'=>$heading,
						'date'=>$db->now(),
					);
	$db->where ('storeid', $_SESSION['storeid']);

	if ($db->update ('review_template', $data_ins)){
		track_activity(array('updates'=>json_encode($data_ins),'section'=>'reputation-management','details'=>'Update Template'));
		$msg="Your template has been saved.";
		pageRedirect($msg, 'success', $url_redirect);
	}else{
		$msg= "Sorry, there was an error saving your template.";
		pageRedirect($msg, 'error', $url_redirect);
	}
}
?>