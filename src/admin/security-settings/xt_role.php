<?
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");
session_start();

/*if($_SERVER['REQUEST_URI']!="/login.php"){
	if(!$_SESSION["username"]){
		$_SESSION['error']="You must be logged in to view this page.";
		header('location: /login.php');
		exit;
	}
}*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	
	if(isset($_POST['submitBtnUpdateRole']) && $_POST['submitBtnUpdateRole'] == 'SAVE'){
		$role_id = $_POST['role_id'];
		$role_name = filter_var($_POST['role_name'], FILTER_SANITIZE_STRING);
		$role_description= filter_var($_POST['role_description'], FILTER_SANITIZE_STRING);
		$role_permissions_form = $_POST['permissions'];
		$role_permissions_db = unserialize(base64_decode($_POST['role_permissions_db']));
		$permission_names = $_POST['permission_names'];
		$user_role_permission_error = [];
		$error_msg = '';

		if($_SESSION["user_role_name"] == "admin_root"){
			if(($role_id != '') && !(empty($role_name)) && !(empty($role_description))){

				if(!(empty($role_permissions_form))){

					$role_name = strtolower(preg_replace(array('/[[:space:]]+/', '/[\-]/', '/[^A-Za-z0-9\-]/'), '_', $role_name));

					$sql_role_exists = "SELECT * FROM ".$_SESSION['database'].".user_roles WHERE name='$role_name' AND id != '$role_id' LIMIT 1";

					$db->rawQuery($sql_role_exists);
						
					//If the role doesn't exist in the database, it will be updated
					if(!$db->count>0){
						
						$cols = array("name", "description");
					
						$db->where("id",$role_id);

						$role = $db->getOne('user_roles', $cols);
						
						$data = array("name"=>$role_name, "description"=>$role_description);

						$db->where ('id', $role_id);
						
						if($db->update ('user_roles', $data)){
							
						$input = array("name"=>$role_name, "description"=>$role_description);

						$updates = array_diff($input,$role);

						$data_track = array("updates"=>json_encode($updates),"section"=>"roles", "details"=>"Updated: ".$role_name);
						
						track_activity($data_track);
							
						//Checks if there are permissions to delete
						$permissions_to_delete = array_diff($role_permissions_db, $role_permissions_form);
						
						if(!empty($permissions_to_delete)){
							
							foreach($permissions_to_delete as $permission_id_to_delete){
								$sql_permission_name = "SELECT name FROM ".$_SESSION['database'].".permissions WHERE id = '".$permission_id_to_delete."' LIMIT 1;";
								
								$permission_field = $db->rawQuery($sql_permission_name);
								
								if($db->count>0){
									$permission_name = $permission_field[0]['name'];
								
									$db->where('id_user_role', $role_id)
									   ->where('id_permission', $permission_id_to_delete);

									if(!$db->delete('user_roles_permissions')){
										$error_msg .= 'Permission name: '.$permission_name.' '."(This permission was not deleted).".'</br>';
										pageRedirect($error_msg, "error", "/admin/security-settings/user.php");
									}
								
								}else{
									pageRedirect($error_msg, "error", "/admin/security-settings/user.php");
								}
							}
						}
							
						//Checks if there are new permissions to insert
						$permissions_to_insert = array_diff($role_permissions_form, $role_permissions_db);
						
						if(!empty($permissions_to_insert)){
						
							$data_permissions_to_insert = array();
						
							foreach($permissions_to_insert as  $id_permission){
								$user_role_permission = array("id_user_role"=>$role_id, "id_permission"=>$id_permission);
								array_push($data_permissions_to_insert, $user_role_permission);	
							}
							
							$permissions_inserted_ok = $db->insertMulti('user_roles_permissions', $data_permissions_to_insert);
							
							if(!$permissions_inserted_ok){
								pageRedirect("The permission(s) were not inserted.", "error", "/admin/security-settings/user.php");
							}
							
						}
					
						pageRedirect("The role has been successfully updated.", "success", "/admin/security-settings/user.php");
						
						}else{
							pageRedirect("Error updating changes.", "error", "/admin/security-settings/user.php");
						}
					}else{
						pageRedirect("The role already exists in the database. Try another.", "error", "/admin/security-settings/user.php");
					}
				}else{
					pageRedirect("A permission must be selected.", "error", "/admin/security-settings/user.php");
				}
			}else{
				pageRedirect("All fields must be fill out.", "error", "/admin/security-settings/user.php");
			}
		}else{
			pageRedirect("You must be authorized to view this page.", "error", "/admin/security-settings/user.php");
		}
		
	}elseif(isset($_POST['submitBtnAddRole']) && $_POST['submitBtnAddRole'] == 'Save'){
		$role_name = filter_var($_POST['role_name'], FILTER_SANITIZE_STRING);
		$role_description = ucfirst(strtolower(filter_var($_POST['role_description'], FILTER_SANITIZE_STRING)));
		$role_permissions_form = $_POST['permissions'];

		if($_SESSION["user_role_name"] == "admin_root"){
			if(!(empty($role_name)) && !(empty($role_description))){
				
				if(!(empty($role_permissions_form))){
					$role_name = strtolower(preg_replace(array('/[[:space:]]+/', '/[\-]/', '/[^A-Za-z0-9\-]/'), '_', $role_name));

					$sql = "SELECT * FROM ".$_SESSION['database'].".user_roles WHERE name='$role_name' limit 1";
					
					$db->rawQuery($sql);
					
					//If the role doesn't exist in the database, it will be created
					if(!$db->count>0){
						
						$data = array("name"=>$role_name, "description"=>$role_description);
						
						if($last_user_roles_id = $db->insert ('user_roles', $data)){

							$data_track = array("updates"=>json_encode($data),"section"=>"roles", "details"=>"Created: ".$role_name);
							
							track_activity($data_track);
							
							$data_permissions_to_insert = array();
						
							foreach($role_permissions_form as  $id_permission){
								$user_role_permission = array("id_user_role"=>$last_user_roles_id, "id_permission"=>$id_permission);
								array_push($data_permissions_to_insert, $user_role_permission);	
							}
							
							$permissions_inserted_ok = $db->insertMulti('user_roles_permissions', $data_permissions_to_insert);
							
							if($permissions_inserted_ok){
								pageRedirect("The role has been successfully created.", "success", "/admin/security-settings/user.php");
							}else{
								pageRedirect("Sorry, there was an error assigning the permissions to the role.", "error", "/admin/security-settings/user.php");
							}
							
						}else{
							pageRedirect("Sorry, there was an error creating the role.", "error", "/admin/security-settings/user.php");
						}
					}else{
						pageRedirect("The role already exists in the database. Try another.", "error", "/admin/security-settings/user.php");
					}
				}else{
					pageRedirect("A permission must be selected.", "error", "/admin/security-settings/user.php");
				}
			}else{
				pageRedirect("All fields must be fill out.", "error", "/admin/security-settings/user.php");
			}
		}else{
			pageRedirect("You must be authorized to view this page.", "error", "/admin/security-settings/user.php");
		}
	}
}
pageRedirect("You must be authorized to view this page.", "error", "/admin/security-settings/user.php");