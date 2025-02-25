<!doctype html>
<html lang="en">
<head>

	<?php 
		include ($_SERVER['DOCUMENT_ROOT']."/includes/head.php");
		if(!(roleHasPermission('show_plan_and_publish', $_SESSION['role_permissions']))){
			header('location: /');
			exit;
		}

		require_once ($_SERVER['DOCUMENT_ROOT']."/includes/DasApiSDK/vendor/autoload.php");
		use Das\facebook\FbUtils;
		use Das\facebook\Page;		

		$post_id = filter_var(filter_var($_GET['post_id'], FILTER_VALIDATE_INT)) ? $_GET['post_id'] : false;
	?>

	<title> FB Promoted </title>

	<style>
		.none_upload{ display:none;text-align:center;}	
		.loader {
        	position: fixed;
        	left: 0px;
        	top: 0px;
        	width: 100%;
        	height: 100%;
        	z-index: 9999;
        	background: url('/../../yextAPI/spinner_preloader.gif') 50% 50% no-repeat rgba(255, 255, 255, 0.3);
        } 

		.bg-lightgray{
			background-color: #f5f7f8;
		}
		.petitie-caps{
			font-variant: all-petite-caps;
		}
		.logo-round{
			background-color: black;
			min-height: 4rem;
			min-width: 4rem;
			border-radius:4rem;
		}
		.left-col{
			max-height: 80vh;
			overflow-y: scroll;
			overflow-x: hidden;
		}
		.info{
			color: darkgray;
			font-size: small;
		}
		.text-darkgray{
			color: darkgray;
		}
		.username{
			color: black; 
			font-size: 1.5rem; 
			margin-bottom: 0rem; 
			max-width: 23rem; 
			line-height: 2.5rem;
		}
		.w-30{
			width: 30%;
		}
	</style>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.css" />
	<script src="https://js.stripe.com/v3/"></script>
	<link rel="stylesheet" type="text/css" href="/payments/stripe-add-new-form.css"/>
</head>
<body class="bg-light cbp-spmenu-push">
	<? include ($_SERVER['DOCUMENT_ROOT']."/includes/top.php"); ?>

	<div class="container-fluid">
		<div class="row">
			<? include ($_SERVER['DOCUMENT_ROOT']."/includes/nav.php"); ?>

			<main role="main" class="col-md-9 ml-sm-auto col-lg-10 pt-2 px-4 mb-4">
				<div id="spinner_loading" class="none_upload loader"></div>
				<?php include $_SERVER['DOCUMENT_ROOT']."/includes/alerts.php"; ?>

				<div class="p-0 border-bottom mb-1">
					<div class="d-flex d-block align-items-center clearfix py-0 px-4">
						<h1 class="h2 font-light mb-0 text-center text-sm-left"><i class="fas fa-chart-line"></i> Facebook Boost</h1>
					</div>
				</div>

				<div class="px-4 pt-0">
					<?php 

					$post_interests = isset($active_location['interests'])? $active_location['interests'] : '';
					
					$fbPage = new Page($token_api);
					$fbInfo = $fbPage->getPageByClient($_SESSION['client'],$_SESSION['storeid']);
					
					if( !isset($fbInfo['data'][0]) ){
						$msg = 'Sorry, there was an error with your Facebook configuration. Please contact support for further assistance.';
						?>
						<div class="alert alert-danger alert-dismissible fade show" role="alert">
							<strong>Error!</strong> <?php echo $msg;?>
							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<?php
					}else{

						$fbInfo = $fbInfo['data'][0];
						$id_page = $fbInfo['id_page'];
						$latitude = 0;
						$longitude = 0;
						if ($fbInfo['location'] != "") {
							$location = json_decode($fbInfo['location']);
							$latitude = $location->latitude;
							$longitude = $location->longitude;
						}						
						?>

						<div class="row mb-4 dashboard">
							<div class="col-sm-12">
								<div class="box px-3">
									<div class="row">
										<?php
										$fbUtils = new FbUtils( $token_api );
										$targeting = $fbUtils->getTargeting($_SESSION['client'],$_SESSION['storeid']);
										$interests = array();
										$behaviors = array();
										if( isset($targeting["data"]['0']) ){

											if( isset($targeting["data"]['0']['interests']) ){
												$interests = explode(',', $targeting["data"]['0']['interests']);
											}
											if( $targeting["data"]['0']['behaviors'] ){
												$behaviors = explode(',', $targeting["data"]['0']['behaviors']);
											}
										}
										?>
										<div class="col-sm-4 px-0">
											<div class="mb-3">
												<h5 class="bg-lightgray petitie-caps border-bottom p-2">AUDIENCE (Total Users: <label id="total_audience">0</label>)</h5>

													<div class="pl-3 py-2">
														<span class="letter-spacing-1 text-uppercase small mr-2 mr-xl-0">Interest:</span><br>
														
														<select name="interest_filter" id="interest_filter" class="chosen_filter flex-grow form-control form-control-sm w-auto rounded-pill custom-select-arrow pr-4" data-column="0" multiple>
															<?php foreach ($interests as $interest) { ?>	
																<option  value="<?php echo $interest;?>"><?php echo $interest;?></option>
															<?php } ?>
														</select>
													</div>
												<!--	<div class="pl-3 py-2">
														<span class="letter-spacing-1 text-uppercase small mr-2 mr-xl-0">Behavior:</span><br>
														
														<select name="behavior_filter" id="behavior_filter" class="chosen_filter flex-grow form-control form-control-sm w-auto rounded-pill custom-select-arrow pr-4" data-column="0" multiple>
															<?php foreach ($behaviors as $behavior) { ?>	
																<option  value="<?php echo $behavior;?>"><?php echo $behavior;?></option>
															<?php } ?>
														</select>
													</div>-->
											</div>
											<div class="mb-3">
												<input type="hidden" name="post_interests" id="post_interests" value="<?php echo $post_interests;?>">
												<input type="hidden" name="latitude" id="latitude" value="<?php echo $latitude;?>">
												<input type="hidden" name="longitude" id="longitude" value="<?php echo $longitude;?>">
												<h5 class="bg-lightgray petitie-caps border-bottom p-2">Location Information</h5>

												<div class="row">
													<div class="col-10 ml-3">
														<label class="mb-0" for="input_location">Location
															<button type="button" data-toggle="tooltip" data-placement="top" title="Your Info Here" style="border:none;background-color:white;color:darkgray;padding:0px;">
															<i class="fas fa-info-circle"></i>
														</button>
														</label>
													</div>
													<div class="col-10 ml-3">
														<!--Popluate with location address-->
														<input type="text" class="form-control" id="input_location" name="location" readonly value="<?php echo $active_location['address'].' '.$active_location['city'].', '.$active_location['state'].', '.$active_location['zip'];?>">
													</div>
												</div>
												<div class="row">
													<div class="col-10 ml-3">
														<label class="mb-0" for="input_radius">Ad Radius<span class="text-danger">*</span>
															<button type="button" data-toggle="tooltip" data-placement="top" title="Radius around latitude/longitude, in miles unless otherwise in distance_unit. From 5 to 45 miles" style="border:none;background-color:white;color:darkgray;padding:0px;">
															<i class="fas fa-info-circle"></i>
														</button>
														</label>
													</div>
													<div class="col-10 ml-3">
														<select name="radius" id="input_radius" class="form-control mb-3" required>
															<option value="">---</option>
															<option value="5">5</option>
															<option value="10">10</option>
															<option value="15" selected>15</option>
															<option value="20" >20</option>
															<option value="25" >25</option>
															<option value="30">30</option>
															<option value="35">35</option>
															<option value="40">40</option>
															<option value="45">45</option>
														</select>
													</div>
												</div>

												<div class="form-row">
													<div class="form-group col-md-6 ml-3">
														<label class="mb-0" for="age_min">Age Min<span class="text-danger">*</span>
															<button type="button" data-toggle="tooltip" data-placement="top" title="Minimum age. If used, must be 13 or higher. Default to 18" style="border:none;background-color:white;color:darkgray;padding:0px;">
															<i class="fas fa-info-circle"></i>
															</button>
														</label>
														<input type="number" class="form-control" id="age_min" name="age_min" value="18" min="14" max="65" required>
													</div>
													<div class="form-group col-md-4">
														<label class="mb-0" for="age_max">Age Max<span class="text-danger">*</span>
																<button type="button" data-toggle="tooltip" data-placement="top" title="Maximum age. If used, must be 65 or lower." style="border:none;background-color:white;color:darkgray;padding:0px;">
																<i class="fas fa-info-circle"></i>
															</button>
														</label>
														<input type="text" class="form-control money" id="age_max" name="age_max" value="55" min="14" max="65" required>
													</div>
												</div>


											</div>

											<div class="mb-3">												
												<h5 class="bg-lightgray petitie-caps border-bottom p-2">DURATION AND BUDGET</h5>

												<div class="form-row">
													<div class="form-group col-md-6 ml-3">
														<label class="mb-0" for="input_duration">Days<span class="text-danger">*</span>
															<button type="button" data-toggle="tooltip" data-placement="top" title="Your Info Here" style="border:none;background-color:white;color:darkgray;padding:0px;">
															<i class="fas fa-info-circle"></i>
															</button>
														</label>
														<input type="number" class="form-control" id="input_duration" name="duration" value="5" min="1" required>
													</div>
													<div class="form-group col-md-4">
														<label class="mb-0" for="input_total_budget">Total Budget<span class="text-danger">*</span>
																<button type="button" data-toggle="tooltip" data-placement="top" title="You must spend a minimum of $5/day." style="border:none;background-color:white;color:darkgray;padding:0px;">
																<i class="fas fa-info-circle"></i>
															</button>
														</label>
														<input type="text" class="form-control money" id="input_total_budget" name="total_budget" value="25.00" required>
													</div>
												</div>												
											</div>	

											<div class="mb-3">
												
												<h5 class="bg-lightgray petitie-caps border-bottom p-2">PAYMENT INFO</h5>

												<p class="info pl-3">
													Payment 
													<button type="button" data-toggle="tooltip" data-placement="top" title="Your Info Here" style="border:none;background-color:white;color:darkgray;padding:0px;">
														<i class="fas fa-info-circle"></i>
													</button>
												</p>

												<div class="row">
													<div class="col-10 ml-3">
														<input type="hidden" name="seo_city" value="<?php echo $active_location['seo_city'];?>">
														<input type="hidden" name="url" value="<?php echo $active_location['url'];?>">
														<input type="hidden" name="receipt_email" value="<?php echo $active_location['email'];?>">
														<input type="hidden" id="customer_id" name="customer_id" value="<?php echo $active_location['customer_id'];?>">
														<input type="hidden" name="campaign_name">
														<input type="hidden" name="source_token" >

														<select class="form-control mb-3" name="payment-method" id="payment_method" required>
															<?php
															require_once ($_SERVER['DOCUMENT_ROOT']."/includes/ClassDasStripe.php");

															if(isset($active_location['customer_id']) && $active_location['customer_id'] != ''){
																$dasStripe = new Das_Stripe($db,$token_api,$_SESSION['client'],isset($_SESSION['storeid']) ? $_SESSION['storeid'] : null);
																$card_id_inc = isset($inc["card_id"]) ? $inc["card_id"] : false;
																$cards = $dasStripe->getCards($active_location['customer_id']);
																
																foreach($cards as $source ){
																	$source = ((object)$source);

																	$id = $source->id;
																	if($source->object == "source"){
																		if($source->card === null)
																			continue;
																		$source =  (object)$source->card;
																	}
																	if(isset($source->cvc_check)){
																		if(($source->address_line1_check == "fail" ) || ($source->address_zip_check == "fail" ) || ($source->cvc_check != "pass" ) ||
																			(strtotime($source->exp_year."-".$source->exp_month."-1") <= strtotime(date("Y-n-d")))){
																			continue;
																		}
																	}
																?>
																<option value="<?=$id?>"
																	<?php echo ( $dasStripe->isDefaultCard($active_location['customer_id'],$id) && !isset($inc))? "selected": ""?> <?php echo ($id == $card_id_inc ) ?"selected": ""?> >
																	<?php echo $source->brand. " Ending in ".$source->last4 ?>
																</option>
															<?php } ?>
														<?php } ?>

													</select>
													<button class='form-control btn bg-light d-block mb-4 border' data-toggle="modal" data-target="#addNewCard" type="button" data-backdrop="static" data-keyboard="false"><i class="fa fa-credit-card"></i> Add New Card</button>
												</div>
												<div class="col">

												</div>
											</div>

										</div>

									</div>	

									<div class="col-sm-8 p-4" style="background-color: #e9ebee;">
										<div class="container border mx-auto" style="width:75%;background-color: white;">
											<div class="row py-3 px-3">
												<div class="col-xs my-auto"><div class="logo-round" style="background-img:url();"></div>
											</div>
											<div class="col px-4 py-2">
												<h3 class="username"><?php echo $fbInfo['name_page'];?></h3>
												<span class="text-darkgray">Sponsored - <i class="fas fa-globe-americas"></i></span>
											</div>
										</div>
										<div id="post_information">

										</div>
										<div class="row">
											<div class="col py-3 my-auto">
												<h6 class="text-center m-0"><i class="far fa-thumbs-up"></i> Like</h6>
											</div>
											<div class="col py-3 my-auto">
												<h6 class="text-center m-0"><i class="far fa-comment-alt"></i> Comment</h6>
											</div>
											<div class="col py-3 my-auto">
												<h6 class="text-center m-0"><i class="fas fa-share-square"></i> Share</h6>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="text-right px-4">
					<button type="button" class="btn save bg-dark-blue text-white btn-sm px-4" value="Boost" id="boostPostId"> Boost </button>
				</div>
			<?php } ?>
		</div>
	</main>
</div>
</div>
	<form action="xt_new_stripe_object.php" method="post" name="addNewCard2" id="payment-form">
	<div class="modal" tabindex="-1" role="dialog" id="addNewCard">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title text-center">DAS Group</h5>
					<button type="button" class="close" data-dismiss="modal" id="close-btn-modal-addcard"aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
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
					</div>
				</div>
			</div>
		</div>
	</form>
<?php
include ($_SERVER['DOCUMENT_ROOT']."/includes/footer.php");
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.js" type="text/javascript"></script>
<script>
	$("#interest_filter").chosen({no_results_text: "Oops, nothing found!"});
	//$("#behavior_filter").chosen({no_results_text: "Oops, nothing found!"});
	//$("#behavior_filter").addClass('d-none');
	$("#interest_filter").addClass('d-none');
	getReachEstimate();

	get_posts(<?php echo $post_id;?>);
	/*$("#behavior_filter").change(function(e, params){
		 getReachEstimate();
	});*/

	$("#age_max").change(function(e, params){
		 getReachEstimate();
	});

	$("#age_min").change(function(e, params){
		 getReachEstimate();
	});

	$("#interest_filter").change(function(e, params){
		 getReachEstimate();

	});

	$("#input_radius").change(function(e, params){
		 getReachEstimate();
	});

	$('#boostPostId').on('click',function(e){
		e.preventDefault();
		$('#boostPostId').prop('disabled', true);

		$('#spinner_loading').removeClass("none_upload");

		var behaviors = '';
		//var behaviors = $("#behavior_filter").val();
		var interests = $("#interest_filter").val();
		var latitude  = $("#latitude").val();
		var longitude = $("#longitude").val();
		var radius    = $('#input_radius').find(":selected").val();

		var age_max = $("#age_max").val();
		var age_min = $("#age_min").val();

		var link 	  = $("#link").val();
		var post 	  = $("#post").val();
		var http_path = $("#http_path").val();
		var media 	  = $("#media").val();
		var post_interests 	  = $("#post_interests").val();

		var payment_method = $('#payment_method').find(":selected").val();
		var customer_id    = $('#customer_id').val();

		var input_duration = $('#input_duration').val();
		var input_total_budget    = $('#input_total_budget').val();
		var post_date    = $('#post_date').val();

		var post_id = <?php echo $post_id;?>;
		var id_page = <?php echo $id_page;?>;

		$.ajax({
        url: "xt_boostPost.php",
        method: "POST",
        dataType: "json",
        data: {
        		id_page:id_page,
        		post_id:post_id,
        		post_date:post_date,
        		input_duration:input_duration,
        		input_total_budget:input_total_budget,
        		customer_id:customer_id,
        		payment_method:payment_method,
        		post:post,
        		post_interests:post_interests,
        		http_path:http_path,
        		media:media,
        		link:link,
        		behaviors: behaviors,
        		interests: interests,
        		latitude:latitude,
        		longitude:longitude,
        		radius:radius,
        		age_min:age_min,
        		age_max:age_max
        	},
        success: function( data ) {
        	$('#spinner_loading').addClass("none_upload");
            if( data['error'] == 0 ){
            	
            	window.location.href = '/plan-and-publish/social-media/';
            }else{
            	$('#boostPostId').prop('disabled', false);
            	location.reload();
            }
        
        }
    });
	});

	function get_posts(post_id){
		$('#boostPostId').prop('disabled', true);
		$.ajax({
	        type: "POST",
	        url: "xt_get_post.php",
	        data: {"post_id":post_id},
	        cache: false,
			beforeSend:function(html){
	            $("#post_information").html('<div class="text-center"><img src="/img/loading.svg"></div>');
	        },
	        success: function(html){
	            $("#post_information").html(html);
	            $('#boostPostId').prop('disabled', false);
	        },
			error: function(xhr, status, error) {
			  var err = eval("(" + xhr.responseText + ")");
			  $('#boostPostId').prop('disabled', true);
			  console.log(err.Message);
			} 
	    });
	}

	function getReachEstimate(){
		//var behaviors = $("#behavior_filter").val();
		var behaviors = '';
		var interests = $("#interest_filter").val();
		var latitude = $("#latitude").val();
		var longitude = $("#longitude").val();
		var radius = $('#input_radius').find(":selected").val();

		var age_max = $("#age_max").val();
		var age_min = $("#age_min").val();


		$.ajax({
        url: "xt_getReachEstimate.php",
        method: "POST",
        data: {behaviors: behaviors,interests: interests, latitude:latitude,longitude:longitude, radius:radius,age_min:age_min,age_max:age_max},
        success: function( html ) {
            console.log(html);  
            $('#total_audience').text(html);        
        }
    });
	}

</script>
<script src="/payments/stripe-add-new-form.js"></script>
</body>
</html>