<?php
include ($_SERVER['DOCUMENT_ROOT']."/connect.php");
session_start();

$sql="update advtrack.client_review set approved='".$_POST['value']."' where id='".$_POST['reviewid']."'";

if(mysqli_query($conn, $sql))
	echo "success";