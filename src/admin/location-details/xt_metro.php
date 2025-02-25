<?php
session_start();
include_once ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include_once ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");

// Website db info
$websiteUpdates = ["dastoken" => "DAS%])p6Eu8SUuqN9U",
		           "action" => "new",
			       "table" => "option_values"];

//Contains the data that will be inserted in the option_values table
$db_data = [];

$values = $_POST; // Comes from /yextAPI/localsites2yext.php
$db_data["option"] = 'metro_area';
$db_data["display_name"] = $values["display_name"];
$db_data["value"] = $values["value"]; // slugify convert into url slug format

$db->where ("option", "metro_area");
$db->where ("display_name", $db_data["display_name"]);
$db->where ("value", $db_data["value"]);
$metro = $db->getOne ($websiteUpdates["table"]);

if(!$metro['id']){
	//Add to localsite
	if($db->insert ($websiteUpdates["table"], $db_data)){
		$data_track = array("username"=>$values["username"], "storeid"=>$values["storeid"], "updates"=>json_encode($db_data), "section"=>"location-details", "details"=>"Added a new Metro: ".$db_data["value"]);
		$db_data["username"] = $values["username"];
		track_activity($data_track);

		if(false && count($websiteUpdates) && count($db_data)){
			$urlUpdate = CLIENT_URL."xt_cupdate.php/?".http_build_query(array_merge($db_data,$websiteUpdates));
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $urlUpdate);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			$response_curl = curl_exec($ch);

			$response_curl = curl_getinfo($ch, CURLINFO_HTTP_CODE);

			curl_close($ch);
		}
	}else{
		http_response_code(400);
		exit;// Returns a response different from 200 (400), so the file that is sending the petition will be able to handle the error
	}
}else{
	http_response_code(400);
	exit;// Returns a response different from 200 (400), so the file that is sending the petition will be able to handle the error
}
?>