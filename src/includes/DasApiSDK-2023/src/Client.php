<?php

namespace Das;

use Das\CallCurl;

class Client extends CallCurl
{

	function __construct($token)
	{
		 parent::__construct($token);
	}
	
	function getLocationsDetails( $client,$params = null ){
		$url = "client/$client/locations/details";

		return $this->call('GET', $url,$params );
	}

	function updateLocation( $client,$storeid,$params ){
		$url = "client/$client/location/$storeid";

		return $this->call('POST', $url, $params );
	}

	function update($client,$params,$storeid = null){
		$url = "client/$client";

		if ( isset($storeid) ) {
			$url = $url.'/'.$storeid;
		}
		return $this->call('POST', $url, $params );
	}

	function create($params){
		$url = 'client';
		
		return $this->call('POST', $url, $params );
	}
	
	function action( $params ){
		$url = 'client/action';
		
		return $this->call('POST', $url, $params );
	}

	function delete($client,$storeid = null){
		$url = "client/$client";

		if ( isset($storeid) ) {
			$url = $url.'/'.$storeid;
		}
		return $this->call('DELETE', $url );
	}

	function getLocations( $client,$params = null ){
		$url = "locations/client/$client";

		return $this->call('GET', $url,$params );
	}
	
	function getClients( $params = null ){
		$url = "client";

		return $this->call('GET', $url,$params );
	}

	function getClient( $client, $storeid = null ){
		$url = "client/$client";		

		if ( isset($storeid) ) {
			$url = $url.'/'.$storeid;
		}
		return $this->call('GET', $url );
	}
	
	function getClientLogins( $client,$params ){
		$url = "client/$client/logins";		
		return $this->call('GET', $url,$params);
	}
	
	function getGAInfoByClient( $client, $storeid = null ){
		$url = "ga/$client";

		if ( isset($storeid) ) {
			$url = $url.'/'.$storeid;
		}
		return $this->call('GET', $url );
	}

	function addGaInfo( $client,$params ){
		$url = "ga/$client";
		
		return $this->call('POST', $url, $params );
	}

	function updateGaInfo( $client,$params ){
		$url = "ga/$client";
		
		return $this->call('PUT', $url, $params );
	}
}
?>
