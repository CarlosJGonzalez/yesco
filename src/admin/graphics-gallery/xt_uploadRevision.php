<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");

$request_id = $db->escape($_POST['id']);
$storeid = $db->escape($_POST['storeid']);

$db->where("id",$_SESSION['user_id']);
$user = $db->getOne("storelogin",array("name"));

$imgData = Array("target_dir"=>$_SERVER["DOCUMENT_ROOT"]."/uploads/custom-requests",
				 "allow_file_types"=>Array("JPG", "JPEG", "PNG", "GIF", "PDF"),
				 "file"=>$_FILES["fileToUpload"]);

if($_FILES["fileToUpload"]["error"] == 0){
	$image = upload_image($imgData);
	if($image['success']==1){
		
		$data = Array("request_id"=>$request_id,
					 "uploaded_by"=>$user['name'],
					  "filename"=>$image['path'],
					 "date"=>$db->now());
		$db->insert("custom_requests_revisions",$data);
		
		$reqdata = Array (
			'status' => 'Completed'
		);
		$db->where ('id', $request_id);
		$db->update ('custom_requests', $reqdata);
		
		$data['status'] = "Completed";
			
		$dataAct = array("username"=>$_SESSION['email'],
					 "storeid"=>$_SESSION['storeid'],
					 "updates"=>json_encode($data),
					 "section"=>"graphics-gallery",
					 "ip_address"=>get_ip(),
					 "details"=>"Graphic uploaded to request");
		$db->insert ('activity', $dataAct);

		$db->where("storeid",$storeid);
		$location = $db->getOne("locationlist");
		create_notification(array("user_type"=>"user",
								 "storeid"=>$storeid,
								 "message"=>"Your custom graphic request has been completed.",
								 "date"=>$db->now(),
								 "unread"=>"1",
								 "new"=>"1",
								 "msg_type"=>"graphics-gallery",
								 "icon"=>"far fa-image",
								 "link"=>"/graphics-gallery/requests/details.php?id=".$request_id));

		
		redir("Your image has been added.");

	}else redir("",$image['error']);
}else redir("",$_FILES["fileToUpload"]["error"]);

function redir($success ="",$error = ""){
	global $request_id;
	if(!empty($error)){
		$_SESSION['error'] = $error;
	}
	if(!empty($success)){
		$_SESSION['success'] = $success;
	}
	$url = "/admin/graphics-gallery/details.php?id=".$request_id;
	header("location:".$url);
	exit;
}