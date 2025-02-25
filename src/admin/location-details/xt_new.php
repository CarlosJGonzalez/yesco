<?
session_start();
//error_reporting(E_ALL & ~E_NOTICE);
include_once ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include_once ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");
//include ($_SERVER['DOCUMENT_ROOT']."/BrightLocal-API-Helper-master/src/ranking.php");

require_once ($_SERVER['DOCUMENT_ROOT']."/includes/DasApiSDK/vendor/autoload.php");
use Das\Client;


if(!$_SESSION["email"] && ($_SESSION["user_role_name"] != "admin_root" || $_SESSION["user_role_name"] != "admin_rep")){
	pageRedirect("Access denied: You must be authorized to view this page.", "error", "/");
}

$clientObj = new Client($token_api);
//Contains the data that will be inserted in the locationlist table
$data_to_insert = [];
$data_to_insert_admin_login = []; //Contains credentials for the website in admin_login

###### CORE INFORMATION FORM ######
$storeid = $_POST['storeid']; //Required
$data_to_insert["storeid"] = $storeid; //Required
$data_to_insert["companyname"] = $companyname = filter_var($_POST['companyname'], FILTER_SANITIZE_STRING); //Required
$data_to_insert["displayname"] = $displayName = filter_var($_POST['displayname'], FILTER_SANITIZE_STRING); //Required
$data_to_insert["url"] = $url = strtolower(filter_var($_POST['url'], FILTER_SANITIZE_URL)); //Required
//$data_to_insert["url"] = CLIENT_URL.'locations/'.$url; //Required
$data_to_insert["email"] = $primary_location_email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL); //Required
$data_to_insert["phone"] = $phone = filter_var($_POST['phone'], FILTER_SANITIZE_NUMBER_INT); //Required
###### END CORE INFORMATION FORM ######

###### LOCATION INFORMATION FORM ######
$data_to_insert["address"] = $address = filter_var($_POST['address'], FILTER_SANITIZE_STRING); //Required
$data_to_insert["city"] = $city = filter_var($_POST['city'], FILTER_SANITIZE_STRING); //Required
$data_to_insert["state"] = $state = filter_var($_POST['state'], FILTER_SANITIZE_STRING); //Required
$data_to_insert["zip"] = $zip = filter_var($_POST['zip'], FILTER_SANITIZE_NUMBER_INT); //Required
$data_to_insert["country"] = 'USA'; //Required
$data_to_insert["address2"] = $address2 = filter_var($_POST['address2'], FILTER_SANITIZE_STRING);
###### END LOCATION INFORMATION FORM ######

###### LOGIN CREDENTIALS FORM ######
$user_email = filter_var($_POST['user_email'], FILTER_SANITIZE_EMAIL); //Required
$password_without_hash = $db->escape($_POST['user_password']);
$user_password = password_hash($password_without_hash, PASSWORD_DEFAULT); //Required
$user_full_name = filter_var($_POST['user_full_name'], FILTER_SANITIZE_STRING); //Required
$send_password_to_user = ( isset($_POST['switch-send-password']) && ($_POST['switch-send-password'] == '1') ) ? true : false;
$new_user = true;
###### END LOGIN CREDENTIALS FORM ######

//Verify the required fields
if($data_to_insert["storeid"] != '' && $data_to_insert["companyname"] != '' && $data_to_insert["displayname"] != '' && $data_to_insert["url"] != ''
   && $data_to_insert["email"] != '' && $data_to_insert["phone"] != '' && $data_to_insert["city"] != '' 
   && $data_to_insert["state"] != '' && $data_to_insert["zip"] != '' && $user_email != ''){
	
	//Clear companyname and add this into local_hashtag
	$replace = array('/','\/'," ", "_", "-", "(", ")",".",",");
	$data_to_insert['local_hashtag'] = str_replace($replace,"",strtolower($data_to_insert["companyname"]));	

	//Looks for the storeid in the locationlist table
	$loc = $db->where("storeid",$storeid, "=")
					->getOne("locationlist");
					
	//If the storeid doesn't exist the script will continue. Otherwise, it will redirect the user
	if(!count($loc)){
		###### LOCATION INFORMATION FORM ######
		
		//Get latitude and longitude base on the address provided on the location form
		$fullAddress = $address.' '.$address2.' '.$city.' '.$state.' '.$zip; // Google HQ
		$prepAddr = str_replace(' ','+',$fullAddress);
		$maps_api = "AIzaSyAfM2dHZh6tUw3Q-l9bT8ugyVpeOBHRkHA";
		$urlmaps='https://maps.google.com/maps/api/geocode/json?address='.$prepAddr."&sensor=false&key=$maps_api";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $urlmaps);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$response = curl_exec($ch);
		error_log(print_r($response,true));
		curl_close($ch);
		$response_a = json_decode($response);
		$data_to_insert["latitude"] = $latitude = $response_a->results[0]->geometry->location->lat;
		$data_to_insert["longitude"] = $longitude = $response_a->results[0]->geometry->location->lng;
		
		//Get the locations hours
		$days = array('mon','tue','wed','thu','fri','sat','sun');
		
		foreach ($days as $day){
			if(isset($_POST[$day.'_opt'])){
				${$day."_open"} = '09:00';
				${$day."_close"} = '17:00';
			}else{
				${$day."_open"} = $_POST[$day.'_open'];
				${$day."_close"} = $_POST[$day.'_close'];
			}
			//echo $day." open- ".${$day."_open"}.'<br>';
			//echo $day." close- ".${$day."_close"}.'<br>';
		}
		
		//LOCATION HOURS
		$data_to_insert["mon_open"] = $mon_open;
		$data_to_insert["mon_close"] = $mon_close;
		if (isset($_POST['mon_opt']) && (!empty($_POST['mon_opt']))){
			$data_to_insert["mon_opt"] = $_POST['mon_opt'];
		}
		
		$data_to_insert["tue_open"] = $tue_open;
		$data_to_insert["tue_close"] = $tue_close;
		if (isset($_POST['tue_opt']) && (!empty($_POST['tue_opt']))){
			$data_to_insert["tue_opt"] = $_POST['tue_opt'];
		}
		
		$data_to_insert["wed_open"] = $wed_open;
		$data_to_insert["wed_close"] = $wed_close;
		if (isset($_POST['wed_opt']) && (!empty($_POST['wed_opt']))){
			$data_to_insert["wed_opt"] = $_POST['wed_opt'];
		}
		
		$data_to_insert["thu_open"] = $thu_open;
		$data_to_insert["thu_close"] = $thu_close;
		if (isset($_POST['thu_opt']) && (!empty($_POST['thu_opt']))){
			$data_to_insert["thu_opt"] = $_POST['thu_opt'];
		}
		
		$data_to_insert["fri_open"] = $fri_open;
		$data_to_insert["fri_close"] = $fri_close;
		if (isset($_POST['fri_opt']) && (!empty($_POST['fri_opt']))){
			$data_to_insert["fri_opt"] = $_POST['fri_opt'];
		}
		
		$data_to_insert["sat_open"] = $sat_open;
		$data_to_insert["sat_close"] = $sat_close;
		if (isset($_POST['sat_opt']) && (!empty($_POST['sat_opt']))){
			$data_to_insert["sat_opt"] = $_POST['sat_opt'];
		}
		
		$data_to_insert["sun_open"] = $sun_open;
		$data_to_insert["sun_close"] = $sun_close;
		if (isset($_POST['sun_opt']) && (!empty($_POST['sun_opt']))){
			$data_to_insert["sun_opt"] = $_POST['sun_opt'];
		}
		###### END LOCATION INFORMATION FORM ######
			
		###### CONTACT INFORMATION FORM ######
		$data_to_insert["fname1"] = $fname1 = filter_var($_POST['fname1'], FILTER_SANITIZE_STRING);
		$data_to_insert["lname1"] = $lname1 = filter_var($_POST['lname1'], FILTER_SANITIZE_STRING);
		$data_to_insert["phone1"] = $phone1 = filter_var($_POST['phone1'], FILTER_SANITIZE_NUMBER_INT);
		$data_to_insert["reportemail"] = $reportemail = filter_var($_POST['reportemail'], FILTER_SANITIZE_EMAIL);
		$data_to_insert["fname2"] = $fname2 = filter_var($_POST['fname2'], FILTER_SANITIZE_STRING);
		$data_to_insert["lname2"] = $lname2 = filter_var($_POST['lname2'], FILTER_SANITIZE_STRING);
		$data_to_insert["phone2"] = $phone2 = filter_var($_POST['phone2'], FILTER_SANITIZE_NUMBER_INT);
		$data_to_insert["altreportemail"] = $altreportemail = filter_var($_POST['altreportemail'], FILTER_SANITIZE_EMAIL);
		###### END CONTACT INFORMATION FORM ######
		
		###### DATES INFORMATION FORM ######
		$data_to_insert["suspend"] = '0';
		$data_to_insert["launch_date"] = date("Y-m-d", strtotime($_POST['launch_date']));
		$data_to_insert["start_campaign"] = date("Y-m-d", strtotime($_POST['campaign_start']));
		###### END DATES INFORMATION FORM ######
		
		###### ADDITIONAL INFORMATION ######
		$data_to_insert["email_review"] = $primary_location_email;
		$data_to_insert["adfundmember"] = 'Y';
		
		//Get the rep id based on the amount of stores assigned to every rep
		$rep_id = getStoreRep($db);
		$data_to_insert["rep"] = $rep_id;
		
		//Inserts the information of the form in the locationlist table
		$location_was_inserted = $db->insert("locationlist",$data_to_insert);
		
		if($location_was_inserted){		
			$clientObj->action( array( 
										"storeid"=> $storeid,
										"client"=> $_SESSION['client'],
										"action" => 1
									));
			
			//Creates credentials in storelogin and live site admin_login table also
			$db->where("name",'store_user', "=");
			$user_role = $db->getOne("user_roles", "id"); //store_user role
			
			$user_status = '1'; //It means active user. 0 is for inactive user in the storelogin table
			
			$db->where("email",$user_email);
			$user = $db->getOne('storelogin', '*');

			//If the user doesn't exist in the database, it will be created
			if(!$db->count>0){
				
				if($user_password != '' && $user_full_name != ""){
					$sql_uuid = "SELECT replace(uuid(),'-','') as token;";
					$row_uuid = $db->rawQuery ($sql_uuid);
					$token_db = $row_uuid[0]["token"];
					
					$token = checkToken('storelogin', $token_db);

					//Data for storelogin
					$data_new_user = array("name"=>$user_full_name, "storeid"=>$storeid, 
										   "email"=>$user_email, "password"=>$user_password, 
										   "token"=>$token, "status"=>$user_status);	
					
					$storelogin_user_inserted_ok = $db->insert ('storelogin', $data_new_user);
					
					//Creates the user in the storelogin table
					if ($storelogin_user_inserted_ok){
						//The var storelogin_user_inserted_ok gets the ID of The Inserted Record in storelogin
						//Creates the role in the storelogin_user_roles table
						$data_user_role = array("id_storelogin"=>$storelogin_user_inserted_ok, "id_user_roles"=>$user_role['id']);
						$storelogin_user_role_inserted_ok = $db->insert ('storelogin_user_roles', $data_user_role);
						
						//Creates the user role
						if(!$storelogin_user_role_inserted_ok){
							pageRedirect("Sorry, there was an error creating your user role.", "error", "/admin/location-details/new.php");
						}
					}else {
						$db->where("storeid",$storeid)->delete('locationlist');
						pageRedirect("Sorry, there was an error creating your user.", "error", "/admin/location-details/new.php");
					}
				
				}else{
					$db->where("storeid",$storeid)->delete('locationlist');
					pageRedirect("Please, enter a password and full name.", "error", "/admin/location-details/new.php");
				}
			}else{
				$branches = $user['storeid'].','.$storeid;
				
				$data_update_user = Array (
					'storeid' => $branches
				);
				
				$db->where("email",$user_email);
				
				if (!$db->update ('storelogin', $data_update_user)){
					$db->where("storeid",$storeid)->delete('locationlist');
					pageRedirect("Sorry, there was an error adding this store to the user: ".$user_email, "error", "/admin/location-details/new.php");
				}
				
				$new_user = false;
			}
			
			###### CREATING YEXT LOCATION TRANSACTION ######
			$urlsCurl = [];
			$server = ($_SERVER["HTTPS"] == "on") ? "https://".$_SERVER["SERVER_NAME"] : "http://".$_SERVER["SERVER_NAME"]	;
			
			$dataCurl = ["client"=> $_SESSION['database'], "storeid"=>$storeid ];
			
			$urlsCurl[] = getFullUrl()."/yextAPI/xt_add_yext_location.php";
			
			/*Sets error_flag to true if the response that is comming from xt_add_yext_location.php is diffrent from 200. 
			(It happens when the exit() function is called)*/
			$error_flag = false;
			
			if(count($urlsCurl)){
				$mh = curl_multi_init();
				foreach($urlsCurl as $key => $value){
					$ch2[$key] = curl_init($value);
					curl_setopt($ch2[$key], CURLOPT_NOBODY, true);
					curl_setopt($ch2[$key], CURLOPT_HEADER, true);
					curl_setopt($ch2[$key], CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch2[$key], CURLOPT_SSL_VERIFYPEER, false);
					curl_setopt($ch2[$key], CURLOPT_SSL_VERIFYHOST, false);
					curl_setopt($ch2[$key], CURLOPT_POST, count($dataCurl));
					curl_setopt($ch2[$key], CURLOPT_POSTFIELDS, http_build_query($dataCurl));      
					curl_multi_add_handle($mh,$ch2[$key]);
				}
				
				do {
				  curl_multi_exec($mh, $running);
				  curl_multi_select($mh);
				} while ($running > 0);

				$curl_info_response = curl_getinfo($ch2[0]);

				/*Sets error_flag to true if the response that is comming from xt_add_yext_location.php is diffrent from 200. 
				(It happens when the exit() function is called)*/
				$error_flag = false;
				
				if($curl_info_response['http_code'] != '200'){
					$error_flag = true;
				}

				foreach(array_keys($ch2) as $key){		     
				  curl_multi_remove_handle($mh, $ch2[$key]);
				}

				curl_multi_close($mh);
			}
			
			###### END CREATING YEXT LOCATION TRANSACTION ######
			
			//Looks for the storeid in the locationlist table
			/*$locationDb = $db->where("storeid",$storeid, "=")
							->getOne("locationlist");
							
			//If the storeid doesn't exist the script will continue. Otherwise, it will redirect the user
			(count($locationDb))*/
			
			//If the exit() function was called on xt_add_yext_location.php, $error_flag was set to true
			/*if ($error_flag) {
				//If the location wasn't created on Yext, it will be deleted from the location list table
				$db->where("storeid",$storeid)->delete('locationlist');
				$_SESSION["error"] = "There was an error adding the location.";	
				
				header('location:/admin/location-details/new.php');
				exit;
			}else{*/
				/*
				###### ADVTRACK ######
				$sql =  "delete from advtrack.client where client='".$_SESSION['client']."-".$storeid."'";
				mysqli_query($conn, $sql);
				$sql =  "insert into advtrack.client(client,name,disposition,displaylevel,active) values('".$_SESSION['client']."-".$storeid."','" .$client." ".$companyname."','Sale,No Sale,Prospect,Existing Customer,Other,Quote Request,Directions,Set Up Apptmt','2','Y')";
				mysqli_query($conn, $sql);
				$sql =  "delete from advtrack.client_mapping where client='".$_SESSION['client']."-".$storeid."'";
				mysqli_query($conn, $sql);	
				$sql =  "insert into advtrack.client_mapping(client,name,branch,area,region) values('".$_SESSION['client']."-".$storeid."','". $cmapname." ".$companyname."','".$storeid."','ALL','')";
				mysqli_query($conn, $sql);	
				$sql =  "delete from advtrack.campid where client='".$_SESSION['client']."-".$storeid."'";
				mysqli_query($conn, $sql);
				$sql =  "insert into advtrack.campid(client,campid,name,type,summarygroup,active,channel) values('".$_SESSION['client']."-".$storeid."','0','None','C','0','Y','SEO')";
				mysqli_query($conn, $sql);
				$sql =  "insert into advtrack.campid(client,campid,name,type,summarygroup,active,channel) values('".$_SESSION['client']."-".$storeid."','0','None','S','0','Y','SEO')";
				mysqli_query($conn, $sql);
				$sql =  "delete from advtrack.goalpages where client='".$_SESSION['client']."-".$storeid."'";
				mysqli_query($conn, $sql);
				$sql =  "insert into advtrack.goalpages(client,name,url,refer,goaltype) values ('".$_SESSION['client']."-".$storeid."','Contact Us Submitted','%contact-success%','','1')";
				mysqli_query($conn, $sql);
				$sql =  "insert into advtrack.goalpages(client,name,url,refer,goaltype) values ('".$_SESSION['client']."-".$storeid."','Landing Page - Quote','%landing/thank-you%','','1')";
				mysqli_query($conn, $sql);
				$sql =  "insert into advtrack.goalpages(client,name,url,refer,goaltype) values ('".$_SESSION['client']."-".$storeid."','Request Quote Submitted','%services/quote-success%','','1')";
				mysqli_query($conn, $sql);
				$sql =  "insert into advtrack.campid(client,campid,name,type,summarygroup,active,channel) values('".$_SESSION['client']."-".$storeid."','999','Google','C','999','Y','SEM')";
				mysqli_query($conn, $sql);
				$sql =  "insert into advtrack.campid(client,campid,name,type,summarygroup,active,channel) values('".$_SESSION['client']."-".$storeid."','999','Google','S','999','Y','SEM')";
				mysqli_query($conn, $sql);
				$sql =  "delete from advtrack.users where client='".$_SESSION['client']."-".$storeid."'";
				mysqli_query($conn, $sql);
				$sql =  "insert into advtrack.users(client,email,password,token,nologin) values('".$_SESSION['client']."-".$storeid."','".$_SESSION['site'].$storeid.$cemail."','".$_SESSION['site'].$storeid."',replace(uuid(),'-',''),'')";
				mysqli_query($conn, $sql);
				$sql =  "delete from advtrack.client_quotes where client='".$_SESSION['client']."-".$storeid. "'";
				mysqli_query($conn, $sql);
				$sql =  "insert into advtrack.client_quotes(client,dbname,tbname,colnames,dispname,isclient) values('".$_SESSION['client']."-".$storeid. "','signarama','contactus','date,name,email,phone,inquiry,message','date,name,email,phone,inquiry,message','0')";
				mysqli_query($conn, $sql);
				$sql =  "select * from advtrack.users where client = '".$_SESSION['client']."-".$storeid."'";
				$result = $conn->query($sql);
				if ($result->num_rows > 0){
					$advrow = $result->fetch_assoc();
					$sql =  "update ".$_SESSION['database'].".storelogin set adjacksso='".$advrow['token']."' where storeid='".$storeid."'";
					mysqli_query($conn, $sql);
				}*/
				
				//Add Google Cost
				/*$next15th = mktime(0, 0, 0, date('n') + (date('j') >= 15), 15);
				$start= date('Y-m-d', $next15th);
				$end = date('Y-m-d', strtotime("+5 years", strtotime($start)));
				$sql =  "insert into advtrack.adcost (client,campid,cost,start,end,type_cost) values('".$_SESSION['client']."-".$storeid."', '999','8.17','".$start."','".$end."','F')";
				mysqli_query($conn, $sql);*/
				
				/* Getting Call Tracking Number from Convirza */
				/*$phone = str_replace('-','',$phone);
				if ( $phone <> ""){
				$url = "https://api.logmycalls.com/services/getNumbers?criteria%5Bsearchpattern%5D=". substr($phone,0,3) . "&criteria%5Bmatchtype%5D=NPA&criteria%5Bquantity%5D=1&api_key=97b8877dc5accc92ac7b0d0c1059aa87&api_secret=%241%24UDVLmdnQ%24qt25eAYjpvtTnHynWOOfd.";
				echo $url;
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_URL,$url);
				$result=curl_exec($ch);
				$jsondata = json_decode($result);
				$tracking_num = $jsondata->available[0];
				echo 'tracking_num-'.$tracking_num;
				$url = "https://api.logmycalls.com/services/getGroups?criteria%5Bname%5D=" .$client."&api_key=97b8877dc5accc92ac7b0d0c1059aa87&api_secret=%241%24UDVLmdnQ%24qt25eAYjpvtTnHynWOOfd.";
				curl_setopt($ch, CURLOPT_URL,$url);
				$result=curl_exec($ch);
				$jsondata = json_decode($result);
				$matches = $jsondata->matches;
				if ($matches == 0){
					echo "New Client";
				}else{
					echo "Existing Client" . $jsondata->results[0]->ouid;
					$ouid = $jsondata->results[0]->ouid;
					$url = "https://api.logmycalls.com/services/createRoute?criteria%5Broute_type%5D=simple&criteria%5Bouid%5D=" . $ouid . "&criteria%5Btracking_number%5D=" . $tracking_num . "&criteria%5Bname%5D=" .$client."{". $_SESSION['client'] ."-". $storeid ."}[999]&criteria%5Bringto_ouid%5D=" . $ouid . "&criteria%5Bdefault_ringto%5D=" . $phone . "&api_key=97b8877dc5accc92ac7b0d0c1059aa87&api_secret=%241%24UDVLmdnQ%24qt25eAYjpvtTnHynWOOfd.";
					echo $url;
					curl_setopt($ch, CURLOPT_URL,$url);
					$result=curl_exec($ch);
					$jsondata = json_decode($result);
					$route_id = $jsondata->call_flow->id;
					$url = "https://api.logmycalls.com/services/createWebhookAssignment?ouid=49409&webhook_provisioned_route_list=[{'webhook_id':345,'provisioned_route_id':" . $route_id . ",'action':'all'}]&api_key=97b8877dc5accc92ac7b0d0c1059aa87&api_secret=%241%24UDVLmdnQ%24qt25eAYjpvtTnHynWOOfd.";
					curl_setopt($ch, CURLOPT_URL,$url);
					$result=curl_exec($ch);
					$sql =  "insert into advtrack.callfire_routing(client,branch,campid,phone,terminatingnum,date) values('".$_SESSION['client']."-".$storeid. "','" .$storeid. "','1','" .$tracking_num. "','" .$phone. "',now())";
					mysqli_query($conn, $sql);
				}
				curl_close($ch);
				}*/
				
				###### END ADVTRACK ######
				
				$fpUpdatesLocationList = ["dastoken" => "DAS%])p6Eu8SUuqN9U",
										  "action" => "new",
										  "table" => "locationlist"];
				
				if(count($fpUpdatesLocationList) && count($data_to_insert)){

					//$urlUpdate = "https://fullypromoted.com/xt_cupdate.php/?".http_build_query(array_merge($data_to_insert,$fpUpdatesLocationList));
					$urlUpdate = CLIENT_URL."xt_cupdate.php/?".http_build_query(array_merge($data_to_insert,$fpUpdatesLocationList));
					
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
				
				$fpUpdatesAdminLogin = ["dastoken" => "DAS%])p6Eu8SUuqN9U",
										"action" => "new",
										"table" => "admin_login"];
				
				$data_to_insert_admin_login["storeid"] = $storeid;
				$data_to_insert_admin_login["email"] = $user_email;
				$data_to_insert_admin_login["password"] = $user_password;
				$data_to_insert_admin_login["token"] = $token;
				$data_to_insert_admin_login["name"] = $user_full_name;
				
				if(count($fpUpdatesAdminLogin) && count($data_to_insert_admin_login)){
					//$urlUpdateAdminLogin = "https://fullypromoted.com/xt_cupdate.php/?".http_build_query(array_merge($data_to_insert_admin_login,$fpUpdatesAdminLogin));
					$urlUpdateAdminLogin = CLIENT_URL."xt_cupdate.php/?".http_build_query(array_merge($data_to_insert_admin_login,$fpUpdatesAdminLogin));
					
					$ch3 = curl_init();
					curl_setopt($ch3, CURLOPT_URL, $urlUpdateAdminLogin);
					curl_setopt($ch3, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch3, CURLOPT_FOLLOWLOCATION, true);
					curl_setopt($ch3, CURLOPT_PROXYPORT, 3128);
					curl_setopt($ch3, CURLOPT_SSL_VERIFYHOST, 0);
					curl_setopt($ch3, CURLOPT_SSL_VERIFYPEER, 0);
					$response_curl_2 = curl_exec($ch3);

					$response_curl_2 = curl_getinfo($ch3, CURLINFO_HTTP_CODE);

					curl_close($ch3);
				}
				
			//}
			
			//Selects the email and email_notification from the representative of the selected storeid 
			$sql_rep_users =  "SELECT strl.email, strl.email_notification, strl.token FROM ".$_SESSION['database'].".storelogin strl, ".$_SESSION['database'].".reps rep WHERE strl.email = rep.email AND rep.id = '".$rep_id."'";
			$rep_users = $db->rawQuery($sql_rep_users);
			
			$emails_tokens = array();
			
			//If the rep users have at least one email, it will store them. 
			if (!empty($rep_users)){
				$token = $rep_users[0]['token'];
				
				//Gets the email from the rep
				if(!empty($rep_users[0]['email_notification'])){
					$to = $rep_users[0]['email_notification'];
					$emails_tokens[] = array("to"=>$to, "token"=>$token);
				}elseif(!empty($rep_users[0]['email']) && filter_var($rep_users[0]['email'], FILTER_VALIDATE_EMAIL)){
					$to = $rep_users[0]['email'];
					$emails_tokens[] = array("to"=>$to, "token"=>$token);
				}
			}
			
			create_notification(array("user_type"=>"das_admin",
									 "user_id"=>$_SESSION['user_id'],
									 "message"=>"A new location was added. <b>Location Name:</b> ".$companyname.". <b>Store Id:</b> ".$storeid,
									 "date"=>$db->now(),
									 "unread"=>"1",
									 "new"=>"1",
									 "msg_type"=>"location-details",
									 "link"=>"/admin/location-details/edit.php?storeid=".$storeid), $emails_tokens);
			
			//Sends email to a new user
			if($new_user){
				$subject = "Welcome to Local ".CLIENT_NAME."!";
				$email_template = file_get_contents($_SERVER['DOCUMENT_ROOT']."/emails/new-user.php");
				$email_template = str_replace("%%NAME%%", $user_full_name, $email_template);
				$email_template = str_replace("%%USERNAME%%", $user_email, $email_template);
				$email_template = str_replace("%%CLIENT_URL%%", CLIENT_URL, $email_template);
				$email_template = str_replace("%%LOCAL_CLIENT_URL%%", LOCAL_CLIENT_URL, $email_template);
				$email_template = str_replace("%%YEAR%%", date("Y"), $email_template);
				
				if($send_password_to_user){
					//Password is only available for the user and location rep
					$email_template1 = str_replace("%%CLIENT_NAME%%", CLIENT_NAME, $email_template);
					$email_template1 = str_replace("%%PASSWORD%%", $password_without_hash, $email_template1);
					
					$data = Array (
						'copy_hidden'=> $to.',sicwing@das-group.com',
						'subject'    => $subject,
						'from' 	     => 'DAS Group <noreply@das-group.com>',
						'sender'     => 'DAS Group <noreply@das-group.com>',
						'body' 	     => $email_template1,
						'copy' 	     => '',
						'storeid' 	 => $_SESSION['client'].'-'.$storeid,
						'to' 	     => $user_email
					);
				
					$db->insert ('emails_send.emails', $data);
				}
				
			$send_email_to_seo = true;
			
			}else{ // Sends email to an existing user
				$subject = "Welcome to Local ".CLIENT_NAME."!";
				$email_template = file_get_contents($_SERVER['DOCUMENT_ROOT']."/emails/new-location-existing-user.php");
				$email_template = str_replace("%%NAME%%", $user_full_name, $email_template);
				$email_template = str_replace("%%BUSINESS_NAME%%", $companyname, $email_template);
				$email_template = str_replace("%%CLIENT_URL%%", CLIENT_URL, $email_template);
				$email_template = str_replace("%%LOCAL_CLIENT_URL%%", LOCAL_CLIENT_URL, $email_template);
				$email_template = str_replace("%%YEAR%%", date("Y"), $email_template);
				$email_template = str_replace("%%PRIMARY_LOCATION_EMAIL%%", $primary_location_email, $email_template);
				$email_template = str_replace("%%CLIENT_NAME%%", CLIENT_NAME, $email_template);
				
				$data = Array (
					'copy_hidden'=> $to.',sicwing@das-group.com,nidhi@das-group.com',
					'subject'    => $subject,
					'from' 	     => 'DAS Group <noreply@das-group.com>',
					'sender'     => 'DAS Group <noreply@das-group.com>',
					'body' 	     => $email_template,
					'copy' 	     => '',
					'storeid' 	 => $_SESSION['client'].'-'.$storeid,
					'to' 	     => $user_email
				);
			
				$db->insert ('emails_send.emails', $data);
				
				$send_email_to_seo = false;
			}
			
			if($send_email_to_seo){
				//This is only for SEO purpose
				$subject = "Welcome to Local ".CLIENT_NAME."! ".$companyname.' Store id: '.$storeid;
				$email_template = str_replace("%%CLIENT_NAME%%", CLIENT_NAME, $email_template);
				
				//Send email to Nidhi
				$data_email = Array (
					'copy_hidden'=> 'sicwing@das-group.com',
					'subject'    => $subject,
					'from' 	     => 'DAS Group <noreply@das-group.com>',
					'sender'     => 'DAS Group <noreply@das-group.com>',
					'body' 	     => $email_template,
					'copy' 	     => $to,
					'storeid' 	 => $_SESSION['client'].'-'.$storeid,
					'to' 	     => 'nidhi@das-group.com'
				);
				
				$db->insert ('emails_send.emails', $data_email);
			}
			
			$data_to_insert["user_email"] = $user_email;
			$data_to_insert["user_full_name"] = $user_full_name;
			$data_to_insert["send_password_to_user"] = $send_password_to_user;
			
			$data_track = array("updates"=>json_encode($data_to_insert),"section"=>"location-details", "details"=>"Created: ".$companyname);
			track_activity($data_track);
			
			pageRedirect($companyname." was successfully added.", "success", "/admin/location-details/new.php");

		}else{
			pageRedirect("There was an error adding this location.", "error", "/admin/location-details/new.php");
		}
	}else{
		pageRedirect("This store id already exists", "error", "/admin/location-details/new.php");
	}
}else{
	pageRedirect("All required fields must be filled out.", "error", "/admin/location-details/new.php");
}