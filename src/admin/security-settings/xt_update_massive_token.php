<?
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");

if(isset($_SESSION["email"]) && $_SESSION["user_role_name"] == 'admin_root'){
	$db->where ("token", NULL, 'IS');
	$cols = Array ("storeid", "email", "token", "id");
	$users = $db->get ("storelogin", null, $cols);

	//If the user doesn't exist in the database, it will be created
	if($db->count>0){
		
		$fails = array();

		foreach($users as $user){
			$sql_uuid = "SELECT replace(uuid(),'-','') as token;";
			$row_uuid = $db->rawQuery ($sql_uuid);
			$token_db = $row_uuid[0]["token"];
			
			$token = checkToken('storelogin', $token_db);
			
			//Data for storelogin
			$data_update_user = array("token"=>$token);
			
			$db->where ('id', $user['id']);
			
			//$storelogin_user_updated_ok = $db->update ('storelogin', $data_update_user);
			
			if (!$storelogin_user_updated_ok){
				$storeid = $user['id'];
				array_push($fails, $storeid);
			}
		}
		
		if(count($fails) > 0){
			echo '<pre>'; print_r($fails); echo '</pre>';
		}else
			echo '<pre>'; print_r($users); echo '</pre>';
			
	}
	
	echo '<pre>'; print_r($fails); echo '</pre>';
	
}else{
	pageRedirect("You must be authorized to view this page.", "error", "/admin/security-settings/user.php");
}