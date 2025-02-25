<?php

namespace Das;

use Das\CallCurl;

class CoreBridge extends CallCurl
{

	function __construct($token)
	{
		 parent::__construct($token);
	}

	function createLead($params){
		$url = '/corebridge/lead/';

		return $this->call('POST', $url, $params );
	}
}
?>
