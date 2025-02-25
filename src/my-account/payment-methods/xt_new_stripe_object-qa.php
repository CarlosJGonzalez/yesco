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
		if(isset($source->cvc_check)){
			if(($source->address_line1_check == "fail" ) || ($source->address_zip_check == "fail" ) || ($source->cvc_check == "fail" ) ||
				(strtotime($source->exp_year."-".$source->exp_month."-1") <= strtotime(date("Y-n-d")))){
				continue;
			}
		}
		
		$cardInfo = array();
		$cardInfo['name'] = $source->name;
		$cardInfo['brand'] = $source->brand;
		$cardInfo['exp_year'] = $source->exp_year;
		$cardInfo['exp_month'] = $source->exp_month;
		$cardInfo['last4'] = $source->last4;
		
		//Only the cards that have passed will be added
		$options .= getHtmlOutput($cardInfo);
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
		
		echo '<div class="card-deck">'.$options.'</div>';
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

function getHtmlOutput($cardInfo){
	$icon = getCardBrandElement($cardInfo['brand']);
	$name = (isset($cardInfo['name'])) ? strtoupper($cardInfo['name']) : 'N/A';
	
	$card_btn_actions = '<div class="col-sm-4 text-right">
							<div>
								<a href="xt_card_actions.php?card=<?=$card_id?>&customer=<?=$customer_id?>&action=remove" class="text-uppercase text-danger removeCard">Delete <i class="far fa-trash-alt fa-lg ml-2"></i></a>	
							</div>
							<div>
								<a href="" class="text-uppercase text-danger" id="updateCardId" data-target="#updCardModal" data-toggle="modal" data-card = "<?php echo $card->id; ?>"  data-customer = "<?php echo $customer_id; ?>" data-exp_year = "<?php echo $card->exp_year; ?>" data-exp_month = "<?php echo $card->exp_month; ?>" >
							Update 
							<i class="far fa-edit fa-lg ml-2"></i>
							</a>
							</div>
						  </div>';
	
	$output = '<div class="card box border-0">
			   <div class="p-3">
					<div class="row">
						<div class="col-sm-8">
							<div class="d-flex">
								<i class="fab'. $icon["icon"].' mr-3 fa-2x"></i>
								<div>
									<p class="mb-1"><'.strtoupper($cardInfo["brand"]).' "("' .$cardInfo["last4"] .' ")"</p>
									<p class="mb-1">Exp: <strong id="expInfo" >'. $cardInfo["exp_month"].'/'.$cardInfo["exp_year"].'</strong></p>
									<p class="mb-1"></br>'.$name.'</p>
								</div>
							</div>
						</div>
						
						<?php if(!$is_expired){ ?>

						<? } ?>
						
					</div>
			   </div>
			   </div>';
			   
	return $output;
}