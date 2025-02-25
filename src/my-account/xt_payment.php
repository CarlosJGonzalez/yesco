<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT'].'/includes/stripe.php');

if(!$_SESSION["email"]){
	pageRedirect("Access denied: You must be authorized to view this page.", "error", "/");
	exit;
}

$draw = 0;
$all_payment_objects = Array();
$data = Array();

if(isset($_SESSION['storeid'])){
	
	if(isset($_POST['customer_id']) && !empty($_POST['customer_id'])){
		$customer_id = $_POST['customer_id'];
		$rowperpage = $_POST['length']; // Rows display per page
		$order_by_date = $_POST['order']['0']['dir'];  // Search input value
		$draw = $_POST['draw'];
		$start_date = '';
		$end_date = '';
		
		try {
			//It will retrieve Stripe\Charge Objects limited by rowperpage selected on the datatable
			$payments = \Stripe\Charge::all(array("customer" => $customer_id, "limit" => $rowperpage));
			
			//If the user has selected both dates, and the search button was clicked
			if($_POST["is_date_search"] == "yes"){
				
				//Getting datapickers values
				$start_date = strtotime($_POST["start_date"]);
				$end_date = strtotime($_POST["end_date"]);
				
				// previous to PHP 5.1.0 you would compare with -1, instead of false
				if ($start_date === false || $end_date === false) {
					//Today's payment
					$start_date = strtotime('today');
					$end_date = strtotime('today');
				}
				
				//It will retrieve Stripe\Charge Objects limited by rowperpage selected on the datatable and start_date and end_date
				$payments = \Stripe\Charge::all(array("customer" => $customer_id, "limit" => $rowperpage, "created" => array(
							'gte' => $start_date,
							'lte' => $end_date
						)));
			}
		
		} catch (\Stripe\Error\RateLimit $e) {
		  // Too many requests made to the API too quickly
		  print_json($draw, $all_payment_objects, $data);
		} catch (\Stripe\Error\InvalidRequest $e) {
		  // Invalid parameters were supplied to Stripe's API
		  print_json($draw, $all_payment_objects, $data);
		} catch (\Stripe\Error\Authentication $e) {
		  // Authentication with Stripe's API failed
		  // (maybe you changed API keys recently)
		  print_json($draw, $all_payment_objects, $data);
		} catch (\Stripe\Error\ApiConnection $e) {
		  // Network communication with Stripe failed
		  print_json($draw, $all_payment_objects, $data);
		} catch (\Stripe\Error\Base $e) {
		  // Display a very generic error to the user, and maybe send
		  // yourself an email
		  print_json($draw, $all_payment_objects, $data);
		} catch (Exception $e) {
		  // Something else happened, completely unrelated to Stripe
		  print_json($draw, $all_payment_objects, $data);
		}
		
		//Contains an array with all the Stripe\Charge Objects 
		$all_payment_objects = $payments->data;

		foreach($payments->data as $payment){
			$payment_date = date("m/d/Y h:i A", $payment->created);
			$payment_description = $payment->description;
			$amount = "$".number_format(($payment->amount /100), 2, '.', ' ');
			$status = $payment->status;
			
			$data[] = array(
			"date"	=> $payment_date,
			"description"  => $payment_description,
			"amount" => $amount,
			"status" => $status
			);
		}
		
		//The table will be ordered (ascending) by the date column.
		if($order_by_date == 'asc'){
			sort($data);
		}
		
		print_json($draw, $all_payment_objects, $data);
		
	}else{
		print_json($draw, $all_payment_objects, $data);
	}
	
}else{
	print_json($draw, $all_payment_objects, $data);
}

function print_json($draw, $all_payment_objects, $data){
	
	$response = array(
		"draw" => intval($draw),
		"iTotalRecords" => count($all_payment_objects),
		"iTotalDisplayRecords" => count($all_payment_objects),
		"aaData" => $data
	);

	echo json_encode($response);
	
	exit();
}
?>