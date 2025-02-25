<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/ClassDasClickUp.php");

$values = array();
foreach($_POST as $key => $val){
	$values[$key] = $db->escape($val);
}
if(!empty($values["gallery_id"])){
	$db->where("id",$values["gallery_id"]);
	$img = $db->getOne("gallery");

//	function getImg($url){
//		$curl = curl_init($url);
//		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
//		$data = curl_exec($curl);
//		curl_close($curl);
//		return $data;
//	}
//
//	$raw = getImg($img["image"]);
//	$im = imagecreatefromstring($raw);
//	$width = imagesx($im);
//	$height = imagesy($im);
	$title = $img["name"];
}
//$defaults = array("title"=>$title,
//				 "status"=>"Pending",
//				 "dimensions"=>$width."x".$height." Pixels",
//				 "orientation"=>($width>$height) ? "Landscape" : "Portrait");
$defaults = array("title"=>$title,
				 "status"=>"Pending",
				 "dimensions"=>"N/A",
				 "orientation"=>"N/A");

$data = array_merge($defaults,$values);
$data["storeid"] = $_SESSION['storeid'];

$request_id = $db->insert("custom_requests",$data);
if($request_id){
	$dataAct = array("username"=>$_SESSION['email'],
				 "storeid"=>$_SESSION['storeid'],
				 "updates"=>json_encode($data),
				 "section"=>"gallery",
				 "ip_address"=>get_ip(),
				 "details"=>"Requested Graphic ");
	$id = $db->insert ('activity', $dataAct);
	
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
		/*foreach($art_dep_users as $art_dep_user){
			
			$token = $art_dep_user['token'];
			
			if(!empty($art_dep_user['email_notification'])){
				$to = $art_dep_user['email_notification'];
				$emails_tokens[] = array("to"=>$to, "token"=>$token);
			}elseif(!empty($art_dep_user['email']) && filter_var($art_dep_user['email'], FILTER_VALIDATE_EMAIL)){
				$to = $art_dep_user['email'];
				$emails_tokens[] = array("to"=>$to, "token"=>$token);
			}
		}*/
	}
	
	$db->where("storeid",$_SESSION['storeid']);
	$location = $db->getOne("locationlist");
	create_notification(array("user_type"=>"das_admin",
							 "message"=>$location['companyname']." submitted a custom graphics request.",
							 "date"=>$db->now(),
							 "unread"=>"1",
							 "new"=>"1",
							 "msg_type"=>"graphics-gallery",
							 "link"=>"/admin/graphics-gallery/details.php?id=".$request_id,
							 ), $emails_tokens);
							 
	//Creates a click up task
	$message = "Processed on: " .date('m/d/Y h:i'). " ET <br>";
	$message .= "<strong>Store Details</strong><br>";
	$message .= "Store ID: ".$_SESSION['storeid'].'<br>';
	$message .= "Company Name: ".$location['companyname'].'<br>';
	$message .= "Address: ".$location['address'].' '.$location['address2'].' '.$location['city'].', '.$location['state'].' '.$location['zip'].'<br>';
	$message .= "Phone: ".$location['phone'].'<br>';
	$message .= "Email: ".$location['email'].'<br>';
	$message .= "URL: ".$location['url'].'<br>';
	$message .= "<hr>";
	$message .= "<strong>Order Details</strong><br>";
	$message .= "Name: ".$title.'<br>';
	$message .= "Category: ".$img["category"].'<br>';
	$message .= "Image: <a href='".$img['image']."'>".$title.'</a><br>';
	$message .= "Details: ".$values['job_details'].'<br>';	
	
	//$team_id   = '1284882';
	$name_clickUp = "Local ".CLIENT_NAME." Gallery Orders";
	$body_clickUp = $message;
	$body_clickUp = str_replace("<br>", "\n", $body_clickUp);
	$body_clickUp = str_replace("<hr>", "\n", $body_clickUp);
	$body_clickUp = str_replace("<strong>", "***", $body_clickUp);
	$body_clickUp = str_replace("</strong>", "***", $body_clickUp);
	//$clickUp = new Das_ClickUp('pk_ZXPF8MRVJVZ9JBNYUEJNGL72O9TFU0H4');
	// Click up lexis ID '4250799'
	//$clickUp->newTask($team_id,'Creative','Gallery Orders','FP',$name_clickUp,$body_clickUp,array( '1477597','1475750'));
	$clickUp = new Das_ClickUp($token_api);	
	$clickUp->newTask('DAS','Creative','Gallery Orders','FP',$name_clickUp,$body_clickUp,array( '1477597', '1475750') );
	
	
	$user_store_id = $_SESSION['client'].'-'.$_SESSION['storeid'];
	$subject = CLIENT_NAME.'. Location:'.$location['companyname']." submitted a custom graphics request.";
	//Send email to the user
	$data_email = Array (
		'copy_hidden'=> 'sicwing@das-group.com,adrian@das-group.com',
		'subject'    => $subject,
		'from' 	     => 'DAS Group <noreply@das-group.com>',
		'sender'     => 'DAS Group <noreply@das-group.com>',
		'body' 	     => $message,
		'copy' 	     => '',
		'storeid' 	 => $user_store_id,
		'to' 	     => 'andrea@das-group.com,kaitlyn@das-group.com'
	);
	
	$db->insert ('emails_send.emails', $data_email);
	
	$_SESSION['success'] = "Your request has been sent.";
	
	header("location:/graphics-gallery/requests/");
	exit;
}else{
	$_SESSION['error'] = "There was an error sending your request.";
	header("location:/graphics-gallery/requests/");
	exit;
}