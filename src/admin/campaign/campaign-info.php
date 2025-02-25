<!doctype html>
<html lang="en">
  <head>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<link href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.css" rel="stylesheet" type="text/css" />
	<link href="//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
	<link href="//cdn.datatables.net/buttons/1.5.6/css/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
	<link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css" rel="stylesheet">
    <?php 
	include ($_SERVER['DOCUMENT_ROOT']."/includes/head.php");
	if( (!(roleHasPermission('show_ongoing_campaigns', $_SESSION['role_permissions']))) && (isset($_SESSION['email']))){
		header('location: /dashboard.php');
		exit;
	}
	?>
	<?php include ($_SERVER['DOCUMENT_ROOT']."/includes/ClassDasCampaign.php"); ?>
   	<style>
		.dt-buttons{
			margin-bottom:.5rem;
			margin-top:.5rem;
		}

		.dt-buttons > button{
			border-radius: 50rem !important;
			font-size: .875rem;
			line-height: 1.5;
			background-color:#0067b1;
			padding: .25rem 1rem;
			margin-right: .5rem !important;
			border:none;
		}
		
		.CTNOptions{
			display: none;
		}
		#whisperMessage{
			display: none;
		}

        #forwardAreacodeDiv{
            display: none;
        }

		#tollFreeDiv{
			display: none;
		}
	
		.ctn-addon{
			padding:5px;
		}
		.dash{
			position: relative;
			top: 7px;
			padding-left: 2px;
			padding-right: 2px;
		}
		.r-bracket{
			position: relative;
			top: 5px;
			padding-left: 5px;
			font-size: large;
		}
		.l-bracket{
			position: relative;
			top: 5px;
			padding-right: 5px;
			font-size: large;
		}
		
		.toggle.btn-sm {
  			min-width: 85%;
		}
		input#whisperMessagePortalAdd {
    		width: 85%;
		}
		
		
		input[type="checkbox"] {
			cursor: pointer;
			-webkit-appearance: none;
			-moz-appearance: none;
			appearance: none;
			outline: 0;
			background: whtie;
			height: 22px;
			width: 24px;
			border: 1px solid lightgray;
			position: relative;
			top: 2px;
		}
		input[type="checkbox"]:checked {
			background: #003d4c;
		}
		input[type="checkbox"]:hover {
			filter: brightness(90%);
		}
		input[type="checkbox"]:disabled {
			background: #e6e6e6;
			opacity: 0.6;
			pointer-events: none;
		}
		input[type="checkbox"]:after {
			position: relative;
			border-width:opx;
			display: none;
			margin-left: 3px;
			font-family: "Font Awesome 5 Free";
			font-weight: 900;
			content: "\f00c";
			color: white;
		}
		input[type="checkbox"]:checked:after {
			display: block;
		}
		input[type="checkbox"]:disabled:after {
			border-color: #7b7b7b;
		}
		
		
		.btn-primary{
			background-color: #003d4c;
    		border-color: #003d4c;
		}
		.btn-primary:hover{
			background-color: #012a34;
    		border-color: #012a34;
		}
		.btn-primary:not(:disabled):not(.disabled).active, .btn-primary:not(:disabled):not(.disabled):active, .show>.btn-primary.dropdown-toggle{
			background-color: #012a34;
    		border-color: #012a34;
		}
	</style>
    <title>Campaign Information | Local <?php echo CLIENT_NAME; ?></title>
  </head>
  <body class="bg-light cbp-spmenu-push">
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/top.php"); ?>

    <div class="container-fluid">
    	<div class="row">
    		<?php 
    		include ($_SERVER['DOCUMENT_ROOT']."/includes/nav.php"); 
    		?>

    		<main role="main" class="col-md-9 ml-sm-auto col-lg-10 p-0">

    			<div class="p-0 border-bottom mb-4">
    				<div class="d-flex d-block align-items-center clearfix py-2 px-4">
    					<h1 class="h2 font-light mb-0 text-center text-sm-left"><i class="fas fa-clipboard-list mr-2"></i>Campaigns Management</h1>
    					<?php if($_SESSION['admin']){ ?>
    						<div class="ml-auto">
    							<div class="dropdown d-inline-block">
    								<button type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="border-0 bg-transparent">
    									<i class="fas fa-2x text-muted fa-plus-circle"></i>
    								</button>
    								<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
    									<a href="#addModal" title="Add File" data-toggle="modal" data-target="#addModal" class="dropdown-item small">Add Ongoing Campaign</a>
    									<a href="#addPortalModal" title="Add File" data-toggle="modal" data-target="#addPortalModal" class="dropdown-item small">Add Portal Information</a>
    								</div>

    							</div>
    						</div>
    					<? } ?>
    				</div>
    			</div>

    			<div class="py-3 px-4">
    				<?php include $_SERVER['DOCUMENT_ROOT']."/includes/alerts.php"; ?>

    				<?php 
    					$campaigns_info = new Das_Campaign($db,$token_api,$_SESSION['client']);
    				?>
    				<ul class="nav nav-tabs" role="tablist">
    					<li class="nav-item">
    						<a class="nav-link text-blue active" data-toggle="tab" href="#tabs-campid">Campaign Information</a>
    					</li>
    					<li class="nav-item">
    						<a class="nav-link text-blue" data-toggle="tab" href="#tabs-ongoingcamp">Ongoing Campaign</a>
    					</li>
    					<li class="nav-item">
    						<a class="nav-link text-blue" data-toggle="tab" href="#tabs-endedcamp">Ended Campaign</a>
    					</li>
    					<li class="nav-item">
    						<a class="nav-link text-blue" data-toggle="tab" id="test" href="#tabs-mynumbers">My Numbers</a>
    					</li>
    				</ul>
    				<div class="tab-content p-2">
    					<div id="tabs-campid" class="tab-pane active">
    						<div class="table-responsive">
    							<table class="table" id="campaignIdDataTable">
    								<thead class="thead-dark">
    									<tr>
    										<th>Status</th>
    										<th>Description</th>
    										<th>Campaign Id</th>
    										<th>Source / Medium</th>
    										<th>Channel</th>
    										<?php if(roleHasPermission('show_markup_ongoing_campaigns', $_SESSION['role_permissions'])){ ?>
    											<th>MarkUp</th>
    										<?php } ?>
    										<th>CTN</th>
    										
    										<?php if(roleHasPermission('show_actions_ongoing_campaigns', $_SESSION['role_permissions'])){ ?>
    											<th class="nowrap">Actions</th>
    										<?php } ?>
    									</tr>
    								</thead>
    								<tbody>
    									<?php
    									$portals = $campaigns_info->getPortals();

    									foreach($portals as $portal){
    										$callFireRouting = $campaigns_info->getCallFireRouting($portal['campid'],array('is_inactive' => 0 ));    										
    										?>
    										<tr>
    											<td><?php echo ($callFireRouting['active'] == 'N')? 'Inactive':'Active'; ?></td>
    											<td><?php echo $portal['name']; ?></td>
    											<td><?php echo $portal['campid']; ?></td>
    											<td><?php echo $portal['source']." / ".$portal['medium']; ?></td>
    											<td><?php echo $portal['channel']; ?></td>
    											<?php if(roleHasPermission('show_markup_ongoing_campaigns', $_SESSION['role_permissions'])){ ?>
    												<td><?php echo $campaigns_info->getActiveMarkUpByDate($portal['id'],[]).'%'; ?></td>
    											<?php } ?>
    											<td>
    												<?php 
    													echo ($callFireRouting['count'] &&  !$callFireRouting['is_error'] && !$callFireRouting['data'][0]['inactive']) ? format_phone( $callFireRouting['data'][0]['phone'] ) : 'N/A';  
    												?>
    											</td>

    											<td class="nowrap">
                                                <?php if(roleHasPermission('show_markup_history', $_SESSION['role_permissions'])){ ?>
                                                    <a href="/admin/campaign/markup?campid=<?php echo $portal['id']; ?>" title="MarkUp History" class="text-light-d hitory" id="markup_history" data-id="<?php echo $portal['id']; ?>">
                                                        <i class="fas fa-lg fa-history text-dark cursor-pointer"></i>
                                                    </a>
                                                    &nbsp;&nbsp;
                                                <?php 
                                                } 
                                                if(roleHasPermission('show_actions_ongoing_campaigns', $_SESSION['role_permissions'])){ ?>
                                                        
                                                    <a href="" title="Copy To Clipboard Tracking Url" class="text-light-d copy" id="tracking_url" data-id="<?php echo $portal['id']; ?>">
                                                            <i class="fas fa-lg fa-link text-dark cursor-pointer"></i>
                                                        </a>
                                                        <input type="hidden" name="tracking_url" id="tracking_url_save">
                                                        &nbsp;&nbsp;
                                                        <a href="" title="Update" class="text-light-d  change_ctn" id="update_portal" data-id="<?php echo $portal['id']; ?>">
                                                            <i class="far fa-edit text-dark cursor-pointer"></i>
                                                        </a>
                                                        &nbsp;&nbsp;
                                                        <a href="" title="Change CTN" class="text-light-d change_ctn" id="change_ctn" data-id="<?php echo $portal['id']; ?>">
                                                            <i class="fa fa-exchange text-dark cursor-pointer"></i>
                                                        </a>
                                                        &nbsp;&nbsp;
                                                        <a href="" title="Delete Portal" class="text-light-d delete" id="delete_portal" data-id="<?php echo $portal['id']; ?>">
                                                            <i class="fas fa-lg fa-trash text-danger cursor-pointer"></i>
                                                        </a>
                                                    
                                                <?php } ?>  
                                                </td>   									
    										</tr>
    									<?php } ?>
    								</tbody>
    							</table>
    						</div>
    					</div>

    					<div id="tabs-mynumbers" class="tab-pane">
    						<div class="table-responsive">
    							<table class="table" id="callFireRouterTable">
    								<thead class="thead-dark">
    									<tr>
    										<th>Status</th>
    										<th>Description</th>
    										<th>Campaign Id</th>
    										<th>Tracking Number</th>
    										<th>Terminating Number</th>
    										<th class="nowrap">Actions</th>
    									</tr>
    								</thead>
    								<tbody>
    									<?php

    									$callFireRoutings = $campaigns_info->getCallFireRouting();
    									if(!$callFireRoutings['is_error']){
    										$callFireRoutings  = $callFireRoutings['data'];
    										foreach($callFireRoutings as $callFireRouting){
    											$portal = $campaigns_info->getPortals($callFireRouting['campid']);  										
    											
    											?>
    											<tr>
    												<td><?php echo ($callFireRouting['inactive'])? 'Inactive':'Active'; ?></td>
    												<td>
    													<?php 
    														echo isset($portal[0]['name']) ? $portal[0]['name'] : 'N/A';
    													?>
    												</td>
    												<td><?php echo $callFireRouting['campid']; ?></td>
    												<td><?php echo $callFireRouting['phone']; ?></td>
    												<td><?php echo $callFireRouting['terminatingnum']; ?></td>
    												<td >
    													<?php if(roleHasPermission('show_actions_ongoing_campaigns', $_SESSION['role_permissions']) && (!$callFireRouting['inactive']) ){  ?> 
    														<button type="button" id="idUpdateCTN" class="btn btn-sm text-white bg-blue text-uppercase" data-id="<?php echo $callFireRouting['id']; ?>">Edit</button>&nbsp;&nbsp;
    														<button type="button" id="idDeleteCTN" class="btn btn-sm text-white bg-blue text-uppercase" data-id="<?php echo $callFireRouting['id']; ?>">Delete</button>&nbsp;&nbsp;
    													<? } ?>
    												</td>

    											</tr>
    										<?php } }else{?>	
    											<tr>
    												<td></td>
    												<td></td>
    												<td></td>
    												<td></td>
    											</tr>
    										<?php } ?>	
    									</tbody>
    								</table>
    							</div>
    						</div>

    						<div id="tabs-endedcamp" class="tab-pane">
    							<div class="table-responsive">
    								<table class="table" id="campaignEndDataTable">
    									<thead class="thead-dark">
    										<tr>
    											<th>Portal</th>
    											<th>Campaign Name</th>
    											<th>Start Date</th>
    											<th>End Date</th>
    											<th>Budget</th>
    											<th>Payment Period</th>
    											<th>Notes</th>
    										</tr>
    									</thead>
    									<tbody>
    										<?php
    										$campaignsEnd = $campaigns_info->getCampaignsEnd();
    										foreach($campaignsEnd as $campaign){ ?>
    											<tr>
    												<td><?php echo $campaign['portal']; ?></td>
    												<td><?php echo $campaign['campaign_name']; ?></td>
    												<td><?php echo date("m/d/Y",strtotime($campaign['start_date'])); ?></td>
    												<td><?php echo $campaign['end_date'] > 0 ? date("m/d/Y",strtotime($campaign['end_date'])) : ""; ?></td>
    												<td><?php echo $campaign['budget']; ?></td>
    												<td><?php echo $campaign['payment_period']; ?></td>
    												<td><?php echo preg_replace('/\v+|\\\r\\\n/Ui','<br/>',$campaign['notes']); ?></td>
    											</tr>
    										<?php } ?>
    									</tbody>
    								</table>
    							</div>
    						</div>

    						<div id="tabs-ongoingcamp" class="tab-pane">
    							<div class="table-responsive">
    								<table class="table" id="campaignDataTable">
    									<thead class="thead-dark">
    										<tr>
    											<th>Portal</th>
    											<th>Campaign Name</th>
    											<th>Start Date</th>
    											<th>End Date</th>
    											<th>Budget</th>
    											<th>Payment Period</th>
    											<th>Notes</th>
    											<?php if(roleHasPermission('show_actions_ongoing_campaigns', $_SESSION['role_permissions'])){ ?>
    												<th class="nowrap">Actions</th>
    											<? } ?>
    										</tr>
    									</thead>
    									<tbody>
    										<?php
    										$campaigns = $campaigns_info->getCampaignsOn();
    										foreach($campaigns as $campaign){
    											?>
    											<tr>
    												<td><?php echo $campaign['portal']; ?></td>
    												<td><?php echo $campaign['campaign_name']; ?></td>
    												<td><?php echo date("m/d/Y",strtotime($campaign['start_date'])); ?></td>
    												<td><?php echo $campaign['end_date'] > 0 ? date("m/d/Y",strtotime($campaign['end_date'])) : ""; ?></td>
    												<td><?php echo $campaign['budget']; ?></td>
    												<td><?php echo $campaign['payment_period']; ?></td>
    												<td><?php echo preg_replace('/\v+|\\\r\\\n/Ui','<br/>',$campaign['notes']); ?></td>
    												<?php if(roleHasPermission('show_actions_ongoing_campaigns', $_SESSION['role_permissions'])){  ?> 
    													<td class="nowrap">
    														<button type="button" class="btn btn-sm text-white bg-blue text-uppercase" data-toggle="modal" data-target="#editModal" data-id="<?php echo $campaign['id']; ?>">Edit</button>&nbsp;&nbsp;
    														<a href="" title="Delete Campaing" class="text-light-d ml-2 delete" id="delete_campaign" data-id="<?php echo $campaign['id']; ?>"><i class="fas fa-lg fa-trash text-danger cursor-pointer"></i></a></td><? } ?>
    													</tr>
    												<?php } ?>
    											</tbody>
    										</table>
    									</div>
    								</div>
    							</div>
    						</div>

    						<!-- Add Campaign Info form-->
    						<form action="xt_campaign.php" method="POST" name="addModal">
    							<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalTitle" aria-hidden="true">
    								<div class="modal-dialog modal-dialog-centered" role="document">
    									<div class="modal-content">
    										<div class="modal-header">
    											<h5 class="modal-title" id="uploadModalTitle">Add Campaign</h5>
    											<button type="button" class="close" data-dismiss="modal" aria-label="Close">
    												<span aria-hidden="true">&times;</span>
    											</button>
    										</div>
    										<div class="modal-body">
    											<div class="form-group">
    												<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Portal</label>
    												<select name="portal" id="portalCamp" class="form-control rounded-bottom rounded-right custom-select-arrow" required>
    													<option value="">Select</option>
    													<?php
    													$portals = $campaigns_info->getPortals();

    													foreach($portals as $portal) {?>
    														<option data-campid = "<?php echo $portal['campid']; ?>" value="<?php echo $portal['name']; ?>"><?php echo $portal['name']; ?></option>
    													<?php } ?>
    												</select>
    											</div>
    											<div class="form-group">
    												<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Campaign Name</label>
    												<input type="text" name="campaign_name" id="campaign_name" class="form-control" readonly />
    											</div>
    											<div class="form-group">
    												<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Campaign Id</label>
    												<input type="text" name="campid" id="campid" class="form-control" readonly />
    											</div>
    											<div class="form-group">
    												<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Start Date<span class="text-danger">*</span></label>
    												<input type="text" name="start_date" class="form-control datepicker" required />
    											</div>
    											<div class="form-group">
    												<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">End Date</label>
    												<input type="text" name="end_date" class="form-control datepicker" />
    											</div>

    											<div class="form-group">
    												<div class="form-row">
    													<div class="col">
    														<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">budget</label>
    														<input type="text" pattern="[0-9]+(\.[0-9]{1,2})?%?" name="budget" id="budget" class="form-control" required />
    													</div>
    													<div class="col">
    														<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Payment Period</label>
    														<select name="payment_period" class="form-control rounded-bottom rounded-right custom-select-arrow" required>
    															<?php
    															$payPeriodTypes = $campaigns_info->getPayPeriodType();																					
    															foreach($payPeriodTypes as $payPeriodType) {
    																$selected = ($payPeriodType['unique_name'] == 'monthly')? 'selected':'';
    																?>
    																<option <?php echo $selected;?> value="<?php echo $payPeriodType['unique_name']; ?>"><?php echo $payPeriodType['name']; ?></option>
    															<?php } ?>
    														</select>
    													</div>
    												</div>
    											</div>
    											<div class="form-group">
    												<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Notes</label>
    												<textarea name="notes" placeholder="Notes" class="form-control"></textarea>
    											</div>
    										</div>
    										<div class="modal-footer">
    											<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
    											<input type="submit" class="btn bg-blue text-white btn-sm" value="Save changes" name="submitBtnAddCampaing">
    										</div>

    									</div>
    								</div>
    							</div>
    						</form>
    						<!-- End Add Campaign Info modal form-->

    						<!-- Change CTN Info form-->
    						<form action="xt_addPortal.php" method="POST" name="editCTN">
    							<div class="modal" id="editCTNModal" tabindex="-1" role="dialog">
    								<div class="modal-dialog" role="document">
    									<div class="modal-content">
    										<div class="modal-header">
    											<h5 class="modal-title">Modal title</h5>
    											<button type="button" class="close" data-dismiss="modal" aria-label="Close">
    												<span aria-hidden="true">&times;</span>
    											</button>
    										</div>
    										<div class="modal-body">
    										</div>
    										<div class="modal-footer">
    											<input type="submit" class="btn bg-blue text-white btn-sm" value="Save Changes" name="submitBtnEditCTN">
    											<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
    										</div>
    									</div>
    								</div>
    							</div>
    						</form>
    						<!-- End CTN Info modal form-->

                             <!-- Edit Portal Info form-->
                            <form action="xt_addPortal.php" method="POST" name="updatePortal">
                                <div class="modal" id="updatePortalModal" tabindex="-1" role="dialog">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Update Portal</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                            </div>
                                            <div class="modal-footer">
                                                <input type="submit" class="btn bg-blue text-white btn-sm" value="Save" name="submitBtnUpdatePortal">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <!-- End Edit Portal Info form-->

    						<!-- Change CTN Info form-->
    						<form action="xt_addPortal.php" method="POST" name="changeCTN">
    							<div class="modal" id="changeCTNModal" tabindex="-1" role="dialog">
    								<div class="modal-dialog" role="document">
    									<div class="modal-content">
    										<div class="modal-header">
    											<h5 class="modal-title">Change CTN</h5>
    											<button type="button" class="close" data-dismiss="modal" aria-label="Close">
    												<span aria-hidden="true">&times;</span>
    											</button>
    										</div>
    										<div class="modal-body">
    										</div>
    										<div class="modal-footer">
    											<input type="submit" class="btn bg-blue text-white btn-sm" value="Save Changes" name="submitBtnEditCTN">
    											<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
    										</div>
    									</div>
    								</div>
    							</div>
    						</form>
    						<!-- End CTN Info modal form-->

    						<!-- Add Portal form-->
    						<form action="xt_addPortal.php" method="POST" name="addPortalInfo">
    							<div class="modal fade" id="addPortalModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalTitle" aria-hidden="true">
    								<div class="modal-dialog modal-dialog-centered" role="document">
    									<div class="modal-content">
    										<div class="modal-header">
    											<h5 class="modal-title" id="uploadModalTitle">Add Portal</h5>
    											<button type="button" class="close" data-dismiss="modal" aria-label="Close">
    												<span aria-hidden="true">&times;</span>
    											</button>
    										</div>
    										<div class="modal-body">
    											<div class="form-group">
    												<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Description</label>
    												<input type="text" pattern="[a-z A-Z:-_]+?" name="name" id="namePortalAdd" class="form-control" required />
    											</div>
    											<div class="form-group">
    												<div class="form-row">								   
    													<div class="col">
    														<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Campaign ID</label>
    														<input type="text" pattern="[0-9]+?" name="campid" id="input" class="form-control"  oninput="myFunction()" required />
    													</div>
    													<div class="col">
                                                            <label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Channel</label>
                                                            <select name="channel" id="utm_channel" class="form-control rounded-bottom rounded-right custom-select-arrow" required>
                                                                <option  value="">Select Channel</option>
                                                                <?php
                                                                    $channels = $campaigns_info->getChannels();
                                                                    foreach($channels as $channel) {                                                
                                                                ?>
                                                                    <option data-id = "<?php echo $channel['id']; ?>" value="<?php echo $channel['name']; ?>">
                                                                        <?php echo $channel['name']; ?>
                                                                            
                                                                        </option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
    												</div>
    											</div>

                                                <div class="form-group">
                                                    <div class="form-row">
                                                        <div class="col">
                                                            <label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Source</label>
                                                            <input type="text" name="source" id="source" class="form-control" required />
                                                        </div>

                                                        <div class="col">
                                                            <label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Medium</label>
                                                            <select name="medium" id="utm_medium" class="form-control rounded-bottom rounded-right custom-select-arrow" disabled required>
                                                                <option  value="">Select Medium</option>
                                                                <?php
                                                                $mediums = $campaigns_info->getMedium();                                                                                    
                                                                foreach($mediums as $medium) { ?>
                                                                    <option value="<?php echo $medium['name']; ?>">
                                                                        <?php echo $medium['name']; ?>
                                                                    </option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>


    											<div class="form-group">
    												<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">MarkUp</label>
    												<div class="input-group mb-3">

    													<input type="text" pattern="[0-9]+(\.[0-9]{1,2})?%?" name="markup" id="markupPortalAdd" class="form-control" required />
    													<div class="input-group-append">
    														<span class="input-group-text" id="basic-addon2">%</span>
    													</div>
    												</div>
    											</div>
    											<div class="form-group">
    												<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Notes</label>
    												<textarea name="notes" placeholder="Notes" class="form-control"></textarea>
    											</div>



    											<!-- Create CTN -->
    											<div class="form-group">
    												<input type="checkbox" id="createCTN" name="createCTN" >
    												<label for="createCTN" class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Create CTN</label>
    											</div>
    											<div class="CTNOptions">
    												<div class="form-group">
    													<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Client</label>
    													<select name="client" id="company" class="form-control rounded-bottom rounded-right custom-select-arrow">
    														<option value="">Select Client</option>									
    													</select>
    												</div>


    												<div class="form-group">
    													<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Number Name</label>
    													<input type="text" value="<?php echo CLIENT_NAME; ?> {<?php echo $_SESSION['client']; ?>} " name="numberName" id="numberNamePortalAdd" class="form-control"  />
    												</div>

    												<div class="form-group">
    													<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Forward Calls To</label>
    													<select name="country" id="country" class="form-control rounded-bottom rounded-right custom-select-arrow mb-1" >
    														<option  value="">Select Country</option>
    														<option value="+1" selected >US & Canada</option>
    														<option value="+44">Australia</option>
    														<option value="+66">United Kingdom</option>
    													</select>
    													<div class="input-group mb-3">
    														<div class="input-group-append">
    															<span class="input-group-text rounded-left country-code" id="basic-addon2">+1</span>
    														</div>
    														<input type="phone" name="forward" id="forwardPortalAdd" class="form-control" />
    													</div>
    												</div>

    												<div class="form-group">
    													<div class="form-row">
    														<div class="col">
    															<label class="font-11 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Diff AreaCode</label>
    															<input type="checkbox" name="forwardAreacodeToggle" id="forwardAreacodeToggle" data-toggle="toggle" data-size="sm">
    															<div id="forwardAreacodeDiv" style="display: none;" class="mt-1">
    																<input type="tel" name="forwardAreacode" id="forwardAreacode" class="form-control" size="3" pattern="\d{3}" title="3-digit area code" />
    															</div>
    														</div>

    														<div class="col">
    															<label class="font-11 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Recording</label>
    															<input type="checkbox" name="call_recording" id="call_recording" checked data-toggle="toggle" data-size="sm">
    														</div>
    														<div class="col">
    															<label class="font-11 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">SMS</label>
    															<input type="checkbox" name="sms_enabled" id="sms_enabled" data-toggle="toggle" data-size="sm">
    														</div>								
    													</div>
    												</div>

    												<div class="form-group">
    													<div class="form-row">	
    														<div class="col">
    															<label class="font-11 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Toll Free</label>
    															<input type="checkbox" name="is_tollfree" id="is_tollfree" data-toggle="toggle" data-size="sm">
    															<div id="tollFreeDiv" class="mt-1">
    																<select name="tollFreeAreaCode" id="tollFreeAreaCode" class="form-control rounded-bottom rounded-right custom-select-arrow mb-1" >
    																	<option value="">Select Area Code</option>
    																	<option value="800">800</option>
    																	<option value="888">888</option>
    																	<option value="877">877</option>
    																	<option value="866">866</option>
    																	<option value="855">855</option>
    																	<option value="844">844</option>
    																	<option value="833">833</option>
    																</select>
    															</div>
    														</div>
    														<div class="col">
    															<label class="font-11 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Greeting</label>
    															<input type="checkbox" name="call_greeting" id="greetingToggle" checked data-toggle="toggle" data-size="sm">
    															<div id="greetingMessage" class="mt-1">
    																<input type="text" name="greetingrMessage" id="greetingrMessagePortalAdd" value="This call will be recorded for quality assurance" placeholder="Message" class="form-control" />
    															</div>
    														</div>
    														<div class="col">
    															<label class="font-11 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Whisper</label>
    															<input type="checkbox" name="whisper_message" data-toggle="toggle" id="whisperToggle" data-size="sm">
    															<div id="whisperMessage" class="mt-1">
    																<input type="text" name="whisperMessage" id="whisperMessagePortalAdd" placeholder="Message" class="form-control" />
    															</div>
    														</div>
    													</div>
    												</div>

    											</div>
    											<!-- /Create CTN -->							
    											<div class="modal-footer">
    												<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
    												<input type="submit" class="btn bg-blue text-white btn-sm" value="Save Changes" name="submitBtnAddPortalInfo">
    											</div>
    										</div>
    									</div>
    								</div>
    							</form>
    							<!-- End Add Portal modal form-->			
    							
    						</main>
    					</div>
    				</div>
<!-- Change Campaign Info form-->
            <form action="xt_campaign.php" method="POST" name="editCampaignInfo">
                <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalTitle" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="uploadModalTitle">Edit Campaign</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                                <input type="submit" class="btn bg-blue text-white btn-sm" value="Save Changes" name="submitBtnEditCampaing">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <!-- End Campaign Info modal form-->

    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>
	<script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
    <script type="text/javascript" src="//cdn.datatables.net/plug-ins/1.10.13/sorting/datetime-moment.js"></script>
	<script src="//cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script src="//cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
	<script src="//cdn.datatables.net/buttons/1.5.6/js/buttons.bootstrap4.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
	<script src="//cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
	<script src="//cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>
	<script src="//cdn.datatables.net/buttons/1.5.6/js/buttons.colVis.min.js "></script>
	<script src="//cdn.datatables.net/select/1.2.2/js/dataTables.select.min.js"></script>
	<script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>
	<scirpt src="https://ajax.googleapis.com/ajax/libs/angularjs/1.7.8/angular.min.js"></scirpt>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/inputmask/4.0.9/jquery.inputmask.bundle.min.js"></script>
	
	<script>
		
		$(document).ready( function () {
			$("#forwardPortalAdd").inputmask({"mask": "(999) 999-9999"});
			$( ".datepicker" ).datepicker();

			$('#campaignEndDataTable').DataTable({
				"order": [[ 2, "desc" ]],
				responsive: true,
			});

			$('#callFireRouterTable').DataTable({
				"order": [[ 2, "desc" ]],
				responsive: true,
			});

			$('#campaignDataTable').DataTable({
				"order": [[ 2, "desc" ]],
				responsive: true,
			});

			$('#campaignIdDataTable').DataTable({
				"order": [[ 0, "desc" ]],
				responsive: true,
			});
		} );
		
		// if checkbox checked show CreateCTN

		$(document).on('click','#createCTN',function(e){
			$(".CTNOptions").slideToggle();
			
			if($(this).is(':checked')){

				ctnInfoChange('#addPortalModal');
			}else{
				addRequired($('#addPortalModal #company'),false);
				addRequired($('#addPortalModal #country'),false);
				addRequired($('#addPortalModal #forwardPortalAdd'),false);
				addRequired($('#addPortalModal #numberNamePortalAdd'),false);
			}
			
		});

		function ctnInfoChange($parentId){
			addRequired($($parentId+' #company'),true);
			addRequired($($parentId+' #country'),true);
			addRequired($($parentId+' #forwardPortalAdd'),true);
			addRequired($($parentId+' #numberNamePortalAdd'),true);

			$.ajax({
		        type: 'POST',
		        url: 'xt_getCompany.php',
		        data: {
		            client:<?php echo $_SESSION['client'];?>
		        },
		        success: function(data) {
		        	var parsed_data = JSON.parse(data);
		            var company = $($parentId+' #company');

		            company.empty();
		            company.append('<option value="" selected >Select Client</option>');
		            for (var i = 0; i < parsed_data.count; i++) {
		                company.append('<option value=' + parsed_data.data[i].companyid + '>' + parsed_data.data[i].name + '</option>');
		            }
		        }

		    });
		}

		function addRequired(JQelement,value){
		 	JQelement.prop('required',value);
		 }

	
		$(document).on('click','#idUpdateCTN',function(e){
			
			var id = $(this).data("id");
			var self =$('#editCTNModal').find(".modal-body");
			$.ajax({
                url: "xt_updateCTN.php", 
                type:"POST",
                data:{"id":id},
                success: function(result){
                    $(self).html(result);
                    $(self).find( "#forwardPortalAdd" ).inputmask({"mask": "(999) 999-9999"});
                    $('#editCTNModal').modal('show');
                }
            });
		});
		
			 $(document).on('click','#tracking_url',function(e){
            e.preventDefault();
            var id = $(this).data("id");
            $.ajax({
                url: "xt_getTrackingUrl.php", 
                type:"POST",
                data:{ "id":id },
                success: function(result){
                    $('#tracking_url_save').val(result); 

                    //Do it
                    url =copyToClipboard('#tracking_url_save');
                    if(copyToClipboard('#tracking_url_save')) {
                        alert('Tracking Url copied '+url);
                    } else {
                        alert('Tracking Url failed');
                    }
                }
            });
        });

        function copyToClipboard(elem) {
            var $temp = $("<textarea>");
            $("body").append($temp);
            $temp.val($(elem).val() ).select();
         


            var succeed;
            try {
                succeed = document.execCommand("copy");
            } catch(e) {
                succeed = false;
            }
            $temp.remove();
            return $(elem).val();
    } 

        $(document).on('click','#update_portal',function(e){
            e.preventDefault();
            var id = $(this).data("id");
            var self =$('#updatePortalModal').find(".modal-body");
            
            $.ajax({
                url: "getCampIdInformation.php", 
                type:"POST",
                data:{ "id":id },
                success: function(result){
                    $(self).html(result);
                    $('#updatePortalModal').modal('show');
                }
            });
        });

		$(document).on('click','#change_ctn',function(e){
			e.preventDefault();
			var id = $(this).data("id");
			var self =$('#changeCTNModal').find(".modal-body");
			$.ajax({
                url: "xt_changeCTN.php", 
                type:"POST",
                data:{ "id":id },
                success: function(result){
                    $(self).html(result);
                    $(self).find( "#forwardPortalAdd" ).inputmask({"mask": "(999) 999-9999"});
                    $('#changeCTNModal').modal('show');
                    ctnInfoChange('#changeCTNModal');
                   // $('#createCTN').click();
                }
            });
		});

		// add campid to number name value	
		input.oninput = function() {
			numberNamePortalAdd.value = "<?php echo CLIENT_NAME; ?> {<?php echo $_SESSION['client']; ?>}[" + input.value + "]";
		};

        $(document).on('change','#utm_channel',function(e) {
            e.preventDefault();
            var selected = $(this).find('option:selected');
            var id = selected.data('id');           

            $.ajax({
                url: "xt_getMedium.php", 
                type:"POST",
                data:{ "id":id },
                success: function(result){
                    $('#utm_medium').html(result);
                    $('#utm_medium').prop("disabled", false); 
                }
            });
        });
		
		// if checkbox checked show Whisper Message input box
		$(document).on('change','#whisperToggle',function() {
		  $('#whisperMessage').slideToggle();
		  	if($('#whisperToggle').is(':checked')){
				addRequired($('#whisperMessagePortalAdd'),true);
			}else{
				addRequired($('#whisperMessagePortalAdd'),false);
				$('#whisperMessagePortalAdd').val('');
			}
		});

		// if checkbox checked show Whisper Message input box
		$(document).on('change','#is_tollfree',function() {
		  $('#tollFreeDiv').slideToggle();
		  	if($('#is_tollfree').is(':checked')){
				addRequired($('#tollFreeAreaCode'),true);
				$('#tollFreeAreaCode option:eq(0)').attr('selected','selected'); 
			}else{
				addRequired($('#tollFreeAreaCode'),false);
				$('#tollFreeAreaCode option:eq(0)').prop('selected','selected'); 
			}
		});

		// if checkbox checked show AreaCode input box
		$(document).on('change','#forwardAreacodeToggle',function() {
		  $('#forwardAreacodeDiv').slideToggle();
		  	if($('#forwardAreacodeToggle').is(':checked')){
				addRequired($('#forwardAreacode'),true);
			}else{
				addRequired($('#forwardAreacode'),false);
				$('#forwardAreacode').val('');				
			}
		});

		// if checkbox checked show Greeting Message input box
		$(document).on('change','#greetingToggle',function() {
		  $('#greetingMessage').slideToggle();
		  	if($('#greetingToggle').is(':checked')){
				addRequired($('#greetingrMessagePortalAdd'),true);
				$('#greetingrMessagePortalAdd').val('This call will be recorded for quality assurance');
			}else{
				addRequired($('#greetingrMessagePortalAdd'),false);
				$('#greetingrMessagePortalAdd').val('');				
			}
		});

		
		// select country code
		$(document).on('change','select#country',function() {
			var code="";
			$("select#country option:selected").each(function(){
				code += $(this).val();				
			});
			$(".country-code").text(code);
		}).trigger("change");
		
		// phone mask
		$("#forwardPortalAdd").inputmask({"mask": "(999) 999-9999"});
		

		$('#portalCamp').on('change', function(e) {
			self = $('option:selected',this);			
			$('#campaign_name').val(self.val() );
			$('#campid').val(self.data("campid"));
		});
				
		$(document).on("click",'#delete_campaign',function(e) {
			e.preventDefault();
			var id = $(this).data("id");
			if(confirm("Are you sure you want to delete this campaign?")){
				window.location.href = "xt_campaign.php?action=delete&id="+id;
			}
		});

		$(document).on("click",'#delete_portal',function(e) {
			e.preventDefault();
			var id = $(this).data("id");
			if(confirm("Are you sure you want to delete this Portal?")){
				window.location.href = "xt_addPortal.php?action=delete&id="+id;
			}
		});

		$(document).on("click",'#idDeleteCTN',function(e){
			
			e.preventDefault();
			var id = $(this).data("id");
			if(confirm("Are you sure you want to delete this CTN?")){
				window.location.href = "xt_addPortal.php?action=deleteCTN&id="+id;
			}
		});

		/*$(document).on("click",'#change_ctn',function(e) {
			e.preventDefault();
			var id = $(this).data("id");
			if(confirm("Are you sure you want to change the CTN?")){
				window.location.href = "xt_addPortal.php?action=change_ctn&id="+id;
			}
		});*/

		$(document).on('show.bs.modal','#editModal', function (e) {

			var target = $(this).find(".modal-body");
			var id = $(e.relatedTarget).data("id");
			$.ajax({
                url: "get_campaign.php", 
                type:"POST",
                data:{"id":id},
                success: function(result){
                    $(target).html(result);
					$(target).find( ".datepicker" ).datepicker();
                }
            });
		});


		if($( window ).width()<992){
			$('.collapse').collapse('hide')
		}
		$( window ).resize(function() {
			if($( window ).width()<992){
				$('.collapse').collapse('hide')
			}else{
				$('.collapse').collapse('show')
			}
		});
	</script>
  </body>
</html>