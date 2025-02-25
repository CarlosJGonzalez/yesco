<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
require_once ($_SERVER['DOCUMENT_ROOT']."/includes/DasApiSDK/vendor/autoload.php");
use Das\facebook\FbUtils;
$fbUtils = new FbUtils( $token_api );
$params = array();

if( $_POST['age_min'] &&  $_POST['age_max']){
	$params['maxAge']= $_POST['age_max'];
	$params['minAge']= $_POST['age_min'];
}

if( $_POST['latitude'] &&  $_POST['longitude']){
	$params['latitude']= $_POST['latitude'];
	$params['longitude']= $_POST['longitude'];
}

$interests = arrayToString($_POST['interests']);
$behaviors = arrayToString($_POST['behaviors']);

if( $_POST['radius'] ){
	$params['radius']= $_POST['radius'];
}

if( $behaviors != ""){
	$params['behaviors']= $behaviors;
}

if( $interests != ""){
	$params['interests']= $interests;
}

$targeting = $fbUtils->getReachEstimate($params,$_SESSION['client'],$_SESSION['storeid']);

$users = isset($targeting['data']['data']['users']) ? $targeting['data']['data']['users'] : 0;

echo $users;exit;
function arrayToString($array,$deli=',',$key = null){
 	$str = '';
	foreach ($array as $value) {
		if( isset($key) ){
			$str .= $value[$key].$deli;
		}else{
			$str .= $value.$deli;
		}
		
	}

	$str = rtrim($str, $deli);
	return $str;
}
?>