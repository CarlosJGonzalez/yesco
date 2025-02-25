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
	
	if(isset($_POST['submitBtnUpdatePermission']) && $_POST['submitBtnUpdatePermission'] == 'SAVE'){
		$permission_id = $_POST['permission_id'];
		$permission_name = filter_var($_POST['permission_name'], FILTER_SANITIZE_STRING);
		$permission_description= filter_var($_POST['permission_description'], FILTER_SANITIZE_STRING);

		if($_SESSION["user_role_name"] == "admin_root"){
			if(($permission_id != '') && !(empty($permission_name)) && !(empty($permission_description))){

				$permission_name = strtolower(preg_replace(array('/[[:space:]]+/', '/[\-]/', '/[^A-Za-z0-9\-]/'), '_', $permission_name));

				$sql_permission_exists = "SELECT * FROM ".$_SESSION['database'].".permissions WHERE name='$permission_name' AND id != '$permission_id' limit 1";
				
				$result = $db->rawQuery($sql_permission_exists);

				//If the permission doesn't exist in the database, it will be updated
				if(!$db->count>0){
					$cols = array("name", "description");
					
					$db->where("id",$permission_id);

					$permission = $db->getOne('permissions', $cols);
					
					$data = array("name"=>$permission_name, "description"=>$permission_description);

					$db->where ('id', $permission_id);

					if($db->update ('permissions', $data)){

						$input = array("name"=>$permission_name, "description"=>$permission_description);

						$updates = array_diff($input,$permission);

						$data_track = array("updates"=>json_encode($updates),"section"=>"permissions", "details"=>"Updated: ".$permission_name);
						
						track_activity($data_track);
						
						pageRedirect("Changes updated successfully.", "success", "/admin/security-settings/user.php");
					}else{
						pageRedirect("Error updating changes.", "error", "/admin/security-settings/user.php");
					}
				}else{
					pageRedirect("The permission already exists in the database. Try another.", "error", "/admin/security-settings/user.php");
				}

			}else{
				pageRedirect("All fields must be fill out.", "error", "/admin/security-settings/user.php");
			}
		}else{
			pageRedirect("You must be authorized to view this page.", "error", "/admin/security-settings/user.php");
		}
		
	}elseif(isset($_POST['submitBtnAddPermission']) && $_POST['submitBtnAddPermission'] == 'Save'){
		$permission_name = filter_var($_POST['permission_name'], FILTER_SANITIZE_STRING);
		$permission_description = ucfirst(strtolower(filter_var($_POST['permission_description'], FILTER_SANITIZE_STRING)));
		
		if($_SESSION["user_role_name"] == "admin_root"){
			
			if(!(empty($permission_name)) && !(empty($permission_description))){
				$permission_name = strtolower(preg_replace(array('/[[:space:]]+/', '/[\-]/', '/[^A-Za-z0-9\-]/'), '_', $permission_name));

				$sql = "SELECT * FROM ".$_SESSION['database'].".permissions WHERE name='$permission_name' limit 1";
				
				$db->rawQuery($sql);
				
				//If the permission doesn't exist in the database, it will be created
				if(!$db->count>0){
					
					$data = array("name"=>$permission_name, "description"=>$permission_description);

					if($db->insert ('permissions', $data)){
						$data_track = array("updates"=>json_encode($data),"section"=>"permissions", "details"=>"Created: ".$permission_name);
	
						track_activity($data_track);

						pageRedirect("The permission has been successfully created.", "success", "/admin/security-settings/user.php");
					}else {
						pageRedirect("Sorry, there was an error creating the permission.", "error", "/admin/security-settings/user.php");
					}
				}else{
					pageRedirect("The permission already exists in the database. Try another.", "error", "/admin/security-settings/user.php");
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