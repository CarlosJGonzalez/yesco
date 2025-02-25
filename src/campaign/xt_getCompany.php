<?php 
session_start();
require ($_SERVER['DOCUMENT_ROOT']."/includes/DasApiSDK/vendor/autoload.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
use Das\CallRail;

$callRail = new CallRail($token_api,'224437392');
echo json_encode($callRail->getCompany($_POST["client"]));
exit();
?>