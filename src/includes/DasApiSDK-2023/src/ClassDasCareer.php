<?php
require_once ($_SERVER['DOCUMENT_ROOT']."/includes/DasApiSDK-2023/vendor/autoload.php");
use Das\Career;

class Das_Career
{
	private $db;
	private $client;
	private $storeid;
	private $client_storeid;
	private $careerObj;

	function __construct( $db,$token,$client,$storeid = null ){
		$this->db = $db;
		$this->storeid = $storeid;
		$this->client  = $client;

		if( isset($this->storeid) ){
			$this->client_storeid = $this->client.'-'.$this->storeid;
		}else{
			$this->client_storeid = $this->client;
		}

		$this->careerObj = new Career($token);
	}

	function saveFbAdsInfo($params){
		return $this->careerObj->saveFbAdsInfo( $params );
	}

	function getFbAdsInfo( $job_id ){
		$job = $this->careerObj->getFbAdsInfo( $job_id );
		if( !$job['is_error'] && $job['info']['count'] > 0 ){
			$job =  $job['data'];
			return $job;
		}
		return false;
	}

	function updateJob( $job_id,$params ){

		$job = $this->careerObj->updateJob( $job_id,$params );		
		error_log(print_r($job,true));
		if( !$job['is_error'] && $job['info']['count'] > 0 ){
			$job =  $job['data'];
			return $job;
		}
		return false;
	}

	function createJob( $params ){

		if( isset($this->storeid) ){
			$params['storeid'] = $this->storeid;
		}

		$job = $this->careerObj->createJob( $this->client,$params );
		if( !$job['is_error'] && $job['info']['count'] > 0 ){
			$job =  $job['data'];
			return $job;
		}
		return false;
	}


	function getJob( $id ){
		$job = $this->careerObj->getJob( $id );
		if( !$job['is_error'] && $job['info']['count'] > 0 ){
			$job =  $job['data'];
			return $job;
		}
		return false;
	}

	function getJobsTemplate( $params = null ){

		$jobs = array();

		if ( isset($params) ) {
			$params = array_merge($params,array('template' => 1 ));
		}

		$locationJobs = $this->careerObj->getJobs($this->client,null,$params);

		if( !$locationJobs['is_error'] && $locationJobs['info']['count'] > 0 ){
			$jobs = $locationJobs['data'];
		}
		return $jobs;
	}

	function getJobs( $params = null ){

		$jobs = array();

		$locationJobs = $this->careerObj->getJobs($this->client,$this->storeid,$params);

		if( !$locationJobs['is_error'] && $locationJobs['info']['count'] > 0 ){
			$jobs = $locationJobs['data'];
		}
		return $jobs;
	}


	function getJobType( $params = null  ){
		$jobType = $this->careerObj->getJobType( $params );
		if( !$jobType['is_error'] && $jobType['info']['count'] > 0 ){
			$jobType =  $jobType['data'];
			return $jobType;
		}
		return false;
	}

	function getDefaultImage( $params = null  ){
		$defaultImage = $this->careerObj->getDefaultImage( $this->client,$params );
				
		if( !$defaultImage['is_error'] && $defaultImage['info']['count'] > 0 ){
			$defaultImage =  $defaultImage['data'];
			return $defaultImage;
		}
		return false;
	}

}
?>