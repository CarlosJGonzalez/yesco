<?php

namespace Das;

use Das\CallCurl;

class Workwave extends CallCurl
{

	function __construct($token)
	{
		 parent::__construct($token);
	}

	function createLead($params){
		$url = 'lead';
		
		return $this->call('POST', $url, $params );
	}

	function createLocation($params){
		$url = 'location';
		
		return $this->call('POST', $url, $params );
	}


	function getBranch( $id ){
		$url = "branch/".$id;
		return $this->call('GET', $url );
	}

	function getBranches(){
		$url = "branches";
		return $this->call('GET', $url );
	}
}
?>
