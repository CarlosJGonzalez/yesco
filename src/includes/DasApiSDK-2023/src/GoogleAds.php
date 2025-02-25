<?php

namespace Das;

use Das\CallCurl;

class GoogleAds extends CallCurl
{

	function __construct($token)
	{
		 parent::__construct($token);
	}
	
	function getGoogleAdsByCampaignId($campaignid,$params = false){
		$url = "/googleads/$campaignid";
		
		return $this->call('GET', $url, $params);
	}

	function getGoogleAds($client,$storeid,$params = false){
		$url = "/googleads/$client/$storeid";
		
		return $this->call('GET', $url, $params);
	}

	function getCampaigns($client,$params = false){
		$url = "/googleads/campaigns/$client";	
		
		return $this->call('GET', $url, $params);
	}
}
?>
