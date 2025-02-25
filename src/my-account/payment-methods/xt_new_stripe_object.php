<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/ClassDasStripe.php");

date_default_timezone_set('America/New_York');

/* Adding a new card and new customer if does not exist. */
if($_POST["object"] == "card"){
	$storeid = isset($_SESSION['storeid']) ? $_SESSION['storeid'] : null;
	$stripe = new Das_Stripe($db,$token_api,$_SESSION['client'],$storeid);
	$customerid = false;
	$error = false;
	$cardOk = true;
	$newCustomer = false;

	if($_POST["customer_id"] == ""){
		$params = array(
							'client' => $_SESSION['client'],
							'storeid'=> $storeid,
							'name'   => $_POST["owner_email"],
							'email'  => $_POST["owner_email"],
						);
		
		$customer = $stripe->createCustomer( $params );

		if( $customer['is_error'] ){
			$error_msg = $customer['data']['error'];
			$error = true;
		}else{
			$newCustomer = true;
			$customerid = $customer['data']['id'];
		}
	}else{
		$customerid = $_POST["customer_id"];
	}

	if( !$error && $customerid){
		$db->where ('storeid', $storeid);
		$db->update('locationlist', array('customer_id' => $customerid));

		$card = $stripe->createCard( $customerid, $_POST['id'] );
		
		if( $card['is_error'] ){
			$cardOk = false;
			$error_msg = $card['data']['error'];
			$error = true;
		}
	}

	//If there was an error, the html response will be empty
	if( $error ){
		$_SESSION["error"] = "Something went wrong adding the new payment method.".$error_msg;
		echo 'redirect';
		exit;
	}else{
		$data = array (
			'Customer Id' => $customerid
		);
		
		$dataAct = array("username"=>$_SESSION['username'],
						 "storeid"=>$_SESSION['storeid'],
						 "updates"=>json_encode($data),
						 "section"=>"payment",
						 "details"=>"Added a new card to Stripe"
						 );
					 
		track_activity($dataAct);
		
		$_SESSION["success"] = "Your card was successfully added!";
		echo 'redirect';
		exit;
	}
}else{
	$_SESSION["error"] = "There was an error adding your card: No card was found.";
	echo 'redirect';
	exit;
}