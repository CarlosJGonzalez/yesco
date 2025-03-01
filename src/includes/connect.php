<?php
require ($_SERVER['DOCUMENT_ROOT']."/includes/MysqliDb.php");


//$db = new MysqliDb ('db', 'admin', 'GQzF1xo38auaoIUnWSux', 'yesco_new');
$db = new MysqliDb ('rackspace-application-rds.co8bxehb4baf.us-east-1.rds.amazonaws.com', 'admin', 'GQzF1xo38auaoIUnWSux', 'yesco_new');

$token_api = '$2y$10$w/0VvnOoj5fvGA7alcDkROYWd6d2aeeZrUK2fduL9EFNen5RIRSfG';
const CLIENT_NAME = "Yesco";
const CLIENT_URL = "https://www.yesco.com/";
const LOCAL_CLIENT_URL = "https://localyesco.com/";
const SHOP_CLIENT_URL = "https://www.yesco.com/";

//https://stackoverflow.com/questions/520237/how-do-i-expire-a-php-session-after-30-minutes/1270960#1270960
//session.gc_maxlifetime it's defined as a directive in the PHP's configuration. 
//It is set to 1440 sec, which represents 24  min. For more information please refer to phpinfo()
$maxlifetime = ini_get("session.gc_maxlifetime");

// If last request was more than 24 minutes ago
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $maxlifetime)) {

    session_unset();     // unset $_SESSION variable for the run-time 
    session_destroy();   // destroy session data in storage
	
	header("location:/xt_logout.php"); // Redirects to login page
	exit;
}

$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp
if( !defined ( 'GOOGLE_MAPS_API_KEY' ) ){
    /*************************************************************
     * this key is under Das-Dashboard Project
     * Only must be used in CURL calls since it is not restricted!
     ************************************************************/
    define( 'GOOGLE_MAPS_API_KEY', 'AIzaSyDlvzKwuwBpIoMGheHh3xGtODV_QtctOdg' );
}
?>
