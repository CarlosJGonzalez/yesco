<!doctype html>
<html lang="en">
  <head>
	  <link rel="stylesheet" href="/css/checkbox.css">
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/head.php");?>

    <title>Payment Methods | <?php echo CLIENT_NAME; ?></title>
	  
  </head>
  <body class="bg-light cbp-spmenu-push">
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/top.php"); ?>

    <div class="container-fluid">
      <div class="row">
        <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/nav.php");
		include ($_SERVER['DOCUMENT_ROOT'].'/includes/stripe.php');
		?>
        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 p-0 mb-4">
		<?php
			$db->where("storeid",$_SESSION['storeid']);
			//$db->where("email",'sicwing@das-group.com');
			$user_detail = $db->getOne("locationlist", "customer_id");
			//$customer_id = $user_detail["customer_id"] = 'cus_EcTygVeqN4ZQTy';
			$customer_id = $user_detail["customer_id"];
		?>
			<div class="p-0 border-bottom mb-4">
				<div class="d-flex d-block align-items-center clearfix py-2 px-4">
					<h1 class="h2 font-light mb-0 text-center text-sm-left"><i class="fas fa-credit-card mr-2"></i> Payment Methods</h1>
					<div class="ml-auto">
						<form name="addNewCard" method="POST" action="xt_addNewCard.php">
                            <div class="new_card card">
                                 <script
                                    src="https://checkout.stripe.com/checkout.js" class="stripe-button"
                                    data-key=<?=$PKAPIKEY?>
                                    data-name=<?=$STRIPENAME?>
                                    data-image="https://stripe.com/img/documentation/checkout/marketplace.png"
                                    data-locale="auto"
                                    data-zip-code="true"
                                    data-billing-address="true"
                                    data-label="Add New Card"
                                    data-panel-label="Add">
                                  </script>
                            </div>
                            <!--<input type="hidden" name="customer_id" value="cus_EUDYxj7EDu2F9a">-->
							<input type="hidden" name="customer_id" value="<?php echo $customer_id ;?>">
                        </form>
						  <!--<button type="button" class="border-0 bg-transparent" data-toggle="modal" data-target="#addCard">
							<i class="fas fa-2x text-muted fa-plus-circle"></i>
						  </button>-->
					</div>
				</div>
			</div>
			<div class="modal fade" id="addCard" tabindex="-1" role="dialog" aria-labelledby="addCardLabel" aria-hidden="true">
			  <div class="modal-dialog" role="document">
				<div class="modal-content">
				  <div class="modal-header">
					<h5 class="modal-title" id="addCardLabel">Add Payment Method</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					  <span aria-hidden="true">&times;</span>
					</button>
				  </div>
				  <div class="modal-body">
					<div class="form-group">
						<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Card Number</label>
						<input type="text" class="form-control" name="cc_number">
					</div>
					  <div class="form-group">
						<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Expiration Date</label>
						  <div class="row">
							  <div class="col-8">
								  <select name="exp_month" class="form-control custom-select-arrow">
									  <?php 
									  $months = get_months();
									  foreach($months as $key => $val){ ?>
										<option value="<?php echo $key?>"><?php echo $val?></option>
									  <?php } ?>
								  </select>
							  </div>
							  <div class="col-4">
								  <select name="exp_year" class="form-control custom-select-arrow">
									  <?php for($i=date("Y");$i<date("Y")+11;$i++){ ?>
										<option value="<?php echo $i?>"><?php echo $i?></option>
									  <?php } ?>
								  </select>
							  </div>
						  </div>
					</div>
					  <div class="form-group">
						<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Name on Card</label>
						<input type="text" class="form-control" name="cc_number">
					</div>
					  <div class="form-group">
						  <label class="label cusor-pointer d-flex text-center" for="default">
							<input  class="label__checkbox" type="checkbox" name="make_default" value="" type="checkbox" id="default"/>
							<span class="label__text d-flex align-items-center">
							  <span class="label__check d-flex rounded-circle mr-2">
								<i class="fa fa-check icon small"></i>
							  </span>
								<span class="text-uppercase small letter-spacing-1 d-inline-block">Set as default payment method</span>
							</span>
						  </label>
					  </div>
				  </div>
				  <div class="modal-footer">

					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<input type="submit" class="btn bg-blue text-white" value="Add Card">
				  </div>
				</div>
			  </div>
			</div>
        	<div class="px-4 py-3">
				<?php if(isset($_SESSION['success'])){ ?>
				<div class="alert alert-success alert-dismissible fade show mt-4" role="alert">
				  <strong>Success!</strong> <?php echo $_SESSION['success'];?>
				  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				  </button>
				</div>
				<?php unset($_SESSION['success']); } ?>
				<?php if(isset($_SESSION['error'])){ ?>
				<div class="alert alert-danger alert-dismissible fade show mt-4" role="alert">
				  <strong>Error!</strong> <?php echo $_SESSION['error'];?>
				  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				  </button>
				</div>
				<?php unset($_SESSION['error']); } ?>
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
														<i class="fab <?=$icon['icon']?> mr-3 fa-2x"></i>
														<div>
															<p class="mb-1"><?=strtoupper($card->brand). " ". "(" .$card->last4 . ")"?></p>
															<p class="mb-1">Exp: <?=$card->exp_month.'/'.$card->exp_year?></p>
															<p class="mb-1"><? if(isset($card->name)) echo '<br>'.strtoupper($card->name);?></p>
														</div>
													</div>
												</div>
												<?php if(!$is_expired){ ?>
													<div class="col-sm-4 text-right">
														<!--<a href="xt_addNewCard.php?card=<?//=$card_id?>&customer=<?//='cus_EUDYxj7EDu2F9a'?>&action=remove" class="text-uppercase text-danger removeCard">Delete <i class="far fa-trash-alt fa-lg ml-2"></i></a>-->
														<a href="xt_addNewCard.php?card=<?=$card_id?>&customer=<?=$customer_id?>&action=remove" class="text-uppercase text-danger removeCard">Delete <i class="far fa-trash-alt fa-lg ml-2"></i></a>
													</div>
												<? } ?>
											</div>
											<? if($is_regular){ ?>
											<div class="my-2">
												<label class="label cusor-pointer d-flex text-center" for="default-3">
													<input  class="label__checkbox" type="checkbox" name="make_default" value="3" type="checkbox" id="default-3" />
													<!--<a href="xt_addNewCard.php?card=<?//=$card_id?>&customer=<?//=cus_EUDYxj7EDu2F9a?>&action=default" class="defaultCard"></a>-->
													<a href="xt_addNewCard.php?card=<?=$card_id?>&customer=<?=$customer_id?>&action=default" class="defaultCard"></a>
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
						?>
						
						
						<!--
							<div class="card box border-0">
								<div class="p-3">
									<div class="row">
										<div class="col-sm-8">
											<div class="d-flex">
												<i class="fab fa-cc-visa mr-3 fa-2x"></i>
												<div>
													<p class="mb-1">VISA (9865)</p>
													<p class="mb-1">Exp: 08/22</p>
													<p class="mb-1">John Doe</p>
												</div>
											</div>
										</div>
										<div class="col-sm-4 text-right">
											<a href="" class="text-uppercase text-danger">Delete <i class="far fa-trash-alt fa-lg ml-2"></i></a>
										</div>
									</div>
								</div>
								<div class="alert alert-warning rounded-0 mb-0 px-3 py-2">
									<i class="fas fa-info-circle"></i> This is your default payment.
								</div>
							</div>
							<div class="card box border-0">
								<div class="p-3">
									<div class="row">
										<div class="col-sm-8">
											<div class="d-flex">
												<i class="fab fa-cc-amex mr-3 fa-2x"></i>
												<div>
													<p class="mb-1">AMEX (1234)</p>
													<p class="mb-1">Exp: 04/18</p>
													<p class="mb-1">John Doe</p>
												</div>
											</div>
										</div>
										
									</div>
								</div>
								<div class="alert alert-danger rounded-0 mb-0 px-3 py-2">
									<i class="fas fa-exclamation-triangle"></i> This card has expired.
								</div>
							</div>
							<div class="card box border-0">
								<div class="p-3">
									<div class="row">
										<div class="col-sm-8">
											<div class="d-flex">
												<i class="fab fa-cc-visa mr-3 fa-2x"></i>
												<div>
													<p class="mb-1">VISA (8654)</p>
													<p class="mb-1">Exp: 010/24</p>
													<p class="mb-1">John Doe</p>
												</div>
											</div>
										</div>
										<div class="col-sm-4 text-right">
											<a href="" class="text-uppercase text-danger">Delete <i class="far fa-trash-alt fa-lg ml-2"></i></a>
										</div>
									</div>
									<div class="my-2">
										<label class="label cusor-pointer d-flex text-center" for="default-3">
											<input  class="label__checkbox" type="checkbox" name="make_default" value="3" type="checkbox" id="default-3" />
											<span class="label__text d-flex align-items-center">
											  <span class="label__check d-flex rounded-circle mr-2">
												<i class="fa fa-check icon small"></i>
											  </span>
												<span class="text-uppercase small letter-spacing-1 d-inline-block">Set as default payment method</span>
											</span>
										  </label>
									</div>
								</div>
							</div>-->
							
						</div>
					</div>
				</div>
			</div>
			
        </main>
      </div>
    </div>


    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>
	<script src="/my-account/scripts.js"></script>
	<script>		
	$(".removeCard").click(function(e){
			e.preventDefault();
			if(confirm("Are you sure you want to delete this card?")){
				window.location.href = $(this).attr('href');
			}
		});
	$("#default-3").click(function(e){
		e.preventDefault();
		if(confirm("Are you sure you want to make this your default card? Any recurring payments will be charged to this card in the future.")){
			window.location.href = $(".defaultCard").attr('href');
		}
	});
	</script>
  </body>
</html>