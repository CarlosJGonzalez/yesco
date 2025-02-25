<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");

$id = $db->escape($_POST['id']);
$db->where("id",$id);
$details = $db->getOne("training",array("name","description","category","show_link","download_link2"));

if($_POST['submit']=="Delete"){
	$db->where("id",$id);
	if($db->delete("training")){
		$fileName = end(explode("/",$details['download_link2']));
		unlink($_SERVER['DOCUMENT_ROOT']."/uploads/training/".$fileName);
		
		$data_track = array("updates"=>json_encode($details),"section"=>"training", "details"=>"Deleted: ".$id);
		track_activity($data_track);
		
		$_SESSION['success']="Your changes has been saved.";
	}else
		$_SESSION['error'] = "Sorry, there was an error saving your changes. ";
}else{
	$name = $db->escape($_POST['name']);
	$category = $db->escape($_POST['category']);
	$description = $db->escape($_POST['description']);
	$show_link = $db->escape($_POST['show_link']);
	unset($details["download_link2"]);
	$data = array("name"=>$name,
				 "description"=>$description,
				 "category"=>$category,
				 "show_link"=>$show_link);
	$updates = array_diff($data,$details);

	$db->where("id",$id);
	if($db->update("training",$updates)){
		$_SESSION['success']="Your changes has been saved.";
		
		$data_track = array("updates"=>json_encode($updates),"section"=>"training", "details"=>"Updates: ".$name);
		track_activity($data_track);
	}else
		$_SESSION['error'] = "Sorry, there was an error saving your changes. ";
}
header("location:/admin/training/");