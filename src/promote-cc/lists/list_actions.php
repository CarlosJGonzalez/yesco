<?
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/ClassDasConstantContact.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");

if(isset($_SESSION["user_role_name"])){
	
	$locationList = $db->where("storeid",$_SESSION['storeid'])->getOne("locationlist");
	
	//Only for test purpose
	/*$locationList['constant_contact_api_key'] = 'j3bn9adcxrgg2jvxd6nmg75b';
	$locationList['constant_contact_access_token'] = '138e5b8a-ad09-419b-92f7-399d64875e4f';*/

	if(empty($locationList['constant_contact_api_key']) || empty($locationList['constant_contact_access_token'])){
		$_SESSION['error'] = "Please enter a valid api key and token.";
		header('location: /settings/promote/');
		exit;
	}else{
		$cc_api_key = $locationList['constant_contact_api_key'];
		$cc_access_token = $locationList['constant_contact_access_token'];
	}

	//ClassDasConstantContact 
	$cc = new Das_ConstantContact($cc_api_key, $cc_access_token);
	
	//Deletes a list
	if(isset($_GET["list_id_to_delete"]) && $_GET["list_id_to_delete"] != ""){
		
		$list_id = $_GET["list_id_to_delete"];
					
		$list = $cc->deleteList($list_id);
		
		if(!$list["is_error"]){
							
			$db->where('list_id', $list_id);
			
			if($db->delete('promote_lists')){
				pageRedirect("The list was successfully deleted.", "success", "/promote-cc/lists/");
			}else{
				pageRedirect("Sorry! There was an error deleting the list.", "error", "/promote-cc/");
			}
			
		}else{
			pageRedirect("There was an error deleting this list. ".$list['error_info']['error_message'], "error", "/promote-cc/lists/");
		}
		
	}
	
}else{
	pageRedirect("You must be authorized to view this page.", "error", "/promote-cc/lists/");
}