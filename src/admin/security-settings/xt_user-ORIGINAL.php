<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");

if(!$_SESSION["email"] && ($_SESSION["user_role_name"] != "admin_root" || $_SESSION["user_role_name"] != "admin_rep")){
	pageRedirect("Access denied: You must be authorized to view this page.", "error", "/");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	
	if(isset($_POST['submitBtnUpdateUser']) && $_POST['submitBtnUpdateUser'] == 'SAVE'){
		$user_id = $_POST['user_id'];
		$local_email = filter_var($_POST['login_username'], FILTER_SANITIZE_STRING);
		$user_role = filter_var($_POST['user_role'], FILTER_SANITIZE_STRING);
		$user_status = $_POST['user_status'];
		$user_store_id = $_POST['storeid'];

		if($_SESSION["user_role_name"] == "admin_root" || $_SESSION["user_role_name"] == "admin_rep"){
			if(($user_id != '') && !(empty($local_email)) && ($user_role != 'n/a') && ($user_status != '')){
				
				if (filter_var($local_email, FILTER_VALIDATE_EMAIL)) {
					//$sql_user_exists = "SELECT * FROM ".$_SESSION['database'].".storelogin WHERE email='$local_email' AND storeid = '$user_store_id' AND id != '$user_id' limit 1";
					$sql_user_exists = "SELECT * FROM ".$_SESSION['database'].".storelogin WHERE email='$local_email' AND id != '$user_id' limit 1";

					$db->rawQuery($sql_user_exists);
						
					//If the user doesn't exist in the database, it will be updated
					if(!$db->count>0){
						//Retrieves user role
						$sql_user_role = "SELECT id_user_roles FROM ".$_SESSION['database'].".storelogin_user_roles WHERE id_storelogin = '".$user_id."'";
						$db->rawQuery($sql_user_role);

						//If the user has a role, it will be updated. Otherwise, it will be inserted
						if($db->count>0){
							
							//Data for storelogin
							$cols = array("email", "status");
							
							$db->where("id",$user_id);

							$user = $db->getOne('storelogin', $cols);
							
							if($_SESSION["user_role_name"] == "admin_root"){
								$data = array("email"=>$local_email, "status"=>$user_status, "storeid"=>$user_store_id);
							}else{
								$data = array("email"=>$local_email, "status"=>$user_status);
							}
							
							$db->where ('id', $user_id);
							$storelogin_updated_ok = $db->update ('storelogin', $data);
							
							//Data for storelogin_user_roles
							$data_user_role = array("id_user_roles"=>$user_role);
							$db->where("id_storelogin",$user_id);
							$storelogin_user_roles_updated_ok = $db->update ('storelogin_user_roles', $data_user_role);
							
							//If the user information and its role was updated, a success message will be shown 
							if($storelogin_updated_ok && $storelogin_user_roles_updated_ok){
								
								$input = array("email"=>$local_email, "status"=>$user_status);

								$updates = array_diff($input,$user);

								$data_track = array("updates"=>json_encode($updates),"section"=>"user", "details"=>"Updated: ".$local_email);
								
								track_activity($data_track);
								
								$_SESSION["email"] = $local_email;
								
								pageRedirect("Changes updated successfully.", "success", "/admin/security-settings/user.php");
							}else{
								pageRedirect("Error updating changes.", "error", "/admin/security-settings/user.php");
							}
						}else{
							//Data for storelogin
							$cols = array("email", "status");
							
							$db->where("id",$user_id);

							$user = $db->getOne('storelogin', $cols);
							
							$data = array("email"=>$local_email,"status"=>$user_status);
							$db->where ('id', $user_id);
							$storelogin_updated_ok = $db->update ('storelogin', $data);
							
							//Data for storelogin storelogin_user_roles
							$data_user_role = array("id_storelogin"=>$user_id, "id_user_roles"=>$user_role);
							$storelogin_user_roles_inserted_ok = $db->insert ('storelogin_user_roles', $data_user_role);
							
							//If the user information and its role was updated, a success message will be shown 
							if($storelogin_updated_ok && $storelogin_user_roles_inserted_ok){
								
								$input = array("email"=>$local_email, "status"=>$user_status);

								$updates = array_diff($input,$user);

								$data_track = array("updates"=>json_encode($updates),"section"=>"user", "details"=>"Updated: ".$local_email);
								
								track_activity($data_track);
								
								$_SESSION["email"] = $local_email;

								pageRedirect("Changes updated successfully.", "success", "/admin/security-settings/user.php");
							}else{
								pageRedirect("Error updating changes.", "error", "/admin/security-settings/user.php");
							}
						}
					}else{
						pageRedirect("That username is taken. Try another.", "error", "/admin/security-settings/user.php");
					}
			
				}else{
					pageRedirect("Please, enter a valid email.", "error", "/admin/security-settings/user.php");
				}
			
			}else{
				pageRedirect("All fields must be fill out.", "error", "/admin/security-settings/user.php");
			}
		}else{
			if(!(empty($user_id)) && !(empty($local_email))){
				
				if (filter_var($local_email, FILTER_VALIDATE_EMAIL)) {
					$sql_user_exists = "SELECT * FROM ".$_SESSION['database'].".storelogin WHERE email='$local_email' AND id != '$user_id' limit 1";
						
					$db->rawQuery($sql_user_exists);
						
					//If the user doesn't exist in the database, it will be updated
					if(!$db->count>0){
						//Data for storelogin
						$cols = array("email");
						
						$db->where("id",$user_id);

						$user = $db->getOne('storelogin', $cols);
						
						$data = array("email"=>$local_email);
						$db->where ('id', $user_id);
						$storelogin_updated_ok = $db->update ('storelogin', $data);
						
						if($storelogin_updated_ok){
							$input = array("email"=>$local_email);

							$updates = array_diff($input,$user);

							$data_track = array("updates"=>json_encode($updates),"section"=>"user", "details"=>"Updated: ".$local_email);
							
							track_activity($data_track);
							
							$_SESSION['success']= "Changes updated successfully";
							$_SESSION["email"] = $local_email;
							pageRedirect("Changes updated successfully.", "success", "/admin/security-settings/user.php");
						}else{
							pageRedirect("Error updating changes.", "error", "/admin/security-settings/user.php");
						}
					}else{
						pageRedirect("That username is taken. Try another.", "error", "/admin/security-settings/user.php");
					}
				
				}else{
					pageRedirect("Please, enter a valid email.", "error", "/admin/security-settings/user.php");
				}
				
			}else{
				pageRedirect("All fields must be fill out.", "error", "/admin/security-settings/user.php");
			}
		}
	}elseif(isset($_POST['submitBtnAddUser']) && $_POST['submitBtnAddUser'] == 'Save'){
		$first_name = $db->escape($_POST['first_name']);
		$last_name = $db->escape($_POST['last_name']);
		$full_name = $first_name." ".$last_name;
		$user_email = filter_var($_POST['login_username_new'], FILTER_SANITIZE_STRING);
		$user_password = password_hash($db->escape($_POST['login_password_new']), PASSWORD_DEFAULT);
		$user_role = filter_var($_POST['user_role_new'], FILTER_SANITIZE_STRING);
		$user_status = $_POST['user_status_new'];
		$send_email_notification = ( isset($_POST['switch-send-password']) && ($_POST['switch-send-password'] == '1') ) ? true : false;
		
		//Getting user role selected on the add user form
		$db->where("id",$user_role);
		$row_result_user_role = $db->getOne('user_roles', 'name');
		$user_role_name = $row_result_user_role['name'];
		
		if(isset($_POST['input-store-select']) && ($_POST['input-store-select'] != '') && $user_role_name == 'store_user')
			$user_store_id = $_POST['input-store-select'];
		else
			$user_store_id = '-1';

		if($_SESSION["user_role_name"] == "admin_root" || $_SESSION["user_role_name"] == "admin_rep"){
			
			if(!(empty($first_name)) && !(empty($last_name)) && !(empty($user_email)) && !(empty($user_password)) && ($user_role != '') && ($user_status != '')){

				if (filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
					/*$db->where("email",$user_email)
						->where("storeid",$user_store_id);
					$db->getOne('storelogin', '*');*/
					
					$db->where("email",$user_email);
					$db->getOne('storelogin', '*');

					//If the user doesn't exist in the database, it will be created
					if(!$db->count>0){
						$sql_uuid = "SELECT replace(uuid(),'-','') as token;";
						$row_uuid = $db->rawQuery ($sql_uuid);
						$token_db = $row_uuid[0]["token"];
						
						$token = checkToken('storelogin', $token_db);

						//Data for storelogin
						$data_new_user = array("name"=>$full_name, "storeid"=>$user_store_id, 
											   "email"=>$user_email, "password"=>$user_password, 
											   "token"=>$token, "status"=>$user_status);
						$storelogin_user_inserted_ok = $db->insert ('storelogin', $data_new_user);

						//Creates the user in the storelogin table
						//if ($db->getLastErrno() === 0){
						if ($storelogin_user_inserted_ok){

							//The var storelogin_user_inserted_ok gets the ID of The Inserted Record in storelogin
							//Creates the role in the storelogin_user_roles table
							$data_user_role = array("id_storelogin"=>$storelogin_user_inserted_ok, "id_user_roles"=>$user_role);
							$storelogin_user_role_inserted_ok = $db->insert ('storelogin_user_roles', $data_user_role);
							
							//Insert in storelogin_user_roles the userrole
							if($storelogin_user_role_inserted_ok){
								$data_track = array("updates"=>json_encode($data_new_user),"section"=>"users", "details"=>"Created: ".$user_email);
								track_activity($data_track);
								
								if($send_email_notification){
									$subject = "Welcome to Local ".CLIENT_NAME."!";
									$email_template = file_get_contents($_SERVER['DOCUMENT_ROOT']."/emails/new-user.php");
									$email_template = str_replace("%%NAME%%", $full_name, $email_template);
									$email_template = str_replace("%%USERNAME%%", $user_email, $email_template);
									$email_template = str_replace("%%PASSWORD%%", $_POST['login_password_new'], $email_template);
									$email_template = str_replace("%%CLIENT_URL%%", CLIENT_URL, $email_template);
									$email_template = str_replace("%%LOCAL_CLIENT_URL%%", LOCAL_CLIENT_URL, $email_template);
									$email_template = str_replace("%%CLIENT_NAME%%", CLIENT_NAME, $email_template);
									$email_template = str_replace("%%YEAR%%", date("Y"), $email_template);
								
									$client_store_id = ($user_store_id < '1') ? $_SESSION['client'] : $_SESSION['client'].'-'.$user_store_id;
								
									//Send email to the user
									$data_email = Array (
										'copy_hidden'=> 'lisa@das-group.com, sicwing@das-group.com',
										'subject'    => $subject,
										'from' 	     => 'DAS Group <noreply@das-group.com>',
										'sender'     => 'DAS Group <noreply@das-group.com>',
										'body' 	     => $email_template,
										'copy' 	     => '',
										'storeid' 	 => $client_store_id ,
										'to' 	     => $user_email
									);
									
									$db->insert ('emails_send.emails', $data_email);
								}
								
								$data_to_insert_admin_login = []; //Contains credentials for the website in admin_login
								
								$fpUpdatesAdminLogin = ["dastoken" => "DAS%])p6Eu8SUuqN9U",
														"action" => "new",
														"table" => "admin_login"];
								
								// Set empty for admin users. It works like this in the website db									
								$user_store_id = ($user_store_id == '-1') ? '' : $user_store_id;
												
								$data_to_insert_admin_login["storeid"] = $user_store_id;
								$data_to_insert_admin_login["email"] = $user_email;
								$data_to_insert_admin_login["password"] = $user_password;
								$data_to_insert_admin_login["token"] = $token;
								$data_to_insert_admin_login["name"] = $full_name;

								if(count($fpUpdatesAdminLogin) && count($data_to_insert_admin_login)){
									//$urlUpdateAdminLogin = "https://fullypromoted.com/xt_cupdate.php/?".http_build_query(array_merge($data_to_insert_admin_login,$fpUpdatesAdminLogin));
									$urlUpdateAdminLogin = CLIENT_URL."xt_cupdate.php/?".http_build_query(array_merge($data_to_insert_admin_login,$fpUpdatesAdminLogin));
									
									$ch2 = curl_init();
									curl_setopt($ch2, CURLOPT_URL, $urlUpdateAdminLogin);
									curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
									curl_setopt($ch2, CURLOPT_FOLLOWLOCATION, true);
									curl_setopt($ch2, CURLOPT_PROXYPORT, 3128);
									curl_setopt($ch2, CURLOPT_SSL_VERIFYHOST, 0);
									curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, 0);
									$response_curl_2 = curl_exec($ch2);

									$response_curl_2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);

									curl_close($ch2);
								}
								
								pageRedirect("Your user has been successfully created.", "success", "/admin/security-settings/user.php");
							}else {
								pageRedirect("Sorry, there was an error creating your user role.", "error", "/admin/security-settings/user.php");
							}
						
						}else {
							pageRedirect("Sorry, there was an error creating your user.", "error", "/admin/security-settings/user.php");
						}
						
					}else{
						pageRedirect("That username is taken. Try another.", "error", "/admin/security-settings/user.php");
					}
			
				}else{
					pageRedirect("Please, enter a valid email.", "error", "/admin/security-settings/user.php");
				}
			}else{
				pageRedirect("All fields must be fill out.", "error", "/admin/security-settings/user.php");
			}
		
		}else{
			pageRedirect("You must be authorized to view this page.", "error", "/admin/security-settings/user.php");
		}
	}elseif(isset($_POST['action']) && $_POST['action'] == 'change_user_pass_form'){

		$user_id = $db->escape($_POST['user_id_change_pass']);
		$new_password = password_hash($db->escape($_POST['new_pass']), PASSWORD_DEFAULT);
		$password_confirmation = password_hash($db->escape($_POST['password_confirmation']), PASSWORD_DEFAULT);
		
		if(!(empty($user_id)) && !(empty($new_password)) && !(empty($password_confirmation))){
		
			$db->where("id",$user_id,"=");
			$user = $db->getOne('storelogin');
			$user_email = $user['email'];
			$user_store_id = $user['storeid'];
			$full_name = $user['name'];
			
			if($_POST['new_pass'] == $_POST['password_confirmation']){

				//If the user exists in the database, it will be updated
				if ($user['id']) {
					
					$data = Array ('password' => $new_password);
					$db->where("id",$user_id,"=");
					
					if($db->update ('storelogin', $data)){

						$subject = "Your Local ".CLIENT_NAME." Password Has Been Updated!";
						$email_template = file_get_contents($_SERVER['DOCUMENT_ROOT']."/emails/password-update.php");
						$email_template = str_replace("%%NAME%%", $full_name, $email_template);
						$email_template = str_replace("%%USERNAME%%", $user_email, $email_template);
						$email_template = str_replace("%%CLIENT_URL%%", CLIENT_URL, $email_template);
						$email_template = str_replace("%%LOCAL_CLIENT_URL%%", LOCAL_CLIENT_URL, $email_template);
						$email_template = str_replace("%%CLIENT_NAME%%", CLIENT_NAME, $email_template);
						$email_template = str_replace("%%YEAR%%", date("Y"), $email_template);
						$email_template = str_replace("%%PASSWORD%%", $_POST['new_pass'], $email_template);
						
						$user_store_id = ($user_store_id < '1') ? $_SESSION['client'] : $_SESSION['client'].'-'.$user_store_id;
						
						//Send email to the user
						$data_email = Array (
							'copy_hidden'=> 'sicwing@das-group.com',
							'subject'    => $subject,
							'from' 	     => 'DAS Group <noreply@das-group.com>',
							'sender'     => 'DAS Group <noreply@das-group.com>',
							'body' 	     => $email_template,
							'copy' 	     => '',
							'storeid' 	 => $user_store_id,
							'to' 	     => $user_email
						);
						
						$db->insert ('emails_send.emails', $data_email);

						$updates = array("email"=>$user_email, "name"=>$full_name);
						
						$dataAct = array("username"=>$_SESSION['email'],
										 "storeid"=>$_SESSION['storeid'],
										 "updates"=>json_encode($updates),
										 "section"=>"login",
										 "details"=>"Updated password for ".$user_id
						);

						track_activity($dataAct);
						
						pageRedirect("Changes updated successfully.", "success", "/admin/security-settings/user.php");
					}else{
						pageRedirect("Error updating changes", "error", "/admin/security-settings/user.php");
					}
				}else{
					pageRedirect("The username doesn't exist.", "error", "/admin/security-settings/user.php");
				}
			
			}else{
				pageRedirect("Passwords are not matching.", "error", "/admin/security-settings/user.php");
			}
			
		}else{
			pageRedirect("All fields must be fill out.", "error", "/admin/security-settings/user.php");
		}
	}elseif(isset($_POST['delete_confirmation_ok']) && $_POST['delete_confirmation_ok'] == 'DELETE'){
		$user_id = $db->escape($_POST['user_id_delete']);

		if(!(empty($user_id))){
			$cols = array("id,storeid,email,name");
			$db->where("id",$user_id,"=");
			$user = $db->getOne('storelogin', $cols);
			
			if($user['id']){
				$user_email = $user['email'];
				$user_fullname = $user['name'];
				$user_storeid = $user['storeid'];
				
				$db->where("id_storelogin",$user_id,"=");	
				if($db->delete ('storelogin_user_roles')){
					$db->where("id",$user_id,"=");
					if($db->delete ('storelogin')){
						$data_deleted_user = array("id"=>$user_id, "storeid"=>$user_storeid, "email"=>$user_email, "name"=>$user_fullname);	
						
						$data_track = array("storeid"=> $_SESSION['storeid'], "updates"=>json_encode($data_deleted_user),"section"=>"users", "details"=>"Deleted an user: ".$user_email);
						track_activity($data_track);
						
						pageRedirect("User deleted successfully.", "success", "/admin/security-settings/user.php");
					}else{
						pageRedirect("There was an error deleting the user.", "error", "/admin/security-settings/user.php");
					}
				}else{
					pageRedirect("There was an error deleting the user role.", "error", "/admin/security-settings/user.php");
				}
			}else{
				pageRedirect("The user doesn't exist.", "error", "/admin/security-settings/user.php");
			}
			
		}else{
			pageRedirect("All fields must be fill out.", "error", "/admin/security-settings/user.php");
		}
	}
}//End if ($_SERVER['REQUEST_METHOD'] === 'POST')

pageRedirect("You must be authorized to view this page.", "error", "/admin/security-settings/user.php");