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
<title>Wedding | Fully Promoted</title>
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
						<td align="center"><img src="<?php echo $vars['banner']; ?>" width="100%" alt="Wedding Favor Banner" /></td>
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
						<td width="540"><span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 16px; line-height: 22px;"><p style="line-height: 25px;"><?php echo replace_characters($vars['para'], "<br>"); ?><br><br>
						<?php echo replace_characters($vars['para2'], "<br>"); ?></p>
						</span></td>
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
						<td align="center" height="12"><p><span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 22px; line-height: 22px;text-align: center;color:white;font-weight: 600;"><?php echo $vars['headline']; ?></span></p></td>
					</tr>
				</table>
				<!--/blue text bar-->
			</td>
		</tr>
		<tr>
			<td>
				<!--full width img-->
				<table cellpadding="0" cellspacing="0" align="center" width="600" bgcolor="#FFFFFF">
					<tr>
						<td align="center"><img src="https://localfullypromoted.com/promote/templates/img/wedding-full-width-img.jpg" width="100%" alt="Wedding Favor Ideas" /></td>
					</tr>
				</table>
				<!--/full width img-->
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
								<p style="line-height: 25px;"><?php echo str_replace("[[company_name]]",$active_location['companyname'],$vars['para_3']); ?></p>
							</span>
						</td>
						<td width="30">&nbsp;</td>
					</tr>
					<tr>
						<td width="30">&nbsp;</td>
						<td width="540">
							<span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 16px; line-height: 33px;">
								<p style="line-height: 25px;"><span style="font-weight: bold;color:#005d9c;">Candy, cookies & snacks</span> – choose something special that represents the couple in a customized bag or box featuring their monogram and a note that says “Midnight Snack”<br><br>
								<span style="font-weight: bold;color:#005d9c;">Tote bags, sunglasses, sunscreen, etc.</span> – customized tote bags and other must-haves are a perfect party favor for beach weddings <br><br>
								<span style="font-weight: bold;color:#005d9c;">Wine bottle opener and stoppers</span> – for the couple getting married at a vineyard, give guests a practical yet memorable keepsake<br><br>
								<span style="font-weight: bold;color:#005d9c;">Hand sanitizer, hand lotion, lip balm, tissue, water bottles, etc.</span> – we can add personalized labels and create a small care package with these and other essentials to help guests recover from a long celebration<br><br>
								<span style="font-weight: bold;color:#005d9c;">Coffee and tea gift boxes</span> – for the couple that loves their hot beverages, these can be customized with a wedding logo or monogram<br><br>
								<span style="font-weight: bold;color:#005d9c;">Bottle openers</span> – available in a wide array of styles, this is perfect for the couple getting married at a brewery
								</p>
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
		<tr>
			<td>
				<!--white text bar-->
				<table cellpadding="0" cellspacing="0" align="center" width="600" bgcolor="#fff">
					<tr>
						<td width="20">&nbsp;</td>
						<td width="560" align="center" height="12"><p><span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 20px; line-height: 22px;text-align: center;color:black;font-weight: 800;"><?php echo replace_characters($vars['para_4'], "<br>"); ?></span></p>
						<a href="https://fullypromoted.com/locations/<?php echo $active_location['url'] ?>/get-a-quote/"><img src="https://localfullypromoted.com/promote/templates/img/shop-btn.jpg" alt="Contact Us" style="width:100%;margin-bottom: 15px;" width="400"></a>
						</td>
						<td width="20">&nbsp;</td>
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
							<span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 15px; line-height: 25px; color:white;"><a href="<?php echo $vars['shop_url']; ?>" style="color: #ffffff; text-decoration:none;">Shop Now </a> | <a href="<?php echo $vars['contact_url']; ?>" style="color: #ffffff; text-decoration:none;"> Contact Us</a></span>
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