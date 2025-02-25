<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/stripe.php");


$id = $db->escape($_POST['id']);
$customer_id = $db->escape($_POST['customer_id']);
$source = $db->escape($_POST['source']);
if(!$source || !$customer_id){
	$_SESSION['error'] = "Please select a payment method.";
	header("location:/graphics-gallery/requests/details.php?id=".$id);
	exit;
}
$db->where("id",$id);
$request = $db->getOne("custom_requests",array("quote"));

$success=0;
try {
	//Charge card
	\Stripe\Charge::create([
	  "amount" => $request['quote']*100,
	  "currency" => "usd",
	  "customer" => $customer_id, 
	  "source" => $source,
	  "description" => "Local ". CLIENT_NAME . " custom graphic request"
	]);
	$success=1;

} catch(Stripe_CardError $e) {
  $error[] = $e->getMessage();
} catch (Stripe_InvalidRequestError $e) {
  // Invalid parameters were supplied to Stripe's API
  $error[] = $e->getMessage();
} catch (Stripe_AuthenticationError $e) {
  // Authentication with Stripe's API failed
  $error[] = $e->getMessage();
} catch (Stripe_ApiConnectionError $e) {
  // Network communication with Stripe failed
  $error[] = $e->getMessage();
} catch (Stripe_Error $e) {
  // Display a very generic error to the user, and maybe send
  // yourself an email
  $error[] = $e->getMessage();
} catch (Exception $e) {
  // Something else happened, completely unrelated to Stripe
  $error[] = $e->getMessage();
}
if($success!=1){
	$_SESSION['error'] = "There was an error processing your payment. ";
	foreach($error as $e){
		$_SESSION['error'] .= "<br>".$e;
	}
}else{
	//Update status to paid
	$data = Array (
		'status' => 'Paid',
		'approved'=>'Paid'
	);
	$db->where ('id', $id);
	$db->update ('custom_requests', $data);
	
	//Track activity
	$data['id']=$id;
	$data['amount']=$request['quote'];
	$data['customer']=$customer_id;
	$data['source']=$source;
	$dataAct = array("username"=>$_SESSION['email'],
				 "storeid"=>$_SESSION['storeid'],
				 "updates"=>json_encode($data),
				 "section"=>"graphics-gallery",
				 "ip_address"=>get_ip(),
				 "details"=>"Paid for Custom Request");
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
	
	//Create notification
	$db->where("storeid",$_SESSION['storeid']);
	$location = $db->getOne("locationlist");
	create_notification(array("user_type"=>"das_admin",
							 "user_id"=>$_SESSION['user_id'],
							 "message"=>$location['companyname']." paid for a custom graphics request.",
							 "date"=>$db->now(),
							 "unread"=>"1",
							 "new"=>"1",
							 "msg_type"=>"graphics-gallery",
							 "icon"=>"fas fa-credit-card",
							 "link"=>"/admin/graphics-gallery/details.php?id=".$id), $emails_tokens);
	$_SESSION['success'] = "Your payment is being processed.";
}


header("location:/graphics-gallery/requests/details.php?id=".$id);
