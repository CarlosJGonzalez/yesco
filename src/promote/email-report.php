<!doctype html>
<html lang="en">
  <head>
    <?php 
	include ($_SERVER['DOCUMENT_ROOT']."/includes/head.php");
	include ($_SERVER['DOCUMENT_ROOT']."/includes/ClassDasMC.php");
	
	if(!(roleHasPermission('show_promote_link', $_SESSION['role_permissions']))){
		$_SESSION['error'] = "Sorry! You must be authorized to see this page.";
		header('location: /');
		exit;
	}

	if(empty($active_location['loyalty_promotions_key'])){
		$_SESSION['error'] = "Please enter a key.";
		header('location: /settings/promote/');
		exit;
	}else{
		$mc_api_key = $active_location['loyalty_promotions_key'];
	}

	$mc = new Das_MC($mc_api_key);
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
					<a href="/promote/" class="text-muted">Promote</a>
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
				$campaign = $db->getOne("mailchimp_campaigns");

				if($db->count>0){					
					$campaign_details = $mc->getCampaign($campaign['campaign_id']);
					$campaign_report = $mc->getReport($campaign['campaign_id']);
					$template_id = $campaign_details['settings']['template_id'];
					$template = $mc->getTemplate($template_id);
					?>

				<div class="row">
				
					<div class="col-sm-6">
						<div class="border rounded p-2 mb-3">
							<span class="text-blue text-uppercase font-weight-bold"><?php echo $campaign_details['status']; ?></span>
							<?php 
								if (!empty($campaign_report['send_time'])){
							?>
								<span class="text-muted"><?php echo date('M d, o H:i:s',strtotime($campaign_report['send_time'])); ?></span>
							<?php }else{ ?>
								<span class="text-muted"><?php echo date('M d, o H:i:s',strtotime($campaign_details['create_time'])); ?></span>
							<?php	
								} 
							?>
							
							
						</div>
					
						<iframe src="<?=$campaign_details['long_archive_url']?>" style="width: 100%; height: 800px;"></iframe>
					</div>

					<div class="col-sm-6">
					
						<div class="border rounded p-2 mb-3">
							<div class="d-flex justify-content-around align-items-center">
								<span class="text-muted text-uppercase d-block">SUCCESSFUL DELIVERIES</span>
								<span class="h4 text-blue d-block mb-1 font-weight-bold"><?php echo $campaign_report['emails_sent']; ?></span>
									 
							</div>
						</div>
						
						<div class="bg-light mb-3">
							<div class="row align-items-center">
								<div class="col-2">
									 <span class="text-muted text-uppercase d-block">OPEN RATE</span>
								</div>
								<div class="col-10">
									<?php $amount = round(($campaign_report['opens']['unique_opens']/$campaign_report['emails_sent'])*100,2); ?>
									<div class="progressbar" data-val="<?php echo $amount; ?>"><span class="small position-absolute <?php if($amount>0){ ?>text-white<?php } ?> ml-1 mt-1"><?php echo $amount; ?>%</span></div>
								</div>
							</div>
						</div>
						<div class="bg-light mb-3">
							<div class="row align-items-center">
								<div class="col-2">
									 <span class="text-muted text-uppercase d-block">CLICK RATE</span>
								</div>
								<div class="col-10">
									<?php $amount = round(($campaign_report['clicks']['unique_clicks']/$campaign_report['emails_sent'])*100,2); ?>
									<div class="progressbar" data-val="<?php echo $amount;?>"><span class="small position-absolute <?php if($amount>0){ ?>text-white<?php } ?> mr-1 mt-1"><?php echo $amount;?>%</span></div>
								</div>
							</div>
						</div>
					
						<div class="border rounded p-2 mb-3">
							<div class="d-flex justify-content-between">
								<div class="text-center">
									 <span class="h4 text-blue d-block mb-1 font-weight-bold"><?php echo $campaign_report['opens']['unique_opens'];?>%</span>
									 <span class="text-muted text-uppercase d-block">OPENED</span>
								</div>
								<div class="text-center">
									 <span class="h4 text-blue d-block mb-1 font-weight-bold"><?php echo $campaign_report['clicks']['unique_clicks'];?></span>
									 <span class="text-muted text-uppercase d-block">CLICKED</span>
								</div>
								<div class="text-center">
									 <span class="h4 text-blue d-block mb-1 font-weight-bold"><?php echo $campaign_report['bounces']['soft_bounces'];?></span>
									 <span class="text-muted text-uppercase d-block">BOUNCED</span>
								</div>
								<div class="text-center">
									 <span class="h4 text-blue d-block mb-1 font-weight-bold"><?php echo $campaign_report['unsubscribed'];?></span>
									 <span class="text-muted text-uppercase d-block">UNSUBSCRIBED</span>
								</div>
							</div>
						</div>
						
						<div class="border rounded p-2 mb-3">
							<div class="row align-items-center">
								<div class="col-md-6">
									<div class="float-left">
										<span class="fa-stack fa-2x">
										  <i class="fas fa-circle fa-stack-2x"></i>
										  <i class="far fa-envelope-open fa-stack-1x fa-inverse"></i>
										</span>
									</div>
									<div class="d-inline-block">
										<p class="text-muted text-uppercase mb-1"><span class="text-blue font-weight-bold">TOTAL OPENED:</span> <?php echo $campaign_report['opens']['opens_total'];?></p>
										<p class="text-muted text-uppercase mb-0"><span class="text-blue font-weight-bold">LAST OPENED:</span> <?php if (!empty($campaign_report['opens']['last_open'])) echo date("m/d/Y g:i A",strtotime($campaign_report['opens']['last_open'])); else echo 'N/A';?></p>
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
										<p class="text-muted text-uppercase mb-1"><span class="text-blue font-weight-bold">TOTAL CLICKS:</span> <?php echo $campaign_report['clicks']['clicks_total']; ?></p>
										<p class="text-muted text-uppercase mb-0"><span class="text-blue font-weight-bold">LAST CLICKED:</span> <?php if (!empty($campaign_report['clicks']['last_click'])) echo date("m/d/Y g:i A",strtotime($campaign_report['clicks']['last_click'])); else echo 'N/A';?></p>
									</div>
									
								</div>
							</div>
						</div>
						
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