<?php

namespace Das;

use Das\CallCurl;

class DasLead extends CallCurl
{

	function __construct($token)
	{
		 parent::__construct($token);
	}

	function create($client,$params){
		$url = '/das/lead/'.$client;

		return $this->call('POST', $url, $params );
	}
}
?>