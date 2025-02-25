<?php
class Das_ConstantContact {
	private $cc;
	private $token;
	private $version;
	private $url = 'https://api.cc.email/';

	function __construct($token, $version = 'v3')
	{
		//$this->apiKey= $apiKey;
		$this->token = $token;
		$this->version = $version;
	}

	/*
		Update or Create contact
		Info: https://v3.developer.constantcontact.com/api_reference/index.html#!/Contacts/createOrUpdateContact
	*/
	public function createUpdateContact($params){
		$endpoint = 'contacts/sign_up_form';

		return $this->call("POST",$endpoint,$params);
	}

	/*
		Get All or 1 List
		List $id no requierd
		Info: hhttps://v3.developer.constantcontact.com/api_reference/index.html#!/Contact_Lists/createList
	*/
	public function getLists( $id = null ){

		$endpoint = 'contact_lists';
		if( isset( $id ) ){
			$endpoint .= '/'.$id;
		}
		return $this->call("GET",$endpoint);
	}	


	/*
		Create a (contact) List
		Info: https://v3.developer.constantcontact.com/api_reference/index.html#!/Contact_Lists/createList
	*/
	public function addList( $params ){
		$endpoint = 'contact_lists';
		return $this->call( "POST", $endpoint, $params );
	}

	//Main CURL Call
	function call($verb, $endpoint, $params = false){
		$url = $this->url.'/'.$this->version.'/'.$endpoint; 		

		
		if( $params && $verb == 'GET' ){
			$url .= '&' . http_build_query($params);
		}
	
		$ch = curl_init($url);
		$httpheader = ['Content-Type: application/json',sprintf('Authorization: Bearer %s', $this->token)];
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $verb);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		if($params && $verb != 'GET'){
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
		}else{
			$httpheader[] = 'Content-Length: 0';
		}

		curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);

		$result = curl_exec($ch);

		$curl_info_response = curl_getinfo($ch);

		curl_close($ch);
		
		$result = json_decode($result, true);
		$result['response_status'] = $curl_info_response['http_code'];
		
		if(is_array($result)){
			return $this->is_error($result);
		}
		
		return $result;
	}

	private function is_error($data){

		if(isset($data['response_status']) && $data['response_status'] >= 400){
			$msg = $this->getErrorMsg($data[0]);

			return array_merge($data,['is_error'=>1,'error_info'=>$msg]);
		}

		return array_merge($data,['is_error'=>0,'msg_error'=>false]);
	}

	private function getErrorMsg($data){
		$msg = array();
		if(isset($data['error_key']) && isset($data['error_message']) && count($data['error_message'])){
			$msg['error_key'] = $data['error_key'];
			$msg['error_message'] = $data['error_message'];
		}
		
		return $msg;
	}

}
?>