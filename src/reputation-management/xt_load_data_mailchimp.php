<?php
date_default_timezone_set('America/New_York');
session_start();
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/connect.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/includes/ClassDasMC.php';

if(isset($_SESSION['storeid'])){

	$mailchimp= new Das_MC('686a8f18f70a53080e689fe237f9ded9-us20');
	$row= $db->where('storeid',$_SESSION['storeid'] )->getOne('locationlist');
	$draw = $_POST['draw'];
	
	$response = array(
		"draw" => intval($draw),
		"iTotalRecords" => 0,
		"iTotalDisplayRecords" => 0,
		"aaData" => Array()
	);

	if(filter_var($_POST['search']['value'],FILTER_VALIDATE_EMAIL) !== false){
		$value = $mailchimp->getMember($row['mailchimp_listid'],$_POST['search']['value']);	
		$info_act="";
		if (count($value['activity'])){	
			$info_act=get_html_activity($value);
		}
		
		if($value['is_error'] != 1){
			
			$name = $value["info"]['email_address'];
			if(isset($value["info"]['fname']) && $value["info"]['fname'] != "" ){
				$name= $value["info"]['fname']. " ".$value["info"]['lname'];
			}
			
			$data[]=array(
							"id"		=> 1,
							"name"		=>$name,
							"email"		=>$value["info"]['email_address'],
							"status"	=>$value["info"]['status'],
							"actions"	=>$info_act,
						);
			$response = array(
								"draw" => intval($draw),
								"iTotalRecords" => 2,
								"iTotalDisplayRecords" => 2,
								"aaData" => $data
							);
		}else{
			$response = array(
								"draw" => intval($draw),
								"iTotalRecords" => 0,
								"iTotalDisplayRecords" => 0,
								"aaData" => array()
							);
		}
	}else{ 
	
	
	$start = $_POST['start'];
	$rowperpage = $_POST['length']; // Rows display per page

	if(isset($row['mailchimp_listid']) && $row['mailchimp_listid'] !=""){
		$rrs=$mailchimp->getMembers($row['mailchimp_listid'],array('count'=>$rowperpage,'offset'=>$start));

		$data = array();
	
		foreach ($rrs as $key => $value) {
			if (is_numeric($key)) {
			$info_act="";
			if (count($value['activity'])){	
				
				$info_act=get_html_activity($value);												
			}
			
			$name = $value["info"]['email_address'];
			if(isset($value["info"]['fname']) && $value["info"]['fname'] != "" ){
				$name= $value["info"]['fname']. " ".$value["info"]['lname'];
			}

			$data[] = array( 
				"id"		=>$key+1,
				"name"		=>$name,
				"email"		=>$value["info"]['email_address'],
				"status"	=>$value["info"]['status'],
				"actions"	=>$info_act
			);
			}
		}
		$response = array(
			"draw" => intval($draw),
			"iTotalRecords" => $rrs['total_items'],
			"iTotalDisplayRecords" => $rrs['total_items'],
			"aaData" => $data
		);

		
	}
	}
	echo json_encode($response);
}

function get_html_activity($rr){

	$icons=array('open'=>'fa-envelope-open','click'=>'fa-mouse-pointer','sent'=>'fa-paper-plane');
	$html='<div style="font-size: 1rem;">';
	foreach ($rr['activity'] as $key => $value) {

		if(isset($icons[$key])){

			$html.='<span class="fa-layers fa-2x mr-2" style="pading:MistyRose">
    				<i title="'.ucfirst($key).'" class="fas '.$icons[$key].' activity"></i>
    				<span class="fa-layers-counter">'.$value.'</span>
  					</span>';
		}
	}
	return $html.'</div>';
}
?>