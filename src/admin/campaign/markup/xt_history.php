<?php 
session_start();
require ($_SERVER['DOCUMENT_ROOT']."/includes/DasApiSDK/vendor/autoload.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
use Das\MarkUp;

if( isset($_POST['id']) && $_POST['id'] != '' ){

	$markupObj = new Markup($token_api);

	$draw = $_POST['draw'];
	$start = $_POST['start'];
	$rowperpage = $_POST['length']; // Rows display per page

	$histories = $markupObj->getHistoryMarkUp($_POST['id']);
	$histories = isset($histories['data'][0]) ? $histories['data'] :[];

	$data =array();

	foreach ($histories as $history) {
		$row = array(
							'start' =>$history['start'],
							'end'   =>$history['end'],
							'markup' => $history['markup'],
							'active' => $history['active'] ? 'Yes' : 'No'
						);

		if( $history['active'] ){ 
			$actions = '<a href=""  data-id="'.$history['id'].'" id="action_edit" class="btn rounded-pill btn-sm btn-secondary edit">Edit</a> ';
			$actions .= '<a href=""  data-id="'.$history['id'].'" id="action_delete" class="btn rounded-pill btn-sm btn-secondary delete">Delete</a>';
		}else{
			$actions = '<a href=""  data-id="'.$history['id'].'" id="action_restore" class="btn rounded-pill btn-sm btn-secondary restore">Re Active</a>';
		}
		$row['actions'] = $actions;
		$data[]=$row;		
	}

	$response = array(
		"draw" => intval($draw),
		"iTotalRecords" => count($data),
		"iTotalDisplayRecords" => count($data),
		"aaData" => $data
	);

	echo json_encode($response);
}