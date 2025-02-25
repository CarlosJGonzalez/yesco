<?
?>
<!doctype html>
<html>
<head>
 <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <? include ($_SERVER['DOCUMENT_ROOT'].'/includes/head.php'); ?>
    <title>Email Campaign Reports | Local <?=$client?></title>
	  <link rel="stylesheet" href="/corebridge/css/style.css" type="text/css">
	 <link rel="stylesheet" href="/corebridge/css/styles.css" type="text/css">
     <style>
	 	iframe{
			width:100%;
			height:800px;
			border:none;
		}
		.goals-bar-wrapper{
			max-width:none;
			background:none;
			padding:0;
		}
		.goal-item.to-goal{
			padding-right:10px;
			margin-top:10px;
		}
		.d-block{
			display:block;
		}
		.stat-num{
			font-size:21px;
			font-weight:bold;
			color:#337ab7;
		}
		.stat-label{
			text-transform:uppercase;
			
		}
		.stats{
			overflow:hidden;
		}
		.stats{
			border:1px solid #CCC;
		}
		.stats > div{
			padding:10px !important;
		}
		.stats > div.br{
			border-right:1px solid #CCC;
		}
		.total {
			width: 60%;
			margin: 0 auto 10px auto;
		}
		.total p{
			margin: 0;
			letter-spacing: .5px;
			overflow: hidden;
			vertical-align: middle;
			display: flex;
			justify-content: space-between;
			align-items: center;
		}
		.total span.float-left{
			font-size: 18px;
			text-transform: uppercase;
			float:left;	
		}
		.total span.float-right{
			float: right;
			font-size: 30px;
			font-weight: bold;
			color: #337ab7;
		}
		.goals-inner-wrapper{
			margin-bottom:10px !important;
		}
		.mt-3{
			margin-top:3rem;
		}
		.mb-3{
			margin-bottom:3rem;
		}
		.d-flex{
			display: flex;
		}
		.align-items-center{
			align-items:center;
		}
		.justify-content-between{
			justify-content:space-between;
		}
		.flex-grow{
			flex-grow:1;
		}
		.mb-0{
			margin-bottom:0;
		}
		.mb-1{
			margin-bottom:1rem;
		}
		.pr-1{
			padding-right:1rem;
		}
		.mr-1{
			margin-right:1rem;
		}
		.click-open{
			text-transform:uppercase;
		}
		.click-open .num{
			font-size: 18px;
			font-weight: bold;
			color: #337ab7;
		}
		.fa-circle{
			color:#5F5F5F;
		}
		.font-italic{
			font-style:italic;
		}
		.pl-1{
			padding-left:1rem;
		}
	 </style>
  </head>
</head>
<body>
<? include ($_SERVER['DOCUMENT_ROOT'].'/includes/nav.php'); ?>
<div class="main location">
	<?php
	$data = $db->where('campaing_id',$_GET['id'])->getOne('advtrack.mailchimp_report');

if (count($data) > 0){
	$apiKey = '686a8f18f70a53080e689fe237f9ded9-us20';
	$URL = 'https://' . substr($apiKey,strpos($apiKey,'-')+1) . '.api.mailchimp.com/3.0/campaigns/'.$_GET['id'];
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$URL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
		'Authorization: Basic '.$apiKey,
		'Content-Type: application/json')                                                                       
	);         
	$campaign_data = curl_exec($ch);
	$campaign_data = json_decode($campaign_data, true);
}

	?>
	<h1><?=$data['campaign_title']?> <span class="pl-1 small"> (Sent <?=date("m/d/Y g:i A", strtotime($data['send_time']))?>)</span></h1>
	
	<div class="row">
    	<div class="col-sm-6">
        	<iframe src="<?=$campaign_data['long_archive_url']?>"></iframe> 
        </div>
        <div class="col-sm-6">
        	<div class="goals-bar-wrapper row clearfix">
            	<div class="total">
                    <p class="d-block"><span class="float-left">Successful deliveries</span> <span class="float-right"><?=$data['emails_sent']?></span></p>
                </div>
                <div class="goals-inner-wrapper clearfix"> 
                    <div class="goal-item month-label col-xs-4 col-md-3">Open rate</div>
                    <div class="goal-item to-goal col-xs-8 col-md-9">
                        <span class="goal-value percentage-bar">
                            <div class="progress-bar" style="width:<?php echo round(($data['unique_opens']/$data['emails_sent'])*100,2);?>%"><span class="textvalue"><?php echo round(($data['unique_opens']/$data['emails_sent'])*100,2);?>%</span></div>
                        </span>
                    </div>
                 
                </div>
                <div class="goals-inner-wrapper clearfix"> 
                    <div class="goal-item month-label col-xs-4 col-md-3">Click rate</div>
                    <div class="goal-item to-goal col-xs-8 col-md-9">
                        <span class="goal-value percentage-bar">
                            <div class="progress-bar" style="width:<?php echo round(($data['unique_clicks']/$data['emails_sent'])*100,2);?>%"><span class="textvalue"><?php echo round(($data['unique_clicks']/$data['emails_sent'])*100,2);?>%</span></div>
                        </span>
                    </div>
                 
                </div>
                
                <div class="stats  mt-3 mb-3 d-block text-center">
                	<div class="col-xs-12 col-sm-6 col-md-3 br">
                    	<span class="d-block stat-num"><?=$data['unique_opens']?></span>
                        <span class="d-block stat-label">Opened</span>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-3 br">
                    	<span class="d-block stat-num"><?=$data['unique_clicks']?></span>
                        <span class="d-block stat-label">Clicked</span>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-3 br">
                    	<span class="d-block stat-num"><?=$data['soft_bounces']?></span>
                        <span class="d-block stat-label">Bounced</span>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-3">
                    	<span class="d-block stat-num"><?=$data['unsubscribed']?></span>
                        <span class="d-block stat-label">Unsubscribed</span>
                    </div>
                </div>
            </div>
            <div class="text-left click-open"> 
                <div class="col-sm-6">
                	<div class="d-flex align-items-center ">
                    	<span class="fa-stack fa-2x mr-1">
                          <i class="fa fa-circle fa-stack-2x"></i>
                          <i class="fa fa-envelope-open-o fa-stack-1x fa-inverse"></i>
                        </span>
                        <div class="flex-grow">
                            <p class="mb-0 d-flex align-items-center ">Total opens: <span class="flex-grow num text-right"><?=$data['opens_total']?></span></p>
                            <p class="mb-0 d-flex align-items-center flex-grow">Last opened: <span class="flex-grow num text-right"><?=date("m/d/Y g:i A",strtotime($data['last_open']))?></span></p>
                        </div>
                    </div>
                </div> 
                <div class="col-sm-6">
                	<div class="d-flex align-items-center ">
                    	<span class="fa-stack fa-2x mr-1">
                          <i class="fa fa-circle fa-stack-2x"></i>
                          <i class="fa fa-hand-pointer-o fa-stack-1x fa-inverse"></i>
                        </span>
                        <div class="flex-grow">
                            <p class="mb-0 d-flex align-items-center ">Total clicks: <span class="flex-grow num text-right"><?=$data['clicks_total']?></span></p>
                            <p class="mb-0 d-flex align-items-center flex-grow">Last clicked: <span class="flex-grow num text-right"><?=date("m/d/Y g:i A",strtotime($data['last_click']))?></span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<? include ($_SERVER['DOCUMENT_ROOT'].'/includes/footer.php'); ?> 

</body>
</html>


