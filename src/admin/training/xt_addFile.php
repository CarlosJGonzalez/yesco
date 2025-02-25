<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");

$error = '';

if ( $_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST) && empty($_FILES) && $_SERVER['CONTENT_LENGTH'] > 0 ){
	$postLength = '';
	$sizeType = '';
	$displayMaxSize = ini_get('post_max_size');
	$sizeType = substr($displayMaxSize,-1);

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
	$_SESSION['error'] = $error;
	
}else{
	$name = $db->escape($_POST['name']);
	$category = $db->escape($_POST['category']);
	$description = $db->escape($_POST['description']);
	$show_link = $db->escape($_POST['show_link']);
	
	//If a file was not uploaded, it will save the link only
	if($_FILES["fileToUpload"]["error"] == 4 ){
		$data = array("name"=>$name,
					  "active"=>1,
					  "description"=>$description,
					  "category"=>$category,
					  "show_link"=>$show_link,
					  "date_added"=>$db->now());
					  
		if($db->insert("training",$data)){
			$data_track = array("updates"=>json_encode($data),"section"=>"training", "details"=>"Added a file: ".$name);
			track_activity($data_track);
			
			$_SESSION['success']="Your file has been successfully uploaded.";
		}else
			$_SESSION['error'] = "Sorry, there was an error uploading the file to the db.";

	}elseif($_FILES["fileToUpload"]["error"] != 4 && $_FILES["fileToUpload"]["error"] == 0){
		$BASE_URL = getFullUrl();

		//Getting the file size value in the $_FILES array
		$arraySum = $_FILES["fileToUpload"]["size"];

		//if file size value is less or equal to 2MB, the file will be uploaded
		if($arraySum <= 40000000){
			if(!empty($_FILES['fileToUpload']['name'])) {
				$target_dir = $_SERVER["DOCUMENT_ROOT"]."/uploads/training/";

				$temp = explode(".", $_FILES["fileToUpload"]["name"]);
				$newfilename = str_replace(" ","-",$temp[0]).'-'.$_SESSION['storeid'].'.'.end($temp);
				
				$uploadOk = 1;
				$imageFileType = strtolower(pathinfo($newfilename,PATHINFO_EXTENSION));
			
				// Check if file already exists
				$target_file = checkFile($target_dir,$newfilename);
				$newfilename=end(explode("/", $target_file));
			
				if($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg" || $imageFileType == "gif" ) {
					$show_link = $BASE_URL."/uploads/training/".$newfilename;
				}
				
				if (!is_dir($target_dir)) {
					mkdir($target_dir, 0777, true);
				}
				
				// Check if $uploadOk is set to 0 by an error
				if ($uploadOk == 0) {
					$_SESSION['error'] =  "Sorry, there was an error uploading your file: ".$error;
				// if everything is ok, try to upload file
				}else{
					if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
						$data = array("name"=>$name,
									  "active"=>1,
									  "description"=>$description,
									  "category"=>$category,
									  "download_link2"=>$BASE_URL."/uploads/training/".$newfilename,
									  "show_link"=>$show_link,
									  "date_added"=>$db->now());
									  
						if($db->insert("training",$data)){
							
							$data_track = array("updates"=>json_encode($data),"section"=>"training", "details"=>"Created: ".$name);
							track_activity($data_track);
							
							$_SESSION['success']="Your file has been successfully uploaded.";
						}else
							$_SESSION['error'] = "Sorry, there was an error uploading the file to the dashboard. ";
					}else 
						$_SESSION['error'] = "Sorry, there was an error uploading the file to the server. ";
				}
			}
		}else {
			$_SESSION['error'] = "Your file size must be less than 40MB. Please optimize your file before uploading.";
		}	
	}else{
		$_SESSION["error"] = "There was an error uploading your file.";
	}
}
header("location:/admin/training/");