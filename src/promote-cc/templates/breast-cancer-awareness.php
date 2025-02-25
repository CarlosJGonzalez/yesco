<?php
session_start();
include_once  ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include_once  ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");

$active_location = $db->where("storeid",$_SESSION['storeid'])->getOne("locationlist");

$db->where("template_name",basename(__FILE__, '.php'));
$cols = Array("field_name","default_text");
$default_vars_a = $db->get("email_template_fields",null,$cols);

foreach ($default_vars_a as $field){
	$value = $db->escape ($field['default_text']);
	$default_vars[$field['field_name']] = $value;
}

if(isset($_POST['vars'])){
	foreach ($_POST['vars'] as $key => $value){
		$value = $db->escape($value);
		$vars[$key] = $value;
	}
	$vars = array_merge($default_vars,$vars);
}elseif($_POST['edit'] == "ok"){
	foreach ($dynamic_template_fields as $field){
		$vars[$field['field_name']] = $field['default_text'];
	}
}else{
	$vars = $default_vars;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Breast Cancer Awareness | Fully Promoted</title>
	<link rel="stylesheet" href="/promote/templates/style.css" type="text/css">
	
	<style>
		body {
			background: #dedede;
			height: 100%;
			width: 100%;
			margin: 0;
			padding: 0;
		}
	</style>
	
</head>

<body>
	<!--wrapper-->
	<table cellpadding="0" cellspacing="0" bgcolor="#dedede" width="100%" align="center" style="margin: 0 auto;">
		<tr>
			<td>
				<!--header-->
				<table cellpadding="0" cellspacing="0" align="center" width="600">
					<tr>
						<td align="left" valign="middle"><img src="https://localfullypromoted.com/promote/templates/img/logo-fp.png" width="250" style="margin:20px 0px 15px 0px;" alt="Fully Promoted Logo" /></td>
						<?  if($vars['phone'] != ''){
									$phone = $vars['phone'];
								}elseif($vars['phone'] == '' && $active_location['phone'] != ''){
									$phone = $active_location['phone'];
								}else{
									$phone = '555-555-5555';
								}
							?>
						<td align="right" valign="middle"><strong style="font-family: Century Gothic, Helvetica, Arial, sans-serif; color:#005d9c; font-size: 24px; line-height: 30px; text-align: center;"><a style="color:#005d9c; text-decoration: none;" href="tel:<? echo $phone; ?>"><? echo $phone; ?></a></strong></td>
					</tr>
				</table>
				<!--/header-->
			</td>
		</tr>
		<tr>
			<td>
				<!--banner-->
				<table cellpadding="0" cellspacing="0" align="center" width="600" bgcolor="#FFFFFF">
					<tr>
						<td align="center"><img src="<?php echo $vars['banner']; ?>" width="100%" alt="Breast Cancer Awareness" /></td>
					</tr>
				</table>
				<!--/banner-->
			</td>
		</tr>
		<tr>
			<td>
				<!--body-->
				<table width="600" cellspacing="0" cellpadding="0" border="0" align="center" style="margin: 0 auto;" bgcolor="#ffffff">
					<tr>
						<td colspan="3">&nbsp;</td>
					</tr>
					<tr>
						<td width="30">&nbsp;</td>
						<td width="540">
							<span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px;"><?php echo $vars['para_1']; ?></span>
						</td>
						<td width="30">&nbsp;</td>
					</tr>
					
					<tr>
						<td colspan="3" height="30">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="3">
						
							<table cellpadding="0" cellspacing="0" width="600" align="center">
								<tr>
									<td width="30">&nbsp;</td>
									<td width="160" align="center"><img src="https://localfullypromoted.com/promote/templates/img/pink-keychain.jpg" height="130" style="margin-bottom: 15px;" /></td>
									<td width="30">&nbsp;</td>
									<td width="160" align="center"><img src="https://localfullypromoted.com/promote/templates/img/pink-pens.jpg" height="130" style="margin-bottom: 15px;" /></td>
									<td width="30">&nbsp;</td>
									<td width="160" align="center"><img src="https://localfullypromoted.com/promote/templates/img/pink-totes.jpg" height="130"  style="margin-bottom: 15px;" /></td>
									<td width="30">&nbsp;</td>
								</tr>
								<tr>
									<td width="30">&nbsp;</td>
									<td width="160" align="center" valign="top"><span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 15px; line-height: 18px;"><strong style="line-height: 24px;">Keychain</strong><br>Shaped like a pink ribbon, these are year-long reminders that bring awareness</span></td>
									<td width="30">&nbsp;</td>
									<td width="160" align="center" valign="top"><span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 15px; line-height: 18px;"><strong style="line-height: 24px;">Pens</strong><br>Pink pens featuring your logo and a pink ribbon design tell the community you support the cause</span></td>
									<td width="30">&nbsp;</td>
									<td width="160" align="center" valign="top"><span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 15px; line-height: 18px;"><strong style="line-height: 24px;">Shopping Totes</strong><br>Featuring a pink ribbon design, these are popular giveaways because they are very useful</span></td>
									<td width="30">&nbsp;</td>
								</tr>
								<tr>
									<td colspan="7" height="40">&nbsp;</td>
								</tr>
								<tr>
									<td width="30">&nbsp;</td>
									<td width="160" align="center"><img src="https://localfullypromoted.com/promote/templates/img/pink-magnet.jpg" height="130" style="margin-bottom: 15px;" /></td>
									<td width="30">&nbsp;</td>
									<td width="160" align="center"><img src="https://localfullypromoted.com/promote/templates/img/pink-shirt.jpg" height="130" style="margin-bottom: 15px;" /></td>
									<td width="30">&nbsp;</td>
									<td width="160" align="center"><img src="https://localfullypromoted.com/promote/templates/img/pink-cup.jpg" height="130"  style="margin-bottom: 15px;" /></td>
									<td width="30">&nbsp;</td>
								</tr>
								<tr>
									<td width="30">&nbsp;</td>
									<td width="160" align="center" valign="top"><span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 15px; line-height: 18px;"><strong style="line-height: 24px;">Magnets</strong><br>Designed to help remind patients of their next exam appointment</span></td>
									<td width="30">&nbsp;</td>
									<td width="160" align="center" valign="top"><span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 15px; line-height: 18px;"><strong style="line-height: 24px;">T-shirts</strong><br>Custom-designed and screenprinted for your team’s participation in a local walk-a-thon</span></td>
									<td width="30">&nbsp;</td>
									<td width="160" align="center" valign="top"><span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 15px; line-height: 18px;"><strong style="line-height: 24px;">Stadium Cups</strong><br>Ideal for use at any community events to promote awareness</span></td>
									<td width="30">&nbsp;</td>
								</tr>
							</table>
							
						</td>
					</tr>
					<tr>
						<td colspan="3" height="40">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="3" align="center"><a href="<?php echo $vars['shop_url']; ?>"><img src="https://localfullypromoted.com/promote/templates/img/shop-now.jpg" alt="Shop Now" /></a></td>
					</tr>
					<tr>
						<td colspan="3" height="30">&nbsp;</td>
					</tr>
				</table>
				<!--/body-->
			</td>
		</tr>

		<tr>
			<td>
				<!--text-->
				<table width="600" cellspacing="0" cellpadding="0" border="0" align="center" style="margin: 0 auto;" bgcolor="#cccccc">
					<tr>
						<td colspan="3" height="30">&nbsp;</td>
					</tr>
					<tr>
						<td width="30">&nbsp;</td>
						<td width="540" align="center">
							<span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 16px; line-height: 22px; color: #000000;"><em><?php echo $vars['para_2']; ?></em></span><br><br><a href="<?php echo $vars['contact_url']; ?>"><img src="https://localfullypromoted.com/promote/templates/img/contact-us.jpg" alt="Contact Us" /></a>
						</td>
						<td width="30">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="3" height="30">&nbsp;</td>
					</tr>
				</table>
				<!--/text-->
			</td>
		</tr>
		
		
		
		<tr>
			<td>
				<!--blue bar-->
				<table cellpadding="0" cellspacing="0" align="center" width="600" bgcolor="#005d9c">
					<tr>
						<td align="center" height="12"></td>
					</tr>
				</table>
				<!--/blue bar-->
			</td>
		</tr>	
		
		<tr>
			<td>	
                <!--contact-->
                <table width="600" cellspacing="0" cellpadding="0" border="0" align="center" style="margin: 0 auto;" bgcolor="#1d3349">
                    <tr>
                        <td colspan="5" height="40">&nbsp;</td>
                    </tr>
                    <tr>
                        <td width="30">&nbsp;</td>
                        <td width="540" style="text-align: center;">
							<span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 22px; line-height: 32px; color: #ffffff;">Fully Promoted <?php echo $active_location['companyname']; ?><br>
							<? if($vars['phone'] != ''){
								$phone = $vars['phone'];
								} elseif($vars['phone'] == '' && $active_location['phone'] != ''){
									$phone = $active_location['phone'];
								} else{
									$phone = '555-555-5555';
								}
							?>
							<strong><a href="tel:<? echo $phone; ?>" style="color: #ffffff; text-decoration:none;"><? echo $phone; ?></a></strong></span>
						</td>
						<td width="30">&nbsp;</td>
                    </tr>	
					<tr>
                        <td colspan="5" height="40">&nbsp;</td>
                    </tr>
                </table>
                <!--/contact-->
                <tr>
					<td>&nbsp;</td>
				</tr>
				
				<tr>
                    <td height="40">&nbsp;</td>
                </tr>
			</td>
		</tr>
	</table>
	<!--/bg-->
</body>
</html>