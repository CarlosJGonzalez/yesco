<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/ClassDasConstantContact.php");

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

	$list_name = $db->escape($_POST['list_name']);
	//$from_name = $db->escape($_POST['from_name']);
	
	$params = Array("name"=>$list_name,
					"description"=>"this is a test",
					"favorite" => true
					);
	$list = $cc->addList($params);
error_log( print_r( $list, true ) );
	if(!$list["is_error"]){

		$data = Array ("storeid" => $_SESSION['storeid'],
					   "list_id" => $list['id']
		);

		if($id = $db->insert ('promote_lists', $data)){
			$_SESSION['success'] = "Your changes have been successfully saved.";
			header("Location:/promote-cc/lists/members.php?id=".$list['id']);
			exit;
		}else{
			$_SESSION['error'] = "Sorry! There was an error creating your list.";
			header("Location:/promote-cc/lists/create.php");
			exit;
		}

	}else{
		$_SESSION['error'] = "There was an error creating your list. ".$list['error_info']['error_message'];
		header("Location:/promote-cc/lists/create.php");
		exit;
	}

}else{
	pageRedirect("You must be authorized to view this page.", "error", "/promote-cc/");
}