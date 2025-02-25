<?php 
session_start();
require ($_SERVER['DOCUMENT_ROOT']."/includes/DasApiSDK/vendor/autoload.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
use Das\MarkUp;

if( isset($_POST['id']) && $_POST['id'] != '' ){

	$markupObj = new Markup($token_api);

	$histories = $markupObj->getMarkUp($_POST['id']);
	$histories = isset($histories['data'][0]) ? $histories['data'] :[];

	$row = array();
	foreach ($histories as $history) {
		$row = array(
							'start' =>$history['start'],
							'end'   =>$history['end'],
							'markup' => $history['markup']
						);	
	}

	echo json_encode($row);exit();
}