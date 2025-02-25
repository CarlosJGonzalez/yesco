<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");

if(isset($_POST['submitBtnUpdateMyPassword']) && $_POST['submitBtnUpdateMyPassword'] == 'Save Password'){

		$new_password = $db->escape($_POST['new_pass']);
		$new_password_hash = password_hash($db->escape($_POST['new_pass']), PASSWORD_DEFAULT);
		$confirm_password = $db->escape($_POST['confirm_pass']);
		$user_email = $db->escape($_POST['user_email']);
		$token = $db->escape($_POST['user_token']);

		if(!(empty($user_email)) && !(empty($new_password)) && !(empty($confirm_password))){

			if($new_password == $confirm_password){
				
				$db->where("email",$user_email,"=");

				$user = $db->getOne('storelogin');

				//If the user doesn't exist in the database, it will be updated
				if ($db->count > 0) {
					
					$db->where("token",$token);
					$db->getOne("forgot_pass_login");
					
					if($db->count > 0){
					
						$sql = "SELECT email FROM forgot_pass_login WHERE token = '$token' AND expires_date > NOW()";
						$db->rawQuery($sql);

						if($db->count > 0){
					
							$data = Array ('password' => $new_password_hash);
							
							$db->where("email",$user_email,"=");
							
							if($db->update ('storelogin', $data)){
								$db->where('email', $user_email);
								
								if($db->delete('forgot_pass_login')){
									$input = array("email"=>$user_email, "password"=>$new_password_hash);
									$data_track = array("username"=>$user_email,"updates"=>json_encode($input),"section"=>"forgot password", "details"=>"Updated password.");
									track_activity($data_track);
									
									$_SESSION['show_msg_forgot_pass'] = 'yes';
									pageRedirect("Changes updated successfully. <a href='/'>Login</a>", "success", "/forgot-password/msg.php");
								}else{
									$_SESSION['email_forgot_pass'] = $user_email;
									$_SESSION['token_forgot_pass'] = $token;
									pageRedirect("Something went wrong updating your password", "error", "/forgot-password/reset_password.php");
								}
								
							}else{
								$_SESSION['email_forgot_pass'] = $user_email;
								$_SESSION['token_forgot_pass'] = $token;
								pageRedirect("Error updating changes", "error", "/forgot-password/reset_password.php");
							}
					
						}else{
							pageRedirect("Sorry, you don't have access to this page anymore. Try again!", "error", "/forgot-password/");
						}
					
					}else{
						pageRedirect("Sorry, you cannot access to this page.", "error", "/forgot-password/");
					}
					
				}else{
					$_SESSION['email_forgot_pass'] = $user_email;
					$_SESSION['token_forgot_pass'] = $token;
					pageRedirect("The username doesn't exist.", "error", "/forgot-password/reset_password.php");
				}
			
			}else{
				$_SESSION['email_forgot_pass'] = $user_email;
				$_SESSION['token_forgot_pass'] = $token;
				pageRedirect("Passwords don't match.", "error", "/forgot-password/reset_password.php");
			}
			
		}else{
			$_SESSION['email_forgot_pass'] = $user_email;
			$_SESSION['token_forgot_pass'] = $token;
			pageRedirect("All fields must be fill out.", "error", "/forgot-password/reset_password.php");
		}
}elseif(!empty($db->escape($_GET['token']))){
	
	$token = $db->escape($_GET['token']);
	$db->where("token",$token);
	$db->getOne("forgot_pass_login");
	
	if($db->count > 0){
		$sql = "SELECT email FROM forgot_pass_login WHERE token = '$token' AND expires_date > NOW()";
		$user_email = $db->rawQuery($sql);

		if($db->count > 0){
			$_SESSION['email_forgot_pass'] = $user_email[0]['email'];
			$_SESSION['token_forgot_pass'] = $token;
			pageRedirect(" ", "n/a", "/forgot-password/reset_password.php");
		}else{
			pageRedirect("Sorry, you don't have access to this page anymore. Try again!", "error", "/forgot-password/");
		}

	}else{
		pageRedirect("Sorry, you cannot access to this page.", "error", "/forgot-password/");
	}
}
?>