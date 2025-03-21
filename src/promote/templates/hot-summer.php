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
<title>Hot Summer | Fully Promoted</title>
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
						<td align="center"><img src="<?php echo $vars['banner']; ?>" width="100%" alt="Hot Summer Banner" /></td>
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
						<span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 16px; line-height: 22px;"><p style="line-height: 25px;"><?php echo replace_characters($vars['para'], "<br>"); ?></p>
						<p style="line-height: 25px;">
						<?php echo replace_characters($vars['para_2'], "<br>"); ?>
						</p>
						</span>
						</td>
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
								<p style="line-height: 25px;"><?php echo replace_characters($vars['para_3'], "<br>"); ?></p>
							</span>
						</td>
						<td width="30">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="3">&nbsp;</td>
					</tr>
					<tr>
					<td colspan="2" style="padding: 0px 40px;">
						<table cellpadding="0" cellspacing="0">
							<tbody><tr>
								<td width="100" style="text-align:center;">
									<img src="/promote/templates/img/hot-summer/tote-bags.jpg" alt="Hot Summer Image" style="width:100%;max-width:88px;" width="88">
									<p style="font-family: 'Century Gothic', Arial, sans-serif; font-size: 17px; line-height:22px; color: #1ab7ea; padding-bottom: 15px;padding-top: 10px;">Tote bags</p>
								</td>
								<td width="30"></td>
								<td width="100" style="text-align:center;">
									<img src="/promote/templates/img/hot-summer/sunscreen.jpg" alt="Hot Summer Image" style="width:100%;max-width:88px;" width="88">
									<p style="font-family: 'Century Gothic', Arial, sans-serif; font-size: 17px; line-height:22px; color: #1ab7ea; padding-bottom: 15px;padding-top: 10px;">Sunscreen</p>
								</td>
								<td width="30"></td>
								<td width="100" style="text-align:center;">
									<img src="/promote/templates/img/hot-summer/sunglasses.jpg" alt="Hot Summer Image" style="width:100%;max-width:88px;" width="88">
									<p style="font-family: 'Century Gothic', Arial, sans-serif; font-size: 17px; line-height:22px; color: #1ab7ea; padding-bottom: 15px;padding-top: 10px;">Sunglasses</p>
								</td>
								<td width="30"></td>
								<td width="100" style="text-align:center;">
									<img src="/promote/templates/img/hot-summer/visor-and-caps.jpg" alt="Hot Summer Image" style="width:100%;max-width:88px;" width="88">
									<p style="font-family: 'Century Gothic', Arial, sans-serif; font-size: 17px; line-height:22px; color: #1ab7ea; padding-bottom: 15px;padding-top: 10px;">Visors and caps</p>
								</td>
							</tr>
						</tbody>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="padding: 0px 40px;">
						<table cellpadding="0" cellspacing="0">
							<tbody><tr>
								<td width="100" style="text-align:center;">
									<img src="/promote/templates/img/hot-summer/beach-balls.jpg" alt="Hot Summer Image" style="width:100%;max-width:88px;" width="88">
									<p style="font-family: 'Century Gothic', Arial, sans-serif; font-size: 17px; line-height:22px; color: #1ab7ea; padding-bottom: 15px;padding-top: 10px;">Beach balls</p>
								</td>
								<td width="30"></td>
								<td width="100" style="text-align:center;">
									<img src="/promote/templates/img/hot-summer/hand-sanitizer.jpg" alt="Hot Summer Image" style="width:100%;max-width:88px;" width="88">
									<p style="font-family: 'Century Gothic', Arial, sans-serif; font-size: 17px; line-height:22px; color: #1ab7ea; padding-bottom: 15px;padding-top: 10px;">Hand sanitizer</p>
								</td>
								<td width="30"></td>
								<td width="100" style="text-align:center;">
									<img src="/promote/templates/img/hot-summer/frisbees.jpg" alt="Hot Summer Image" style="width:100%;max-width:88px;" width="88">
									<p style="font-family: 'Century Gothic', Arial, sans-serif; font-size: 17px; line-height:22px; color: #1ab7ea; padding-bottom: 15px;padding-top: 10px;">Frisbees</p>
								</td>
								<td width="30"></td>
								<td width="100" style="text-align:center;">
									<img src="/promote/templates/img/hot-summer/headphone-earbud-cases.jpg" alt="Hot Summer Image" style="width:100%;max-width:88px;" width="88">
									<p style="font-family: 'Century Gothic', Arial, sans-serif; font-size: 17px; line-height:22px; color: #1ab7ea; padding-bottom: 15px;padding-top: 10px;">Headphone or earbud cases</p>
								</td>
							</tr>
							
							</tbody>
							</table>
						</td>
					</tr>
					<tr>
					<td colspan="2" style="padding: 0px 40px;">
						<table cellpadding="0" cellspacing="0">
							<tbody><tr>
								<td width="100" style="text-align:center;">
									<p style="font-family: 'Century Gothic', Arial, sans-serif; font-size: 17px; line-height:22px; color: #1ab7ea; padding-bottom: 15px;padding-top: 10px;"> </p>
								</td>
								<td width="30"></td>
								<td width="100" style="text-align:center;">
									<img src="/promote/templates/img/hot-summer/pool-floats.jpg" alt="Hot Summer Image" style="width:100%;max-width:88px;" width="88">
									<p style="font-family: 'Century Gothic', Arial, sans-serif; font-size: 17px; line-height:22px; color: #1ab7ea; padding-bottom: 15px;padding-top: 10px;">Pool floats</p>
								</td>
								<td width="30"></td>
								<td width="100" style="text-align:center;">
									<img src="/promote/templates/img/hot-summer/towels.jpg" alt="Hot Summer Image" style="width:100%;max-width:88px;" width="88">
									<p style="font-family: 'Century Gothic', Arial, sans-serif; font-size: 17px; line-height:22px; color: #1ab7ea; padding-bottom: 15px;padding-top: 10px;">Towels</p>
								</td>
								<td width="30"></td>
								<td width="100" style="text-align:center;">
									<p style="font-family: 'Century Gothic', Arial, sans-serif; font-size: 17px; line-height:22px; color: #1ab7ea; padding-bottom: 15px;padding-top: 10px;">  </p>
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
						<td align="center" height="12"><p><span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 20px; line-height: 22px;text-align: center;color:black;font-weight: 800;"><?php echo $vars['headline_2']; ?></span></p></td>
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