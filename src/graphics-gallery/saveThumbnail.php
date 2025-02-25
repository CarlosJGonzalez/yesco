<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");

$path = $db->escape($_POST['ogpath']);
$base64 = $db->escape($_POST['croppiebase64']);
$img = str_replace("data:image/png;base64","",$base64);
$file = end(explode("/",$path));

$jpeg = save_base64_image($base64, pathinfo($file, PATHINFO_FILENAME), $_SERVER['DOCUMENT_ROOT']."/uploads/gallery/thumbnails/");
$_SESSION['success'] = "Your image has been added.";
		header("location:/graphics-gallery/");
		exit;
if($jpeg){
	$_SESSION['success'] = "Your thumbnail has been saved.";
	header("location:/graphics-gallery/");
	exit;
}else{
	$_SESSION['error'] = "There was an error saving your thumbnail.";
	header("location:/graphics-gallery/");
	exit;
}

function save_base64_image($base64_image_string, $output_file_without_extension, $path_with_end_slash="" ) {
    $splited = explode(',', substr( $base64_image_string , 5 ) , 2);
    $mime=$splited[0];
    $data=$splited[1];

    $mime_split_without_base64=explode(';', $mime,2);
    $mime_split=explode('/', $mime_split_without_base64[0],2);
    if(count($mime_split)==2)
    {
        $extension=$mime_split[1];
        if($extension=='jpeg')$extension='jpg';

        $output_file_with_extension=$output_file_without_extension.'.'.$extension;
    }
    file_put_contents( $path_with_end_slash . $output_file_with_extension, base64_decode($data) );
    return $output_file_with_extension;
}
