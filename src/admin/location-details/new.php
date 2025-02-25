<!doctype html>
<html lang="en">
  <head>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<link href="/css/smart_wizard.min.css" rel="stylesheet" type="text/css" />
	<link href="/css/smart_wizard_theme_arrows.min.css" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" href="/css/checkbox.css">
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/head.php");?>

    <title>Add New Location | <?php echo CLIENT_NAME; ?></title>
	<style>
	label.error{
		color:red;
		font-weight: bold;
	}
	</style>
  </head>
  <body class="bg-light cbp-spmenu-push">
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/top.php"); ?>

    <div class="container-fluid">

      <div class="row">
        <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/nav.php"); ?>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 mb-4 p-0">
			<div class="breadcrumbs bg-white px-4 py-1 border-bottom small">
				<a href="/admin/location-details/" class="text-muted">All Locations</a>
				<span class="mx-1">&rsaquo;</span>
				<span class="font-weight-bold text-muted">Add New Location</span>
			</div>
			<div class="border-bottom-dotted d-flex d-block align-items-center clearfix py-2 px-4 mb-4">
				<h1 class="h2 font-light mb-0 text-center text-sm-left">
					<span class="fa-layers fa-fw mr-2">
						<i class="fas fa-map-marker"></i>
						<i class="fas fa-plus-circle fa-inverse" data-fa-transform="shrink-8 up-2"></i>
					 </span>
					 Add New Location</h1>
			</div>
			
			<div class="py-3 px-4">

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

				<form action="xt_new.php" method="POST" id="addLocation" name="addLocation">
					<div id="smartwizard" class="border-0">
						<ul>
							<li><a href="#step-1">Core<small class="d-none d-sm-block">Enter core information</small></a></li>
							<li><a href="#step-2">Location<small class="d-none d-sm-block">Enter location information</small></a></li>
							<li><a href="#step-3">Contact<small class="d-none d-sm-block">Enter contact information</small></a></li>
							<li><a href="#step-4">Dates<small class="d-none d-sm-block">Initiate start dates</small></a></li>
							<li><a href="#step-5">Login Credentials<small class="d-none d-sm-block">Access Setup Login Credentials</small></a></li>
						</ul>
						<div class="py-3 px-2 bg-white">
							<div id="step-1">
								<div class="row">
									<div class="col-sm-6 form-group">
										<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Store ID<span class="text-danger">*</span></label>
										<input type="text" class="form-control rounded-bottom rounded-right" name="storeid" id="storeid" placeholder="123456" required>
										<label id="storeid_msg_container" class="error" for="storeid"></label>
									</div>
									<div class="col-sm-6 form-group">
										<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Business Name<span class="text-danger">*</span></label>
										<input type="text" class="form-control rounded-bottom rounded-right" name="companyname" id="companyname" placeholder="Miami, FL" required>
										<small>Required Format: City, State <em>i.e. Miami, FL</em></small>
									</div>
									<div class="col-sm-6 form-group">
										<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Display Name<span class="text-danger">*</span></label>
										<input type="text" class="form-control rounded-bottom rounded-right" name="displayname" id="displayname" placeholder="<?php echo CLIENT_NAME; ?> Miami, FL" required>
										<small>Required Format: <?php echo CLIENT_NAME; ?> City, State <em>i.e. <?php echo CLIENT_NAME; ?> Miami, FL</em></small>
									</div>
									<div class="col-sm-6 form-group">
										<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Primary Location Email<span class="text-danger">*</span></label>
										<input type="email" class="form-control" id="primary_loc_email" name="email" required>
									</div>
									<div class="col-sm-6 form-group">
										<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Business Phone</label>
										<input type="tel" class="form-control phone" name="phone" id="phone" required>
									</div>
									<div class="col-sm-6 form-group">
										<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Website<span class="text-danger">*</span></label>
										<div class="input-group">
										  <div class="input-group-prepend">
											<span class="input-group-text rounded-left" id="basic-addon3"><?php echo CLIENT_URL; ?>locations/</span> 
										  </div>
										  <input type="text" class="form-control rounded-bottom rounded-right" name="url" id="basic-url" aria-describedby="basic-addon3" placeholder="miami-fl" required>
										</div>
										<small class="d-block">Include only the slug (ex. miami-fl)</small>
									</div>
<!--
									<div class="col-sm-4 form-group">
									  <span class="switch">
										<input type="checkbox" class="switch" id="switch-gmb">
										<label for="switch-gmb" class="font-weight-light font-lg letter-spacing-1 d-inline-block">Google My Business</label>
									  </span>
									</div>
									<div class="col-sm-4 form-group">
									  <span class="switch">
										<input type="checkbox" class="switch" id="switch-yext">
										<label for="switch-yext" class="font-weight-light font-lg letter-spacing-1 d-inline-block">Yext</label>
									  </span>
									</div>
-->
								</div>
							</div>
							<div id="step-2" class="">
								<span class="border-bottom d-block pb-2 mb-3 font-weight-bold text-uppercase letter-spacing-1 text-dark">Address</span>
								<div class="row mb-4">
									<div class="col-sm-6">
										<div class="field form-group">
											<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Address</label>
											  <input type="text" class="form-control rounded-bottom rounded-right" name="address" id="address1" onFocus="initAutocomplete()" autocomplete="off" placeholder="" required>
										</div>
									</div>
									<div class="col-sm-6">
										<div class="field form-group">
											<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Address 2</label>
											<input type="text" class="form-control rounded-bottom rounded-right" name="address2" id="address2">
											<small>Apartment, suite, unit, building, floor, etc</small>
										</div>
									</div>
									<div class="col-sm-6">
										<div class="field form-group">
											<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">City</label>
											<input type="text" class="form-control rounded-bottom rounded-right" name="city" id="locality" required>
										</div>
									</div>
									<div class="col-sm-6">
										<div class="field form-group">
											<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">State</label>
											<div class="select-wrapper">
												<select name="state" class="form-control rounded-bottom rounded-right bg-white border" id="administrative_area_level_1" required>
													<?php echo stateSelect();?>
												</select>
											</div>
										</div>
									</div>
									<div class="col-sm-6">
										<div class="field form-group">
											<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Zip</label>
											<input type="text" class="form-control rounded-bottom rounded-right" name="zip" id="postal_code" required>
										</div>
									</div>
									<input type="hidden" class="field" id="street_number" name="street_number" disabled="true">
									<input type="hidden" class="field" id="route" name="route" disabled="true">
									<input type="hidden" class="field" id="country" name="country" disabled="true">
									<!--<div class=" col-12 text-center">
										<button class="btn bg-dark-blue rounded-pill btn-small text-white letter-spacing-1 px-4 text-uppercase" id="validateAddress">Verify Address</button>
										<svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2" class="checkAnimate ml-2">
										  <circle class="path circle" fill="none" stroke="#73AF55" stroke-width="6" stroke-miterlimit="10" cx="65.1" cy="65.1" r="62.1"/>
										  <polyline class="path check" fill="none" stroke="#73AF55" stroke-width="6" stroke-linecap="round" stroke-miterlimit="10" points="100.2,40.2 51.5,88.8 29.8,67.5 "/>
										</svg>
									</div>-->
									<!--Address suggestion modal-->
									<div class="modal fade" id="sugModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
									  <div class="modal-dialog modal-dialog-centered" role="document">
										<div class="modal-content">
										  <div class="modal-header">
											<h5 class="modal-title" id="exampleModalLabel">Verify Your Address Details</h5>
											<button type="button" class="close" data-dismiss="modal" aria-label="Close">
											  <span aria-hidden="true">&times;</span>
											</button>
										  </div>
										  <div class="modal-body mb-2">
											  <p>Address entered may be incorrect or incomplete. Please review our recommendation and select from the options below.</p>
											  <div class="row">
												  <div class="col-sm-6">
													  <span class="text-uppercase letter-spacing-1 font-weight-bold text-dark">You Entered</span>
													  <p class="mb-0" id="address1_entered"></p>
													  <p class="mb-0" id="address2_entered"></p>
													  <p class="mb-0"><span id="locality_entered"></span>, <span id="administrative_area_level_1_entered"></span></p>
													  <p id="postal_code_entered"></p>
													  <button type="button" class="btn btn-sm btn-outline-dark rounded-0 text-uppercase letter-spacing-1" data-dismiss="modal">Keep This Address</button>
												  </div>
												  <div class="col-sm-6">
													  <span class="text-uppercase letter-spacing-1 font-weight-bold text-dark">We Suggest</span>
													  <p class="mb-0"><span id="primary_number_sug"></span> <span id="street_name_sug"></span> <span id="street_suffix_sug"></span></p>
													  <p class="mb-0"><span id="secondary_designator_sug"></span> <span id="secondary_number_sug"></span></p>
													  <p class="mb-0"><span id="city_name_sug"></span>, <span id="state_abbreviation_sug"></span></p>
													  <p id="zipcode_sug"></p>
													  <button type="button" class="btn btn-sm bg-dark-blue rounded-0 text-uppercase letter-spacing-1 text-white" id="useAddress">Use This Address</button>
												  </div>
											  </div>
										  </div>
										
										</div>
									  </div>
									</div>

								</div>
								<span class="border-bottom d-block pb-2 mb-3 font-weight-bold text-uppercase letter-spacing-1 text-dark">location hours</span>
								<div class="my-3 row no-gutters d-xl-flex justify-content-between">
									<?php
									$days = Array("Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday");
									foreach($days as $day){
										 $abbr = strtolower(substr($day,0,3));
									?>
									<div class="mb-3 mb-lg-0 col-6 col-lg-4 col-xl-auto flex-xl-fill">
										<span class="h6 d-block text-lg-center"><?php echo $day ?></span>
										<div class="d-flex hour align-items-center">
											<div class="select-wrapper">
												<select name="<?=$abbr?>_open" class="w-auto p-1 pr-3 text-dark rounded">
													<?
													$start = "5:00";
													$end = "20:00";

													$tStart = strtotime($start);
													$tEnd = strtotime($end);
													$tNow = $tStart;

													while($tNow <= $tEnd){
														if($tNow == strtotime($row[$abbr.'_open']))
															echo "<option value='".date("H:i",$tNow)."' selected>".date("g:i A",$tNow)."</option>";
														else
															echo "<option value='".date("H:i",$tNow)."'>".date("g:i A",$tNow)."</option>";
													  $tNow = strtotime('+30 minutes',$tNow);
													}
													?>
												</select>
											</div>
											<span class="mx-1">-</span>
											<div class="select-wrapper">
												<select name="<?=$abbr?>_close" class="w-auto p-1 pr-3 text-dark rounded">
													<?php
													$start = "5:00";
													$end = "20:00";

													$tStart = strtotime($start);
													$tEnd = strtotime($end);
													/* The below line of code causes the next error: "Something went wrong, try again. 
													 * Exception : Location create or update failed: Business Hours: Value must be an hours interval" 
													*/
													//$tNow = $tStart;
													$tNow = strtotime('+1 hours '.$start);

													while($tNow <= $tEnd){
														if($tNow == strtotime($row[$abbr.'_close']))
															echo "<option value='".date("H:i",$tNow)."' selected>".date("g:i A",$tNow)."</option>";
														else
															echo "<option value='".date("H:i",$tNow)."'>".date("g:i A",$tNow)."</option>";
													  $tNow = strtotime('+30 minutes',$tNow);
													}
													?>
												</select>
											</div>
										</div>
										<div class="mt-2">
										  <label class="label cusor-pointer d-flex text-center" for="<?php echo $abbr?>_opt_c">
											<input  class="label__checkbox" type="checkbox" name="<?php echo $abbr?>_opt" value="C" type="checkbox" id="<?php echo $abbr?>_opt_c" <?php if($row[$abbr.'_opt'] == "C") echo "checked"; ?> />
											<span class="label__text d-flex align-items-center">
											  <span class="label__check d-flex rounded-circle mr-2">
												<i class="fa fa-check icon small"></i>
											  </span>
												<span class="text-uppercase small letter-spacing-1 d-inline-block">Closed</span>
											</span>
										  </label>
										</div>
										  <label class="label cusor-pointer text-center d-flex" for="<?php echo $abbr?>_opt_a">
											<input  class="label__checkbox" type="checkbox" name="<?php echo $abbr?>_opt" value="A" type="checkbox" id="<?php echo $abbr?>_opt_a" <?php if($row[$abbr.'_opt'] == "A") echo "checked"; ?> />
											<span class="label__text d-flex align-items-center">
											  <span class="label__check d-flex rounded-circle mr-2">
												<i class="fa fa-check icon small"></i>
											  </span>
												<span class="text-uppercase small letter-spacing-1 d-inline-block">By Appt Only</span>
											</span>
										  </label>



											<?php if($day=="Monday"){ ?>
												<small id="applyAll" class="cursor-pointer">Apply to business days</small>
											<?php } ?>
									</div>
									<?php } ?>
								</div>
							</div>
							<div id="step-3" class="">
								<div class="row">
									<div class="col-md-6">
										<span class="border-bottom d-block pb-2 mb-3 font-weight-bold text-uppercase letter-spacing-1 text-dark">Primary Contact</span>
										<div class="form-group">
											<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">First Name</label>
											<input type="text" class="form-control rounded-bottom rounded-right" name="fname1" id="fname1" value="<?php echo $row['fname1']?>">
										</div>
										<div class="form-group">
											<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Last Name</label>
											<input type="text" class="form-control rounded-bottom rounded-right" name="lname1" id="lname1" value="<?php echo $row['lname1']?>">
										</div>
										<div class="form-group">
											<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Phone</label>
											<input type="tel" class="form-control rounded-bottom rounded-right phone" name="phone1" value="<?php echo $row['phone1']?>">
										</div>
										<div class="form-group">
											<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Email Address</label>
											<input type="email" class="form-control rounded-bottom rounded-right" name="reportemail" value="<?php echo $row['reportemail']?>">
										</div>
									</div>
									<div class="col-md-6">
										<span class="border-bottom d-block pb-2 mb-3 font-weight-bold text-uppercase letter-spacing-1 text-dark">Secondary Contact</span>
										<div class="form-group">
											<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">First Name</label>
											<input type="text" class="form-control rounded-bottom rounded-right" name="fname2" value="<?php echo $row['fname2']?>">
										</div>
										<div class="form-group">
											<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Last Name</label>
											<input type="text" class="form-control rounded-bottom rounded-right" name="lname2" value="<?php echo $row['lname2']?>">
										</div>
										<div class="form-group">
											<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Phone</label>
											<input type="tel" class="form-control rounded-bottom rounded-right phone" name="phone2" value="<?php echo $row['phone2']?>">
										</div>
										<div class="form-group">
											<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Email Address</label>
											<input type="email" class="form-control rounded-bottom rounded-right" name="altreportemail" value="<?php echo $row['altreportemail']?>">
										</div>
									</div>
								</div>
							</div>
							<div id="step-4" class="">
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Website Launch Date</label>
											<input type="text" class="form-control rounded-bottom rounded-right datepicker" name="launch_date" value="<?php echo $row['launch_date']?>">
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Campaign Start Date</label>
											<input type="text" class="form-control rounded-bottom rounded-right datepicker" name="campaign_start" value="<?php echo $row['campaign_start']?>">
										</div>
									</div>
								</div>
							</div>
							<div id="step-5">
								<div class="row">
									<div class="col-sm-4 form-group">
										<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">User Email<span class="text-danger">*</span></label>
										<input type="email" class="form-control" id="user_email" name="user_email" required>
										<label id="user_email_msg_container" class="alert alert-warning mt-1" for="user_email"></label>
									</div>
									<div class="col-sm-4 form-group login_credentials">
										<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">User Password<span class="text-danger">*</span></label>
										<input type="text" class="form-control" id="user_password" name="user_password" value="" required>
									</div>
									<div class="col-sm-4 form-group login_credentials">
										<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Name<span class="text-danger">*</span></label>
										<input type="text" class="form-control" id="user_full_name" name="user_full_name" required>
									</div>
									<div class="col-sm-4 form-group login_credentials">
									  <span class="switch">
										<input type="checkbox" class="switch" id="switch-generate-password" name="switch-generate-password" value="0">
										<label for="switch-generate-password" class="font-weight-light font-lg letter-spacing-1 d-inline-block">Generate a random password</label>
									  </span>
									</div>
									<div class="col-sm-4 form-group login_credentials">
									  <span class="switch">
										<input type="checkbox" class="switch" id="switch-send-password" name="switch-send-password" value="">
										<label for="switch-send-password" class="font-weight-light font-lg letter-spacing-1 d-inline-block">Send password by email</label>
									  </span>
									</div>
								</div>
							</div>

						</div><!-- End <div class="py-3 px-2 bg-white"> -->
					</div>
				</form>
		  </div>
        </main>
      </div>
    </div>


    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>
	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDcZQgytDL7Lwlc76bzc7MA5bt-cq2mqO8&libraries=places&callback=initAutocomplete" async defer></script>

	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script type="text/javascript" src="/js/jquery.smartWizard.min.js"></script>
	<script type="text/javascript" src="/js/jquery.validate.js"></script>
	<!--<script type="text/javascript" src="/js/jquery.validate.min.js"></script>-->
	<script type="text/javascript" src="/js/maskedinput.min.js"></script>
	<script type="text/javascript">
		$(document).ready(function(){
			$("input.phone").mask("999-999-9999");
			
			$('#smartwizard').smartWizard({
							theme:"arrows",
							keyNavigation:true,
							useURLhash:false,
							showStepURLhash:false,
							toolbarSettings: {
								toolbarExtraButtons: [
								$('<button></button>').text('Add Location')
								.addClass('btn bg-blue text-white').addClass('submit')
								.attr('id', 'addLocBtn').attr('name', 'addLocBtn')
								.attr('type', 'submit')
								]
							}
						});

			//Executes an action when the user wants to move to the next step
			$("#smartwizard").on("leaveStep", function(e, anchorObject, stepNumber, stepDirection) {
				
				var form_wizard = $( "#addLocation" );
				
				var jQueryValidatorObj, id_invalid_element_set, step_form, id_required_element, id_invalid_element, step_form_int, step_form_ok;
				
				// stepDirection === 'forward' :- this condition allows to do the form validation
				// only on forward navigation, that makes easy navigation on backwards still do the validation when going next
				if(stepDirection === 'forward' && form_wizard){
					
					//validate.js
					jQuery.validator.setDefaults({
					  //debug: true,
					  success: "valid",
					});
					
					//Validates the form_wizard
					jQueryValidatorObj =  form_wizard.validate({
						ignore: [],
						submitHandler: function(form) {
							form.submit();
						}
					});
					
					//Returns true if the form_wizard has errors. Otherwise, it return false
					if(!form_wizard.valid()){
						//Gets the invalidElements of the entire form
						id_invalid_element_set = jQueryValidatorObj.invalidElements();
					}
					
					//It constains the string "failed" for every invalid element
					step_form_ok = [];
					
					//The stepNumber is 0 by default, but our div starts with 1
					step_form_int = parseInt(stepNumber);
					step_form_int = step_form_int + 1;
					step_form = '#step-' + step_form_int;

					//Finds all inputs fields from the stepNumber
					$(step_form).find('input').each(function(){
						//Finds all required fields from the stepNumber
						if($(this).prop('required')){
							//Constains the id value of every required element base on the stepNumber where the user is
							id_required_element = $(this).attr("id");
							/* If a required field from the step exists on the invalidElements of the entire form,
							 * it will add "failed" to step_form_ok. Otherwise, the array will contains undefined
							*/
							step_form_ok.push(checkStepFormErros(id_required_element));
						}
					});
						
					//Returns "failed" if a required field from the step exists on the invalidElements of the entire form
					function checkStepFormErros(id_required_element){
						var error_set;
					
						$( id_invalid_element_set ).each(function( i ) {
							
							id_invalid_element = $(this).attr("id");
							
							if(id_required_element === id_invalid_element){
								error_set = 'failed';
							}
						});
						
						return error_set;
					}
					
					//console.log(step_form_ok);
					
					//If at least one element failed, it returns false. Otherwise, it returns true
					if(jQuery.inArray('failed', step_form_ok) !== -1){
						//It doesn't allow the user to move to the next step
						return false;
					}else{
						//It allows the user to move to the next step
						return true;
					}
					
					/*var elmErr = $(step_form + ":input").('.error');
					console.log(elmErr);
					if(elmErr && elmErr.length > 0){
						// Form validation failed
						return false;
					}*/
				}
				//return true;
			});
	
			$('.sw-btn-group-extra').hide();
			
            $("#smartwizard").on("showStep", function(e, anchorObject, stepNumber, stepDirection) {
				var step_form_int, step_form;
				//The stepNumber is 0 by default, but our div starts with 1
				step_form_int = parseInt(stepNumber);
				step_form_int = step_form_int + 1;
				step_form = '#step-' + step_form_int;
                // Enable finish button only on last step
                if(step_form_int == 5){
                    $('.sw-btn-group-extra').show();
                }else{
                    $('.sw-btn-group-extra').hide();
                }
            });

			$( ".datepicker" ).datepicker({
				"minDate":0
			});
				
			//Handles the Apply to business days link. It sets all business days with the monday value
			$('#applyAll').click(function(){
				$('select[name="tue_open"]').val($('select[name="mon_open"]').val());
				$('select[name="wed_open"]').val($('select[name="mon_open"]').val());
				$('select[name="thu_open"]').val($('select[name="mon_open"]').val());
				$('select[name="fri_open"]').val($('select[name="mon_open"]').val());
				$('select[name="tue_close"]').val($('select[name="mon_close"]').val());
				$('select[name="wed_close"]').val($('select[name="mon_close"]').val());
				$('select[name="thu_close"]').val($('select[name="mon_close"]').val());
				$('select[name="fri_close"]').val($('select[name="mon_close"]').val());
			});
		});
		
		/*$("#smartwizard").on("showStep", function(e, anchorObject, stepNumber, stepDirection) {
			if($('button.sw-btn-next').hasClass('disabled')){
				$('.sw-btn-group-extra').show();
			}else{
				$('.sw-btn-group-extra').hide();				
			}
		});*/
		
		var placeSearch, autocomplete;
		var componentForm = {
			street_number: 'short_name',
			route: 'long_name',
			locality: 'long_name',
			administrative_area_level_1: 'short_name',
			country: 'long_name',
			postal_code: 'short_name'
		};

		function initAutocomplete() {
			//console.log("init");
			// Create the autocomplete object, restricting the search to geographical
			// location types.
			autocomplete = new google.maps.places.Autocomplete(
				/** @type {!HTMLInputElement} */(document.getElementById('address1')),
				{types: ['geocode']});

			// When the user selects an address from the dropdown, populate the address
			// fields in the form.
			autocomplete.addListener('place_changed', fillInAddress);
		}

		function fillInAddress() {
			
			// Get the place details from the autocomplete object.
			var place = autocomplete.getPlace();
			//console.log(place);
			for (var component in componentForm) {
			  document.getElementById(component).value = '';
			  document.getElementById(component).disabled = false;
			}

			// Get each component of the address from the place details
			// and fill the corresponding field on the form.
			for (var i = 0; i < place.address_components.length; i++) {
			  var addressType = place.address_components[i].types[0];
			  if (componentForm[addressType]) {
				var val = place.address_components[i][componentForm[addressType]];
				document.getElementById(addressType).value = val;
			  }
			}
			document.getElementById('address1').value = 
			place.address_components[0]['long_name'] + ' ' +
			place.address_components[1]['long_name'];
		}
		$("#validateAddress").click(function(e){
			e.preventDefault();
			var street = $("#address1").val();
			var secondary = $("#address2").val();
			var city = $("#locality").val();
			var state = $("#administrative_area_level_1").val();
			var zipcode = $("#postal_code").val();
			$("#sugModal .modal-body #address1_entered").text(street);
			$("#sugModal .modal-body #address2_entered").text(secondary);
			$("#sugModal .modal-body #locality_entered").text(city);
			$("#sugModal .modal-body #administrative_area_level_1_entered").text(state);
			$("#sugModal .modal-body #postal_code_entered").text(zipcode);
			
			$.ajax({
                type: "POST",
                url: "validate_address.php",
                data: {"street":street,"secondary":secondary,"city":city,"state":state,"zipcode":zipcode},
                cache: false,
                success: function(result){
					//console.log(result)
					if(result=="true"){
						$(".checkAnimate").show().delay(2000).fadeOut();
					}else{
						$('#sugModal').modal('show');
						var obj = jQuery.parseJSON(result); 
						//console.log(obj[0]);
						$("#sugModal .modal-body #primary_number_sug").text(obj[0]["components"].primary_number);
						$("#sugModal .modal-body #street_name_sug").text(obj[0]["components"].street_name);
						$("#sugModal .modal-body #street_suffix_sug").text(obj[0]["components"].street_suffix);
						$("#sugModal .modal-body #secondary_designator_sug").text(obj[0]["components"].secondary_designator);
						$("#sugModal .modal-body #secondary_number_sug").text(obj[0]["components"].secondary_number);
						$("#sugModal .modal-body #city_name_sug").text(obj[0]["components"].city_name);
						$("#sugModal .modal-body #state_abbreviation_sug").text(obj[0]["components"].state_abbreviation);
						$("#sugModal .modal-body #zipcode_sug").text(obj[0]["components"].zipcode);
					}
                },
				error: function(xhr, status, error) {
				   var errorMessage = xhr.status + ': ' + xhr.statusText
         			//console.log('Error - ' + errorMessage);
				} 
            });
		});
		$("#useAddress").click(function(e){
			$("#address1").val($("#sugModal .modal-body #primary_number_sug").text()+" "+$("#sugModal .modal-body #street_name_sug").text()+" "+$("#sugModal .modal-body #street_suffix_sug").text());
			$("#address2").val($("#sugModal .modal-body #secondary_designator_sug").text()+" "+$("#sugModal .modal-body #secondary_number_sug").text());
			$("#locality").val($("#sugModal .modal-body #city_name_sug").text());
			$("#administrative_area_level_1").val($("#sugModal .modal-body #state_abbreviation_sug").text());
			$("#postal_code").val($("#sugModal .modal-body #zipcode_sug").text());
		
			$('#sugModal').modal('hide');
		});

		/*
		The user_email is automatically fill out with the primary location email value, 
		and the user_full_name with the primary contact first name and last name values
		*/
		$('#primary_loc_email, #fname1, #lname1').change(function() {
			var fieldValue, idAttributeValue;

			fieldValue = $(this).val();
			idAttributeValue = $(this).attr("id");
			
			switch (idAttributeValue) {
			  case 'primary_loc_email':
				$('#user_email').val(fieldValue);
				verify_user_email(fieldValue);
				break;
			  case 'fname1':
				fieldValue += ' ' + $('#lname1').val();
				$('#user_full_name').val(fieldValue);
				break;
			case 'lname1':
				fieldValue = $('#fname1').val() + ' ' + fieldValue;
				$('#user_full_name').val(fieldValue);
				break;
			  default:
				$('#user_email').val('');
				$('#user_full_name').val('');
			}
			
		});
			
		$("input[name=switch-generate-password]").click(function(){
			if($(this).is(":checked")){
				$(this).attr("value", "1");
			}
			else if($(this).is(":not(:checked)")){
				$(this).attr("value", "0");
			}
			
			var switch_generate_password =  $("input[name=switch-generate-password]").val();
			var url_action = location.protocol+'//'+location.hostname+'/admin/security-settings/xt_generate_random_password.php';
			var loading_image = location.protocol+'//'+location.hostname+'/img/loading.svg';
			
			$.ajax({
				type: "POST",
				url: url_action,
				data: {"switch_generate_password":switch_generate_password},
				cache: false,
				success: function(result){
					$("#user_password").val(result);
				},
				error: function(xhr, status, error) {
				  var err = eval("(" + xhr.responseText + ")");
				  console.log(err.Message);
				} 
			});

		});
		
		$("#storeid").on('change',function(e){
			var url_action = location.protocol+'//'+location.hostname+'/admin/location-details/xt_check_storeid.php';
			var storeid = $( '#storeid' ).val();
			
			$.ajax({
				type: "POST",
				url: url_action,
				data: {"storeid":storeid},
				cache: false,
				success: function(result){
					$("#storeid_msg_container").html(result);
					$("#storeid_msg_container").attr("required",true);
				},
				error: function(xhr, status, error) {
				  var err = eval("(" + xhr.responseText + ")");
				  console.log(err.Message);
				} 
			});
		});
		
		$("#user_email").on('change',function(e){
			var user_email = $( '#user_email' ).val();
			verify_user_email(user_email);
		});
		
		$("input[name=switch-send-password]").click(function(){
			if($(this).is(":checked")){
				$(this).attr("value", "1");
			}
			else if($(this).is(":not(:checked)")){
				$(this).attr("value", "0");
			}
		});
		
		function verify_user_email(user_email){
			var url_action = location.protocol+'//'+location.hostname+'/admin/location-details/xt_check_existing_user.php';
			
			$.ajax({
				type: "POST",
				url: url_action,
				data: {"user_email":user_email},
				cache: false,
				success: function(result){
					$( "#user_email_msg_container" ).html(result);
					$("#user_email_msg_container").attr("required",true);
					
					if(result != ''){
						$( "#user_email_msg_container" ).addClass( "alert alert-warning" );
						/*my_element_jq = $('.login_credentials');
						comment = document.createComment(my_element_jq.get(0).outerHTML);
						my_element_jq.replaceWith(comment);*/
						$('.login_credentials').hide();
						$("#user_password").attr("required",false);
						$("#user_full_name").attr("required",false);
					}else{
						$( "#user_email_msg_container" ).removeClass( "alert alert-warning" );
						$('.login_credentials').show();
						$("#user_password").attr("required",true);
						$("#user_full_name").attr("required",true);
					}
				},
				error: function(xhr, status, error) {
				  var err = eval("(" + xhr.responseText + ")");
				  console.log(err.Message);
				} 
			});
		}
	  </script>
  </body>
</html>