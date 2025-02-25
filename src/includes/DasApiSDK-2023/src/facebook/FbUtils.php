<?php

namespace Das\facebook;

use Das\CallCurl;

class FbUtils extends CallCurl
{

	function __construct($token)
	{
		 parent::__construct($token);
	}
	
	/*
	** Get ReachEstimate
	**
	**@param $client 
	**@param $storeid 
	**
	**return array
	*/
	function getReachEstimate($params,$client,$storeid = null){
		$url = "/fb/reachestimate/$client";

		if ( isset($storeid) ) {
			$url = $url.'/'.$storeid;
		}
		return $this->call('GET', $url,$params );
	}

	/*
	** Get Targeting by Client/Storeid
	**
	**@param $client 
	**@param $storeid 
	**
	**return array
	*/
	function getTargeting($client,$storeid = null){
		$url = "/fb/targeting/$client";

		if ( isset($storeid) ) {
			$url = $url.'/'.$storeid;
		}
		return $this->call('GET', $url );
	}

}
?>
