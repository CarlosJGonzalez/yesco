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
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <title>Promote | <?php echo CLIENT_NAME; ?></title>
	  
  </head>
  <body class="bg-light cbp-spmenu-push">
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/top.php"); ?>

    <div class="container-fluid">
      <div class="row">
        <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/nav.php"); ?>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 p-0 mb-4">
			<div class="p-0 border-bottom mb-4">
				<div class="breadcrumbs bg-white px-3 py-1 border-bottom small">
					<a href="/promote-cc/" class="text-muted">Promote</a>
					<span class="mx-1">&rsaquo;</span>
					<span class="font-weight-bold text-muted">Email Report</span>
				</div>
				<div class="d-flex d-block align-items-center clearfix py-2 px-4">
					<h1 class="h2 font-light mb-0 text-center text-sm-left"><i class="fas fa-paper-plane mr-2"></i> Email Report</h1>
				</div>
			</div>
			
        	<div class="px-4 py-3">
				<?php 
				$campaign_id_url = $_GET['id'];
				
				$db->where("campaign_id",$_GET['id']);
				$campaign = $db->getOne("promote_campaigns");
				
				if($db->count>0){					
					$campaign_details = $cc->getCampaign($campaign['campaign_id']);
					$previewOfEmailCampaign = $cc->previewOfEmailCampaign($campaign['campaign_id']);
				?>

				<div class="row">
				
					<div class="col-sm-6">
						<div class="border rounded p-2 mb-3">
							<span class="text-blue text-uppercase font-weight-bold"><?php echo $campaign_details['status']; ?></span>
							<span class="text-muted"><?php echo date('M d, o H:i:s',strtotime($campaign_details['modified_date'])); ?></span>
						</div>
					
						<iframe src="<?=LOCAL_CLIENT_URL."/promote-cc/preview-content.php?id=".$campaign['campaign_id']?>" style="width: 100%; height: 800px;"></iframe>
					</div>

					<div class="col-sm-6">
					
						<div class="border rounded p-2 mb-3">
							<div class="d-flex justify-content-around align-items-center">
								<span class="text-muted text-uppercase d-block">SUCCESSFUL DELIVERIES</span>
								<span class="h4 text-blue d-block mb-1 font-weight-bold"><?php echo $campaign_details['tracking_summary']['sends']; ?></span>
									 
							</div>
						</div>
						
						<div class="bg-light mb-3">
							<?php
								$open_rate = ($campaign_details['tracking_summary']['sends'] != 0) ? round(($campaign_details['tracking_summary']['opens']/$campaign_details['tracking_summary']['sends'])*100,2) : 0;
								$clik_rate = ($campaign_details['tracking_summary']['sends'] != 0) ? round(($campaign_details['tracking_summary']['clicks']/$campaign_details['tracking_summary']['sends'])*100,2) : 0;
							?>
							<div class="row align-items-center">
								<div class="col-2">
									 <span class="text-muted text-uppercase d-block">OPEN RATE</span>
								</div>
								<div class="col-10">
									<div class="progressbar" data-val="<?php echo $open_rate; ?>"><span class="small position-absolute <?php if($open_rate>0){ ?>text-white<?php } ?> ml-1 mt-1"><?php echo $open_rate; ?>%</span></div>
								</div>
							</div>
						</div>
						<div class="bg-light mb-3">
							<div class="row align-items-center">
								<div class="col-2">
									 <span class="text-muted text-uppercase d-block">CLICK RATE</span>
								</div>
								<div class="col-10">
									<div class="progressbar" data-val="<?php echo $clik_rate;?>"><span class="small position-absolute <?php if($clik_rate>0){ ?>text-white<?php } ?> mr-1 mt-1"><?php echo $clik_rate;?>%</span></div>
								</div>
							</div>
						</div>
					
						<div class="border rounded p-2 mb-3">
							<div class="d-flex justify-content-between">
								<div class="text-center">
									 <span class="h4 text-blue d-block mb-1 font-weight-bold"><?php echo $campaign_details['tracking_summary']['opens'];?></span>
									 <span class="text-muted text-uppercase d-block">OPENED</span>
								</div>
								<div class="text-center">
									 <span class="h4 text-blue d-block mb-1 font-weight-bold"><?php echo $campaign_details['tracking_summary']['clicks'];?></span>
									 <span class="text-muted text-uppercase d-block">CLICKED</span>
								</div>
								<div class="text-center">
									 <span class="h4 text-blue d-block mb-1 font-weight-bold"><?php echo $campaign_details['tracking_summary']['bounces'];?></span>
									 <span class="text-muted text-uppercase d-block">BOUNCED</span>
								</div>
								<div class="text-center">
									 <span class="h4 text-blue d-block mb-1 font-weight-bold"><?php echo $campaign_details['tracking_summary']['unsubscribes'];?></span>
									 <span class="text-muted text-uppercase d-block">UNSUBSCRIBED</span>
								</div>
							</div>
						</div>
						
						<!--<div class="border rounded p-2 mb-3">
							<div class="row align-items-center">
								<div class="col-md-6">
									<div class="float-left">
										<span class="fa-stack fa-2x">
										  <i class="fas fa-circle fa-stack-2x"></i>
										  <i class="far fa-envelope-open fa-stack-1x fa-inverse"></i>
										</span>
									</div>
									<div class="d-inline-block">
										<p class="text-muted text-uppercase mb-1"><span class="text-blue font-weight-bold">TOTAL OPENED:</span> <?php //echo $campaign_report['opens']['opens_total'];?></p>
										<p class="text-muted text-uppercase mb-0"><span class="text-blue font-weight-bold">LAST OPENED:</span> <?php //if (!empty($campaign_report['opens']['last_open'])) echo date("m/d/Y g:i A",strtotime($campaign_report['opens']['last_open'])); else echo 'N/A';?></p>
									</div>
								</div>
								<div class="col-md-6">
									<div class="float-left">
										<span class="fa-stack fa-2x">
										  <i class="fas fa-circle fa-stack-2x"></i>
										  <i class="far fa-hand-pointer fa-stack-1x fa-inverse"></i>
										</span>
									</div>
									<div class="d-inline-block">
										<p class="text-muted text-uppercase mb-1"><span class="text-blue font-weight-bold">TOTAL CLICKS:</span> <?php //echo $campaign_report['clicks']['clicks_total']; ?></p>
										<p class="text-muted text-uppercase mb-0"><span class="text-blue font-weight-bold">LAST CLICKED:</span> <?php //if (!empty($campaign_report['clicks']['last_click'])) echo date("m/d/Y g:i A",strtotime($campaign_report['clicks']['last_click'])); else echo 'N/A';?></p>
									</div>
									
								</div>
							</div>
						</div>-->
						
					</div>

				</div>
				
				<?php
				}else{ ?>
					<p class="text-muted font-italic">The campaign does not exist. Try another.</p>
				<?php } ?>
			</div>
        </main>
      </div>
    </div>
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>
	    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script>
		$( ".progressbar" ).each(function() {
			var val = $( this ).data( "val" );
			$(this).progressbar({
			  value: val,
				classes: {
					"ui-progressbar-value": "bg-blue"
				  }
			});
		  
		});
	  </script>
  </body>
</html>