<? include ($_SERVER['DOCUMENT_ROOT'].'/connect.php');?>
<div class="wrapper">
<?
$sql_review = "select concat('https://search.google.com/local/writereview?placeid=',place_id) as googleurl from facebook_post.gmb_locations a where a.client='".$_POST['client']."' and a.store_id='".$_POST['storeid']."'";
$result_review = $conn->query($sql_review);
if ($result_review->num_rows > 0){
	$review = $result_review->fetch_assoc();
	$google_url = $review['googleurl'];
}
$sql_review = "select concat(link_page,'reviews/') as fburl from facebook_post.fb_pages a where a.client='".$_POST['client']."-".$_POST['storeid']."' and a.store_id='".$_POST['storeid']."'";
$result_review = $conn->query($sql_review);
if ($result_review->num_rows > 0){
	$review = $result_review->fetch_assoc();
	$fb_url = $review['fburl'];
}
$sql_review = "select yelp from locationlist where storeid='".$_POST['storeid']."'";
$result_review = $conn->query($sql_review);
if ($result_review->num_rows > 0){
	$review = $result_review->fetch_assoc();
	$yelp_url = $review['yelp'];
}
?>
	<div class="content">
		<div class="header">
			<div class="left">
				<img src="/corebridge/email/img/logo.png" class="logo" />
			</div>
			<div class="right">
				<!--edit person's name--><p><strong><?=$_POST['vars']['name']?></strong>, <!--pull in location--><?=$_POST['vars']['companyname']?></p>
			</div>
		</div>
		<div class="top">
			<p><img src="https://localsignarama.com/corebridge/email/img/bubble.jpg" /></p>
			<p><img src="https://localsignarama.com/corebridge/email/img/how.png" /></p>
			<!--pull in product name, text not editable--><h1><?=$_POST['vars']['header']?></h1>
			<!--edit block--><p><?=$_POST['vars']['body']?></p>
			<p>
				<a href="<?=$fb_url?>"><img src="http://localexperimac.com/corebridge/email/img/facebook.png" /></a>&nbsp;&nbsp;&nbsp;
				<a href="<?=$google_url?>"><img src="http://localexperimac.com/corebridge/email/img/google.png" /></a>&nbsp;&nbsp;&nbsp;
				<!--<a href="<?=$yelp_url?>"><img src="http://localexperimac.com/corebridge/email/img/yelp.png" /></a>-->
			</p>
		</div>
		
	</div> 
</div>

