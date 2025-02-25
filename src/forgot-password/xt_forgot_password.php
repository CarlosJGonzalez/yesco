<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");

if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])){
	//your site secret key
	$secret = '6LdMzdoUAAAAAF6tUUPIhGeam0pKnUIqC8P9Ipsg';
	$verifyResponse = url_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['g-recaptcha-response'], $useragent='cURL', $headers=false, $follow_redirects=false, $debug=false);
	$responseData = json_decode($verifyResponse);
}

if($responseData->success){
	
	$user_email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);

	if(!empty($user_email)){
		if (filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
			$sql_user_exists = "SELECT * FROM ".$_SESSION['database'].".storelogin WHERE email='$user_email' limit 1";	
			$user_storelogin = $db->rawQueryOne($sql_user_exists);
				
			//If the user exists in the database, the script will continue
			if($db->count>0){
				
				//Get locations that aren't suspended in locationlist 
				$actives_locations = get_active_locations($user_storelogin['storeid']);
				
				//If there aren't any active stores assigned to the user, the system won't let him change his password
				if($actives_locations[0] !=''){
					$db->where("email",$user_email,"=");
					$user = $db->getOne('forgot_pass_login');
					
					if($db->count>0){
						$db->where('email', $user_email);
						
						$delete_info_ok = $db->delete('forgot_pass_login');
						
						if(!$delete_info_ok){
							pageRedirect("Something went wrong, please try again.", "error", "/forgot-password/");
						}
					}
						
					$sql_uuid = "SELECT replace(uuid(),'-','') as token;";
					$row_uuid = $db->rawQuery ($sql_uuid);
					$token_db = $row_uuid[0]["token"];
					$token = checkToken('forgot_pass_login', $token_db);

					//Data for storelogin
					$data_temporal_user = array("email"=>$user_email, "token"=>$token, "created_date"=>$db->now(), "expires_date"=>$db->now('+1d'));	
					$forgot_pass_user_inserted_ok = $db->insert ('forgot_pass_login', $data_temporal_user);

					if ($forgot_pass_user_inserted_ok){
						
						$subject = "Reset Password instructions.";
						$template = get_email_header();
						$template .= '<tr>
										<td>
											<table cellpadding="0" cellspacing="0" align="center" width="600" bgcolor="#d8151f">
												<tr>
													<td colspan="3" height="40">&nbsp;</td>
												</tr>
												<tr>
													<td colspan="3" align="center"><img src="https://www.yesco.com/franchising/wp-content/themes/yesco-franchising/img/logo.png" width="328" height="40" alt="Reset Password instructions" /></td>			
												</tr>
												<tr>
													<td colspan="3">&nbsp;</td>
												</tr>
												<tr>
													<td width="30"></td>
													<td width="540" align="center"><span style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22px;text-align:center;">Please, click on the button below to reset your password.</span></td>
													<td width="30"></td>
												</tr>
												<tr>
													<td colspan="3">&nbsp;</td>
												</tr>
												<tr>
													<td colspan="3" align="center"><a href="'.getFullUrl().'/forgot-password/xt_reset_password.php?token='.$token.'" target="_blank"><img src="'.LOCAL_CLIENT_URL.'img/login-btn.jpg" width="102" height="40" alt="Reset Password" title="Reset Password" /></a></td>			
												</tr>
												<tr>
													<td colspan="3" height="40">&nbsp;</td>
												</tr>
											</table>
										</td>
									</tr>';
						$template .= get_email_footer();

						$headers = "MIME-Version: 1.0" . "\r\n";
						$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
						$headers .= 'From: <noreply@das-group.com>' . "\r\n";

						mail($user_email,$subject,$template,$headers);
						
						$_SESSION['show_msg_forgot_pass'] = 'yes';
						
						pageRedirect("An email has been sent to you with password reset instructions. </br> <b>Note:</b> If the email does not arrive soon, check your spam foler. It was sent from noreply@das-group.com", "success", "/forgot-password/msg.php");
						
						}else{
							pageRedirect("There was a problem, plase try again.", "error", "/forgot-password/");
						}
				}else{
					pageRedirect("Sorry, you cannot access to this page.", "error", "/forgot-password/");
				}	
			}else{
				pageRedirect("Incorrect username.", "error", "/forgot-password/");
			}
		}else{
			pageRedirect("Please, enter a valid email.", "error", "/forgot-password/");
		}
	}else{
		pageRedirect("Please enter an email.", "error", "/forgot-password/");
	}
	
}else{
	pageRedirect("Please verify the Captcha.", "error", "/forgot-password/");
}
?>