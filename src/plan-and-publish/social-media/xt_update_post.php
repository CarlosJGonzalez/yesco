<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/
include_once ($_SERVER['DOCUMENT_ROOT']."/includes/head.php");
include_once ($_SERVER['DOCUMENT_ROOT']."/includes/ClassDasPost.php");
//Todo Replace code for class Das_Post.
$link = $post = $boost_amount = $boost_start = $boost_end = $postid = $is_store = $postdate =  "";

if ($_SERVER["REQUEST_METHOD"] == "POST" ) {

	$link 		  = clear_input($_POST['link']);
	$post 		  = clear_input($_POST['post']);
	$postdate 	  = clear_input($_POST['postdate']);
	$boost_amount = clear_input(isset($_POST['boost_amount']) ? $_POST['boost_amount'] : '');
	$boost_start  = clear_input(isset($_POST['boost_start']) ? $_POST['boost_start'] : '');
	$boost_end    = clear_input(isset($_POST['boost_end']) ? $_POST['boost_end']: '');
	$is_store     = clear_input($_POST['is_store']);
	$postid       = clear_input($_POST['postid']);

	$storeid = $_SESSION['storeid'];
	$orig_post = getPost($db,$storeid,$postid,$is_store);

	$orig_post_val = trim($orig_post['post']); // Only for email purpose
  	$orig_post_val = stripslashes($orig_post_val);
	$orig_date_val =  $orig_post['date']; // Only for email purpose
	$orig_link_val =  $orig_post['link']; // Only for email purpose
    $orig_post['post'] = mysql_escape_mimic($post);
    $orig_post['link'] = mysql_escape_mimic($link);
    $orig_post['date'] = date("Y-m-d H:i:s", strtotime($_POST['postdate']));
    $orig_post['boost_amount'] = $boost_amount;
    $orig_post['boost_start'] = date('Y-m-d', strtotime($boost_start));
    $orig_post['boost_end'] = ($boost_end != '0000-00-00' && $boost_end != '') ? date('Y-m-d', strtotime($boost_end)):'0000-00-00';

	$postid = add_update_post($db,$storeid,$postid,$orig_post,$is_store);
	
	if($postid){
		$portal = $orig_post['portal'];
		
		//send email to rep if GMB or Instagram
		if($orig_post['portal']=="Google" || ($orig_post['portal'] == 'Instagram' && in_array($_SESSION['storeid'], array('98') ) ) ){
			$client_store_id = $_SESSION['client'].'-'.$_SESSION['storeid'];
			
			$emails_tokens = getRepUserInfo($_SESSION['storeid']);
			$to = $emails_tokens['data']['to'];
			$rep_token = $emails_tokens['data']['token'];
			
			$location = $active_location['companyname']." (".$_SESSION['storeid'].")";
			
			$updated_post_val = ($post != $orig_post_val) ? $post : 'N/A';
			$updated_post_date_val = (date("Y-m-d H:i:s", strtotime($_POST['postdate']))  != $orig_date_val) ? date("Y-m-d H:i:s", strtotime($_POST['postdate']))  : 'N/A';
			$updated_post_link_val = ($link !=  str_replace("[[site_url]]",$active_location['url'],$orig_link_val)) ? $link : 'N/A';

			if($updated_post_val != 'N/A' || $updated_post_date_val != 'N/A' || $updated_post_link_val != 'N/A'){
			
				$subject = "Local ".CLIENT_NAME." ".$orig_post['portal']." Post Changed Id: ".$orig_post['id'];
				$email_template = file_get_contents($_SERVER['DOCUMENT_ROOT']."/emails/update-post.php");
				$email_template = str_replace("%%USERNAME%%", $_SESSION['email'], $email_template);
				$email_template = str_replace("%%LOCATION%%", $location, $email_template);
				$email_template = str_replace("%%ORIGINAL_POST_DATE%%", date("Y-m-d H:i:s", strtotime($orig_date_val)), $email_template);
				$email_template = str_replace("%%ORIGINAL_POST%%", $orig_post_val, $email_template);
				$email_template = str_replace("%%ORIGINAL_POST_LINK%%", $orig_link_val, $email_template);
				$email_template = str_replace("%%UPDATED_POST_DATE%%", $updated_post_date_val, $email_template);
				$email_template = str_replace("%%UPDATED_POST%%", $updated_post_val, $email_template);
				$email_template = str_replace("%%UPDATED_POST_LINK%%", $updated_post_link_val, $email_template);
				$email_template = str_replace("%%CLIENT_URL%%", CLIENT_URL, $email_template);
				$email_template = str_replace("%%LOCAL_CLIENT_URL%%", LOCAL_CLIENT_URL, $email_template);
				$email_template = str_replace("%%CLIENT_NAME%%", CLIENT_NAME, $email_template);
				$email_template = str_replace("%%YEAR%%", date("Y"), $email_template);
				$email_template = str_replace("%%LOCAL_URL_DEST%%", LOCAL_CLIENT_URL, $email_template);
								
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
				
				//$db->insert ('emails_send.emails', $data_email);
				
				/*$message = "Location: ".$active_location['companyname']." (".$_SESSION['storeid'].")"."<br>";
				$message .= "Original Post Date: ".date("m/d/Y", strtotime($postdate))."<br>";
				$message .= "Original Post: ".$orig_post['post']."<br>";
				if($post != $orig_post['post']) $message .= "New Post: ".$post."<br>";
				$message .= "Check the post in the local site for all updates.";*/
				
				/*create_notification(array("user_type"=>"das_admin",
										  "message"=>$active_location['companyname']." edited a post.",
										  "date"=>$db->now(),
										  "unread"=>"1",
										  "new"=>"1",
										  "msg_type"=>"social-media",
										  "link"=>"/plan-and-publish/social-media/edit.php?storeid=".$storeid."&view=user&id=".$postid,
										 ),
										 $emails_tokens
									);
				*/
			}
		}
		
		$data_track = array("updates"=>json_encode($orig_post),"section"=>"social-media", "details"=>"Local ".CLIENT_NAME." ".$portal." Post Changed");
		track_activity($data_track);

    	$_SESSION['success']="Your changes have been successfully saved.";
    }else{
    	$_SESSION['error'] = "There was an error updating your post in the db.";
		header('location:/plan-and-publish/social-media/');
		exit;
    }
}else{
	$_SESSION['error'] = "Invalid request.";
	header('location:/plan-and-publish/social-media/');
	exit;
}

header('location:/plan-and-publish/social-media/edit.php?id='.$postid);
exit;

function getPost(&$db,$storeid,$postid,$is_store){

	if ($is_store) {
		return $db->where('id',$postid)->where('storeid',$storeid)->getOne('social_media_local_posts_store');
	}

	return $db->where('id',$postid)->getOne('social_media__local_posts');
}

function clear_input($data) {
  	$data = trim($data);
  	$data = stripslashes($data);
  	$data = htmlspecialchars($data);
  	return $data;
}

function add_update_post(&$db,$storeid,$postid,$data,$is_store){
	if ($is_store) {
		$db->where('id',$postid)->where('storeid',$storeid);
		$data['boost_store']=0;
		if ($db->update ('social_media_local_posts_store', $data))
			return $postid;
		else
			return false;
	}

	$data['storeid']=$storeid;
	unset($data['notes']);
	unset($data['boost']);
	unset($data['id']);
	$id = $db->insert ('social_media_local_posts_store', $data);

	if ($id){
		$data = Array (
					"id" => $postid,
               		"storeid" => $storeid,
               		"optout" => 1,
               		"date" => $db->now()
				  );
		$db->insert ('social_media_local_posts_optout', $data);

		return $id;
	}else{
		return false;
	}
	   
}
?>