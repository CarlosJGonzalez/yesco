<?php 
require ($_SERVER['DOCUMENT_ROOT']."/includes/DasApiSDK/vendor/autoload.php");
use Das\Review;

class Das_Review
{
	private $db;
	private $client;
	private $storeid = null;
	private $client_storeid;
	private $review;
	
	public function __construct($db,$token,$client,$storeid = null)
	{
		$this->db = $db;
		$this->client = $client;		
		$this->review = new Review($token);

		if( !isset($storeid) ){
			$this->client_storeid = $this->client;
		}else{
			$this->client_storeid = $this->client.'-'.$this->storeid;
			$this->storeid = $storeid;
		}
	}

	function getReviews($parameters = false){
		$reviews = $this->review->getReviews($this->client,$this->storeid,$parameters);
		return $reviews;
	}

	function getReviewsStats($parameters = false){
		$reviews = $this->review->getReviewsStats($this->client,$this->storeid,$parameters);
		return $reviews;
	}

	function getGMBReviewsLink(){

		$g=$this->db->where('storeid',$this->storeid)
			 	 ->getOne('locationlist',"displayname,address,city,state,zip");

	 	if(isset($g['displayname']) && $g['displayname'] != '' ){
	 		$place_id=$this->db->where('store_id',$this->storeid)
				 ->where('client',$this->client)->where("place_id <> ''")
			 	 ->getOne('facebook_post.gmb_locations',"place_id");

		 	if(isset($place_id['place_id']) && $place_id['place_id'] != '' ){
		 		$businessName = join(" ",array_merge([$g["displayname"]],[$g["address"]], [$g["city"]],[$g["state"]],[$g["zip"]]));	
		 		$placeId = $place_id['place_id'];
		 	}else{
		 		return '';
		 	}
	 	}else{
	 		return '';
	 	}

		$maps_cid_structure = "https://maps.google.com/?cid=";
		$default_url = "https://www.google.com/search?ludocid=&q=".rawurlencode($businessName);
		$review_url= "https://www.google.com/search?q=".rawurlencode($businessName);
		/*Signarama Project API Key*/
		$maps_api = "AIzaSyA6OmvG-XyCVw7MyCUOW6qNABkc21kslmA";
		$url = "https://maps.googleapis.com/maps/api/place/details/json?placeid=$placeId&key=$maps_api";
		$agent = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:7.0.1) Gecko/20100101 Firefox/7.0.1";
		$cURL = curl_init();
		curl_setopt($cURL, CURLOPT_URL, $url);
		curl_setopt($cURL, CURLOPT_HTTPGET, true);
		curl_setopt($cURL, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Accept: application/json'
		));
		curl_setopt($cURL,CURLOPT_USERAGENT,$agent);
		curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
		
		try{					
			$resp_json = curl_exec($cURL);				
		}catch(Exception $ex){			     
		 	return $default_url;
		}

		if(curl_errno($cURL)){
			return $default_url ;
		}
		$place_details = json_decode($resp_json, true);
		
		if($place_details["status"] != "OK")
			return $default_url ;
		$cid = str_replace($maps_cid_structure,"",$place_details["result"]["url"]);
		
		if(!is_numeric($cid))
			return $default_url ;
		
		$hex_cid = $this->my_base_convert($cid);

		return $review_url."&lucid=$cid#lrd=0x0:0x$hex_cid,1";		
	}


	function getLinkGoogle(){
		$g=$this->db->where('store_id',$this->storeid)
				 ->where('client',$this->client)->where("place_id <> ''")
			 	 ->getOne('facebook_post.gmb_locations',"concat('https://search.google.com/local/writereview?placeid=',place_id) as url");
	 	return count($g) ? $g['url'] : '';
	}
	
	function getLinkFB(){
		$fb=$this->db->where('store_id',$this->storeid)
				 ->where('client',$this->client.'-'.$this->storeid)
			 	 ->getOne('facebook_post.fb_pages',"concat(link_page,'reviews/') as url");
	 	return count($fb) ? $fb['url'] : '';
	}

	/*function getReviews($parameters = null){
		$url = 'https://adjack.net/api/reviews/read1.php';
		$call = $this->call("GET",$url,json_encode($parameters));
		return $call;
	}*/

	private function call($method, $url, $json=""){	
		if( $json && $method == 'GET' )
			$url .= '?' . http_build_query(json_decode($json));

		$ch = curl_init($url);

		/*$authorization = "Authorization: Basic ".base64_encode($this->apiUsername.":".$this->apiPassword);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
			'Content-Type: application/json' , $authorization)                                                                       
		);*/

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		if($json && $method != 'GET'){
			curl_setopt($ch, CURLOPT_POSTFIELDS, $json);                                                                                                                 
		}

		$result = curl_exec($ch);
		curl_close($ch);
		$result = json_decode($result, true);

		if(is_array($result)){
			return $this->is_error($result);
		}

		return $result;		
	}

	private function is_error($data){
		if(isset($data['status']) && $data['status'] >= 400){
			$msg = $this->getErrorMsg($data);
			return array_merge($data,['is_error'=>1,'msg_error'=>$msg]);
		}

		return array_merge($data,['is_error'=>0,'msg_error'=>false]);
	}

	private function getErrorMsg($data){
		$msg='';
		if(isset($data['errors']) && count($data['errors'])){
			foreach ($data['errors'] as  $value) {
				$field = isset($value['field']) ? $value['field'] : '';
				$message = isset($value['message']) ? $value['message'] : '';
				$msg .= $field.':'.$message.'.';
			}
		}else{
			$msg = isset($data['detail']) ? $data['detail'] : '';
		}
		return $msg;
	}

	private function my_base_convert($numstring) {
		$orig = $numstring;
		$deci = "$orig";
		$hexa = "1";
		$pos = 1;
		$deln = strlen($orig);
		$ans = '';
		$valid = 1;
		for ($i=($deln-1); $i >= 0; $i--)
	     {
	          $ths = substr($orig,$i,1);
			  
	          if (strpos('0123456789', $ths) === false)
	          {
	               $valid = 0;
	          }
	     }	
	     if ($valid == 1)
	     {
	          while (strlen($hexa) <= $deln and $pos < 100)
	          {
				  $hexa = bcmul($hexa, "16");		 
				  $pos = $pos + 1;
	          }
	          $added_any=0;
	          do
	          {
	               $manyhexas = bcdiv($deci, $hexa,0);
					if (($manyhexas > 0) || ($added_any == 1))
	               {
	                    $added_any=1;
	                    $ans = $ans . base_convert($manyhexas,10,16);
	                    $deci = bcsub($deci, bcmul($manyhexas, $hexa));
	               }
	               $hexa = bcdiv($hexa,16,0);
	               $pos = $pos -1;
	          }
	          while ($pos > 0);
	      }
		
	  return  $ans;
	}
}
?>