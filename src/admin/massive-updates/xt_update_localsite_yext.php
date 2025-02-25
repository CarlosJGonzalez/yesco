<?php
die;
ini_set('max_execution_time', '600'); //300 seconds = 5 minutes
session_start();
include_once ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include_once ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");
include_once ($_SERVER['DOCUMENT_ROOT']."/yextAPI/Yext.php");
include_once ($_SERVER['DOCUMENT_ROOT']."/yextAPI/config.php");

if(!$_SESSION["email"] && ($_SESSION["user_role_name"] != "admin_root" || $_SESSION["user_role_name"] != "admin_rep")){
	pageRedirect("Access denied: You must be authorized to view this page.", "error", "/");
}

$settings = $settings[$_SESSION['database']]; // $settings comes from config.php. It contains the db_name, client

$sql_locs = "SELECT storeid, companyname, yext_featured_message, yext_products, yext_services, yext_specialties, brands, yearestablished, business_desc, yext_categories, tagline, phone, phone1, phone2 
			 FROM locationlist
			 WHERE tagline IS NULL 
			 LIMIT 0,10";
$sql_biz_desc = "SELECT storeid, displayname, business_desc 
				 FROM locationlist 
			     WHERE business_desc LIKE 'The world’s largest branded products franchise%'
				 AND business_desc LIKE '%helps businesses with their promotional products, branded apparel and marketing services.%'
				 AND business_desc LIKE '%is individually owned and operated by highly trained experts who know our local community and how to brand your message.  We specialize in all facets of promotional materials; including,  apparel, custom embroidery, bags, drinkware, screen-printing and more!'";
$locations = $db->rawQuery ($sql_locs);

if ($db->count > 0){		
	$yext_env = $env["prod"]; // Comes from config.php. It contains the client_id and the api_key for the production Yext API
	$yext_helper = new Yext($yext_env["client_id"],$yext_env["api_key"]);
	
    foreach ($locations as $location) {
		$error_msg = "";
		//Contain db location info
		$update_data = [];
		//Contain yext location info
		$location_data = [];
		$updateResponse = [];
		$update = true;
		
		//Get loc info
		$store_id = $location['storeid'];
		
		// Website Info
		$websiteUpdates = ["dastoken" => "DAS%])p6Eu8SUuqN9U",
						   "storeid" =>$store_id,
						   "action" => "modify",
						   "table" => "locationlist"];

		$location_id = $locationId  = $settings["client"]."-".$store_id;	
	
		$options["filters"] = '[{"folder":'.$settings["yext_folderid"].'},{"storeId":{"equalTo":["'.$locationId.'"]}}]';
		
		// yext_featured_message
		if($location["yext_featured_message"] != ""){
			if($location["yext_featured_message"] != "Get a Quote!"){
				$update_data["yext_featured_message"] =  $location["yext_featured_message"];
				$location_data["featuredMessage"] = $location["yext_featured_message"];
			}else{
				$update_data["yext_featured_message"] =  $default_yext_settings["featuredMessage"];
				$location_data["featuredMessage"] = $default_yext_settings["featuredMessage"];
			}
		}else{
			$update_data["yext_featured_message"] =  $default_yext_settings["featuredMessage"];
			$location_data["featuredMessage"] = $default_yext_settings["featuredMessage"];
		}
		
		//yext products
		if($location["yext_products"] != ""){
			$update_data["yext_products"] =  $location["yext_products"];
			$location_data["products"] = explode(";",$location["yext_products"]);
		}else{
			$update_data["yext_products"] =  $default_yext_settings["products"];
			$location_data["products"] = explode(";",$default_yext_settings["products"]);
		}
		
		// yext_services
		if($location["yext_services"] != ""){
			$update_data["yext_services"] =  $location["yext_services"];
			$location_data["services"] = explode(";",$location["yext_services"]);
		}else{
			$update_data["yext_services"] =  $default_yext_settings["services"];
			$location_data["services"] = explode(";",$default_yext_settings["services"]);
		}
		
		// yext_specialties
		if($location["yext_specialties"] != ""){
			$update_data["yext_specialties"] =  $location["yext_specialties"];
			$location_data["specialties"] = explode(";",$location["yext_specialties"]);
		}else{
			$update_data["yext_specialties"] =  $default_yext_settings["specialties"];
			$location_data["specialties"] = explode(";",$default_yext_settings["specialties"]);
		}
		
		//Brands
		//$update_data["brands"] =  '';
		//$default_yext_settings = ["brands" => ""];
		//$location_data["brands"] = explode(";",$default_yext_settings["brands"]);
		
		// year
		$location_data["yearEstablished"] = $update_data["yearestablished"] = $default_yext_settings["yearestablished"];
		
		// yext description
		if($location["business_desc"] != ""){
			$location_data["description"] = $update_data["business_desc"] = $location["business_desc"];
		}else{
			$default_yext_description = $default_yext_settings["desc"];
			$default_yext_description = str_replace("%%COMPANYNAME%%", trim($location["displayname"]), $default_yext_description);
			$location_data["description"] = $update_data["business_desc"] = $default_yext_description;
		}

		// yext categories
		$location_data["categoryIds"] = explode(",",$settings["yext_categories"]);
		$update_data["yext_categories"] =  $settings["yext_categories"];

		// tagline
		$update_data["tagline"] = "Your local branded products & marketing services team!";
		
		// Business Phone
		$location_data["phone"] = $update_data["phone"] = (sanitize_phone($location["phone"]) === FALSE) ? $location["phone"] : sanitize_phone($location["phone"]);
		
		$update_data["phone1"] = (sanitize_phone($location["phone1"]) === FALSE) ? $location["phone1"] : sanitize_phone($location["phone1"]);
		
		$update_data["phone2"] = (sanitize_phone($location["phone2"]) === FALSE) ? $location["phone2"] : sanitize_phone($location["phone2"]);

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
			
			echo '<pre>'; print_r($urlUpdate); echo '</pre>';
			
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
		
	}
}

function sanitize_phone( $phone, $international = false ) {
	$format = "/(?:(?:\+?1\s*(?:[.-]\s*)?)?(?:\(\s*([2-9]1[02-9]|[2-9][02-8]1|[2-9][02-8][02-9])\s*\)|([2-9]1[02-9]|[2-9][02-8]1|[2-9][02-8][02-9]))\s*(?:[.-]\s*)?)?([2-9]1[02-9]|[2-9][02-9]1|[2-9][02-9]{2})\s*(?:[.-]\s*)?([0-9]{4})(?:\s*(?:#|x\.?|ext\.?|extension)\s*(\d+))?$/";;
	
	$alt_format = '/^(\+\s*)?((0{0,2}1{1,3}[^\d]+)?\(?\s*([2-9][0-9]{2})\s*[^\d]?\s*([2-9][0-9]{2})\s*[^\d]?\s*([\d]{4})){1}(\s*([[:alpha:]#][^\d]*\d.*))?$/';

	// Trim & Clean extension
    $phone = trim( $phone );
    $phone = preg_replace( '/\s+(#|x|ext(ension)?)\.?:?\s*(\d+)/', ' ext \3', $phone );

    if ( preg_match( $alt_format, $phone, $matches ) ) {
        return $matches[4] . '-' . $matches[5] . '-' . $matches[6] . ( !empty( $matches[8] ) ? ' ' . $matches[8] : '' );
    } elseif( preg_match( $format, $phone, $matches ) ) {

    	// format
    	$phone = preg_replace( $format, "$2-$3-$4", $phone );

    	// Remove likely has a preceding dash
    	$phone = ltrim( $phone, '-' );

    	// Remove empty area codes
    	if ( false !== strpos( trim( $phone ), '()', 0 ) ) { 
    		$phone = ltrim( trim( $phone ), '()' );
    	}

    	// Trim and remove double spaces created
    	return preg_replace('/\\s+/', ' ', trim( $phone ));
    }

    return false;
}
?>