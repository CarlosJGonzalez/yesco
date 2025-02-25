<?php 
session_start();
require ($_SERVER['DOCUMENT_ROOT']."/includes/DasApiSDK/vendor/autoload.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
use Das\MarkUp;

$data = -1;
if( !isset($_SESSION['email']) && !isset($_POST['campid']) ){
	echo $data;
	exit();
}

$markupObj = new Markup($token_api);

$markup = (double)$_POST['markup'];
$campid = (int)$_POST['campid'];

if( $_POST['id'] == 0 ){
	$campIdInfo = $markupObj->create( $campid,
										array(
												'markup'=> $markup,
												'start'=> (string)strtotime($_POST['start']),
												'end'=> (string)strtotime($_POST['end']),
											 )
									  );
}else{
	$campIdInfo = $markupObj->update( $_POST['id'],
										array(
												'markup'=> $markup,
												'start'=> (string)strtotime($_POST['start']),
												'end'=> (string)strtotime($_POST['end']),
												'campid_id'=> $campid
											 )
									  );
}


if( isset( $campIdInfo['data'][0]['id'] ) ){
	$data = 1;
}
echo $data;
exit();
?>