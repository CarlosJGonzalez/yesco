<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");

$quote = $db->escape($_POST['quote']);
$id = $db->escape($_POST['id']);
$data = Array (
	'quote' => $quote
);
if($quote==0){
	$data['status'] = "Processing";
	$data['approved'] = "Paid";
}
if($db->where ('id', $id)->update ('custom_requests', $data)){
	$data['id']=$id;
	$dataAct = array("username"=>$_SESSION['email'],
				 "storeid"=>$_SESSION['storeid'],
				 "updates"=>json_encode($data),
				 "section"=>"graphics-gallery",
				 "ip_address"=>get_ip(),
				 "details"=>"Quote added");
	$db->insert ('activity', $dataAct);
	
	if($quote>0){
		$location = $db->where("id",$id)->getOne("custom_requests");
		create_notification(array("storeid"=>$location['storeid'],
								 "user_type"=>"user",
								 "message"=>"A quote has been added to your custom request.",
								 "date"=>$db->now(),
								 "unread"=>"1",
								 "new"=>"1",
								 "msg_type"=>"graphics-gallery",
								  "icon"=>"fas fa-comment-dollar",
								 "link"=>"/graphics-gallery/requests/details.php?id=".$id));
	}
	$_SESSION['success'] = "Your changes have been saved.";
}else{
	$_SESSION['error'] = "There was an error saving your changes.";
}
if($_GET['type']=="user")
	header("location:/graphics-gallery/requests/details.php?id=".$id);
else
	header("location:/admin/graphics-gallery/details.php?id=".$id);
