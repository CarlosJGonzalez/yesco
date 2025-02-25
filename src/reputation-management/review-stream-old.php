<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <? include ($_SERVER['DOCUMENT_ROOT'].'/includes/head.php'); 

   
	/*if(!(roleHasPermission('show_nav_link_option', $_SESSION['role_permissions']))){
		header('location: /');
	}*/
	?>
    <title>Manage Reviews | Local <?=$client?></title>
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
	
    
  </head>
  <body>
  	<? include ($_SERVER['DOCUMENT_ROOT'].'/includes/nav.php'); ?>
	
    <div class="main">
    	<?php
		 if (!empty($_SESSION['success'])) {
			echo '<p class="alert alert-success">'.$_SESSION['success'].'</p>';
			unset($_SESSION['success']);
		 }
		 if (!empty($_SESSION['error'])) {
			echo '<p class="alert alert-danger">'.$_SESSION['error'].'</p>';
			unset($_SESSION['error']);
		 }
		 if (!empty($_SESSION['warning'])) {
			echo '<p class="alert alert-warning">'.$_SESSION['warning'].'</p>';
			unset($_SESSION['warning']);
		 }
		?>
        
    <h1>Reviews</h1>
    <div class="reviews-container clearfix paging">
		<div class="col-sm-12 col-md-6 col-lg-7">
		<?
			$sql="select review_id,reviewer_name,comment,rating,create_date,reply_comment,reply_date,portal,review_link ".
			"from ((select id as review_id,concat(reviewer_firstname,' ',reviewer_lastname) as reviewer_name,review_text as comment,rating,date as create_date,answer as reply_comment,answer_date as reply_date,'facebook' as portal,'' as review_link from facebook_post.fb_reviews where client='".$_SESSION['client']."' and storeid='".$_SESSION['storeid']."')".
			"union (select review_id,reviewer_name,comment,rating,create_date,reply_comment,reply_date,'google' as portal,'' as review_link from facebook_post.gmb_reviews where client='".$_SESSION['client']."' and storeid='".$_SESSION['storeid']."') ".
			"union (select review_id,reviewer_name,comment,rating,create_date,'' as reply_comment,'' as reply_date,'yelp' as portal,review_link from facebook_post.yelp_reviews where client='".$_SESSION['client']."' and storeid='".$_SESSION['storeid']."')) a order by create_date desc";
			



			$sql_review = "SELECT (select concat(link_page,'reviews/') from facebook_post.fb_pages b where b.store_id='".$_SESSION['storeid']."' and b.client = '".$_SESSION['client'].'-'.$_SESSION['storeid']."') as fburl,(SELECT concat('https://search.google.com/local/writereview?placeid=',place_id) FROM facebook_post.gmb_locations a  WHERE a.client='".$_SESSION['client']."' and a.store_id='".$_SESSION['storeid']."') as googleurl";
		
			$review_link = $db->rawQuery($sql_review);
			
			$google_link = '';
			$fb_url     = '';
			if (count($review_link)){
				$google_link = $review_link['googleurl'];
				$facebook_link = $review_link['fburl'];
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
           <i class="fa fa-quote-left fa-lg" aria-hidden="true"></i><!--<img src="http://localexperimac.com/img/quote-left.png">-->
            <p><?=$reviews['comment'];?></p>
			  <span class="pull-right"><i class="fa fa-quote-right fa-lg" aria-hidden="true"></i><!--<img src="http://localexperimac.com/img/quote-right.png">--></span>
          </div><!--/panel-body-->
		  <?
			}
		
		  ?>
		   </div>
          <div class="panel-footer alignRight clearfix">
              <span class="share"><i class="fa fa-share"></i> <a id="<?=$counter?>" href="#" data-toggle="modal" data-target="#shareModal" class="modalTrigger">Share</a></span> |
              <span class="bullhorn"><i class="fa fa-bullhorn"></i> 
			  <?php if($reviews['portal'] != "yelp"){ ?>
				<a href="#" data-toggle="modal" class="respondModal" data-target="#respondModal" data-id="<?=$reviw_id;?>" data-portal="<?=$reviews['portal'];?>" >Respond</a>
			  <?php } else {?>
			  <a href="https://biz.yelp.com/login" target="_blank" >Respond</a>
				</span> |
				<?
				}
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
					<table cellpadding="0" cellspacing="0" style="border: 1px solid #dedede; border-left: 5px solid #cc2028; background: #ffffff;">
						<tr>
							<td height="60"><img src="http://localexperimac.com/img/how.png" /><br><span style="font-family: Helvetica, Arial, sans-serif; color: #000000; font-size: 12px; font-style: italic;">Rate your experience with Experimac <?php echo $row['companyname'] ?>!&nbsp;&nbsp;&nbsp;&nbsp;</span>
							</td>
						</tr>
						<tr>
							<td height="50">
								<a href="<?=$facebook_link?>" target="_blank"><img src="http://localexperimac.com/img/icon-fb.png"/></a>&nbsp;&nbsp;<a href="<?=$google_link?>" target="_blank"><img src="http://localexperimac.com/img/icon-google.png"/></a>
							</td>
						</tr>
						<tr>
							<td><span style="font-family: Helvetica, Arial, sans-serif; color: #000000; font-size: 14px;"><strong style="font-size: 14px; color: #cc2028;">Sell us your old Apple<sup>&reg;</sup> devices.</strong></span></td>
						</tr>
					</table>
					<div class="code">
						<small class="vCode">View Script</small>
						<code>
							&#x3C;table cellpadding=&#x22;0&#x22; cellspacing=&#x22;0&#x22; style=&#x22;border: 1px solid #dedede; border-left: 5px solid #cc2028; background: #ffffff;&#x22;&#x3E;
	&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;tr&#x3E;
	&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;td height=&#x22;60&#x22;&#x3E;&#x3C;img src=&#x22;http://localexperimac.com/img/how.png&#x22; /&#x3E;&#x3C;br&#x3E;&#x3C;span style=&#x22;font-family: Helvetica, Arial, sans-serif; color: #000000; font-size: 12px; font-style: italic;&#x22;&#x3E;Rate your experience with Experimac <?php echo $row["companyname"] ?>&nbsp;&#x26;nbsp;&#x26;nbsp;&#x26;nbsp;&#x3C;/span&#x3E;
	&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;/td&#x3E;
	&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;/tr&#x3E;
	&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;tr&#x3E;
	&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;td height=&#x22;50&#x22;&#x3E;
	&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;a href=&#x22;<?=$facebook_link?>&#x22; target=&#x22;_blank&#x22;&#x3E;&#x3C;img src=&#x22;http://localexperimac.com/img/icon-fb.png&#x22;/&#x3E;&#x3C;/a&#x3E;&#x26;nbsp;&#x26;nbsp;&#x3C;a href=&#x22;<?=$google_link?>&#x22; target=&#x22;_blank&#x22;&#x3E;&#x3C;img src=&#x22;http://localexperimac.com/img/icon-google.png&#x22;/&#x3E;&#x3C;/a&#x3E;
	&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;/td&#x3E;
	&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;/tr&#x3E;
							&#x3C;tr&#x3E;
&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;td&#x3E;&#x3C;span style=&#x22;font-family: Helvetica, Arial, sans-serif; color: #000000; font-size: 14px;&#x22;&#x3E;&#x3C;strong style=&#x22;font-size: 14px; color: #cc2028;&#x22;&#x3E;Sell us your old Apple&#x3C;sup&#x3E;&#x26;reg;&#x3C;/sup&#x3E; devices.&#x3C;/strong&#x3E;&#x3C;/span&#x3E;&#x3C;/td&#x3E;
&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;/tr&#x3E;
	&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;/table&#x3E;
						</code>
					</div>
				</div>
				<div class="widget" id="widget2">
					<table cellpadding="0" cellspacing="0" style="border: 1px solid #dedede; background: #ffffff;">
						<tr bgcolor="#333333">
							<td height="52" align="center"><img src="http://localexperimac.com/img/logo-white.png"/></td>
						</tr>
						<tr>
							<td height="62" align="center"><img src="http://localexperimac.com/img/how.png" /><br><span style="font-family: Helvetica, Arial, sans-serif; color: #000000; font-size: 12px; font-style: italic;">Rate your experience with Experimac <?php echo $row['companyname'] ?>!&nbsp;&nbsp;&nbsp;&nbsp;</span>
							</td>
						</tr>
						<tr>
							<td height="50" align="center">
								<a href="<?=$facebook_link?>" target="_blank"><img src="http://localexperimac.com/img/icon-fb.png"/></a>&nbsp;&nbsp;<a href="<?=$google_link?>" target="_blank"><img src="http://localexperimac.com/img/icon-google.png"/></a>
							</td>
						</tr>
						<tr>
							<td align="center"><span style="font-family: Helvetica, Arial, sans-serif; color: #000000; font-size: 14px;"><strong style="font-size: 14px; color: #cc2028;">Sell us your old Apple<sup>&reg;</sup> devices.</strong></span></td>
						</tr>
					</table>
					<div class="code">
						<small class="vCode">View Script</small>
						<code>
							&#x3C;table cellpadding=&#x22;0&#x22; cellspacing=&#x22;0&#x22; style=&#x22;border: 1px solid #dedede; background: #ffffff;&#x22;&#x3E;
	&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;tr bgcolor=&#x22;#333333&#x22;&#x3E;
	&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;td height=&#x22;52&#x22; align=&#x22;center&#x22;&#x3E;&#x3C;img src=&#x22;http://localexperimac.com/img/logo-white.png&#x22;/&#x3E;&#x3C;/td&#x3E;
	&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;/tr&#x3E;
	&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;tr&#x3E;
	&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;td height=&#x22;62&#x22; align=&#x22;center&#x22;&#x3E;&#x3C;img src=&#x22;http://localexperimac.com/img/how.png&#x22; /&#x3E;&#x3C;br&#x3E;&#x3C;span style=&#x22;font-family: Helvetica, Arial, sans-serif; color: #000000; font-size: 12px; font-style: italic;&#x22;&#x3E;Rate your experience with Experimac <?php echo $row["companyname"] ?>&#x26;nbsp;&#x26;nbsp;&#x26;nbsp;&#x26;nbsp;&#x3C;/span&#x3E;
	&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;/td&#x3E;
	&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;/tr&#x3E;
	&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;tr&#x3E;
	&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;td height=&#x22;50&#x22; align=&#x22;center&#x22;&#x3E;
	&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;a href=&#x22;<?=$facebook_link?>&#x22; target=&#x22;_blank&#x22;&#x3E;&#x3C;img src=&#x22;http://localexperimac.com/img/icon-fb.png&#x22;/&#x3E;&#x3C;/a&#x3E;&#x26;nbsp;&#x3C;a href=&#x22;<?=$google_link?>&#x22; target=&#x22;_blank&#x22;&#x3E;&#x3C;img src=&#x22;http://localexperimac.com/img/icon-google.png&#x22;/&#x3E;&#x3C;/a&#x3E;
	&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;/td&#x3E;
	&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;/tr&#x3E;
							&#x3C;tr&#x3E;
&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;td align=&#x22;center&#x22;&#x3E;&#x3C;span style=&#x22;font-family: Helvetica, Arial, sans-serif; color: #000000; font-size: 14px;&#x22;&#x3E;&#x3C;strong style=&#x22;font-size: 14px; color: #cc2028;&#x22;&#x3E;Sell us your old Apple&#x3C;sup&#x3E;&#x26;reg;&#x3C;/sup&#x3E; devices.&#x3C;/strong&#x3E;&#x3C;/span&#x3E;&#x3C;/td&#x3E;
&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;/tr&#x3E;
	&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;/table&#x3E;
						</code>
					</div>
				</div>
				<div class="widget" id="widget3">
					<table cellpadding="0" cellspacing="0" style="border: 1px solid #dedede; border-left: 5px solid #cc2028; background: #ffffff;">
						<tr>
							<td height="110" align="center" valign="middle"><img src="http://localexperimac.com/img/logo-smaller.png" width="220"/><br><span style="font-family: Helvetica, Arial, sans-serif; color: #000000; font-size: 12px;"><strong style="font-size: 12px; color: #cc2028;">Sell us your old Apple<sup>&reg;</sup> devices.</strong></span></td>
							<td height="110" valign="middle">
								<img src="http://localexperimac.com/img/how.png" /><br><span style="font-family: Helvetica, Arial, sans-serif; color: #000000; font-size: 12px; font-style: italic;">Rate your experience with Experimac <?php echo $row['companyname'] ?>!&nbsp;&nbsp;&nbsp;&nbsp;</span><br>
								<a href="<?=$facebook_link?>" target="_blank"><img src="http://localexperimac.com/img/icon-fb.png"/></a>&nbsp;&nbsp;<a href="<?=$google_link?>" target="_blank"><img src="http://localexperimac.com/img/icon-google.png"/></a>
							</td>
						</tr>
					</table>
					<div class="code">
						<small class="vCode">View Script</small>
						<code>
							&#x3C;table cellpadding=&#x22;0&#x22; cellspacing=&#x22;0&#x22; style=&#x22;border: 1px solid #dedede; border-left: 5px solid #cc2028; background: #ffffff;&#x22;&#x3E;
	&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;tr&#x3E;
	&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;td height=&#x22;110&#x22; align=&#x22;center&#x22; valign=&#x22;middle&#x22;&#x3E;&#x3C;img src=&#x22;http://localexperimac.com/img/logo-smaller.png&#x22; width=&#x22;220&#x22;/&#x3E;&#x3C;br&#x3E;&#x3C;span style=&#x22;font-family: Helvetica, Arial, sans-serif; color: #000000; font-size: 12px;&#x22;&#x3E;&#x3C;strong style=&#x22;font-size: 12px; color: #cc2028;&#x22;&#x3E;Sell us your old Apple&#x3C;sup&#x3E;&#x26;reg;&#x3C;/sup&#x3E; devices.&#x3C;/strong&#x3E;&#x3C;/span&#x3E;&#x3C;/td&#x3E;
	&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;td height=&#x22;110&#x22; valign=&#x22;middle&#x22;&#x3E;
	&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;img src=&#x22;http://localexperimac.com/img/how.png&#x22; /&#x3E;&#x3C;br&#x3E;&#x3C;span style=&#x22;font-family: Helvetica, Arial, sans-serif; color: #000000; font-size: 12px; font-style: italic;&#x22;&#x3E;Rate your experience with Experimac <?php echo $row["companyname"] ?>&#x26;nbsp;&#x26;nbsp;&#x26;nbsp;&#x26;nbsp;&#x3C;/span&#x3E;&#x3C;br&#x3E;
	&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;a href=&#x22;<?=$facebook_link?>&#x22; target=&#x22;_blank&#x22;&#x3E;&#x3C;img src=&#x22;http://localexperimac.com/img/icon-fb.png&#x22;/&#x3E;&#x3C;/a&#x3E;&#x26;nbsp;&#x26;nbsp;&#x3C;a href=&#x22;<?=$google_link?>&#x22; target=&#x22;_blank&#x22;&#x3E;&#x3C;img src=&#x22;http://localexperimac.com/img/icon-google.png&#x22;/&#x3E;&#x3C;/a&#x3E;
	&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;/td&#x3E;
	&#x9;&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;/tr&#x3E;
	&#x9;&#x9;&#x9;&#x9;&#x9;&#x3C;/table&#x3E;
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
				  	<label>
					  <input type="checkbox" name="share[]" value="facebook" />
					  <i class="fa fa-facebook fa-3x text-facebook p-2" aria-hidden="true"></i>
					</label>
					<label>
					  <input type="checkbox" name="share[]" value="twitter" />
					  <i class="fa fa-twitter fa-3x text-twitter p-2" aria-hidden="true"></i>
					</label>
					<label>
					  <input type="checkbox" name="share[]" value="google" />
					  <i class="fa fa-google fa-3x text-googlemb p-2" aria-hidden="true"></i>
					</label>
					  
				  </div>

			  </div>
			  <div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" id="btn_share">Share</button>
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
    
    
    
    
    
</div>

    <? include ($_SERVER['DOCUMENT_ROOT'].'/includes/footer.php'); ?>

    
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
	<script src="html2canvas.js"></script>
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
				//console.log(canvas);
				//console.log(canvas.toDataURL());
				
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
