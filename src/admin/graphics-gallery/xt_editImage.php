<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");

$id = $db->escape($_POST['id']);
$type = $db->escape($_POST['type']);

$name = $db->escape($_POST['name']);
$category = $db->escape($_POST['category']);
$tags = $db->escape($_POST['tags']);
$apply_all = $db->escape($_POST['apply_all']);
if(empty($id)){
	 fail("This image does not exist.");
}
$cols=array("name","category","tags","apply_all");
$db->where("id",$id);
$img = $db->getOne("gallery",$cols);

if($db->count == 0) fail("This image does not exist.");

$input=array("name"=>$name,
		   "category"=>$category,
		   "tags"=>$tags,
		   "apply_all"=>$apply_all);

$updates = array_diff($input,$img);

$db->where ('id', $id);

if(count($updates)){
	if ($db->update ('gallery', $updates)){
		
		$data = array("username"=>$_SESSION['username'],
					 "storeid"=>$_SESSION['storeid'],
					 "updates"=>json_encode($updates),
					 "section"=>"gallery",
					 "ip_address"=>get_ip(),
					 "details"=>"Updated ".$id);
		$db->insert ('activity', $data);
		$_SESSION['success'] = "Your image has been updated.";
		redir($type);

	}else redir($type,$error = "There was an error saving your changes.");
}else redir($type,$error = "No changes were found.");

function fail($error){
	$_SESSION['error'] = $error;
	header("location:/admin/graphics-gallery/");
	exit;
}
function redir($type,$error = ""){
	if(!empty($error)){
		$_SESSION['error'] = $error;
	}
	$url = $type=="admin" ? "/admin/graphics-gallery/" : "/graphics-gallery/";
	header("location:".$url);
	exit;
}