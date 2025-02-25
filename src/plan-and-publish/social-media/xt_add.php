<?php
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");
include_once ($_SERVER['DOCUMENT_ROOT']."/includes/ClassDasPost.php");
session_start();

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
	pageRedirect($error, 'error', '/plan-and-publish/social-media/');
	
}else{
	if(!$_SESSION['storeid']){
		$msg="Error: missing ID";
		pageRedirect($msg ,'error', '/plan-and-publish/social-media/');
	}


	if((!isset($_POST['strpost']) || $_POST['strpost'] == '') || (!isset($_POST['postlink']) || $_POST['postlink'] == '') || (!isset($_POST['portal']) || $_POST['portal'] == '') ){
		$msg="Please check your information.";
		pageRedirect($msg ,'error', '/plan-and-publish/social-media/');
	}

	$strpost = htmlspecialchars($_POST['strpost']);

	if($_POST['boost_start']) $boost_start = date('Y-m-d', strtotime($_POST['boost_start']));
	else $boost_start = "0000-00-00";
	if($_POST['boost_end']) $boost_end = date('Y-m-d', strtotime($_POST['boost_end']));
	else $boost_end = "0000-00-00";

	//Default Time
	$default_time = "10:00:00";
	$postdate = date("Y-m-d H:i:s", strtotime($_POST['postdate']));
	
	$rautoId = $db->rawQueryOne("SELECT MAX(id) as id FROM social_media__local_posts");

	$nextId = ( $autoId['id'] + 10000 );
	$nextId = nextId($db,$nextId);

	$data_post = array(
						'id'=>$nextId,
						'storeid'=>$_SESSION['storeid'],
						'date'=>$postdate,
						'post'=>mysql_escape_mimic($strpost),
						'link'=>mysql_escape_mimic($_POST['postlink']),
						'portal'=>$_POST['portal'],
						'boost_start'=>$boost_start,
						'boost_end'=>$boost_end,
						'boost_amount'=>isset($_POST['boost_amount'])?$_POST['boost_amount']:'0.00',
					  ); 

 	$store_id = $_SESSION['storeid'];
	$daspost = new Das_Post($db,$_SESSION['client'],$store_id);
	$post_id=$daspost->createStorePost($data_post);

	if($post_id){

		$track = array('updates' => json_encode($data_post) ,'details'=>'Add new Post' );
		track_activity($track);
		
		$_SESSION['success']="Your post has been successfully added.";

		$total = count($_FILES['fileToUpload']['name']);
		$images = array();
		//Calculate the sum of all file sizes values in the $_FILES array
		$arraySum = array_sum($_FILES["fileToUpload"]["size"]);
		
		//If there are less than 4 files, the script will continue
		if($total < 4) {

			//if file size value is less or equal to 40MB, the file(s) will be uploaded
			if($arraySum <= 40000000){
		
				for($i=0;$i<$total;$i++){
					if(!empty($_FILES['fileToUpload']['name'][$i])) {

						$temp = explode(".", $_FILES['fileToUpload']['name'][$i]);
						
						$newfilename = slugify($temp[0]).'.'.end($temp);
						
						$type = isValidType($_FILES['fileToUpload']['type'][$i]);			
						$is_video = false;
						
						if ($type == 'image') {
							$target_dir = '/uploads/social-media-calendar/img/'.$post_id.'_'.$store_id;
						}elseif ($type == 'video') {
							$target_dir = '/uploads/social-media-calendar/video/'.$post_id.'_'.$store_id;
							$is_video = true;
							if(count($total) > 1){
								$msg= "Only add 1 video.";
								pageRedirect($msg, 'error', '/plan-and-publish/social-media/edit.php?id='.$post_id);	
							}
						}else{
							$msg= "Is no a valid file.Please check your file";
							pageRedirect($msg, 'error', '/plan-and-publish/social-media/edit.php?id='.$post_id);					
						}
						
						$target_file = $target_dir .'/'. $newfilename;	
						
						$uploadOk = 1;
						$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
						if(isset($_POST["submit"])) {
							$check = getimagesize($_FILES["fileToUpload"]["tmp_name"][$i]);
							if($check !== false) {
								$uploadOk = 1;
							} else {
								$error= "File is not an image: ".$newfilename."<br>";
								$uploadOk = 0;
							}
						}
						// Check if file already exists
						if (glob($_SERVER["DOCUMENT_ROOT"].$target_file)) {
							if($is_video){
								$sql= "update ".$_SESSION['database'].".social_media_local_posts_store set img='',image='',video= '".$newfilename."' where id='".$post_id."' and storeid='".$store_id."'";
							}else{
								$sql= "update ".$_SESSION['database'].".social_media_local_posts_store set img= REPLACE(img, ';".$newfilename."', '') where id='".$post_id."' and storeid='".$store_id."'";
							}
							
							$db->rawQuery($sql);
							track_activity(array('section'=>'social-media','updates' => $sql));
						}
						
						if (!is_dir($_SERVER["DOCUMENT_ROOT"].$target_dir)) {
							mkdir($_SERVER["DOCUMENT_ROOT"].$target_dir, 0777, true);
						}
						// Check if $uploadOk is set to 0 by an error
						if ($uploadOk == 0) {
							echo "Sorry, your file was not uploaded: ".$newfilename."<br>";

						} else {
							
							
						if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"][$i], $_SERVER["DOCUMENT_ROOT"].$target_file)) {						
							if($is_video){
								$sql= "update ".$_SESSION['database'].".social_media_local_posts_store set img='',image='',video= '".$newfilename."' where id='".$post_id."' and storeid='".$store_id."'";
							}else{
								$sql= "update ".$_SESSION['database'].".social_media_local_posts_store set img= CONCAT(img, ';".$newfilename."') where id='".$post_id."' and storeid='".$store_id."'";
							}
							$db->rawQuery($sql);
							track_activity(array('section'=>'social-media','updates' => $sql));
							array_push($images,$newfilename);
						
						}else 
							$_SESSION['error'] .= "Sorry, there was an error uploading your file: ".$error;
						}
					}
				}
		
			}else {
				$_SESSION['error'] = "Your file size must be less than 40MB. Please, optimize your file(s) before uploading.";
			}

		}else {
			$_SESSION['error'] = "You can upload up to 20 images.";
		}
	}else
		$_SESSION['error']="There was an error adding your post.";
}
header('location:/plan-and-publish/social-media/');
exit;

function nextId(&$conn,&$lastId){
	$rautoId = $conn->rawQueryOne("SELECT id FROM ".$_SESSION['database'].".social_media_local_posts_store where id = '$lastId'");
	$rautoId2 = $conn->rawQueryOne("SELECT id FROM ".$_SESSION['database'].".social_media_posts where id = '$lastId'");
	$rautoId3 = $conn->rawQueryOne("SELECT id FROM ".$_SESSION['database'].".social_media__local_posts_optional where id = '$lastId'");
	
	if(count($rautoId) || count($rautoId2) || count($rautoId3)){
		$lastId +=1;
		$lastId = nextId($conn,$lastId);
	}

	return $lastId;
}

function isValidType($mediatype){
		$image = array('image/png', 'image/jpeg', 'image/jpeg','png','jpg','jpeg');
		$video = array('video/mp4', 'video/mov','video/MOV','mp4','mov','MOV');
		

		if(in_array($mediatype, $image)){
			return 'image';
		}

		if(in_array($mediatype, $video)){
			return 'video';
		}
		return false;
}
?>