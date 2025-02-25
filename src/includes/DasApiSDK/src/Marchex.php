<?php

namespace Das;

use Das\CallCurl;

class Marchex extends CallCurl
{

	function __construct($token)
	{
		 parent::__construct($token);
	}

	function getRecording( $callId ){
		$url = "/marchex/call/$callId/recording";
		
		return $this->call('GET', $url );
	}
}
?>
