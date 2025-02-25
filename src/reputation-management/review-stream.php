<!doctype html>
<html lang="en">
  <head>
	  <link rel="stylesheet" href="/css/checkbox.css">
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/head.php"); ?>

    <title>Manage Reviews | <?php echo CLIENT_NAME; ?></title>
	<style>
		.panel .fa-yelp{
			color: #d32323;
		}
		.panel .fa-calendar{
			color: #777676;
		}
		.panel .fa-google {
			color: #dd4b39;
		}
		.panel .fa-facebook {
	  		color:#3b5998;;
		}
		.panel .fa-star{
			color: #f0c000;
		}
		.panel .fa-quote-left{
			color: #b2b1b9;
			font-size: 24px;
		}
		.panel .fa-quote-right{
			color: #b2b1b9;
			font-size: 24px;
		}
		strong{
			font-size: 18px;
		}
		.share {
			color: #2eb137;
			font-size: 16px;
		}
		.share a {
			color: #2eb137;
		}
		.bullhorn{
			color: #f56800;
			font-size: 16px;
		}
		.bullhorn a {
			color: #f56800;
		}
		.eye{
			color: #2b83ce;
			font-size: 16px;
		}
		.eye a {
			color: #2b83ce;
		}
		.reviews-container{
			padding-top: 20px;
		}
		.panel-default{
			border: none !important;
		}
		.panel{
			border-radius: 10px !important;
		}
		.panel-title {
			font-weight: 500px;
		}
		.panel-heading{
			background-color: #fff !important;
			border-bottom: none !important;
			border-top-right-radius: 10px !important;
    		border-top-left-radius: 10px !important;
		}
		.panel-footer{
			background-color: #fff !important;
			border: none !important;
			border-bottom-right-radius: 10px !important;
    		border-bottom-left-radius: 10px !important;
		}
		.alignRight{
			text-align: right;
		}
		.alignCenter{
			text-align: center;
		}
		.panel-body p {
			padding: 15px 30px;
			margin: 0;
			font-size: 16px;
			font-style: italic;
		}
		.pagination>li>a, .pagination>li>span { border-radius: 50% !important;margin: 0 5px;}
		
		
		#page-nav.pagination ul {
			display: inline-block;
			padding-left: 0;
			margin: 20px 0;
			border-radius: 4px;
		}

		#page-nav.pagination ul>li{ display:inline;}
		
		#page-nav.pagination ul>li>a, 
		#page-nav.pagination ul>li>span {
			position: relative;
			float: left;
			padding: 6px 12px;
			margin-left: -1px;
			line-height: 1.42857143;
			color: #337ab7;
			text-decoration: none;
			background-color: #fff;
			border: 1px solid #ddd;
		}
		
		#page-nav.pagination ul>li>a, 
		#page-nav.pagination ul>li>span {
    		border-radius: 50% !important;
    		margin: 0 5px;
		}
		
		
		#page-nav.pagination ul>.active>a, 
		#page-nav.pagination ul>.active>a:focus, 
		#page-nav.pagination ul>.active>a:hover, 
		#page-nav.pagination ul>.active>span, 
		#page-nav.pagination ul>.active>span:focus, 
		#page-nav.pagination ul>.active>span:hover {
			z-index: 3;
			color: #fff;
			cursor: default;
			background-color: #337ab7;
			border-color: #337ab7;
}

		
		#page-nav.pagination ul>.disabled>a, 
		#page-nav.pagination ul>.disabled>a:focus, 
		#page-nav.pagination ul>.disabled>a:hover, 
		#page-nav.pagination ul>.disabled>span, 
		#page-nav.pagination ul>.disabled>span:focus, 
		#page-nav.pagination ul>.disabled>span:hover {
			color: #777;
			cursor: not-allowed;
			background-color: #fff;
			border-color: #ddd;
		}
		
		#page-nav.pagination ul>li:first-child>a, 
		#page-nav.pagination ul>li:first-child>span {
			margin-left: 0;
			border-top-left-radius: 4px;
			border-bottom-left-radius: 4px;
		}
		
		#page-nav.pagination ul>li>a:focus, 
		#page-nav.pagination ul>li>a:hover, 
		#page-nav.pagination ul>li>span:focus, 
		#page-nav.pagination ul>li>span:hover {
			z-index: 2;
			color: #23527c;
			background-color: #eee;
			border-color: #ddd;
		}
		.hide-radio label > input{ /* HIDE RADIO */
		  visibility: hidden; /* Makes input not-clickable */
		  position: absolute; /* Remove input from document flow */
		}
		.hide-radio label > input + i{ /* IMAGE STYLES */
		  cursor:pointer;
		  border:2px solid transparent;
		}
		.hide-radio label > input:checked + i{ /* (RADIO CHECKED) IMAGE STYLES */
		  border:2px solid #f00;
		}
		.p-2{
			padding:1rem;
		}
		.d-inline-block{
			display:inline-block;
		}
		#shareModal #canvas{
			max-width: 100% !important;
			height: auto !important;
		}
		#shareModal #canvas canvas{
			max-width: 100% !important;
			height: auto !important;
		}
		.widgetwrap {
			padding: 20px;
			border: 1px solid #cccccc;
		}
		.widget {
			padding-bottom: 20px;
			margin-bottom: 20px;
			border-bottom: 1px solid #cccccc;
		}
		.widget:last-child {
			border-bottom: none;
		}
		code{
			display: block;
			padding: 5px;
			margin-top: 10px;
			display: none;
		}
		.vCode{
			cursor: pointer;
		}
		
	</style> 
	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
  </head>
  <body class="bg-light cbp-spmenu-push">
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/top.php"); ?>

    <div class="container-fluid">
      <div class="row">
        <?php 
    		include ($_SERVER['DOCUMENT_ROOT']."/includes/nav.php");
        	require_once ($_SERVER['DOCUMENT_ROOT']."/includes/ClassDasReview.php");
         ?>
        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 p-0 mb-4">
			<div id="spinner_loading" class="none_upload loader"></div>
			<div class="p-0 border-bottom mb-4">
				<div class="d-flex d-block align-items-center clearfix py-2 px-4">
					<h1 class="h2 font-light mb-0 text-center text-sm-left"><i class="fas fa-star mr-2"></i> Reputation Management</h1>
				</div>
			</div>
        	<div class="px-4 py-3">
				<div class="row paging">
				<div class="col-sm-12 col-md-6 col-lg-7">
				<?
					$sql="select review_id,reviewer_name,comment,rating,create_date,reply_comment,reply_date,portal,review_link ".
					"from ((select id as review_id,concat(reviewer_firstname,' ',reviewer_lastname) as reviewer_name,review_text as comment,rating,date as create_date,answer as reply_comment,answer_date as reply_date,'facebook' as portal,'' as review_link from facebook_post.fb_reviews where client='".$_SESSION['client']."' and storeid='".$_SESSION['storeid']."')".
					"union (select review_id,reviewer_name,comment,rating,create_date,reply_comment,reply_date,'google' as portal,'' as review_link from facebook_post.gmb_reviews where client='".$_SESSION['client']."' and storeid='".$_SESSION['storeid']."') ".
					"union (select review_id,reviewer_name,comment,rating,create_date,'' as reply_comment,'' as reply_date,'yelp' as portal,review_link from facebook_post.yelp_reviews where client='".$_SESSION['client']."' and storeid='".$_SESSION['storeid']."')) a order by create_date desc";

					/*$sql="select review_id,reviewer_name,comment,rating,create_date,reply_comment,reply_date,portal,review_link ".
					"from ((select id as review_id,concat(reviewer_firstname,' ',reviewer_lastname) as reviewer_name,review_text as comment,rating,date as create_date,answer as reply_comment,answer_date as reply_date,'facebook' as portal,'' as review_link from facebook_post.fb_reviews where client='0004' and storeid='10398')".
					"union (select review_id,reviewer_name,comment,rating,create_date,reply_comment,reply_date,'google' as portal,'' as review_link from facebook_post.gmb_reviews where client='0004' and storeid='10398') ".
					"union (select review_id,reviewer_name,comment,rating,create_date,'' as reply_comment,'' as reply_date,'yelp' as portal,review_link from facebook_post.yelp_reviews where client='0004' and storeid='10398')) a order by create_date desc";*/


					$dasReview = new Das_Review($db,$client,$storeid);

					$google_link   = $dasReview->getLinkGoogle();
					$facebook_link = $dasReview->getLinkFB();

					if($facebook_link != ""){
						$fb_html ='&#x3C;a href=&#x22;'.$facebook_link.'&#x22; target=&#x22;_blank&#x22;&#x3E;&#x3C;img src=&#x22;http://localfullypromoted.com/img/icon-fb.png&#x22;/&#x3E;&#x3C;/a&#x3E;';
						$fb_html_view = '<a href="'.$facebook_link.'" target="_blank"><img src="http://localfullypromoted.com/img/icon-fb.png"/></a>';
					}
					if($google_link != "" ){
						$goo_html='&#x3C;a href=&#x22;'.$google_link.'&#x22; target=&#x22;_blank&#x22;&#x3E;&#x3C;img src=&#x22;http://localfullypromoted.com/img/icon-google.png&#x22;/&#x3E;&#x3C;/a&#x3E;';
						$goo_html_view = '<a href="'.$google_link.'" target="_blank"><img src="http://localfullypromoted.com/img/icon-google.png"/></a>';
					}


					if($row["yelp"]){
						$yelp_link = $row["yelp"];
					}
					$reviews_all = $db->rawQuery($sql);

					if (count($reviews_all) > 0){

					foreach ($reviews_all as $reviews) {

					$reviw_id = base64_encode($client.$reviews['review_id']);
					if($reviews["portal"] == "yelp"){
								$review_yelp_link = explode("?",$reviews["review_link"]);
								$hrid = $review_yelp_link[1]; 

								$yelp_link = $yelp_link."?$hrid";
							}

				?>
			   <div  class="review-item col-sm-12 paginate">
				 <div class="panel panel-default">
					 <div class="review-image" id="img-<?=$counter?>">
					<div class="panel-heading">
						<h3 class="panel-title" itemprop="author"><strong><?=$reviews['reviewer_name'];?></strong> on <i class="fa fa-<?=$reviews['portal'];?>"></i></span> <span>| <i class="fa fa-calendar"></i> <?=date("Y-m-d", strtotime($reviews['create_date']));?></span>
						<span class="pull-right">
							<?for ($k = 0 ; $k < $reviews['rating']; $k++){?>
							<i class="fa fa-star"></i>
							<?}?>
						</span>
						</h3>
					</div><!--/panel-heading-->
					<?
					if ($reviews['comment'] <> ''){
					?>
				  <div class="panel-body" itemprop="reviewBody">
				   <i class="fa fa-quote-left fa-lg" aria-hidden="true"></i><!--<img src="http://localfullypromoted.com/img/quote-left.png">-->
					<p><?=$reviews['comment'];?></p>
					  <span class="pull-right"><i class="fa fa-quote-right fa-lg" aria-hidden="true"></i><!--<img src="http://localfullypromoted.com/img/quote-right.png">--></span>
				  </div><!--/panel-body-->
				  <?
					}

				  ?>
				   </div>
				  <div class="panel-footer alignRight clearfix">
				  		<?php if($reviews['portal'] != "google"){ ?>
					  <span class="share"><i class="fa fa-share"></i> 
					  	<a id="<?=$counter?>" href="#" data-toggle="modal" data-target="#shareModal" class="modalTrigger">Share</a></span> |
					  <?php } if($reviews['portal'] != "facebook"){ ?>
					  <span class="bullhorn"><i class="fa fa-bullhorn"></i> 
					  <?php if($reviews['portal'] != "yelp"){ ?>
						<a  class="respondModal" href="#" data-toggle="modal" data-target="#respondModal" data-id="<?=$reviw_id;?>" data-portal="<?=$reviews['portal'];?>" >Respond</a>
					  <?php } else {?>
					  <a href="https://biz.yelp.com/login" target="_blank" >Respond</a>
						</span> |
						<?
						}}
					  ?>
					  <span class="eye"><i class="fa fa-eye"></i><a href="<?=${$reviews['portal']."_link"};?>" target="_blank">View</a></span>
				  </div><!--/panel-footer-->
				</div><!--/panel-->
				</div>
				<?
				 $counter ++;	
				 }
			 }
				?>
			  </div>

				<div class="col-12 col-md-6 col-lg-5">
					<h2 style="margin: 0 0 20px;">Email Signature Snippets</h2>
					<div class="widgetwrap">
						<div class="widget" id="widget1">
							<table cellpadding="0" cellspacing="0" style="border: 1px solid #dedede; border-left: 5px solid #2aaed8; background: #ffffff;">
								<tr>
									<td width="15">&nbsp;</td>
									<td height="60"><img src="https://bucketeer-7e072203-1909-474e-9ffa-d14319aed5b1.s3.amazonaws.com/public/how.png" /><br><span style="font-family: Helvetica, Arial, sans-serif; color: #000000; font-size: 12px; font-style: italic;">Rate your experience with <?php echo CLIENT_NAME; ?> <?php echo $row['companyname'] ?>! &nbsp;&nbsp;&nbsp;&nbsp;</span>
									</td>
								</tr>
								<tr>
									<td width="15">&nbsp;</td>
									<td height="50">
										<?=$fb_html_view?>&nbsp;&nbsp;<?=$goo_html_view?>
									</td>
								</tr>
							</table>
							<div class="code">
								<small class="vCode">View Script</small>
								<code>
									&#x3C;table cellpadding=&#x22;0&#x22; cellspacing=&#x22;0&#x22; style=&#x22;border: 1px solid #dedede; border-left: 5px solid #2aaed8; background: #ffffff;&#x22;&#x3E;
&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;tr&#x3E;
&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;td width=&#x22;15&#x22;&#x3E;&#x26;nbsp;&#x3C;/td&#x3E;
&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;td height=&#x22;60&#x22;&#x3E;&#x3C;img src=&#x22;https://bucketeer-7e072203-1909-474e-9ffa-d14319aed5b1.s3.amazonaws.com/public/how.png&#x22; /&#x3E;&#x3C;br&#x3E;&#x3C;span style=&#x22;font-family: Helvetica, Arial, sans-serif; color: #000000; font-size: 12px; font-style: italic;&#x22;&#x3E;Rate your experience with <?php echo CLIENT_NAME; ?> <?php echo $row['companyname'] ?>! &#x26;nbsp;&#x26;nbsp;&#x26;nbsp;&#x26;nbsp;&#x3C;/span&#x3E;
&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;/td&#x3E;
&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;/tr&#x3E;
&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;tr&#x3E;
&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;td width=&#x22;15&#x22;&#x3E;&#x26;nbsp;&#x3C;/td&#x3E;
&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;td height=&#x22;50&#x22;&#x3E;
&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;<?=$fb_html_view?>&#x26;nbsp;&#x26;nbsp;<?=$goo_html_view?>
&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;/td&#x3E;
&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;/tr&#x3E;
&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;/table&#x3E;
								</code>
							</div>
						</div>
						<div class="widget" id="widget2">
							<table cellpadding="0" cellspacing="0" style="border: 1px solid #dedede; background: #ffffff;">
								<tr bgcolor="#333333">
									<td width="15">&nbsp;</td>
									<td height="52" align="center"><img src="https://bucketeer-7e072203-1909-474e-9ffa-d14319aed5b1.s3.amazonaws.com/public/fp-white.png" height="40"/></td>
									<td width="15">&nbsp;</td>
								</tr>
								<tr>
									<td width="15">&nbsp;</td>
									<td height="62" align="center"><img src="https://bucketeer-7e072203-1909-474e-9ffa-d14319aed5b1.s3.amazonaws.com/public/how.png" /><br><span style="font-family: Helvetica, Arial, sans-serif; color: #000000; font-size: 12px; font-style: italic;">Rate your experience with <?php echo CLIENT_NAME; ?> <?php echo $row['companyname'] ?>!&nbsp;&nbsp;&nbsp;&nbsp;</span>
									</td>
									<td width="15">&nbsp;</td>
								</tr>
								<tr>
									<td width="15">&nbsp;</td>
									<td height="50" align="center">
										<?=$fb_html_view?>&nbsp;&nbsp;<?=$goo_html_view?>
									</td>
									<td width="15">&nbsp;</td>
								</tr>
							</table>
							<div class="code">
								<small class="vCode">View Script</small>
								<code>
									&#x3C;table cellpadding=&#x22;0&#x22; cellspacing=&#x22;0&#x22; style=&#x22;border: 1px solid #dedede; background: #ffffff;&#x22;&#x3E;
&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;tr bgcolor=&#x22;#333333&#x22;&#x3E;
&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;td width=&#x22;15&#x22;&#x3E;&#x26;nbsp;&#x3C;/td&#x3E;
&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;td height=&#x22;52&#x22; align=&#x22;center&#x22;&#x3E;&#x3C;img src=&#x22;https://bucketeer-7e072203-1909-474e-9ffa-d14319aed5b1.s3.amazonaws.com/public/fp-white.png&#x22; height=&#x22;40&#x22;/&#x3E;&#x3C;/td&#x3E;
&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;td width=&#x22;15&#x22;&#x3E;&#x26;nbsp;&#x3C;/td&#x3E;
&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;/tr&#x3E;
&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;tr&#x3E;
&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;td width=&#x22;15&#x22;&#x3E;&#x26;nbsp;&#x3C;/td&#x3E;
&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;td height=&#x22;62&#x22; align=&#x22;center&#x22;&#x3E;&#x3C;img src=&#x22;https://bucketeer-7e072203-1909-474e-9ffa-d14319aed5b1.s3.amazonaws.com/public/how.png&#x22; /&#x3E;&#x3C;br&#x3E;&#x3C;span style=&#x22;font-family: Helvetica, Arial, sans-serif; color: #000000; font-size: 12px; font-style: italic;&#x22;&#x3E;Rate your experience with <?php echo CLIENT_NAME; ?> <?php echo $row['companyname'] ?>!&#x26;nbsp;&#x26;nbsp;&#x26;nbsp;&#x26;nbsp;&#x3C;/span&#x3E;
&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;/td&#x3E;
&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;td width=&#x22;15&#x22;&#x3E;&#x26;nbsp;&#x3C;/td&#x3E;
&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;/tr&#x3E;
&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;tr&#x3E;
&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;td width=&#x22;15&#x22;&#x3E;&#x26;nbsp;&#x3C;/td&#x3E;
&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;td height=&#x22;50&#x22; align=&#x22;center&#x22;&#x3E;
&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;<?=$fb_html_view?>&#x26;nbsp;&#x26;nbsp;<?=$goo_html_view?>
&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;/td&#x3E;
&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;td width=&#x22;15&#x22;&#x3E;&#x26;nbsp;&#x3C;/td&#x3E;
&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;/tr&#x3E;
&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;/table&#x3E;
								</code>
							</div>
						</div>
						<div class="widget" id="widget3">
							<table cellpadding="0" cellspacing="0" style="border: 1px solid #dedede; border-left: 5px solid #2aaed8; background: #ffffff;">
								<tr>
									<td height="110" align="center" valign="middle"><img src="https://bucketeer-7e072203-1909-474e-9ffa-d14319aed5b1.s3.amazonaws.com/public/fp.png" width="220"/></td>
									<td width="20">&nbsp;</td>
									<td height="110" valign="middle">
										<img src="https://bucketeer-7e072203-1909-474e-9ffa-d14319aed5b1.s3.amazonaws.com/public/how.png" /><br><span style="font-family: Helvetica, Arial, sans-serif; color: #000000; font-size: 12px; font-style: italic;">Rate your experience with <?php echo CLIENT_NAME; ?> <?php echo $row['companyname'] ?>!&nbsp;&nbsp;&nbsp;&nbsp;</span><br>
										<?=$fb_html_view?>&nbsp;&nbsp;<?=$goo_html_view?>
									</td>
								</tr>
							</table>
							<div class="code">
								<small class="vCode">View Script</small>
								<code>
									&#x3C;table cellpadding=&#x22;0&#x22; cellspacing=&#x22;0&#x22; style=&#x22;border: 1px solid #dedede; border-left: 5px solid #2aaed8; background: #ffffff;&#x22;&#x3E;
&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;tr&#x3E;
&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;td height=&#x22;110&#x22; align=&#x22;center&#x22; valign=&#x22;middle&#x22;&#x3E;&#x3C;img src=&#x22;https://bucketeer-7e072203-1909-474e-9ffa-d14319aed5b1.s3.amazonaws.com/public/fp.png&#x22; width=&#x22;220&#x22;/&#x3E;&#x3C;/td&#x3E;
&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;td width=&#x22;20&#x22;&#x3E;&#x26;nbsp;&#x3C;/td&#x3E;
&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;td height=&#x22;110&#x22; valign=&#x22;middle&#x22;&#x3E;
&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;img src=&#x22;https://bucketeer-7e072203-1909-474e-9ffa-d14319aed5b1.s3.amazonaws.com/public/how.png&#x22; /&#x3E;&#x3C;br&#x3E;&#x3C;span style=&#x22;font-family: Helvetica, Arial, sans-serif; color: #000000; font-size: 12px; font-style: italic;&#x22;&#x3E;Rate your experience with <?php echo CLIENT_NAME; ?> <?php echo $row['companyname'] ?>!&#x26;nbsp;&#x26;nbsp;&#x26;nbsp;&#x26;nbsp;&#x3C;/span&#x3E;&#x3C;br&#x3E;
&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;<?=$fb_html_view?>&#x26;nbsp;&#x26;nbsp;<?=$goo_html_view?>
&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;/td&#x3E;
&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;/tr&#x3E;
&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;/table&#x3E;
								</code>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!--Share-->
			  <form action="xt_share_review.php" method="POST" id="frm_share">
				<div class="modal fade" id="shareModal" tabindex="-1" role="dialog" aria-labelledby="shareModalLabel" aria-hidden="true">
				  <div class="modal-dialog" role="document">
					<div class="modal-content">
					  <div class="modal-header">
						<h5 class="modal-title d-inline-block h4">Share Review</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						  <span aria-hidden="true">&times;</span>
						</button>
					  </div>
					  <div class="modal-body">
						 <div id="canvas">
						 </div>
						  <p>Select which platform you'd like to share your review on.</p>
						  <div class="hide-radio text-center">
						  	<?php if($facebook_link != "" ){ ?>				
								 <input type="checkbox" name="share[]" value="facebook" />
                                 <i class="fab fa-facebook-f fa-3x"></i>
			
							<?php } ?>
							<!--<label>
							  <input type="checkbox" name="share[]" value="twitter" />
							  <i class="fas fa-twitter fa-3x text-twitter p-2" aria-hidden="true"></i>
							</label>-->
							<?php if($google_link != "" ){ ?>
							
								  <input type="checkbox" name="share[]" value="google" />
								  <i class="fab fa-google fa-3x" ></i>
					
							<?php } ?>
						  </div>

					  </div>
					  <div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						<?php if($google_link != "" && $facebook_link != ""){ ?>
							<button type="button" class="btn btn-primary" id="btn_share">Share</button>
						<?php } ?>
						<!--<input type="submit" class="btn btn-primary" id="btn_share" value="Share">-->
					  </div>
					</div>
				  </div>
				</div>
			  </form>

			  <!--Respond-->
			  <form action="xt_reply_review.php" id="frm_reply" method="POST">
				<div class="modal fade" id="respondModal" tabindex="-1" role="dialog" aria-labelledby="respondModalLabel" aria-hidden="true">
				  <div class="modal-dialog" role="document">
					<div class="modal-content">
					  <div class="modal-header">
						<h5 class="modal-title d-inline-block h4">Respond to Review</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						  <span aria-hidden="true">&times;</span>
						</button>
					  </div>
					  <div class="modal-body">
						  <textarea class="form-control" name="txt_reply" required></textarea>

					  </div>
					  <div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						<button type="button" class="btn btn-primary" id="btn_reply">Send</button>
					  </div>
					</div>
				  </div>
				</div>
			  </form>


			<div class="col-sm-12 alignCenter"> 
				<hr> 
				<div id="page-nav" class="pagination" ></div>    

				<!--

				<hr> 
				   <ul class="pagination">
						  <li class="disabled"><a href="#">«</a></li>
						  <li class="active"><a href="#">1 <span class="sr-only">(current)</span></a></li>
						  <li><a href="#">2</a></li>
						  <li><a href="#">3</a></li>
						  <li><a href="#">4</a></li>
						  <li><a href="#">5</a></li>
						  <li><a href="#">6</a></li>
						  <li><a href="#">7</a></li>
						  <li><a href="#">8</a></li>
						  <li><a href="#">9</a></li>
						  <li><a href="#">10</a></li>
						  <li><a href="#">11</a></li>
						  <li><a href="#">12</a></li>
						  <li><a href="#">»</a></li>
				   </ul> -->
			 </div> 
			</div>
        </main>
      </div>
    </div>


    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>
    <script type="text/javascript">

		$(document).ready(function(){
			
			$('select[name="approved"]').change(function() {
			  var value = $(this).val();
			  var reviewid = $(this).find(':selected').data('reviewid');
			  var dataString = 'value='+ value + '&reviewid=' + reviewid;
			  $.ajax
			  ({
			   type: "POST",
			   url: "xt_updateApproval.php",
			   data: dataString,
			   cache: false,
			   success: function(html){
					if(html=="success"){
						$( "<p class='alert alert-success'>Your changes have been successfully saved.</p>" ).prependTo($(".main")).delay(2500).fadeOut("slow");
					}
			   } 
			   });
			});
			
			//    simple pagination
			
			$(function() {
				// Grab whatever we need to paginate
				var pageParts = $(".paginate");
			
				// How many parts do we have?
				var numPages = pageParts.length;
				// How many parts do we want per page?
				var perPage = 10;
			
				// When the document loads we're on page 1
				// So to start with... hide everything else
				pageParts.slice(perPage).hide();
				// Apply simplePagination to our placeholder
				$("#page-nav").pagination({
					items: numPages,
					itemsOnPage: perPage,
					prevText: '«',
					nextText: '»',
					//cssStyle: "light-theme",
					// We implement the actual pagination
					// in this next function. It runs on
					// the event that a user changes page
					onPageClick: function(pageNum) {
						// Which page parts do we show?
						var start = perPage * (pageNum - 1);
						var end = start + perPage;
			
						// First hide all page parts
						// Then show those just for our page
						pageParts.hide()
							.slice(start, end).show();
					}
				});
			});
			
			
		$("#btn_share").click(function(e){			
		
			var elem = document.getElementsByTagName("canvas")[0];
			$('<input />').attr('type', 'hidden')
					  .attr('name', "canvas_context")
					  .attr('value', elem.toDataURL())
					  .appendTo('#frm_share');
			$('<input />').attr('type', 'hidden')
					  .attr('name', "xt")
					  .attr('value', "share")
					  .appendTo('#frm_share');
			$("#frm_share").submit();
			
		});
		
		$("#btn_reply").click(function(e){
			if(($("textarea[name='txt_reply']").val() === "") || 
			($("textarea[name='txt_reply']").length === 0) || 
			($("textarea[name='txt_reply']").val() === "undefined" )){
				return false;
			}
			$('<input />').attr('type', 'hidden')
					  .attr('name', "xt")
					  .attr('value', "reply")
					  .appendTo('#frm_reply');
					  
			//console.log($("#frm_reply").serialize());
			$("#frm_reply").submit();
			
		});	
			
	});

    </script>
	<script src="/../js/html2canvas.js"></script>
	<script>
		$('.modalTrigger').on('click', function (e) {
			e.stopPropagation();
			var id = $(this).attr('id');		
			var panel = $(this).closest("div.panel-footer");
			//var panelFooter = $item.parentsUntil($(".review-item")).find(".");
			var review_image = $(panel).siblings("div.review-image");
			document.getElementById('canvas').innerHTML = "";
			document.getElementById('canvas').style.width = "500px"; 
			var item = document.getElementById("img-"+id);
			item.style.width = "500px"; 		
			html2canvas(item,{removeContainer:true,scale:2}).then(function(canvas) {				
				document.getElementById('canvas').appendChild(canvas);			
			});
					
			$('#shareModal').modal("show")  ;
			//console.log(panelFooter);
		});
		
		$('#shareModal').on('hide.bs.modal', function (e) {
			  $("div.review-image").css("width", "100%");
		});
		
		$('#respondModal').on('hide.bs.modal', function (e) {		
			$("input[name='review_id']").remove();
			$("input[name='review_portal']").remove();	
		});
		
		$('.respondModal').on('click', function (e) {
			e.stopPropagation();
			$('<input />').attr('type', 'hidden')
				  .attr('name', "review_id")
				  .attr('value',this.dataset["id"])
				  .appendTo('#frm_reply');
			$('<input />').attr('type', 'hidden')
				  .attr('name', "review_portal")
				  .attr('value',this.dataset["portal"])
				  .appendTo('#frm_reply');
				  
			$('#respondModal').modal("show")  ;
			//console.log(this.dataset["id"]);
			
		});
		
		$(".vCode").on('click', function (e) {
			$(this).siblings("code").slideToggle();
		});
		
		
			
		
	</script>
	
  </body>
</html>