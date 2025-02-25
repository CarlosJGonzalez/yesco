<?php 
session_start();
require ($_SERVER['DOCUMENT_ROOT']."/includes/DasApiSDK/vendor/autoload.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
use Das\CampId;

$campid = new CampId($token_api);
$campIdInfo=  $campid->getUrl($_POST["id"]);
$data = '';

if( isset( $campIdInfo['data'][0]['url'] ) ){
	$data = $campIdInfo['data'][0]['url'];
}
echo $data;
exit();
?>