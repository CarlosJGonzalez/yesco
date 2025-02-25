<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 'On');

if(isset($_SESSION['user_id'])){
	session_unset();
}

include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");

$email = $db->escape($_POST['email']);
$password = $db->escape($_POST['password']);

$token = null;
if( $_GET && isset( $_GET['token'] ) ){
	$token = $db->escape($_GET['token']);
}

if(!empty($email) && !empty($password)){
	$db->where("email",$email);
	$user = $db->getOne("storelogin");

	if($db->count>0){
		
		if($user['status'] == 1){
			
			if (password_verify($password, $user['password'])) {
				
				//Get locations that aren't suspended in locationlist 
				$actives_locations = get_active_locations($user['storeid']);

				//Returns a string from the actives_locations array. E.g (90018,2)
				$active_storeids = implode(',',$actives_locations);
				//When login, it only allows the user to see active locations
				$user['storeid'] = $active_storeids;
			
				//Update storelogin with only active locations assigned to the user. If there aren't any active one, the storeid field will be set to empty
				$db->where ('email', $email);
				$update = $db->update ('storelogin', Array ('storeid' => $active_storeids));
				
				//If there aren't any active stores assigned to the user, the system won't let him login
				if($actives_locations[0] !=''){
					allow_access($user);
				}else{
					error_log('No actives locations:' . print_r($actives_locations, true ) );
					$_SESSION['error'] = "The username is not authorized to login.";
				}
			}else{				
				
				$_SESSION['error'] = "Incorrect username / password.";
			}
		}else{
				
			$_SESSION['error'] = "The user is inactive.";
		}
	}else{
		
		$_SESSION['error'] = "Incorrect username or password.";
	}

}else if(!empty($token)){
	
	$db->where("token",$token);
	$user = $db->getOne("storelogin");
	
	if($db->count>0){

		if($user['status'] == 1){
			//Get locations that aren't suspended in locationlist 
			$actives_locations = get_active_locations($user['storeid']);
			//Returns a string from the actives_locations array. E.g (90018,2)
			$active_storeids = implode(',',$actives_locations);
			//When login, it only allows the user to see active locations
			$user['storeid'] = $active_storeids;
			
			//Update storelogin with only active locations assigned to the user. If there aren't any active one, the storeid field will be set to empty
			$db->where("token",$token);
			$update = $db->update ('storelogin', Array ('storeid' => $active_storeids));
			
			//If there aren't any active stores assigned to the user, the system won't let him login
			if($actives_locations[0] !='')
				allow_access($user);
			else
				$_SESSION['error'] = "The username is not authorized to login.";
		}else{
			$_SESSION['error'] = "The user is inactive.";
		}
	}else{
		$_SESSION['error'] = "Incorrect username or password.";
	}
}

function allow_access($user){
	
	global $db;
	$branches = explode(",",$user['storeid']);
	$_SESSION['storeid'] = $branches[0];
	$_SESSION['name'] = $user['name'];
	$_SESSION['email'] = $user['email'];
	$user_id = $_SESSION['user_id'] = $user['id'];
	$_SESSION["user_status"]= $user['status'];
	$_SESSION['view']="user";
	$_SESSION['token'] = $user['token'];
	
	//Stores userrole permissions
	$permissions = array();
	
	//Getting user role
	$sql_user_role = "SELECT ur.name, sur.id_user_roles FROM user_roles ur, storelogin_user_roles sur WHERE id_storelogin= ? and sur.id_user_roles = ur.id limit 1";
	$params = Array($user_id);
	$row_result_user_role = $db->rawQuery($sql_user_role, $params);
	
	$id_user_role = $row_result_user_role[0]['id_user_roles'];
	$user_role_name = $row_result_user_role[0]['name'];
	
	//Getting user role permissions
	$sql_user_role_permissions_name = "SELECT perm.name, urp.id_permission FROM user_roles_permissions urp, permissions perm WHERE perm.id = urp.id_permission AND urp.id_user_role = (SELECT sur.id_user_roles FROM storelogin_user_roles sur WHERE sur.id_storelogin = ?);";
	$params = Array($user_id);
	$row_result_user_role_permissions_name = $db->rawQuery($sql_user_role_permissions_name, $params);
	
	foreach($row_result_user_role_permissions_name as $permission_name){
		array_push($permissions, $permission_name['name']);	
	}

	//role_permission for this particular user
	$_SESSION["role_permissions"] = $permissions;
	$_SESSION["id_user_role"] = $id_user_role;
	$_SESSION["user_role_name"] = $user_role_name;
	
	if($user['storeid']<0){
		$_SESSION['admin']=true;
		$_SESSION['view']="das_admin";
	}
	$data = array("section"=>"login");
	track_activity($data);

	$data = Array ('lastlogin' => $db->now());
	$db->where ('id', $user['id']);
	$db->update ('storelogin', $data);
	
	if(!isset($_GET['url']) && empty($_GET['url'])){
		
		if($_SESSION['storeid']<0){
			if($_SESSION["user_role_name"] == 'graphics_gallery_only')
				header("location:/admin/graphics-gallery/");	
			else
				header("location:/admin/location-details/");
		}else{
			//header("location:/dashboard.php");
			header("location:/location-details/");
		}
	}else{
		header('location: /'.$_GET['url']);
	}
	
	exit;
}

header("location:/");
exit;