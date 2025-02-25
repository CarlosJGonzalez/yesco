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
<title>5k Race | Fully Promoted</title>
	
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
						<td align="center"><img src="<?php echo $vars['banner']; ?>" width="100%" alt="5k Banner" /></td>
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
						<td width="540"><span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 16px; line-height: 22px;"><p style="line-height: 25px;"><?php echo replace_characters($vars['para'], "<br>"); ?></p>
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
								<p style="line-height: 25px;"><?php echo $vars['para_2']; ?></p>
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
								<td width="142" style="text-align:center;">
									<img src="/promote/templates/img/5k/water-bottle.jpg" alt="5k Image" style="width:100%;max-width:88px;" width="88">
									<p style="font-family: 'Century Gothic', Arial, sans-serif; font-size: 17px; line-height:22px; color: #1ab7ea; padding-bottom: 15px;padding-top: 10px;">Reusable water bottles</p>
								</td>
								<td width="30"></td>
								<td width="142" style="text-align:center;">
									<img src="/promote/templates/img/5k/snack-pack.jpg" alt="5k Image" style="width:100%;max-width:88px;" width="88">
									<p style="font-family: 'Century Gothic', Arial, sans-serif; font-size: 17px; line-height:22px; color: #1ab7ea; padding-bottom: 15px;padding-top: 10px;">Snack packs with protein-rich snacks such as energy bars</p>
								</td>
								<td width="30"></td>
								<td width="142" style="text-align:center;">
									<img src="/promote/templates/img/5k/moisture-wicking.jpg" alt="5k Image" style="width:100%;max-width:88px;" width="88">
									<p style="font-family: 'Century Gothic', Arial, sans-serif; font-size: 17px; line-height:22px; color: #1ab7ea; padding-bottom: 15px;padding-top: 10px;">Moisture-wicking weather-appropriate apparel </p>
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
								<td width="142" style="text-align:center;">
									<img src="/promote/templates/img/hot-summer/sunscreen.jpg" alt="5k Image" style="width:100%;max-width:88px;" width="88">
									<p style="font-family: 'Century Gothic', Arial, sans-serif; font-size: 17px; line-height:22px; color: #1ab7ea; padding-bottom: 15px;padding-top: 10px;">Mini sunscreen bottles</p>
								</td>
								<td width="30"></td>
								<td width="142" style="text-align:center;">
									<img src="/promote/templates/img/hot-summer/hand-sanitizer.jpg" alt="5k Image" style="width:100%;max-width:88px;" width="88">
									<p style="font-family: 'Century Gothic', Arial, sans-serif; font-size: 17px; line-height:22px; color: #1ab7ea; padding-bottom: 15px;padding-top: 10px;">Mini hand sanitizer bottles</p>
								</td>
								<td width="30"></td>
								<td width="142" style="text-align:center;">
									<img src="/promote/templates/img/hot-summer/headphone-earbud-cases.jpg"  alt="5k Image" style="width:100%;max-width:88px;" width="88">
									<p style="font-family: 'Century Gothic', Arial, sans-serif; font-size: 17px; line-height:22px; color: #1ab7ea; padding-bottom: 15px;padding-top: 10px;">Earbuds</p>
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
								<td width="142" style="text-align:center;">
									<img src="/promote/templates/img/5k/armband.jpg" alt="5k Image" style="width:100%;max-width:88px;" width="88">
									<p style="font-family: 'Century Gothic', Arial, sans-serif; font-size: 17px; line-height:22px; color: #1ab7ea; padding-bottom: 15px;padding-top: 10px;">Armband for holding smartphone</p>
								</td>
								<td width="30"></td>
								<td width="142" style="text-align:center;">
									<img src="/promote/templates/img/tradeshow/drawstring-bag.jpg" alt="5k Image" style="width:100%;max-width:88px;" width="88">
									<p style="font-family: 'Century Gothic', Arial, sans-serif; font-size: 17px; line-height:22px; color: #1ab7ea; padding-bottom: 15px;padding-top: 10px;">Backpacks, duffle bags, drawstring bags, etc. </p>
								</td>
								<td width="30"></td>
								<td width="142" style="text-align:center;">
									<img src="/promote/templates/img/5k/microfiber.jpg" alt="5k Image" style="width:100%;max-width:88px;" width="88">
									<p style="font-family: 'Century Gothic', Arial, sans-serif; font-size: 17px; line-height:22px; color: #1ab7ea; padding-bottom: 15px;padding-top: 10px;">Microfiber sweat towels</p>
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
						<td align="center" height="12"><p><span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 20px; line-height: 22px;text-align: center;color:black;font-weight: 800;"><?php echo str_replace("[[company_name]]",$active_location['companyname'],$vars['para_3']); ?></span></p></td>
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