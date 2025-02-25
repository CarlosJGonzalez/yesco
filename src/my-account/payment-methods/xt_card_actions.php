<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");
include ($_SERVER['DOCUMENT_ROOT'].'/includes/stripe.php');

if(isset($_SESSION["user_role_name"])){
	
	// remove or default
	$action_required = (isset($_GET['action']) && !empty($_GET['action'])) ? $_GET['action'] : '';
	
	if($action_required != ''){
		
		if($action_required == "remove"){
			$customer = \Stripe\Customer::retrieve($_GET['customer']);
			$delete_card = $customer->sources->retrieve($_GET['card'])->delete();
			
			if($delete_card){
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
				
				pageRedirect("Your card has been successfully removed.", "success", "/my-account/payment-methods/");
			}else{
				pageRedirect("There was an error removing your card.", "error", "/my-account/payment-methods/");
			}
			
		}else if($action_required == "default"){
			$customer = \Stripe\Customer::retrieve($_GET['customer']);
			$customer->default_source = $_GET['card'];
			
			if($customer->save()){
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
				
				pageRedirect("Your card has been successfully set as a default payment method.", "success", "/my-account/payment-methods/");
			}else{
				pageRedirect("There was an error setting your card as a default payment method.", "success", "/my-account/payment-methods/");
			}
		}
	
	}else{
		pageRedirect("There was an error saving your changes. No action was found..", "error", "/my-account/payment-methods/");
	}

}else{
	pageRedirect("You must be authorized to see this page.", "error", "/promote/");
}
?>