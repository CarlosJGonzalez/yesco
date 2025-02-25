<?php
	session_start();
    error_reporting(E_ALL & ~E_NOTICE);
	include_once ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
    //include_once  __DIR__ ."/../includes/MysqliDb.php";
	require  __DIR__ ."/../yextAPI/config.php";
    require  __DIR__ ."/../includes/functions.php";

	//$db_link = new MysqliDb ('web1rs.das-group.com', 'fullypromoted', 'h28r7gdk', 'fullypromoted');

	$values = $_POST;
	$settings = $settings[$_SESSION['database']]; // $settings comes from config.php. It contains the db_name, client
	//$db_link = new MysqliDb(null,null,null,$_SESSION['database']); // $host = null, $username = null, $password = null, $db = 'fullypromoted'
	
	if(isset($_POST['storeid']) && !empty($_POST['storeid'])){
		$store_id = $_POST['storeid'];
	}else{
		$store_id = $_SESSION["storeid"];
	}
	
	//$store_id = '18910';
	
	//Retrieves all information from the locationlist table where store id = $_SESSION["storeid"] 
	$current_data = $db->where("storeid", $store_id , "=")
							->get($settings["db_name"].".locationlist", null, "*");
	//Contains the data that will be updated in the locationlist table
	$update_data = [];
	
	######CONTACT INFORMATION FORM ######
	if(array_key_exists("fname1", $values) && (strcmp($values["fname1"], $current_data[0]["fname1"]) !== 0) ){
		$update_data["fname1"] = $values["fname1"];
	}
	if(array_key_exists("lname1", $values) && (strcmp($values["lname1"], $current_data[0]["lname1"]) !== 0) ){		
			$update_data["lname1"] = $values["lname1"];
	}
	if(array_key_exists("displayname", $values) && (strcmp($values["displayname"], $current_data[0]["displayname"]) !== 0) ){		
			$update_data["displayname"] = $values["displayname"];
	}
	if(array_key_exists("phone1", $values) && (strcmp($values["phone1"], $current_data[0]["phone1"]) !== 0) ){
			$update_data["phone1"] = $values["phone1"];
	}
	if(array_key_exists("reportemail", $values) && (strcmp($values["reportemail"], $current_data[0]["reportemail"]) !== 0) ){
			$update_data["reportemail"] = $values["reportemail"];
	}
	if(array_key_exists("fname2", $values) && (strcmp($values["fname2"], $current_data[0]["fname2"]) !== 0) ){
			$update_data["fname2"] = $values["fname2"];
	}
	if(array_key_exists("lname2", $values) && (strcmp($values["lname2"], $current_data[0]["lname2"]) !== 0) ){
			$update_data["lname2"] = $values["lname2"];
	}
	if(array_key_exists("phone2", $values) && (strcmp($values["phone2"], $current_data[0]["phone2"]) !== 0) ){
			$update_data["phone2"] = $values["phone2"];
	}
	if(array_key_exists("altreportemail", $values) && (strcmp($values["altreportemail"], $current_data[0]["altreportemail"]) !== 0) ){
			$update_data["altreportemail"] = $values["altreportemail"];
	}
	######END CONTACT INFORMATION FORM ######
	
	//The locationlisttable will be updated, if $update_data has data for updaiting 
	if(count($update_data)){
		$update = $db->where("storeid", $store_id, "=" )
						->update($settings["db_name"].".locationlist", $update_data);
		//track($_SESSION["username"],$_SESSION["storeid"],"profile",join(",", array_keys($update_data)));
		//track('dasit',$store_id,"profile",join(",", array_keys($update_data)));
		
		if(!$update){
			$subject = "New Yext Location ".$location_id;
			$headers = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
			$headers .= 'From: <noreply@das-group.com>' . "\r\n";	
			$message = "Something went wrong, try again. \n Exception: ".$db_link->getLastError()."\n ArrayData : ".json_encode($update_data);
			mail("sicwing@das-group.com",$subject,$message,$headers);				
		}
	}
	
	$response = ($update) ?
				["style"=>"alert-success", "msg"=>"Profile updated successfuly."]:
				["style"=>"alert-danger", "msg"=>"Something went wrong, please try again."];
	
	unset($values, $_POST, $update_data);
	header('Content-type:application/json;charset=utf-8'); 
	echo json_encode($response);