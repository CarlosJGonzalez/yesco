<?
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");
session_start();

if(!$_SESSION["email"] && ($_SESSION["user_role_name"] != "admin_root" || $_SESSION["user_role_name"] != "admin_rep")){
	pageRedirect("Access denied: You must be authorized to view this page.", "error", "/");
	exit;
}

if($_SESSION["user_role_name"] == "admin_root" || $_SESSION["user_role_name"] == "admin_rep"){

	if(isset($_POST["user_role_new_ajax"]) && $_POST["user_role_new_ajax"] != ""){
		
		//Getting user role selected on the add user form
		$role = $_POST["user_role_new_ajax"];
		$sql_user_role = "SELECT name FROM ".$_SESSION['database'].".user_roles WHERE id ='$role' limit 1";
		$row_result_user_role = $db->rawQuery($sql_user_role);
		$user_role_name = $row_result_user_role[0]['name'];
		
		//It will only show a select with all the stores if the user to be created is a store user
		if($user_role_name == 'store_user'){
			
			$sql_all_stores = '';
			
			if($_SESSION["user_role_name"] == "admin_root")
				//Retrieves all locations
				$sql_all_stores = "SELECT locl.storeid, locl.companyname FROM ".$_SESSION['database'].".locationlist locl WHERE locl.suspend = 0 ORDER BY locl.companyname";
			else
				//Retrieves only the locations that are assigned to the representative
				//$sql_all_stores = "SELECT locl.storeid, locl.companyname FROM ".$_SESSION['database'].".locationlist locl, ".$_SESSION['database'].".reps rept WHERE locl.rep != '' AND rept.id = locl.rep AND rept.id = (SELECT id FROM reps WHERE email = '".$_SESSION['email']."') ORDER BY locl.companyname";
				$sql_all_stores = "SELECT locl.storeid, locl.companyname FROM ".$_SESSION['database'].".locationlist locl WHERE locl.suspend = 0 ORDER BY locl.companyname";
			
			$select = '<select name="input-store-select" id="input-store-select" class="form-control custom-select-arrow pr-4">';
			$options = "";
			
			$result_all_stores = $db->rawQuery($sql_all_stores);

			if($db->count>0){
				foreach($result_all_stores as $list){
					$options .= "<option value='".$list['storeid']."'>".$list['companyname'].' ('.$list['storeid'].')'."</option>";
				} 
			echo $select.$options."</select>";
			}
		}else{
			echo '';
		}
	}

}