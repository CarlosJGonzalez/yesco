<?php

namespace Das\facebook;

use Das\CallCurl;

class Page extends CallCurl
{

	function __construct($token)
	{
		 parent::__construct($token);
	}
	
	/*
	** If the page is a parent this function get all location associte 
	**
	**@param $face_id name or fb_page_id
	**
	**return FB Pages array
	*/
	function getPageLocations( $face_id ){
		$url = "fb/page/$face_id/locations";
	
		return $this->call('GET', $url);	
	}
	
	/*
	** Get Page Info directly for FB
	**
	**@param $face_id name or fb_page_id
	**
	**return array
	*/
	function getPageInfo( $face_id ){
		$url = "fb/page/$face_id/info";

		return $this->call('GET', $url);	
	}
	
	/*
	** Get Page Info by clients
	**
	**@param $client 
	**@param $storeid 
	**
	**return array
	*/
	function getPageByClient($client,$storeid = null){
		$url = "/fb/page/client/$client";

		if ( isset($storeid) ) {
			$url = $url.'/'.$storeid;
		}
		return $this->call('GET', $url );
	}
	
	/*
	**Save Page information
	**
	**@param $client 
	**@param $data Information Page. 
	**
	**return array
	*/
	function savePageInfo($client,$data){
		$url = "/fb/page/$client/save";

		return $this->call('POST', $url,$data );
	}

}
?>
