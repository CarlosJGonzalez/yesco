<?php
include __DIR__."/includes/connect.php";
set_time_limit(0);

$data = $db->orderBy("date","desc")->where("date > '2020-01-01'")->where("ip_country",null,"IS")->where("ip_address",null,"IS NOT")->get("form_data");
foreach($data as $d){
	$ip = explode(",",$d['ip_address']);
	echo $ip[0];
	$details = getLocationInfoByIp($ip[0]);
	echo "<pre>";var_dump($details);echo "</pre>";
	$db->where("id",$d['id'])->update("form_data",array("ip_country"=>$details['country'],"ip_city"=>$details['city'],"ip_state"=>$details['state']));
//	$db->where("id",$d['id'])->update("form_data",array("ip_country"=>$details['country_code'],"ip_city"=>$details['city'],"ip_state"=>$details['region_code']));
}
function getLocationInfoByIp($ip = null){
	if(is_null($ip)){
		$client  = @$_SERVER['HTTP_CLIENT_IP'];
		$forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
		$remote  = @$_SERVER['REMOTE_ADDR'];
		$result  = array('country'=>'', 'state'=>'','city'=>'');
		if(filter_var($client, FILTER_VALIDATE_IP)){
			$ip = $client;
		}elseif(filter_var($forward, FILTER_VALIDATE_IP)){
			$ip = $forward;
		}else{
			$ip = $remote;
		}
	}
	$ip_data = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".$ip));    
	if($ip_data && $ip_data->geoplugin_countryName != null){
		$result['country'] = $ip_data->geoplugin_countryCode;
		$result['city'] = $ip_data->geoplugin_city;
		$result['state'] = $ip_data->geoplugin_regionCode;
	}
	
//	$url .= 'http://api.ipstack.com/'.$ip.'?access_key=710603f35d4b0a6b4e69fea25264fc0f';
//
//
//	$ch = curl_init();
////	curl_setopt($ch, CURLOPT_HTTPHEADER, array($authorization )); 
//	curl_setopt($ch, CURLOPT_URL,$url);
//	curl_setopt($ch, CURLOPT_TIMEOUT, 30); 
//	curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
////		curl_setopt($ch, CURLOPT_FAILONERROR,1);
//	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
//
//	$result = curl_exec($ch);
////		echo"<pre>";var_dump($result);echo"</pre>";
////		echo $result;
//	curl_close($ch);
//
//	$result = json_decode($result, true);


	return $result;
}