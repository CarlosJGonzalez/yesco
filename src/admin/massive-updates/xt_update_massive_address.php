<?php
die;
ini_set('max_execution_time', '600'); //300 seconds = 5 minutes
session_start();
error_reporting(E_ALL & ~E_NOTICE);
include_once ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include_once ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");
include_once ($_SERVER['DOCUMENT_ROOT']."/yextAPI/Yext.php");
include_once ($_SERVER['DOCUMENT_ROOT']."/yextAPI/config.php");

if(!$_SESSION["email"] && ($_SESSION["user_role_name"] != "admin_root" || $_SESSION["user_role_name"] != "admin_rep")){
	pageRedirect("Access denied: You must be authorized to view this page.", "error", "/");
}

$settings = $settings[$_SESSION['database']]; // $settings comes from config.php. It contains the db_name, client
$sql = "SELECT loca.storeid, locb.storeid AS new_storeid, loca.address, locb.address AS new_address, loca.address2, locb.address2 AS new_address2, loca.city, locb.city AS new_city, loca.state, locb.state AS new_state, loca.zip, locb.zip AS new_zip
		FROM locationlist loca, locationlist_new_address locb
		WHERE loca.storeid = locb.storeid
		AND loca.adfundmember = 'Y'
		AND (loca.address != locb.address 
		OR loca.address2 != locb.address2
		OR loca.city != locb.city
		OR loca.state != locb.state
		OR loca.zip != locb.zip)";
$locations = $db->rawQuery ($sql);

if (count($locations) > 0){
	$error_msg = "";
	//Contain db location info
	$update_data = [];
	//Contain yext location info
	$location_data = [];
	$yext_env = $env["prod"]; // Comes from config.php. It contains the client_id and the api_key for the production Yext API
	$yext_helper = new Yext($yext_env["client_id"],$yext_env["api_key"]);
	$updateResponse = [];
	$update = true;
	
    foreach ($locations as $location) {
		//Get loc info
		$store_id = $location['new_storeid'];
		$address = $location_data["address"] = $update_data["address"] = trim($location["new_address"]);
		$address2 = $location_data["address2"] = $update_data["address2"] = trim($location["new_address2"]); 
		$city = $location_data["city"] = $update_data["city"] = trim($location["new_city"]); 
		$state = $location_data["state"] = $update_data["state"] = trim($location["new_state"]); 
		$zip = $location_data["zip"] = $update_data["zip"] = trim($location["new_zip"]); 
		
		// Website Info
		$websiteUpdates = ["dastoken" => "DAS%])p6Eu8SUuqN9U",
						   "storeid" =>$store_id,
						   "action" => "modify",
						   "table" => "locationlist"];

		//$location_id = "8014-1";//Test Location
		$location_id = $locationId  = $settings["client"]."-".$store_id;	
	
		$options["filters"] = '[{"folder":'.$settings["yext_folderid"].'},{"storeId":{"equalTo":["'.$locationId.'"]}}]';
		
		//Get latitude and longitude base on the address provided on the location form
		$fullAddress = $address.' '.$address2.' '.$city.' '.$state.' '.$zip; // Google HQ
		$prepAddr = str_replace(' ','+',$fullAddress);
		$maps_api = "AIzaSyA6OmvG-XyCVw7MyCUOW6qNABkc21kslmA";
		$urlmaps='https://maps.google.com/maps/api/geocode/json?address='.$prepAddr."&sensor=false&key=$maps_api";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $urlmaps);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$response = curl_exec($ch);
		curl_close($ch);
		$response_a = json_decode($response);
		if($response_a->results[0]->geometry->location->lat)
			$location_data["displayLat"] = $update_data["latitude"] = $latitude = $response_a->results[0]->geometry->location->lat;
		if($response_a->results[0]->geometry->location->lng)
			$location_data["displayLng"] = $update_data["longitude"] = $longitude = $response_a->results[0]->geometry->location->lng;

		$yext_location = $yext_helper->searchLocation($options);
		
		if(!count($yext_location) ){
			$locationId   = $settings["client"].$store_id;			
			$yext_location = $yext_helper->searchLocation($options);
		}
		
		//The yextlocation will be updated, if the yext_location already exists and $location_data has data for updaiting 
		if(count($yext_location) && count($location_data)){
			try{
				$updateResponse = $yext_helper->updateLocation($location_id, $location_data);
			}catch(Exception $ex){
				$updateResponse = false;
			}
			
			if($updateResponse === false){
				$subject = "New Local Listings Location ".$location_id;
				$headers = "MIME-Version: 1.0" . "\r\n";
				$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
				$headers .= 'From: <noreply@das-group.com>' . "\r\n";	
				$message = "Something went wrong with Local Listings, try again. \n Exception: ".$ex->getMessage ."\n ArrayData : ".json_encode($location_data);
				//mail("sicwing@das-group.com",$subject,$message,$headers);
				
				echo "There was a problem connecting to Local Listings.";
			}
			
		}

		//The locationlisttable will be updated, if the yext_location was updated successfuly and $update_data has data for updaiting 
		if(($updateResponse !== false) && count($update_data)){
			
			$update = $db->where("storeid", $store_id, "=" )
						 ->update($settings["db_name"].".locationlist", $update_data);

			if(!$update){
				$subject = "New Local Listings Location ".$location_id;
				$headers = "MIME-Version: 1.0" . "\r\n";
				$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
				$headers .= 'From: <noreply@das-group.com>' . "\r\n";	
				$message = "Something went wrong with the Data Base, try again. \n Exception: ".$db_link->getLastError()."\n ArrayData : ".json_encode($update_data);
				//mail("sicwing@das-group.com",$subject,$message,$headers);

				echo "There was a problem connecting to the database.";			
			}else{
				echo '<pre>'; print_r($update_data); echo '</pre>';
				echo '<pre>'; print_r($location_data); echo '</pre>';
				echo '<pre>'; print_r($location_id); echo '</pre>';
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
		
	} // end foreach
}
?>