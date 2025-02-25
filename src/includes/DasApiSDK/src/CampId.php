<?php

namespace Das;

use Das\CallCurl;

class CampId extends CallCurl
{

	function __construct($token)
	{
		 parent::__construct($token);
	}

	function create( $params ){
		$url = "campid";

		return $this->call('POST', $url,$params );
	}
	
	function getUrl( $id ){
		$url = "campid/$id/url";
		return $this->call('GET', $url );
	}

	function delete($id){
		$url = "campid/$id";

		return $this->call('DELETE', $url );
	}

	function getCampIdInfo($id){
		$url = "campid/$id";

		return $this->call('GET', $url );
	}

	function getCampIdByStoreId( $client,$storeid = null,$params = false ){
		$url = 'campid/search/'.$client;

		if ( isset($storeid) ) {
			$url = $url.'/'.$storeid;
		}

		return $this->call('GET', $url, $params );
	}
	
	function getCampId($campid,$client,$storeid = null){
		$url = "campid/search/$campid/client/$client";

		if ( isset($storeid) ) {
			return $this->call('GET', $url.'/'.$storeid );
		}
		return $this->call('GET', $url );
	}

	function getDebits($client,$storeid = null,$params = false){
		if (isset($storeid)) {
			$url = "/campid/debit/$client/$storeid";
		}else{
			$url = "/campid/debit/$client";
		}		
		return $this->call('GET', $url, $params);
	}

	function getCredits($client,$storeid = null,$params = false){
		if (isset($storeid)) {
			$url = "/campid/credit/$client/$storeid";
		}else{
			$url = "/campid/credit/$client";
		}		
		return $this->call('GET', $url, $params);
	}
}
?>
