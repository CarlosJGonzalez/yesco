<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");

$type = $db->escape($_POST['type']);
$video_link = $db->escape($_POST['video_link']);

$imgData = Array("target_dir"=>$_SERVER["DOCUMENT_ROOT"]."/uploads/gallery",
				 "allow_file_types"=>Array("MP4", "MOV"),
				 "file"=>$_FILES["fileToUpload"],
				 "make_thumbnail"=>1,
				"thumbnail_dest"=>$_SERVER["DOCUMENT_ROOT"]."/uploads/gallery/thumbnails",
				"thumbnail_width"=>250,
				"type"=>"video",
				"src"=>$video_link);

if($_FILES["fileToUpload"]["error"] == 0){
	$image = upload_image($imgData);
	if($image['success']==1){
		$name = $db->escape($_POST['name']);
		$category = $db->escape($_POST['category']);
		$tags = $db->escape($_POST['tags']);
		$apply_all = $db->escape($_POST['apply_all']);
		$data = Array("name"=>$name,
					 "video_raw"=>$image['path'],
					 "video_link"=>$video_link,
					 "thumbnail"=>$image["thumbnail"]["path"],
					 "active"=>1,
					 "storeid"=>$_SESSION['storeid'],
					 "apply_all"=>$apply_all,
					 "category"=>$category,
					 "tags"=>$tags,
					 "image"=>$image['image']);
		$video_id = $db->insert("gallery",$data);
		
		$dataAct = array("username"=>$_SESSION['email'],
						 "storeid"=>$_SESSION['storeid'],
						 "updates"=>json_encode($data),
						 "section"=>"graphics-gallery",
						 "details"=>"Added an video. Id: ".$video_id
						 );
	
		track_activity($dataAct);
		
		$_SESSION['success'] = "Your video has been added.";
		redir($type);

	}else redir($type,$image['error']);
}else redir($type,$_FILES["fileToUpload"]["error"]);

function redir($type,$error = ""){
	if(!empty($error)){
		$_SESSION['error'] = $error;
	}
	$url = $type=="admin" ? "/admin/graphics-gallery/" : "/graphics-gallery/";
	header("location:".$url);
	exit;
}