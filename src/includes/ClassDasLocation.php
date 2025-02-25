<?php

class Das_Location
{

	private $storeid;
	private $db;

	function __construct($storeid = null, $db)
	{
		$this->storeid= $storeid;
		$this->db= $db;
	}

	public function getLastLogin(){
		return $this->db->orderBy("lastlogin","desc")->where("storeid",$this->storeid)->getOne("storelogin",array("lastlogin"));
	}
/*
	function mysql_escape_mimic($inp) { 
	    if(is_array($inp)) 
	        return array_map(__METHOD__, $inp); 

	    if(!empty($inp) && is_string($inp)) { 
	        return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $inp); 
	    } 

	    return $inp; 
	}

	function clear_input($text) {
	  	$data = trim($text);
	  	$data = stripslashes($text);
	  	$data = htmlspecialchars($text);
	  	return $data;
	}

	public function get_ip(){
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}

	//Returns something like https://localfullypromoted.com/
	public function getFullUrl(){
		$link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 
	                "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
					
		return $link;
	}

	public function track_activity($data){

		$default_data = array(
								"username"=>$_SESSION['email'],
							 	"storeid"=>$_SESSION['storeid'],
							 	"ip_address"=>$this->get_ip(),
							 	"time"=>$this->db->now(),
							 	"uri"=>$this->getFullUrl().$_SERVER['REQUEST_URI']
							 );

		$result = array_merge($default_data,$data);
		
		$db->insert ('activity', $result);
		return;
	}	*/
}