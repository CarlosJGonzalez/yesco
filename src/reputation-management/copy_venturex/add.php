<?
include ($_SERVER['DOCUMENT_ROOT']."/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/functions.php");

session_start();

if($_GET['action']=="add"){
	$sql= "select OrderContact,OrderContactEmail,storeid from corebridge_order where customerid in (".$_GET['values'].") and storeid = '".$_SESSION["storeid"]."'";
	$result = $conn->query($sql);
	if ($result->num_rows > 0)
	while($row = $result->fetch_assoc()){	
		$email = $row['OrderContactEmail'];
	
			 // Checking duplicate entry
		$sql = "select count(*) as allcount from review_recipient where email='" . $email . "'";
		$retrieve_data = $conn->query($sql);
		$row1 = $retrieve_data->fetch_assoc();
		$count = $row1['allcount'];
		 if($count == 0){
			// Insert record
			$insert_query = "insert into review_recipient(name,email,storeid) values('".$row['OrderContact']."','".$row['OrderContactEmail']."','".$_SESSION['storeid']."')";
			$result1 = $conn->query($insert_query);
		 }
	
	}
	if(mysqli_query($conn, $sql)){
		$_SESSION['success']="Your emails have been saved.";
		track($_SESSION["username"],$_SESSION["storeid"],"review_template",$insert_query);
	}else
		$_SESSION['error'] = "Sorry, there was an error adding your emails.";
}
header("location:/reviews/upload-customers.php");