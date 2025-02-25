<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");

$id = $db->escape($_POST['id']);
$data = array("gallery_id"=>$id,
			 "storeid"=>$_SESSION['storeid'],
			 "date"=>$db->now());
$db->insert("gallery_downloads",$data);