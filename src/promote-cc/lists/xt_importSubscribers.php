<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/ClassDasConstantContact.php");

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

$file_types = Array("text/csv","application/vnd.ms-excel","ms-excel");
$listid = $db->escape($_POST['listid']);

$error_msg = '';
$success_msg = '';

if(isset($_POST['importSubmit']) && !empty($_FILES['file'])){
    
    // Allowed mime types
    $csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');
    
    // Validate whether selected file is a CSV file
    if(!empty($_FILES['file']['name']) && in_array($_FILES['file']['type'], $csvMimes)){

        // If the file is uploaded
        if(is_uploaded_file($_FILES['file']['tmp_name'])){
            
            // Open uploaded CSV file with read-only mode
            $csvFile = fopen($_FILES['file']['tmp_name'], 'r');
            
            // Skip the first line
            fgetcsv($csvFile);
          
			$membersInsertedOk = array();
		  
            // Parse data from CSV file line by line
            while(($line = fgetcsv($csvFile)) !== FALSE){
				
				$email = $line[0];
				$fname = $line[1];
				$lname = $line[2];
				$user_msg = '';
				$msg_type = '';
					
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
						$success_msg .= "<p>The member : ". $line[0]. ' was created sucessfully! </p>';
						$msg_type = 'success';
						array_push($membersInsertedOk, "success");
					}else{
						$error_msg .= "<p>There was an error adding the member: ". $line[0]. '. '. $member['error_info']['error_message'] .'</p>';
						$msg_type = 'error';
						array_push($membersInsertedOk, "fail");
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
					
					//If there is an error trying to add an specific member, "fail" will be added to the array membersInsertedOk
					if($member['id']){
						$success_msg .= "<p>The member : ". $line[0]. ' was created sucessfully! </p>';
						$msg_type = 'success';
						array_push($membersInsertedOk, "success");
					}else{
						$error_msg .= "<p>There was an error adding the member: ". $line[0]. '. '. $member['error_info']['error_message'] .'</p>';
						$msg_type = 'error';
						array_push($membersInsertedOk, "fail");
					}
				}
				
            }
            
            // Close opened CSV file
            fclose($csvFile);
            
			//If there were not any error trying to add any of the imported members, the expected html will be print 
			if(!in_array("fail", $membersInsertedOk)){
				$_SESSION['success'] = "Your changes have been successfully saved.";
				header("Location:/promote-cc/lists/members.php?id=".$listid);
				exit;
			}else{
				if(!in_array("success", $membersInsertedOk)){
					$_SESSION['error'] = $error_msg;
					header("Location:/promote-cc/lists/members.php?id=".$listid);
					exit;
				}else{
					$_SESSION['success'] = $success_msg;
					$_SESSION['error'] = $error_msg;
					header("Location:/promote-cc/lists/members.php?id=".$listid);
					exit;
				}
			}
			
        }else{
            $_SESSION['error'] = "There was an error saving your changes. The file has an error.";
			header("Location:/promote-cc/lists/members.php?id=".$listid);
			exit;
        }
    }else{
        $_SESSION['error'] = "Only .csv files can be imported.";
		header("Location:/promote-cc/lists/members.php?id=".$listid);
		exit;
    }
}else{
	$_SESSION['error'] = "There was an error saving your changes.";
	header("Location:/promote-cc/lists/members.php?id=".$listid);
	exit;
}

?>