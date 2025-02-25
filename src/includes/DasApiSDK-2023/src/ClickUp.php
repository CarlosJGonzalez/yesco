<?php

namespace Das;

use Das\CallCurl;

class ClickUp extends CallCurl
{

	function __construct($token)
	{
		 parent::__construct($token);
	}

	function createTask($params){
		$url = '/clickup/create';
		return $this->call('POST', $url, $params );
	}
}
?>
