<?
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");

/*if($_SESSION['admin'] && $_SESSION['storeid']>0){
	return;
}*/
if($_POST['type']=="single"){
	$db->setTrace (true);
	$data = array($_POST['value']=>"0");
	$db->where ('id', $_POST['id']);
	print_r($data);
	if($db->update ('notifications', $data))
		echo "success";
	
	print_r ($db->trace);die;
}else{
	$user_type = $_POST['user_type'];
	$data = array($_POST['value']=>"0");
	
	if($user_type == "user"){
		$db->where ('storeid', $_POST['storeid']);
	}else{
		$db->where("user_type",$_SESSION['view']);
	}

	if($db->update ('notifications', $data))
		echo "success";
}