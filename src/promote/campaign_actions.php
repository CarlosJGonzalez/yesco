<?
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/ClassDasMC.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");

if(isset($_SESSION["user_role_name"])){
	
	$cols = Array ("storeid", "loyalty_promotions_key");
	$locationList = $db->Where("storeid", $_SESSION['storeid'])->getOne("locationlist", $cols);

	if(empty($locationList['loyalty_promotions_key'])){
		$_SESSION['error'] = "Please enter a key.";
		header('location: /settings/promote/');
		exit;
	}else{
		$mc_api_key = $locationList['loyalty_promotions_key'];
	}

	$mc = new Das_MC($mc_api_key);
	
	//Deletes a campaign
	if(isset($_POST['delete_confirmation_ok'])){
		if($_POST['delete_confirmation_ok'] != ''){
			
			$campaign_id = $_POST["campaign_id_to_delete"];

			if($campaign_id != ""){
				if($_POST["delete_confirmation_ok"] == "DELETE"){
					
					$campaign_deleted_ok = $mc->deleteCampaign($campaign_id);
					
					if($campaign_deleted_ok['is_error'] == 0){
						
						$template_id = $_POST["template_id_to_delete"];
						
						$db->where('template_id', $template_id);
						$db->get('mailchimp_campaigns');
						
						//If the template is not beeing used by another campaign, it will be deleted
						if ($db->count == 1)
							$mc->deleteTemplate($template_id);
						
						$db->where('campaign_id', $campaign_id);
						
						if($db->delete('mailchimp_campaigns')){
							
							$db->where('campaign_id', $campaign_id);
						
							if($db->delete('mailchimp_campaign_email_template_fields')){
								pageRedirect("The campaign was successfully deleted.", "success", "/promote/");
							}else{
								pageRedirect("The campaign was not deleted. Please, try again!", "error", "/promote/");
							}
						}else{
							pageRedirect("The campaign was not deleted.", "success", "/promote/");
						}
					}else{
						pageRedirect("There was an error deleting the campaign from Mailchimp.", "error", "/promote/");
					}
				}else
					pageRedirect("Please enter the word DELETE to confirm.", "error", "/promote/");
			}else{
				pageRedirect("The campaign's id is not correct.", "error", "/promote/");
			}
			
		}else{
			pageRedirect("There was an error deleting the campaign.", "error", "/promote/");
		}
		
	}
	
	//Resend a campaign
	if(isset($_POST['resend_to_confirmation_ok'])){
		if($_POST['resend_to_confirmation_ok'] != ''){
			
			$delivery_time = $_POST["delivery_hour"].":".$_POST["delivery_min"]." ".$_POST["delivery_ap"];
			
			if(strtotime($_POST["delivery_date"]." ".$delivery_time) < strtotime("now")){
				pageRedirect("You must select a time in the future.", "error", "/promote/");
			}
			
			$campaign_id = $_POST["campaign_id_to_resend"];

			if($campaign_id != ""){
				
				if($_POST["resend_to_confirmation_ok"] == "RESEND"){

					$campaignResendAction = $mc->actionsCampaign($campaign_id,"create-resend");
					
					if($campaignResendAction['is_error'] == 0){
						
						//Schedule campaign
						$delivery_time_f = date("H:i:s", strtotime($delivery_time));
						$dt = new DateTime($_POST['delivery_date']. ' ' .$delivery_time_f);
						$dt->setTimezone(new DateTimeZone('UTC'));
						$sched_date = $dt->format('Y-m-d');
						$sched_date_time = $dt->format('H:i:s+00:00');
						$sched_date_param = $sched_date.'T'.$sched_date_time;

						$parameters = json_encode([
												'schedule_time' => $sched_date_param,
												'timewarp'=>'false',//Pay function
												'batch_delay' =>'false'
											]);
											
						$campaignActionSchedule = $mc->actionsCampaign($campaignResendAction['id'],"schedule",$parameters);
						
						if($campaignActionSchedule['is_error'] == 0){
							if($campaignResendAction['recipients']['recipient_count'] == 0){
								$mc->deleteCampaign($campaignResendAction['id']);
								pageRedirect("There are not Non-Openers version of this campaign.", "error", "/promote/");
							}else{
								$data = Array ("storeid" => $_SESSION['storeid'],
									   "campaign_id" => $campaignResendAction['id'],
									   "template_id" => $campaignResendAction['settings']['template_id'],
									   "date_created_or_sent" => $sched_date_param,
										);
					
								if($db->insert ('mailchimp_campaigns', $data)){
									pageRedirect("The campaign was successfully sent.", "success", "/promote/");
								}else{
									$mc->deleteCampaign($campaignResendAction['id']);
									pageRedirect("The campaign was not resent.", "error", "/promote/");
								}
							}
						}else{
							pageRedirect("There was an error resending the campaign. The campaign was not scheduled. ".$campaignActionSchedule['msg_error'] , "error", "/promote/");
						}
						
					}else{
						pageRedirect("There was an error resending the campaign. ".$campaignResendAction['msg_error'] , "error", "/promote/");
					}
				}else
					pageRedirect("Please enter the word RESEND to confirm.", "error", "/promote/");
			}else{
				pageRedirect("The campaign's id is not correct.", "error", "/promote/");
			}
			
		}else{
			pageRedirect("Sorry! There was an error resending the campaign.", "error", "/promote/");
		}
		
	}

	//Replicates a campaign
	if(isset($_GET["campaign_id_to_replicate"]) && $_GET["campaign_id_to_replicate"] != ""){
		
		$campaign_id = $_GET["campaign_id_to_replicate"];
					
		$campaignAction = $mc->actionsCampaign($campaign_id,"replicate");
		
		if($campaignAction["is_error"] == 0){
			
				$data = Array ("storeid" => $_SESSION['storeid'],
							   "campaign_id" => $campaignAction['id'],
							   "template_id" => $campaignAction['settings']['template_id'],
							   "date_created_or_sent" => $campaignAction['create_time'],
							);
				
				if($id_db = $db->insert ('mailchimp_campaigns', $data)){
					
					$data = array();
					
					$cols = array("template_id", "display_name", "field_name", "type", "sort", "default_text", "campaign_id", "store_id");
					$db->where("campaign_id",$campaign_id);
					$campaigns = $db->get('mailchimp_campaign_email_template_fields', null, $cols);
					
					//Loop through the clean template_vars array to built the array $data that it's going to be inserted in the DB
					foreach ($campaigns as $value){
						
						if($value['field_name'] == "campaign_title")
							$default_text = $campaignAction['settings']['title'];
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

					$ids = $db->insertMulti('mailchimp_campaign_email_template_fields', $data);
					
					if(!$ids) {
						$db->where('id', $id_db);
						$db->delete('mailchimp_campaigns');
						$mc->deleteCampaign($campaignAction['id']);
						pageRedirect("Sorry! There was an error replicating the campaign.", "error", "/promote/");
					}else {
						pageRedirect("The campaign was successfully copied.", "success", "/promote/");
					}
					
				}else{
					$mc->deleteCampaign($campaignAction['id']);
					pageRedirect("There was an error replicating the campaign.", "error", "/promote/");
				}
			
		}else{
			pageRedirect("There was an error copying the campaign. ".$campaignAction["msg_error"], "error", "/promote/");
		}
		
	}
	
	//Pauses a campaign
	if(isset($_GET["campaign_id_to_pause"]) && $_GET["campaign_id_to_pause"] != ""){
		
		$campaign_id = $_GET["campaign_id_to_pause"];
		
		$campaignAction = $mc->actionsCampaign($campaign_id,"unschedule");
			
		if($campaignAction["is_error"] == 0){
			pageRedirect("The campaign was successfully paused.", "success", "/promote/");
		}else{
			pageRedirect("There was an error pausing the campaign. ".$campaignAction["msg_error"], "error", "/promote/");
		}
		
	}
	
	//Changes a campaign's name
	if(isset($_POST['edit_campaign_name'])){
		if($_POST['edit_campaign_name'] == "true"){
			
			$campaign_id = $_POST['campaign_id'];
			$campaign_title = $_POST['campaign_title'];
	
			$campaign_details = $mc->getCampaign($campaign_id);
	
			//Create campaign
			$campaignParams = Array('settings' => [
										"subject_line"=>$campaign_details["settings"]["subject_line"],
										"title"=>ucwords($campaign_title),
										"from_name"=>$campaign_details["settings"]["from_name"],
										"reply_to"=>$campaign_details["settings"]["reply_to"],
									]
									);				
					
			$campaign = $mc->editCampaign($campaign_id, json_encode($campaignParams));

			//echo '<pre>'; print_r($campaign); echo '</pre>';die;

			if($campaign["is_error"] == 0){
				$data = Array ("default_text" => $campaign_title,
							);
				
				$db->where ('campaign_id', $campaign_id);
				$db->where ('field_name', 'campaign_title');
				
				if($db->update ('mailchimp_campaign_email_template_fields', $data)){
					echo '<span class="h3 mb-1 text-capitalize span-title-editable">'.$campaign["settings"]["title"].'</span>';
				}else{
					echo '<div class="alert alert-danger answer-fail"><a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>There was an error updating the campaign '.ucwords($campaign_details["settings"]["title"]).' in the database.</div>';
				}
			}else{
				echo '<div class="alert alert-danger answer-fail"><a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>There was an error updating the campaign '.ucwords($campaign_details["settings"]["title"]).'. '.$campaign["msg_error"].'</div>';
			}
			
		}
	}
	
	
}else{
	pageRedirect("You must be authorized to view this page.", "error", "/promote/");
}