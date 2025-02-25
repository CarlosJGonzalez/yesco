<?php
include_once  ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");

$db->where("template_name",basename(__FILE__, '.php'));
$cols = Array("field_name","default_text");
$default_vars_a = $db->get("email_template_fields",null,$cols);

foreach ($default_vars_a as $field){
	$value = $db->escape ($field['default_text']);
	$default_vars[$field['field_name']] = $value;
}

//echo '<pre>'; print_r($_POST); echo '</pre>';

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

//echo "<pre>";var_dump($vars);echo "</pre>";
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Template</title>
	
	<style>
		body {
			margin: 0; padding: 0; background: #efefef;
		}
	</style>
	
</head>

<body>
	
	<table cellpadding="0" cellspacing="0" align="center" style="margin: 0 auto;" bgcolor="#efefef">
		<tr>
			<td height="50">&nbsp;</td>
		</tr>
		<tr>
			<td>
				<!--body-->
				<table cellpadding="0" cellspacing="0"  width="600" align="center" style="margin: 0 auto;" bgcolor="#ffffff">
					<tr>
						<td>
							<!--header-->
							<table cellpadding="0" cellspacing="0"  width="600" align="center" style="margin: 0 auto;">
								<tr bgcolor="#0067B1">
									<td valign="middle" height="100" align="center"><img src="https://localfullypromoted.com/img/FP-logo-white.png" alt="Fully Promoted Logo" /></td>
								</tr>
								<tr>
									<td><img src="<?php echo $vars['banner']; ?>" alt="Banner" style="width:100%;max-width:600px;display: block;" width="600" /></td>
								</tr>
							</table>
							<!--/header-->
						</td>
					</tr>
					<tr>
						<td>
							<!--main content-->
							<table cellpadding="0" cellspacing="0"  width="600" align="center" style="margin: 0 auto;">
								<tr>
									<td colspan="3" height="30">&nbsp;</td>
								</tr>
								<tr>
									<td width="30"></td>
									<td width="540"><span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px;"><strong style="font-size: 34px; line-height: 40px; color: #0067B1;"><?php echo $vars['title']; ?></strong><br><br><?php echo $vars['para']; ?></span></td>
									<td width="30"></td>
								</tr>
								<tr>
									<td colspan="3" height="30">&nbsp;</td>
								</tr>
							</table>
							<!--/main content-->
						</td>
					</tr>
					<tr>
						<td>
							<!--secondary content-->
							<table cellpadding="0" cellspacing="0"  width="600" align="center" style="margin: 0 auto;" bgcolor="#dedede">
								<tr>
									<td colspan="3" height="30">&nbsp;</td>
								</tr>
								<tr>
									<td width="30"></td>
									<td width="295" valign="top"><span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 14px; line-height: 20px;"><strong style="font-size: 18px; color: #0067B1;"><?php echo $vars['h2']; ?></strong><br><?php echo $vars['para2']; ?></span></td>
									<td width="20"></td>
									<td width="225" valign="top"><img src="<?php echo $vars['image']; ?>" style="border: 1px solid #333;" width="225" /></td>
									<td width="30"></td>
								</tr>
								<tr>
									<td colspan="3" height="30">&nbsp;</td>
								</tr>
							</table>
							<!--/secondary content-->
						</td>
					</tr>
					<tr bgcolor="#333333">
						<td align="center" height="40">
							<span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 12px; line-height: 24px; color: #ffffff;">&copy; Fully Promoted. All rights reserved.</span>
						</td>
					</tr>
				</table>
				<!--/body-->
			</td>
		</tr>
		<tr>
			<td height="50">&nbsp;</td>
		</tr>
	</table>
	
</body>
</html>
