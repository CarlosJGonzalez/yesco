<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT'].'/includes/stripe.php');


if(isset($_SESSION['storeid']) && ( isset($_POST['year']) && isset($_POST['month']) && isset($_POST['customer']) && isset($_POST['card']) )) {
	$year = $_POST['year'];
	$month = $_POST['month'];
	$customer = $_POST['customer'];
	$card = $_POST['card'];
	
	$error = 0;
	$msg = "";
	try {
    	\Stripe\Customer::updateSource(
		  $customer,
		  $card,
		  [
		    'exp_year'  => $year,
		    'exp_month' => $month,
		  ]
		);
	} catch (\Stripe\Error\RateLimit $e) {
	  // Too many requests made to the API too quickly
		$error = 1;
		$msg = $e;
	} catch (\Stripe\Error\InvalidRequest $e) {
	  // Invalid parameters were supplied to Stripe's API
	} catch (\Stripe\Error\Authentication $e) {
	  // Authentication with Stripe's API failed
	  // (maybe you changed API keys recently)
		$error = 1;
		$msg = $e;
	} catch (\Stripe\Error\ApiConnection $e) {
	  // Network communication with Stripe failed
		$error = 1;
		$msg = $e;
	} catch (\Stripe\Error\Base $e) {
	  // Display a very generic error to the user, and maybe send
	  // yourself an email
		$error = 1;
		$msg = $e;
	} catch (Exception $e) {
	  // Something else happened, completely unrelated to Stripe
		$error = 1;
		$msg = $e;
	}

	if($error){
		echo json_encode(array('error' => 1, 'msg' =>$msg ));exit;
	}
	echo json_encode(array('error' => 0 ));exit;
}
?>