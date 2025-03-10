<?php 
session_start();
/*********************************************
 * Loading the environment file
 */
include_once $_SERVER['DOCUMENT_ROOT']."/autoload.php";
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");
ini_set( 'display_errors', 0 );
ini_set( 'display_startup_errors', 0 );
error_reporting(0);

$logsDb = new MysqliDb('rackspace-applications-rds.co8bxehb4baf.us-east-1.rds.amazonaws.com', 'admin', 'GQzF1xo38auaoIUnWSux', 'logs');

use src\DotEnv;
( new DotEnv( $_SERVER['DOCUMENT_ROOT'] . '/.env') )->load();

/********************************************
 * Pulling constants from .env
 */
if( getenv( 'DAS_CLIENT_ID' ) ) {
	$_SESSION['client'] = getenv( 'DAS_CLIENT_ID' );
}else{
	$_SESSION['client'] = -1;
	$_SESSION['error'] = 'The DAS_CLIENT_ID constant was not found in the environment file';
}

if( getenv( 'DATABASE_NAME' ) ){
	$_SESSION['database'] = getenv( 'DATABASE_NAME' );
}else{
	$_SESSION['error'] = 'The DATABASE_NAME constant was not found in the environment file';
	$_SESSION['database'] = '';
}


// set store id by parameter
if(isset($_GET['storeid']) && !empty($_GET['storeid'])){
	$_SESSION['storeid'] = $db->escape($_GET['storeid']);
}
// set view by parameter
if(isset($_GET['view']) && !empty($_GET['view']) && in_array($_GET['view'],array("user","das_admin"))){
	$_SESSION['view'] = $db->escape($_GET['view']);
}
// set active location by parameter

if($_SESSION && isset($_SESSION['view']) && $_SESSION['view']=="user"){
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