<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/ClassDasMC.php");

if(isset($_SESSION["user_role_name"])){
	
	$locationList = $db->Where("storeid", $_SESSION['storeid'])->getOne("locationlist");

	if(empty($locationList['loyalty_promotions_key'])){
		$_SESSION['error'] = "Please enter a key.";
		header('location: /settings/promote/');
		exit;
	}else{
		$mc_api_key = $locationList['loyalty_promotions_key'];
	}

	$mc = new Das_MC($mc_api_key);
	
	$list_name = $db->escape($_POST['list_name']);
	$from_name = $db->escape($_POST['from_name']);

	$params = Array("name"=>$list_name,
				   "contact"=>[
					   "company"=>$locationList['displayname'],
					   "address1"=>$locationList['address'],
					   "city"=>$locationList['city'],
					   "state"=>$locationList['state'],
					   "zip"=>$locationList['zip'],
					   "country"=>$locationList['country']
				   ],
				   "permission_reminder"=>"You are receiving this from Fully Promoted.",
				   "campaign_defaults"=>[
					   "from_name"=>$from_name,
					   "from_email"=>"info@localfullypromoted.com",
					   "subject"=>"Fully Promoted",
					   "language"=>"English"
				   ],
				   "email_type_option"=>false);
	$list = $mc->addList(json_encode($params));

	if($list ["is_error"] == 0){

		$data = Array ("storeid" => $_SESSION['storeid'],
					   "list_id" => $list['id']
		);

		if($id = $db->insert ('mailchimp_lists', $data)){
			$_SESSION['success'] = "Your changes have been successfully saved.";
			header("Location:/promote/lists/members.php?id=".$list['id']);
			exit;
		}else{
			$_SESSION['error'] = "Sorry! There was an error creating your list.";
			header("Location:/promote/lists/create.php");
			exit;
		}

	}else{
		$_SESSION['error'] = "There was an error creating your list.";
		header("Location:/promote/lists/create.php");
		exit;
	}

}else{
	pageRedirect("You must be authorized to view this page.", "error", "/promote/");
}