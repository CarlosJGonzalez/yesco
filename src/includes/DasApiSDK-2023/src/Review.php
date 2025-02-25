<?php

namespace Das;

use Das\CallCurl;

class Review extends CallCurl
{

	function __construct($token)
	{
		 parent::__construct($token);
	}

	function getReviews($client,$storeid = null,$params = false){
		$url = "/review/$client/$storeid";
		
		return $this->call('GET', $url, $params);
	}

	function getReviewsStats($client,$storeid = null,$params = false){

		if (isset($storeid)) {
			$url = "/review/stats/$client/$storeid";
		}else{
			$url = "/review/stats/$client";
		}		
		return $this->call('GET', $url, $params);
	}
}
?>
