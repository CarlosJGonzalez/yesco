<? include ($_SERVER['DOCUMENT_ROOT'].'/connect.php');?>
<div class="wrapper">
<?
$sql_review = "select concat('https://search.google.com/local/writereview?placeid=',place_id) as googleurl,concat(link_page,'reviews/') as fburl from facebook_post.gmb_locations a,facebook_post.fb_pages b where a.client='".$_POST['client']."' and a.store_id='".$_POST['storeid']."' and b.client=concat(a.client,'-',a.store_id) and a.store_id=b.store_id";
$result_review = $conn->query($sql_review);
if ($result_review->num_rows > 0){
	$review = $result_review->fetch_assoc();
	$google_url = $review['googleurl'];
	$fb_url = $review['fburl'];
}
?>
	<div class="content">
		<div class="header">
			<div class="left">
				<img src="http://localexperimac.com/img/logo.png" height="50" class="logo" />
			</div>
			<div class="right">
				<!--edit person's name--><p><strong><?=$_POST['vars']['name']?></strong>, <!--pull in location--><?=$_POST['vars']['companyname']?></p>
			</div>
		</div>
		<div class="top">
			<p><img src="http://localexperimac.com/img/bubble.png" /></p>
			<p><img src="http://localexperimac.com/img/how-lg.png" /></p>
			<!--pull in product name, text not editable--><h1><?=$_POST['vars']['header']?></h1>
			<!--edit block--><p><?=$_POST['vars']['body']?></p>
			<p>
				<a href="<?=$fb_url?>"><img src="http://localexperimac.com/img/facebook.png" /></a>&nbsp;&nbsp;&nbsp;
				<a href="<?=$google_url?>"><img src="http://localexperimac.com/img/google.png" /></a>&nbsp;&nbsp;&nbsp;
				<!--<a href=""><img src="http://localexperimac.com/img/yelp.png" /></a>-->
			</p>
		</div>
	</div> 
</div>

