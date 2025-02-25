<?php 
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/ClassDasConstantContact.php");

if(isset($_SESSION["user_role_name"])){
	
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

		$error_msg = '';

		$list_name = $db->escape($_POST['list_name']);
		//$from_name = $db->escape($_POST['from_name']);
		$optRadioSubscriber = $db->escape($_POST['optRadioSubscriber']);
		$authorizedCheck = $db->escape($_POST['authorizedCheck']);
		
		if(isset($authorizedCheck) && $authorizedCheck == '1'){
			$params = Array("name"=>$list_name,
							"status"=>"ACTIVE"
							);
			$list = $cc->addList($params);

			if(!$list ["is_error"]){

				$data = Array ("storeid" => $_SESSION['storeid'],
							   "list_id" => $list['id']
							);
				
				if($id_list_db = $db->insert ('promote_lists', $data)){
					
				//Get inside if the user clicked on the radio Add a Subscriber
				if($optRadioSubscriber == "addSubs"){
					$email = $db->escape($_POST['email']);
					$fname = $db->escape($_POST['fname']);
					$lname = $db->escape($_POST['lname']);
					$listid = $db->escape($_POST['listid']);
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
							$user_msg = "Your contact have been successfully updated.";
							$msg_type = 'success';
						}else{
							$user_msg = "There was an error updating your changes. ".$updatedContact['error_info']['error_message'];
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

					/* Gets the updated list of members after adding the subscribers because before this point the list didn't have members yet. 
					 * Therefore, now the $mc_refreshed_list['contact_count'] can be printed
					*/
					//$mc_refreshed_list = $cc->getContactsFromList($listid);
					
					if($msg_type == "success"){ ?>
						<label class="label cusor-pointer text-center d-flex successAddSubs" for="list-<?php echo $listid; ?>">
							<input  class="label__checkbox" type="radio" name="list" value="<?php echo $listid; ?>" type="checkbox" id="list-<?php echo $listid; ?>" <?php if(isset($_SESSION['post']) && $_SESSION['post']['list'] == $listid) echo "checked"; ?> />
							<span class="label__text d-flex align-items-center">
							  <span class="label__check d-flex rounded-circle mr-2">
								<i class="fa fa-check icon small"></i>
							  </span>
								<div class="d-inline-block cursor-pointer text-left"><span class="font-weight-light font-lg letter-spacing-1 "><?php echo $list['name']; ?></span><span class="small d-block">1 subscribers</span></div>
							</span>
						</label>
					<?php  
					}else{
						$db->where('id', $id_list_db);
						$db->delete('promote_lists');
						
						$cc->deleteList($listid);
						
						echo '<div class="alert alert-danger errorAddSubs" role="alert">'.$user_msg .'</div>';
					}
				}

				//Get inside if the user clicked on the radio Import Contacts
				if($optRadioSubscriber == "importSubs"){
					$file_types = Array("text/csv","application/vnd.ms-excel","ms-excel");
					$listid = $list['id'];
					if(!empty($_FILES['file'])){
						
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
									$paramsNewContact = Array("lists"=>[[
										   "id"=>$listid
									   ]],
									   "email_addresses"=>[[
										   "email_address"=>$line[0]
									   ]],
									   "first_name"=>$line[1],
									   "last_name"=>$line[2],
									   );

									$member = $cc->addContact($paramsNewContact);
									
									//If there is an error trying to add an specific member, "fail" will be added to the array membersInsertedOk
									if($member["is_error"] == '1'){
										$error_msg .= "<p>There was an error adding the member: ". $line[0]. '. '. $member['error_info']['error_message'] .'</p>';
										array_push($membersInsertedOk, "fail");
									}
									
									////If there is not any error trying to add an specific member, "success" will be added to the array membersInsertedOk
									if(!$member ["is_error"]){
										array_push($membersInsertedOk, "success");
									}
								}
								
								// Close opened CSV file
								fclose($csvFile);
											
								/* Gets the updated list of members after adding the subscribers because before this point the list didn't have members yet. 
								 * Therefore, now the $mc_refreshed_list['contact_count'] can be printed
								*/
								//$mc_refreshed_list = $cc->getContactsFromList($listid);
											
								//If there were not any error trying to add any of the imported members, the expected html will be print 
								if(!in_array("fail", $membersInsertedOk)){ ?>
									<label class="label cusor-pointer text-center d-flex successAddSubs" for="list-<?php echo $listid; ?>">
										<input  class="label__checkbox" type="radio" name="list" value="<?php echo $listid; ?>" type="checkbox" id="list-<?php echo $listid; ?>" <?php if(isset($_SESSION['post']) && $_SESSION['post']['list'] == $listid) echo "checked"; ?> />
										<span class="label__text d-flex align-items-center">
										  <span class="label__check d-flex rounded-circle mr-2">
											<i class="fa fa-check icon small"></i>
										  </span>
											<div class="d-inline-block cursor-pointer text-left"><span class="font-weight-light font-lg letter-spacing-1 "><?php echo $list['name']; ?></span><span class="small d-block"><?php echo $mc_refreshed_list['contact_count']; ?> subscribers</span></div>
										</span>
									 </label>
								<?php
								}else{
									/* If there was an error trying to add all the members from the file, the list will be deleted from MailChimp and the database.
									   Otherwise, if at least one member was added successfully. The list won't be deleted, but an error msg will show those who 
									   had the error.
									*/
									if(!in_array("success", $membersInsertedOk)){
										$db->where('id', $id_list_db);
										$db->delete('promote_lists');
										
										$cc->deleteList($listid);
										
										echo '<div class="alert alert-danger errorAddSubs" role="alert">'.$error_msg .'</div>';
									}else{
										?>
										<label class="label cusor-pointer text-center d-flex successAddSubs" for="list-<?php echo $listid; ?>">
											<input  class="label__checkbox" type="radio" name="list" value="<?php echo $listid; ?>" type="checkbox" id="list-<?php echo $listid; ?>" <?php if(isset($_SESSION['post']) && $_SESSION['post']['list'] == $listid) echo "checked"; ?> />
											<span class="label__text d-flex align-items-center">
											  <span class="label__check d-flex rounded-circle mr-2">
												<i class="fa fa-check icon small"></i>
											  </span>
												<div class="d-inline-block cursor-pointer text-left"><span class="font-weight-light font-lg letter-spacing-1 "><?php echo $list['name']; ?></span><span class="small d-block"><?php echo $mc_refreshed_list['contact_count']; ?> subscribers</span></div>
											</span>
										 </label>
										 <div class="alert alert-danger" role="alert"><?php echo $error_msg; ?></div>
										<?php
									}
									
								}
							
							}else{
								$db->where('id', $id_list_db);
								$db->delete('promote_lists');
								
								$cc->deleteList($listid);
								
								$error_msg = "There was an error uploading the file.";
								echo '<div class="alert alert-danger errorAddSubs" role="alert">'.$error_msg.'</div>';
							}
						}else{
							$db->where('id', $id_list_db);
							$db->delete('promote_lists');
							
							$cc->deleteList($listid);
							
							$error_msg = "Only .csv files can be imported.";
							echo '<div class="alert alert-danger errorAddSubs" role="alert">'.$error_msg.'</div>';
						}
					}else{
						$db->where('id', $id_list_db);
						$db->delete('promote_lists');
						
						$cc->deleteList($listid);
						
						$error_msg = "You must select a file.";
						echo '<div class="alert alert-danger errorAddSubs" role="alert">'.$error_msg.'</div>';
					}
				}

				}else{
					$cc->deleteList($listid);
					
					$error_msg = "Sorry! There was an error creating your list.";
					echo '<div class="alert alert-danger errorAddSubs" role="alert">'.$error_msg.'</div>';
				}

			}else{
				$error_msg = "There was an error creating your list.";
				echo '<div class="alert alert-danger errorAddSubs" role="alert">'.$error_msg.'</div>';
			}

		}else{
			$error_msg = "Please verify that you have permission to add the subscriber(s).";
			echo '<div class="alert alert-danger errorAddSubs" role="alert">'.$error_msg.'</div>';
		}

	} // $_SERVER['REQUEST_METHOD'] === 'POST'

}else{
	pageRedirect("You must be authorized to view this page.", "error", "/promote-cc/");
}
?>