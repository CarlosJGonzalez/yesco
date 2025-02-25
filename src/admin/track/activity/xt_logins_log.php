<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");

require ($_SERVER['DOCUMENT_ROOT']."/includes/DasApiSDK/vendor/autoload.php");
use Das\Report;
use Das\Client;

if( count($_POST) ){
	$draw = $_POST['draw'];
	$start = $_POST['start'];
	$rowperpage = $_POST['length']; // Rows display per page

	$params = false;

	if (isset($_POST['start_date']) && isset($_POST['end_date'])) {
		$params['gte'] = strtotime($_POST['start_date']);
		$params['lte'] = strtotime($_POST['end_date']);
	}

	if( isset($_POST['search']['value']) && strlen($_POST['search']['value']) >= 3 ){
		$params['query'] = $_POST['search']['value'];
	}

	if (isset($_POST['start']) && isset($_POST['length'])) {

		$params['start_p'] = $_POST['start'];
		$params['end_p'] = $_POST['length'];

	}
	$filter = false;

	$itotal1  = 0;
	$itotal  = 0;
	$data = array();

	$storeid = isset($_SESSION['storeid']) ? $_SESSION['storeid'] : null;

	if( isset($storeid) ){
		$params['storeid'] = $storeid; 
	}

	$clientLogins = new Client($token_api);
	$loginInfoLog = $clientLogins->getClientLogins($_SESSION['client'],$params);


	$itotal = $loginInfoLog['info']['total_items'];
	$itotal1 = $loginInfoLog['info']['total_items'];


	foreach ($loginInfoLog['data'] as $info) {
		$name_client = $clientLogins->getClient( $_SESSION['client'], $info['storeid'] );
		$name_client = ( isset($name_client['data'][0]['name']) ) ?$name_client['data'][0]['name'] : 'N/A';

		$data[] = array(
								"date"  	   => date("m/d/Y g:i:s A", strtotime($info['time'])),
								"storeid"  	   => $info['storeid'],
								"locationName" => $name_client,
								"username" 	   => $info['username'],
								"ip"  	       => $info['ip_address']
						    );
	}

	$response = array(
		"draw" => intval($draw),
		"iTotalRecords" => $itotal1,
		"iTotalDisplayRecords" => $itotal,
		"aaData" => $data
	);

	echo json_encode($response);
}
?>