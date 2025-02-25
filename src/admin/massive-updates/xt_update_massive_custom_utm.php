<?php
die;
set_time_limit(300);
session_start();

//error_reporting(E_ALL & ~E_NOTICE);
include_once ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include_once ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");
include_once ($_SERVER['DOCUMENT_ROOT']."/yextAPI/Yext.php");
include_once ($_SERVER['DOCUMENT_ROOT']."/yextAPI/config.php");

if(!$_SESSION["email"] && $_SESSION["user_role_name"] != "admin_root"){
	pageRedirect("Access denied: You must be authorized to view this page.", "error", "/");
}

//$sql_locations = "SELECT storeid, google_ua FROM `locationlist` WHERE google_ua IS NOT NULL AND google_ua != ''";
$sql_locations_utm = "SELECT storeid, url, state, city FROM `locationlist` WHERE url IS NOT NULL AND url != '' AND adfundmember = 'Y' AND suspend = 0";
$locations = $db->rawQuery($sql_locations_utm);

$yext_env = $env["prod"]; // Comes from config.php. It contains the client_id and the api_key for the production Yext API
$location_data = []; // Contains the data that will be updated in Yext
$settings = $settings[$_SESSION['database']]; // $settings comes from config.php. It contains the db_name, client

$error_msg = '';
$yextOk = true;
$updateGoogleUA = false;
$updateUtmUrl = false;
$updateCustomUtm = true;

foreach($locations as $location){
	
	$values = $location;
	$store_id = $values['storeid'];

	$location_id = $locationId  = $settings["client"]."-".$store_id;
	
	$cols = Array ("state_abbr", "state_name");
	$db->where("state_abbr", $location["state"], "=");
	$result = $db->getOne("state_names", $cols);
	$state = (isset($result['state_name'])) ? strtolower($result['state_name']) : strtolower($location["city"]);
	$state = str_replace(" ", "-", $state);
	
	//Contains the data that will be updated in the locationlist table
	$update_data = [];
	
	// Only if Yext connection is neeeded
	if($yextOk){
			
		$options["filters"] = '[{"folder":'.$settings["yext_folderid"].'},{"storeId":{"equalTo":["'.$locationId.'"]}}]';
	
		//Creates an instance from the Class Yext, which has functions to connect with the Yext API
		$yext_helper = new Yext($yext_env["client_id"],$yext_env["api_key"]);
		
		$yext_location = $yext_helper->searchLocation($options);

		if(!count($yext_location) ){
			$locationId   = $settings["client"].$store_id;			
			$yext_location = $yext_helper->searchLocation($options);
		}
	}
	
	if($updateGoogleUA){
		$google_ua  = $values["google_ua"];
		$update_data["google_ua"] =  $google_ua;
	}
	
	if($updateUtmUrl){
		//$location_data["websiteUrl"] = strtolower($settings["website"]."$state/".$values["url"]."/?utm_source=gmb&utm_medium=organic");
		$location_data["websiteUrl"] = strtolower($settings["website"]."$state/".$values["url"]."/");
		//$location_data["featuredMessageUrl"] = strtolower($settings["website"]."$state/".$values["url"]."/?utm_source=organic&utm_medium=organic");
		$location_data["featuredMessageUrl"] = strtolower($settings["website"]."$state/".$values["url"]."/");
		$location_data["displayWebsiteUrl"] = strtolower($settings["website"]."$state/".$values["url"]."/");
		$location_data["featuredMessage"] = $default_yext_settings["featuredMessage"];
	}
	if($updateCustomUtm){
		$custom_urls = array();
		$location_data["googleWebsiteOverride"] = strtolower($settings["website"].$values["url"]."/?utm_source=gmb&utm_medium=organic");
			
		// https://www.yext.com/s/1791965/account/customFields/233455
		//$custom_urls["233455"] = strtolower($settings["website"]."$state/".$values["url"]."/?utm_source=facebook&utm_medium=organic"); // Facebook Website Override
		//$custom_urls["233455"] = null; // Facebook Website Override
		
		// https://www.yext.com/s/1791965/account/customFields/233456
		//$custom_urls["233456"] = strtolower($settings["website"]."$state/".$values["url"]."/?utm_source=bing&utm_medium=organic"); // Bing Website Override
		
		//$location_data["customFields"] = $custom_urls;
	}
	
	$to = 'sicwing@das-group.com';
	
	$updateResponse = [];
	$update = true;
	
	echo '<pre>'; print_r($location); echo '</pre>';
	echo '<pre>'; print_r($location_data); echo '</pre>';
	
	//The yextlocation will be updated, if the yext_location already exists and $location_data has data for updaiting 
	if(count($yext_location) && count($location_data) && $yextOk){

		try{
			$updateResponse = $yext_helper->updateLocation($location_id, $location_data);
		}catch(Exception $ex){
			$updateResponse = false;
		}
		
		if($updateResponse === false){
			$subject = "New ".CLIENT_NAME." Local Listings Location ".$location_id;
			$message = "Something went wrong with the Local Listings, try again. \n Exception: ".$ex->getMessage ."\n ArrayData : ".json_encode($location_data);
			$error_msg = "There was a problem connecting to Local Listings: ".$subject." ".$message;
		}else{
			$messageSuccess = CLIENT_NAME." Profile Updates - ".$location_id;		
		}
	}
	
	//The locationlisttable will be updated, if the yext_location was updated successfuly and $update_data has data for updaiting 
	if($updateResponse !== false && $error_msg == ''){
		echo '<pre>'; print_r($messageSuccess); echo '</pre>';
	}else{
		echo '<pre>'; print_r($error_msg); echo '</pre>';
	}
	
}//ednforeach