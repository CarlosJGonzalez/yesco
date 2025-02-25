<?php

namespace Das;

use Das\CallCurl;

class GoogleAds extends CallCurl
{

	function __construct($token)
	{
		 parent::__construct($token);
	}
	
	function update( $account,$campaign,$params){		
		$url = "/googleads/$account/campaign/$campaign";
		
		return $this->call('PUT', $url,$params);
	}
	
	function getAccount( $client = null,$storeid = null ){
		$url = '/googleads/accounts';

		if( isset($client) ){
			$url = "/googleads/account/$client";
			if( isset($storeid) ){
				$url = "/googleads/account/$client/$storeid";
			}

		}		
		
		return $this->call('GET', $url);
	}

	function save(){
		$url = '/googleads/accounts';
		
		return $this->call('POST', $url);
	}
}
?>
