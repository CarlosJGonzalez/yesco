<?php

class Das_Website
{

	private $apiUsername;
	private $apiPassword;
	private $storeid;
	function __construct($storeid = null, $apiUsername = null, $apiPassword = null)
	{
		if(is_null($apiUsername))
			$this->apiUsername= "dasadmin";
		else
			$this->apiUsername= $apiUsername;
		
		if(is_null($apiPassword))
			$this->apiPassword= "8D5jzatbZk79sAZR";
		else
			$this->apiPassword= $apiPassword;
		
		$this->storeid= $storeid;
	}
	function getFormData($parameters = null){
		if(!is_null($this->storeid)) $parameters["storeid"] = $this->storeid;
		$url = 'https://fullypromoted.com/api/forms/read.php';
		return $this->call("GET",$url,json_encode($parameters));
	}
	
	private function call($method, $url, $json=""){	
		if( $json && $method == 'GET' )
			$url .= '?' . http_build_query(json_decode($json));

		$ch = curl_init($url);

		$authorization = "Authorization: Basic ".base64_encode($this->apiUsername.":".$this->apiPassword);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
			'Content-Type: application/json' , $authorization)                                                                       
		);
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
}
?>
