<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");

$id = $db->escape($_GET['id']);

$type = $db->escape($_GET['type']);
$db->where('id', $id);
if($db->delete("gallery")){
	$_SESSION['success'] = "Your image has been deleted.";
	redir($type);
}else{
	$_SESSION['error'] = "There was an error deleting your image.";
	redir($type);
}
function redir($type){
	$url = $type=="admin" ? "/admin/graphics-gallery/" : "/graphics-gallery/";
	header("location:".$url);
	exit;
}