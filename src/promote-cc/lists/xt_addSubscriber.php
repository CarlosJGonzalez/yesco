<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/ClassDasConstantContact.php");

if(isset($_SESSION["user_role_name"])){
	$locationList = $db->where("storeid",$_SESSION['storeid'])->getOne("locationlist");
	
	//Only for test purpose
	/*$locationList['constant_contact_api_key'] = 'j3bn9adcxrgg2jvxd6nmg75b';
	$locationList['constant_contact_access_token'] = '138e5b8a-ad09-419b-92f7-399d64875e4f';*/

	if(empty($locationList['constant_contact_api_key']) || empty($locationList['constant_contact_access_token'])){
		$_SESSION['error'] = "Please enter a valid api key and token.";
		header('location: /settings/promote/');
		exit;
	}else{
		$cc_api_key = $locationList['constant_contact_api_key'];
		$cc_access_token = $locationList['constant_contact_access_token'];
	}

	//ClassDasConstantContact 
	$cc = new Das_ConstantContact($cc_api_key, $cc_access_token);

	$email = $db->escape($_POST['email']);
	$fname = $db->escape($_POST['fname']);
	$lname = $db->escape($_POST['lname']);
	$listid = $db->escape($_POST['listid']);
	$user_msg = '';
	$msg_type = '';
	
	if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
		
		$paramsContactByEmail = Array("email"=>$email);
		
		//Get contact info
		$contactByEmail = $cc->getContactByEmail($paramsContactByEmail);

		//Store contact id
		$contact_id = $contactByEmail['results'][0]['id'];

		if($contact_id){
			$contact_lists = $contactByEmail['results'][0]['lists'];
			
			if(count($contact_lists) > 0){
				$new_list_array = array();
				
				foreach($contact_lists as $list_cc){
					array_push($new_list_array, array("id" => $list_cc['id']));
				}
				array_push($new_list_array,array("id" => $listid));
				
				$paramsUpdatedContact = Array("lists"=>$new_list_array,
											  "email_addresses"=>[[
												   "email_address"=> $email
											   ]]
											   );
											   
				if($fname != '')
					$paramsUpdatedContact['first_name'] = $fname;
				
				if($lname != '')
					$paramsUpdatedContact['last_name']	= $lname;	
			}else{
				$paramsUpdatedContact = Array("lists"=>[[
												   "id"=>$listid
											   ]],
											   "email_addresses"=>[[
												   "email_address"=> $email
											   ]]
											   );
				
				if($fname != '')
					$paramsUpdatedContact['first_name'] = $fname;
				
				if($lname != '')
					$paramsUpdatedContact['last_name']	= $lname;	
											   
			}

			$updatedContact = $cc->updateContact($contact_id, $paramsUpdatedContact);

			if($updatedContact['id']){
				$user_msg = "Your contact have been successfully updated.";
				$msg_type = 'success';
			}else{
				$user_msg = "There was an error saving your changes. ".$updatedContact['error_info']['error_message'];
				$msg_type = 'error';
			}

		}else{						  
			$paramsNewContact = Array("lists"=>[[
								   "id"=>$listid
							   ]],
							   "email_addresses"=>[[
								   "email_address"=> $email
							   ]],
							   "first_name"=>$fname,
							   "last_name"=>$lname,
							   );
		
			$member = $cc->addContact($paramsNewContact);
			
			if($member['id']){
				$user_msg = "Your contact have been successfully added.";
				$msg_type = 'success';
			}else{
				$user_msg = "There was an error saving your changes. ".$member['error_info']['error_message'];
				$msg_type = 'error';
			}
		}

		pageRedirect($user_msg, $msg_type, "/promote-cc/lists/members.php?id=".$listid);

	}else{
		pageRedirect("Please enter a vaid email.", "error", "/promote-cc/lists/members.php?id=".$listid);
	}

}else{
	pageRedirect("You must be authorized to view this page.", "error", "/promote-cc/");
}