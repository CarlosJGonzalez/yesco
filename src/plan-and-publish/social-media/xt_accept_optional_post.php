<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");
include_once ($_SERVER['DOCUMENT_ROOT']."/includes/ClassDasPost.php");
//Todo Replace code for class Das_Post.
$link = $post = $boost_amount = $boost_start = $boost_end = $postid = $is_store = $postdate =  "";

if(!$_SESSION['storeid']){
		$msg="Error: missing ID";
		pageRedirect($msg ,'error', '/plan-and-publish/social-media/');
	}

if ($_SERVER["REQUEST_METHOD"] == "POST" ) {

	$postid    = clear_input($_POST['postid']);
	$orig_post = getOptionalPost($db,$postid);

	$store_id = $_SESSION['storeid'];
	$daspost = new Das_Post($db,$_SESSION['client'],$store_id);

	$orig_post['storeid']=$store_id;
	unset($orig_post['notes']);
	unset($orig_post['boost']);
	$orig_post['boost_amount'] = 0;
    $orig_post['boost_start'] = '0000-00-00';
    $orig_post['boost_end'] = '0000-00-00';

	$post_id=$daspost->createStorePost($orig_post);

	if($postid){
		$portal = $orig_post['portal'];
		
		//send email to rep if GMB
		if($orig_post['portal']=="Google"){
			$client_store_id = $_SESSION['client'].'-'.$_SESSION['storeid'];
			
			$emails_tokens = getRepUserInfo($_SESSION['storeid']);
			$to = $emails_tokens['data']['to'];
			$rep_token = $emails_tokens['data']['token'];
			
			$location = $active_location['companyname']." (".$_SESSION['storeid'].")";
			
			$subject = "Local ".CLIENT_NAME." Accept Optional Post Id: ".$orig_post['id'];
			$email_template = file_get_contents($_SERVER['DOCUMENT_ROOT']."/emails/optional-post.php");
			$email_template = str_replace("%%USERNAME%%", $_SESSION['email'], $email_template);
			$email_template = str_replace("%%LOCATION%%", $location, $email_template);
			$email_template = str_replace("%%POSTDATE%%", date("Y-m-d H:i:s", strtotime($orig_post['date'])), $email_template);

			$email_template = str_replace("%%CLIENT_URL%%", CLIENT_URL, $email_template);
			$email_template = str_replace("%%LOCAL_CLIENT_URL%%", LOCAL_CLIENT_URL, $email_template);
			$email_template = str_replace("%%CLIENT_NAME%%", CLIENT_NAME, $email_template);
			$email_template = str_replace("%%YEAR%%", date("Y"), $email_template);
			$email_template = str_replace("%%LOCAL_URL_DEST%%", LOCAL_CLIENT_URL, $email_template);

			$body = "This location accepted an optional post.";
			$email_template = str_replace("%%BODY%%", $body, $email_template);
							
			//Send email to the reps
			$data_email = Array (
				'copy_hidden'=> 'dev@das-group.com',
				'subject'    => $subject,
				'from' 	     => 'DAS Group <noreply@das-group.com>',
				'sender'     => 'DAS Group <noreply@das-group.com>',
				'body' 	     => $email_template,
				'copy' 	     => 'lisa@das-group.com,bperez@das-group.com',
				'storeid' 	 => $client_store_id ,
				'to' 	     => $to
			);
			
			$db->insert ('emails_send.emails', $data_email);			


		}
		
		$data_track = array("updates"=>json_encode($orig_post),"section"=>"social-media", "details"=>"Local ".CLIENT_NAME." ".$portal." Accept Optional Post");
		track_activity($data_track);

    	echo $postid;
		exit;
    }else{
    	$_SESSION['error'] = "There was an error updating your post in the db.";
		header('location:/plan-and-publish/social-media/');
		exit;
    }
}else{
	$_SESSION['error'] = "Invalid request.";
	echo -1;
	exit;
}

echo $postid;
exit;

function getOptionalPost(&$db,$postid){
	return $db->where('id',$postid)->getOne('social_media__local_posts_optional');
}

function clear_input($data) {
  	$data = trim($data);
  	$data = stripslashes($data);
  	$data = htmlspecialchars($data);
  	return $data;
}
?>