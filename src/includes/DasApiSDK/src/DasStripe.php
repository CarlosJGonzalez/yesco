<?php
namespace Das;

use Das\CallCurl;

class DasStripe extends CallCurl
{

	function __construct($token)
	{
		 parent::__construct($token);
	}

	function getCustomersByClientStoreId( $client,$storeid = null ){
		$url = "/stripe/customer/$client";
		if ( isset($storeid) ) {
			$url .= "/$storeid";
		}

		return $this->call('GET', $url);
	}

	function getCards($customerid){
		$url = "/stripe/$customerid/cards";
		
		return $this->call('GET', $url);
	}

	function createCharge($params){
		$url = "/stripe/charge";
		
		return $this->call('POST', $url, $params);
	}

	function createCustomer($params){
		$url = "/stripe/customer";
		
		return $this->call('POST', $url, $params);
	}

	function createCard($customerid,$token){
		$url = "/stripe/$customerid/card/$token";
		
		return $this->call('POST', $url);
	}

	function isDefaultCard($customerid,$cardId){
		$url = "/stripe/$customerid/card/$cardId";
		
		return $this->call('GET', $url);
	}

	function getCharges( $params ){
		$url = "/stripe/charges";
		
		return $this->call('GET', $url,$params);
	}

	function deleteCard( $customerid,$cardId ){
		$url = "/stripe/$customerid/card/$cardId";
		
		return $this->call('DELETE', $url);
	}

	function updateCard( $customerid,$cardId,$params ){
		$url = "/stripe/$customerid/card/$cardId";
		
		return $this->call( 'PUT', $url,$params );
	}
}
?>
