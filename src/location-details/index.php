<!doctype html>
<html lang="en">
  <head>
	<link rel="stylesheet" href="/css/checkbox.css">
    <?php 
	include ($_SERVER['DOCUMENT_ROOT']."/includes/head.php"); 
	?>

    <title>Location Details | <?php echo CLIENT_NAME; ?></title>
	<style>
		.main .field label{
			text-transform: uppercase;
			font-size: 12px;
			letter-spacing: 1px;
			background-color: #eee;
		}
		.main .field label.cm{
			background-color: #FFF;
			padding:4px 0;
		}
		.main .hours .field .hour{
			margin-bottom:5px;
		}
		.main .hours .field label.cm span{
			width:20px;
			height:20px;
		}
		.main .field input, .main .field .input{
			border: 1px solid #ddd;
			-moz-border-radius: 0px 5px 5px 5px;
			-webkit-border-radius: 0px 5px 5px 5px;
			border-radius: 0px 5px 5px 5px;
			padding:5px 10px;
		}
		.input ul{
			padding:0;
		}
		.input li{
			list-style:none;
		}
		.d-flex{
			display:flex;
		}
		.align-items-center{
			align-items:center;
		}
		.hours .day select{
			width:auto;
		}
		.title {
			font-size:16px;
			text-align:center;
			margin-bottom:6px;
		}
		.d-block{
			display:block;
		}
		.holidayAdd{
			cursor:pointer;
		}
		#holiday-container .hourRow, #language-container .form-inline, #brands-container .form-inline,
		#certifications-container .form-inline {
			margin-bottom:10px;
		}
		#holiday-container input, #language-container .form-inline input, .main .field input.rounded{
			-moz-border-radius: 5px !important;
			-webkit-border-radius: 5px !important;
			border-radius: 5px !important;
		}
		.splitHours{
			display:inline-block;
		}
		.main .field .input-group-addon{
			background-color:#eee;
			-moz-border-top-left-radius: 0 !important;
			-webkit-border-top-left-radius: 0 !important;
			border-top-left-radius: 0 !important;
			
		}
		#basic-url{
			-moz-border-bottom-left-radius: 0 !important;
			-webkit-border-bottom-left-radius: 0 !important;
			border-bottom-left-radius: 0 !important;
		}
		.img-bordered{
			border:1px solid #CCC;	
		}		
		.max-300{
			max-width:300px;
		}
		.max-200{
			max-width:200px;
			max-height:200px;
		}
		.p-1{
			padding:1rem;
		}
		.photos img{
			padding:5px;
		}
		.photos .title{
			text-align:left;
			margin-top:10px;
		}
		.photos input{
			width:auto;
			-moz-border-radius: 5px !important;
			-webkit-border-radius: 5px !important;
			border-radius: 5px !important;
		}
	
		input.save{
			margin: 0 auto 20px auto;
			display: inherit;
		}
		.none_upload{ display:none;text-align:center;}	
		.loader {
        	position: fixed;
        	left: 0px;
        	top: 0px;
        	width: 100%;
        	height: 100%;
        	z-index: 9999;
        	background: url('../yextAPI/spinner_preloader.gif') 50% 50% no-repeat rgba(255, 255, 255, 0.3);
        }  
		.w-90{
			width:90% !important;
		}
	
		#mapMarker{
			height:300px;
		}
		.btn-default.btn-on.active{background-color: #56ab4a;color: white;}
		.btn-default.btn-off.active{background-color: #DA4F49;color: white;}
		.gmnoprint span {
					font-size: 10px !important;			
		}
		/*Datatable*/
		table thead {
			background-color: #0067b1;
			color: #fff;
		}
		.dataTable thead {
			background-color: #ac1f2d;
			color: #fff;
		}
		/*Sticky message coming from yext.js */
		div.sticky {
		  position: sticky;
		  top: 1%;
		  left: 50%;
		  z-index: 9999;
		  width:25%;
		}
		
	</style> 
	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
  </head>
  <body class="bg-light cbp-spmenu-push">
	
  
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/top.php"); ?>

    <div class="container-fluid">
	
      <div class="row">
	  
        <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/nav.php"); ?>
		
		<?php 
		$locationList = $db->where("storeid",$_SESSION['storeid'])->getOne("locationlist");
		
		$locationList['twitter'] = ($locationList['twitter']) ? $locationList['twitter'] : 'yesco';
		$locationList['facebook']= ($locationList['facebook']) ? $locationList['facebook'] : 'facebook.com/yesco/';
		$locationList['instagram']= ($locationList['instagram']) ? $locationList['instagram'] : 'yesco';
		$locationList['linkedin']= ($locationList['linkedin']) ? $locationList['linkedin'] : 'https://www.linkedin.com/company/yesco';
		$locationList['pinterest']= ($locationList['pinterest']) ? $locationList['pinterest'] : 'https://www.pinterest.ca/yesco/';
		$locationList['youtube']= ($locationList['youtube']) ? $locationList['youtube'] : 'https://www.youtube.com/c/yesco';
		?>
        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 p-0 mb-4">
			<div class="sticky"></div>
			<div id="spinner_loading" class="none_upload loader"></div>
			
			<div class="p-0 border-bottom mb-4">
				<div class="d-flex d-block align-items-center clearfix py-2 px-4">
					<h1 class="h2 font-light mb-0 text-center text-sm-left"><i class="fas fa-map-marker-alt mr-2"></i> Location Details</h1>
				</div>
			</div>

        	<div class="px-4 py-3">
				<!-- <div class="alert alert-warning">
					Due to the high volume of business hour and listing data changes due to COVID-19, publishers have restricted access for automatic updates. Your request will be handled shortly but please expect delay of up to 1 day to process. 
				</div> -->
				<p class="text-muted font-italic">Disabled fields must be approved and updated by corporate.</p>
				<div class="row mb-4 dashboard">
					<div class="col-sm-8">
					<div class="box p-3">
						<form action="" method="post" id="frm_core_information">
							<h2 class="text-uppercase h4 text-dark d-flex flex-wrap mb-4">Core&nbsp;<span class="text-blue">Information</span></h2>						
							<div class="row">
								<div class="col-sm-4 form-group">
									<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Business Name</label>
									<input type="text" class="form-control rounded-bottom rounded-right" name="companyname" value="<?=$locationList['companyname']?>" readonly>
								</div>
								 <div class="col-sm-4 form-group">
									<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Display Name</label>
									<input type="text" class="form-control rounded-bottom rounded-right" name="displayname" value="<?=$locationList['displayname']?>" readonly>
								</div>
								<div class="col-sm-4 form-group">
									<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">SEO CITY <span data-toggle="tooltip" data-placement="top" title="Enter the name of the city around which your search engine optimization (SEO) efforts should be centered."><i class="far fa-question-circle"></i></span> </label>
									<input type="text" class="form-control rounded-bottom rounded-right" name="seo_city" value="<?php echo $locationList['seo_city']?>">
								</div>
								<!--<div class="col-sm-4 form-group">
									<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Store ID</label>
									<input type="text" class="form-control rounded-bottom rounded-right" name="storeid" value="<?//=$locationList['storeid']?>">
								</div>-->
								<div class="col-12 form-group">
									<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Categories <span data-toggle="tooltip" data-placement="top" title="These categories will be reflected in your local listings, social media profiles, etc. as your business’s areas of focus."><i class="far fa-question-circle"></i></span></label>
									<div class="border rounded-bottom rounded-right p-2">
										<ul class="mb-0 list-unstyled">
											<?
											if(!empty($locationList['yext_categories'])){
												$sql_yext_categories = "SELECT * FROM facebook_post.yext_categories WHERE id IN (".$locationList['yext_categories'].") ORDER BY FIELD (id, ".$locationList['yext_categories'].")";
												$yext_categories = $db->rawQuery($sql_yext_categories);
												
												if ($db->count>0){
													foreach($yext_categories as $category){
														echo '<li>'.$category['fullName'].'</li>';
													}
												}
											}else{
												echo '<li>There are not categories yet!</li>';
											}
											?>
										</ul>
										<!--<ul class="mb-0 list-unstyled">
											<li>Business Services &gt; Design &amp; Printing</li>
											<li>Business Services &gt; Design &amp; Printing &gt; Commercial Printing</li>
											<li>Business Services &gt; Design &amp; Printing &gt; Custom Banners</li>
											<li>Business Services &gt; Design &amp; Printing &gt; Decals &amp; Labels</li>
											<li>Business Services &gt; Design &amp; Printing &gt; Flags</li>
											<li>Business Services &gt; Design &amp; Printing &gt; Graphic Designers</li>
											<li>Business Services &gt; Design &amp; Printing &gt; Logo Designers</li>
											<li>Business Services &gt; Design &amp; Printing &gt; Sign Printing</li>
											<li>Business Services &gt; Design &amp; Printing &gt; Trade Show Displays</li>
										</ul>-->
									</div>
								</div>
								<div class="col-sm-6 form-group">
									<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Featured Message <span data-toggle="tooltip" data-placement="top" title="This message should be an appropriate call to action (CTA) that will be used in your local listings (i.e., Get a Quote)."><i class="far fa-question-circle"></i></span></label>
									<input type="text" class="form-control rounded-bottom rounded-right" name="yext_featured_message" value="<?php echo $locationList['yext_featured_message']?>">
								</div>
								<div class="col-sm-6 form-group">
									<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Featured Message URL <span data-toggle="tooltip" data-placement="top" title="This is the URL that your “Featured Message” will direct users to. When users click on the Featured Message (Get a Quote, for example), they will be directed to your local website’s homepage and we can track where this traffic comes from."><i class="far fa-question-circle"></i></span></label>
									<input type="text" class="form-control rounded-bottom rounded-right" name="yext_featured_message_url" value="<?php echo $locationList['yext_featured_message_url']?>" readonly>
								</div>
								<!--<div class="col-sm-6 form-group">
									<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Rep</label>
									<div class="select-wrapper">
										<select name="rep" class="form-control bg-white border rounded">
											<option>Test 1</option>
											<option>Test 2</option>
										</select>
									</div>
								</div>-->
								<div class="col-12 text-center">
									<input type="submit" class="btn save bg-dark-blue text-white btn-sm px-4" value="Save" id="core_information" />
								</div>
							</div>
						</form>
					</div>
					</div>
					<div class="col-sm-4">
					<div class="box p-3">
						<form action="" method="post" id="frm_location_information">
							<h2 class="text-uppercase h4 text-dark d-flex flex-wrap mb-4">Location&nbsp;<span class="text-blue">Information</span></h2>						
							<div class="row">
								<input type="hidden" name="change_address" value="0"/>
								<div class="col-sm-12">
									<div class="field form-group">
										<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Address </label>
										<input type="text" class="form-control rounded-bottom rounded-right address" name="address" value="<?php echo $locationList['address']?>" readonly>
									</div>
								</div>
								<div class="col-sm-6">
									<div class="field form-group">
										<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Address 2 </label>
										<input type="text" class="form-control rounded-bottom rounded-right address" name="address2" value="<?php echo $locationList['address2']?>" readonly>
									</div>
								</div>
								<div class="col-sm-6">
									<div class="field form-group">
										<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">City </label>
										<input type="text" class="form-control rounded-bottom rounded-right address" name="city" value="<?php echo $locationList['city']?>" readonly>
									</div>
								</div>
								<div class="col-sm-6">
									<div class="field form-group">
										<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">State </label>
										<input type="text" class="form-control rounded-bottom rounded-right address" name="state" value="<?php echo $locationList['state']?>" readonly>
									</div>
								</div>
								<div class="col-sm-6">
									<div class="field form-group">
										<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Zip </label>
										<input type="text" class="form-control rounded-bottom rounded-right address" name="zip" value="<?php echo $locationList['zip']?>" readonly>
									</div>
								</div>
								<!--<div class="col-12">
									<div class="field form-group clearfix">
										<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Map Marker</label>
										<div class="input">
											<div id="mapMarker"></div>
											<input type="hidden" id="lat" name="lat" value="<?php //echo $locationList['latitude']?>">
											<input type="hidden" id="lng" name="lng" value="<?php //echo $locationList['longitude']?>">
										</div>
									</div>
								</div>-->
								<div class="col-12 text-center">
									<input type="submit" class="btn save bg-dark-blue text-white btn-sm px-4" value="Save" id="location_information" />
								</div>
							</div>
						</form>
					</div>
					</div>
				</div>

				<div class="row mb-4">
					<div class="col-12">
						<div class="box p-3">
						<form action="" method="post"  id="frm_business_details">
							<h2 class="text-uppercase h4 text-dark d-flex flex-wrap mb-4">Business&nbsp;<span class="text-blue">Details</span></h2>
							<div class="field form-group">
								<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Business Description <span data-toggle="tooltip" data-placement="top" title="Enter an accurate business description in no more than 750 characters that includes your business’s name and the products/services your offer, as well as the mission and history of your business. This will appear in your local listings."><i class="far fa-question-circle"></i></span></label>
								<textarea class="form-control custom-control" name="business_desc" rows="3"><?=$locationList['business_desc']?></textarea>     
							</div>
							<!-- Business Hours -->
							<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Hours of Operation </label>
							<div class="mb-3 row no-gutters d-xl-flex justify-content-between business_hours">
								<input type="hidden" name="change_business_hours" value="0"/>
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
													if($tNow == strtotime($locationList[$abbr.'_open']))
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
												<?
												$start = "5:00";
												$end = "20:00";

												$tStart = strtotime($start);
												$tEnd = strtotime($end);
												$tNow = $tStart;

												while($tNow <= $tEnd){
													if($tNow == strtotime($locationList[$abbr.'_close']))
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
										<input  class="label__checkbox" type="checkbox" name="<?php echo $abbr?>_opt" value="C" type="checkbox" id="<?php echo $abbr?>_opt_c" <?php if($locationList[$abbr.'_opt'] == "C") echo "checked"; ?> />
										<span class="label__text d-flex align-items-center">
										  <span class="label__check d-flex rounded-circle mr-2">
											<i class="fa fa-check icon small"></i>
										  </span>
											<span class="text-uppercase small letter-spacing-1 d-inline-block">Closed</span>
										</span>
										  <!--<input type="hidden" name="<?php //echo $abbr?>_opt" value="<?php //echo $locationList[$abbr.'_opt']; ?>">-->
									  </label>
									</div>
									  <label class="label cusor-pointer text-center d-flex" for="<?php echo $abbr?>_opt_a">
										<input  class="label__checkbox" type="checkbox" name="<?php echo $abbr?>_opt" value="A" type="checkbox" id="<?php echo $abbr?>_opt_a" <?php if($locationList[$abbr.'_opt'] == "A") echo "checked"; ?> />
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
							<!-- /Business Hours -->
							
							<!-- Holiday Hours -->
							<div class="field form-group ">
								<input type="hidden" name="change_holiday_hours" value="0"/>
								<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Holiday Hours </label>
								<div class="border rounded-bottom rounded-right p-2">
									<div class="input clearfix">
										<div id="holiday-container">
										<?php													
											if(($locationList['yext_holiday_hours'] != "") || ($locationList['yext_holiday_hours'] != "{}")){

												$holiday_hours = transformYextHolidayHours(json_decode($locationList['yext_holiday_hours'], TRUE));

												foreach($holiday_hours as $item){
												?>
												<div class="form-inline hourRow holiday_hours">														
													<i class="far fa-trash-alt removeHourRow cursor-pointer mr-2 text-danger" aria-hidden="true"></i>
													<input type="text" class="form-control datepicker" name="holiday-date[]" value="<?=$item['date'];?>">                                                        
													<select name="hours-type[]" class="mx-2 form-control rounded custom-select-arrow">
														<option value="OPEN" <?=($item['type'] == 'OPEN' )?'selected': '';?> > Open </option>
														<option value="SPLIT" <?=($item['type'] == 'SPLIT' )?'selected': '';?> > Split </option>
														<option value="ALWAYS_OPEN" <?=($item['type'] == 'ALWAYS_OPEN' )?'selected': '';?>> 24 hour </option>
														<option value="CLOSED" <?=($item['type'] == 'CLOSED' )?'selected': '';?> > Closed </option>
														<option value="IS_REGULAR_HOURS"<?=($item['type'] == 'IS_REGULAR_HOURS' )?'selected': '';?> > Regular Hours</option>
													</select>
													<input type="text" class="form-control time hol-am" name="holStart[]"  value="<?=$item['holStart'];?>" <?=!array_key_exists('holStart',$item)?'disabled':''?>>  <span class="mx-1 small">to </span>
													<input type="text" class="form-control time hol-pm" name="holEnd[]"  value="<?=$item['holEnd'];?>" <?=!array_key_exists('holEnd',$item)?'disabled':''?> >
													<div class="splitHours">
													<?php if(array_key_exists('holStart_split', $item) && array_key_exists('holEnd_split', $item)){?>
														and <input type="text" class="form-control time hol-pm" name="holStart_split[]" value="<?=$item['holStart_split'];?>"> 
														to <input type="text" class="form-control time hol-pm" name="holEnd_split[]"  value="<?=$item['holEnd_split'];?>">
													<?php } ?>
													</div>
												</div>															
										<?php	}
											}
										?>
										</div>
										<small class="cursor-pointer holidayAdd" id="addAnotherHoliday">+ Add Another</small>
									</div>
								</div>
							</div>
							<!-- /Holiday Hours -->
							
							<div class="field form-group">
								<input type="hidden" name="change_payment_methods" value="0"/>
								<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Payment Options <span data-toggle="tooltip" data-placement="top" title="Select all of the payment methods you accept. This will appear in your local listings."><i class="far fa-question-circle"></i></span></label>
								<div class="border rounded-bottom rounded-right p-2">
									<?php
									$pOptions = Array("Discover"=>"discover",
															 "Visa"=>"visa",
															 "Invoice"=>"invoice",
															 "Mastercard"=>"mastercard",
															 "Traveler's Check"=>"travelers_check",
															 "Check"=>"check",
															 "Apple Pay"=>"applepay",
															 "Cash"=>"cash",
															 "Google Pay"=>"andropay",
															 "Diners Club"=>"diners_club",
															 "Financing"=>"financing",
															 "American Express"=>"amex",
															 "PayPal"=>"paypal",
															 "Bitcoin"=>"bitcoin",
															 "Samsung Pay"=>"samsungpay");
									foreach($pOptions as $key => $val){
									?>
									<label class="label cusor-pointer text-center d-flex" for="<?php echo 'pay_'.$val; ?>">
										<input  class="label__checkbox" type="checkbox" name="payments[]" value="<?php echo $val; ?>" type="checkbox" id="<?php echo 'pay_'.$val; ?>" <? if (strpos($locationList['paymentmethod'], $val) !== false) echo 'checked'; ?> />
										<span class="label__text d-flex align-items-center">
										  <span class="label__check d-flex rounded-circle mr-2">
											<i class="fa fa-check icon small"></i>
										  </span>
											<span class="font-weight-light font-lg letter-spacing-1 d-inline-block cursor-pointer"><?php echo $key; ?></span>
										</span>
									 </label>
									<?php } ?>
								</div>
							</div>
							
							<div class="field form-group">
								<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Additional Hours Text <span data-toggle="tooltip" data-placement="top" title="Add any other information as it is related to your hours of operation. This will appear in your local listings."><i class="far fa-question-circle"></i></span></label>
								<textarea class="form-control rounded-bottom rounded-right custom-control" maxlength="255" name="additional_hours_text" rows="3"><?=$locationList['additional_text_hours']?></textarea>     
								<small><b>Note: </b>Only 255 characters are allowed.</small>
							</div>
							<div class="text-center">
								<input type="submit" class="btn save bg-dark-blue text-white btn-sm px-4" value="Save" id="business_details" />
							</div>
						</form>
					</div>
					</div>
				</div>

			<div class="box p-3 mb-4">
			<form action="" method="post" id="frm_contact_information">
				<input type="hidden" name="change_contact_information" value="0"/>
				<h2 class="text-uppercase h4 text-dark mb-4">Contact&nbsp;<span class="text-blue">Information</span></h2>	
				<div class="row">
					<div class="col-md-6">
						<h3 class="h5 text-muted mb-4">Primary Contact</h3>
						<div class="form-group">
							<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">First Name </label>
							<input type="text" class="form-control rounded-bottom rounded-right contact_info" name="fname1" value="<?php echo $locationList['fname1']?>">
						</div>
						<div class="form-group">
							<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Last Name </label>
							<input type="text" class="form-control rounded-bottom rounded-right contact_info" name="lname1" value="<?php echo $locationList['lname1']?>">
						</div>
						<div class="form-group">
							<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Phone </label>
							<input type="text" class="form-control rounded-bottom rounded-right contact_info phone" name="phone1" value="<?php echo $locationList['phone1']?>">
						</div>
						<div class="form-group">
							<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Email Address </label>
							<input type="text" class="form-control rounded-bottom rounded-right contact_info" name="reportemail" value="<?php echo $locationList['reportemail']?>">
						</div>
					</div>
					<div class="col-md-6">
						<h3 class="h5 text-muted mb-4">Secondary Contact</h3>
						<div class="form-group">
							<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">First Name </label>
							<input type="text" class="form-control rounded-bottom rounded-right contact_info" name="fname2" value="<?php echo $locationList['fname2']?>">
						</div>
						<div class="form-group">
							<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Last Name </label>
							<input type="text" class="form-control rounded-bottom rounded-right contact_info" name="lname2" value="<?php echo $locationList['lname2']?>">
						</div>
						<div class="form-group">
							<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Phone </label>
							<input type="text" class="form-control rounded-bottom rounded-right contact_info phone" name="phone2" value="<?php echo $locationList['phone2']?>">
						</div>
						<div class="form-group">
							<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Email Address </label>
							<input type="text" class="form-control rounded-bottom rounded-right contact_info" name="altreportemail" value="<?php echo $locationList['altreportemail']?>">
						</div>
					</div>
				</div>
				<div class="text-center">
					<input type="submit" class="btn save bg-dark-blue text-white btn-sm px-4" value="Save" id="contact_information" />
				</div>
			</form>
			</div>

			<div class="card-deck mb-4 dashboard">
				<div class="card rounded-0 border-0 box p-3">
					<form action="" method="post" id="frm_website_information">
					<input type="hidden" name="change_website_information" value="0"/>
					<h2 class="text-uppercase h4 text-dark mb-4">Website&nbsp;<span class="text-blue">Information</span></h2>	
					<div class="card-body p-0">
						<div class="field form-group">
							<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Primary Location Email </label>
							<input type="text" class="form-control web_info send_to" name="email" id="email" value="<?php echo $locationList['email']?>" data-original_val="" data-allowed_emails_qty="5" data-allowed_delimiter="," required>
							<small><b>Note: </b>Separate Emails By Comma.</small>
						</div>
						<div class="field form-group">
							<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Website </label>
							<div class="input-group mb-3">
							  <div class="input-group-prepend">
								<span class="input-group-text rounded-left" id="basic-addon3"><?php echo CLIENT_URL.'locations/'; ?></span> 
							  </div>
							  <input type="text" class="form-control rounded-bottom rounded-right web_info" name="url" id="basic-url" aria-describedby="basic-addon3" value="<?php echo $locationList['url']?>" readonly>
							</div>
						</div>
						<div class="field form-group">
							<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Alternative Website </label>
							<input type="url" class="form-control web_info" name="alternative_website_url" value="<?php echo $locationList['alternative_website_url']?>" required readonly>
						</div>
						<div class="field form-group">
							<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Business Phone </label>
							<input type="text" class="form-control rounded-bottom rounded-right phoneInput web_info phone" name="phone" value="<?php echo $locationList['phone']?>" readonly>
						</div>
						<!--<div class="form-group">
							<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Google UA</label>
							<input type="text" class="form-control rounded-bottom rounded-right web_info" name="google_ua" value="<?php //echo $locationList['google_ua']?>">
						</div>
						<div class="form-group">
							<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">View ID</label>
							<input type="text" class="form-control rounded-bottom rounded-right web_info" name="ga_viewid" value="<?php //echo $locationList['ga_viewid']?>">
						</div>
						<div class="form-group">
							<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Refresh Token</label>
							<input type="text" class="form-control rounded-bottom rounded-right web_info" name="refresh_token" value="<?php //echo $locationList['refresh_token']?>">
						</div>-->
						<div class="form-group">
							<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Send Contact Form To </label>
							<input type="text" class="form-control rounded-bottom rounded-right web_info" name="email_quote" value="<?php echo $locationList['email_quote']?>">
						</div>
					</div>
					<div class="text-center card-footer border-0 bg-white">
						<input type="submit" class="btn save bg-dark-blue text-white btn-sm px-4" value="Save" id="website_information" />
					</div>
					</form>
				</div>
				<div class="card rounded-0 border-0 box p-3">
					<form action="" method="POST" id="frm_social_media">
					<input type="hidden" name="change_social_media" value="0"/>
					<h2 class="text-uppercase h4 text-dark mb-4">Social&nbsp;<span class="text-blue">media</span></h2>
					<div class="card-body p-0">
						<div class="field form-group">
							<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Twitter <span data-toggle="tooltip" data-placement="top" title="Enter valid Twitter handle for the location without the leading “@”."><i class="far fa-question-circle"></i></span></label>
							<div class="input-group mb-1">
							  <div class="input-group-prepend">
								<span class="input-group-text rounded-left" id="basic-addon3"><?php  echo '@'; ?></span> 
							  </div>
							  <input type="text" class="form-control rounded-bottom rounded-right social_med handle" name="twitter" aria-describedby="basic-addon3" value="<?php echo $locationList['twitter']?>" data-original_val="">
							</div>
						</div>
						<div class="field form-group">
							<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Facebook Page URL <span data-toggle="tooltip" data-placement="top" title="Enter valid Facebook URL for the location (i.e., facebook.com/profile.php?id=[numId]OR facebook.com/group.php?gid=[numId] OR facebook.com/groups/[numId] OR facebook.com/[Name] OR facebook.com/pages/[Name]/[numId])."><i class="far fa-question-circle"></i></span></label>
							<?php $facebook  = str_replace("https://www.","",$locationList['facebook']); ?>
							<input type="text" class="form-control rounded-bottom rounded-right social_med handle" name="facebook" value="<?php echo $facebook?>" data-original_val="">
							<small></small>
						</div>
						<div class="field form-group">
							<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Instagram <span data-toggle="tooltip" data-placement="top" title="Enter valid Instagram username for the location without the leading “@”."><i class="far fa-question-circle"></i></span></label>
							<div class="input-group mb-1">
							  <div class="input-group-prepend">
								<span class="input-group-text rounded-left" id="basic-addon3"><?php  echo '@'; ?></span> 
							  </div>
							  <input type="text" class="form-control rounded-bottom rounded-right social_med handle" name="instagram" aria-describedby="basic-addon3" value="<?php echo $locationList['instagram']?>" data-original_val="">
							</div>
						</div>
						<div class="field form-group">
							<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">LinkedIn Page URL <span data-toggle="tooltip" data-placement="top" title="Enter valid LinkedIn page URL for the location"><i class="far fa-question-circle"></i></span></label>
							<input type="text" class="form-control rounded-bottom rounded-right social_med" name="linkedin" value="<?php echo $locationList['linkedin']?>">
						</div>
						<div class="field form-group">
							<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Pinterest Page URL <span data-toggle="tooltip" data-placement="top" title="Enter valid Pinterest page URL for the location. If your location does not have Pinterest, the link for the corporate account will be used."><i class="far fa-question-circle"></i></span></label>
							<input type="text" class="form-control rounded-bottom rounded-right social_med" name="pinterest" value="<?php echo $locationList['pinterest']?>">
						</div>
						<div class="field form-group">
							<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">YouTube Page URL <span data-toggle="tooltip" data-placement="top" title="Enter valid YouTube page URL for the location. If your location does not have YouTube, the link for the corporate channel will be used."><i class="far fa-question-circle"></i></span></label>
							<input type="text" class="form-control rounded-bottom rounded-right social_med" name="youtube" value="<?php echo $locationList['youtube']?>">
						</div>
						<div class="field form-group">
							<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Yelp Page URL <span data-toggle="tooltip" data-placement="top" title="Enter valid Yelp page URL for the location."><i class="far fa-question-circle"></i></span></label>
							<input type="text" class="form-control rounded-bottom rounded-right social_med" name="yelp" value="<?php echo $locationList['yelp']?>">
						</div>
						<?php if(roleHasPermission('show_email_review_field', $_SESSION['role_permissions'])){ ?>
						<div class="field form-group">
							<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Send Email Review To <span data-toggle="tooltip" data-placement="top" title="Enter the email address(es) that notifications about online review of your business should be sent to."><i class="far fa-question-circle"></i></span></label>
							<?php $email_review = (isset($locationList['email_review']) && $locationList['email_review'] != '') ? $locationList['email_review'] : $locationList['email'];  ?>
							<input type="text" class="form-control rounded-bottom rounded-right social_med send_to" name="email_review" id="email_review" value="<?php echo $email_review ?>" data-original_val="" data-allowed_emails_qty="5" data-allowed_delimiter=",">
							<small><b>Note: </b>Separate Emails By Comma.</small>
						</div>
						<?php } ?>
					</div>
					<div class="text-center card-footer border-0 bg-white">
						<input type="submit" class="btn save bg-dark-blue text-white btn-sm px-4" value="Save" id="social_media" />
					</div>
					</form>					
				</div>
			</div>

			<div class="row mb-4">
				<div class="col-12">
					<div class="box p-3">
						<h2 class="text-uppercase h4 text-dark mb-4">Media&nbsp;<span class="text-blue">Assets</span></h2>	
						<div class="field form-group">
							<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Business Logo</label>
							<div class="border rounded-bottom rounded-right p-2">
								<?php if($locationList['yext_logo']){  ?>
									<img src="<?=$locationList['yext_logo']?>" alt="Logo" class="img-thumbnail" />
								<?php } ?>
							</div>
						</div>
						<div class="field form-group">
							<form action="../yextAPI/upload.php" method="POST" enctype="multipart/form-data" id="frm_photos_videos">
								<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Photo Gallery <span data-toggle="tooltip" data-placement="top" title="These photos will be displayed as part of your local listings. They are intended to be representative of your business and its products/services."><i class="far fa-question-circle"></i></span></label>
								<div class="border rounded-bottom rounded-right p-2">
									<input type="hidden" name="image_form_submit" value="0"/>
									<input type="hidden" name="delete_yext_images" value="0"/>
									<div id="gallery" >
									<?php if(!empty($locationList['yext_photos'])){
										$photo = json_decode($locationList['yext_photos']);
										foreach($photo as $p){?>
											<div class="img-thumbnail position-relative d-inline-block mb-2">
												<div class="p-1 bg-white position-absolute top-right">
													<i class="far fa-trash-alt removephoto cursor-pointer" title="Delete" aria-hidden="true"></i>
												</div>
												<img src="<?php echo $p?>" class="max-200" />								
											</div>
										<?php } 
										}?>
									</div>
									<div class="uploading none_upload">
										<label>&nbsp;</label>
										<img src="../yextAPI/uploading.gif"/>
									</div>
									<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Add Photos <span data-toggle="tooltip" data-placement="top" title="Click on “Browse” to add additional photos to the Gallery above."><i class="far fa-question-circle"></i></span></label>
									<div class="mb-3">
										<div class="input-group ">
										  <div class="custom-file">
											<input type="file" name="photos[]" class="custom-file-input" id="inputGroupFile01" aria-describedby="inputGroupFileAddon01" id="myFile" onchange="validateFiles(this.id,'imgMsgContainer','image','photos_videos',20,2000000)" accept="image/jpg, image/png, image/jpeg, image/gif" multiple>
											<label class="custom-file-label" for="inputGroupFile01">Choose file</label>
										  </div>
										</div>
										<small class="d-block" id="imgMsgContainer">Only image files are accepted.</small>
									</div>
								</div>
							</form>
							
						</div>
					<div class="text-center card-footer border-0 bg-white">
						<input type="submit" class="btn save bg-dark-blue text-white btn-sm px-4" value="Save" id="photos_videos" />
					</div>
					</div>
				</div>
			</div>

			<div class="row mb-4">
				<div class="col-12">
					<div class="box p-3">
						<form action="" method="POST" id="frm_additional_attributes">
							<h2 class="text-uppercase h4 text-dark d-flex flex-wrap">Additional&nbsp;<span class="text-blue">Attributes</span></h2>	
							<div class="field form-group">
								<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Year Established</label>
								<input type="text" class="form-control rounded-bottom rounded-right" name="yearestablished" value="<?php echo $locationList['yearestablished']?>" readonly>
							</div>
							<div class="field form-group">
								<?php $products = $locationList['yext_products']!= ""?explode(';' , $locationList['yext_products']):[]; ?>
								<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Products</label>
								<div id="prod-container" class="border rounded-bottom rounded-right p-2">
									<div class="row">
										<div class="form-inline col-sm-3 mb-2">
											<!--<i class="far fa-trash-alt removeProdRow cursor-pointer mr-2" aria-hidden="true"></i>-->
											<input type="text"  class ="form-control rounded apiLimit" name="products[]" value="<?=(count($products)>0)?trim(array_shift($products)):'';?>" readonly />   
										</div>
										<?php for($i=0;$i<count($products);$i++){
												$prod = preg_replace('/\s+/', ' ', $products[$i]);																
											if($prod == "" || $prod == " ")
												continue;
											?>
										<div class="form-inline col-sm-3 mb-2">
											<!--<i class="far fa-trash-alt removeProdRow cursor-pointer mr-2" aria-hidden="true"></i>-->
											<input type="text" class="form-control rounded apiLimit" name="products[]" value="<?=trim($prod);?>" readonly />   
										</div>
										<?php } ?>
									</div>
								</div>
								<!--<small style="cursor:pointer;" class="cursor-pointer prodAdd" data-input_name="products[]">+ Add Another</small>-->
							</div>
							<div class="field form-group">
								<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Services</label>
								<?php $services = $locationList['yext_services']!= ""?explode(';' , $locationList['yext_services']):[]; ?>
								<div id="serv-container" class="border rounded-bottom rounded-right p-2">
									<div class="row">
										<div class="form-inline col-sm-3 mb-2">
											<!--<i class="far fa-trash-alt removeServRow cursor-pointer mr-2" aria-hidden="true"></i>-->
											<input type="text"  class ="form-control rounded apiLimit" name="services[]" value="<?=(count($services)>0)?trim(array_shift($services)):'';?>" readonly />   
										</div>
										<?php for($i=0;$i<count($services);$i++){
											$serv = preg_replace('/\s+/', ' ', $services[$i]);																
											if($serv == "" || $serv == " ")
												continue;
											?>
										<div class="form-inline col-sm-3 mb-2">
											<!--<i class="far fa-trash-alt removeServRow cursor-pointer mr-2" aria-hidden="true"></i>-->
											<input type="text" class="form-control rounded apiLimit" name="services[]" value="<?=trim($serv);?>" readonly />   
										</div>
										<?php } ?>
									</div>
								</div>
								<!--<small style="cursor:pointer;" class="cusor-pointer servAdd" data-input_name="services[]">+ Add Another</small>-->
							</div>
							<div class="field form-group">
								<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Specialties </label>
								<?php $specialties = $locationList['yext_specialties']!= ""?explode(';' , $locationList['yext_specialties']):[]; ?>
								<div id="specialties-container" class="border rounded-bottom rounded-right p-2">
									<div class="row">
										<div class="form-inline col-sm-3 mb-2">
											<!--<i class="far fa-trash-alt removeSpecRow mr-2" aria-hidden="true"></i>--> 
											<input type="text"  class ="form-control rounded w-90 apiLimit" name="specialties[]" value="<?=(count($specialties)>0)?array_shift($specialties):'';?>" readonly />   
										</div>
										<?php for($i=0;$i<count($specialties);$i++){
											$spec = preg_replace('/\s+/', ' ', $specialties[$i]);																
											if($spec == "" || $spec == " ")
												continue;
											?>
										<div class="form-inline col-sm-3 mb-2">
											<!--<i class="far fa-trash-alt removeSpecRow cursor-pointer mr-2" aria-hidden="true"></i> -->
											<input type="text" class="form-control rounded w-90 apiLimit" name="specialties[]" value="<?=$spec;?>" readonly />   
										</div>
										<?php } ?>
									</div>
								</div>
								<small style="cursor:pointer;" class="cusor-pointer specAdd" data-input_name="specialties[]">+ Add Another</small>
							</div>
							<div class="field form-group">
								<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Certifications &amp; Affiliations </label>
								<?php $certifications = $locationList['certifications']!= ""?explode(';' , $locationList['certifications']):[]; ?>
								<div id="certifications-container" class="border rounded-bottom rounded-right p-2">
									<div class="row">
										<div class="form-inline col-sm-3">
											<i class="far fa-trash-alt removeCertRow cursor-pointer mr-2" aria-hidden="true"></i> 
											<input type="text"  class ="form-control rounded apiLimit" name="certifications[]" value="<?=count($certifications)?array_shift($certifications):'';?>"/>   
										</div>
										<?php 	
										for($i=0;$i<count($certifications);$i++){
											$cert = preg_replace('/\s+/', ' ', $certifications[$i]);																
											if($cert == "" || $cert == " ")
												continue;

											?>
										<div class="form-inline col-sm-3">
											<i class="far fa-trash-alt removeCertRow cursor-pointer mr-2" aria-hidden="true"></i> 
											<input type="text" class="form-control rounded apiLimit" name="certifications[]" value="<?=$cert;?>"/>   
										</div>
										<?php } ?>
									</div>
								</div>
								<small style="cursor:pointer;" class="cusor-pointer certsAdd" data-input_name="certifications[]">+ Add Another</small>
							</div>
							<div class="field form-group">
								<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Brands</label>
								<?php $brands = $locationList['brands']!= ""?explode(';' , $locationList['brands']):[]; ?>
								<div id="brands-container" class="border rounded-bottom rounded-right p-2">
									<div class="row">
										<div class="form-inline col-sm-3">
											<!--<i class="far fa-trash-alt removeBrandRow cursor-pointer mr-2" aria-hidden="true"></i>-->
											<input type="text" class="form-control rounded w-90 apiLimit"  name="brands[]" value="<?=(count($brands)>0)?array_shift($brands):'';?>" readonly />   
										</div>
										<?php for($i=0;$i<count($brands);$i++){
											$brand = preg_replace('/\s+/', ' ', $brands[$i]);																
											if($brand == "" || $brand == " ")
												continue;
											?>
											<div class="form-inline col-sm-4">
												<!--<i class="far fa-trash-alt removeBrandRow cursor-pointer mr-2" aria-hidden="true"></i>-->
												<input type="text" class="form-control rounded w-90 apiLimit" name="brands[]" value="<?=$brand;?>" readonly />   
											</div>
										<?php } ?>
									</div>
								</div>
								<!--<small style="cursor:pointer;" class="cusor-pointer brandsAdd" data-input_name="brands[]">+ Add Another</small>-->
							</div>
							<div class="field form-group">
								<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Languages <span data-toggle="tooltip" data-placement="top" title="Enter any languages spoken by your location’s team members in addition to English. This will appear in your local listings."><i class="far fa-question-circle"></i></span></label>
								<?php $languages = $locationList['languages']!= ""?explode(';' , $locationList['languages']):[]; ?>
								<div id="language-container" class="border rounded-bottom rounded-right p-2">
									<div class="row">
										<div class="form-inline col-sm-3">
											<i class="far fa-trash-alt removeLanguageRow cursor-pointer mr-2" aria-hidden="true"></i> 
											<input type="text" class="form-control rounded apiLimit" name="languages[]" value="<?=(count($languages)>0)?array_shift($languages):'';?>">   
										</div>
										<?php for($i=0;$i<count($languages);$i++){
											$lang = preg_replace('/\s+/', ' ', $languages[$i]);																
											if($lang == "" || $lang == " ")
												continue;
											?>
											<div class="form-inline col-sm-3">
												<i class="far fa-trash-alt removeLanguageRow  cursor-pointer mr-2" aria-hidden="true"></i> 
												<input type="text" class="form-control rounded apiLimit" name="languages[]" value="<?=$lang;?>">   
											</div>
										<?php } ?>
									</div>
								</div>
								<small class="cusor-pointer languageAdd" data-input_name="languages[]">+ Add Another</small>
							</div>
							<div class="field form-group">
								<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Tagline</label>
								<input type="text" class="form-control rounded-bottom rounded-right" name="tagline" value="<?php echo $locationList['tagline']?>" readonly />
							</div>
							<div class="text-center">
								<input type="submit" class="btn save bg-dark-blue text-white btn-sm px-4" value="Save" id="additional_attributes" />
							</div>
						</form>
					</div>
				</div>
			</div>

			</div>
        </main>
      </div>
    </div>


    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

	<script>
	/* begin datepicker */
	$( function() {
		var dateFormat = "yy-mm-dd",
		  from = $( "#from" )
			.datepicker({
			  defaultDate: "+1w",
			  changeMonth: true,
			  dateFormat: "yy-mm-dd",
			  numberOfMonths: 1
			})
			.on( "change", function() {
			  to.datepicker( "option", "minDate", getDate( this ) );
			}),
		  to = $( "#to" ).datepicker({
			defaultDate: "+1w",
			changeMonth: true,
			dateFormat: "yy-mm-dd",
			numberOfMonths: 1
		  })
		  .on( "change", function() {
			from.datepicker( "option", "maxDate", getDate( this ) );
		  });
	 
		function getDate( element ) {
		  var date;
		  try {
			date = $.datepicker.parseDate( dateFormat, element.value );
		  } catch( error ) {
			date = null;
		  }
	 
		  return date;
		}
	  } );
	/* end datepicker */
	</script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.2/jquery.form.min.js" integrity="sha384-FzT3vTVGXqf7wRfy8k4BiyzvbNfeYjK+frTVqZeNDFl8woCbF0CYG6g2fMEFFo/i" crossorigin="anonymous"></script>
	<script type="text/javascript" src="../yextAPI/yext.js"></script>
	<script type="text/javascript" src="../js/functions.js"></script>
	<script type="text/javascript" src="/js/maskedinput.min.js"></script>
	<!--<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA6OmvG-XyCVw7MyCUOW6qNABkc21kslmA&callback=initMap"></script>-->
    <script>	
	var forms_action = location.protocol+'//'+location.hostname+'/yextAPI/localsites2yext.php';
    	$(document).ready(function(){
			$("input.phone").mask("999-999-9999"); 
			
			// Cache the highest
			var highestBox = 0;
			  
			// Select and loop the elements you want to equalise
			$('.hours .day', this).each(function(){
				// If this box is higher than the cached highest then store it
				if($(this).height() > highestBox) {
					highestBox = $(this).height(); 
				}
			});  
					
			// Set the height of all those children to whichever was highest 
			$('.hours .day',this).height(highestBox);
			
			/*
			//It sets the color of the payment options images like localSAR
			if($('#pay_discover').is(':checked'))
				$('#pay_discover').siblings().children('i').css('color','#e94d00');
			
			if($('#pay_visa').is(':checked'))
				$('#pay_visa').siblings().children('i').css('color','#1a1e5a');
			
			if($('#pay_invoice').is(':checked'))
				$('#pay_invoice').siblings().children('i').css('color','#cc0000');
			
			if($('#pay_mastercard').is(':checked'))
				$('#pay_mastercard').siblings().children('i').css('color','#cc0000');
			
			if($('#pay_travelers_check').is(':checked'))
				$('#pay_travelers_check').siblings().children('i').css('color','#008543');
			
			if($('#pay_check').is(':checked'))
				$('#pay_check').siblings().children('i').css('color','#008543');
			
			if($('#pay_applepay').is(':checked'))
				$('#pay_applepay').siblings().children('i').css('color','#008543');
			
			if($('#pay_cash').is(':checked'))
				$('#pay_cash').siblings().children('i').css('color','#008543');
			
			if($('#pay_andropay').is(':checked'))
				$('#pay_andropay').siblings().children('i').css('color','#008543');
			
			if($('#pay_diners_club').is(':checked'))
				$('#pay_diners_club').siblings().children('i').css('color','#008543');
			
			if($('#pay_financing').is(':checked'))
				$('#pay_financing').siblings().children('i').css('color','#008543');

			if($('#pay_amex').is(':checked'))
				$('#pay_amex').siblings().children('i').css('color','#004fc8');
			
			if($('#pay_paypal').is(':checked'))
				$('#pay_paypal').siblings().children('i').css('color','#004fc8');
			
			if($('#pay_bitcoin').is(':checked'))
				$('#pay_bitcoin').siblings().children('i').css('color','#004fc8');
			
			if($('#pay_samsungpay').is(':checked'))
				$('#pay_samsungpay').siblings().children('i').css('color','#004fc8');

			//Sets the payment option checkbox color. Checked = #e94d00. Uncheck = e1e1e1
			$('input.payments').change(function(){
				if($('#pay_discover').is(':checked'))
					$('#pay_discover').siblings().children('i').css('color','#e94d00');
				else
					$('#pay_discover').siblings().children('i').css('color','#e1e1e1');
				if($('#pay_visa').is(':checked'))
					$('#pay_visa').siblings().children('i').css('color','#1a1e5a');
				else
					$('#pay_visa').siblings().children('i').css('color','#e1e1e1');
				if($('#pay_invoice').is(':checked'))
					$('#pay_invoice').siblings().children('i').css('color','#1a1e5a');
				else
					$('#pay_invoice').siblings().children('i').css('color','#e1e1e1');
				if($('#pay_mastercard').is(':checked'))
					$('#pay_mastercard').siblings().children('i').css('color','#cc0000');
				else
					$('#pay_mastercard').siblings().children('i').css('color','#e1e1e1');
				if($('#pay_travelers_check').is(':checked'))
					$('#pay_travelers_check').siblings().children('i').css('color','#cc0000');
				else
					$('#pay_travelers_check').siblings().children('i').css('color','#e1e1e1');
				if($('#pay_check').is(':checked'))
					$('#pay_check').siblings().children('i').css('color','#74C0FF');
				else
					$('#pay_check').siblings().children('i').css('color','#e1e1e1');
				if($('#pay_applepay').is(':checked'))
					$('#pay_applepay').siblings().children('i').css('color','#74C0FF');
				else
					$('#pay_applepay').siblings().children('i').css('color','#e1e1e1');
				if($('#pay_cash').is(':checked'))
					$('#pay_cash').siblings().children('i').css('color','#008543');
				else
					$('#pay_cash').siblings().children('i').css('color','#e1e1e1');
				if($('#pay_andropay').is(':checked'))
					$('#pay_andropay').siblings().children('i').css('color','#004fc8');
				else
					$('#pay_andropay').siblings().children('i').css('color','#e1e1e1');
				if($('#pay_diners_club').is(':checked'))
					$('#pay_diners_club').siblings().children('i').css('color','#004fc8');
				else
					$('#pay_diners_club').siblings().children('i').css('color','#e1e1e1');
				if($('#pay_financing').is(':checked'))
					$('#pay_financing').siblings().children('i').css('color','#004fc8');
				else
					$('#pay_financing').siblings().children('i').css('color','#e1e1e1');
				if($('#pay_amex').is(':checked'))
					$('#pay_amex').siblings().children('i').css('color','#004fc8');
				else
					$('#pay_amex').siblings().children('i').css('color','#e1e1e1');
				if($('#pay_paypal').is(':checked'))
					$('#pay_paypal').siblings().children('i').css('color','#004fc8');
				else
					$('#pay_paypal').siblings().children('i').css('color','#e1e1e1');
				if($('#pay_bitcoin').is(':checked'))
					$('#pay_bitcoin').siblings().children('i').css('color','#004fc8');
				else
					$('#pay_bitcoin').siblings().children('i').css('color','#e1e1e1');
				if($('#pay_samsungpay').is(':checked'))
					$('#pay_samsungpay').siblings().children('i').css('color','#004fc8');
				else
					$('#pay_samsungpay').siblings().children('i').css('color','#e1e1e1');
			});*/
			
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
			/*
			$(function() {
				$( "#sortable1, #sortable2" ).sortable({
				connectWith: ".connectedSortable",
				placeholder: "ui-state-highlight",
				helper: 'clone',
				sort: function(e, ui) {
					$(ui.placeholder).html(Number($("#sortable1 > li:visible").index(ui.placeholder)) + 1);
				},
				update: function(event, ui) {
					populateForm(event, ui)
					var $lis = $(this).children('li');
					$lis.each(function() {
						var $li = $(this);
						var newVal = $(this).index() + 1;
						$(this).children('.sortable-number').html(newVal);
						$(this).children('#item_display_order').val(newVal);
					});
				}
				}).disableSelection();
			});
			function populateForm(event, ui) {
				$('#advertise_product').val($("#sortable1").sortable( "toArray",{"attribute":"data-value"}).join());
				$('#donotadvertise_product').val($("#sortable2").sortable( "toArray",{"attribute":"data-value"}).join());
				
			}*/
			$(document).on('focus', '.datepicker', function(e){
				$(this).datepicker();
			});
			$(document).on('click', '.removeHourRow,.removeProdRow,.removeServRow,.removeSpecRow,.removeCertRow,.removeBrandRow,.removeLanguageRow', function(e){
				$(this).parent().remove();
			});
			
			$(document).on('click', '.removephoto', function(e){
				var image_container = $(this).parent();
				
				image_container.parent().remove();
			});
			
			//Adds the string "AM" to the holStart[] input and "PM" to the holEnd[] input
			$(document).on('focusout', '.hol-am', function(e){
				var val=$(this).val();
				if (val.indexOf("M") < 0 && val.length > 0)
					$(this).val(val+" AM" );
			});
			$(document).on('focusout', '.hol-pm', function(e){
				var val=$(this).val();
				if (val.indexOf("M") < 0 && val.length > 0)
					$(this).val(val+" PM" );
			});
			/*Enables or disables the input with class time depending on the selected choice 
			* Enable = "OPEN". Disabled = "ALWAYS_OPEN, CLOSED, AND IS_REGULAR_HOURS". 
			* Also, it creates two input type text if the selected option is "SPLIT".
			*/
			$(document).on('change', 'select[name="hours-type[]"]', function(e){
				if($(this).val()=="ALWAYS_OPEN" || $(this).val()=="CLOSED" || $(this).val()=="IS_REGULAR_HOURS"){
					$(this).siblings(".time").prop('disabled', true);
					$(this).siblings(".time").val("");
					$(this).siblings(".splitHours").html("");
				}else if($(this).val()=="SPLIT"){
					$(this).siblings(".splitHours").html('and <input type="text" class="form-control time hol-pm" name="holStart_split[]"> to <input type="text" class="form-control time hol-pm" name="holEnd_split[]">');
					$(this).siblings(".time").prop('disabled', false);
				}else{
					$(this).siblings(".time").prop('disabled', false);
					$(this).siblings(".splitHours").html("");
				}
			});
			//Adds another holiday hour input with the trash icon
			$(".holidayAdd").click(function(e){
				$("#holiday-container").append('<div class="form-inline hourRow"><i class="far fa-trash-alt removeHourRow cursor-pointer mr-2 text-danger" aria-hidden="true"></i><input type="text" class="form-control datepicker" name="holiday-date[]"> <select name="hours-type[]" class="mx-2 form-control rounded custom-select-arrow"> <option value="OPEN">Open</option> <option value="SPLIT">Split</option> <option value="ALWAYS_OPEN">24 hour</option> <option value="CLOSED">Closed</option> <option value="IS_REGULAR_HOURS">Regular Hours</option> </select> <input type="text" class="form-control time hol-am" name="holStart[]"> <span class="mx-1 small">to </span> <input type="text" class="form-control time hol-pm" name="holEnd[]"> <div class="splitHours"></div></div>');
			});
			$(".languageAdd").click(function(e){
				var inputName = $(this).data('input_name'); // i.e, products[], services[], specialties[], etc

				if(checkLimit(inputName)){
					$("#language-container").append('<div class="form-inline col-sm-3"><i class="far fa-trash-alt removeLanguageRow mr-2" aria-hidden="true"></i> <input type="text" class="form-control w-90 rounded apiLimit" name="languages[]">');
				}else{
					inputName =  inputName.replace("[]", "");
					alert("Up to 100 "+inputName+" offered are allowed.");
				}
			});
			$(".brandsAdd").click(function(e){
				var inputName = $(this).data('input_name'); // i.e, products[], services[], specialties[], etc

				if(checkLimit(inputName)){
					$("#brands-container").append('<div class="form-inline col-sm-3"><i class="far fa-trash-alt removeBrandRow" aria-hidden="true"></i> <input type="text" class="form-control w-90 rounded apiLimit" name="brands[]">');
				}else{
					inputName =  inputName.replace("[]", "");
					alert("Up to 100 "+inputName+" offered are allowed.");
				}
			});
			$(".certsAdd").click(function(e){
				var inputName = $(this).data('input_name'); // i.e, products[], services[], specialties[], etc

				if(checkLimit(inputName)){
					$("#certifications-container").append('<div class="form-inline col-sm-3 mb-2"><i class="far fa-trash-alt removeCertRow" aria-hidden="true"></i> <input type="text" class="form-control w-90 rounded apiLimit" name="certifications[]">');
				}else{
					inputName =  inputName.replace("[]", "");
					alert("Up to 100 "+inputName+" offered are allowed.");
				}
			});
			$(".specAdd").click(function(e){
				var inputName = $(this).data('input_name'); // i.e, products[], services[], specialties[], etc

				if(checkLimit(inputName)){
					$("#specialties-container").append('<div class="form-inline col-sm-3 mb-2"><i class="far fa-trash-alt removeSpecRow" aria-hidden="true"></i> <input type="text" class="form-control w-90 rounded apiLimit" name="specialties[]">');
				}else{
					inputName =  inputName.replace("[]", "");
					alert("Up to 100 "+inputName+" offered are allowed.");
				}
			});
			$(".servAdd").click(function(e){
				var inputName = $(this).data('input_name'); // i.e, products[], services[], specialties[], etc

				if(checkLimit(inputName)){
					$("#serv-container").append('<div class="form-inline col-sm-3 mb-2"><i class="far fa-trash-alt removeServRow" aria-hidden="true"></i> <input type="text" class="form-control w-90 rounded apiLimit" name="services[]">');
				}else{
					inputName =  inputName.replace("[]", "");
					alert("Up to 100 "+inputName+" offered are allowed.");
				}
			});
			$(".prodAdd").click(function(e){
				var inputName = $(this).data('input_name'); // i.e, products[], services[], specialties[], etc

				if(checkLimit(inputName)){
					$("#prod-container").append('<div class="form-inline col-sm-3 mb-2"><i class="far fa-trash-alt removeProdRow" aria-hidden="true"></i> <input type="text" class="form-control w-90 rounded apiLimit" name="products[]">');
				}else{
					inputName =  inputName.replace("[]", "");
					alert("Up to 100 "+inputName+" offered are allowed.");
				}
			});
			
			/* Save Actions */
		});
		
		//Needed to validate fields that require emails separated by comma
		$('.send_to').on('focusin', function(){
			if (typeof $(this).data('original_val') !== 'undefined'){
				$(this).data('original_val', $(this).val());
			}
		});
		
		//Validate fields that require emails separated by comma
		$(document).on('change', '.send_to', function(e){
			var emails_list = $(this).val();
			var invalid_emails;
			var valid_emails;
			//Get data-values
			var original_val = (typeof $(this).data('original_val') !== 'undefined') ? $(this).data('original_val') : '';
			var allowed_emails_qty = (typeof $(this).data('allowed_emails_qty') !== 'undefined') ? $(this).data('allowed_emails_qty') : 5;
			var allowed_delimiter = (typeof $(this).data('allowed_delimiter') !== 'undefined') ? $(this).data('allowed_delimiter') : ',';
			
			//Gets Invalid Emails
			invalid_emails  = emails_list.split(allowed_delimiter);
			invalid_emails = get_emails_list(invalid_emails, 'invalid').join(); // The function is in /js/functions.js
			
			if(invalid_emails != ''){
				alert('Please, enter valid emails.');
				$(this).val(original_val);
			}else{
				//Gets valid emails
				valid_emails  = emails_list.split(allowed_delimiter);
				valid_emails = get_emails_list(valid_emails, 'valid');
				valid_emails_length = valid_emails.length;
				valid_emails_new_value = valid_emails.join();
				
				if(valid_emails_length > allowed_emails_qty){
					alert('Up to ' +allowed_emails_qty+ ' emails are allowed.');
					$(this).val(original_val);
				}else{
					$(this).val(valid_emails_new_value);
				}
			}
		});
		
		//Validate twiter and instagram handle
		$('.handle').on('focusin', function(){
			if (typeof $(this).data('original_val') !== 'undefined'){
				$(this).data('original_val', $(this).val());
			}
		});
	
		//Validate twiter and instagram handle
		$(document).on('change', '.handle', function(e){
			var arrayLen, i, handleRules;
			var instagramRules = ["https://www.instagram.com/", "https://instagram.com/", "http://instagram.com/","http://www.instagram.com/","www.instagram.com/","instagram.com/","https://www.facebook.com/","/?hl=en","@"];
			var twitterRules = ["https://www.twitter.com/", "https://twitter.com/", "http://twitter.com/","http://www.twitter.com/","www.twitter.com/","twitter.com/","https://www.facebook.com/","/?hl=en","?lang=en","@"];
			var facebookRules = ["https://","http://","https://www.","http://www.","www.","/?hl=en","?lang=en","@","?ref=bookmarks","?ref=page_internal"];
			var original_val = (typeof $(this).data('original_val') !== 'undefined') ? $(this).data('original_val') : '';
			
			if($(this).attr("name") == 'instagram'){
				arrayLen = instagramRules.length;
				handleRules = instagramRules;
			}else if($(this).attr("name") == 'twitter'){
				arrayLen = twitterRules.length;
				handleRules = twitterRules;
			}else if($(this).attr("name") == 'facebook'){
				arrayLen = facebookRules.length;
				handleRules = facebookRules;
			}
			
			if($(this).val().endsWith("/")){
				alert('Invalid Format. The value cannot content a "/" symbol at the end');
				$(this).val(original_val);
			}
			
			for (i = 0; i < arrayLen; i++) {
			  if($(this).val().includes(handleRules[i])){
				alert('Invalid Format. The value cannot content: ' + handleRules[i]);
				$(this).val(original_val);
				break;
			  }
			}	
		});
		
		$(function () {
		  $('[data-toggle="tooltip"]').tooltip()
		})
		
		/*function initMap() {
		  var myLatLng = {lat: <?=$locationList['latitude']?>, lng: <?=$locationList['longitude']?>}; 

		  var map = new google.maps.Map(document.getElementById('mapMarker'), {
			zoom: 18,
			center: myLatLng
		  });

		  var marker = new google.maps.Marker({
			position: myLatLng,
			map: map,
			  draggable:true,
		  });
			google.maps.event.addListener(marker, 'dragend', function (event) {

				document.getElementById("lat").value = event.latLng.lat();

				document.getElementById("lng").value = event.latLng.lng();

				infoWindow.open(map, marker);
			});
		}*/
		
	function checkLimit(inputName){
		var elements_number = $('input[name="'+inputName+'"]').length;
		var limitOk = true;
		
		if(elements_number > 99){
			limitOk = false;
		}
		
		return limitOk;
	}
    </script>
	<script src="https://www.adjack.net/validate-files-js/validate-files.js"></script>
	
  </body>
</html>