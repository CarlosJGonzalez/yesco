<?php

namespace Das;

use Das\CallCurl;

class Call extends CallCurl
{

	function __construct($token)
	{
		 parent::__construct($token);
	}


	function getCalls( $client,$storeid = null,$params = null ){
		$url = "/call/$client";

		if ( isset($storeid) ) {
			$url .= "/$storeid";
		} 
		return $this->call( 'GET', $url, $params );
	}

	function getCallStats( $client,$storeid = null,$params = null ){
		$url = "/call/$client";

		if ( isset($storeid) ) {
			$url .= "/$storeid";
		}

		$url .= "/stats";

		return $this->call( 'GET', $url, $params );
	}


}
?>
