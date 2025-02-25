<?php

namespace Das;

use Das\CallCurl;

class ClientSetting extends CallCurl
{

	function __construct($token)
	{
		 parent::__construct($token);
	}

	function getSetting($client,$storeid,$name){
		$url = "client/$client/$storeid/setting/$name";

		return $this->call('GET', $url );
	}

	function getValidSettings($id = null){
		$url = "client/setting/valids/";

		if ( isset($id) ) {
			$url = $url.$id;
		}

		return $this->call('GET', $url );
	}
}
?>