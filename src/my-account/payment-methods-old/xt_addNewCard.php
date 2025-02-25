<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");
include ($_SERVER['DOCUMENT_ROOT'].'/includes/stripe.php');

if(isset($_SESSION["user_role_name"])){

	if(isset($_POST['stripeToken'])){
		$customer = \Stripe\Customer::retrieve($_POST['customer_id']);
		$new_card= $customer->sources->create(array("source" => $_POST['stripeToken']));
		if($new_card){ 
			$_SESSION['success'] = "Your card has been successfully added.";
		
			$data = array (
				'Customer Id' => $_POST['customer_id']
			);
		
			$dataAct = array("username"=>$_SESSION['email'],
							 "storeid"=>$_SESSION['storeid'],
							 "updates"=>json_encode($data),
							 "section"=>"payment-methods",
							 "details"=>"Added a new card to Stripe"
							 );
							 
			track_activity($dataAct);
		}else{
			$_SESSION['error'] = "There was an error saving your changes";
		}
	}else if($_GET['action']=="remove"){
		$customer = \Stripe\Customer::retrieve($_GET['customer']);
		$delete_card = $customer->sources->retrieve($_GET['card'])->delete();
		
		if($delete_card){
			$_SESSION['success'] = "Your card has been successfully removed.";
			
			$data = array (
				'Customer Id' => $_GET['customer']
			);
		
			$dataAct = array("username"=>$_SESSION['email'],
							 "storeid"=>$_SESSION['storeid'],
							 "updates"=>json_encode($data),
							 "section"=>"payment-methods",
							 "details"=>"Removed a card from Stripe"
							 );
							 
			track_activity($dataAct);
		}else{
			$_SESSION['error']="There was an error saving your changes";
		}
		
	}else if($_GET['action']=="default"){
		$customer = \Stripe\Customer::retrieve($_GET['customer']);
		$customer->default_source = $_GET['card'];
		
		if($customer->save()){
			$_SESSION['success'] = "Your card has been successfully set as a default payment method.";
			
			$data = array (
				'Customer Id' => $_GET['customer']
			);
		
			$dataAct = array("username"=>$_SESSION['email'],
							 "storeid"=>$_SESSION['storeid'],
							 "updates"=>json_encode($data),
							 "section"=>"payment-methods",
							 "details"=>"Set a card as default in Stripe"
							 );
							 
			track_activity($dataAct);
		}else{ 
			$_SESSION['error'] = "There was an error saving your changes";
		}
	}

}else{
	pageRedirect("You must be authorized to see this page.", "error", "/promote/");
}

header("location:/my-account/payment-methods/");
exit;

?>