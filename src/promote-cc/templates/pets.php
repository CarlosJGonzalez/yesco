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
<title>Adopt a Pet | Fully Promoted</title>
	
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
						<td align="center">
							<img src="<?php echo $vars['banner']; ?>" width="100%" alt="Pets Banner" />
						</td>
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
						<td width="540"><span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 16px; line-height: 22px;"><p style="line-height: 25px;"><?php echo str_replace("[[company_name]]",$active_location['companyname'],replace_characters($vars['para'], "<br>")); ?></p>
						<p style="line-height: 25px;"><?php echo replace_characters($vars['para_2'], "<br>"); ?></p>
						<p style="line-height: 25px;">
						<?php echo str_replace("[[company_name]]",$active_location['companyname'],replace_characters($vars['para_3'], "<br>")); ?></p></span></td>
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
					<td colspan="2" style="padding: 0px 20px;">
						<table cellpadding="0" cellspacing="0">
							<tbody><tr>
								<td width="100" style="text-align:center;">
									<img src="/promote/templates/img/pet-bowl.jpg" alt="Pet Image" style="width:100%;max-width:88px;" width="88">
								</td>
								<td width="170" style="text-align:left;">
									<p> <a href="https://shop.fullypromoted.com/promo-products/344/Pet-Bowls.html?promocode=%5b%5bpromocode%5d%5d"><span style="font-family: 'Century Gothic', Arial, sans-serif; font-size: 14px; line-height:17px; color: #1ab7ea; padding-bottom: 15px;padding-top: 10px;">Pet Bowls</span></a><br>
									<span p style="font-family: 'Century Gothic', Arial, sans-serif; font-size: 13px; line-height:17px;padding-bottom: 15px;padding-top: 10px;">We have collapsible and travel bowls for those who are always on the go.</span></p>
								</td>
								<td width="10"></td>
								<td width="100" style="text-align:center;">
									<img src="/promote/templates/img/leash.jpg" alt="Pet Image" style="width:100%;max-width:88px;" width="88">
								</td>
								<td width="170" style="text-align:left;">
									<p> <a href="https://shop.fullypromoted.com/promo-products/341/Pet-Accessories.html?promocode=%5b%5bpromocode%5d%5d"><span style="font-family: 'Century Gothic', Arial, sans-serif; font-size: 14px; line-height:17px; color: #1ab7ea; padding-bottom: 15px;padding-top: 10px;">Pet Accessories </span></a><br>
									<span p style="font-family: 'Century Gothic', Arial, sans-serif; font-size: 13px; line-height:17px;padding-bottom: 15px;padding-top: 10px;">We’ve got everything from collars and leashes to jars with treats, pet wipes, and food scoopers. </span></p>
								</td>
							</tr>
						</tbody>
						</table>
					</td>
				</tr>

				<tr>
					<td colspan="2" style="padding: 0px 20px;">
						<table cellpadding="0" cellspacing="0">
							<tbody><tr>
								<td width="100" style="text-align:center;">
									<img src="/promote/templates/img/dog-toy.jpg" alt="Pet Image" style="width:100%;max-width:88px;" width="88">
								</td>
								<td width="170" style="text-align:left;">
									<p> <a href="https://shop.fullypromoted.com/promo-products/350/Pet-Toys.html?promocode=%5b%5bpromocode%5d%5d"><span style="font-family: 'Century Gothic', Arial, sans-serif; font-size: 14px; line-height:17px; color: #1ab7ea; padding-bottom: 15px;padding-top: 10px;">Pet Toys </span></a><br>
									<span p style="font-family: 'Century Gothic', Arial, sans-serif; font-size: 13px; line-height:17px;padding-bottom: 15px;padding-top: 10px;">Every furbaby needs that special toy – let us add your logo to it.</span></p>
								</td>
								<td width="10"></td>
								<td width="100" style="text-align:center;">
									<img src="/promote/templates/img/pet-bandana.jpg" alt="Corporate Gift Image" style="width:100%;max-width:88px;" width="88">
								</td>
								<td width="170" style="text-align:left;">
									<p> <a href="https://shop.fullypromoted.com/promo-products/785/Pet-Grooming.html?promocode=%5b%5bpromocode%5d%5d"><span style="font-family: 'Century Gothic', Arial, sans-serif; font-size: 14px; line-height:17px; color: #1ab7ea; padding-bottom: 15px;padding-top: 10px;">Pet Apparel & Bandanas </span></a><br>
									<span p style="font-family: 'Century Gothic', Arial, sans-serif; font-size: 13px; line-height:17px;padding-bottom: 15px;padding-top: 10px;">Every furry friend’s wardrobe needs to have your business logo. </span></p>
								</td>
							</tr>
						</tbody>
						</table>
					</td>
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
						<td align="center" height="12" style="padding: 10px 20px;"><p><span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 20px; line-height: 22px;text-align: center;color:black;font-weight: 800;"><?php echo str_replace("[[company_name]]",$active_location['companyname'],replace_characters($vars['headline_2'], "<br>")); ?></span></p>
						<a href="<?php echo $vars['contact_url']; ?>"><img src="/promote/templates/img/corporate-gifts/contact-us.jpg" alt="Contact Us" style="width:100%;max-width:180px;margin-bottom: 15px;" width="50"></a></td>
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