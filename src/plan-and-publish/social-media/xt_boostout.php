<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");

if(!$_SESSION['storeid'] && !$_POST['postid']){
	exit;
}

$post_id = $_POST['postid'];

if($_POST['opt_out']){ 	

	$db->where('storeid', $_SESSION['storeid'])->where('id', $post_id);
	if($db->delete('social_media_local_boots_optout')) 
		echo "<a class='dropdown-item px-2' id ='opt_boost' data-value='0' data-postid='".$post_id."' href='javascript:void(0)'>Remove Boosted Post</a>";

}else{ 

	$data = Array (
					"id" => $post_id,
               		"storeid" => $_SESSION['storeid'],
               		"boostout" => 1,
               		"date" => $db->now()
				  );
	$id = $db->insert ('social_media_local_boots_optout', $data);
	if($id)
		echo "<a class='dropdown-item px-2' id ='opt_boost' data-value='1' data-postid='".$post_id."' href='javascript:void(0)'>Boost this Post</a>";
}