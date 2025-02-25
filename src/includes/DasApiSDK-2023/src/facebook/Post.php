<?php

namespace Das\facebook;

use Das\CallCurl;

class Post extends CallCurl
{

	function __construct($token)
	{
		 parent::__construct($token);
	}


	function create($client,$storeid, $data){
		$url = "/fb/post/$client/$storeid";
		return $this->call('POST', $url,$data );
	}

	function delete( $client,$storeid, $page_post_id ){
		$url = "/fb/post/$client/$storeid/$page_post_id";
		return $this->call( 'DELETE', $url );
	}

	/**
	 *
	 * $page_post_id 
	 *
	 */
	
	function isPromotable( $client,$storeid, $page_post_id ){
		$url = "/fb/post/ispromotable/$client/$storeid/$page_post_id";
		return $this->call( 'GET', $url );
	}

}
?>
