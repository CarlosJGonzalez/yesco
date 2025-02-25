<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Client;

use Ctct\ConstantContact;
use Ctct\Components\Contacts\Contact;
use Ctct\Components\Contacts\ContactList;
use Ctct\Components\Contacts\EmailAddress;
use Ctct\Exception\CtctException;



class Das_CC 
{	

	private $access_token='ac70d929-ee15-4f65-88c6-a53f1fbb44f6';
	private $apikey='5as4yxv4jb42cde4f8fgfvp2';

	private $cc;
	
	function __construct()
	{
		$this->cc= new ConstantContact($this->apikey);
	}

	private function ret($status,$operation,$data){
	    return array('status' =>$status ,'operation' =>$operation ,'data' =>$data );
	}

	function CreateList($name){
		try{

			$info['name']= $name;
			$info['status']="ACTIVE";
			$objlist=ContactList::create($info);
			$newList=$this->cc->listService->addList($this->access_token,$objlist);

			return $this->ret(1,"add",$newList);

		}catch(CtctException $ex){

			return $this->ret(0,"error",$ex->getErrors());
		}		
	}

	function AddOrUpdateContact($email,$list_id,$first_name,$last_name,$cell_phone=""){
			try{
				
				$obj["email"]=$email;
				$response = $this->cc->contactService->getContacts($this->access_token, $obj);
				
			if (empty($response->results)) {
			 	$contact = new Contact();
	            $contact->addEmail($email);
	            $contact->addList($list_id);
	            $contact->first_name = $first_name;
                $contact->last_name = $last_name;
                $contact->cell_phone = $cell_phone;
                

	            $ccContact = $this->cc->contactService->addContact($this->access_token, $contact,array());

	            return $this->ret(1,"add",$ccContact);
            
		 	}else{
		 		$contact = $response->results[0];
		 		
				if ($contact instanceof Contact) {
					$contact->addList($list_id);
		            $contact->first_name = $first_name;
		            $contact->last_name = $last_name;

					$ccContact = $this->cc->contactService->updateContact($this->access_token, $contact,array());

	                return $this->ret(1,"update",$ccContact);
				}
		 	}
		}catch(CtctException $ex){

	        return $this->ret(0,"error",$ex->getErrors());
		}

	}

	function getList($id_list=null){
		try{
			if($id_list){
				return $this->ret(1,"search",$this->cc->listService->getList($this->access_token,$id_list));
		}
		return $this->ret(1,"getAll",$this->cc->listService->getLists($this->access_token));

		}catch(CtctException $ex){
			return $this->ret(0,"error",$ex->getErrors());
		}
		
	}
}
?>