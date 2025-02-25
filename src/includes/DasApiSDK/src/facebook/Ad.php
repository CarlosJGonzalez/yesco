<?php

namespace Das\facebook;

use Das\CallCurl;

class Ad extends CallCurl
{

	function __construct($token)
	{
		 parent::__construct($token);
	}
	
	/*
	** Get Pixel Info by clients
	**
	**@param $client 
	**@param $storeid 
	**
	**return array
	*/
	function getPixel($client,$storeid = null){
		$url = "/fb/ads/$client";

		if ( isset($storeid) ) {
			$url = $url.'/'.$storeid;
		}

		$url = $url.'/pixel';
		return $this->call('GET', $url );
	}

	function boostPost($params,$client,$storeid = null){
		$url = "/fb/ads/boostpost/$client";

		if ( isset($storeid) ) {
			$url = $url.'/'.$storeid;
		}

		return $this->call('POST', $url,$params );
	}

	function deleteCampaign( $campaignId, $client, $storeid = null ){
		$url = "/fb/ads/$client";

		if ( isset($storeid) ) {
			$url = $url.'/'.$storeid;
		}
		$url = $url.'/campaign/'.$campaignId;

		return $this->call('DELETE', $url);
	}

	function boostImage($params,$client,$storeid = null){
		$url = "/fb/ads/boostimage/$client";

		if ( isset($storeid) ) {
			$url = $url.'/'.$storeid;
		}

		return $this->call('POST', $url,$params );
	}
}
?>
