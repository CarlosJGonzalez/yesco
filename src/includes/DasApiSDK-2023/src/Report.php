<?php

namespace Das;

use Das\CallCurl;

class Report extends CallCurl
{

	function __construct($token)
	{
		 parent::__construct($token);
	}

	function getCampaignReport($client,$params){
		$url = "/report/$client/campaign";
		
		return $this->call('POST', $url, $params );
	}

	function getLeadReport($client,$params){
		$url = "/report/$client/lead";
		
		return $this->call('POST', $url, $params );
	}
	
	function getLoginReport($client,$params){
		$url = "/report/$client/login";
		
		return $this->call('POST', $url, $params );
	}
}
?>
