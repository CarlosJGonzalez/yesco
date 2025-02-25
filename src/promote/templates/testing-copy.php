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
<title>Testing</title>

<style>
body {
	background: #e6e6e6;
	height: 100%;
	width: 100%;	
	margin: 0;
	padding: 0;
}
</style>

</head>
<table cellpadding="0" cellspacing="0" align="center" width="100%" bgcolor="bdb7b7">
    <tr>
    	<td>
        	<table cellpadding="0" cellspacing="0" align="center" width="600" style="background:#FFF">
        		<tr style="background-color:#222222;">
                	<td colspan="4">&nbsp;</td>
                </tr>
                <tr style="background-color:#222222;">
                	<td width="20"></td>
                	<td height="50"><img src="http://localfullypromoted.com/img/FP-logo-white.png" height="50" alt="Signarama Logo"/></td>
                    <td height="50" align="right"><span style="font-weight: bold; font-size: 24px; color: #ffffff; font-family: 'Century Gothic', Arial, Sans-serif; margin: 0; padding-top: 30px;"><?php echo $vars['phone']; ?></span></td>
                    <td width="20"></td>
                </tr>
                <tr style="background-color:#222222;">
                	<td colspan="4">&nbsp;</td>
                </tr>
                <tr>
                	<td align="center" colspan="4"><img src="<?php echo $vars['banner']; ?>" style="width:100%;max-width:600px;" width="600"></td>
                </tr>
                
            </table>
        </td>
    </tr>
    <tr>
    	<td>
        	<table cellpadding="0" cellspacing="0" align="center" width="600" style="background:#FFF">
                <tr>
                	<td colspan="2" style="padding:20px;"><p style="font-family: 'Century Gothic', Arial, sans-serif; font-size: 22px; color: #d1302f;"><strong><?php echo $vars['title']; ?></strong></p><p style="font-family: 'Century Gothic', Arial, sans-serif; font-size: 14px; line-height:22px; color: #000;"><?php echo $vars['para']; ?></p></td>
                </tr>
                
                <tr style="background: #d9e5e8;">
                	<td colspan="2" style="padding:20px;"><span style="font-family: 'Century Gothic', Arial, sans-serif; font-size: 18px; color: #000;"><strong><?php echo $vars['h2']; ?></strong></span></td>
                </tr>
                
                <tr style="background: #d9e5e8;vertical-align:top;">
                	<td style="padding:10px 20px;"><span style="font-family: 'Century Gothic', Arial, sans-serif; font-size: 14px; line-height:22px; color: #000;"><?php echo $vars['para2']; ?></span></td>
                    <td width="235" style="padding:10px 20px;"><img src="<?php echo $vars['image']; ?>" width="235" height="155" style="border:1px solid #000;"></td>
                </tr>
                <tr style="background-color:#d9e5e8;">
                	<td colspan="4">&nbsp;</td>
                </tr>
                
              <tr style="background-color:#333333;">
                	<td colspan="4" style="text-align:center; padding: 20px;"><span style="font-family: 'Century Gothic', Arial, sans-serif; font-size: 13px; line-height:22px; color: #FFF;"><strong><?php echo $vars['address']; ?></strong></span><br><span style="font-family: 'Century Gothic', Arial, sans-serif; font-size: 13px; line-height:22px; color: #FFF;">© <?php echo date("Y"); ?> Signarama. All rights reserved.</span></td>
                </tr>
                
          </table>
        </td>
    </tr>
 
    <tr>
    	<td colspan="3" height="40">&nbsp;</td>
    </tr>
</table>

</body>
</html>
