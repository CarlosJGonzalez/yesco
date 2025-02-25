<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>We Need to Verify Your Information!</title>
	
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
<?  include ($_SERVER['DOCUMENT_ROOT'].'/includes/connect.php');
$database=strtolower($client); ?>
<body>
<?
if($_GET['storeid']) $storeid=$_GET['storeid'];
$sql="select * from ".$database.".storelogin where storeid='".$storeid."'";
$row = $db->rawQueryOne($sql);
if ($db->count > 0){
	$uname = $row['email'];
	$pwd = $row['password'];
	$token = $row['token'];
}

$sql="select * from ".$database.".locationlist where storeid='".$storeid."'";
$row = $db->rawQueryOne($sql);
if ($db->count > 0){

	$companyname = 'Fully Promoted '.$row['companyname'];
	$address = $row['address'];
	$address2 = $row['address2'];
	if ($address2 <> '') $address .= ', ' . $address2;
	$city = $row['city'];
	$state = $row['state'];
	$zip = $row['zip'];
	$phone = $row['phone'];
	$website = 'https://fullypromoted.com/locations/'.$row['url'].'/';
	$facebook = $row['facebook'];
	$twitter = $row['twitter'];
	$linkedin = $row['linkedin'];
	$instagram = $row['instagram'];
}

?>
<body>
	<!--bg-->
	<table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#dedede" align="center" style="margin: 0 auto;">
		<tr>
			<td height="40">&nbsp;</td>
		</tr>
		<tr>
			<td>
				<!--header-->
				<table width="600" cellspacing="0" cellpadding="0" border="0" align="center" style="margin: 0 auto;">
					<tr>
						<td><img src="https://localfullypromoted.com/emails/img/logo-das.png" alt="DAS Group Logo" style="display: block;" /></td>
						<td align="right"><img src="https://localfullypromoted.com/emails/img/logo-fp.png" alt="Fully Promoted Logo" style="display: block;" /></td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="2"><img src="https://localfullypromoted.com/emails/img/verify.jpg" alt="We Need to Verify Your Information!" style="display: block;" /></td>
					</tr>
				</table> 
				<!--/header-->
			</td>
		</tr>
		<tr>
			<td>
				<!--body-->
				<table width="600" cellspacing="0" cellpadding="0" border="0" align="center" style="margin: 0 auto;" bgcolor="#ffffff">
					<tr>
						<td colspan="3" height="30">&nbsp;</td>
					</tr>
					<tr>
						<td width="30">&nbsp;</td>
						<td width="540"><span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 15px; line-height: 22px;">As part of your National Ad Fund program, DAS Group is responsible for submitting your local listing data to over 65 local directories including Google My Business, Yelp, Bing, Apple Maps and many more. <strong style="color: #0068b3;">Making sure your location data is displayed correctly across the web is critical to your local search presence success.</strong><br><br>To ensure we have the correct information on file for your location please take a moment to review the information below and complete your location profile on <a style="color: #0068b3;" href="https://localfullypromoted.com/" target="_blank">LocalFullyPromoted.com</a> by <strong style="color: #0068b3;">Friday, December 20th</strong>. The more information you provide the more opportunities for potential customers to find you.</span></td>
						<td width="30">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="3" height="30">&nbsp;</td>
					</tr>
					<tr bgcolor="#1ab7ea">
						<td colspan="3" height="50" align="center"><span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 22px; line-height: 22px; color: #ffffff;">Please Review the Information Below</span></td>
					</tr>
					<tr>
						<td colspan="3" height="30">&nbsp;</td>
					</tr>
					<tr>
						<td width="30">&nbsp;</td>
						<td width="540">
						
							<!--info-->
							<table width="540" cellspacing="0" cellpadding="0" border="0" align="center" style="margin: 0 auto;">
								<tr>
									<td width="40" align="center"><img src="https://localfullypromoted.com/emails/img/icon-company.jpg" style="display: block;" /></td>
									<td width="10">&nbsp;</td>
									<td width="490"><span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 16px; line-height: 22px;"><?=$companyname;?></span></td>
								</tr>
								<tr>
									<td style="border-bottom: 1px solid #dedede;" colspan="3">&nbsp;</td>
								</tr>
								<tr>
									<td colspan="3">&nbsp;</td>
								</tr>
								<tr>
									<td width="40" align="center"><img src="https://localfullypromoted.com/emails/img/icon-marker.jpg" style="display: block;" /></td>
									<td width="10">&nbsp;</td>
									<td width="490"><span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 16px; line-height: 22px;"><?=$address;?><br><?=$city;?>, <?=$state;?> <?=$zip;?></span></td>
								</tr>
								<tr>
									<td style="border-bottom: 1px solid #dedede;" colspan="3">&nbsp;</td>
								</tr>
								<tr>
									<td colspan="3">&nbsp;</td>
								</tr>
								<tr>
									<td width="40" align="center"><img src="https://localfullypromoted.com/emails/img/icon-phone.jpg" style="display: block;" /></td>
									<td width="10">&nbsp;</td>
									<td width="490"><span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 16px; line-height: 22px;"><?=$phone;?></span></td>
								</tr>
								<tr>
									<td style="border-bottom: 1px solid #dedede;" colspan="3">&nbsp;</td>
								</tr>
								<tr>
									<td colspan="3">&nbsp;</td>
								</tr>
								<tr>
									<td width="40" align="center"><img src="https://localfullypromoted.com/emails/img/icon-link.jpg" style="display: block;" /></td>
									<td width="10">&nbsp;</td>
									<td width="490"><span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 16px; line-height: 22px;"><a href="<?=$website;?>" style="color: #000000; text-decoration: none;" target="_blank"><?=$website;?></a></span></td>
								</tr>
								<tr>
									<td style="border-bottom: 1px solid #dedede;" colspan="3">&nbsp;</td>
								</tr>
								<tr>
									<td colspan="3" height="30">&nbsp;</td>
								</tr>
								<tr>
									<td width="40" align="center"><img src="https://localfullypromoted.com/emails/img/icon-facebook.jpg" style="display: block;" /></td>
									<td width="10">&nbsp;</td>
									<td width="490"><span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 16px; line-height: 22px;"><a href="<?=$facebook;?>" style="color: #000000; text-decoration: none;" target="_blank"><?=$facebook;?></a></span></td>
								</tr>
								<tr>
									<td style="border-bottom: 1px solid #dedede;" colspan="3">&nbsp;</td>
								</tr>
								<tr>
									<td colspan="3" height="30">&nbsp;</td>
								</tr>
								<tr>
									<td width="40" align="center"><img src="https://localfullypromoted.com/emails/img/icon-twitter.jpg" style="display: block;" /></td>
									<td width="10">&nbsp;</td>
									<td width="490"><span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 16px; line-height: 22px;"><a href="<?=$twitter;?>" style="color: #000000; text-decoration: none;" target="_blank"><?=$twitter;?></a></span></td>
								</tr>
								<tr>
									<td style="border-bottom: 1px solid #dedede;" colspan="3">&nbsp;</td>
								</tr>
								<tr>
									<td colspan="3" height="30">&nbsp;</td>
								</tr>
								<tr>
									<td width="40" align="center"><img src="https://localfullypromoted.com/emails/img/icon-linkedin.jpg" style="display: block;" /></td>
									<td width="10">&nbsp;</td>
									<td width="490"><span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 16px; line-height: 22px;"><a href="<?=$linkedin;?>" style="color: #000000; text-decoration: none;" target="_blank"><?=$linkedin;?></a></span></td>
								</tr>
								<tr>
									<td style="border-bottom: 1px solid #dedede;" colspan="3">&nbsp;</td>
								</tr>
								<tr>
									<td colspan="3" height="30">&nbsp;</td>
								</tr>
								<tr>
									<td width="40" align="center"><img src="https://localfullypromoted.com/emails/img/icon-instagram.jpg" style="display: block;" /></td>
									<td width="10">&nbsp;</td>
									<td width="490"><span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 16px; line-height: 22px;"><a href="<?=$instagram;?>" style="color: #000000; text-decoration: none;" target="_blank"><?=$instagram;?></a></span></td>
								</tr>
								<tr>
									<td style="border-bottom: 1px solid #dedede;" colspan="3">&nbsp;</td>
								</tr>
								<tr>
									<td colspan="3" height="30">&nbsp;</td>
								</tr>
							</table>
							<!--/info-->
						
						</td>
						<td width="30">&nbsp;</td>
					</tr>
					<tr>
						<td width="30">&nbsp;</td>
						<td width="540" align="center"><span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 15px; line-height: 22px;"><em>If the above information is correct, you don't have to take any further steps. If you need to make changes, please log in using the button below.</em></span><br><br><a href="https://localfullypromoted.com/xt_login.php?token=<?=$token?>"><img src="https://localfullypromoted.com/emails/img/btn-update.jpg" alt="Verify Information" style="display: block;" /></a></td>
						<td width="30">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="3" height="40" style="border-bottom: 10px solid #dedede;">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="3">
						
							<!--contact-->
							<table width="600" cellspacing="0" cellpadding="0" border="0" align="center" style="margin: 0 auto;" bgcolor="#0068b3">
								<tr>
									<td colspan="5" height="30">&nbsp;</td>
								</tr>
								<tr>
									<td width="30">&nbsp;</td>
									<td width="48" valign="middle"><img src="https://localfullypromoted.com/emails/img/question-mark.png" width="48" style="display: block;"/></td>
									<td width="20">&nbsp;</td>
									<td valign="middle"><span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 18px; line-height: 28px; color: #ffffff;">Any questions or concerns please contact DAS Group at <a style="color: #ffffff;" href="mailto:support@das-group.com" target="_blank">support@das-group.com</a>.</span></td>
									<td width="30">&nbsp;</td>
								</tr>	
								<tr>
									<td colspan="5" height="30">&nbsp;</td>
								</tr>
							</table>
							<!--/contact-->
							
						</td>
					</tr>
				</table> 
				<!--/body-->
			</td>
		</tr>
		<tr>
			<td height="40">&nbsp;</td>
		</tr>
	</table>
	<!--/bg-->
</body>
</html>
