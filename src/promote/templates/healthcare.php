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
<title>Healthcare | Fully Promoted</title>
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
						<td align="center"><img src="<?php echo $vars['banner']; ?>" width="100%" alt="Healthcare Heroes" /></td>
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
						<td width="540"><span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px;">
							<strong style="color: #005d9c; font-size: 20px;"><?php echo $vars['head1']; ?></strong><br><br>
							<?php echo $vars['para1']; ?></span>
						</td>
						<td width="30">&nbsp;</td>
					</tr>
					<tr>
						<td width="30">&nbsp;</td>
						<td style="border-bottom: 1px solid #dedede;">&nbsp;</td>
						<td width="30">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="3">&nbsp;</td>
					</tr>
					<tr>
						<td width="30">&nbsp;</td>
						<td width="540"><span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px;">
							<strong><?php echo $vars['head2']; ?></strong><br>
							<?php echo $vars['para2']; ?></span>
						</td>
						<td width="30">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="3">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="3">
						
							<table cellpadding="0" cellspacing="0" width="600" align="center">
								<tr>
									<td width="30">&nbsp;</td>
									<td align="center"><img src="<?php echo $vars['image1']; ?>" /></td>
									<td width="30">&nbsp;</td>
									<td align="center"><img src="<?php echo $vars['image2']; ?>"/></td>
									<td width="30">&nbsp;</td>
								</tr>
								<tr>
									<td width="30">&nbsp;</td>
									<td align="center"><span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 16px; line-height: 26px;"><strong><?php echo $vars['prod1']; ?></strong><br><em><?php echo $vars['desc1']; ?></em></span></td>
									<td width="30">&nbsp;</td>
									<td align="center"><span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 16px; line-height: 26px;"><strong><?php echo $vars['prod2']; ?></strong><br><em><?php echo $vars['desc2']; ?></em></span></td>
									<td width="30">&nbsp;</td>
								</tr>
								<tr>
									<td colspan="2" height="40">&nbsp;</td>
								</tr>
								<tr>
									<td width="30">&nbsp;</td>
									<td align="center"><img src="<?php echo $vars['image3']; ?>" /></td>
									<td width="30">&nbsp;</td>
									<td align="center"><img src="<?php echo $vars['image4']; ?>"/></td>
									<td width="30">&nbsp;</td>
								</tr>
								<tr>
									<td width="30">&nbsp;</td>
									<td align="center"><span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 16px; line-height: 26px;"><strong><?php echo $vars['prod3']; ?></strong><br><em><?php echo $vars['desc3']; ?></em></span></td>
									<td width="30">&nbsp;</td>
									<td align="center"><span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 16px; line-height: 26px;"><strong><?php echo $vars['prod4']; ?></strong><br><em><?php echo $vars['desc4']; ?></em></span></td>
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
							<span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 18px; line-height: 28px; color: #000000;"><em><?php echo $vars['para3']; ?></em> </span>
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
                        <td colspan="5" height="20">&nbsp;</td>
                    </tr>
                    <tr>
                        <td width="30">&nbsp;</td>
                        <td width="540" style="text-align: center;">
							<strong style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 18px; line-height: 30px; color: #ffffff; text-decoration:none;"><?php echo $vars['para4']; ?></strong><br><br><a href="<?php echo $vars['contact_url']; ?>"><img src="https://localfullypromoted.com/promote/templates/img/contact-us.jpg" alt="Contact Us" /></a>
						</td>
						<td width="30">&nbsp;</td>
                    </tr>	
					 <tr>
                        <td colspan="5" height="20">&nbsp;</td>
                    </tr>
					
                    <tr>
                        <td colspan="5" height="20">&nbsp;</td>
                    </tr>
                </table>
                <!--/contact-->
                <tr>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td align="center">
						<span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 22px; line-height: 32px; color: #1d3349;">Fully Promoted <?php echo $active_location['companyname']; ?> | 
							<?  if($vars['phone'] != ''){
									$phone = $vars['phone'];
								}elseif($vars['phone'] == '' && $active_location['phone'] != ''){
									$phone = $active_location['phone'];
								}else{
									$phone = '555-555-5555';
								}
							?>
							<strong><a href="tel:<? echo $phone; ?>" style="color: #1d3349; text-decoration:none;"><? echo $phone; ?></a></strong></span>
					</td>
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