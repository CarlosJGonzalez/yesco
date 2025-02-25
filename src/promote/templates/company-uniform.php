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
<title>Company Uniform | Fully Promoted</title>
<style>
body {
	background: #dedede;
	height: 100%;
	width: 100%;
	margin: 0;
	padding: 0;
}
li::marker{
	font-weight: 700;
	font-size: 20px; 
	color:#0068b3;
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
						<td align="center" height="40"><img src="https://localfullypromoted.com/promote/templates/img/shop-local/logo-fp.png" width="250" style="margin:20px 0px 15px 0px;" alt="Fully Promoted Logo" /></td>
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
						<td align="center"><img src="<?php echo $vars['banner']; ?>" width="100%" alt="Company Uniform Banner" /></td>
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
						<td width="540"><span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 16px; line-height: 22px;"><p style="line-height: 25px;"><?php echo replace_characters($vars['para'], "<br>"); ?></p></span></td>
						<td width="30">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="3">&nbsp;</td>
					</tr>
				</table>
				<!--/body-->
			</td>
		</tr>
		<tr>
			<td>
				<!--blue text bar-->
				<table cellpadding="0" cellspacing="0" align="center" width="600" bgcolor="#005d9c">
					<tr>
						<td align="center" height="12"><p><span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 18px; line-height: 22px;text-align: center;color:white;font-weight: 600;"><?php echo $vars['headline']; ?></span></p></td>
					</tr>
				</table>
				<!--/blue text bar-->
			</td>
		</tr>
		<tr>
			<td>
				<!--list-->
				<table width="600" cellspacing="0" cellpadding="0" border="0" align="center" style="margin: 0 auto;" bgcolor="#ffffff">
					<tr>
						<td colspan="3">&nbsp;</td>
					</tr>
					<tr>
						<td width="30">&nbsp;</td>
						<td width="540">
							<span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 16px; line-height: 33px;">
								<p style="line-height: 25px;"><?php echo replace_characters($vars['para_2'], "<br>"); ?></p>
								<?php echo str_replace("\\r\\n",'',$vars['para_textarea_rich']); ?>
							</span>
						</td>
						<td width="30">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="3">&nbsp;</td>
					</tr>
				</table>
				<!--/list-->
			</td>
		</tr>
		<!--photo-->
		<tr>
			<td>
				<table cellpadding="0" cellspacing="0" align="center" width="600" bgcolor="#FFFFFF">
					<tr>
						<td align="center"><img src="<?php echo $vars['image']; ?>" width="100%" alt="Company Uniform Banner" /></td>
					</tr>
				</table>
			</td>
		</tr>
		<!--/photo-->
		<tr>
			<td>
				<!--blue text bar-->
				<table cellpadding="0" cellspacing="0" align="center" width="600" bgcolor="#005d9c">
					<tr>
						<td align="center" height="12"><p><span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 18px; line-height: 22px;text-align: center;color:white;font-weight: 600;"><?php echo $vars['headline_2']; ?></span></p></td>
					</tr>
				</table>
				<!--/blue text bar-->
			</td>
		</tr>
		<tr>
			<td>
				<!--list-->
				<table width="600" cellspacing="0" cellpadding="0" border="0" align="center" style="margin: 0 auto;" bgcolor="#ffffff">
					<tr>
						<td colspan="3">&nbsp;</td>
					</tr>
					<tr>
						<td width="30">&nbsp;</td>
						<td width="540">
							<span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 16px; line-height: 33px;">
								<p style="line-height: 25px; margin-bottom:0px;"><?php echo replace_characters($vars['para_3'], "<br>"); ?></p>
							</span>
						</td>
						<td width="30">&nbsp;</td>
					</tr>
				</table>
				<!--/list-->
			</td>
		</tr>
		<tr>
			<td>
				<!--list-->
				<table width="600" cellspacing="0" cellpadding="0" border="0" align="center" style="margin: 0 auto;" bgcolor="#ffffff">
					<tr><td width="30"></td><td width="255" style="text-align:left;">
										<tr>
						<td width="30"></td>
						<td width="255" style="text-align:left;">
							<p style="font-family: 'Century Gothic', Arial, sans-serif; font-size: 17px; line-height:30px; color: #1ab7ea; padding-bottom: 15px;padding-top: 10px;">
								Automotive<br>
								Contracting and Trades<br>
								Energy and Utilities<br>
								Food industry<br>
								Healthcare<br>
								Hospitality<br>
								HVAC and Plumbing<br>
							</p>
						</td>
						<td width="30"></td>
						<td width="255" style="text-align:left;">
							<p style="font-family: 'Century Gothic', Arial, sans-serif; font-size: 17px; line-height:30px; color: #1ab7ea; padding-bottom: 15px;padding-top: 10px;">
								Janitorial and Maintenance<br>
								Manufacturing<br>
								Retail and Specialty Stores<br>
								Supermarkets<br>
								Transportation and Warehousing<br>
								And more
							</p>
						</td>
						<td width="30"></td>
					</tr>
					<tr>
						<td colspan="3">&nbsp;</td>
					</tr>
				</table>
				<!--/list-->
			</td>
		</tr>
		<tr>
			<td>
				<!--blue text bar-->
				<table cellpadding="0" cellspacing="0" align="center" width="600" bgcolor="#005d9c">
					<tr>
						<td align="center" height="12"><p><span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 18px; line-height: 22px;text-align: center;color:white;font-weight: 600;"><?php echo $vars['headline_3']; ?></span></p></td>
					</tr>
				</table>
				<!--/blue text bar-->
			</td>
		</tr>
		<tr>
			<td>
				<!--list-->
				<table width="600" cellspacing="0" cellpadding="0" border="0" align="center" style="margin: 0 auto;" bgcolor="#ffffff">
					<tr>
						<td colspan="3">&nbsp;</td>
					</tr>
					<tr>
						<td width="30">&nbsp;</td>
						<td width="540">
							<span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 16px; line-height: 33px;">
								<p style="line-height: 25px;"><?php echo replace_characters($vars['para_4'], "<br>"); ?></p>
							</span>
						</td>
						<td width="30">&nbsp;</td>
					</tr>
				</table>
				<!--/list-->
			</td>
		</tr>
		<tr>
			<td>
				<!--white text bar-->
				<table cellpadding="0" cellspacing="0" align="center" width="600" bgcolor="#fff">
					<tr>
						<td align="center" height="12"><p><span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 20px; line-height: 22px;text-align: center;color:black;font-weight: 800;"><?php echo $vars['headline_4']; ?></span></p></td>
					</tr>
				</table>
				<!--/white text bar-->
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
							<span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 22px; line-height: 25px; color: #ffffff; text-decoration:none;">
								Fully Promoted <?php echo $active_location['companyname']; ?></span><br/>
							<?  if($vars['phone'] != ''){
									$phone = $vars['phone'];
								}elseif($vars['phone'] == '' && $active_location['phone'] != ''){
									$phone = $active_location['phone'];
								}else{
									$phone = '555-555-5555';
								}
							?>
							<span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 22px; line-height: 25px; font-weight: 600; "><a href="tel:<? echo $phone; ?>" style="color: #ffffff; text-decoration:none;"><? echo $phone; ?></a></span><br/>
							<span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 15px; line-height: 25px; color:white;"><a href="https://fullypromoted.com/" style="color: #ffffff; text-decoration:none;">Shop Now </a> | <a href="https://fullypromoted.com/contact/" style="color: #ffffff; text-decoration:none;"> Contact Us</a></span>
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