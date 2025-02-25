<?
?>
<!doctype html>
<html>
<head>
 <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <? include ($_SERVER['DOCUMENT_ROOT'].'/includes/head.php'); ?>
	<?
	if(!(roleHasPermission('show_nav_link_option', $_SESSION['role_permissions']))){
		header('location: /');
	}
	?>
    <title>Email Campaigns | Local <?=$client?></title>
	  <link rel="stylesheet" href="/corebridge/css/style.css" type="text/css">
	 <link rel="stylesheet" href="/corebridge/css/styles.css" type="text/css">
  	<style>
		.d-flex{
			display: flex;
		}
		.align-items-center{
			align-items:center;
		}
		.justify-content-between{
			justify-content:space-between;
		}
		.justify-content-around{
			justify-content:space-around;
		}
		.flex-grow{
			flex-grow:1;
		}
		.font-bold{
			font-weight:bold;
		}
		.text-muted{
			color:#848484;
		}
		.font-16{
			font-size:16px;
		}
		.mr-2{
			margin-right:2rem;
		}
		.c{
			border-bottom:1px solid #ccc;
			margin:5px 0;
			padding:5px 0;
			overflow:f
		}
	</style>
  </head>
</head>
<body>
<? include ($_SERVER['DOCUMENT_ROOT'].'/includes/nav.php'); ?>
<div class="main location">
	
	<h1>Email Campaigns</h1>
	
    <?php
if($_SESSION['storeid'] > 0 ){
	$sql = "SELECT ec.*,mr.* from  email_campaigns ec INNER JOIN advtrack.mailchimp_report mr on  ec.campaignid = mr.campaing_id where mr.client = '".$_SESSION['client']."' and mr.storeid = '".$_SESSION['storeid']."'";
}else{
	$sql = "SELECT ec.*,mr.* from  email_campaigns ec INNER JOIN advtrack.mailchimp_report mr on  ec.campaignid = mr.campaing_id where mr.client = '".$_SESSION['client']."'";
}

$result = $conn->rawQuery($sql);

if (count($result) > 0){
	foreach ($result as $data) {		   

			
			$date = new DateTime($data['send_time']);
			$date->setTimezone(new DateTimeZone('America/New_York')); // +04

			$send_time = $date->format('D, F dS, Y g:i A T');
			
			if(strtotime($data['send_time'])>strtotime("now")) 
				$scheduled = 0;
			else
				$scheduled = 1;
?>
        <div class="c">
        	<div class="row">
                <div class="col-xs-12 col-sm-4">
                    <div class="d-flex align-items-center">
                        <i class="fa fa-envelope-open-o fa-2x text-muted mr-2" aria-hidden="true"></i>
    
                        <div><?php echo $data['campaign_title']?><br>
							<?php if($scheduled==1){ ?>
								<small class="text-muted">Sent <span class="font-bold"><? if($data['send_time']) echo $send_time?></span> to <?=$data['emails_sent']?> recipients</small>
							<?php }else{?>
								<small class="text-muted">Scheduled for <span class="font-bold"><? if($data['send_time']) echo $send_time?></span></small>
							<?php } ?>
						</div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-4">
                    <div class="d-flex align-items-center justify-content-around text-center">
						<?php if($scheduled==1){ ?>
                        <div><span class="font-bold font-16"><?php echo round(($data['unique_opens']/$data['emails_sent'])*100,2);?>%</span><br> <span class="text-muted">Opens</span></div>
                        <div><span class="font-bold font-16"><?php echo round(($data['unique_clicks']/$data['emails_sent'])*100,2);?>%</span><br> <span class="text-muted">Clicks</span></div>
						<?php } ?>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-2 text-right">
                   <?php if($scheduled==1){ ?> <a href="/reviews/reports/email-report.php?id=<?=$data['campaing_id']?>" class="btn btn-primary">View Report</a><?php } ?>
                </div>
            </div>
        </div>
	<?	} 
		} else echo "You currently have no email campagins.";
	?>

</div>
<? include ($_SERVER['DOCUMENT_ROOT'].'/includes/footer.php'); ?> 
<script>
	$(document).ready(function(){
		$('table').DataTable();
	});
</script>
</body>
</html>


