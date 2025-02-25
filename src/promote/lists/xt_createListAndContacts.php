<?php 
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/ClassDasMC.php");

if(isset($_SESSION["user_role_name"])){
	
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {

		$locationList = $db->Where("storeid", $_SESSION['storeid'])->getOne("locationlist");

		if(empty($locationList['loyalty_promotions_key'])){
			$_SESSION['error'] = "Please enter a key.";
			header('location: /settings/promote/');
			exit;
		}else{
			$mc_api_key = $locationList['loyalty_promotions_key'];
		}

		$mc = new Das_MC($mc_api_key);

		$error_msg = '';

		$list_name = $db->escape($_POST['list_name']);
		$from_name = $db->escape($_POST['from_name']);
		$optRadioSubscriber = $db->escape($_POST['optRadioSubscriber']);
		$authorizedCheck = $db->escape($_POST['authorizedCheck']);
		
		if(isset($authorizedCheck) && $authorizedCheck == '1'){
		
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
				
				if($id_list_db = $db->insert ('mailchimp_lists', $data)){
					
					//Get inside if the user clicked on the radio Add a Subscriber
					if($optRadioSubscriber == "addSubs"){
						$email = $db->escape($_POST['email']);
						$fname = $db->escape($_POST['fname']);
						$lname = $db->escape($_POST['lname']);
						$listid = $list['id'];

						$params = Array("email_address"=>$email,
									   "status"=>"subscribed",
									   "merge_fields"=>[
										   "FNAME"=>$fname,
										   "LNAME"=>$lname
									   ]);
						$member = $mc->addMember($listid,json_encode($params));
						
						/* Gets the updated list of members after adding the subscribers because before this point the list didn't have members yet. 
						 * Therefore, now the $mc_refreshed_list['total_items'] can be printed
						*/
						$mc_refreshed_list = $mc->getMembers($listid);
						
						if($member ["is_error"] == 0){
							?>
							<label class="label cusor-pointer text-center d-flex successAddSubs" for="list-<?php echo $listid; ?>">
								<input  class="label__checkbox" type="radio" name="list" value="<?php echo $listid; ?>" type="checkbox" id="list-<?php echo $listid; ?>" <?php if(isset($_SESSION['post']) && $_SESSION['post']['list']== $listid) echo "checked"; ?> />
								<span class="label__text d-flex align-items-center">
								  <span class="label__check d-flex rounded-circle mr-2">
									<i class="fa fa-check icon small"></i>
								  </span>
									<div class="d-inline-block cursor-pointer text-left"><span class="font-weight-light font-lg letter-spacing-1 "><?php echo $list['name']; ?></span><span class="small d-block"><?php echo $mc_refreshed_list['total_items']; ?> subscribers</span></div>
								</span>
							 </label>
							<?php  
						}else{
							$db->where('id', $id_list_db);
							$db->delete('mailchimp_lists');
							
							$mc->deleteList($listid);
							
							$error_msg = "There was an error adding the subscriber. ";
							echo '<div class="alert alert-danger errorAddSubs" role="alert">'.$error_msg. $member ["detail"] .'</div>';
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
										
										$params = Array("email_address"=>$line[0],
													   "status"=>"subscribed",
													   "merge_fields"=>[
														   "FNAME"=>$line[1],
														   "LNAME"=>$line[2]
													   ]);
										$member = $mc->addMember($listid,json_encode($params));
										
										//If there is an error trying to add an specific member, "fail" will be added to the array membersInsertedOk
										if($member ["is_error"] == 1){
											$error_msg .= "<p>There was an error adding the member: ". $line[0]. '. '. $member ["detail"] .'</p>';
											array_push($membersInsertedOk, "fail");
										}
										
										////If there is not any error trying to add an specific member, "success" will be added to the array membersInsertedOk
										if($member ["is_error"] == 0){
											array_push($membersInsertedOk, "success");
										}
										
									}
									
									// Close opened CSV file
									fclose($csvFile);
												
									/* Gets the updated list of members after adding the subscribers because before this point the list didn't have members yet. 
									 * Therefore, now the $mc_refreshed_list['total_items'] can be printed
									*/
									$mc_refreshed_list = $mc->getMembers($listid);
												
									//If there were not any error trying to add any of the imported members, the expected html will be print 
									if(!in_array("fail", $membersInsertedOk)){

										?>
										<label class="label cusor-pointer text-center d-flex successAddSubs" for="list-<?php echo $listid; ?>">
											<input  class="label__checkbox" type="radio" name="list" value="<?php echo $listid; ?>" type="checkbox" id="list-<?php echo $listid; ?>" <?php if(isset($_SESSION['post']) && $_SESSION['post']['list']== $listid) echo "checked"; ?> />
											<span class="label__text d-flex align-items-center">
											  <span class="label__check d-flex rounded-circle mr-2">
												<i class="fa fa-check icon small"></i>
											  </span>
												<div class="d-inline-block cursor-pointer text-left"><span class="font-weight-light font-lg letter-spacing-1 "><?php echo $list['name']; ?></span><span class="small d-block"><?php echo $mc_refreshed_list['total_items']; ?> subscribers</span></div>
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
											$db->delete('mailchimp_lists');
											
											$mc->deleteList($listid);
											
											echo '<div class="alert alert-danger errorAddSubs" role="alert">'.$error_msg .'</div>';
										}else{
											?>
											<label class="label cusor-pointer text-center d-flex successAddSubs" for="list-<?php echo $listid; ?>">
												<input  class="label__checkbox" type="radio" name="list" value="<?php echo $listid; ?>" type="checkbox" id="list-<?php echo $listid; ?>" <?php if(isset($_SESSION['post']) && $_SESSION['post']['list']== $listid) echo "checked"; ?> />
												<span class="label__text d-flex align-items-center">
												  <span class="label__check d-flex rounded-circle mr-2">
													<i class="fa fa-check icon small"></i>
												  </span>
													<div class="d-inline-block cursor-pointer text-left"><span class="font-weight-light font-lg letter-spacing-1 "><?php echo $list['name']; ?></span><span class="small d-block"><?php echo $mc_refreshed_list['total_items']; ?> subscribers</span></div>
												</span>
											 </label>
											 <div class="alert alert-danger" role="alert"><?php echo $error_msg; ?></div>
											<?php
										}
										
									}
								
								}else{
									$db->where('id', $id_list_db);
									$db->delete('mailchimp_lists');
									
									$mc->deleteList($listid);
									
									$error_msg = "There was an error uploading the file.";
									echo '<div class="alert alert-danger errorAddSubs" role="alert">'.$error_msg.'</div>';
								}
							}else{
								$db->where('id', $id_list_db);
								$db->delete('mailchimp_lists');
								
								$mc->deleteList($listid);
								
								$error_msg = "Only .csv files can be imported.";
								echo '<div class="alert alert-danger errorAddSubs" role="alert">'.$error_msg.'</div>';
							}
						}else{
							$db->where('id', $id_list_db);
							$db->delete('mailchimp_lists');
							
							$mc->deleteList($listid);
							
							$error_msg = "You must select a file.";
							echo '<div class="alert alert-danger errorAddSubs" role="alert">'.$error_msg.'</div>';
						}
					}

				}else{
					$mc->deleteList($listid);
					
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

	}

}else{
	pageRedirect("You must be authorized to view this page.", "error", "/promote-cc/");
}
?>