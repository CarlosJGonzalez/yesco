<?
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/ClassDasMC.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");

if(isset($_SESSION["user_role_name"])){
	
	$cols = Array ("storeid", "loyalty_promotions_key");
	$locationList = $db->Where("storeid", $_SESSION['storeid'])->getOne("locationlist", $cols);

	if(empty($locationList['loyalty_promotions_key'])){
		$_SESSION['error'] = "Please enter a key.";
		header('location: /settings/promote/');
		exit;
	}else{
		$mc_api_key = $locationList['loyalty_promotions_key'];
	}

	$mc = new Das_MC($mc_api_key);
	
	//Deletes a list
	if(isset($_GET["list_id_to_delete"]) && $_GET["list_id_to_delete"] != ""){
		
		$list_id = $_GET["list_id_to_delete"];
					
		$list = $mc->deleteList($list_id);
		
		if($list["is_error"] == 0){
							
			$db->where('list_id', $list_id);
			
			if($db->delete('mailchimp_lists')){
				pageRedirect("The list was successfully deleted.", "success", "/promote/lists/");
			}else{
				pageRedirect("Sorry! There was an error deleting the list.", "error", "/promote/");
			}
			
		}else{
			pageRedirect("There was an error deleting this list. ".$list["msg_error"], "error", "/promote/lists/");
		}
		
	}
	
}else{
	pageRedirect("You must be authorized to view this page.", "error", "/promote/lists/");
}