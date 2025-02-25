<?php
die;
set_time_limit(300);
session_start();

include_once ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include_once ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");
include_once ($_SERVER['DOCUMENT_ROOT']."/includes/ClassDasClient.php");
include_once ($_SERVER['DOCUMENT_ROOT']."/yextAPI/Yext.php");
include_once ($_SERVER['DOCUMENT_ROOT']."/yextAPI/config.php");

if(!$_SESSION["email"] && $_SESSION["user_role_name"] != "admin_root"){
	pageRedirect("Access denied: You must be authorized to view this page.", "error", "/");
}

//$sql_locations = "SELECT storeid, google_ua FROM `locationlist` WHERE google_ua IS NOT NULL AND google_ua != ''";
$sql_locations_utm = "SELECT storeid, url FROM `locationlist` WHERE url IS NOT NULL AND url != '' AND adfundmember = 'Y'";
$locations = $db->rawQuery($sql_locations_utm);

$yext_env = $env["prod"]; // Comes from config.php. It contains the client_id and the api_key for the production Yext API
$location_data = []; // Contains the data that will be updated in Yext
$settings = $settings[$_SESSION['database']]; // $settings comes from config.php. It contains the db_name, client

$error_msg = '';

//Data to update website
$websiteUpdates = ["dastoken" => $dastoken,
				   "action" => "modify",
				   "table" => "locationlist"];

$yextOk = true;

$updateUtmUrl = true;

foreach($locations as $location){
	
	$values = $location;
	$store_id = $values['storeid'];
	$websiteUpdates["storeid"] = $store_id;

	$location_id = $locationId  = $settings["client"]."-".$store_id;
	
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
	
	if($updateUtmUrl){
		//$location_data["websiteUrl"] = $settings["website"].$values["url"]."/?utm_source=gmb&utm_medium=organic";
		$location_data["websiteUrl"] = $settings["website"].$values["url"]."/";
	}
	
	$to = 'sicwing@das-group.com';
	
	$updateResponse = [];
	$update = true;
	
	//echo '<pre>'; print_r($location_data); echo '</pre>'; 
	//echo '<pre>'; print_r($yext_location); echo '</pre>'; 
	
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