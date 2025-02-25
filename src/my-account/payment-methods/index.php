<!doctype html>
<html lang="en">
<head>
	<link rel="stylesheet" href="/css/checkbox.css">
	<?php include ($_SERVER['DOCUMENT_ROOT']."/includes/head.php");?>

	<title>Payment Methods | <?php echo CLIENT_NAME; ?></title>
	<script src="https://js.stripe.com/v3/"></script>
	<link rel="stylesheet" type="text/css" href="stripe-add-new-form.css"/>
	<style>
	/*Sticky message coming from xt_updateCard.php */
	div.sticky {
	  position: sticky;
	  top: 1%;
	  left: 50%;
	  z-index: 9999;
	  width:25%;
	}
	</style>
</head>
  <body class="bg-light cbp-spmenu-push">
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/top.php"); ?>

    <div class="container-fluid">
      <div class="row">
        <?php 
        include ($_SERVER['DOCUMENT_ROOT']."/includes/nav.php");
		include ($_SERVER['DOCUMENT_ROOT'].'/includes/stripe.php');
		?>
        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 p-0 mb-4">
			<div class="sticky"></div>
		
		<?php
			$db->where("storeid",$_SESSION['storeid']);
			$user_detail = $db->getOne("locationlist", "customer_id");
			$customer_id = $user_detail["customer_id"];
		?>
			<div class="p-0 border-bottom mb-4">
				<div class="d-flex d-block align-items-center clearfix py-2 px-4">
					<h1 class="h2 font-light mb-0 text-center text-sm-left"><i class="fas fa-credit-card mr-2"></i> Payment Methods</h1>
					<div class="ml-auto">
						<button class='form-control btn bg-light d-block mb-4 border' data-toggle="modal" data-target="#addNewCard" type="button" data-backdrop="static" data-keyboard="false">
							<i class="fa fa-credit-card"></i> Add New Card
						</button>
					</div>
				</div>
			</div>
        	<div class="px-4 py-3">
				
				<?php include ($_SERVER['DOCUMENT_ROOT']."/includes/alerts.php"); ?>

				<div class="row">
					<div class="col-sm-3">
						<?php include "../nav.php"; ?>
					</div>
					<div class="col-sm-9">
						<div class="card-deck">
						<?
						if($customer_id){
							try{
								$cards = \Stripe\Customer::retrieve($customer_id);

								//$cards = \Stripe\Customer::retrieve('cus_EUDYxj7EDu2F9a');
								$default_card = $cards->default_source; 
							} catch (\Stripe\Error\RateLimit $e) {
							  // Too many requests made to the API too quickly
							  echo '<div class="alert alert-warning rounded-0 mb-0 px-3 py-2"><i class="fas fa-info-circle"></i> There was a problem loading your information.</div>';
							  exit();
							} catch (\Stripe\Error\InvalidRequest $e) {
							  // Invalid parameters were supplied to Stripe's API
							  echo '<div class="alert alert-warning rounded-0 mb-0 px-3 py-2"><i class="fas fa-info-circle"></i> There was a problem loading your information.</div>';
							  exit();
							} catch (\Stripe\Error\Authentication $e) {
							  // Authentication with Stripe's API failed
							  // (maybe you changed API keys recently)
							  echo '<div class="alert alert-warning rounded-0 mb-0 px-3 py-2"><i class="fas fa-info-circle"></i> There was a problem loading your information.</div>';
							  exit();
							} catch (\Stripe\Error\ApiConnection $e) {
							  // Network communication with Stripe failed
							  echo '<div class="alert alert-warning rounded-0 mb-0 px-3 py-2"><i class="fas fa-info-circle"></i> There was a problem loading your information.</div>';
							  exit();
							} catch (\Stripe\Error\Base $e) {
							  // Display a very generic error to the user, and maybe send
							  // yourself an email
							  echo '<div class="alert alert-warning rounded-0 mb-0 px-3 py-2"><i class="fas fa-info-circle"></i> There was a problem loading your information.</div>';
							  exit();
							} catch (Exception $e) {
							  // Something else happened, completely unrelated to Stripe
							  echo '<div class="alert alert-warning rounded-0 mb-0 px-3 py-2"><i class="fas fa-info-circle"></i> There was a problem loading your information.</div>';
							  exit();
							}
							
							//If the customer has cards, it proceeds to show them
							if(count($cards->sources->data) != 0){
								foreach($cards->sources->data as $card){
									$card_id = $card->id;
									
									//Assign the card object depending on the type of card (default_card or sources)
									if($card->object == "source"){
										$card =  $card->card;
									}
									
									if($card->exp_month < 10)
										$card_exp = '0'.$card->exp_month;
									else
										$card_exp = $card->exp_month;
									
									$date = $card->exp_year.'-'.$card_exp.'-01'; #could be (almost) any string date
									$now = date('Y-m-d');
									
									$date_compared = dateDiff($date, $now);
									
									$is_default = false;
									$is_expired = false;
									$is_regular = false;

									if ($default_card == $card_id){
										$is_default = true;
									}elseif($date_compared < 0){
										$is_expired = true;
									}else{
										 $is_regular = true;
									}
								?>
									<div class="card box border-0">
										<div class="p-3">
											<div class="row">
												<div class="col-sm-8">
													<div class="d-flex">
														<?php
														$icon = getCardBrandElement($card->brand);
														?>
														<i class="fab <?php echo $icon['icon']?> mr-3 fa-2x"></i>
														<div>
															<p class="mb-1"><?php echo strtoupper($card->brand). " ". "(" .$card->last4 . ")"?></p>
															<p class="mb-1">Exp: <strong id="expInfo" ><?php echo $card->exp_month.'/'.$card->exp_year?></strong></p>
															<p class="mb-1"><? if(isset($card->name)) echo '<br>'.strtoupper($card->name);?></p>
														</div>
													</div>
												</div>
												<?php if(!$is_expired){ ?>
													<div class="col-sm-4 text-right">
														<div>
															<a href="xt_card_actions.php?card=<?=$card_id?>&customer=<?=$customer_id?>&action=remove" class="text-uppercase text-danger removeCard">Delete <i class="far fa-trash-alt fa-lg ml-2"></i></a>	
														</div>
														
														<div>
														<a href="" class="text-uppercase text-danger" id="updateCardId" data-target="#updCardModal" data-toggle="modal" data-card = "<?php echo $card->id; ?>"  data-customer = "<?php echo $customer_id; ?>" data-exp_year = "<?php echo $card->exp_year; ?>" data-exp_month = "<?php echo $card->exp_month; ?>" >
															Update 
															<i class="far fa-edit fa-lg ml-2"></i>
														</a>
														</div>
													</div>
												<? } ?>
											</div>
											<? if($is_regular){ ?>
											<div class="my-2">
												<label class="label cusor-pointer d-flex text-center" for="default-3">
													<input  class="label__checkbox" type="checkbox" name="make_default" value="3" type="checkbox" id="default-3" />
													<!--<a href="xt_addNewCard.php?card=<?//=$card_id?>&customer=<?//=cus_EUDYxj7EDu2F9a?>&action=default" class="defaultCard"></a>-->
													<a href="xt_card_actions.php?card=<?=$card_id?>&customer=<?=$customer_id?>&action=default" class="defaultCard"></a>
													<span class="label__text d-flex align-items-center">
													  <span class="label__check d-flex rounded-circle mr-2">
														<i class="fa fa-check icon small"></i>
													  </span>
														<span class="text-uppercase small letter-spacing-1 d-inline-block">Set as default payment method</span>
													</span>
												  </label>
											</div>
											<? } ?>
										</div>
										<?php
										if ($is_default){
											echo '<div class="alert alert-warning rounded-0 mb-0 px-3 py-2">
													<i class="fas fa-info-circle"></i> This is your default payment.
												  </div>';
										 }elseif($is_expired){
											echo '<div class="alert alert-danger rounded-0 mb-0 px-3 py-2">
													<i class="fas fa-exclamation-triangle"></i> This card has expired.
												  </div>';
										 }elseif($is_default && $is_expired && !$is_default){
											 echo '<div class="alert alert-warning rounded-0 mb-0 px-3 py-2">
													<i class="fas fa-info-circle"></i> This is your default payment.
												  </div>';
											 echo '<div class="alert alert-danger rounded-0 mb-0 px-3 py-2">
													<i class="fas fa-exclamation-triangle"></i> This card has expired.
												  </div>';
										 }
										 ?>
									</div>

							<?	}
							}else{
								echo '<div class="alert alert-warning rounded-0 mb-0 px-3 py-2"><i class="fas fa-info-circle"></i> There are not payment methods.</div>';
							}
						}else{
							echo '<div class="alert alert-warning rounded-0 mb-0 px-3 py-2"><i class="fas fa-info-circle"></i> There are not payment methods in your account.</div>';
						}
						?></div>
					</div>
				</div>
			</div>
			
        </main>
      </div>

	<!-- Update Card Modal -->
	<div class="modal fade" id="updCardModal" tabindex="-1" role="dialog" aria-labelledby="updCardModalLabel" aria-hidden="true">
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title" id="updCardModalLabel">Update Card</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body">
			<input type="hidden" id="modalCustomerId"  name="customer_id" value="">
			<input type="hidden" id="modalCardId"  name="card_id" value="">
			  <div class="row mb-2">			
				<div class="col-md-5"><label for="modalSelectEM">Exp. month</label></div>	      	
				<div class="col-md-7">				      		
					<select id="modalSelectEM" class="form-control custom-select-arrow"></select>
				</div>

			  </div>
			  <div class="row mb-2">			
				<div class="col-md-5"><label for="modalSelectEY">Exp. year</label></div>	      	
				<div class="col-md-7">				      		
					<select id="modalSelectEY" class="form-control custom-select-arrow"></select>
				</div>
			  </div>
		  </div>
		  <div class="modal-footer">
			<button type="button" id="btnUpdateCard" class="btn bg-blue text-white" data-dismiss="modal">Update</button>
		  </div>
		</div>
	  </div>
	</div>
	<!-- End Update Card Modal -->
	
	<!-- Add New Card Modal -->
	<form action="xt_new_stripe_object.php" method="post" name="addNewCard2" id="payment-form">
		<div class="modal" tabindex="-1" role="dialog" id="addNewCard">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<div class="justify-content-start"></div>
						<div class="logo-margin-negative justify-content-center">
							<img src="/img/das_logo_image.jpg" class="center-block img-circle" style="border-radius: 100%;border: 3px solid #fff;width:85px;height:85px;border-radius: 100%;box-shadow: 0 0 0 1px rgba(0,0,0,.18), 0 2px 2px 0 rgba(0,0,0,.08);top: 0;left: 0;" width="120" height="90">
							<h5 class="modal-title text-center">DAS Group</h5>
						</div>
						<div class="justify-content-end">
							<button type="button" class="close" data-dismiss="modal" id="close-btn-modal-addcard"aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-12 input-group mb-2">
								<div class="input-group-prepend">
									<span class="input-group-text" id="inputGroupPrepend"><i class="fa-fw fas fa-user"></i></span>
								</div>
								<input id="input-name" type="text" name="input_name" placeholder="Name" class="form-control" required />
							</div>
							<div class="col-12 input-group mb-2">
								<div class="input-group-prepend">
									<span class="input-group-text" id="inputGroupPrepend"><i class="fa-fw fas fa-envelope"></i></span>
								</div>
								<input id="input-email" type="email" name="input_email" placeholder="Email" class="form-control"required />
							</div>
							<div class="col-12 input-group mb-2">
								<div class="input-group-prepend">
									<span class="input-group-text" id="inputGroupPrepend"><i class="fa-fw fas fa-map-marker-alt"></i></span>
								</div>
								<input id="input-address" type="text" name="input_address" placeholder="Street" class="form-control" required />
							</div>
							<div class="col-12 input-group mb-2">
								<div class="input-group-prepend">
									<span class="input-group-text" id="inputGroupPrepend"><i class="fa-fw fas fa-map-marker-alt"></i></span>
								</div>
								<input id="input-address-2" type="text" name="input_address_2" placeholder="Address 2 (optional)" class="form-control" />
							</div>
							<div class="col-6 input-group mb-2">
								<div class="input-group-prepend">
									<span class="input-group-text" id="inputGroupPrepend"><i class="fa-fw fas fa-map-marker-alt"></i></span>
								</div>
								<input id="input-city" type="text" name="city" placeholder="City" class="form-control" required />
							</div>
							<div class="col-6 input-group mb-2">
								<div class="input-group-prepend">
									<span class="input-group-text" id="inputGroupPrepend"><i class="fa-fw fas fa-map-marker-alt"></i></span>
								</div>
								<input id="input-state" type="text" name="state" placeholder="State" class="form-control" required />
							</div>
							<div class="col-12 input-group mb-5">
								<div class="input-group-prepend">
									<span class="input-group-text" id="inputGroupPrepend"><i class="fa-fw fas fa-map-marker-alt"></i></span>
								</div>
								<select id="input-country" name="month" class="form-control">
									<option value="AU">Australia</option>
									<option value="AT">Austria</option>
									<option value="BE">Belgium</option>
									<option value="BR">Brazil</option>
									<option value="CA">Canada</option>
									<option value="CN">China</option>
									<option value="DK">Denmark</option>
									<option value="FI">Finland</option>
									<option value="FR">France</option>
									<option value="DE">Germany</option>
									<option value="HK">Hong Kong</option>
									<option value="IE">Ireland</option>
									<option value="IT">Italy</option>
									<option value="JP">Japan</option>
									<option value="LU">Luxembourg</option>
									<option value="MX">Mexico</option>
									<option value="NL">Netherlands</option>
									<option value="NZ">New Zealand</option>
									<option value="NO">Norway</option>
									<option value="PT">Portugal</option>
									<option value="SG">Singapore</option>
									<option value="ES">Spain</option>
									<option value="SE">Sweden</option>
									<option value="CH">Switzerland</option>
									<option value="GB">United Kingdom</option>
									<option value="US" selected="selected">United States</option>
								</select>
							</div>
							<div class="col-12">
								<label for="card-element" class="h5">Credit or debit card</label>
								<div id="card-element" class="StripeElement StripeElement--invalid">
									<!-- A Stripe Element will be inserted here. -->								  
								</div>
								<div class="col-xs-12">
									<small id="card-errors" role="alert"></small>
								</div>
							</div>
						</div>
						<div class="modal-footer d-block text-center">
							<button type="submit" class="btn bg-blue text-white" id="submitButtonAddCard" disabled>Add Card</button>
							<input type="hidden" name="current_customer_id" value="<?php echo $customer_id; ?>" required />
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
	<!-- End Add New Card Modal -->
			
	</div><!-- End <div class="container-fluid">  -->

    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>
	<script src="/my-account/scripts.js"></script>
	<script>
	// Remove Card
	$(".removeCard").click(function(e){
		e.preventDefault();
		if(confirm("Are you sure you want to delete this card?")){
			window.location.href = $(this).attr('href');
		}
	});
	
	// Set card as default
	$("#default-3").click(function(e){
		e.preventDefault();
		if(confirm("Are you sure you want to make this your default card? Any recurring payments will be charged to this card in the future.")){
			window.location.href = $(".defaultCard").attr('href');
		}
	});
	
	// Update card
	$('#btnUpdateCard').on('click',function(e){
		if(confirm("Are you sure you want to update this card?")){
			let year  = $( "#modalSelectEY option:selected" ).val();
			let month = $( "#modalSelectEM option:selected" ).val();

			$.ajax({
				url : 'xt_updateCard.php',
				type : 'POST',
				data : { 
							'year' : year,
							'month': month,
							'customer': $('#modalCustomerId').val(),
							'card': $('#modalCardId').val(),
						},
				dataType:'json',
				success : function(data) {              
					//console.log('Data: '+data);
					
					if(data['error']){
						alert(data['msg']);
					}else{
						$('#expInfo').text(month + "/" + year);
						
						placeOnTop("alert-success", "Your card has been successfully updated!");
					}				       
				},
				error : function(request,error)
				{
					console.log("Request: "+JSON.stringify(request));
				}
			});
		}
	});

	// Update modal with greater year. This happens before the updating
	$('#updateCardId').on('click',function(e){
		e.preventDefault();
		$('#modalSelectEM').empty();
		$('#modalSelectEY').empty();
		let selectYear  = $(this).data("exp_year");
		let selectMonth = $(this).data("exp_month");
		$('#modalCustomerId').val($(this).data("customer"));
		$('#modalCardId').val($(this).data("card"));

		for (i = 1 ; i <=  12; i++) {
			if(selectMonth == i){
				$("<option></option>", {value: i,selected:true, text: i}).appendTo('#modalSelectEM');
			}else{
				$("<option></option>", {value: i, text: i}).appendTo('#modalSelectEM');
			}				
		}

		for (i = 1 ; i <=  15; i++) {
			$("<option></option>", {value: selectYear + i,text: selectYear + i}).appendTo('#modalSelectEY');			
		}
	});
	
	function placeOnTop(style, msg){
		$(".sticky").html($('<div>', {class:'alert '+ style, html:"<a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>" + msg}));										
	}
	</script>
	<!-- Script to communicate with Stripe -->
	<script src="stripe-add-new-form.js"></script>
  </body>
</html>