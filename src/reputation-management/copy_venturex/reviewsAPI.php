<?php
	require_once __DIR__."/../facebook-ads-api/vendor/autoload.php";

	/*GMB includes*/
	require __DIR__.'/../google-api-php-client-2.1.3/vendor/autoload.php';
    require __DIR__.'/../google-api-php-client-2.1.3/mybusiness/mybusiness.php';
    require __DIR__.'/../google-api-php-client-2.1.3/vendor/google/auth/src/OAuth2.php';
	
	use FacebookAds\Api;

	
	class Config {
		private $fb_access_token;	
		private $fb_api_key;	
		private $fb_secret_token;
		private $fb_cert_path;
		private $maps_api_key;
		private $gmb_credentials_file;
		private $developer_token;
		private $application_name;
		private $client_id;
		
		
		public function __construct($config){
			
			foreach ($config as  $prop=>$value) {
				 $this->__set($prop,$value);
			}		
		}
		public function __get($property)
		{
			return property_exists($this,$property)?$this->{$property}:false;
		}
		public function __set($property, $value)
		{
			if(property_exists($this,$property)){
				$this->{$property} = $value;
			}
		}
	}
	
	class ReviewsAPI {
		private $conn;
		private $page_id;
		private $gmb_location;
		private $page_access_token;
		private $adsAPI;
		private $gmbAPI;
		private $config ;
		private $storeid ;
		private $gmb_client;
		public function __construct($storeid, Config $config, MysqliDb $conn) {		
			$this->conn = $conn;
			$this->config = $config; 
			$this->adsAPI =  $this->setFbAPI();
			$this->storeid = $storeid;
			$this->gmb_client = $this->getGMBCredentials();
			
			$reviews =    $this->gmb_client->accounts_locations_reviews;
			$name = "accounts/".$this->gmb_location["account_id"]."/locations/".$this->gmb_location["location_id"]."/reviews/AIe9_BFmRFRFwJGRfUDOW8jG3rXnl9IN6O8g57lwj9PQ_EZ_uxBylhAaXRNZIoDFYSGb7ds5R6zGrfFaYlvWRqB03tdhrSn5lCJ74diAXZHfR4B4OUHNN3A";
		
		}
		public function __get($property)
		{
			return property_exists($this,$property)?$this->{$property}:false;
		}
		public function __set($property, $value)
		{
			if(property_exists($this,$property)){
				$this->{$property} = $value;
			}
		}
		private function getAccessToken($extend_access_token){
			return $this->config->__get("fb_access_token");
		}
		private function setFbAPI(){			//print_r($this->config); exit;
			 Api::init($this->config->__get("fb_api_key"),
						$this->config->__get("fb_secret_token"),
						$this->config->__get("fb_access_token"))->getHttpClient()->setCaBundlePath($this->config->__get("fb_cert_path"));
			return Api::instance();
		}
	
		public function getGMBCredentials(){			
			$account = $this->conn->where("a.store_id", $this->storeid, "=")
								->where("a.client", $this->config->__get("client_id"), "=")
								->join("facebook_post.gmb_accounts b","a.parent_account=b.account_id","INNER")
						->get("facebook_post.gmb_locations a", 1, "a.*, b.refresh_token,b.email");
			
			$this->gmb_location = $account[0];
			
			$client = new Google_Client();
			$client->setApplicationName($this->config->__get("application_name"));
			$client->setDeveloperKey($this->config->__get("developer_token"));
			$client->setAuthConfig($this->config->__get("gmb_credentials_file"));  
			$client->setScopes("https://www.googleapis.com/auth/plus.business.manage");
			$client->setSubject($account[0]["email"]);   
			$token = $client->refreshToken($account[0]["refresh_token"]);
			$client->authorize();
			return new Google_Service_Mybusiness($client);
						
		}
		public function getGMBEmbedLink(){
			
			
		
		}
		private function getPlacesReviewsInfo(){
			
			$placeid = $this->conn->where("location_id", "", "=")
						->get("facebook_post.gmb_locations", 1, "place_id");
						
			$query = http_build_query(["placeid" => "{$canonicalName}",
                                    "key" => $this->sets->__get("maps_api_key")]);

			$agent = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:7.0.1) Gecko/20100101 Firefox/7.0.1";

			// google map geocode api url
			$url = "https://maps.googleapis.com/maps/api/place/details/json?".$query ; 
			echo "attempts : $attempts \n";
			echo $url."\n";
			
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
					 // get the json response
					 $resp_json = curl_exec($cURL);
			  }catch(Exception $ex){
				  print_r($ex );            
				  return false;
			  }   
				
			$code = curl_getinfo ($cURL, CURLINFO_HTTP_CODE);

			curl_close($cURL);
			echo "Code : $code \n";
		}
	
	}
	
	
