<?
include ($_SERVER['DOCUMENT_ROOT']."/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/functions.php");
session_start();

$name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
$email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);

$sql = "select * from review_recipient where email='".$email."' and storeid='".$_SESSION['storeid']."'";
$result = $conn->query($sql);
if ($result->num_rows > 0){
	$_SESSION['error']="This customer already exists.";
	header("location:/reviews/upload-customers.php");
	exit;
}else{
	$sql = "insert into review_recipient(name,email,storeid,sent_flag) values('".$name."','".$email."','".$_SESSION['storeid']."','N')";
	$result = $conn->query($sql);
	$_SESSION['success']=$name." <".$email."> has been added.";
}

header("location:/reviews/upload-customers.php");