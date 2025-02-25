<!doctype html>
<html lang="en">
<head>
<?php 
include ($_SERVER['DOCUMENT_ROOT']."/includes/head.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/ClassDasConstantContact.php");
if(!(roleHasPermission('show_promote_link', $_SESSION['role_permissions']))){
	$_SESSION['error'] = "Sorry! You must be authorized to see this page.";
	header('location: /');
	exit;
}

//Only for test purpose
/*$active_location['constant_contact_api_key'] = 'j3bn9adcxrgg2jvxd6nmg75b';
$active_location['constant_contact_access_token'] = '138e5b8a-ad09-419b-92f7-399d64875e4f';*/

if(empty($active_location['constant_contact_api_key']) || empty($active_location['constant_contact_access_token'])){
	$_SESSION['error'] = "Please enter a valid api key and token.";
	header('location: /settings/promote/');
	exit;
}else{
	$cc_api_key = $active_location['constant_contact_api_key'];
	$cc_access_token = $active_location['constant_contact_access_token'];
}

//ClassDasConstantContact 
$cc = new Das_ConstantContact($cc_api_key, $cc_access_token);
?>
<title>Promote | <?php echo CLIENT_NAME; ?></title>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<style>
		/*Sticky message coming from yext.js */
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
        <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/nav.php"); ?>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 p-0 mb-4">
			<div class="sticky"></div>
			<div class="p-0 border-bottom mb-4">
				<div class="d-flex d-block align-items-center clearfix py-2 px-4">
					<h1 class="h2 font-light mb-0 text-center text-sm-left"><i class="fas fa-mail-bulk mr-2"></i> Recent Campaigns</h1>
				</div>
			</div>
        	<div class="px-4 py-3">
			
				<?php include ($_SERVER['DOCUMENT_ROOT']."/includes/alerts.php"); ?>
			
				<div class="row">
					<div class="col-md-4 col-lg-3 col-xl-2 order-md-last">
						<a href="create-campaign.php" class="btn btn-lg bg-dark-blue d-block text-white w-100 py-3 mb-3">Create Campaign</a>
						<!-- <a href="" class="btn btn-lg bg-dark-blue d-block text-white w-100 py-3 mb-3">View Templates</a>-->
						<a href="lists/" class="btn btn-lg bg-dark-blue d-block text-white w-100 py-3 mb-3">Manage Lists</a>
					</div>
					<div class="col-md-8 col-lg-9 col-xl-10 order-md-first">
						<?php 
						$db->where("storeid",$_SESSION['storeid']);
						$db->orderBy("date_created_or_sent","desc");
						$campaigns = $db->get("promote_campaigns");

						if($db->count>0){
							foreach($campaigns as $campaign){							
								$campaign_details = $cc->getCampaign($campaign['campaign_id']);
								$campaign_id = $campaign['campaign_id'];
							?>
								<div class="border rounded p-2 mb-3">
									<div class="row align-items-center">
										<div class="d-none d-md-block col-md-2 text-center">
											<img src="https://via.placeholder.com/250x400" class="img-fluid max-h-100">
											<?php 
											/*if(!$campaign_details['is_error'] || !empty($campaign_id)){
												//$campaign_thumbnail = "https://campaign-thumbnail.constantcontact.com/v1/customer/1133778916741/activity/$campaign_id/size/150x220/mtime/0?generate=false";
												echo '<img src="'.$campaign_thumbnail.'" class="img-fluid max-h-100">';
											}else{
												echo '<img src="https://via.placeholder.com/250x400" class="img-fluid max-h-100">';
											}*/
											?>
										</div>
										<div class="col-sm-6">
											<span class="h3 mb-1 text-capitalize span-title-editable"><?php echo $campaign_details['name']; ?></span>
											<input type='text' class='form-control rounded-bottom rounded-right input-title-editable' name='title-editable' value="<?php echo $campaign_details['name']; ?>" data-id="<?php echo $campaign['campaign_id']; ?>" >
											<?php if($campaign_details['status'] == "DRAFT"){ ?>
												<a href title="Edit Campaign Title" class="ml-2 edit-title-icon"><i class="fas fa-edit"></i></a>
											<?php } ?>
											<div class="d-block">
												<span class="text-blue text-uppercase font-weight-bold"><?php echo $campaign_details['status']; ?></span>
												<span class="text-muted"><?php echo date('M d, o h:i:s A',strtotime($campaign_details['modified_date'])); ?></span>
											</div>
										</div>
										<div class="col-sm-6 col-md-4">
											<div class="d-flex justify-content-between">
												 <?php 
												 $open_rate = ($campaign_details['tracking_summary']['sends'] != 0) ? round(($campaign_details['tracking_summary']['opens']/$campaign_details['tracking_summary']['sends'])*100,2) : 0;
												 $clik_rate = ($campaign_details['tracking_summary']['sends'] != 0) ? round(($campaign_details['tracking_summary']['clicks']/$campaign_details['tracking_summary']['sends'])*100,2) : 0;
												 ?>
												 <div class="text-right">
													 <span class="h4 text-blue d-block mb-1 font-weight-bold"><?php echo $open_rate; ?>%</span>
													 <span class="text-muted text-uppercase d-block">OPEN RATE</span>
												</div>
												<div class="text-right">
													 <span class="h4 text-blue d-block mb-1 font-weight-bold"><?php echo $clik_rate; ?>%</span>
													 <span class="text-muted text-uppercase d-block">CLICK RATE</span>
												</div>
												<div class="text-right">
													 <div class="dropdown">
													  <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
														More
													  </button>
													  <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
													    <a class="dropdown-item" href="email-report.php?id=<?php echo $campaign_details['id']?>">View Report</a>
														<a class="dropdown-item" href="#" id="<?php echo $campaign_details['id']?>" onclick="replicateCampaign(this.id)">Copy</a>
														<?php if($campaign_details['status'] != "SCHEDULED"){ ?>
														<a class="dropdown-item" href="" id="<?php echo $campaign_details['id']?>" data-toggle="modal" data-target="#deleteCampaignModal" data-backdrop="static" data-keyboard="false" onclick="getCampaignInfo(this.id)">Delete</a>
														<input type="hidden" id="<?php echo 'template_id_'.$campaign_details['id'] ;?>" value="<?php echo $campaign_details['settings']['template_id']; ?>">
														<?php } ?>
														<?php if($campaign_details['status'] == "SENT" && $resend_to_flag){ ?>
															<a class="dropdown-item" href="" id="resend_<?php echo $campaign_details['id']?>" data-toggle="modal" data-target="#resendToCampaignModal" data-backdrop="static" data-keyboard="false" onclick="getCampaignInfoResendToModal(this.id)">Resend to...</a>
														<?php } ?>
														<?php if($campaign_details['status'] == "DRAFT"){ ?>
															<a class="dropdown-item" href="edit-campaign.php?edit-campaign-id=<?php echo $campaign_details['id']; ?>">Edit</a>
														<?php } ?>
														<?php if($campaign_details['status'] == "SCHEDULED"){ ?>
															<a class="dropdown-item" href="" id="pause_<?php echo $campaign_details['id']?>" data-toggle="modal" data-target="#pauseCampaignModal" data-backdrop="static" data-keyboard="false" onclick="pauseCampaign(this.id)">Unscheduled</a>
														<?php } ?>
													  </div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							<?php }
						}else{ ?>
							<p class="text-muted font-italic">You have no campaigns yet.</p>
						<?php } ?>
					</div>
				</div>
			</div>
			
			<!-- Delete campaign modal form-->
			<form action="campaign_actions.php" method="POST" name="deleteCampaignForm" id="deleteCampaignForm">
				<div class="modal fade" id="deleteCampaignModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalTitle" aria-hidden="true">
				  <div class="modal-dialog modal-dialog-centered" role="document">
					<div class="modal-content">
					  <div class="modal-header text-center">
						<h5 class="modal-title center w-100" id="uploadModalTitle">Are you sure?</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close" name="closeDeleteCampaignModal">
						  <span aria-hidden="true">&times;</span>
						</button>
					  </div>
					  <div class="modal-body">
							<p>You're about to delete <span>this campaign</span></p>
							<div id="deleteCampaignMsgContainer"></div>
								<table class="table table-striped border" id="deleteCampaignModalTable">
									<thead>
										<tr>
											<th>Title</th>
											<th>Status</th>
										</tr>
									</thead>
									<tbody id='deleteCampaignModalTBody'></tbody>
								</table>
							<div class="form-group delete_confirmation_input">
								<label class="text-uppercase small font-weight-bold">Type DELETE to confirm <span class="text-danger">*</span></label>
								<input type="text" name="delete_confirmation_ok" id="delete_confirmation_ok" class="form-control" autocomplete="off" required />
							</div>
							<div id="delete_confirmation_msg"></div>
					  </div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary btn-sm " data-dismiss="modal" name="closeDeleteCampaignModal">Close</button>
							<input type="hidden" name="campaign_id_to_delete" id="campaign_id_to_delete" value="">
							<input type="hidden" name="template_id_to_delete" id="template_id_to_delete" value="">
							<input type="submit" class="btn bg-blue text-white btn-sm" value="Delete" id="submitBtnDeleteCampaign" name="submitBtnDeleteCampaign">
						</div>

					</div>
				  </div>
				</div>
			</form>
			<!-- End Delete campaign modal form-->
			
			<!-- Resend to modal form-->
			<form action="campaign_actions.php" method="POST" name="resendToCampaignForm" id="resendToCampaignForm">
				<div class="modal fade" id="resendToCampaignModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalTitle" aria-hidden="true">
				  <div class="modal-dialog modal-dialog-centered" role="document">
					<div class="modal-content">
					  <div class="modal-header text-center">
						<h5 class="modal-title center w-100" id="uploadModalTitle">Are you sure?</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close" name="closeResendToCampaignModal">
						  <span aria-hidden="true">&times;</span>
						</button>
					  </div>
					  <div class="modal-body">
							<p>You're about to resend <span>this campaign</span></p>
							<div id="resendToCampaignMsgContainer"></div>
							<table class="table table-striped border" id="resendToCampaignModalTable">
								<thead>
									<tr>
										<th>Title</th>
										<th>Status</th>
									</tr>
								</thead>
								<tbody id='resendToCampaignModalTBody'></tbody>
							</table>
								
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
								</div>
							</div>
								
							<div class="form-group">
								<label class="text-uppercase small font-weight-bold">Type RESEND to confirm <span class="text-danger">*</span></label>
								<input type="text" name="resend_to_confirmation_ok" id="resend_to_confirmation_ok" class="form-control" autocomplete="off" required />
							</div>
							<div id="resend_to_confirmation_msg"></div>
					  </div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary btn-sm " data-dismiss="modal" name="closeResendToCampaignModal">Close</button>
							<input type="hidden" name="campaign_id_to_resend" id="campaign_id_to_resend" value="">
							<input type="submit" class="btn bg-blue text-white btn-sm" value="Resend" id="submitBtnResendToCampaign" name="submitBtnResendToCampaign">
						</div>

					</div>
				  </div>
				</div>
			</form>
			<!-- End Resend campaign modal form-->
        
        </main>
      </div>
    </div>


    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script>
	$(document).ready(function(){

		$( ".datepicker" ).datepicker({
			"minDate":0
		});
		
		$( ".input-title-editable" ).hide();

	});
	
	/****** Delete campaign option *****/
	function getCampaignInfo(linkElement){
		var template_id;
		template_id = $("#template_id_"+linkElement).val();
		
		$("#template_id_to_delete").val(template_id);
		$("#campaign_id_to_delete").val(linkElement);
		
		$.ajax({
			type: "POST",
			url: "<?php  echo getFullUrl();  ?>/promote-cc/campaign_ajax.php",
			data: {"campaign_data_ajax":linkElement},
			dataType:"html",
			cache: false,
			success: function(result){	  
				if(result != ''){
					if(!result.includes("alert alert-danger")){	
						$("#deleteCampaignModalTBody").replaceWith(result);
					}else{
						$("#deleteCampaignModalTable").remove();
						$("#deleteCampaignMsgContainer").append('<p class="alert alert-danger">There was a problem fetching the information.</p>');
						$(".delete_confirmation_input").hide();
					}
				}else{
					$("#deleteCampaignModalTable").remove();
					$("#deleteCampaignMsgContainer").append('<p class="alert alert-danger">There was a problem fetching the information.</p>');
					$(".delete_confirmation_input").hide();
				}
			},
			error: function(xhr, status, error) {
			  var err = eval("(" + xhr.responseText + ")");
			  //console.log(err.Message);
			} 
		});
	}
	
	/****** Resend to option *****/
	function getCampaignInfoResendToModal(campaignId){
		var field, res;
		
		res = campaignId.split("resend_");
		field = res[1];
		
		$("#campaign_id_to_resend").val(field);
		
		$.ajax({
			type: "POST",
			url: "<?php  echo getFullUrl();  ?>/promote-cc/campaign_ajax.php",
			data: {"campaign_data_ajax":field},
			dataType:"html",
			cache: false,
			success: function(result){	  
				if(result != ''){
					$("#resendToCampaignModalTBody").replaceWith(result);
				}else{
					$("#resendToCampaignModalTable").remove();
					$("#resendToCampaignMsgContainer").append('<p class="alert alert-danger">There was a problem fetching the information.</p>');
				}
			},
			error: function(xhr, status, error) {
			  var err = eval("(" + xhr.responseText + ")");
			  //console.log(err.Message);
			} 
		});
	}
	
	$("#submitBtnDeleteCampaign").click(function(event) {
		event.preventDefault();
	
		if($("#delete_confirmation_ok").val() == "DELETE"){
			$("#deleteCampaignForm").submit();
		}else{
			if($("#delete_confirmation_msg_para").length){
				$("#delete_confirmation_msg_para").replaceWith('<p class="alert alert-danger" id="delete_confirmation_msg_para">Please enter the text exactly as it is displayed to confirm.</p>');
			}else{
				$("#delete_confirmation_msg").append('<p class="alert alert-danger" id="delete_confirmation_msg_para">Please enter the text exactly as it is displayed to confirm.</p>');
			}
		}
	});
	
	$("#submitBtnResendToCampaign").click(function(event) {
		event.preventDefault();
	
		if($("#resend_to_confirmation_ok").val() == "RESEND"){
			$("#resendToCampaignForm").submit();
		}else{
			if($("#resend_to_confirmation_msg_para").length){
				$("#resend_to_confirmation_msg_para").replaceWith('<p class="alert alert-danger" id="resend_to_confirmation_msg_para">Please enter the text exactly as it is displayed to confirm.</p>');
			}else{
				$("#resend_to_confirmation_msg").append('<p class="alert alert-danger" id="resend_to_confirmation_msg_para">Please enter the text exactly as it is displayed to confirm.</p>');
			}
		}
	});
	
	$('[name="closeDeleteCampaignModal"]').click(function(event) {
		if($("#delete_confirmation_msg_para").length){
			$("#delete_confirmation_msg_para").remove();
		}
	});
	
	$('[name="closeResendToCampaignModal"]').click(function(event) {
		if($("#resend_to_confirmation_msg_para").length){
			$("#resend_to_confirmation_msg_para").remove();
		}
	});
	
	/****** End Delete campaign option *****/
	
	/****** Replicate campaign option *****/
	
	function replicateCampaign(campaign_id){
		if(confirm("Are you sure you want to proceed?")){
			window.location.href = "campaign_actions.php?campaign_id_to_replicate="+campaign_id;
		}
	}
	
	/****** Pause campaign option *****/
	
	function pauseCampaign(campaign_id){
		var field, res;
		
		res = campaign_id.split("pause_");
		field = res[1];
		
		if(confirm("Are you sure you want to pause this campaign?")){
			window.location.href = "campaign_actions.php?campaign_id_to_pause="+field;
		}
	}
	
	$('.edit-title-icon').click(function(event) {
		event.preventDefault();
		
		var span, input, edit_icon;
		
		input = $( this ).siblings( ".input-title-editable" );

		$( ".input-title-editable" ).each(function(){
			
			span = $( this ).siblings( ".span-title-editable" );
			edit_icon = $( this ).siblings( ".edit-title-icon" );
			
			if($( this ).val() == input.val()){
				$( this ).hide();
				span.hide();
				edit_icon.hide();
				input.show();
				input.focus();
			}else{
				$(this).hide();
				span.show();
				edit_icon.show();
			}	
		});
		
	});
	
	$(".input-title-editable").blur(function(){
		var campaign_id, campaign_title, span, edit_icon, edited_input, stringFound;
		
		campaign_title = $( this ).val();
		campaign_id = $( this ).data('id');
		
		$( this ).addClass("input-was-edited");
		
		$.ajax({
			type: "POST",
			url: "campaign_actions.php",
			data: {"edit_campaign_name":"true", "campaign_id":campaign_id , "campaign_title":campaign_title},
			dataType:"html",
			cache: false,
			success: function(result){
				stringFound = result.search("answer-fail");

				if(stringFound == '-1'){
					edited_input = $(".input-was-edited");
					edited_input.hide();
					span = edited_input.siblings( ".span-title-editable" );
					edit_icon = edited_input.siblings( ".edit-title-icon" );
					span.replaceWith(result);
					span.show();
					edit_icon.show();
					edited_input.removeClass("input-was-edited");
				}else{
					$(".sticky").html(result);
					edited_input = $(".input-was-edited");
					edited_input.hide();
					span = edited_input.siblings( ".span-title-editable" );
					edit_icon = edited_input.siblings( ".edit-title-icon" );
					span.show();
					edit_icon.show();
					edited_input.removeClass("input-was-edited");
				}
			},
			error: function(xhr, status, error) {
			  var err = eval("(" + xhr.responseText + ")");
			  console.log(err.Message);
			} 
		});
	
	});
	/****** End Replicate campaign option *****/
	</script>
  </body>
</html>