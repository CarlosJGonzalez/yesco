<?php 
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");

$_SESSION['database'] ="yesco_new";
$_SESSION['client'] ='1482';

// set store id by parameter
if(!empty($_GET['storeid'])){
	$_SESSION['storeid'] = $db->escape($_GET['storeid']);
}
// set view by parameter
if(!empty($_GET['view']) && in_array($_GET['view'],array("user","das_admin"))){
	$_SESSION['view'] = $db->escape($_GET['view']);
}
// set active location by parameter

if($_SESSION['view']=="user"){
	$db->where("storeid",$_SESSION['storeid']);
	$active_location = $db->getOne("locationlist");
	$_SESSION['location_name'] = $active_location['companyname'];
}else{ // admin view
	if( (isset($_SESSION['storeid']) && !empty($_SESSION['storeid'])) ){
		$db->where("storeid",$_SESSION['storeid']);
		$active_location = $db->getOne("locationlist");
		if ($db->count < 0)
			unset($active_location);
			unset($_SESSION['storeid']);
	}else{ 
		unset($_SESSION['storeid']);
		//unset($active_location);
	}
}

if($_SERVER['REQUEST_URI'] != "/"){
	// not logged in
	if(!isset($_SESSION['user_id'])){
		$_SESSION['error'] = "You do not have access to that page.";
		header("location:/");
		exit;
	}
	// admin cannot view / admin, user cannot view admin
	if($_SESSION['view']=="user" && strpos($_SERVER['REQUEST_URI'], 'admin') !== false){
		header("location:/dashboard.php");
		exit;
	}else if($_SESSION['view']!="user" && strpos($_SERVER['REQUEST_URI'], 'admin/') === false){
		$exceptions  = array('my-account',"");
		if (strposa($_SERVER['REQUEST_URI'], $exceptions, 1)) {
			//echo 'true';
		} else {
			header("location:/admin/location-details/");
			exit;
		}

		
	}
}

function strposa($haystack, $needles=array(), $offset=0) {
	$chr = array();
	foreach($needles as $needle) {
			$res = strpos($haystack, $needle, $offset);
			if ($res !== false) $chr[$needle] = $res;
	}
	if(empty($chr)) return false;
	return min($chr);
}


?>

<link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png">

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<link rel="stylesheet" href="/css/chosen.min.css">
<link rel="stylesheet" href="/css/styles.css">