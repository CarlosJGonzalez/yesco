<?
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");
session_start();

$comments=filter_var($_POST['comment'], FILTER_SANITIZE_STRING);

if($_POST['disposition'])
	$data = array("flagged"=>$_POST['flagged'], "rating"=>$_POST['rating'], "disposition"=>$_POST['disposition'],"comment"=>$comments);
else
	$data = array("flagged"=>$_POST['flagged'], "rating"=>$_POST['rating'], "comment"=>$comments);

$db->where ('callid', $_POST['callid']);
$calls_updated_ok = $db->update ('advtrack.calls', $data);

if($calls_updated_ok)
	$_SESSION['success']="Your changes were successfully saved";
else
	$_SESSION['error']="There was an error saving your changes";

if($_POST['header'])
	header("location: ".$_POST['header']);
else
	//header("location: /call-stats/?analyticsStartDate=".$_GET['analyticsStartDate']."&analyticsEndDate=".$_GET['analyticsEndDate']);
	header("location: /call-stats/?".$_SERVER['QUERY_STRING']);