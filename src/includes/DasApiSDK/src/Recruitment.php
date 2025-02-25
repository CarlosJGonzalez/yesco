<?php

namespace Das;

use Das\CallCurl;

class Recruitment extends CallCurl
{

	function __construct($token)
	{
		 parent::__construct($token);
	}

	function getJob($id){
		$url = "recruitment/job/$id";

		return $this->call('GET', $url );
	}

	function getJobs($client,$storeid = null,$params = ''){
		$url = 'recruitment/'.$client;

		if ( isset($storeid) ) {
			$url .= "/$storeid";
		}
		$url .= '/jobs';
		return $this->call('GET', $url,$params );
	}

	function saveJobPost( $params ){
		$url = 'recruitment/jobpost/save';
		return $this->call('POST', $url,$params );
	}
}
?>