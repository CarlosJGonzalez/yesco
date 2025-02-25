<?php
$trainingCategories = array("Videos", "Support Documents");

function get_ip(){
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	return $ip;
}

function makeEmail(&$db,$subject,$body,$to,$storeid,$cc='',$bcc=''){
	$data = Array (
		'copy_hidden'=> $bcc,
		'subject'    => $subject,
		'from' 	     => 'DAS Group <noreply@das-group.com>',
		'sender'     => 'DAS Group <noreply@das-group.com>',
		'body' 	     => $body,
		'copy' 	     => $cc,
		'storeid' 	 => $storeid,
		'to' 	     => $to
	);	
	
	$id = $db->insert ('emails_send.emails', $data);	
	return $id;
}


function stateSelect($selected = ''){
	$states = array(
		'AL'=>'Alabama',
		'AK'=>'Alaska',
		'AZ'=>'Arizona',
		'AR'=>'Arkansas',
		'CA'=>'California',
		'CO'=>'Colorado',
		'CT'=>'Connecticut',
		'DE'=>'Delaware',
		'DC'=>'District of Columbia',
		'FL'=>'Florida',
		'GA'=>'Georgia',
		'HI'=>'Hawaii',
		'ID'=>'Idaho',
		'IL'=>'Illinois',
		'IN'=>'Indiana',
		'IA'=>'Iowa',
		'KS'=>'Kansas',
		'KY'=>'Kentucky',
		'LA'=>'Louisiana',
		'ME'=>'Maine',
		'MD'=>'Maryland',
		'MA'=>'Massachusetts',
		'MI'=>'Michigan',
		'MN'=>'Minnesota',
		'MS'=>'Mississippi',
		'MO'=>'Missouri',
		'MT'=>'Montana',
		'NE'=>'Nebraska',
		'NV'=>'Nevada',
		'NH'=>'New Hampshire',
		'NJ'=>'New Jersey',
		'NM'=>'New Mexico',
		'NY'=>'New York',
		'NC'=>'North Carolina',
		'ND'=>'North Dakota',
		'OH'=>'Ohio',
		'OK'=>'Oklahoma',
		'OR'=>'Oregon',
		'PA'=>'Pennsylvania',
		'RI'=>'Rhode Island',
		'SC'=>'South Carolina',
		'SD'=>'South Dakota',
		'TN'=>'Tennessee',
		'TX'=>'Texas',
		'UT'=>'Utah',
		'VT'=>'Vermont',
		'VA'=>'Virginia',
		'WA'=>'Washington',
		'WV'=>'West Virginia',
		'WI'=>'Wisconsin',
		'WY'=>'Wyoming',
	);
	foreach($states as $abbr => $full){
		if($selected==$abbr)
			$output .='<option value="'.$abbr.'" selected>'.$full.'</option>';
		else
			$output .='<option value="'.$abbr.'">'.$full.'</option>';
	}
	return $output;
}
function get_months(){
	return array (
	1 => "January",
	2 => "February",
	3 => "March",
	4 => "April",
	5 => "May",
	6 => "June",
	7 => "July",
	8 => "August",
	9 => "September",
	10 => "October",
	11 => "November",
	12 => "December" );
}
function track($user, $storeid, $section, $extra = ""){
	include ($_SERVER['DOCUMENT_ROOT'].'/connect.php');
	$query = filter_var($extra, FILTER_SANITIZE_STRING);
	$sql="INSERT into tracker (username, time, storeid, section, query) values ('".$user."',NOW(),'".$storeid."','".$section."','".$query."')";
	mysqli_query($conn, $sql);
	return;
}

function getUpdatedCols($data){
	$updates = Array();
	foreach($data as $col => $val){
		$updates[$col] = $val;
	}
	return json_encode($updates);
}
function grab_vimeo_thumbnail($vimeo_url){
    if( !$vimeo_url ) return false;
    $data = json_decode( curl_get_contents( 'http://vimeo.com/api/oembed.json?url=' . $vimeo_url ) );
    if( !$data ) return false;
   
    return str_replace("http:", 'https:', $data->thumbnail_url);
}
function curl_get_contents($url)
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);

    $data = curl_exec($ch);
    curl_close($ch);

    return $data;
}
function url_get_contents($url, $useragent='cURL', $headers=false, $follow_redirects=false, $debug=false) {
    // initialise the CURL library
    $ch = curl_init();
    // specify the URL to be retrieved
    curl_setopt($ch, CURLOPT_URL,$url);
    // we want to get the contents of the URL and store it in a variable
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    // specify the useragent: this is a required courtesy to site owners
    curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
    // ignore SSL errors
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    // return headers as requested
    if ($headers==true){
        curl_setopt($ch, CURLOPT_HEADER,1);
    }
    // only return headers
    if ($headers=='headers only') {
        curl_setopt($ch, CURLOPT_NOBODY ,1);
    }
    // follow redirects - note this is disabled by default in most PHP installs from 4.4.4 up
    if ($follow_redirects==true) {
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); 
    }
    // if debugging, return an array with CURL's debug info and the URL contents
    if ($debug==true) {
        $result['contents']=curl_exec($ch);
        $result['info']=curl_getinfo($ch);
    }
    // otherwise just return the contents as a variable
    else $result=curl_exec($ch);
    // free resources
    curl_close($ch);
    return $result;
}
function upload_image($params){
	$local_server = getFullUrl();
	
	$defaults = Array("target_dir"=>$_SERVER["DOCUMENT_ROOT"]."/uploads",
					 "max_file_size"=>"35000000");
	
	$params = array_merge($defaults,$params);
	$output = Array();

	$temp = explode(".", $params["file"]["name"]);
	if($params["add_storeid"]==1)
		$newfilename = slugify($temp[0]).'-'.$params['storeid'].'.'.end($temp);
	else
		$newfilename = slugify($temp[0]).'.'.end($temp);

	$uploadOk = 1;
	$imageFileType = pathinfo($newfilename,PATHINFO_EXTENSION);


	// Check file size
	if ($params["file"]["size"] > $params["max_file_size"]) {
		echo "Sorry, your file is too large.";
		$output["error"] = "Your file is too large. The max file size is ".($params["max_file_size"]/1000000)."MB.";
		$uploadOk = 0;
	}
	
	// Check if file already exists
	$target_file = checkFile($params["target_dir"],$newfilename);
	
	// Allow certain file formats
	if(isset($params["allow_file_types"])){
		$allow_file_types = array_map('strtolower', $params["allow_file_types"]);
	
		if(!in_array(strtolower($imageFileType),$allow_file_types)) {
			$uploadOk = 0;
			$output["error"] = "This format is not accepted.";
		}
	}

	if (!is_dir($params["target_dir"])) {
		mkdir($params["target_dir"], 0777, true);
	}

	// Check if $uploadOk is set to 0 by an error
	if ($uploadOk == 0) {
		return $output;
	// if everything is ok, try to upload file
	} else {
		if (move_uploaded_file($params["file"]["tmp_name"], $target_file)) {
			
			$path = str_replace($_SERVER["DOCUMENT_ROOT"],$local_server,$target_file);
			$output = Array("success"=> 1,
						   "path"=>$path);
			if($params['make_thumbnail']==1 && isset($params['thumbnail_dest'])){
				$img = end(explode("/",$target_file));
				
				if($params['type']=="video"){
					$file_name = explode(".",$img);
					$img = $file_name['0'].'.jpg';
					
					$path = str_replace($_SERVER["DOCUMENT_ROOT"],$local_server,$params["target_dir"]);
					
					if (strpos($params['src'], 'vimeo') !== false){
						$src = grab_vimeo_thumbnail($params['src']);
					}
					
					if (strpos($params['src'], 'youtube') !== false){
						$vid = explode("=",$params['src']);
						$vid = $vid[1];
						$src = "https://img.youtube.com/vi/$vid/mqdefault.jpg";
					}
					
					$ch = curl_init($src);
					$fp = fopen($params["target_dir"]."/".$img, 'wb');
					curl_setopt($ch, CURLOPT_FILE, $fp);
					curl_setopt($ch, CURLOPT_HEADER, 0);
					curl_exec($ch);
					curl_close($ch);
					fclose($fp);
			
					$src = 	$params["target_dir"]."/".$img;	
					$output['image'] = $path."/".$img;
			
				}else{
					$src = $params["target_dir"]."/".$img;
				}
				$thumbnail = create_thumbnail($src,$params['thumbnail_dest']."/".$img,'250');
				$output["thumbnail"]["status"] = $thumbnail;
				$output["thumbnail"]["path"] = str_replace($_SERVER["DOCUMENT_ROOT"],$local_server,$params['thumbnail_dest']."/".$img);
			}
			return $output;
		}else {
			$output["error"] = "There was an error uploading the file.";
			return $output;
		 }
	}
}
function create_thumbnail($src, $dest, $desired_width, $keepRatio = false) {
	/* read the source image */
                      $image_Type = '';
	$source_imageJpg = imagecreatefromjpeg($src);
                     $source_imagePng = imagecreatefrompng($src);
                      $source_imageGif = imagecreatefromgif($src);
                     if($source_imageJpg){
                          $source_image = $source_imageJpg;
                          $image_Type ='jpg';
                      }else if($source_imagePng){
                          $source_image = $source_imagePng;
                           $image_Type =  'png';
                      }else if($source_imageGif){
                          $source_image = $source_imageGif;
                           $image_Type = 'gif';
                      }
	//var_dump($source_imageJpg);
	$width = imagesx($source_image);
	$height = imagesy($source_image);
	if(!$keepRatio){
		if($width>$height){
			$width = $height;
		}else if($height>$width){
			$height = $width;
		}
		$desired_height = $desired_width;
	}else{
		/* find the "desired height" of this thumbnail, relative to the desired width  */
		$desired_height = floor($height * ($desired_width / $width));
	}
	
	/* create a new, "virtual" image */
	$virtual_image = imagecreatetruecolor($desired_width, $desired_height);
	/* copy source image at a resized size */
	imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);
	/* create the physical thumbnail image to its destination */
	 if($image_Type =='jpg'){
		 return imagejpeg($virtual_image,$dest,100);
	}else if($image_Type == 'png'){
	   return imagepng($virtual_image,$dest,1);
	}else if($image_Type == 'gif'){
	   return imagegif($virtual_image,$dest);
	}
  }
function checkFile($target_dir,$newfilename){
	if (file_exists($target_dir."/".$newfilename)) {
		$newfilename = renameFile($newfilename);
		return checkFile($target_dir,$newfilename);
	}
	$target_file = $target_dir."/".$newfilename;
	return $target_file;
}
function renameFile($newfilename){
	return rand(0, 1000).'-'.$newfilename;
}

function slugify($text){
  // replace non letter or digits by -
  $text = preg_replace('~[^\pL\d]+~u', '-', $text);

  // transliterate
  $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

  // remove unwanted characters
  $text = preg_replace('~[^-\w]+~', '', $text);

  // trim
  $text = trim($text, '-');

  // remove duplicate -
  $text = preg_replace('~-+~', '-', $text);

  // lowercase
  $text = strtolower($text);

  return $text;
}
function track_activity($data){
	global $db;
	$default_data = array("username"=>$_SESSION['email'],
						 "storeid"=>$_SESSION['storeid'],
						 "ip_address"=>get_ip(),
						 "time"=>$db->now(),
						 "uri"=>getFullUrl().$_SERVER['REQUEST_URI']);
	$result = array_merge($default_data,$data);
	
	$db->insert ('activity', $result);
}
function get_access_locations($user_id){
	global $db;
	
	if($_SESSION['admin']){
		$db->orderBy("companyname","asc");
		$locations = $db->get("locationlist",null,array("storeid","companyname"));
	}else{
		//$locations = $db->rawQuery("SELECT storeid,companyname FROM `locationlist` where storeid in (select storeid from storelogin where id=?) order by companyname asc",array($user_id));
		$storeids = $db->where('id',$user_id)->getOne('storelogin','storeid');
		$db->where('storeid',explode(',',$storeids['storeid']),'IN');
		$db->where('suspend','0');
		$locations = $db->get('locationlist',null,'storeid,companyname');	
	}
	
	return $locations;
}
/*************Yext functions*****************/
function transformpaymentOptions($payment_methods){

		 $paymentMethodEnum = 	["amex"=>"AMERICANEXPRESS",
								"cash"=>"CASH",
								"visa"=>"VISA",
								"cheque"=>"CHECK",
								"discover"=>"DISCOVER",
								"mastercard"=>"MASTERCARD",
								"dinners"=>"DINERSCLUB",
								"financing"=>"FINANCING",
								"andropay" =>"ANDROIDPAY",
								"applepay"=>"APPLEPAY",
								"samsung" => "SAMSUNGPAY"];

    
        $transformedPaymentMethods = array_filter($paymentMethodEnum, function($v,$k ) use($payment_methods){											
											return in_array($k,$payment_methods) || in_array($v,$payment_methods);
									},ARRAY_FILTER_USE_BOTH);									
		return array_values($transformedPaymentMethods);
}

function transformHoursFromYEXT($hours){
		
		$days = array("Sunday","Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
		$formatted_hours = [];
		$arrayHours = explode(",",$hours);

		foreach($days as $key=>$day){
			
			$pair = array_filter($arrayHours, function($v) use($key){					
					return (substr($v,0,1) == ($key+1));
				});
			
			$abbr = strtolower(substr($day,0,3));
			$opt = "C"	;	
			
			if(count($pair)){
				
				$hoursSplit = explode(":",array_values($pair)[0]);
				$open =$hoursSplit[1].":".$hoursSplit[2];
				$close= $hoursSplit[3].":".$hoursSplit[4];	
				$opt = null	;					
			}
			$formatted_hours = array_merge($formatted_hours,[$abbr."_open"=>isset($open)?$open:"00:00",
									$abbr."_close"=>isset($close)?$close:"00:00",
									$abbr."_opt"=>$opt
								]);
			unset($open, $close, $opt);
		}		
		return $formatted_hours;
}

function transformYextHolidayHours($arrayYextHolidayHours){
	$view_formatted = [];
	foreach($arrayYextHolidayHours as $item){
		$hours = [];
		$splitHours = "";
		if(!$item["isRegularHours"]){				
			if($item["hours"] != ""){
				if($item["hours"] == "0:00:23:59"){
					$type = "ALWAYS_OPEN";
				}else{						
					$splitHours = explode(",",$item["hours"]); 
					$type = count($splitHours) > 1 ? "SPLIT" : "OPEN";							
					$split_1 = explode(":",$splitHours[0] );
					$hours = ["holStart" => date("g:i A", strtotime($split_1[0].":".$split_1[1])),
								"holEnd" => date("g:i A", strtotime($split_1[2].":".$split_1[3]))
							]	;					
					
					if(count($splitHours) > 1){
						$split_2= explode(":",$splitHours[1] );
					
						$hours = array_merge($hours, ["holStart_split"=> date("g:i A", strtotime($split_2[0].":".$split_2[1])),
													"holEnd_split"=>date("g:i A",  strtotime($split_2[2].":".$split_2[3]))]);
					}	
				}
				
			}else{
				$type = "CLOSED";
			}
		}else{
			$type = "IS_REGULAR_HOURS";
		}
		$view_formatted[] = array_merge([ "date" =>date("m/d/Y", strtotime($item["date"])), "type"=>$type],$hours );
	}
	return $view_formatted;
}

function transformHoursYextFormat($local_list, &$location_hours, &$additional_hours_text){

        $hours_string = [];

        if(($local_list["mon_opt"] !== "C") && ($local_list["mon_open"] !== "00:00") && ($local_list["mon_close"] !== "00:00") 
                    && ($local_list["mon_close"] != "") && ($local_list["mon_open"] != "") ) {
                if( $local_list["mon_opt"] == "A"){
                    $additional_hours_text[] = "Monday";
                }else{
                    $mon_open = (substr($local_list["mon_open"], 0, 1) == "0")? substr($local_list["mon_open"], 1):$local_list["mon_open"];
                    $mon_close = (substr($local_list["mon_close"], 0, 1) == "0")? substr($local_list["mon_close"], 1):$local_list["mon_close"];
                    $hours_string[] =  "2:".$mon_open.":". $mon_close;
                }

            }
            if(($local_list["tue_opt"] !== "C") && ($local_list["tue_open"] !== "00:00") && ($local_list["tue_close"] !== "00:00") &&
                           ($local_list["tue_open"] != "") && ($local_list["tue_close"] != "") ) {
                if( $local_list["tue_opt"] == "A"){
                    $additional_hours_text[] = "Tuesday";
                }else{
                    $tue_open = (substr($local_list["tue_open"], 0, 1) == "0")? substr($local_list["tue_open"], 1):$local_list["tue_open"];
                    $tue_close = (substr($local_list["tue_close"], 0, 1) == "0")? substr($local_list["tue_close"], 1):$local_list["tue_close"];
                    $hours_string[] =  "3:".$tue_open.":".$tue_close;
                }

            }
            if(($local_list["wed_opt"] !== "C") && ($local_list["wed_open"] !== "00:00") && ($local_list["wed_close"] !== "00:00")
                            && ($local_list["wed_open"] != "") && ($local_list["wed_close"] !="")) {
                if( $local_list["wed_opt"] == "A"){
                    $additional_hours_text[] = "Wednesday";
                }else{
                    $wed_open = (substr($local_list["wed_open"], 0, 1) == "0")? substr($local_list["wed_open"], 1):$local_list["wed_open"];
                    $wed_close = (substr($local_list["wed_close"], 0, 1) == "0")? substr($local_list["wed_close"], 1):$local_list["wed_close"];
                    $hours_string[] =  "4:".$wed_open.":".$wed_close;
                }

            }
             if(($local_list["thu_opt"] !== "C") && ($local_list["thu_open"] !== "00:00") && ($local_list["thu_close"] !== "00:00")
                        && ($local_list["thu_open"] != "") && ($local_list["thu_close"] != "")) {
                if( $local_list["thu_opt"] == "A"){
                   
                    $additional_hours_text[] = "Thursday";
                }else{
                    $thu_open = (substr($local_list["thu_open"], 0, 1) == "0")? substr($local_list["thu_open"], 1):$local_list["thu_open"];
                    $thu_close = (substr($local_list["thu_close"], 0, 1) == "0")? substr($local_list["thu_close"], 1):$local_list["thu_close"];
                    $hours_string[] =  "5:".$thu_open.":".$thu_close;
                }

            }
            if(($local_list["fri_opt"] !== "C") && ($local_list["fri_open"] !== "00:00") && ($local_list["fri_close"] !=="00:00")
                        && ($local_list["fri_open"] != "") && ($local_list["fri_close"] !="")) {
                if( $local_list["fri_opt"] == "A"){
                    $additional_hours_text[] = "Friday";
                }else{
                    $fri_open = (substr($local_list["fri_open"], 0, 1) == "0")? substr($local_list["fri_open"], 1):$local_list["fri_open"];
                    $fri_close = (substr($local_list["fri_close"], 0, 1) == "0")? substr($local_list["fri_close"], 1):$local_list["fri_close"];
                    $hours_string[] =  "6:".$fri_open.":".$fri_close;
                }

            }            
            if(($local_list["sat_opt"] !== "C") && ($local_list["sat_open"] !== "00:00") && ($local_list["sat_close"] !== "00:00")
                        && ($local_list["sat_open"] != "") && ($local_list["sat_close"] != "")) {
                if( $local_list["sat_opt"] === "A"){
                    $additional_hours_text[] = "Saturday";
                }else{
                    $sat_open = (substr($local_list["sat_open"], 0, 1) == "0")? substr($local_list["sat_open"], 1):$local_list["sat_open"];
                    $sat_close = (substr($local_list["sat_close"], 0, 1) == "0")? substr($local_list["sat_close"], 1):$local_list["sat_close"];
                    $hours_string[] =  "7:".$sat_open.":".$sat_close;
                }
            }
            if(($local_list["sun_opt"] !== "C") && ($local_list["sun_open"] !== "00:00") && ($local_list["sun_close"] != "00:00")
                    && ($local_list["sun_open"] != "") && ($local_list["sun_close"] != "")) {
                if( $local_list["sun_opt"] == "A"){
                    $additional_hours_text[] = "Sunday";
                }
                else{
                    $sun_open = (substr($local_list["sun_open"], 0, 1) == "0")? substr($local_list["sun_open"], 1):$local_list["sun_open"];
                    $sun_close = (substr($local_list["sun_close"], 0, 1) == "0")? substr($local_list["sun_close"], 1):$local_list["sun_close"];

                     array_unshift($hours_string, "1:".$sun_open.":".$sun_close);
                }
            }

            $location_hours =  implode(",", $hours_string);

    }

function transformHolidayHours($arrayHolidayHours){
	
	$yextArrayHolHours = [];
	$holStart = array_key_exists("holStart",$arrayHolidayHours)?$arrayHolidayHours["holStart"]:[];
	$holEnd = array_key_exists("holEnd",$arrayHolidayHours)?$arrayHolidayHours["holEnd"]:[];
	$holStart_split = array_key_exists("holStart_split",$arrayHolidayHours)?$arrayHolidayHours["holStart_split"]:[];
	$holEnd_split = array_key_exists("holEnd_split",$arrayHolidayHours)?$arrayHolidayHours["holEnd_split"]:[];
	for($i = 0; $i< count($arrayHolidayHours["holiday-date"]); $i++){	
		if($arrayHolidayHours["holiday-date"][$i] != ""){
			$hourArray["date"] = date("Y-m-d", strtotime($arrayHolidayHours["holiday-date"][$i]));
		$hourArray["isRegularHours"] = false;
		$type = $arrayHolidayHours["hours-type"][$i];
		switch ($type){
			case "SPLIT":
				$start = array_shift($holStart);
				$end = array_shift($holEnd);					
				$holStart_1 = array_shift($holStart_split);
				$holEnd_1 = array_shift($holEnd_split);
				if(($start != "") && ($end != "") && ($holStart_1 != "") && ($holEnd_1 != "")){
					$hourArray["hours"] = date("H:i", strtotime($start)).":".date("H:i", strtotime($end)).",".
										date("H:i", strtotime($holStart_1)).":".date("H:i", strtotime($holEnd_1));
				}
			break;
			case "ALWAYS_OPEN":
				$hourArray["hours"] = "0:00:23:59";
			break;
			case "CLOSED":
				$hourArray["hours"] = '';
			break;
			case "IS_REGULAR_HOURS":
				$hourArray["isRegularHours"] = true;
			break;
			default:
				$start = array_shift($holStart);
				$end = array_shift($holEnd);
				if(($start != "") && ($end != "")){
					$hourArray["hours"] = date("H:i", strtotime($start)).":".date("H:i", strtotime($end));

				}
				
			break;
		}
		$yextArrayHolHours[] = 	$hourArray;	
		}				
	}
	return $yextArrayHolHours;
	
}
/*function mapBusinessHours($values){

	$days = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
	$hours_map = ["mon_open"=>"","mon_close"=>"","mon_opt"=>"", "tue_open"=>"", "tue_close"=>"", "tue_opt"=>"", "wed_open"=>"","wed_close"=>"", "wed_opt"=>"",
			"thu_open"=>"","thu_close"=>"", "thu_opt"=>"","fri_open"=>"","fri_close"=>"", "fri_opt"=>"","sat_open"=>"","sat_close"=>"", "sat_opt"=>"",
			"sun_open"=>"","sun_close"=>"", "sun_opt"=>""];

	foreach ($days as $day){
		$abbr = strtolower(substr($day,0,3));
		#Open
		$hours_map[$abbr."_open"] = ($values[$abbr."_opt"] != "")? "00:00": $values[$abbr."_open"];
		//$hours_db[$abbr."_open"] = $current_data[0][$abbr."_open"];

		#Close
		$hours_map[$abbr."_close"] = ($values[$abbr."_opt"] != "")? "00:00": $values[$abbr."_close"];
		//$hours_db[$abbr."_close"] = $current_data[0][$abbr."_close"];
		#Opt
		$hours_map[$abbr."_opt"] = $values[$abbr."_opt"];
		//$hours_db[$abbr."_opt"] = $current_data[0][$abbr."_opt"];
	}
	return $hours_map;

}*/

function mapBusinessHours($values){

		$days = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
	 	$hours_map = ["mon_open"=>"","mon_close"=>"","mon_opt"=>"", "tue_open"=>"", "tue_close"=>"", "tue_opt"=>"", "wed_open"=>"","wed_close"=>"", "wed_opt"=>"","thu_open"=>"","thu_close"=>"", "thu_opt"=>"","fri_open"=>"","fri_close"=>"", "fri_opt"=>"","sat_open"=>"","sat_close"=>"", "sat_opt"=>"","sun_open"=>"","sun_close"=>"", "sun_opt"=>""];

	 	foreach ($days as $day){
        	$abbr = strtolower(substr($day,0,3));
        	#Open
        	$hours_map[$abbr."_open"] = ($values[$abbr."_opt"] != "")? "00:00": $values[$abbr."_open"];
        	//$hours_db[$abbr."_open"] = $current_data[0][$abbr."_open"];

        	#Close
    		$hours_map[$abbr."_close"] = ($values[$abbr."_opt"] != "")? "00:00": $values[$abbr."_close"];
        	//$hours_db[$abbr."_close"] = $current_data[0][$abbr."_close"];
        	#Opt
        	if (isset($values[$abbr."_opt"])) {
        		$hours_map[$abbr."_opt"] = $values[$abbr."_opt"];
        	}else{
        		$hours_map[$abbr."_opt"]="";
        	}
        	
        	//$hours_db[$abbr."_opt"] = $current_data[0][$abbr."_opt"];
        }
        return $hours_map;

}


/*
** @param $db Database Conection
** @param $storeid Store Id
** @param $post_id Post Id
** 
** @return True | False if exist or not in Opt Out 
**
*/
function isOptOutPost(&$db,$storeid,$post_id){
	$info=$db->where('storeid',$storeid)->where('id',$post_id)->getOne('social_media_local_posts_optout','optout');
	return (count($info)) ? true : false;
}

function create_notification($data, $emails_tokens=[]){

	global $db;
	
	if($data['user_type']=="user"){
		
		$db->where ("id", $_SESSION['user_id']);
		$token = $db->getOne ("storelogin", 'token');
		
		$id = $db->insert ('notifications', $data);
		$cols = array ("email_notification", "email","notifications");
		$db->where ("storeid", $data['storeid']);
		$location = $db->getOne ("locationlist", null, $cols);
		
		if ($db->count > 0){
			if(!empty($location['notifications']) && $location['notifications'] == "1"){
				//send email to user
				if(!empty($location['email_notification']))
					$to = $location['email_notification'];
				else
					$to = $location['email'];
				
				$subject = "You received a notification!";
				$template = get_email_header();
				$template .= '<tr>
								<td>
									<table cellpadding="0" cellspacing="0" align="center" width="600" bgcolor="#f3f2f2">
										<tr>
											<td colspan="3" height="40">&nbsp;</td>
										</tr>
										<tr>
											<td colspan="3" align="center"><img src="'.getFullUrl().'/img/notification.png" width="328" height="40" alt="You received a notification" /></td>			
										</tr>
										<tr>
											<td colspan="3">&nbsp;</td>
										</tr>
										<tr>
											<td width="30"></td>
											<td width="540" align="center"><span style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22px;text-align:center;">'.$data['message'].'</span></td>
											<td width="30"></td>
										</tr>
										<tr>
											<td colspan="3">&nbsp;</td>
										</tr>
										<tr>
											<td colspan="3" align="center"><a href="'.getFullUrl().'xt_login.php?token='.$token.'&url='.$data["link"].'" target="_blank"><img src="'.getFullUrl().'/img/login-btn.jpg" width="102" height="40" alt="Reset Password" title="Reset Password" /></a></td>			
										</tr>
										<tr>
											<td colspan="3" height="40">&nbsp;</td>
										</tr>
									</table>
								</td>
							</tr>';
				$template .= get_email_footer();

				$headers = "MIME-Version: 1.0" . "\r\n";
				$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
				$headers .= 'From: <noreply@das-group.com>' . "\r\n";

				//mail("jessicas@das-group.com",$subject,$template,$headers);
				mail($to,$subject,$template,$headers);

			}
		}
	}else{
		$id = $db->insert ('notifications', $data);
		
		//send email to admin
		if (!empty($emails_tokens)) {
			
			foreach ($emails_tokens as $value){
				
				$to = $value['to'];
				$token = $value['token'];
				
				$subject = "You received a notification!";
				$template = get_email_header();
				$template .= '<tr>
								<td>
									<table cellpadding="0" cellspacing="0" align="center" width="600" bgcolor="#f3f2f2">
										<tr>
											<td colspan="3" height="40">&nbsp;</td>
										</tr>
										<tr>
											<td colspan="3" align="center"><img src="'.getFullUrl().'/img/notification.png" width="328" height="40" alt="You received a notification" /></td>			
										</tr>
										<tr>
											<td colspan="3">&nbsp;</td>
										</tr>
										<tr>
											<td width="30"></td>
											<td width="540" align="center"><span style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22px;text-align:center;">'.$data['message'].'</span></td>
											<td width="30"></td>
										</tr>
										<tr>
											<td colspan="3">&nbsp;</td>
										</tr>
										<tr>
											<td colspan="3" align="center"><a href="'.getFullUrl().'/xt_login.php?token='.$token.'&url='.$data["link"].'" target="_blank"><img src="'.getFullUrl().'/img/login-btn.jpg" width="102" height="40" alt="Login" title="Login" /></a></td>			
										</tr>
										<tr>
											<td colspan="3" height="40">&nbsp;</td>
										</tr>
									</table>
								</td>
							</tr>';
				$template .= get_email_footer();

				$headers = "MIME-Version: 1.0" . "\r\n";
				$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
				$headers .= 'From: <noreply@das-group.com>' . "\r\n";

				//mail("jessicas@das-group.com",$subject,$template,$headers);
				mail($to,$subject,$template,$headers);
			}

		}

	}
}

function get_email_header(){
	$header = '<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Email Template</title>

	<style>
		body {
			width: 100%;
			height: 100%;
			margin: 0;
			padding: 0;
		}
	</style>	
</head>

<body>
	<table cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" width="100%" align="center" style="margin: 0 auto;">
		<tr>
			<td>
				<table cellpadding="0" cellspacing="0" align="center" width="600" bgcolor="#0067b1">
					<tr>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td align="center"><img src="'.getFullUrl().'/img/FP-logo-white.png" alt="Local Fully Promoted Logo" /></td>			
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
				</table>
			</td>
		</tr>';
	return $header;
}
function get_email_footer(){
	$footer = '<tr>
			<td>
				<table cellpadding="0" cellspacing="0" align="center" width="600" bgcolor="#333333">
					<tr>
						<td height="40" align="center"><span style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; line-height: 22px; color: #ffffff;">Copyright &copy; <?php echo date("Y");?>Fully Promoted. All rights reserved.</span></td>			
					</tr>
				</table>
			</td>
		</tr>
	</table>
</body>
</html>';
	return $footer;
}

function mysql_escape_mimic($inp) { 
    if(is_array($inp)) 
        return array_map(__METHOD__, $inp); 

    if(!empty($inp) && is_string($inp)) { 
        return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $inp); 
    } 

    return $inp; 
} 

function pageRedirect($msg = "", $type, $url){
	
	if(!empty($msg)){
		if($type == "success")
			$_SESSION['success'] = $msg;
		elseif($type == "error")
			$_SESSION['error'] = $msg;
	}
	
	header("location:".$url);
	exit;
}

function getCardBrandElement($brand){
	
	$config =array(
					"American Express"=>array(
							'icon'=>'fa-cc-amex'
											 ),
					 "Diners Club"=>array(
							'icon'=>'fa-cc-diners-club'
					 ),
					 "Discover"=>array(
							'icon'=>'fa-cc-discover'
					),
					 "JCB"=>array(
							'icon'=>'fa-cc-jcb'
					 ),
					 "MasterCard"=>array(
							'icon'=>'fa-cc-mastercard'
					),
					 "UnionPay"=>array(
							'icon'=>'fa-cc-amex'
					 ),
					 "Visa"=>array(
							'icon'=>'fa-cc-visa'
					 ),
					 "Unknown"=>array(
							'icon'=>'fa-credit-card'
					 )
				  );
		
		if(isset($config[$brand]))
			return $config[$brand];
		return [];
}

function dateDiff($date1, $date2){
	
	$response = '';
	
	$datetime1 = new DateTime($date1);
	$datetime2 = new DateTime($date2);
	
	$interval = $datetime1->diff($datetime2);
	
	return $interval->format('%a');
}

###################### Role access system functions ###########################

//Returns true if the user role has the requested permission
function roleHasPermission($required_permission, $role_permissions){
	return in_array($required_permission, $role_permissions);
}

function getUserRoles($user_role, $requested_tab=''){
	
	global $db;
	
	$roles = array();
	
	if($user_role == 'admin_root'){
		$sql_user_roles ="SELECT * FROM ".$_SESSION['database'].".user_roles ORDER BY name";
	}
	elseif($user_role == 'admin_rep'){
		
		if (($requested_tab == "tab_rep_store_users") || ($requested_tab == "tab_user_form_update"))
			$sql_user_roles ="SELECT * FROM ".$_SESSION['database'].".user_roles WHERE name IN ('store_user') ORDER BY name";
		elseif($requested_tab == "tab_rep_admin_users")
			$sql_user_roles ="SELECT * FROM ".$_SESSION['database'].".user_roles WHERE name IN ('admin_rep') ORDER BY name";
		else
			$sql_user_roles ="SELECT * FROM ".$_SESSION['database'].".user_roles WHERE name IN ('admin_rep','store_user') ORDER BY name";
	}
	
	$row_user_roles = $db->rawQuery($sql_user_roles);
	
	if($db->count>0){
		foreach($row_user_roles as $user_role){
			$roles[$user_role['id']] = $user_role['name'];
		}
	}
	
	return $roles;
}

function checkToken($table, $token){
	global $db;
	
	$db->where("token",$token);
	$db->getOne($table, 'token');
	
	if($db->count>0){
		$new_token = getToken();
		return checkToken($table, $new_token);
	}
	return $token;
}

function getToken(){
	global $db;
	
	$sql_uuid = "SELECT replace(uuid(),'-','') as token;";
	$row_uuid = $db->rawQuery ($sql_uuid);
	$token_db = $row_uuid[0]["token"];
	
	return $token_db;
}

function randomPassword() {
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
	$passLength = 16;
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < $passLength; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}

//Returns something like https://localfullypromoted.com/
function getFullUrl(){
	$link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 
                "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
				
	return $link;
}

###################### End Role access system functions ###########################

function base64ToImage($base64_string, $output_file) {
    $file = fopen($output_file, "wb");

    $data = explode(',', $base64_string);

    fwrite($file, base64_decode($data[1]));
    fclose($file);

    return $output_file;
}

function reArrayFiles(&$file_post) {

    $file_ary = array();
    $file_count = count($file_post['name']);
    $file_keys = array_keys($file_post);

    for ($i=0; $i<$file_count; $i++) {
        foreach ($file_keys as $key) {
            $file_ary[$i][$key] = $file_post[$key][$i];
        }
    }

    return $file_ary;
}

/**
 *
 * Returns an array of valid or invalid emails
 *
 * @param    array  $emails The array to check
 * @param    string  $type "valid" or "invalid"
 * @return      array
 *
 */
function get_emails_list($emails, $type){
	
	$valid_emails = array();
	$invalid_emails = array();
	
	foreach($emails as $email){
		
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			array_push($invalid_emails, $email);
		}else{
			array_push($valid_emails, $email);
		}
	}

	if($type == "valid"){
		return $valid_emails;
	}

	if($type == "invalid"){
		return $invalid_emails;
	}
}

function getStateName($abbr, $flip=false){
	$states = array(
		'AL'=>'Alabama',
		'AK'=>'Alaska',
		'AZ'=>'Arizona',
		'AR'=>'Arkansas',
		'CA'=>'California',
		'CO'=>'Colorado',
		'CT'=>'Connecticut',
		'DE'=>'Delaware',
		'DC'=>'District of Columbia',
		'FL'=>'Florida',
		'GA'=>'Georgia',
		'HI'=>'Hawaii',
		'ID'=>'Idaho',
		'IL'=>'Illinois',
		'IN'=>'Indiana',
		'IA'=>'Iowa',
		'KS'=>'Kansas',
		'KY'=>'Kentucky',
		'LA'=>'Louisiana',
		'ME'=>'Maine',
		'MD'=>'Maryland',
		'MA'=>'Massachusetts',
		'MI'=>'Michigan',
		'MN'=>'Minnesota',
		'MS'=>'Mississippi',
		'MO'=>'Missouri',
		'MT'=>'Montana',
		'NE'=>'Nebraska',
		'NV'=>'Nevada',
		'NH'=>'New Hampshire',
		'NJ'=>'New Jersey',
		'NM'=>'New Mexico',
		'NY'=>'New York',
		'NC'=>'North Carolina',
		'ND'=>'North Dakota',
		'OH'=>'Ohio',
		'OK'=>'Oklahoma',
		'OR'=>'Oregon',
		'PA'=>'Pennsylvania',
		'RI'=>'Rhode Island',
		'SC'=>'South Carolina',
		'SD'=>'South Dakota',
		'TN'=>'Tennessee',
		'TX'=>'Texas',
		'UT'=>'Utah',
		'VT'=>'Vermont',
		'VA'=>'Virginia',
		'WA'=>'Washington',
		'WV'=>'West Virginia',
		'WI'=>'Wisconsin',
		'WY'=>'Wyoming',
	);
	if($flip){
		$states = array_flip($states);
		$value=ucfirst($abbr);
	}else{
		$value=strtoupper($abbr);
	}
	
	
	return $states[$value];
}

//It is used on /admin/campaign-stats/campaign-data.php
function getadcost($clientnum,$campid,$sdate, $edate, $leads=0){
	global $db;

	$sdate1 = new DateTime($sdate);
	$edate1 = $sdate1->diff(new DateTime($edate));
	$days = $edate1->days;
	$comm = 0;
	$comm = getadcomm($clientnum,$campid,$sdate,$edate,$leads);
	if($campid <> "") $adcost = 0;
	//$db->setTrace(true);
	$campaigncosts = $db->rawQuery("select * from advtrack.adcost where client='".$clientnum."' and campid='".$campid."' and (('".$sdate."' between start and end or '".$edate."' between start and end) or (start >= '".$sdate."' and end <= '".$edate."'))");
	//print_r($db->trace);
	foreach($campaigncosts as $campaigncost){
		$tsdate = $campaigncost['start'];
		$tedate = $campaigncost['end'];
		if($tsdate < $sdate) $tsdate = $sdate;
		if($tedate > $edate || $tedate == '0000-00-00') $tedate = $edate;
		$tsdate1 = new DateTime($tsdate);
		$tedate1 = $tsdate1->diff(new DateTime($tedate));
		$tdays = ($tedate1->days) + 1;
		$cost = $campaigncost['cost'];
		$cost = $cost + ($cost * ($comm / 100));
		$rate = $rate + ($cost * $tdays);	
	}
	$adcost = $rate;
	if ($leads > 0) $adcost = $rate / $leads;
	//echo $adcost;
	return $adcost;
	
}

function getadcomm($clientnum,$campid,$sdate, $edate, $leads = 0){
	global $db;
	
	$sdate1 = new DateTime($sdate);
	$edate1 = $sdate1->diff(new DateTime($edate));
	$days = $edate1->days + 1;
	//$db->setTrace(true);
	$commissions = $db->rawQuery("select * from advtrack.billingparams where client='" .$clientnum. "' and campid='" .$campid. "' and (('".$sdate."' between start and end or '".$edate."' between start and end) or (start >= '".$sdate."' and end <= '".$edate."'))");
	//if($campid == '1') print_r($db->trace);
	$adcomm = 0;
	
	foreach($commissions as $commission){ 
		$tsdate = $commission['start'];
		$tedate = $commission['end'];
		if($tsdate < $sdate) $tsdate = $sdate;
		if($tedate > $edate || $tedate == '0000-00-00') $tedate = $edate;
		$tsdate1 = new DateTime($tsdate);
		$tedate1 = $tsdate1->diff(new DateTime($tedate));
		$tdays = ($tedate1->days) + 1;
		$cost = $commission['showcommissionfee'];
		$rate = $rate + ($cost * $tdays);	
	}
	$adcomm =$rate / $days;
	return $adcomm;
}
function format_phone($number) {
  // Allow only Digits, remove all other characters.
  $number = preg_replace("/[^\d]/","",$number);

  // get number length.
  $length = strlen($number);

 // if number = 10
 if($length == 10) {
  $number = preg_replace("/^1?(\d{3})(\d{3})(\d{4})$/", "$1-$2-$3", $number);
 }

  return $number;
}

function getCompany($ip_address){
	$ch = curl_init();
	$URL = 'https://api.kickfire.com/v2/company:(all)?ip='.$ip_address.'&key=e9f8b3bee1dd0e20';
	curl_setopt($ch, CURLOPT_URL,$URL);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_FAILONERROR,1);
	$result = curl_exec($ch);
	$obj = json_decode($result);
	$isISP = $obj->{'data'}[0]->{'isISP'};
	if($isISP){
		$company = 'Not available';
	}else{
		$company = $obj->{'data'}[0]->{'name'};
	}
	curl_close ($ch);
	return $company;
}

function getPortal($clientid,$campid){
	global $db;
	
	$sql = "SELECT * FROM advtrack.campid WHERE client='".$clientid."' AND campid='".$campid."'";
	
	$portal = $db->rawQueryOne($sql);
	
	if ($db->count > 0){
		if ($portal['name'] == 'None'){ return 'Organic';}else{ return $portal['name'];}
	}else
		return $campid;
}

function array_insert (&$array, $position, $insert_array) { 
  $first_array = array_splice ($array, 0, $position); 
  $array = array_merge ($first_array, $insert_array, $array); 
}

function getrecordurl($callid,$vendorid){
	$url = 'https://api.logmycalls.com/services/getCallDetails?criteria%5Bouid%5D=' . $vendorid . '&criteria%5Bid%5D=' . $callid .'&api_key=97b8877dc5accc92ac7b0d0c1059aa87&api_secret=%241%24UDVLmdnQ%24qt25eAYjpvtTnHynWOOfd.&sort_by=id&sort_order=asc';
	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	$results = curl_exec($ch);
	$json_a = json_decode($results,true);
	$callurl = $json_a[results][0][file_url];
	curl_close($ch);
	return $callurl;
}

function getStoreRep(&$db = false){
	if(!$db)
		include ($_SERVER['DOCUMENT_ROOT'].'/connect_MysqliDb.php');
	
	$repsStoresNumber = $db->rawQueryOne ("SELECT loc.rep, COUNT(loc.storeid) AS number_of_stores FROM locationlist loc, reps rep  WHERE loc.rep != '' AND loc.rep = rep.id GROUP BY loc.rep ORDER BY number_of_stores ASC LIMIT 1;");
	$rep_id = isset($repsStoresNumber['rep'])? $repsStoresNumber['rep'] : '';
	
	return $rep_id;
}

/*
** @param $storeid Store Id
** @return array("data" => array("to" => $email, "token" => $token))
*/
function getRepUserInfo($store_id){
	global $db;
	$result = array();
	$to = '';
	$token = '';
	
	//Selects the email and email_notification from the representative of the selected storeid 
	$sql_rep_users = "SELECT strl.email, strl.email_notification, strl.token FROM ".$_SESSION['database'].".storelogin strl, ".$_SESSION['database'].".reps rep, ".$_SESSION['database'].".locationlist loc WHERE strl.email = rep.email AND rep.id = loc.rep AND loc.storeid = '".$store_id."'";
	$rep_users = $db->rawQueryOne($sql_rep_users);
	
	//If the rep users have at least one email, it will store them. 
	if (!empty($rep_users)){
		$token = $rep_users['token'];
		
		//Gets the email from the rep
		if(!empty($rep_users['email_notification'])){
			$to = $rep_users['email_notification'];
			$result['data'] = array("to"=>$to, "token"=>$token);
		}elseif(!empty($rep_users['email']) && filter_var($rep_users['email'], FILTER_VALIDATE_EMAIL)){
			$to = $rep_users['email'];
			$result['data'] = array("to"=>$to, "token"=>$token);
		}
	}
	
	return $result;
}

function clean($string) {
   $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
   return strtolower(preg_replace('/[^A-Za-z0-9\-]/', '', $string)); // Removes special chars.
}

/*
** @param $user_storeids string separated by comma E.g (90018,2)
** @return array with the active locations assigned to a user
*/
function get_active_locations($user_storeids){
	global $db;
	
	$inactive_loc_new_format = array();
	$all_locations = explode(',',$user_storeids);
	$inactive_locations = $db->get('inactive_locations',null,'storeid');
	
	foreach($inactive_locations as $loc){
		$inactive_loc_new_format[] = $loc['storeid'];
	}
	
	return $result = array_diff($all_locations, $inactive_loc_new_format);
}

function getActiveCampaigns($storeid){
	global $db;
	return $db->where("client","9018-".$storeid)->getValue("advtrack.campid_data","count(*)");
}

function replace_characters($text, $valueToReplace){
	$text = str_replace('\n',$valueToReplace, $text);
	$text = str_replace('\r',$valueToReplace, $text);
	$text = str_replace('\\r\\n',$valueToReplace, $text);
	$text = str_replace('\r\n',$valueToReplace, $text);
	$text = str_replace('\\R\\N',$valueToReplace, $text);
	$text = str_replace('\R\N',$valueToReplace, $text);
	$text = str_replace('/\r\\n',$valueToReplace, $text);
	$text = str_replace('/r/n',$valueToReplace, $text);
	$text = str_replace('/\R\\N',$valueToReplace, $text);
	$text = str_replace('/R/N',$valueToReplace, $text);
	
	return $text;
}

function convertDateTime($db,$zip,$date = 'NOW',$format = 'Y-m-d H:i:s'){
	$zipInfo = getZipCodeInformation($db,$zip);

	$date = new DateTime($date);
	if( $zipInfo ){
	   $subHrs = ( isset($zipInfo['utc']) ) ? ( int )$zipInfo['utc'] * ( -1 ) : 0 ;
	   if( isDaylightTime() ){
	   		$subHrs -=1;
	   }
	   $date = $date->sub( new DateInterval("PT".$subHrs."H"));
	}

	return $date->format( $format );
}

function getZipCodeInformation($db,$zip){
	$zip_info= $db->where('zipcode',$zip)
					   ->where('ziptype','P')
					   ->getOne('rates.zipcodeworld2','cityname,stateabbr,utc');

	if (isset($zip_info['cityname'])){
		return $zip_info;
	}
	return false;
}

function isDaylightTime(){
	$date = new DateTime();
	return $date->format('I');
}