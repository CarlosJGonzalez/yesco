<?
/*
** This file is used to create users in the storelogin table based on a query
**
*/

session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");

if(!$_SESSION["email"] && $_SESSION["user_role_name"] != "admin_root"){
	pageRedirect("Access denied: You must be authorized to view this page.", "error", "/");
	exit;
}

$sql_locations_users = "SELECT storeid, fname1, lname1, email  FROM `locationlist` where suspend = '0' AND storeid != ''  AND storeid != '' AND fname1 != '' AND lname1 != '' AND email != ''";
$locations_data = $db->rawQuery($sql_locations_users);

if($db->count > 0){
	foreach ($locations_data as $data){
		$user_store_id = $data['storeid'];
		$first_name = $data['fname1'];
		$last_name = $data['lname1'];
		$full_name = $first_name." ".$last_name;
		$emails = explode(",",$data['email']);
		$user_email = $emails[0];
		$user_email = filter_var($user_email, FILTER_SANITIZE_STRING);
		$password_without_hash = randomPassword();
		$user_password = password_hash($db->escape($password_without_hash), PASSWORD_DEFAULT);
		$user_role = filter_var('51', FILTER_SANITIZE_STRING);
		$user_status = 1;
		
		if(!(empty($first_name)) && !(empty($last_name)) && !(empty($user_email)) && !(empty($user_password)) && ($user_role != '') && ($user_status != '')){

			if (filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
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
										   "token"=>$token, "status"=>$user_status, 
										   "user_password"=>$password_without_hash);

					echo '<pre>';print_r($password_without_hash); echo '</pre>';
					echo '<pre>';print_r($data_new_user); echo '</pre>';
					
					/*$storelogin_user_inserted_ok = $db->insert ('storelogin', $data_new_user);

					//Creates the user in the storelogin table
					if ($storelogin_user_inserted_ok){
						//The var storelogin_user_inserted_ok gets the ID of The Inserted Record in storelogin
						//Creates the role in the storelogin_user_roles table
						$data_user_role = array("id_storelogin"=>$storelogin_user_inserted_ok, "id_user_roles"=>$user_role);
						$storelogin_user_role_inserted_ok = $db->insert ('storelogin_user_roles', $data_user_role);
						
						//Insert in storelogin_user_roles the userrole
						if($storelogin_user_role_inserted_ok){
							//$data_track = array("updates"=>json_encode($data_new_user),"section"=>"users", "details"=>"Created: ".$user_email);
							//track_activity($data_track);
							echo "Your user has been successfully created: ". $user_email;
						}else {
							echo "Sorry, there was an error creating your user role: ".$user_email;
						}
					
					}else {
						echo "Sorry, there was an error creating your user: ".$user_email;
					}*/
					
				}else{
					echo "That username is taken. Try another: ".$user_email;
				}
		
			}else{
				echo "Please, eneter a valid email: ".$user_email;
			}
		}else{
			echo "All fields must be fill out.";
		}
	}//End for
}