<?php 
session_start();
require ($_SERVER['DOCUMENT_ROOT']."/includes/DasApiSDK/vendor/autoload.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
use Das\MarkUp;

$markupObj = new Markup($token_api);

$campIdInfo=  $markupObj->update($_POST["id"],array('active'=>1));
$data = -1;

if( isset( $campIdInfo['data'][0]['id'] ) ){
	$data = 1;
}
echo $data;
exit();
?>