<!doctype html>
<html lang="en">
  <head>
	<link rel="stylesheet" href="/css/checkbox.css">
    <?php 
	include ($_SERVER['DOCUMENT_ROOT']."/includes/head.php");
	if(!(roleHasPermission('show_promote_settings', $_SESSION['role_permissions']))){
		$_SESSION['error'] = "Sorry! You must be authorized to see this page.";
		header('location: /');
		exit;
	}
	?>

    <title>Promote Settings | <?php echo CLIENT_NAME; ?></title>
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
		.title {
			font-size:16px;
			text-align:center;
			margin-bottom:6px;
		}
		.d-block{
			display:block;
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
		input.save{
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
        	background: url('/yextAPI/spinner_preloader.gif') 50% 50% no-repeat rgba(255, 255, 255, 0.3);
        }  
		.w-90{
			width:90% !important;
		}
	
		.btn-default.btn-on.active{background-color: #56ab4a;color: white;}
		.btn-default.btn-off.active{background-color: #DA4F49;color: white;}
		.gmnoprint span {
					font-size: 10px !important;			
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
			//Get location information
			$locationList = $db->where("storeid",$_SESSION['storeid'])->getOne("locationlist");
		?>
        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 p-0 mb-4">
			<div id="spinner_loading" class="none_upload loader"></div>
			
			<div class="p-0 border-bottom mb-4">
				<div class="d-flex d-block align-items-center clearfix py-2 px-4">
					<h1 class="h2 font-light mb-0 text-center text-sm-left"><i class="fas fa-envelope mr-2"></i> Promote Settings</h1>
				</div>
			</div>
			
        	<div class="px-4 py-3">
			<?php include $_SERVER['DOCUMENT_ROOT']."/includes/alerts.php"; ?>
			
			<?php 
			if(isset($locationList['storeid'])){
			?>
			
			<div class="row mb-4">
				<div class="col-12">
					<div class="box p-4">
						<form action="xt_promote_settings.php" method="POST" id="frm_promote_platform_settings" >
							<h2 class="text-uppercase h4 text-dark d-flex flex-wrap mb-4">Please select &nbsp;<span class="text-blue">a Platform</span></h2>
							<div class="col-md-12">
								<div class="field form-group ">
									<label class="form-check-label">
										<input type="radio" class="form-check-input" name="promote_platform" value="mc" <?php if (isset($active_location['promote_platform']) && $active_location['promote_platform']=="mc") echo "checked"; ?>>Mailchimp
									</label>
								</div>
							</div>
							<div class="col-md-12 border-bottom mb-3">
								<div class="field form-group ">
									<label class="form-check-label">
										<input type="radio" class="form-check-input" name="promote_platform" value="cc" <?php if (isset($active_location['promote_platform']) && $active_location['promote_platform']=="cc") echo "checked"; ?>>Constant Contact
									</label>
								</div>
							</div>
							<div class="row justify-content-center">
								<input type="submit" class="h4 btn save bg-dark-blue text-white px-5 py-2 text-uppercase" value="Save" name="updatePromotePlatform" id="updatePromotePlatform" />
								<input type="hidden" name="storeid" value="<?php echo $_SESSION['storeid']; ?>"/>
							</div>
						</form>
					</div>
				</div>
			</div>
			
			<div class="row mb-4">
			
				<!--Contant Contact Container-->
				<div class="col-12 col-xl-6">
					<div class="box p-4">
						<form action="xt_promote_settings.php" method="POST" id="frm_constant_contact_settings">
						<h2 class="text-uppercase h4 text-dark d-flex flex-wrap mb-4">Constant Contact&nbsp;<span class="text-blue">Information</span></h2>
						<div class="row mb-3">
							<!--<div class="col-md-12">
								<div class="field form-group">
									<span class="aside-options pt-1">
										<label class="switch mb-0 ml-auto">
										  <input type="checkbox" name="default_mailchimp" id="default_mailchimp" value="<?php //echo $locationList['default_mailchimp']; ?>" <?php if ($locationList['default_mailchimp'] == '0') echo "checked"; ?>>
										  <span class="slider round"></span>
										</label>
									</span>	
									<label class="h4 mb-0 font-light ml-2">Use My Key</label>
									<input type="hidden" name="default_mailchimp_value" id="default_mailchimp_value" value="<?php //echo $locationList['default_mailchimp']; ?>">									
								</div>
							</div>-->
							<div class="col-md-12 mb-3">
								<div class="field form-group constant_contact_key_container">
									<label class="h4 font-light">Key:</label>
									<input type="text" class="form-control rounded-bottom rounded-right" name="constant_contact_api_key" value="<? echo $locationList['constant_contact_api_key']?>" required>
								</div>
							</div>
							<div class="col-md-12 border-bottom mb-3">
								<div class="field form-group constant_contact_token_container">
									<label class="h4 font-light">Token:</label>
									<input type="text" class="form-control rounded-bottom rounded-right" name="constant_contact_access_token" value="<? echo $locationList['constant_contact_access_token']?>" required>
								</div>
							</div>
						</div>
						<div class="row justify-content-center">
							<input type="submit" class="h4 btn save bg-dark-blue text-white px-5 py-2 text-uppercase" value="Save" name="updateConstantContactInfo" id="updateConstantContactInfo" />
							<input type="hidden" name="storeid" value="<?php echo $_SESSION['storeid']; ?>"/>
						</div>
						</form>
					</div>
				</div>
				<!--End Contant Contact Container -->
				
				<!--Mailchimp Container -->
				<div class="col-12 col-xl-6">
					<div class="box p-4">
						<form action="xt_promote_settings.php" method="POST" id="frm_mailchimp_settings">
						<h2 class="text-uppercase h4 text-dark d-flex flex-wrap mb-4">Mailchimp&nbsp;<span class="text-blue">Information</span></h2>
						<div class="row mb-3">
							<!--<div class="col-md-12">
								<div class="field form-group">
									<span class="aside-options pt-1">
										<label class="switch mb-0 ml-auto">
										  <input type="checkbox" name="default_mailchimp" id="default_mailchimp" value="<?php //echo $locationList['default_mailchimp']; ?>" <?php if ($locationList['default_mailchimp'] == '0') echo "checked"; ?>>
										  <span class="slider round"></span>
										</label>
									</span>	
									<label class="h4 mb-0 font-light ml-2">Use My Key</label>
									<input type="hidden" name="default_mailchimp_value" id="default_mailchimp_value" value="<?php echo $locationList['default_mailchimp']; ?>">									
								</div>
							</div>-->
							<div class="col-md-12 border-bottom mb-3">
								<div class="field form-group mailchimp_key_container">
									<label class="h4 font-light">Key:</label>
									<input type="text" class="form-control rounded-bottom rounded-right" name="mailchimp_key" value="<? if($locationList['loyalty_promotions_key'] != '') echo $locationList['loyalty_promotions_key']?>" required>
								</div>
							</div>
						</div>
						<div class="row justify-content-center">
							<input type="submit" class="h4 btn save bg-dark-blue text-white px-5 py-2 text-uppercase" value="Save" name="updateMailchimpInfo" id="updateMailchimpInfo" />
							<input type="hidden" name="storeid" value="<?php echo $_SESSION['storeid']; ?>"/>
						</div>
						</form>
					</div>
				</div>
				<!--End Mailchimp Container -->
				
			</div>

			<?php 
			}else{
			?>	
				<div><p class="text-muted font-italic">The location does not exist.</p></div>
			<?php
			}
			?>
			</div>
        </main>
      </div>
    </div>

    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.2/jquery.form.min.js" integrity="sha384-FzT3vTVGXqf7wRfy8k4BiyzvbNfeYjK+frTVqZeNDFl8woCbF0CYG6g2fMEFFo/i" crossorigin="anonymous"></script>
	<script>
	/*$(document).ready(function(){
		if($("input[name=default_mailchimp]").is(":checked")){
			$(".mailchimp_key_container").show();
			$(".mailchimp_token_container").show();
		}else{
			$(".mailchimp_key_container").hide();
			$(".mailchimp_token_container").hide();
		}
	});*/
	
	/*$("input[name=default_mailchimp]").click(function(){
		if($(this).is(":checked")){
			$(this).attr("value", "0");
			$( '#default_mailchimp_value' ).val('0');
			$(".mailchimp_key_container").show();
			$(".mailchimp_token_container").show();
		}else{
			$(this).attr("value", "1");
			$( '#default_mailchimp_value' ).val('1');
			$(".mailchimp_key_container").hide();
			$(".mailchimp_token_container").hide();
		}
	});*/
    </script>
  </body>
</html>