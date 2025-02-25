<?php
session_start();
include_once ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");

if(isset($_SESSION["user_role_name"])){

################## TESTING ###################
$template_vars['banner'] = 'https://localfullypromoted.com/promote/templates/img/banner.jpg';
$template_vars['title'] = 'Promote Your Brand. Put Your Logo on a Mug!';
$template_vars['para'] = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed risus eros, condimentum vitae ipsum vulputate, auctor molestie sapien. Cras sed aliquet eros. Proin in vehicula libero, nec faucibus massa. Ut vel sollicitudin nunc, id eleifend magna. Ut sit amet eros gravida, bibendum ligula vitae, accumsan ex. Vivamus gravida magna eros, at vestibulum sapien placerat nec. Nullam sed elit orci. Praesent commodo lacinia diam nec porta.';
$template_vars['h2'] = 'You Can Put Your Logo on Lots of Products!';
$template_vars['para2'] = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed risus eros, condimentum vitae ipsum vulputate, auctor molestie sapien. Cras sed aliquet eros. Proin in vehicula libero, nec faucibus massa. Ut vel sollicitudin nunc, id eleifend magna.';
$template_vars['image'] = 'https://localfullypromoted.com/promote/templates/img/image.jpg';

$templateParams = Array("name"=>"campaign_title Template",
					    "html"=>getTemplate($template_vars, 'testing'));

print_r($templateParams);

################## SHOP LOCAL ###################
$template_vars['banner'] = 'https://localfullypromoted.com/promote/templates/img/shop-local/shop-local.jpg';
$template_vars['title'] = 'Why is it important to shop locally?';
$template_vars['para'] = 'When you shop locally, you are supporting a member of your local community and in these trying times, supporting local businesses will help them stay afloat and survive the quarantines and shutdowns brought on by the Coronavirus. Small businesses in our communities are important to our local economy and research has indicated that when you shop locally, you keep it local because $68 of every $100 spent at a local business ends up in the local community. We are here to build long term strategies to keep your brand top of mind. Working with one professional source can help alleviate stress during these difficult times.';
$template_vars['h2'] = 'When you shop local, you:';
$template_vars['para_textarea_rich'] = '<ul style="padding-left:0; margin-left:18px;">								
	<li style="color:#005d9c;"><span style="color:#000000;">Help keep money flowing through the community</span></li>
	<li style="color:#005d9c;"><span style="color:#000000;">Increase the community’s tax base and local government services</span></li>
	<li style="color:#005d9c;"><span style="color:#000000;">Demo of your Local DashboardCreate jobs within the community</span></li>
	<li style="color:#005d9c;"><span style="color:#000000;">Receive better service that is more personal</span></li>
	<li style="color:#005d9c;line-height: 25px;"><span style="color:#000000;">Spend less time waiting for your products to arrive or services to be performed</span></li>
	<li style="color:#005d9c;"><span style="color:#000000;">Access more unique products</span></li>
	<li style="color:#005d9c;"><span style="color:#000000;">Support the local non-profit community</span></li>
	<li style="color:#005d9c;"><span style="color:#000000;">Support a local family</span></li>
	<li style="color:#005d9c;"><span style="color:#000000;">Help someone in your community reach their goals</span></li>
</ul>';
$template_vars['para3'] = 'Your local Fully Promoted team is ready to help your business with promotional products or custom marketing solutions because we want to help your business survive the Coronavirus.';
$template_vars['image'] = 'https://localfullypromoted.com/promote/templates/img/shop-local/support-small.png';
$template_vars['phone'] = '678-908-9900';

$templateParams = Array("name"=>"campaign_title Template",
					    "html"=>getTemplate($template_vars, 'shop_local'));

print_r($templateParams);

}else{
	pageRedirect("You must be authorized to see this page.", "error", "/promote/");
}

function getTemplate($data, $template_name){
	$template_vars = $data;
	ob_start();

	include ("templates/all-templates.php");
	$val =$template_html[$template_name];
	ob_get_clean();

	return $val;
}

/*function getTemplate($data, $template_name){
	$_POST['vars']= $data;
	ob_start();

	include ("templates/".$template_name.".php");
	//$val =$template_html[$template_name];
	$val = ob_get_clean();
	unset($_POST['vars']);
	return $val;
}*/
?>