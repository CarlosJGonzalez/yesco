<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/xlsxwriter.class.php");
require_once ($_SERVER['DOCUMENT_ROOT']."/includes/ClassDasReview.php");
require_once ($_SERVER['DOCUMENT_ROOT']."/includes/ClassDasClient.php");


if( count($_POST) ){
	$params =array();
	$params['start_p'] = 0;
	$params['end_p'] = 1000;

	if (isset($_POST['start_date']) && isset($_POST['end_date'])) {
		$params['gte'] = strtotime($_POST['start_date']);
		$params['lte'] = strtotime($_POST['end_date']);
	}

	if (isset($_POST['start']) && isset($_POST['length'])) {

		$params['start_p'] = $_POST['start'];
		$params['end_p'] = $_POST['length'];

	}

	$filter = false;
	if( isset($_POST['filter_star']) && $_POST['filter_star'] != '' ){
		filterStar($params,$_POST['filter_star']);
		$filter = true;
	}

	if( isset($_POST['filter_portal']) && $_POST['filter_portal'] != '' ){
		$params['source'] = trim( $_POST['filter_portal'] , ',' );
		$filter = true;
	}

	$storeid = isset($_SESSION['storeid']) ? $_SESSION['storeid'] : null;
	$dasReview = new Das_Review($db,$token_api,$_SESSION['client'],$storeid);

	$reviews = $dasReview->getReviews( $params );

	$header = array(
				  	"date"     =>'string',
				  	"client"   =>'string',
				  	"source"   =>'string',
				  	"rating"   =>'integer',
				  	"name"     =>'string',				  
				  	"comment"  =>'string',
				);	


	if(!$result['is_error']){
		$result_data = $reviews['data'];
	}else{
		$result_data = [];
	}

	$writer = new XLSXWriter();
	$writer->writeSheetHeader('Sheet1', $header );

	$clientObj = '';
	foreach ($result_data as $obj) {

		$dasClient = new Das_Client($db,$token_api,$obj['client'],$obj['storeid']);
		$clientInfo = $dasClient->getClient();
		
		$data = array(
							isset($obj['create_date']) ? $obj['create_date'] : 'N/A',
							isset( $clientInfo['data'][0]['name'] ) ? $clientInfo['data'][0]['name'] : $review['storeid'],
							isset($obj['portal']) ? $obj['portal'] : 'N/A',
							isset($obj['rating']) ? $obj['rating'] : 'N/A',
							isset($obj['name']) ? $obj['name'] : 'N/A',
							isset($obj['comment']) ? $obj['comment'] : 'N/A',
					    );
		$writer->writeSheetRow('Sheet1', $data );	
	}

	$file_name = "ReviewsReport_".md5(serialize($params)).".xlsx";

	
	$writer->writeToFile($file_name);

	echo LOCAL_CLIENT_URL.'admin/reputation-management/'.$file_name;die;
}


function filterStar(&$params,$filter){
	$filters = explode(',', trim($filter,','));
	
	if( count( $filters ) == 1){
		$params['rating'] = $filters[0];
	}else{
		sort($filters);
		$params['gte_rating'] = $filters[0];
		$params['lte_rating'] = end($filters);		
	}
}
?>