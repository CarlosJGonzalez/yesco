<?php

namespace Das;

use Das\CallCurl;

class MarkUp extends CallCurl
{

	function __construct($token)
	{
		 parent::__construct($token);
	}

	function create( $campid_id,$params ){
		$url = "campid/$campid_id/markup";

		return $this->call('POST', $url,$params );
	}

	function update( $id,$params ){
		$url = "campid/markup/$id";

		return $this->call('PUT', $url,$params );
	}
	
	function delete($id){
		$url = "campid/markup/$id";

		return $this->call('DELETE', $url );
	}

	function getHistoryMarkUp( $campid_id ){
		$url = "campid/$campid_id/markup";
		return $this->call('GET', $url );
	}

	function getMarkUp( $id ){
		$url = "campid/markup/$id";
		return $this->call('GET', $url );
	}
	
	function getMarkUpActiveByDate( $campid_id,$params ){
		$url = "campid/$campid_id/markup/active";
		return $this->call('GET', $url,$params );
	}
}
?>
