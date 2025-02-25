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
		$_SESSION["error"] = "Something went wrong adding the new payment method. ".$error_msg;
		echo '';
		exit;
	}

	$select = '<select class="form-control mb-3" name="payment-method">';	
	$options = "";

	$cards = $stripe->getCards( $customerid );					
	
													
	foreach($cards as $source ){
		$source = ((object)$source);

		$id = $source->id;

		if($source->object == "source"){
			if($source->card === null)
				continue;
			$source =  $source->card;
		}																	
		
		//Validates if address_zip_check is defined and not null. Stripe doesn't allow to enter the zip code for certain kind of cards
		if( isset($source->cvc_check) ){
			//If the card has failed, it won't be added
			if(($source->address_line1_check == "fail" ) || ($source->address_zip_check == "fail" ) || ($source->cvc_check != "pass" ) ||
			(strtotime($source->exp_year."-".$source->exp_month."-1") <= strtotime(date("Y-n-d")))){
				continue;
			}
		}
		
		//Only the cards that have passed will be added
		$selected = ($id == $stripe->isDefaultCard( $customerid, $id )) ? "selected":"";
		$options .= "<option value='".$id."' $selected >".$source->brand. " Ending in ".$source->last4."</option>";
	}

	//If a new customer was created, the html response will be a <select> and a hidden input with the customer_id
	if( $newCustomer ){
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
		
		echo $select.$options."</select><br><input type='hidden' name='customer_id' value='".$customerid."'>";
	}else{
		$data = Array (
			'Customer Id' => $_POST["customer_id"]
		);
			
		$dataAct = array("username"=>$_SESSION['email'],
						 "storeid"=>$_SESSION['storeid'],
						 "updates"=>json_encode($data),
						 "section"=>"payment",
						 "details"=>"Added a new card to Stripe"
						 );
						 
		track_activity($dataAct);
		
		echo $select.$options."</select>";
	}
	exit;
}