<?
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
	
	//Deletes a campaign
	if(isset($_POST['delete_confirmation_ok'])){
		if($_POST['delete_confirmation_ok'] != ''){
			
			$campaign_id = $_POST["campaign_id_to_delete"];

			if($campaign_id != ""){
				if($_POST["delete_confirmation_ok"] == "DELETE"){
					
					$campaign_deleted_ok = $cc->deleteCampaign($campaign_id);

					if(!$campaign_deleted_ok['is_error']){
						
						/*$template_id = $_POST["template_id_to_delete"];
						
						$db->where('template_id', $template_id);
						$db->get('mailchimp_campaigns');
						
						//If the template is not beeing used by another campaign, it will be deleted
						if ($db->count == 1)
							$cc->deleteTemplate($template_id);
						*/
						$db->where('campaign_id', $campaign_id);
						if($db->delete('promote_campaigns')){
							
							$db->where('campaign_id', $campaign_id);
							
							if($db->delete('promote_campaign_email_template_fields')){
								pageRedirect("The campaign was successfully deleted.", "success", "/promote-cc/");
							}else{
								pageRedirect("The campaign was not deleted. Please, try again!", "error", "/promote-cc/");
							}
						}else{
							pageRedirect("The campaign was not deleted.", "success", "/promote-cc/");
						}
					}else{
						pageRedirect("There was an error deleting the campaign..", "error", "/promote-cc/");
					}
				}else
					pageRedirect("Please enter the word DELETE to confirm.", "error", "/promote-cc/");
			}else{
				pageRedirect("The campaign's id is not correct.", "error", "/promote-cc/");
			}
			
		}else{
			pageRedirect("There was an error deleting the campaign.", "error", "/promote-cc/");
		}
		
	}
	
	//Resend a campaign
	if(isset($_POST['resend_to_confirmation_ok'])){
		if($_POST['resend_to_confirmation_ok'] != ''){
			
			$delivery_time = $_POST["delivery_hour"].":".$_POST["delivery_min"]." ".$_POST["delivery_ap"];
			
			if(strtotime($_POST["delivery_date"]." ".$delivery_time) < strtotime("now")){
				pageRedirect("You must select a time in the future.", "error", "/promote-cc/");
			}
			
			$campaign_id = $_POST["campaign_id_to_resend"];

			if($campaign_id != ""){
				
				if($_POST["resend_to_confirmation_ok"] == "RESEND"){

					$campaignResendAction = $cc->actionsCampaign($campaign_id,"create-resend");
					
					if(!$campaignResendAction['is_error']){
						
						//Schedule campaign
						$delivery_time_f = date("H:i:s", strtotime($delivery_time));
						$dt = new DateTime($_POST['delivery_date']. ' ' .$delivery_time_f);
						$dt->setTimezone(new DateTimeZone('UTC'));
						$sched_date = $dt->format('Y-m-d');
						$sched_date_time = $dt->format('H:i:s+00:00');
						$sched_date_param = $sched_date.'T'.$sched_date_time;

						$parameters = [
										'schedule_time' => $sched_date_param,
										'timewarp'=>'false',//Pay function
										'batch_delay' =>'false'
									  ];
											
						$campaignActionSchedule = $cc->actionsCampaign($campaignResendAction['id'],"schedule",$parameters);
						
						if(!$campaignActionSchedule['is_error']){
							if($campaignResendAction['recipients']['recipient_count'] == 0){
								$cc->deleteCampaign($campaignResendAction['id']);
								pageRedirect("There are not Non-Openers version of this campaign.", "error", "/promote-cc/");
							}else{
								$data = Array ("storeid" => $_SESSION['storeid'],
									   "campaign_id" => $campaignResendAction['id'],
									   "template_id" => $campaignResendAction['settings']['template_id'],
									   "date_created_or_sent" => $sched_date_param,
										);
					
								if($db->insert ('mailchimp_campaigns', $data)){
									pageRedirect("The campaign was successfully sent.", "success", "/promote-cc/");
								}else{
									$cc->deleteCampaign($campaignResendAction['id']);
									pageRedirect("The campaign was not resent.", "error", "/promote-cc/");
								}
							}
						}else{
							pageRedirect("There was an error resending the campaign. The campaign was not scheduled. ".$campaignActionSchedule['msg_error'] , "error", "/promote-cc/");
						}
						
					}else{
						pageRedirect("There was an error resending the campaign. ".$campaignResendAction['msg_error'] , "error", "/promote-cc/");
					}
				}else
					pageRedirect("Please enter the word RESEND to confirm.", "error", "/promote-cc/");
			}else{
				pageRedirect("The campaign's id is not correct.", "error", "/promote-cc/");
			}
			
		}else{
			pageRedirect("Sorry! There was an error resending the campaign.", "error", "/promote-cc/");
		}
		
	}

	//Replicates a campaign
	if(isset($_GET["campaign_id_to_replicate"]) && $_GET["campaign_id_to_replicate"] != ""){
		
		$campaign_id = $_GET["campaign_id_to_replicate"];
		$campaign_details = $cc->getCampaign($campaign_id);
		
		//Generating random numbers without repeats
		$numbers = range(1, 50);
		shuffle($numbers);
		
		$campaign_name_copied = $campaign_details["name"].' Copy '.$numbers[0];
	
		$campaignParams = Array("name"=>$campaign_name_copied,
								"subject"=>$campaign_details["subject"],
								"from_name"=>$campaign_details["from_name"],
								"from_email"=>$campaign_details["from_email"],
								"reply_to_email"=>$campaign_details["reply_to_email"],
								"is_permission_reminder_enabled"=>true,
								"permission_reminder_text"=>"As a reminder, you're receiving this email because you have expressed an interest in MyCompany. Don't forget to add from_email@example.com to your address book so we'll be sure to land in your inbox! You may unsubscribe if you no longer wish to receive our emails.",
								"is_view_as_webpage_enabled"=>true,
								"view_as_web_page_text"=> "View this message as a web page",
								"view_as_web_page_link_text"=>"Click Here",
								"greeting_salutations"=>"Hello",
								"greeting_name"=>"FIRST_NAME",
								"greeting_string"=>"Dear ",
								"email_content"=>$campaign_details["email_content"],
								"text_content"=>$campaign_details["text_content"],
								"email_content_format"=>"HTML",
								"style_sheet"=>"",
								"sent_to_contact_lists"=>$campaign_details["sent_to_contact_lists"],
								);					
		
		$campaignAction = $cc->addCampaign($campaignParams);
		
		if(!$campaignAction["is_error"]){
			
				$data = Array ("storeid" => $_SESSION['storeid'],
							   "campaign_id" => $campaignAction['id'],
							   "date_created_or_sent" => $db->now(),
							);
				
				if($id_db = $db->insert ('promote_campaigns', $data)){
					
					$data = array();
					
					$cols = array("template_id", "display_name", "field_name", "type", "sort", "default_text", "campaign_id", "store_id");
					$db->where("campaign_id",$campaign_id);
					$campaigns = $db->get('promote_campaign_email_template_fields', null, $cols);
					
					//Loop through the clean template_vars array to built the array $data that it's going to be inserted in the DB
					foreach ($campaigns as $value){
						
						if($value['field_name'] == "campaign_title")
							$default_text = $campaignAction['name'];
						else
							$default_text = $value['default_text'];
						
						$db_field = Array ("template_id" => $value['template_id'],
										   "display_name" => $value['display_name'],
										   "field_name" => $value['field_name'],
										   "type" => $value['type'],
										   "sort" => $value['sort'],
										   "default_text" => $default_text,
										   "campaign_id" => $campaignAction['id'],
										   "store_id" => $value['store_id'],
						);
						
						array_push($data, $db_field);
					}

					$ids = $db->insertMulti('promote_campaign_email_template_fields', $data);
					
					if(!$ids) {
						$db->where('id', $id_db);
						$db->delete('promote_campaigns');
						$cc->deleteCampaign($campaignAction['id']);
						pageRedirect("Sorry! There was an error replicating the campaign.", "error", "/promote-cc/");
					}else {
						pageRedirect("The campaign was successfully copied.", "success", "/promote-cc/");
					}
					
				}else{
					$cc->deleteCampaign($campaignAction['id']);
					pageRedirect("There was an error replicating the campaign.", "error", "/promote-cc/");
				}
			
		}else{
			pageRedirect("There was an error copying the campaign. ".$campaignAction["error_info"]['error_message'], "error", "/promote-cc/");
		}
		
	}
	
	//Pauses a campaign
	if(isset($_GET["campaign_id_to_pause"]) && $_GET["campaign_id_to_pause"] != ""){
		
		$campaign_id = $_GET["campaign_id_to_pause"];
		
		$allCampaignSchedules = $cc->getAllCampaignSchedules($campaign_id);
		$scheduleId = $allCampaignSchedules[0]['id'];
		
		$deletedCampaignSchedule = $cc->deleteCampaignSchedule($campaign_id, $scheduleId );
		
		if(!$deletedCampaignSchedule["is_error"]){
			pageRedirect("The campaign was successfully paused.", "success", "/promote-cc/");
		}else{
			pageRedirect("There was an error pausing the campaign. ".$editedCampaign["error_info"]['error_message'], "error", "/promote-cc/");
		}

	}
	
	//Changes a campaign's name
	if(isset($_POST['edit_campaign_name'])){
		if($_POST['edit_campaign_name'] == "true"){
			
			$campaign_id = $_POST['campaign_id'];
			$campaign_title = $_POST['campaign_title'];
	
			$campaign_details = $cc->getCampaign($campaign_id);
				
			$email_content = $campaign_details["email_content"];
			
			$campaignParams = Array("name"=>ucwords($campaign_title),
									"subject"=>$campaign_details["subject"],
									"from_name"=>$campaign_details["from_name"],
									"from_email"=>$campaign_details["from_email"],
									"reply_to_email"=>$campaign_details["reply_to_email"],
									"email_content"=>$email_content,
									"text_content"=>$campaign_details["text_content"],
									);
					
			$editedCampaign = $cc->editCampaign($campaign_id, $campaignParams);

			if(!$editedCampaign["is_error"]){
				$data = Array ("default_text" => $campaign_title);
				
				$db->where ('campaign_id', $campaign_id);
				$db->where ('field_name', 'campaign_title');
				
				if($db->update ('promote_campaign_email_template_fields', $data)){
					echo '<span class="h3 mb-1 text-capitalize span-title-editable">'.$editedCampaign["name"].'</span>';
				}else{
					echo '<div class="alert alert-danger answer-fail"><a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>There was an error updating the campaign '.ucwords($campaign_details["name"]).' in the database.</div>';
				}
			}else{
				echo '<div class="alert alert-danger answer-fail"><a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>There was an error updating the campaign '.ucwords($campaign_details["name"]).'. '.$editedCampaign["error_info"]['error_message'].'</div>';
			}
			
		}
	}
	
}else{
	pageRedirect("You must be authorized to view this page.", "error", "/promote-cc/");
}