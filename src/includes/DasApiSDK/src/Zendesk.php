<?php

namespace Das;

use Das\CallCurl;

class Zendesk extends CallCurl
{

	function __construct($token)
	{
		 parent::__construct($token);
	}

	function createTicket( $params ){
		$url = '/zendesk/createticket';
		return $this->call('POST', $url, $params );
	}
	
	function getUsers( $params ){
		$url = '/zendesk/users';
		return $this->call('GET', $url, $params );
	}

	function updateStatus( $ticketId,$params ){
		$url = "/zendesk/ticket/$ticketId/status";
		return $this->call('PUT', $url, $params );
	}

	function addComment( $ticketId,$params ){
		$url = "/zendesk/ticket/$ticketId/comment";
		return $this->call('PUT', $url, $params );
	}

	function getTickets( $params ){
		$url = '/zendesk/tickets';
		return $this->call('GET', $url, $params );
	}
}
?>
