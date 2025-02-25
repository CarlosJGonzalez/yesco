<?php 
require ($_SERVER['DOCUMENT_ROOT']."/includes/DasApiSDK/vendor/autoload.php");
use Das\DasStripe;

class Das_Stripe
{
	private $db;
	private $client;
	private $storeid = null;
	private $client_storeid;
	private $dasStripe;
	
	public function __construct($db,$token,$client,$storeid = null){
		$this->db = $db;
		$this->client = $client;		
		$this->dasStripe = new DasStripe($token);

		if( !isset($storeid) ){
			$this->client_storeid = $this->client;
		}else{
			$this->client_storeid = $this->client.'-'.$this->storeid;
			$this->storeid = $storeid;
		}
	}

	function getCards($customerid){
		$cards = $this->dasStripe->getCards($customerid);
		return ( !$cards['is_error'] ) ? $cards["data"] : [];
	}

	function isDefaultCard($customerid,$cardId){
		$cards = $this->dasStripe->isDefaultCard($customerid,$cardId);
		return ( !$cards['is_error'] ) ? $cards["data"] : false;
	}

	function createCharge($params){
		return $this->dasStripe->createCharge($params);
	}

	function createCustomer($params){
		return $this->dasStripe->createCustomer($params);
	}

	function createCard($customerid,$token){
		return $this->dasStripe->createCard( $customerid,$token);
	}
}
?>