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
<title>Sports | Fully Promoted</title>
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
						<td align="center"><img src="<?php echo $vars['banner']; ?>" width="100%" alt="Sports" /></td>
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
						<td width="540"><span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 15px; line-height: 22px;">
							<p><?php echo $vars['para_1']; ?></p>
							<p><?php echo $vars['para_2']; ?></p></span>
						</td>
						<td width="30">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="3">&nbsp;</td>
					</tr>
					<tr>
						<td width="30">&nbsp;</td>
						<td width="540">
							
							<table cellpadding="0" cellspacing="0" align="center" style="margin: 0 auto;">
							
								<tr>
									<td width="248" align="left"><img src="<?php echo $vars['image']; ?>" width="248" alt="Sports" /></td>
									<td width="30">&nbsp;</td>
									<td width="262" valign="top">
										<?php echo $vars['list']; ?>
									</td>
								</tr>
								
							</table>
							
						</td>
						<td width="30">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="3">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="3" align="center"><a href="<?php echo $vars['shop_url']; ?>"><img src="https://localfullypromoted.com/promote/templates/img/get-started.jpg" alt="Get Started" /></a></td>
					</tr>
					<tr>
						<td colspan="3" height="30">&nbsp;</td>
					</tr>
					<tr>
						<td width="30">&nbsp;</td>
						<td width="540"><span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 15px; line-height: 22px;"><strong style="color:#2aaed8; font-size: 18px;"><?php echo $vars['heading']; ?></strong><p><?php echo $vars['para_3']; ?></p></span>
							<?php echo $vars['list_2']; ?>
							<p style="font-family: Century Gothic, Helvetica, Arial, sans-serif; color:#2aaed8; font-size: 18px; line-height: 22px; font-weight: bold;"><?php echo $vars['heading_2']; ?></p>
						</td>
						<td width="30">&nbsp;</td>
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
							<strong style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 16px; line-height: 22px; color: #005d9c;"><?php echo $vars['para_4']; ?></strong>
						</td>
						<td width="30">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="3">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="3" align="center"><a href="<?php echo $vars['contact_url']; ?>"><img src="https://localfullypromoted.com/promote/templates/img/contact-us.jpg" alt="Contact Us" /></a></td>
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
							<span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 22px; line-height: 32px; color: #ffffff; text-decoration:none;">
								Fully Promoted <?php echo $active_location['companyname']; ?></span><br/>
							<?  if($vars['phone'] != ''){
									$phone = $vars['phone'];
								}elseif($vars['phone'] == '' && $active_location['phone'] != ''){
									$phone = $active_location['phone'];
								}else{
									$phone = '555-555-5555';
								}
							?>
							<span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 22px; line-height: 32px; font-weight: 600; "><a href="tel:<? echo $phone; ?>" style="color: #ffffff; text-decoration:none;"><? echo $phone; ?></a></span><br/>
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
                    <td height="40">&nbsp;</td>
                </tr>
			</td>
		</tr>
	</table>
	<!--/bg-->
</body>
</html>