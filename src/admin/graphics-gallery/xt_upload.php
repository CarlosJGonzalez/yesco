<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");

$error = '';

/*If the request and processed method was POST, $_POST is empty, $FILE is empty, and the content length 
that was passed to the HTTP server from the client is greater than 0, there is file size overflow.*/
if ( $_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST) && empty($_FILES) && $_SERVER['CONTENT_LENGTH'] > 0 ){
	$postLength = '';
	$sizeType = '';
	//Gets the value of the post_max_size configuration option
	$displayMaxSize = ini_get('post_max_size');
	//Returns G, M, or K. The post_max_size comes with the format "8G, 8M, and 8K"
	$sizeType = substr($displayMaxSize,-1);

	//Formats the postLength depending on the sizeType
	switch ($sizeType) {
		case 'G':
			$postLength = $_SERVER['CONTENT_LENGTH'] / 1000000000;
			break;
		case 'M':
			$postLength = $_SERVER['CONTENT_LENGTH'] / 1000000;
			break;
		case 'K':
			$postLength = $_SERVER['CONTENT_LENGTH'] / 1000;
			break;
	}
 
	$error = 'Posted data is too large. All your files are '.$postLength.' '.$sizeType.', which exceed the maximum size of '.$displayMaxSize;
	redir($type, $error);
	
}else{

	$type = $db->escape($_POST['type']);

	if ($_FILES['fileToUpload']) {
		$file_ary = reArrayFiles($_FILES['fileToUpload']);
		
		$total = count($file_ary);
		
		foreach ($file_ary as $file) {

			$imgData = Array("target_dir"=>$_SERVER["DOCUMENT_ROOT"]."/uploads/gallery",
						 "allow_file_types"=>Array("JPG", "JPEG", "PNG", "GIF"),
						 "file"=>$file,
						 "make_thumbnail"=>1,
						 "thumbnail_dest"=>$_SERVER["DOCUMENT_ROOT"]."/uploads/gallery/thumbnails",
						 "thumbnail_width"=>250);
						
			if($_FILES["fileToUpload"]["error"][$i] == 0){
				
				$image = upload_image($imgData);
				
				if($image['success']==1){
					
					$temp = explode(".", $file["name"]);
					
					//If multiple files are uploaded it will take the image name of the file
					if($total>1)
						$nameOfImg = ucwords(str_replace("-"," ",clean($temp[0])));
					else
						$nameOfImg = ucwords($db->escape($_POST['name']));
					
					$name = $nameOfImg;
					$category = $db->escape($_POST['category']);
					$tags = $db->escape($_POST['tags']);
					$apply_all = $db->escape($_POST['apply_all']);
					$data = Array("name"=>$name,
								 "image"=>$image['path'],
								  "thumbnail"=>$image["thumbnail"]["path"],
								 "active"=>1,
								 "storeid"=>$_SESSION['storeid'],
								 "apply_all"=>$apply_all,
								 "category"=>$category,
								 "tags"=>$tags);
					$image_id = $db->insert("gallery",$data);
				}else redir($type,$image['error']);
			}else redir($type,$_FILES["fileToUpload"]["error"]);

		}
		
		$dataAct = array("username"=>$_SESSION['email'],
						 "storeid"=>$_SESSION['storeid'],
						 "updates"=>json_encode($data),
						 "section"=>"graphics-gallery",
						 "details"=>"Added an image. Id: ".$image_id
						 );
						 
		track_activity($dataAct);
		
		$_SESSION['success'] = "Your image(s) have been added.";
		redir($type);
	}

}

function redir($type,$error = ""){
	if(!empty($error)){
		$_SESSION['error'] = $error;
	}
	$url = $type=="admin" ? "/admin/graphics-gallery/" : "/graphics-gallery/";
	header("location:".$url);
	exit;
}
?>