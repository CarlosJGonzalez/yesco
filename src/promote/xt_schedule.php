<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");
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
		
		//Every Image file gets uploaded to D:\Website\htdocs\localfullypromoted.com\htdocs/uploads/promote/mailchimp-campaign/
		$target_dir = $_SERVER["DOCUMENT_ROOT"]."/uploads/promote/mailchimp-campaign";
		
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

			$full_url = getFullUrl().'/uploads/promote/mailchimp-campaign';
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
		pageRedirect("You must select a template.", "error", "/promote/create-campaign.php");
	}
	if (new DateTime() > $sched_date) {
		$_SESSION['post'] = $_POST;
		pageRedirect("You must select a time in the future.", "error", "/promote/create-campaign.php");
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

			include ("templates/all-templates.php");

			// Create Template
			$templateParams = Array("name"=>$vars["campaign_title"]." Template",
								    "html"=>getTemplate($template_vars, $vars["template"]));   

			$template = $mc->addTemplate(json_encode($templateParams));

			if($template["is_error"] == 0){

				//Create campaign
				$campaignParams = Array("type"=>"regular",
										'recipients' => [
											"list_id"=>$vars["list"]
										],
										'settings' => [
											"subject_line"=>$vars["subject"],
											"title"=>ucwords($vars["campaign_title"]),
											"auto_footer"=>false,
											"template_id"=>$template['id'],
											"from_name"=>$locationList["displayname"],
											"reply_to"=>"no-reply@das-group.com",
										],
										'tracking' => [
											"opens"=>true,
											"html_clicks"=>true,
											"text_clicks"=>true,
											"goal_tracking"=>true,
										]);
										
										
				$campaign = $mc->addCampaign(json_encode($campaignParams));

				//Get date and time based inn the time zone
				if($locationList['zip']){
					$sql_timezone = "SELECT timezone FROM rates.zipcodeworld2 WHERE zipcode = ".$locationList['zip']." LIMIT 1"; 
					$timezone = $db->rawQueryOne($sql_timezone);
					if (isset($timezone['timezone'])){	
						$sched_date = validateTime($sched_date, $timezone['timezone']);
					}
				}
				
				$sched_date = $sched_date->format('Y-m-d H:i:s');

				if($campaign["is_error"] == 0){
					$dataCampaign = Array ("storeid" => $_SESSION['storeid'],
										   "campaign_id" => $campaign['id'],
										   "template_id" => $campaign['settings']['template_id'],
								           "date_created_or_sent" => $sched_date,
									);
					
					if($id_campaign_db = $db->insert ('mailchimp_campaigns', $dataCampaign)){

						$parameters= json_encode([
												'schedule_time' => $sched_date,
												'timewarp'=>'false',//Pay function
												'batch_delay' =>'false'
											]);

						$campaignAction = $mc->actionsCampaign($campaign['id'],"schedule",$parameters);
						
						if($campaignAction["is_error"] == 0){
							
							$data = array();
							
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
		
								//Data for mailchimp_campaign_email_template_fields
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
								
								array_push($data, $db_field);
							}
							
							$ids = $db->insertMulti('mailchimp_campaign_email_template_fields', $data);
							
							if(!$ids) {
								$db->where('id', $id_campaign_db);
								$db->delete('mailchimp_campaigns');
								$mc->deleteTemplate($template['id']);
								$mc->deleteCampaign($campaign['id']);
								pageRedirect("Sorry! There was an error scheduling the campaign.", "error", "/promote/");
							}else {
								$dataAct = array("username"=>$_SESSION['email'],
												 "storeid"=>$_SESSION['storeid'],
												 "updates"=>json_encode($dataCampaign),
												 "section"=>"promote",
												 "details"=>"Added an email campaign."
								);
								
								track_activity($dataAct);
								
								pageRedirect("The campaign was successfully created.", "success", "/promote/");
							}
							
						}else{
							$db->where('id', $id_campaign_db);
							$db->delete('mailchimp_campaigns');
							$mc->deleteTemplate($template['id']);
							$mc->deleteCampaign($campaign['id']);

							pageRedirect("There was an error scheduling the campaign.", "error", "/promote/create-campaign.php");
						}
					
					}else{
						$mc->deleteTemplate($template['id']);
						$mc->deleteCampaign($campaign['id']);
						pageRedirect("There was an error in creating the campaign in the database.", "error", "/promote/create-campaign.php");
					}
					
				}else{
					$mc->deleteTemplate($template['id']);
					
					pageRedirect("There was an error creating the campaign. ".$campaign["msg_error"], "error", "/promote/create-campaign.php");
				}

			}else{
				pageRedirect("There was an error creating this campaing's template.", "error", "/promote/create-campaign.php");
			}

		}else{
			pageRedirect("The template does not have fields.", "error", "/promote/create-campaign.php");
		}

	}else{
		pageRedirect("The template does not exist.", "error", "/promote/create-campaign.php");
	}

}else{
	pageRedirect("You must be authorized to view this page.", "error", "/promote/");
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