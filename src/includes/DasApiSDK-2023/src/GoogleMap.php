<?php

namespace Das;

use Das\CallCurl;

class GoogleMap extends CallCurl
{

	function __construct($token)
	{
		 parent::__construct($token);
	}

	function getAddressInfo($params){
		$url = "/gm/addressinfo/";		
		return $this->call('GET', $url, $params);
	}
}
?>
