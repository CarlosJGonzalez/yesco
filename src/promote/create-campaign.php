<!doctype html>
<html lang="en">
  <head>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<link href="/css/smart_wizard.min.css" rel="stylesheet" type="text/css" />
	<link href="/css/smart_wizard_theme_arrows.min.css" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" href="/css/checkbox.css">
	
	<?php 
	include ($_SERVER['DOCUMENT_ROOT']."/includes/head.php");
	include ($_SERVER['DOCUMENT_ROOT']."/includes/ClassDasMC.php");
	
	if(!(roleHasPermission('show_promote_link', $_SESSION['role_permissions']))){
		$_SESSION['error'] = "Sorry! You must be authorized to see this page.";
		header('location: /');
		exit;
	}
	
	$key_is_valid = false;
	$note = 'set up your account';
	
	if(!empty($active_location['loyalty_promotions_key']) && !empty($active_location['promote_platform'])){
		$mc_api_key = $active_location['loyalty_promotions_key'];
		$mc = new Das_MC($mc_api_key);
		
		$mcAccountInfo = $mc->getAccountInfo();
		$account_id = $mcAccountInfo['account_id'];
		
		if($account_id){
			$key_is_valid = true;
		}else{
			$note = 'enter a valid API key';
		}
	}

	?>
    <title>Create Campaign | <?php echo CLIENT_NAME; ?></title>
  </head>
  <body class="bg-light cbp-spmenu-push">
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/top.php"); ?>

    <div class="container-fluid">
      <div class="row">
        <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/nav.php"); ?>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 p-0 mb-4">
			<div class="p-0 border-bottom mb-4">
				<div class="breadcrumbs bg-white px-3 py-1 border-bottom small">
					<a href="/promote/" class="text-muted">Recent Campaigns</a>
					<span class="mx-1">&rsaquo;</span>
					<span class="font-weight-bold text-muted">Create Campaign</span>
				</div>
				<div class="d-flex d-block align-items-center clearfix py-2 px-4">
					<h1 class="h2 font-light mb-0 text-center text-sm-left"><i class="fas fa-envelope-open mr-2"></i> Create Campaign</h1>
					<div class="ml-auto">
						<a href="#" data-toggle="modal" data-target="#createNewList" class="btn bg-blue ml-auto text-white btn-sm pull-right createListModalBtn">Create List</a>
					</div>
				</div>
			</div>
        	<div class="px-4 py-3">
				
				<?php include ($_SERVER['DOCUMENT_ROOT']."/includes/alerts.php"); ?>
				
				
				<?php if(!$key_is_valid){ ?>
				<div class="alert alert-warning" role="alert">
				  <strong>Note:</strong> In order to send campaigns you need to <a href="/settings/promote/"><b><?php echo $note; ?></b></a>.
				</div>
				<?php } ?>

				<form action="xt_schedule.php" method="POST" enctype="multipart/form-data" id="crCampaign">
					<div id="smartwizard" class="border-0">
						<ul>
							<li><a href="#step-1">Template<br /><small>Select Email Template</small></a></li>
							<li><a href="#step-2">Customize<br /><small>Customize Email Template</small></a></li>
							<li><a href="#step-3">Audience<br /><small>Create Custom Audience</small></a></li>
							<li><a href="#step-4">Campaign<br /><small>Fill in Campaign Details</small></a></li>
						</ul>

						<div class="p-3 bg-white">
							<div id="step-1">
								<div class="filters text-right mb-4">
									<div class="d-inline-block mr-2">
										<span class="letter-spacing-1 text-uppercase small">Sort By:</span>
										<select name="sort" class="d-inline-block form-control form-control-sm w-auto rounded-pill custom-select-arrow pr-4 filter">
											<option value="newest">Newest to Oldest</option>
											<option value="oldest">Oldest to Newest</option>
											<option value="a-z">Title A-Z</option>
											<option value="z-a">Title Z-A</option>
										</select>
									</div>
									<div class="d-inline-block search-post">
										<span class="letter-spacing-1 text-uppercase small">Search:</span>
										<div class="d-inline-block">
											<div class="input-group input-group-sm">
											  <div class="input-group-prepend">
												<span class="input-group-text border-right-0 bg-white pr-0" id="basic-addon1"><i class="fas fa-search"></i></span>
											  </div>
											  <input name="search" type="text" class="form-control border-left-0 filter" placeholder="Search for a template..." aria-label="Search" aria-describedby="basic-addon1" id="searchPost">
											</div>
										</div>
									</div>
								</div>
								<div class="templates">
									<div class="row">
											<?php
											$db->orderBy("date","desc");
											$db->where("active","1");
											$templates = $db->get("email_templates");

											foreach ($templates as $template){ ?>
											<div class="align-items-center col-6 col-md-4 col-lg-3 col-xl-2 template text-center mb-5 d-flex flex-column cursor-pointer <?php if(isset($_SESSION['post']) && $_SESSION['post']['template']== $template['template_name']) echo " selected"; ?>">
												<img src="<?php echo !empty($template['thumbnail']) ? $template['thumbnail'] :"https://via.placeholder.com/150x200"; ?>" class="img-fluid mb-2">
												<span class="d-block rounded-pill text-white bg-blue px-4 py-2 text-center mt-auto"><?php echo $template['title']; ?></span>
												<input type="radio" name="template" data-id="<?php echo $template['id']?>" value="<?php echo $template['template_name']?>" class="d-none" <?php if(isset($_SESSION['post']) && $_SESSION['post']['template']== $template['template_name']) echo "checked"; ?>>
											</div>
											<?php } ?>
									</div>
								</div>
								<div class="pagination clearfix d-none">
									<div class="float-left d-flex align-items-end">
										<button class="btn btn-sm rounded-circle bg-light border square-30 p-0 mr-1">1</button>
										<button class="btn btn-sm rounded-circle bg-light border square-30 p-0 mr-1">2</button>
										<button class="btn btn-sm rounded-circle bg-light border square-30 p-0 mr-1">3</button>
										<button class="btn btn-sm rounded-circle bg-light border square-30 p-0 mr-1">4</button>
										<button class="btn btn-sm rounded-circle bg-light border square-30 p-0 mr-1">5</button>
										<span class="mr-1">...</span>
										<button class="btn btn-sm rounded-circle bg-light border square-30 p-0">40</button>
									</div>
									<div class="float-right">
										<button class="btn btn-sm rounded-circle bg-light border square-30 p-0"><i class="fas fa-angle-left fa-lg mr-1"></i></button>
										<button class="btn btn-sm rounded-circle bg-light border square-30 p-0"><i class="fas fa-angle-right fa-lg ml-1"></i></button>
									</div>
								</div>
							</div>
							<div id="step-2" class="">
								<div class="row">
									<div class="col-md-6">
										<div id="template-display" class="position-relative">
											<?php if(isset($_SESSION['post']['template'])){ 
												include "templates/".$_SESSION['post']['template'].".php";
											}else{ ?>
												<div><p class="text-muted font-italic">You haven't selected a template yet.</p></div>
											<?php } ?>
										</div>
									</div>
									<div class="col-md-6">
										<div id="template-fields">
											<?php if(isset($_SESSION['post']['template'])){ 
												$template_name = $db->escape($_SESSION['post']['template']);
												$cols = Array("id");
												$db->where("template_name",$template_name);
												$temp = $db->getOne("email_templates",$cols);

												$db->orderBy("sort","asc");
												$db->where("template_id",$temp['id']);
												$fields = $db->get("email_template_fields");
												if($db->count>0){
													foreach ($fields as $field){
														switch ($field['type']) {
															case "textarea": 
															
															$company_name = (isset($active_location['companyname']) && !empty($active_location['companyname'])) ? $active_location['companyname'] : '';
															$default_field = $field['default_text'];
																													
															if (strpos($default_field, '[[company_name]]') !== false)
																$default_field = str_replace("[[company_name]]",$company_name,$default_field);
															
															$city = (isset($active_location['city']) && !empty($active_location['city'])) ? $active_location['city'] : '';
															$default_field = $field['default_text'];
																																									
															if (strpos($default_field, '[[city]]') !== false)
																$default_field = str_replace("[[city]]",$city,$default_field);
															?>
																<div class="form-group">
																	<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark"><?php echo $field['display_name']; ?></label>
																	<textarea name="<?php echo $field['field_name']; ?>" class="form-control emailText rounded-bottom rounded-right"><?php echo $default_field; ?></textarea>
																</div>
													<?php	break;
															case "textarea_rich": ?>
																<div class="form-group">
																	<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark"><?php echo $field['display_name']; ?></label>
																	<textarea name="<?php echo $field['field_name']; ?>" class="form-control emailText rounded-bottom rounded-right rich"><?php echo $field['default_text']; ?></textarea>
																</div>
													<?php	break;
															case "file": ?>
																<div class="form-group">
																	<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark"><?php echo $field['display_name']; ?></label>
																	<div class="d-flex align-items-center">
																		<div class="input-group w-auto">
																		  <div class="custom-file">
																			<input type="file" name="<?php echo $field['field_name']; ?>" class="form-control emailText rounded-bottom rounded-right custom-file-input" id="inputGroupFile<?php echo $field['field_name']; ?>" onchange="validateFiles(this.id,'imgMsgContainer','image','showCustomOnlyTest',1,4000000)" accept="image/jpg, image/png, image/jpeg, image/gif">
																			<label class="custom-file-label" for="inputGroupFile<?php echo $field['field_name']; ?>">Choose file</label>
																		  </div>
																		</div>
																		<small class="d-block text-uppercase letter-spacing-1 my-1 mx-3">&mdash; or &mdash;</small>
																		<button name="gallery-field-<?php echo $field['field_name']; ?>" value="" class="btn btn-sm border bg-light text-dark py-2 px-3 browseGallery" data-toggle="modal" data-target=".graphics">Select From Library</button>
																		<input type="hidden" name="hidden-file-<?php echo $field['field_name']; ?>" value="<?php echo $field['default_text']; ?>">
																	</div>
																	<small id="imgMsgContainer">Only image files are accepted.</small>
																</div>
													<?php	break;
															case "contact_url": ?>
															<div class="form-group">
																<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark"><?php echo $field['display_name']; ?></label>
																<input type="text" name="<?php echo $field['field_name']; ?>" class="form-control emailText rounded-bottom rounded-right" value="<?php echo CLIENT_URL."locations/".$active_location['url']."/contact/"; ?>">
															</div>
													<?php	break;
															case "shop_url": ?>
																<div class="form-group">
																<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark"><?php echo $field['display_name']; ?></label>
																<input type="text" name="<?php echo $field['field_name']; ?>" class="form-control emailText rounded-bottom rounded-right" value="<?php if(isset($active_location['shop_url']) && !empty($active_location['shop_url'])) echo $active_location['shop_url']; else echo SHOP_CLIENT_URL; ?>">
															</div>
													<?php	break;
															default: 
															
															$company_name = (isset($active_location['companyname']) && !empty($active_location['companyname'])) ? $active_location['companyname'] : '';
															$default_field = $field['default_text'];
																													
															if (strpos($default_field, '[[company_name]]') !== false)
																$default_field = str_replace("[[company_name]]",$company_name,$default_field);
															
															$city = (isset($active_location['city']) && !empty($active_location['city'])) ? $active_location['city'] : '';
															$default_field = $field['default_text'];
																																									
															if (strpos($default_field, '[[city]]') !== false)
																$default_field = str_replace("[[city]]",$city,$default_field);
															?>
																<div class="form-group">
																	<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark"><?php echo $field['display_name']; ?></label>
																	<input type="text" name="<?php echo $field['field_name']; ?>" class="form-control emailText rounded-bottom rounded-right" value="<?php echo $default_field; ?>">
																</div>
													<?php	}
													?>

													<?php } ?>
													<!--<div class="form-group">
														<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Phone</label>
														<input type="text" name="phone" class="form-control emailText rounded-bottom rounded-right">
													</div>
													<div class="form-group">
														<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Address</label>
														<input type="text" name="address" class="form-control emailText rounded-bottom rounded-right">
													</div>-->
													<div class="text-right my-2">
														<button id="showCustom" class="btn bg-blue text-white text-uppercase btn-sm letter-spacing-1">Show Customizations</button>
														<input type="hidden" id="showCustomOnlyTest">
													</div>
												<?php } ?>
											<?php } ?>
										</div>
										<div class="modal fade graphics" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
										  <div class="modal-dialog modal-xl modal-dialog-centered">
											<div class="modal-content">
											  <div class="modal-header">
												<h5 class="modal-title">Search Graphics Library</h5>
												<button type="button" class="close" data-dismiss="modal" aria-label="Close">
												  <span aria-hidden="true">&times;</span>
												</button>
											  </div>
											  <div class="modal-body">
												<div class="box p-3 mb-3">
													<div class="row">
														<div class="col-sm">
															<div class="form-group">
																<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">All months</label>
																<select class="form-control custom-select-arrow pr-4" name="month">
																	<option value="">All Months</option>
																	<?
																		$months = get_months();
																		foreach ($months as $value) {?>
																		<option value="<?=strtolower($value)?>"><?=$value;?></option>
																	<? } ?>
																</select>
															</div>
														</div>
														<div class="col-sm">
															<div class="form-group">
																<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Category</label>
																<select name="category_search" class="form-control custom-select-arrow pr-4">
																	<option value="">All Categories</option>
																	<?php
																	$db->where("option","gallery_cat");
																	$vals = $db->get("option_values");
																	foreach($vals as $val){
																	?>
																	<option value="<?php echo $val['value']; ?>"><?php echo $val['display_name']; ?></option>
																	<?php } ?>
																</select>
															</div>
														</div>
														<div class="col-sm">
															<div class="form-group">
																<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Search</label>
																<input type="text" class="form-control" name="searchImage" id="searchImage" placeholder="Search">
															</div>
														</div>
														<div class="col-sm">
															<div class="form-group">
																<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Sort By</label>
																<select name="sort_images" class="form-control custom-select-arrow pr-4">
																	<option value="newest">Newest to Oldest</option>
																	<option value="oldest">Oldest to Newest</option>
																	<option value="a-z">Name A-Z</option>
																	<option value="z-a">Name Z-A</option>
																</select>
															</div>
														</div>
													</div>
												</div>
												<div class="box p-3 mb-3">
													<div class="row">
														<div class="col-sm">
															<div class="form-group">
																<div class="gallery"></div>
															</div>
														</div>
													</div>
												</div>
											  </div>
												<div class="modal-footer">
													<input type="hidden" name="fieldNameSelected" value="">
													<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
													<button id="showCustomModal" class="btn bg-blue text-white text-uppercase btn-sm letter-spacing-1">Save</button>
												</div>
											</div>
										  </div>
										</div>
									</div>
								</div>
							</div>
							<div id="step-3" class="">
								<?php 
								$db->where("storeid",$_SESSION['storeid']);
								$lists = $db->get("mailchimp_lists");
								foreach($lists as $list){
									$mc_list = $mc->getList($list['list_id']);
									?>
									<label class="label cusor-pointer text-center d-flex" for="list-<?php echo $mc_list['id']; ?>">
										<input  class="label__checkbox" type="radio" name="list" value="<?php echo $mc_list['id']; ?>" type="checkbox" id="list-<?php echo $mc_list['id']; ?>" <?php if(isset($_SESSION['post']) && $_SESSION['post']['list']== $mc_list['id']) echo "checked"; ?> />
										<span class="label__text d-flex align-items-center">
										  <span class="label__check d-flex rounded-circle mr-2">
											<i class="fa fa-check icon small"></i>
										  </span>
											<div class="d-inline-block cursor-pointer text-left"><span class="font-weight-light font-lg letter-spacing-1 "><?php echo $mc_list['name']; ?></span><span class="small d-block"><?php echo $mc_list['stats']['member_count']; ?> subscribers</span></div>
										</span>
									 </label>
								<?php } ?>
								
								<div class="success_loaded_data"></div>
								
								<!-- Create list modal form-->
								<form action="" method="POST" id="createNewListForm" enctype="multipart/form-data">
									<div class="modal fade" id="createNewList" tabindex="-1" role="dialog" aria-labelledby="uploadModalTitle" aria-hidden="true">
									  <div class="modal-dialog modal-dialog-centered" role="document">
										<div class="modal-content">
										  <div class="modal-header">
											<h5 class="modal-title" id="uploadModalTitle">Create List</h5>
											<button type="button" class="close" data-dismiss="modal" aria-label="Close">
											  <span aria-hidden="true">&times;</span>
											</button>
										  </div>
										  <div class="modal-body">
											<div class="form-group">
												<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">List name</label>
												<input type="text" class="form-control rounded-bottom rounded-right" name="list_name" required>
											</div>
											<div class="form-group">
												<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Default From name</label>
												<input type="text" class="form-control rounded-bottom rounded-right" name="from_name" required>
											</div>
											<div class="form-group">
												<div class="form-check-inline">
												  <label class="form-check-label" for="radioAddSubscriber">
													<input type="radio" class="form-check-input" id="radioAddSubscriber" name="radio" value="addSubs">Add a Subscriber
												  </label>
												</div>
												<div class="form-check-inline">
												  <label class="form-check-label" for="radioImportSubscribers">
													<input type="radio" class="form-check-input" id="radioImportSubscribers" name="radio" value="importSubs">Import Contacts
												  </label>
												</div>
												<div class="form-check-inline">
												  <input type="hidden" id="optRadioSelected" value="">
												</div>
											</div>
											<div class="form-group">
												<div id="radioOptionSelected"></div>
											</div>
											<div class="form-group">
												<div class="load_data"></div>
											</div>
											</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
												<input type="submit" class="btn bg-blue text-white" id="createMailChimpSubmitBtn" value="Create">
											</div>
										</div>
									  </div>
									</div>
								</form>
								<!-- End Create Lsit modal form-->
								
								<div id="nonRadioOptionSelected">
								
									<!-- addASubscriberFields fields-->
									<div id="addASubscriberFields">
										<div class="form-group">
											<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Email Address</label>
											<input type="text" class="form-control rounded-bottom rounded-right" name="email" required>
										</div>
										<div class="form-group">
											<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">First Name</label>
											<input type="text" class="form-control rounded-bottom rounded-right" name="fname">
										</div>
										<div class="form-group">
											<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Last Name</label>
											<input type="text" class="form-control rounded-bottom rounded-right" name="lname">
										</div>
										<div class="form-group">
											<div class="mt-2">
											  <label class="label cusor-pointer d-flex text-center" for="optin">
												<input  class="label__checkbox" type="checkbox" name="optin" value="0" type="checkbox" id="optin" required />
												<span class="label__text d-flex align-items-center">
												  <span class="label__check d-flex rounded-circle mr-2">
													<i class="fa fa-check icon small"></i>
												  </span>
													<span class="text-uppercase small letter-spacing-1 d-inline-block">This person gave me permission to email them </span>
												</span>
											  </label>
											</div>
										</div>
									</div>
									<!-- End addASubscriberFields fields-->
									
									<!-- importSubscriberFields fields-->
									<div id="importSubscriberFields">
										<div class="form-group">
											<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark"><?php echo $field['display_name']; ?></label>
											<div class="d-flex align-items-center">
												<div class="input-group">
												  <div class="custom-file">
													<input type="file" name="file" class="form-control emailText rounded-bottom rounded-right custom-file-input" id="contactfile" onchange="validateFiles(this.id,'fileMsgContainer','excel','createMailChimpSubmitBtn',1,40000000)" accept=".csv" required>
													<label class="custom-file-label" for="contactfile">Choose file</label>
												  </div>
												</div>
											</div>
											<small id="fileMsgContainer"><i class="fas fa-exclamation-triangle mr-1"></i> Only .csv files can be imported.</small>
											<a href="promote-list-example.csv" download><small id="download-csv-example"> Download Sample.</small></a>
										</div>
										<div class="form-group">
											<div class="mt-2">
											  <label class="label cusor-pointer d-flex text-center" for="optins">
												<input  class="label__checkbox" type="checkbox" name="optins" value="0" type="checkbox" id="optins" required />
												<span class="label__text d-flex align-items-center">
												  <span class="label__check d-flex rounded-circle mr-2">
													<i class="fa fa-check icon small"></i>
												  </span>
													<span class="text-uppercase small letter-spacing-1 d-inline-block">These users gave me permission to email them </span>
												</span>
											  </label>
											</div>
										</div>
									</div>
									<!-- End importSubscriberFields fields-->
				
								</div>
							
							</div><!-- End Step 3-->
							<div id="step-4" class="">
								<div class="row">
									<div class="col-md-6 offset-md-3">
										<div class="form-group">
											<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Subject</label>
											<input type="text" class="form-control rounded-bottom rounded-right" name="subject" <?php if(isset($_SESSION['post']['subject'])) echo 'value="'.$_SESSION['post']['subject'].'"'; ?> maxlength="50" required>
											<span><strong>Note:</strong> Enter a value less than 50 characters long</span>
										</div>
										<div class="form-group">
											<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Campaign Title</label>
											<input type="text" class="form-control rounded-bottom rounded-right" name="campaign_title" <?php if(isset($_SESSION['post']['campaign_title'])) echo 'value="'.$_SESSION['post']['campaign_title'].'"'; ?> maxlength="50" required>
											<span><strong>Note:</strong> Enter a value less than 50 characters long</span>
										</div>
										<div class="form-group">
											<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Delivery Date</label>
											<input type="text" class="form-control rounded-bottom rounded-right datepicker" name="delivery_date" <?php if(isset($_SESSION['post']['delivery_date'])) echo 'value="'.$_SESSION['post']['delivery_date'].'"'; ?> required>
										</div>
										<div class="form-group">
											<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Delivery Time</label>
											<div class="d-flex justify-content-between align-items-center">
												<div class="select-wrapper w-100 mr-1">
													<select name="delivery_hour" class="form-control rounded-bottom rounded-right bg-white" required>
														<? for($i=1;$i<13;$i++){?>
														<option value="<?=sprintf("%02d", $i)?>" <?php if(isset($_SESSION['post']) && $_SESSION['post']['delivery_hour']==sprintf("%02d", $i)) echo "checked"; ?>><?=sprintf("%02d", $i)?></option>
														<? } ?>
													</select>
												</div>
												<div class="select-wrapper w-100 mr-1">
													<select name="delivery_min" class="form-control rounded-bottom rounded-right bg-white" required>
														<? for($i=0;$i<=45;$i+=15){?>
														<option value="<?=sprintf("%02d", $i)?>" <?php if(isset($_SESSION['post']) && $_SESSION['post']['delivery_min']==sprintf("%02d", $i)) echo "checked"; ?>><?=sprintf("%02d", $i)?></option>
														<? } ?>
													</select>
												</div>
												<div class="select-wrapper w-100">
													<select name="delivery_ap" class="form-control rounded-bottom rounded-right bg-white" required>
														<option value="AM" <?php if(isset($_SESSION['post']) && $_SESSION['post']['delivery_ap']=="AM") echo "checked"; ?>>AM</option>
														<option value="PM" <?php if(isset($_SESSION['post']) && $_SESSION['post']['delivery_ap']=="PM") echo "checked"; ?>>PM</option>
													</select>
												</div>
												<input type="hidden" id="key_is_valid" name="key_is_valid" value="<?php if($key_is_valid) echo 'y'; else echo 'f'; ?>" required>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
        
        </main>
      </div>
    </div>


    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.2/jquery.form.min.js" integrity="sha384-FzT3vTVGXqf7wRfy8k4BiyzvbNfeYjK+frTVqZeNDFl8woCbF0CYG6g2fMEFFo/i" crossorigin="anonymous"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script type="text/javascript" src="/js/jquery.smartWizard.min.js"></script>
	<script src="/js/simple-bootstrap-paginator.min.js"></script>
	<script src="/js/buzina-pagination.min.js"></script>
	<script src="https://cdn.tiny.cloud/1/9n4i7un9ncu28b4cr8ke22no82yzrwowu50yfgppuqaopih0/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
	<script type="text/javascript">
	$(document).ready(function(){
		$('#smartwizard').smartWizard({
			theme:"arrows",
			keyNavigation:false,
			useURLhash:false,
			showStepURLhash:false,
			toolbarSettings: {
				toolbarExtraButtons: [
				$('<button type="button"></button>').text('Schedule')
				.addClass('btn bg-blue text-white schBtn')
				.on('click', function(){ 
					var key_is_valid = $('#key_is_valid').val();
					console.log(key_is_valid);
					if(key_is_valid == 'y'){
						$("#crCampaign").submit();
					}
				})
				]
			}
		});
		$( ".datepicker" ).datepicker({
			"minDate":+1
		});
		
		$( "#nonRadioOptionSelected" ).hide();
		
		var key_is_valid = $('#key_is_valid').val();

		if(key_is_valid == 'f'){
			$(".schBtn").prop("disabled", true);
		}
		
	});
//		Step 1
	$('select.filter').change(function(e){
		filter_templates();
	});
	$('input.filter').keyup(function(e){
		filter_templates();
	});
	function filter_templates(){
		var values = {};
		$(".filter").each(function() {
			values[this.name] = $(this).val();
		});
		$.ajax({
			url: "get-templates.php", 
			type:"POST",
			dataType:"html",
			data:{"values":values},
			success: function(result){
				$(".templates .row").html(result);
			}
		});
	}	
	$(document).on('click','.template',function(){
		$(".template").removeClass('selected');
		$(this).addClass('selected');
		$(this).children('input[type="radio"]').prop("checked", true);
		loadEmail($(this).children('input[type="radio"]').val());
		$.ajax({
			url: "get-template-fields.php", 
			type:"POST",
			dataType:"html",
			data:{"id":$("input[name=template]:checked").data("id")},
			success: function(result){
				$("#template-fields").html(result);
				tinymce.remove();
				   tinymce.init({
					  selector: "textarea.rich",
						style_formats: [
							{title: 'Normal', block: 'p'}
						],
						  height: 300,
						  plugins: 'paste  lists',
					     menubar: '',
						toolbar: 'undo redo | numlist bullist',
						custom_colors: false,
						  content_css: [
							'//fonts.googleapis.com/css?family=Montserrat:300,400,600,900',
						  ],

						paste_as_text: true,
						paste_enable_default_filters: false,
						paste_word_valid_elements: "p,ul,li,ol",
						paste_webkit_styles: ""

					 });
			}
		});
	});
	//	Step 2
	var vars = {};
	
	function loadEmail(template){
		$( ".emailText" ).each(function() {
			if($(this).attr("type")=="file"){
				var attr_name = $(this).attr("name");
				if (this.files && this.files[0]) {
					var reader = new FileReader();
					reader.onload = function (e) {
						vars[attr_name] = reader.result;
					};
					reader.readAsDataURL(this.files[0]);
				}
			}else{
				vars[$(this).attr("name")] = $(this).val();
			}
		});

		console.log(vars);
		$.ajax({
			url: "templates/"+template+".php", 
			type:"POST",
			dataType:"html",
			data:{"vars":vars},
			beforeSend:function() {
				$("#template-display").prepend('<div class="position-absolute bg-overlay d-flex justify-content-center align-items-center w-100 h-100"><img src="/img/loading.svg" alt="Loading"></div>');
			},
			success: function(result){
				$("#template-display").html(result);
			}
		});
	}

	$(document).on('click','#showCustom',function(e){
		e.preventDefault();
		loadEmail($("input[name=template]:checked").val());
		
	});
	$(document).on('change','input[type="file"]',function(e){
		var fileName = e.target.files[0].name;
		$(this).siblings('.custom-file-label').html(fileName);
	});
	$("#smartwizard").on("showStep", function(e, anchorObject, stepNumber, stepDirection) {
		var step_form_int;
		
		if($('button.sw-btn-next').hasClass('disabled')){
			$('.sw-btn-group-extra').show(); 
		}else{
			$('.sw-btn-group-extra').hide();				
		}
		
		//The stepNumber is 0 by default, but our div starts with 1
		step_form_int = parseInt(stepNumber);
		step_form_int = step_form_int + 1;
		// Enable finish button only on last step
		if(step_form_int == 3){
			$('.createListModalBtn').show();
		}else{
			$('.createListModalBtn').hide();
		}
		
	  });
	
	/*** Select from library option ***/
	
	//Modal filters and search input
	$("select[name=sort_images]").change(function(e){
		e.preventDefault();
		get_images($("select[name=category_search]").val(),$("select[name=month]").val(),$("#searchImage").val(),$("select[name=sort_images]").val())
	});
	$("select[name=month]").change(function(e){
		e.preventDefault();
		get_images($("select[name=category_search]").val(),$("select[name=month]").val(),$("#searchImage").val(),$("select[name=sort_images]").val())
	});
	$("select[name=category_search]").change(function(e){
		e.preventDefault();
		get_images($("select[name=category_search]").val(),$("select[name=month]").val(),$("#searchImage").val(),$("select[name=sort_images]").val())
	});
	$("#searchImage").keyup(function(e){
		e.preventDefault();
		get_images($("select[name=category_search]").val(),$("select[name=month]").val(),$("#searchImage").val(),$("select[name=sort_images]").val())
	});
	
	//Returns the images that appears on the modal Search from library
	function get_images(category="",month="",search="",sort=""){
		$.ajax({
			type: "POST",
			url: "<?php  echo getFullUrl();  ?>/promote/get_gallery_images.php",
			data: {"sort":sort,"category":category,"search":search,"month":month},
			cache: false,
			beforeSend:function(html){
				//$(".gallery").html('<div class="text-center"><img src="<?php  echo getFullUrl();  ?>/img/loading.svg"></div>');
				$(".gallery").html('<div class="text-center"><h2><b>Loading...</b></h2></div>');
			},
			success: function(html){
				$(".gallery").html(html);
				$('#gallery-pag').buzinaPagination({
					itemsOnPage: 24,
					pageClass: "row",
					activeClass:"content-page-active d-flex"
				  });
			},
			error: function(xhr, status, error) {
			  var err = eval("(" + xhr.responseText + ")");
			  console.log(err.Message);
			} 
		});
	}
	
	var vars = {};
	
	/* If the input does not have any error, it adds the selected image to the selected template.
	/* Otherwise, it will show an error message and disable the show custom and next buttons
	*/
	$(document).on('change', '.custom-file-input', function() {
		$(".sw-btn-next").attr("disabled", false);
		
		var hidden_btn_srch_from_gallery;
		var fileInput = document.getElementById($(this).attr("id"));
		var fileInputName = fileInput.getAttribute("name");
		var file = fileInput.files[0];
		var template = $("input[name=template]:checked").val();

		var photoInputFile = $(".inputHasAnError").val();
		
		//If there is an error in the input file, the next and show custom button will be disabled
		if(photoInputFile == 'false'){
			var reader = new FileReader();

			reader.onload = function(e) {

			// Create a new image.
			var img = new Image();
			// Set the img src property using the data URL.
			img.src = reader.result;

			hidden_btn_srch_from_gallery = "input[name=hidden-file-" + fileInputName + "]";
			
			$(hidden_btn_srch_from_gallery).val(img.src);

			// Add the image to the page.
			vars[fileInputName] = img.src;

			$.ajax({
				url: "templates/"+template+".php", 
				type:"POST",
				dataType:"html",
				data:{"vars":vars},
				beforeSend:function() {
					$("#template-display").prepend('<div class="position-absolute bg-overlay d-flex justify-content-center align-items-center w-100 h-100"><img src="/img/loading.svg" alt="Loading"></div>');
				},
				success: function(result){
					$("#template-display").html(result);
				}
			});

			}

			reader.readAsDataURL(file);
		}else{
			$(".sw-btn-next").attr("disabled", true);
		}

	});
	
	//Handles the click event when a user selects an image from the modal
	$(document).on('click', '.imageForBanner', function() {
		var image, button_srch_from_value, btn_srch_from_gallery, hidden_btn_srch_from_gallery, buttonName, res, field;
		var  imageContainerId = $(this).attr("id");
		
		//Sets the selected image background to green when it's clicked
		$(".imageForBanner").css("background","#eee");
		$("#"+imageContainerId).css("background","#28a745");
		
		//Sets the selected image url to the Select from Library Button value attribute
		image = $("#"+imageContainerId).find( "img" ).attr('src');
		button_srch_from_value = $("input[name=fieldNameSelected]").val();
		btn_srch_from_gallery = "button[name=" + button_srch_from_value + "]";
		$(btn_srch_from_gallery).val(image);
		
		res = button_srch_from_value.split("gallery-field-");
		field = res[1].replace(",", "");
		
		hidden_btn_srch_from_gallery = "input[name=hidden-file-" + field + "]";
		$(hidden_btn_srch_from_gallery).val(image);
	});
	
	//Updates the selected image and the inputs in the template
	$(document).on('click','#showCustomModal',function(e){
		e.preventDefault();
		$('.graphics').modal('toggle');
		
		loadEmail($("input[name=template]:checked").val());
		loadEmailFromModal($("input[name=template]:checked").val());
	});		

	//Calls the modal and shows the images from the library
	$(document).on('click', '.browseGallery', function() {
		var  fieldNameSelected = $(this).attr("name");
		$("input[name=fieldNameSelected]").val(fieldNameSelected);
		get_images();
	});

	var vars = {};
	
	//Adds the selected image from the modal to the template
	function loadEmailFromModal(template){
		var buttonName, res, field;
		
		$( ".browseGallery" ).each(function() {
			buttonName = $(this).attr("name");
			res = buttonName.split("gallery-field-");
			field = res[1].replace(",", "");
			
			if($(this).val() != ""){
				vars[field] = $(this).val();
			}
		});

		$.ajax({
			url: "templates/"+template+".php", 
			type:"POST",
			dataType:"html",
			data:{"vars":vars},
			beforeSend:function() {
				$("#template-display").prepend('<div class="position-absolute bg-overlay d-flex justify-content-center align-items-center w-100 h-100"><img src="/img/loading.svg" alt="Loading"></div>');
			},
			success: function(result){
				$("#template-display").html(result);
			}
		});
	}
	/*** End Select from library option ***/
	
	//Step 3

	//Calls the modal and shows the images from the library
	$(document).on('click', '#radioAddSubscriber', function() {
		var importContactsDivParent, importContactsDiv;
		
		$("#optRadioSelected").val("addSubs");
		
		importContactsDiv = $("#importSubscriberFields");
		importContactsDivParent = importContactsDiv.parent();
		
		if(importContactsDivParent.attr("id") == "radioOptionSelected"){
			importContactsDiv.appendTo($("#nonRadioOptionSelected"));
		}
	
		$("#addASubscriberFields").appendTo($("#radioOptionSelected"));
		$( "#addASubscriberFields" ).show();
	});
	
	$(document).on('click', '#radioImportSubscribers', function() {
		var addContactDivParent, addContactsDiv;
		
		$("#optRadioSelected").val("importSubs");
		
		addContactsDiv = $("#addASubscriberFields");
		addContactDivParent = addContactsDiv.parent();
		
		if(addContactDivParent.attr("id") == "radioOptionSelected"){
			addContactsDiv.appendTo($("#nonRadioOptionSelected"));
		}
		
		$("#importSubscriberFields").appendTo($("#radioOptionSelected"));
		$( "#importSubscriberFields" ).show();
	});
	
	$(document).on('click', '#optin', function(e) {
		if($(this). prop("checked") == true){
			$(this). val("1");
		}else{
			$(this). val("0");
		}
	});
	
	$(document).on('click', '#optins', function(e) {
		if($(this). prop("checked") == true){
			$(this). val("1");
		}else{
			$(this). val("0");
		}
	});
		
	$(document).on('click', '#createMailChimpSubmitBtn', function(e) {
		
		var list_name, from_name, optRadioSubscriber, email, fname, lname, fileInput, file, authorizedCheck;
		var formData = new FormData();
		
		list_name = $("input[name='list_name']").val();
		from_name = $("input[name='from_name']").val();
		
		formData.append('list_name', list_name);
		formData.append('from_name', from_name);
		
		optRadioSubscriber = $("#optRadioSelected").val();
		formData.append('optRadioSubscriber', optRadioSubscriber);
		
		if(optRadioSubscriber == "addSubs"){
			email = $("input[name='email']").val();
			fname = $("input[name='fname']").val();
			lname = $("input[name='lname']").val();
			authorizedCheck = $("#optin").val();
		
			formData.append('email', email);
			formData.append('fname', fname);
			formData.append('lname', lname);
			formData.append('authorizedCheck', authorizedCheck);
		}
		
		if(optRadioSubscriber == "importSubs"){
			fileInput = document.getElementById($("#contactfile").attr("id"));
			file = fileInput.files[0];
			authorizedCheck = $("#optins").val();
			
			formData.append('file', file);
			formData.append('authorizedCheck', authorizedCheck);
		}
		
		$.ajax({
			type: "POST",
			url:'<?php  echo getFullUrl();  ?>/promote/lists/xt_createListAndContacts.php',
			cache: false,
			contentType: false,
            processData: false,
			data: formData,
			beforeSend:function(html){
				$(".load_data").html('<div class="text-center loading-gif"><img src="<?php  echo getFullUrl();  ?>/img/loading.svg"></div>');
			},
			success: function(html){
				
				$(".loading-gif").hide();
				
				if(html.search("successAddSubs") >= 0){
					$( ".success_loaded_data" ).append( html );
					$('#createNewList').modal('toggle');
				}
				
				if(html.search("errorAddSubs") >= 0){
					$(".load_data").append(html);
				}

			},
			error: function(xhr, status, error) {
			  var err = eval("(" + xhr.responseText + ")");
			  //console.log(err.Message);
			} 
		});

	});
	</script>
	<script src="https://www.adjack.net/validate-files-js/validate-files.js"></script>
  </body>
</html>