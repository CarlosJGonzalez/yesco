<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");

$id = $db->escape($_GET['id']);
$data = Array (
	'status' => 'Canceled'
);
$db->where ('id', $id);
if($db->update ('custom_requests', $data)){
	$data['id']=$id;
	$dataAct = array("username"=>$_SESSION['email'],
				 "storeid"=>$_SESSION['storeid'],
				 "updates"=>json_encode($data),
				 "section"=>"graphics-gallery",
				 "ip_address"=>get_ip(),
				 "details"=>"Canceled Request");
	$db->insert ('activity', $dataAct);
	
	//Selects the email and email_notification from the representative of the selected storeid 
	$sql_rep_users = "SELECT strl.email, strl.email_notification, strl.token FROM ".$_SESSION['database'].".storelogin strl, ".$_SESSION['database'].".storelogin_user_roles strlur, ".$_SESSION['database'].".reps rep, ".$_SESSION['database'].".locationlist loc WHERE strl.storeid<'0' AND strl.token !='' AND strl.id = strlur.id_storelogin AND strlur.id_user_roles = (SELECT id FROM ".$_SESSION['database'].".user_roles WHERE name = 'admin_rep') AND strl.email IN (SELECT email FROM ".$_SESSION['database'].".reps) AND strl.email = rep.email AND rep.id IN (SELECT rep FROM ".$_SESSION['database'].".locationlist) AND strl.notifications = '1' AND loc.storeid = ".$_SESSION['storeid']."";
	$rep_users = $db->rawQuery($sql_rep_users);
	
	//Selects the email or email_notification from all the art department users
	$sql_art_dep_users = "SELECT strl.email, strl.email_notification, strl.token FROM ".$_SESSION['database'].".storelogin strl, ".$_SESSION['database'].".storelogin_user_roles strlur WHERE strl.storeid<'0' AND strl.token != '' AND strl.id = strlur.id_storelogin AND strlur.id_user_roles = (SELECT id FROM ".$_SESSION['database'].".user_roles WHERE name = 'art_department') AND strl.notifications = '1' AND strl.status = 1";
	$art_dep_users = $db->rawQuery($sql_art_dep_users);
	
	$emails_tokens = array();
	
	//If the rep or art users have at least a email, it will store them. 
	if (!empty($rep_users) || !empty($art_dep_users)){
		
		$token = $rep_users[0]['token'];
		
		//Gets the email from the rep
		if(!empty($rep_users[0]['email_notification'])){
			$to = $rep_users[0]['email_notification'];
			$emails_tokens[] = array("to"=>$to, "token"=>$token);
		}elseif(!empty($rep_users[0]['email']) && filter_var($rep_users[0]['email'], FILTER_VALIDATE_EMAIL)){
			$to = $rep_users[0]['email'];
			$emails_tokens[] = array("to"=>$to, "token"=>$token);
		}

		//Gets the email from the art_dep_users
		foreach($art_dep_users as $art_dep_user){
			
			$token = $art_dep_user['token'];
			
			if(!empty($art_dep_user['email_notification'])){
				$to = $art_dep_user['email_notification'];
				$emails_tokens[] = array("to"=>$to, "token"=>$token);
			}elseif(!empty($art_dep_user['email']) && filter_var($art_dep_user['email'], FILTER_VALIDATE_EMAIL)){
				$to = $art_dep_user['email'];
				$emails_tokens[] = array("to"=>$to, "token"=>$token);
			}
		}
	}
	
	$db->where("storeid",$_SESSION['storeid']);
	$location = $db->getOne("locationlist");
	create_notification(array("user_type"=>"das_admin",
							 "user_id"=>$_SESSION['user_id'],
							 "message"=>$location['companyname']." canceled a custom graphics request.",
							 "date"=>$db->now(),
							 "unread"=>"1",
							 "new"=>"1",
							 "msg_type"=>"graphics-gallery",
							 "link"=>"/admin/graphics-gallery/details.php?id=".$id), $emails_tokens);
	$_SESSION['success'] = "Your request has been canceled.";
}else{
	$_SESSION['error'] = "There was an error canceling your request.";
}
if($_GET['type']=="user")
	header("location:/graphics-gallery/requests/details.php?id=".$id);
else
	header("location:/admin/graphics-gallery/details.php?id=".$id);
