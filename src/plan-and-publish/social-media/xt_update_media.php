<?php 
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");
include_once ($_SERVER['DOCUMENT_ROOT']."/includes/ClassDasPost.php");

function copyImage($addimage,$postid,$store_id,$url){
	$img_add=explode(';', $addimage);	

	foreach ($img_add as $img) {

		$ext = explode('.', $img);
		$type = isValidType($ext[count($ext)-1]);
		if ($type == 'image') {
			$target_dir = '/uploads/social-media-calendar/img/'.$postid.'_'.$store_id;
			$target_oring = '/uploads/social-media-calendar/img/';
		}elseif ($type == 'video') {
			$target_dir = '/uploads/social-media-calendar/video/'.$postid.'_'.$store_id;
			$target_oring = '/uploads/social-media-calendar/video/';
		}
		$target_file = $target_dir .'/'. $img;	

		if (!is_dir($url.$target_dir)) {
			mkdir($url.$target_dir, 0777, true);
		}	

	   	copy($url.$target_oring.$img, $url.$target_file);

	}
}

function delImage($del,&$orig,$url,$postid,$store_id,$store = false){

	$img_del = explode(';', $del);
	foreach ($img_del as $img) {
		if ($store) {
			
			$ext = explode('.', $img);
		
			$type = isValidType($ext[count($ext)-1]);
			if ($type == 'image') {
				$target_dir = '/uploads/social-media-calendar/img/'.$postid.'_'.$store_id;
			}elseif ($type == 'video') {
				$target_dir = '/uploads/social-media-calendar/video/'.$postid.'_'.$store_id;
			}
			$target_file = $target_dir .'/'. $img;	
			$file=glob($url.$target_file);	
		   	if ($file) {
		   		unlink($file[0]);
		   		unset($file);
		   	}
			$orig = str_replace(';'.$img, '', $orig);
			$orig = str_replace($img, '', $orig);
		}else{

			$orig = str_replace($img, '', $orig);
			$orig = str_replace(';'.$img, '', $orig);
		}
	}
}

function clone_post($post,$store_id){
	$bck_post = $post;
	$bck_post['storeid']=$store_id;
	unset($bck_post['notes']);
	unset($bck_post['boost']);
	return $bck_post;
}

$store_id=$_SESSION['storeid'];	
$dasboost = new Das_Post($db,$_SESSION['client'],$store_id);
$post = $dasboost->getPost($_POST['postid']);
$bck_post = clone_post($post,$store_id);

if(!isset($post['img'])){
	$orig_media= $_POST['orig_media'];

	if($_POST['del_media'] != ''){		
		delImage($_POST['del_media'],$orig_media,$_SERVER["DOCUMENT_ROOT"],$_POST['postid'],$store_id);
	}

	$post['img']=$orig_media;	
	$post['image']= '';	

	if(isset($_POST['url_video']) && $_POST['url_video'] !=''){
		$post['img']='';	
		$post['image']= $_POST['url_video'];	
		$post['video']=$orig_media;
	}
	
	$post['storeid']=$store_id;
	unset($post['notes']);
	unset($post['boost']);
	$id = $db->insert ('social_media_local_posts_store', $post);
	
	if (!$id){		
		$msg= "Error while updating your data please try again later.";		
		pageRedirect($msg, 'error', '/plan-and-publish/social-media/edit.php?id='.$_POST['postid']);
	}
	if(trim($orig_media) != '' &&  trim($orig_media) != ';'){
		copyImage($orig_media,$_POST['postid'],$store_id,$_SERVER["DOCUMENT_ROOT"]); 
	}
	   
}else{
	$orig_media= $_POST['orig_media'];

	if($_POST['del_media'] != ''){
		
		delImage($_POST['del_media'],$orig_media,$_SERVER["DOCUMENT_ROOT"],$_POST['postid'],$store_id,true);	
	}
	
	if(isset($_POST['url_video']) && $_POST['url_video'] !=''){
		$dasboost->storePostUpdate($_POST['postid'],array('img'=>'','image'=>$_POST['url_video'],'video'=>$orig_media));
	}else{
		$dasboost->storePostUpdate($_POST['postid'],array('img'=>$orig_media,'image'=>''));
	}	
}

$post = $dasboost->getPost($_POST['postid']);
$total = count($_FILES['fileToUpload']['name']);
$images = array();
$file_urls = array();
$flag_image= true;

for($i=0; $i<$total ;$i++){
	if($_FILES['fileToUpload']['error'][$i] == 0 ) {
		if(!empty($_FILES['fileToUpload']['name'][$i])) {
			$type = isValidType($_FILES['fileToUpload']['type'][$i]);			
			$is_video = false;
			if ($type == 'image') {
				$target_dir = '/uploads/social-media-calendar/img/'.$_POST['postid'].'_'.$store_id;
			}elseif ($type == 'video') {
				$orig = str_replace(';', '', $orig_media);
				if($orig == '' && $_POST['url_video'] !='' &&  $total == 1){
					$is_video = true;
				}else{
					$dasboost->storePostUpdate($_POST['postid'],$bck_post);
					$msg= "If you upload 1 video please after delete all medias";
					pageRedirect($msg, 'error', '/plan-and-publish/social-media/edit.php?id='.$_POST['postid']);					
				}

				$target_dir = '/uploads/social-media-calendar/video/'.$_POST['postid'].'_'.$store_id;
			}else{
				$dasboost->storePostUpdate($_POST['postid'],$bck_post);
				$msg= "Sorry, please your file image type(jpg | jpeg | png)  or video  type(mp4 | mov) ";
				pageRedirect($msg, 'error', '/plan-and-publish/social-media/edit.php?id='.$_POST['postid']);
				
			}

			$temp = explode(".", $_FILES['fileToUpload']['name'][$i]);
						
			$newfilename = slugify($temp[0]).'.'.end($temp);

			$target_file = $target_dir .'/'. $newfilename;	
	
		   	if (glob($_SERVER["DOCUMENT_ROOT"].$target_file)) {
		   		$sql= "update ".$_SESSION['database'].".social_media_local_posts_store set image='', img= REPLACE(img, ';".$newfilename."', '') where id='".$_POST['postid']."' and storeid='".$store_id."'";
				
				if ($is_video) {
					$sql= "update ".$_SESSION['database'].".social_media_local_posts_store set image='".$_POST['url_video']."', img='',video ='".$newfilename."' where id='".$_POST['postid']."' and storeid='".$store_id."'";
				}
				
				$db->rawQuery($sql);
				track_activity(array('section'=>'social-media','details' => $sql));
				
				if ($db->getLastErrno() === 0) { 
					$tmp= 'Update succesfull';
				}
				else{
					$dasboost->storePostUpdate($_POST['postid'],$bck_post);
					$msg= "Sorry, we have problem upload your information..";					
					pageRedirect($msg, 'error', '/plan-and-publish/social-media/edit.php?id='.$_POST['postid']);				
				}				    				
			}

			if (!is_dir($_SERVER["DOCUMENT_ROOT"].$target_dir)) {
				mkdir($_SERVER["DOCUMENT_ROOT"].$target_dir, 0777, true);
			}

			if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"][$i], $_SERVER["DOCUMENT_ROOT"].$target_file)) {
				$sql= "update social_media_local_posts_store set image='', img= CONCAT(img, ';".$newfilename."') where id='".$_POST['postid']."' and storeid='".$store_id."'";

				if ($is_video) {
					$sql= "update ".$_SESSION['database'].".social_media_local_posts_store set image='".$_POST['url_video']."', img='',video ='".$newfilename."' where id='".$_POST['postid']."' and storeid='".$store_id."'";
				}
			
				$db->rawQuery($sql);
				track_activity(array('section'=>'social-media','details' => $sql));
				if ($db->getLastErrno() !== 0) { 
					$dasboost->storePostUpdate($_POST['postid'],$bck_post);
					$msg= "Sorry, we have problem upload your file.";
					pageRedirect($msg, 'error', '/plan-and-publish/social-media/edit.php?id='.$_POST['postid']);
				}
				
				array_push($images,$newfilename);
				
				$local_site_url = rtrim(LOCAL_CLIENT_URL,"/");
				array_push($file_urls,$local_site_url.$target_file);
			}else{
				$dasboost->storePostUpdate($_POST['postid'],$bck_post);
				$msg= "Sorry, we have problem upload your file.";
				pageRedirect($msg, 'error', '/plan-and-publish/social-media/edit.php?id='.$_POST['postid']);	
			}

		}

	}
}// end for

//send email to rep if GMB
if($post['portal']=="Google" || ( $post['portal']=="Instagram" && in_array($_SESSION['storeid'], array('98')) ) ){
	$all_file_urls = implode(",",$file_urls);
	
	$to = "lisa@das-group.com,bperez@das-group.com";
	$subject = "Local ".CLIENT_NAME." ".$post['portal']." Image Post Changed. Id: ".$_POST['postid'];

	$message = "Location: ".$_SESSION['storeid']."<br>";
	$message .= "Original Post Date: ".date("m/d/Y", strtotime($post['date']))."<br>";
	$message .= "Original Post: ".$post['post']."<br>";
	$message .= "Image Urls: ".$all_file_urls."<br>";
	$message .= "Image changed. Check the post in the local site for all updates.";
	
	$client_store_id = $_SESSION['client'].'-'.$_SESSION['storeid'];
	
	//Send email to the reps
	$data_email = Array (
		'copy_hidden'=> 'dev@das-group.com',
		'subject'    => $subject,
		'from' 	     => 'DAS Group <noreply@das-group.com>',
		'sender'     => 'DAS Group <noreply@das-group.com>',
		'body' 	     => $message,
		'copy' 	     => '',
		'storeid' 	 => $client_store_id ,
		'to' 	     => $to
	);
	
	//$db->insert ('emails_send.emails', $data_email);
}

$msg= "Update successful";
pageRedirect($msg, 'success', '/plan-and-publish/social-media/edit.php?id='.$_POST['postid']);

function isValidType($mediatype){
	$image = array('image/png', 'image/jpeg', 'image/jpg','png','jpg','jpeg');
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