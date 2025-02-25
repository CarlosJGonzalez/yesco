<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");

if(!$_SESSION["email"] && ($_SESSION["user_role_name"] != "admin_root" || $_SESSION["user_role_name"] != "admin_rep")){
	pageRedirect("Access denied: You must be authorized to view this page.", "error", "/");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['type'] == 'admin') {
	$id = $db->escape($_POST['id']);
	$status = $db->escape($_POST['status']);
	$notes = $db->escape($_POST['notes']);
	$type = $db->escape($_POST['type']);

	$db->where("id",$id);
	$custom_request = $db->getOne("custom_requests");

	if($custom_request['id']){
		$data_to_update = Array (
									'status' => $status,
									'notes' => $notes
								);
		$db->where("id",$id);	
		$request_updated = $db->update ('custom_requests', $data_to_update);
		
		if($request_updated){
			$dataAct = array("username"=>$_SESSION['email'],
							 "storeid"=>$custom_request['storeid'],
							 "updates"=>json_encode($data_to_update),
							 "section"=>"graphics-gallery",
							 "details"=>"A custom request has been updated. Id:".$id
							);

			track_activity($dataAct);

			pageRedirect("The request has been updated.", "success", "/admin/graphics-gallery/manage.php");
	
		}else{
			pageRedirect("There was an error updating the custom request.", "error", "/admin/graphics-gallery/manage.php");
		}
	}else{
		pageRedirect("The request was not found.", "error", "/admin/graphics-gallery/request/manage.php");
	}
}else{
	pageRedirect("Access Denied: You must be authorized to view this page.", "error", "/admin/graphics-gallery/manage.php");
}