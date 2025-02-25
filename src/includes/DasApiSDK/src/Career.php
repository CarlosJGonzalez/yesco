<?php

namespace Das;

use Das\CallCurl;

class Career extends CallCurl
{

	function __construct($token)
	{
		 parent::__construct($token);
	}

	public function saveFbAdsInfo( $params ){
		$url = "/career/fbboostinfo";
		
		return $this->call('POST', $url, $params );
	}

	public function getFbAdsInfo( $job_id ){
		$url = "/career/fbboostinfo/$job_id";
		
		return $this->call('GET', $url );
	}

	function createJobApplication( $client,$params){
		$url = "/career/$client/job_application";
		
		return $this->call('POST', $url, $params );
	}

	function updateJob( $id,$params){
		$url = "/career/job/$id";
		
		return $this->call('POST', $url, $params );
	}

	function createJob( $client,$params){
		$url = "/career/$client/job";
		
		return $this->call('POST', $url, $params );
	}

	function getJob( $id ){
		$url = "career/job/$id";

		return $this->call('GET', $url );
	}

	function pageStatus($client,$storeid = null,$params = null ){
		$url = 'career/'.$client;

		if ( isset($storeid) ) {
			$url .= "/$storeid";
		}
		$url .= '/career_page';
		return $this->call('GET', $url,$params );
	}

	function getJobs($client,$storeid = null,$params = null ){
		$url = 'career/'.$client;

		if ( isset($storeid) ) {
			$url .= "/$storeid";
		}
		$url .= '/jobs';
		return $this->call('GET', $url,$params );
	}

	function getDefaultImage($client,$params = null ){
		$url = "/career/$client/default_image";

		return $this->call('GET', $url,$params );
	}

	function getJobType($params = null ){
		$url = '/career/job_type';

		return $this->call('GET', $url,$params );
	}
}
?>