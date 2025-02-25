<?php

namespace Das;

use Das\CallCurl;

class CallRail extends CallCurl
{
	protected $account;
	
	function __construct($token,$account)
	{
		$this->account = $account;
		parent::__construct($token);
	}

	function delete($id){
		$url = "callrail/".$this->account."/$id";

		return $this->call('DELETE', $url );
	}

	function getExistTracker($tracker,$termnum = null){
		$url = "callrail/".$this->account."/search/$tracker";

		if ( isset($termnum) ) {			
			return $this->call('GET', $url.'/'.$termnum );
		}
		return $this->call('GET', $url );
	}

	function getCompany($client = null){
		$url = 'callrail/'.$this->account.'/company';
		
		if ( isset($client) ) {
			return $this->call('GET', $url.'/'.$client );
		}
		return $this->call('GET', $url );
	}

	function createTracker($params){
		$url = 'callrail/'.$this->account.'/tracker';
		return $this->call('POST', $url, $params );
	}

	function updateTracker($params){
		$url = 'callrail/'.$this->account.'/tracker/update';
		return $this->call('POST', $url, $params );
	}

	function getCalls( $callId ){
		$url = "/callrail/".$this->account."/calls/$callId";
		return $this->call('GET', $url );
	}

	function getRecording( $callId ){
		$url = "/callrail/".$this->account."/recording/$callId";
		return $this->call('GET', $url );
	}
}
?>
