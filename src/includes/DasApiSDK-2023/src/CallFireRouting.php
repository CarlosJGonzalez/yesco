<?php

namespace Das;

use Das\CallCurl;

class CallFireRouting extends CallCurl
{

	function __construct($token)
	{
		 parent::__construct($token);
	}

	function update($params){
		$url = 'callfirerouting/update';
		return $this->call('POST', $url, $params );
	}

	function create($params){
		$url = 'callfirerouting';
		
		return $this->call('POST', $url, $params );
	}

	function delete($id){
		$url = "callfirerouting/$id";

		return $this->call('DELETE', $url );
	}

	function getCallFireRouting($id){
		$url = "callfirerouting/$id";
		
		return $this->call('GET', $url );
	}

	function getCallFireRoutingByStoreId($client,$storeid = null){
		$url = "callfirerouting/search/$client";
		
		if ( isset($storeid) ) {
			return $this->call('GET', $url.'/'.$storeid );
		}
		return $this->call('GET', $url );
	}

	function getCallFireRoutingByCampId($client,$storeid,$campid,$options = null){
		$url = "callfirerouting/search/$client/$storeid/$campid";

		return $this->call('GET', $url,$options );
	}
}
?>
