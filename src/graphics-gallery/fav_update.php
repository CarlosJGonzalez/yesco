<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");

$id = $db->escape($_POST['id']);
$db->where("storeid",$_SESSION['storeid']);
$db->where("gallery_id",$id);
$fav = $db->getOne("gallery_favs");
if($db->count>0){
	$db->where('id', $fav['id']);
	$db->delete('gallery_favs');
}else{
	$data = array("gallery_id"=>$id,
				 "storeid"=>$_SESSION['storeid']);
	$db->insert('gallery_favs',$data);
}
$db->where("id",$id);
echo json_encode($db->getOne("gallery"));