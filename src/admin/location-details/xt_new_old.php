<?
session_start();
error_reporting(E_ALL & ~E_NOTICE);
include_once ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
//include ($_SERVER['DOCUMENT_ROOT']."/BrightLocal-API-Helper-master/src/ranking.php");

/*
if(!$_SESSION["username"]){
	$_SESSION['error']="You must be logged in to view this page.";
	header('location: /login.php');
	exit;
}*/

//Contains the data that will be inserted in the locationlist table
$data_to_insert = [];

###### REQUIRE FIELDS FOR YEXT ######
//Getting storeid from the core information form
$storeid = $_POST['storeid'];
$data_to_insert["storeid"] = $storeid;
$data_to_insert["displayname"] = $displayName = filter_var($_POST['displayname'], FILTER_SANITIZE_STRING);
$data_to_insert["phone"] = $phone = filter_var($_POST['phone'], FILTER_SANITIZE_NUMBER_INT);
$data_to_insert["address"] = $address = filter_var($_POST['address'], FILTER_SANITIZE_STRING);
$data_to_insert["city"] = $city = filter_var($_POST['city'], FILTER_SANITIZE_STRING);
$data_to_insert["state"] = $state = filter_var($_POST['state'], FILTER_SANITIZE_STRING);
$data_to_insert["zip"] = $zip = filter_var($_POST['zip'], FILTER_SANITIZE_NUMBER_INT);
$data_to_insert["country"] = 'USA';
###### END REQUIRE FIELDS FOR YEXT ######

$fpUpdates = ["dastoken" => "DAS%])p6Eu8SUuqN9U",
			  "action" => "new",
			  "table" => "locationlist"];

//Verifying the required fields
if($data_to_insert["storeid"] != ""){
	
	if($data_to_insert["displayname"] != ""){
		
		if($data_to_insert["phone"] != ""){
			
			if(($data_to_insert["address"] != "") && ($data_to_insert["city"] != "") && ($data_to_insert["state"] != "") 
				&& ($data_to_insert["zip"] != "")){
				
				//Looks for the storeid in the locationlist table
				$loc = $db->where("storeid",$storeid, "=")
								->getOne("locationlist");
								
				//If the storeid doesn't exist the script will continue. Otherwise, it will redirect the user
				if(!count($loc)){
					###### CORE INFORMATION FORM ######
					$data_to_insert["companyname"] = $companyname = filter_var($_POST['companyname'], FILTER_SANITIZE_STRING);
					$data_to_insert["url"] = $url = filter_var($_POST['url'], FILTER_SANITIZE_URL);
					$data_to_insert["email"] = $primary_location_email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
					###### END CORE INFORMATION FORM ######
						
					###### LOCATION INFORMATION FORM ######
					$data_to_insert["address2"] = $address2 = filter_var($_POST['address2'], FILTER_SANITIZE_STRING);
					
					//Get latitude and longitude base on the address provided on the location form
					$fullAddress = $address.' '.$address2.' '.$city.' '.$state.' '.$zip; // Google HQ
					$prepAddr = str_replace(' ','+',$fullAddress);
					$maps_api = "AIzaSyDp-j5aC1D1bi_H14HDvwv-0Rpj66MxL-A";
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
					$data_to_insert["latitude"] = $latitude = $response_a->results[0]->geometry->location->lat;
					$data_to_insert["longitude"] = $longitude = $response_a->results[0]->geometry->location->lng;
					
					$days= array('mon','tue','wed','thu','fri','sat','sun');
					foreach ($days as $day){
						if($_POST[$day.'_opt']){
							${$day."_open"} = '0:00';
							${$day."_close"} = '0:00';
						}else{
							${$day."_open"} = $_POST[$day.'_open'];
							${$day."_close"} = $_POST[$day.'_close'];
						}
						echo $day." open- ".${$day."_open"}.'<br>';
						echo $day." close- ".${$day."_close"}.'<br>';
					}
					
					//LOCATION HOURS
					$data_to_insert["mon_open"] = $mon_open;
					$data_to_insert["mon_close"] = $mon_close;
					$data_to_insert["mon_opt"] = $_POST['mon_opt'];
					
					$data_to_insert["tue_open"] = $tue_open;
					$data_to_insert["tue_close"] = $tue_close;
					$data_to_insert["tue_opt"] = $_POST['tue_opt'];
					
					$data_to_insert["wed_open"] = $wed_open;
					$data_to_insert["wed_close"] = $wed_close;
					$data_to_insert["wed_opt"] = $_POST['wed_opt'];
					
					$data_to_insert["thu_open"] = $thu_open;
					$data_to_insert["thu_close"] = $thu_close;
					$data_to_insert["thu_opt"] = $_POST['thu_opt'];
					
					$data_to_insert["fri_open"] = $fri_open;
					$data_to_insert["fri_close"] = $fri_close;
					$data_to_insert["fri_opt"] = $_POST['fri_opt'];
					
					$data_to_insert["sat_open"] = $sat_open;
					$data_to_insert["sat_close"] = $sat_close;
					$data_to_insert["sat_opt"] = $_POST['sat_opt'];
					
					$data_to_insert["sun_open"] = $sun_open;
					$data_to_insert["sun_close"] = $sun_close;
					$data_to_insert["sun_opt"] = $_POST['sun_opt'];
					
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
					
					###### CONTACT INFORMATION FORM ######
					$data_to_insert["suspend"] = '1';

					$data_to_insert["launch_date"] = date("Y-m-d", strtotime($_POST['launch_date']));
					$data_to_insert["start_campaign"] = date("Y-m-d", strtotime($_POST['campaign_start']));
					###### END CONTACT INFORMATION FORM ######
					
					###### ADDITIONAL INFORMATION ######
					$data_to_insert["adfundmember"] = 'Y';
					
					//Inserts the information of the form in the locationlist table
					$location_was_inserted = $db->insert("locationlist",$data_to_insert);
					
					if($location_was_inserted){
						
						###### CREATING YEXT LOCATION TRANSACTION ######
						/*$urlsCurl = [];
						$server = ($_SERVER["HTTPS"] == "on") ? "https://".$_SERVER["SERVER_NAME"] : "http://".$_SERVER["SERVER_NAME"]	;
						
						$dataCurl = ["client"=> $_SESSION['database'], "storeid"=>$storeid ];

						$urlsCurl[] = "$server/yextAPI/xt_add_yext_location.php";

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

						$curl_info_response = curl_getinfo($ch2[0]);*/

						/*Sets error_flag to true if the response that is comming from xt_add_yext_location.php is diffrent from 200. 
						(It happens when the exit() function is called)*/
						/*$error_flag = false;
						
						if($curl_info_response['http_code'] != '200'){
							$error_flag = true;
						}

						foreach(array_keys($ch2) as $key){		     
						  curl_multi_remove_handle($mh, $ch2[$key]);
						}

						curl_multi_close($mh);*/
						
						/* Getting Call Tracking Number from Convirza */
						//create_report('',$_SESSION['client']."-".$storeid);
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
						}else{
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
							
							if(count($fpUpdates) && count($data_to_insert) && $location_was_inserted){
		
								$urlUpdate = "https://fullypromoted.com/xt_cupdate.php/?".http_build_query(array_merge($data_to_insert,$fpUpdates));

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
							
							$_SESSION['success']= $companyname." was successfully added.";
						//}
					}else{
						$_SESSION['error'] = "There was an error adding this store in the database.";
						header("location:/admin/location-details/new.php");
						exit;
					}
				}else{
					$_SESSION['error']="This store id already exists";
					header('location:/admin/location-details/new.php');
					exit;
				}
			}else{
				$_SESSION['error'] = "Address, City, State, and Zip must be filled out.";
				header("location:/admin/location-details/new.php");
				exit;
			}
		}else{
			$_SESSION['error'] = "Business Phone must be filled out.";
			header("location:/admin/location-details/new.php");
			exit;
		}	
	}else{
		$_SESSION['error'] = "Display Name must be filled out.";
		header("location:/admin/location-details/new.php");
		exit;
	}	
}else{
	$_SESSION['error'] = "Store id must be filled out.";
	header("location:/admin/location-details/new.php");
	exit;
}
header('location:/admin/location-details/new.php');