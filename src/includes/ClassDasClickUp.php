<?php 

require ($_SERVER['DOCUMENT_ROOT']."/includes/DasApiSDK/vendor/autoload.php");
use Das\ClickUp;

class Das_ClickUp
{
	private $service;

	public function __construct($key){
		$this->service = new ClickUp($key);
	}

	public function newTask($teamName,$spaceName,$folderName,$listsName,$name,$content,$assignees){

		$params = array(
						'name' => $name, 
						'teamName' => $teamName, 
						'spaceName' => $spaceName, 
						'folderName' => $folderName, 
						'listName' => $listsName, 
						'content' => $content, 
						'priority' => 1, 
						'status' => 'Open', 
						'assignees' => $assignees, 
					);
	
		return $this->service->createTask($params);
	}

}

?>