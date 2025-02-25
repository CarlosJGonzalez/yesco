<?php
if(!isset($db)){
	include ($_SERVER['DOCUMENT_ROOT'].'/includes/connect.php');
	
}
include ($_SERVER['DOCUMENT_ROOT'].'/includes/functions.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/includes/ClassDasReview.php');



$storeid = $db->escape($_POST['storeid']);
$client = $db->escape($_POST['client']);

//$das_review = new Das_Review($db,'0004','10398');
$das_review = new Das_Review($db,$client,$storeid);

$google_url = $das_review->getLinkGoogle();
$fb_url = $das_review->getLinkFB();
?>

<!doctype html>
	<html>
	<head>
		<meta charset="utf-8">
		<title>Invitation</title>
		<style>
			body {
				background: #dfdede;
				height: 100%;
				width: 100%;
				margin: 0;
				padding: 0;
			}
		</style>
	</head>

	<body>
		<table cellpadding="0" cellspacing="0" align="center" width="100%" bgcolor="dfdede">
			<tr>
				<td height="30">&nbsp;</td>
			</tr>
			<tr>
				<td>
					<!--top-->
					<table cellpadding="0" cellspacing="0" align="center" width="600" style="margin: 0 auto;">
						<tr bgcolor="#ffffff">
							<td> <img src="<?php echo getFullUrl() ?>/emails/img/logo-left.jpg" width="305" style="display: block;"/> </td>
							<td width="273" align="center"><span style="font-family: \'Century Gothic\', Helvetica, Arial, sans-serif; font-size: 12px; line-height: 14px; color: #0067b1;"><strong><?php echo $_POST['vars']['name']?></strong>, <?php echo $_POST['vars']['companyname']?></span>
							</td>
							<td width="22"> <img src="<?php echo getFullUrl(); ?>/emails/img/header-right.jpg" width="22" style="display: block;"/> </td>
						</tr>
						<tr>
							<td>&nbsp;</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<table cellpadding="0" cellspacing="0" align="center" width="600" style="margin: 0 auto;" bgcolor="#0067b1">
						<tr>
							<td colspan="3"><img src="<?php echo getFullUrl(); ?>/emails/img/body-top.jpg" style="display: block;"/>
							</td>
						</tr>
						<tr>
							<td colspan="3" height="40">&nbsp;</td>
						</tr>
						<tr>
							<td colspan="3" align="center"><img src="<?php echo getFullUrl(); ?>/emails/assets/review-comments.png" style="display: block;"/>
							</td>
						</tr>
						<tr>
							<td colspan="3" height="30">&nbsp;</td>
						</tr>
						<tr>
							<td colspan="3" align="center"><img src="<?php echo getFullUrl(); ?>/emails/assets/review-h1.png" style="display: block;"/>
							</td>
						</tr>
						<tr>
							<td colspan="3" height="30">&nbsp;</td>
						</tr>
						<tr>
							<td width="30">&nbsp;</td>
							<td width="540" align="center"><span style="font-family: Century Gothic, Helvetica, Arial, sans-serif; font-size: 17px; line-height: 28px; color: #ffffff;"><strong style="font-size: 24px; line-height: 34px;"><?php echo $_POST['vars']['header']?></strong><br><br><?php echo $_POST['vars']['body']?></span>
							</td>
							<td width="30">&nbsp;</td>
						</tr>
						<tr>
							<td colspan="3" height="30">&nbsp;</td>
						</tr>
						<tr>
							<td colspan="3" align="center">
								<?php if($fb_url != ''){?>
								<a href="<?= $fb_url ?>"><img src="<?php echo getFullUrl(); ?>/emails/img/facebook.png" style="display: inline-block;"/></a>
								<?}?>
								&nbsp;&nbsp;&nbsp;
								<?php if($google_url != ''){?>
								<a href="<?=$google_url?>"><img src="<?php echo getFullUrl(); ?>/emails/img/google.png" style="display: inline-block;"/></a>
								<?}?>
							</td>
						</tr>
						<tr>
							<td colspan="3" height="50">&nbsp;</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<table cellpadding="0" cellspacing="0" align="center" width="600" style="margin: 0 auto;" bgcolor="dfdede">
						<tr>
							<td colspan="5"><img src="<?php echo getFullUrl(); ?>/emails/img/body-bottom.jpg" style="display: block;"/>
							</td>
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