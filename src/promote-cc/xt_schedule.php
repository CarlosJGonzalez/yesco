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

	if(isset($_SESSION['post'])) unset($_SESSION['post']);

	$input_files = array();

	foreach ($_POST as $key => $value){

		$value = $db->escape($value);
		$key = str_replace("hidden-file-","",$key);
		
		if (strpos($value, 'data:image/') !== false) {
			$input_files[$key] = $value;
		}
		
		if (strpos($key, 'para_textarea_rich') !== false) {
			$vars[$key] = replace_characters($value, "");
		}elseif (strpos($key, 'contact_url') !== false || strpos($key, 'shop_url') !== false) {
			$link = replace_characters($value, "");

			$link = trim($link,' ');
			$client_link = isset($_SESSION['storeid']) ? $_SESSION['client'].'-'.$_SESSION['storeid'] : $_SESSION['client'];
			
			if (strpos($link, '?')) {				
				$link = rtrim($link,'\/')."&campid=31&utm_campaign=email&utm_source=localsite&utm_medium=email&utm_channel=email&client=".$client_link;
			}else{
				$link = rtrim($link,'\/')."?campid=31&utm_campaign=email&utm_source=localsite&utm_medium=email&utm_channel=email&client=".$client_link;
			}
			$vars[$key] = $link;
		}else{
			$vars[$key] = replace_characters($value, "<br>");
		}
		
	}

	if(count($input_files)){
		
		//Every Image file gets uploaded to D:\Website\htdocs\localfullypromoted.com\htdocs/uploads/promote/constact-contact/
		$target_dir = $_SERVER["DOCUMENT_ROOT"]."/uploads/promote/constact-contact";
		
		//If the directories don't exist, they are created
		if (!is_dir($target_dir)) {
			mkdir($target_dir, 0777, true);
		}
		
		foreach ($input_files as $key => $file){
			
			$imageFileType = explode(";", $file);
			$imageFileType = str_replace("data:image/","",$imageFileType[0]);
		
			$newfilename = $_SESSION['storeid'].'-'.$_SESSION['client'].'-template'.'.'.$imageFileType;

			// Check if file already exists
			$target_file = checkFile($target_dir,$newfilename);
			
			$file = base64ToImage($file, $target_file);

			$full_url = getFullUrl().'/uploads/promote/constact-contact';
			$full_url = str_replace($target_dir,$full_url,$file);
			
			$input_files[$key] = $full_url;
		}

		$vars = array_merge($vars,$input_files);
	}

	//$delivery_time = $vars["delivery_hour"].":".$vars["delivery_min"]." ".$vars["delivery_ap"];
	$registration_time = date("H:i:s", strtotime($vars['delivery_hour'].":".$vars['delivery_min']." ".$vars['delivery_ap']));
	$sched_date = new DateTime(filter_var($vars['delivery_date'], FILTER_SANITIZE_STRING) . ' ' . filter_var($registration_time, FILTER_SANITIZE_STRING));

	if(!file_exists("templates/".$vars["template"].".php")){
		$_SESSION['post'] = $_POST;
		pageRedirect("You must select a template.", "error", "/promote-cc/create-campaign.php");
	}
	if (new DateTime() > $sched_date) {
		$_SESSION['post'] = $_POST;
		pageRedirect("You must select a time in the future.", "error", "/promote-cc/create-campaign.php");
	}

	$cols = Array("id");
	$db->where("template_name",$vars["template"]);
	$temp = $db->getOne("email_templates",$cols);

	//If the template exists in the database, the script will continue
	if($db->count>0){
		$db->where("template_id",$temp['id']);
		$fields_db = $db->get("email_template_fields");
		
		$fields = array();
		
		foreach($fields_db as $value){
			$value['field_name'];
			$value['default_text'];
			$fields[$value['field_name']] = $value['default_text'];
		}
		
		$template_vars = array_merge($fields,$vars);

		//If the template has fields in the database, the script will continue
		if($db->count>0){
			
			//include ("templates/all-templates.php");
			
			//Retrieve a verified email address associated with the account.
			$accountEmailAddressParamsAll = Array("status"=>"CONFIRMED");
			$verifiedEmailAddresses = $cc->verifiedEmailAddresses($accountEmailAddressParamsAll);
			
			$email_content = getTemplate($template_vars, $vars["template"]); //REQUIRED. The full HTML or XHTML content of the email campaign.
			
			if($verifiedEmailAddresses[0]['email_address']){
				
				//Create campaign
				$campaignParams = Array("name"=>$vars["campaign_title"],
										"subject"=>$vars["subject"],
										"from_name"=>$locationList["displayname"],
										"from_email"=>$verifiedEmailAddresses[0]['email_address'],
										"reply_to_email"=>$verifiedEmailAddresses[0]['email_address'],
										"is_permission_reminder_enabled"=>true,
										"permission_reminder_text"=>"As a reminder, you're receiving this email because you have expressed an interest in MyCompany. Don't forget to add from_email@example.com to your address book so we'll be sure to land in your inbox! You may unsubscribe if you no longer wish to receive our emails.",
										"is_view_as_webpage_enabled"=>true,
										"view_as_web_page_text"=> "View this message as a web page",
										"view_as_web_page_link_text"=>"Click Here",
										"greeting_salutations"=>"Hello",
										"greeting_name"=>"FIRST_NAME",
										"greeting_string"=>"Dear ",
										"email_content"=>$email_content, 
										"text_content"=>strip_tags($email_content), //REQUIRED. This is the text-only content of the email message for mail clients that do not support HTML.
										"email_content_format"=>"HTML",
										"style_sheet"=>"",
										"sent_to_contact_lists"=>[
										  [
											   "id"=>$vars["list"] 
										   ]
										],
										);
				
				$campaign = $cc->addCampaign($campaignParams);
				
				if(!$campaign['is_error']){
					//Get date and time based inn the time zone
					if($locationList['zip']){
						$sql_timezone = "SELECT timezone FROM rates.zipcodeworld2 WHERE zipcode = ".$locationList['zip']." LIMIT 1"; 
						$timezone = $db->rawQueryOne($sql_timezone);
						if (isset($timezone['timezone'])){	
							$sched_date = validateTime($sched_date, $timezone['timezone']);
						}
					}
					
					$sched_date = $sched_date->format('c');

					$dataCampaign = Array ("storeid" => $_SESSION['storeid'],
										   "campaign_id" => $campaign['id'],
										   "date_created_or_sent" => $sched_date,
									);
					
					if($id_campaign_db = $db->insert ('promote_campaigns', $dataCampaign)){

						$scheduleCampaignParams = Array("scheduled_date"=>$sched_date);
						$scheduleCampaign = $cc->scheduleCampaign($campaign['id'],$scheduleCampaignParams);
						
						if(!$scheduleCampaign["is_error"]){
							$data_template_fields = array();
							
							//These fields are coming from $_POST, but it is not neccesary to insert them in the DB
							$no_fields_needed = array("sort","search","month","category_search","searchImage","sort_images","fieldNameSelected",
													  "list_name","from_name","email","fname","lname"
												);
												
							//Delete the unneccesary fields from template_vars
							foreach($template_vars as $key => $value){
								foreach($no_fields_needed as $field){
									if($key == $field){
										unset($template_vars[$key]);
									}
								}
							}
							
							//Loop through the clean template_vars array to built the array $data that it's going to be inserted in the DB
							foreach ($template_vars as $key => $value){
		
								//Data for promote_campaign_email_template_fields
								$cols = array("display_name", "type", "sort");
								$db->where("template_id",$temp['id']);
								$db->where("field_name", $key);
								$field_db = $db->getOne('email_template_fields', $cols);
								
								$db_field = Array ("template_id" => $temp['id'],
												   "display_name" => $field_db['display_name'],
												   "field_name" => $key,
												   "type" => $field_db['type'],
												   "sort" => $field_db['sort'],
												   "default_text" => $value,
												   "campaign_id" => $campaign['id'],
												   "store_id" => $_SESSION['storeid'],
								);
								
								array_push($data_template_fields, $db_field);
							}
							
							$ids = $db->insertMulti('promote_campaign_email_template_fields', $data_template_fields);
							
							if(!$ids) {
								$db->where('id', $id_campaign_db);
								$db->delete('promote_campaigns');
								$cc->deleteCampaign($campaign['id']);
								pageRedirect("Sorry! There was an error with the template.", "error", "/promote-cc/");
							}else {
								$dataAct = array("username"=>$_SESSION['email'],
												 "storeid"=>$_SESSION['storeid'],
												 "updates"=>json_encode($dataCampaign),
												 "section"=>"promote-cc",
												 "details"=>"Added an CC email campaign."
												);
												
								track_activity($dataAct);
								
								pageRedirect("The campaign was successfully created.", "success", "/promote-cc/");
							}
							
						}else{
							$db->where('id', $id_campaign_db);
							$db->delete('promote_campaigns');
							$cc->deleteCampaign($campaign['id']);
							pageRedirect("There was an error scheduling the campaign. ".$scheduleCampaign['error_info']['error_message'], "error", "/promote-cc/create-campaign.php");
						}
					
					}else{
						$cc->deleteCampaign($campaign['id']);
						pageRedirect("There was an error adding the campaign in the database.", "error", "/promote-cc/create-campaign.php");
					}
					
				}else{
					pageRedirect("There was an error creating the campaign. ".$campaign['error_info']['error_message'], "error", "/promote-cc/create-campaign.php");
				}
			
			}else{
				pageRedirect("There is not a verified email address associated with the account. ".$campaign["msg_error"], "error", "/promote-cc/create-campaign.php");
			}

		}else{
			pageRedirect("The template does not have fields.", "error", "/promote-cc/create-campaign.php");
		}

	}else{
		pageRedirect("The template does not exist.", "error", "/promote-cc/create-campaign.php");
	}

}else{
	pageRedirect("You must be authorized to view this page.", "error", "/promote-cc/");
}

function validateTime(DateTime $date, $timezone){

	switch($timezone){        
	    case "Central":
	        $isTimeToTweet = $date->add( new DateInterval("PT1H"));
	        //"Central";
	        break;
	    case "Mountain":
	        $isTimeToTweet = $date->add( new DateInterval("PT2H"));
	        //"Mountain";
	        break;
	    case "Pacific":
	        $isTimeToTweet = $date->add( new DateInterval("PT3H"));
	        //"Pacific";
	        break;
	    default :
	       $isTimeToTweet = $date;
	        
	}
	return $isTimeToTweet;
}

function getTemplate($data, $template_name){
	$template_vars = $data;
	ob_start();

	include ("templates/all-templates.php");
	$val = $template_html[$template_name];
	ob_get_clean();

	return $val;
}