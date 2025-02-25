<?php

namespace Das;

use Das\CallCurl;

class GoogleAnalytic extends CallCurl
{

	function __construct($token)
	{
		 parent::__construct($token);
	}

	function createMeasurProtocol($params){

		$url = '/ga/measurprotocol';
		return $this->call('POST', $url, $params );
	}
}
?>
