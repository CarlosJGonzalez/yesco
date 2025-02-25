<?php
require ($_SERVER['DOCUMENT_ROOT']."/includes/DasApiSDK/vendor/autoload.php");
use Das\Client;

class Das_Client
{

	private $db;
	private $client;
	private $storeid;
	private $client_storeid;
	private $clientObj;


	function __construct($db,$token,$client,$storeid = null){
		$this->db = $db;
		$this->storeid = $storeid;
		$this->client = $client;
		$this->clientObj = new Client($token);

		if( isset($this->storeid) ){
			$this->client_storeid = $this->client;
		}else{
			$this->client_storeid = $this->client.'-'.$this->storeid;
		}
	}
	
	function updateOrCreateGaInfo( $data ){
		$ga = $this->clientObj->getGAInfoByClient( $this->client,$this->storeid );
		
		if( !$ga['is_error'] ){
			if( $ga['info']['count'] ){
				return $this->clientObj->updateGaInfo( $this->client,$data );				
			}else{
				return $this->clientObj->addGaInfo( $this->client,$data );
			}			
		}
	}
	
	function updateOrCreate($data){
		$tmpClient = $this->getClient();

		if( !$tmpClient['is_error'] ){

			if( $tmpClient['info']['count'] ){
				return $this->update($data);
			}else{
				return $this->create($data);
			}			
		}
	}

	function getClient(){
		return $this->clientObj->getClient($this->client,$this->storeid);
	}

	function create($data){
		return $this->clientObj->create($data);
	}

	function delete(){
		return $this->clientObj->delete($this->client,$this->storeid);
	}

	function update($data){
		return $this->clientObj->update($this->client,$data,$this->storeid);
	}
}
?>