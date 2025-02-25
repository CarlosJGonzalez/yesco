<?php
die;
set_time_limit(300);
session_start();

//error_reporting(E_ALL & ~E_NOTICE);
include_once ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include_once ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");
include_once ($_SERVER['DOCUMENT_ROOT']."/includes/ClassDasClient.php");
include_once ($_SERVER['DOCUMENT_ROOT']."/yextAPI/Yext.php");
include_once ($_SERVER['DOCUMENT_ROOT']."/yextAPI/config.php");

if(!$_SESSION["email"] && $_SESSION["user_role_name"] != "admin_root"){
	pageRedirect("Access denied: You must be authorized to view this page.", "error", "/");
}

$yext_env = $env["prod"]; // Comes from config.php. It contains the client_id and the api_key for the production Yext API
$location_data = []; // Contains the data that will be updated in Yext
$settings = $settings[$_SESSION['database']]; // $settings comes from config.php. It contains the db_name, client

$error_msg = '';

//Data to update website
$websiteUpdates = ["dastoken" => "DAS%])p6Eu8SUuqN9U",
				   "action" => "modify",
				   "table" => "locationlist"];

$updateInstagram = false;
$updateTwitter = false;
$updateFacebook = false;
$updatePinterest = false;

$sql_instagram = "SELECT storeid, instagram FROM `locationlist` WHERE instagram IS NOT NULL AND instagram != ''";
$sql_twitter = "SELECT storeid, twitter FROM `locationlist` WHERE twitter IS NOT NULL AND twitter != ''";
$sql_facebook = "SELECT storeid, facebook FROM `locationlist` WHERE facebook IS NOT NULL AND facebook != ''";
$sql_pinterest = "SELECT storeid, pinterest FROM `locationlist` WHERE pinterest IS NULL OR pinterest = '' OR `pinterest` LIKE '%https://www.pinterest.com/embroidme/%' OR `pinterest` LIKE '%https://www.pinterest.ca/embroidme%'";

if($updateInstagram)
	$sql_locs = $sql_instagram;
elseif($updateTwitter)
	$sql_locs = $sql_twitter;
elseif($updateFacebook)
	$sql_locs = $sql_facebook;
elseif($updatePinterest)
	$sql_locs = $sql_pinterest;

$locations = $db->rawQuery($sql_locs);

foreach($locations as $location){
	$values = $location;
	$store_id = $values['storeid'];
	$websiteUpdates["storeid"] = $store_id;

	//Retrieves all information from the locationlist table where store id = $_SESSION["storeid"] 
	$current_data = $db->where("storeid", $store_id , "=")
				       ->get($settings["db_name"].".locationlist", null, "*");
	
	//$location_id = "9018-1";//Test Location
	$location_id = $locationId  = $settings["client"]."-".$store_id;
	
	//Contains the data that will be updated in the locationlist table
	$update_data = [];
							
	$options["filters"] = '[{"folder":'.$settings["yext_folderid"].'},{"storeId":{"equalTo":["'.$locationId.'"]}}]';
	
	//Creates an instance from the Class Yext, which has functions to connect with the Yext API
	$yext_helper = new Yext($yext_env["client_id"],$yext_env["api_key"]);
	
	$yext_location = $yext_helper->searchLocation($options);

	if(!count($yext_location) ){
		$locationId   = $settings["client"].$store_id;			
		$yext_location = $yext_helper->searchLocation($options);
	}
	
	###### SOCIAL MEDIA FORM ######
	if($updateTwitter){
		$twitter  = $values["twitter"];
		$twitter  = str_replace("https://www.twitter.com/","",$twitter);
		$twitter  = str_replace("https://twitter.com/","",$twitter);
		$twitter  = str_replace("http://twitter.com/","",$twitter);
		$twitter  = str_replace("http://www.twitter.com/","",$twitter);
		$twitter  = str_replace("https://mobile.","",$twitter);
		$twitter  = str_replace("www.twitter.com/","",$twitter);
		$twitter  = str_replace("twitter.com/","",$twitter);
		$twitter  = str_replace("https://www.facebook.com/","",$twitter);
		$twitter  = str_replace("/?hl=en","",$twitter);
		$twitter  = str_replace("?lang=en","",$twitter);
		$twitter  = str_replace("@","",$twitter);
		$twitter  = trim($twitter,'/');
		
		$update_data["twitter"] =  $twitter;
		$hasupdates["TwitterHandle"] = $location_data["twitterHandle"] = $twitter;
	}

	//INSTAGRAM
	if($updateInstagram){
		$instagram  = $values["instagram"];
		$instagram  = str_replace("https://www.instagram.com/","",$instagram);
		$instagram  = str_replace("https://instagram.com/","",$instagram);
		$instagram  = str_replace("http://instagram.com/","",$instagram);
		$instagram  = str_replace("http://www.instagram.com/","",$instagram);
		$instagram  = str_replace("www.instagram.com/","",$instagram);
		$instagram  = str_replace("instagram.com/","",$instagram);
		$instagram  = str_replace("https://www.facebook.com/","",$instagram);
		$instagram  = str_replace("/?hl=en","",$instagram);
		$instagram  = str_replace("@","",$instagram);
		$instagram  = trim($instagram,'/');
		
		$update_data["instagram"] =  $instagram;
		$hasupdates["InstagramHandle"] = $location_data["instagramHandle"] = $instagram;
	}
	
	if($updateFacebook){
		$facebook  = $values["facebook"];
		$facebook  = str_replace("/?hl=en","",$facebook);
		$facebook  = str_replace("?lang=en","",$facebook);
		$facebook  = str_replace("?ref=bookmarks","",$facebook);
		$facebook  = str_replace("?ref=page_internal","",$facebook);
		$facebook  = str_replace("?fref=photo","",$facebook);
		$facebook  = str_replace("?ref=your_pages","",$facebook);
		$facebook  = str_replace("@","",$facebook);
		$facebook  = trim($facebook,'/');
		$facebook_for_yext = str_replace("https://www.", "", $facebook);
		$facebook_for_yext = str_replace("http://www.", "", $facebook_for_yext);
		$facebook_for_yext = str_replace("www", "", $facebook_for_yext);

		$update_data["facebook"] =  $facebook;
		$hasupdates["FacebookPageUrl"] = $location_data["facebookPageUrl"] = $facebook_for_yext;
	}
	
	if($updatePinterest){
		$pinterest = ($values["pinterest"] == '') ? "https://www.pinterest.com/FullyPromoted/" : $values["pinterest"];
		$pinterest = str_replace("https://www.pinterest.com/embroidme/","https://www.pinterest.com/FullyPromoted/",$pinterest);
		$pinterest = str_replace("https://www.pinterest.ca/embroidme/","https://www.pinterest.com/FullyPromoted/",$pinterest);
		$pinterest = str_replace("https://www.pinterest.ca/embroidme","https://www.pinterest.com/FullyPromoted/",$pinterest);

		$pinterest = trim($pinterest , "/");
		$update_data["pinterest"] = $hasupdates["Pinterest"] = $pinterest;	
	}
	
	if(array_key_exists("linkedin", $values) && ($values["linkedin"] != $current_data[0]["linkedin"])){
		$hasupdates["LinkedIn"] = $update_data["linkedin"] = trim($values["linkedin"], "/");			
	}

	if(array_key_exists("youtube", $values) && ($values["youtube"] != $current_data[0]["youtube"])){
		//$location_data["videoUrls"] = array(trim($values["youtube"], "/"));
		$update_data["youtube"] = trim($values["youtube"], "/");
		$hasupdates["Youtube"] = str_replace("https://www.", "", $update_data["youtube"]);
		//$location_data["videoUrls"] = str_replace("https://www.", "", $location_data["videoUrls"]);
	}
	
	$to = 'sicwing@das-group.com';
	
	$updateResponse = [];
	$update = true;
	
	
	//The yextlocation will be updated, if the yext_location already exists and $location_data has data for updaiting 
	if(count($yext_location) && count($location_data)){

		try{
			$updateResponse = $yext_helper->updateLocation($location_id, $location_data);
		}catch(Exception $ex){
			$updateResponse = false;
		}
		
		if($updateResponse === false){
			$subject = "New ".CLIENT_NAME." Local Listings Location ".$location_id;
			$message = "Something went wrong with the Local Listings, try again. \n Exception: ".$ex->getMessage ."\n ArrayData : ".json_encode($location_data);
			$error_msg = "There was a problem connecting to Local Listings: ".$subject." ".$message;
		}
	}
	
	//The locationlisttable will be updated, if the yext_location was updated successfuly and $update_data has data for updaiting 
	//if((($updateResponse !== false) && count($update_data)) || (count($update_data))){
	if((($updateResponse !== false) && count($update_data))){

		$update = $db->where("storeid", $store_id, "=" )
				     ->update($settings["db_name"].".locationlist", $update_data);

		if(!$update){
			$subject = CLIENT_NAME." Profile Updates - ".$location_id;
			$message = "Something went wrong with the Data Base, try again. \n Exception: ".$db_link->getLastError()."\n ArrayData : ".json_encode($update_data);
			$error_msg = "There was a problem connecting to the database. ".$subject." ".$message;			
		}else{
			//Send Notification After Update
			$messageSuccess = CLIENT_NAME." Profile Updates - ".$location_id;		
		}	
		
	}
	
	if(count($websiteUpdates)  && count($update_data) && $update){
		$urlUpdate = CLIENT_URL."xt_cupdate.php/?".http_build_query(array_merge($update_data,$websiteUpdates));
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $urlUpdate);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$response = curl_exec($ch);

		$response = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		curl_close($ch);
	}
	
	if($updateResponse !== false && $update && $error_msg == ''){
		echo '<pre>'; print_r($messageSuccess); echo '</pre>';
	}else{
		echo '<pre>'; print_r($error_msg); echo '</pre>';
	}
	
}//ednforeach