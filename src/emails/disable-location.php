<?php
include_once ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Email Template</title>

	<style>
		body {
			width: 100%;
			height: 100%;
			margin: 0;
			padding: 0;
		}
	</style>	
</head>

<body>
	<!--wrapper-->
	<table cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" width="100%" align="center" style="margin: 0 auto;">
		<tr>
			<td>
				<!--header-->
				<table cellpadding="0" cellspacing="0" align="center" width="600" bgcolor="#0067b1">
					<tr>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td align="center"><img src="%%LOCAL_CLIENT_URL%%img/FP-logo-white.png" width="50%" alt="%%CLIENT_NAME%% Logo" /></td>			
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
				</table>
				<!--/header-->
			</td>
		</tr>
		<tr>
			<td>
				<!--body-->
				<table cellpadding="0" cellspacing="0" align="center" width="600" bgcolor="#f3f2f2">
					<tr>
						<td colspan="3" height="40">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="3" align="center"><span style="font-family: Arial, Helvetica, sans-serif; font-size: 24px; font-weight: bold;text-align:center;">%%SUBJECT%%</span></td>			
					</tr>
					<tr>
						<td colspan="3">&nbsp;</td>
					</tr>
					<tr>
						<td width="30"></td>
						<td width="540" align="center">
							<span style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22px;text-align:center;">This email includes location details about this transaction.</span><br><br>
							<span style="font-family: Arial, Helvetica, sans-serif; font-size: 20px; font-weight: bold;text-align:center;color:#333333">Location Information</span><br><br>
							<span style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22px;text-align:center;font-weight:bold;">Updated By:</span>
							<span style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22px;text-align:center;"> %%UPDATED_BY%%</span><br>
							<span style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22px;text-align:center;font-weight:bold;">Store Id:</span>
							<span style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22px;text-align:center;"> %%STORE_ID%%</span><br>
							<span style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22px;text-align:center;font-weight:bold;">Business Name:</span>
							<span style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22px;text-align:center;"> %%BUSINESS_NAME%%</span><br>
							<span style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22px;text-align:center;font-weight:bold;">Primary Location email:</span>
							<span style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22px;text-align:center;"> %%PRIMARY_LOC_EMAIL%%</span><br>
						</td>
						<td width="30"></td>
					</tr>
					<tr>
						<td colspan="3">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="3" align="center"><a href="%%URL_TOKEN_ACCESS%%" target="_blank"><img src="%%LOCAL_CLIENT_URL%%img/login-btn.jpg" width="102" height="40" alt="Login" title="Login" /></a></td>			
					</tr>
					<tr>
						<td colspan="3" height="40">&nbsp;</td>
					</tr>
				</table>
				<!--/body -->
			</td>
		</tr>
		<tr>
			<td>
				<!--footer-->
				<table cellpadding="0" cellspacing="0" align="center" width="600" bgcolor="#333333">
					<tr>
						<td height="40" align="center"><span style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; line-height: 22px; color: #ffffff;">Copyright &copy; %%YEAR%% %%CLIENT_NAME%%. All rights reserved.</span></td>			
					</tr>
				</table>
				<!--/footer-->
			</td>
		</tr>
	</table>
	<!--/wrapper-->
</body>
</html>
