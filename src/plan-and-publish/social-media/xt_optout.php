<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");

if(!$_SESSION['storeid'] && !$_POST['postid']){
	exit;
}

$post_id = $_POST['postid'];

$action = 'Post Id no exist';
if($_POST['opt_out']){ //if user opted in	
	$db->where('storeid', $_SESSION['storeid'])->where('id', $post_id);
	if($db->delete('social_media_local_posts_optout')){
		echo "<a class='dropdown-item px-2 post_opts' id ='opt_".$post_id."' data-value='0' data-postid='".$post_id."' href='javascript:void(0)'>Opt Out</a>";
		$action = 'Opt In';
	}	
}else{ // else opt out
	$db->where('storeid', $_SESSION['storeid'])->where('id', $_POST['postid'])->delete('social_media_local_posts_optout');
	$db->where('storeid', $_SESSION['storeid'])->where('id', $_POST['postid'])->delete('social_media_local_posts_store');

	$data = Array (
					"id" => $post_id,
               		"storeid" => $_SESSION['storeid'],
               		"optout" => 1,
               		"date" => $db->now()
				  );
	$id = $db->insert ('social_media_local_posts_optout', $data);
	$action = 'Opt Out';
	if($id)
		echo "<a class='dropdown-item px-2 post_opts' id ='opt_".$post_id."' data-value='1' data-postid='".$post_id."' href='javascript:void(0)'>Opt In</a>";
}

$data = (count($_POST) > 0) ? $_POST : $_GET;
	
$dataAct = array("username"=>$_SESSION['email'],
				 "storeid"=>$_SESSION['storeid'],
				 "updates"=>json_encode($data),
				 "section"=>"plan-and-publish",
				 "details"=>$action
			 );
					 
track_activity($dataAct);
exit;