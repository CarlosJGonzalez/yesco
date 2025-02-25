<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<link rel="stylesheet" href="/css/styles.css">
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">

<?php include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
	  include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");
session_start();

/*if($_SERVER['REQUEST_URI']!="/login.php"){
	if(!$_SESSION["username"]){
		$_SESSION['error']="You must be logged in to view this page.";
		header('location: /login.php');
		exit;
	}
}*/

if(isset($_SESSION['admin']))
	if(isset($_GET['storeid'])) $_SESSION['storeid'] =$_GET['storeid'];
else
	if(isset($_GET['storeid']) && in_array($_GET['storeid'],$_SESSION['storelist'])) $_SESSION['storeid'] =$_GET['storeid'];

$_SESSION['database'] ="fullypromoted";

//if($_SESSION['storeid']>0){
	//$locationList = $db->where("storeid","18910")->getOne("locationlist");
	
//}

//print_r($locationList);
?>