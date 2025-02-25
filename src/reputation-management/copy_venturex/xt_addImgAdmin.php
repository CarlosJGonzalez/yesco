<?
include ($_SERVER['DOCUMENT_ROOT']."/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/functions.php");
session_start();

$name=filter_var($_POST['name'], FILTER_SANITIZE_STRING);
$tags=filter_var($_POST['tags'], FILTER_SANITIZE_STRING);
$category=filter_var($_POST['category'], FILTER_SANITIZE_STRING);
$month=filter_var($_POST['month'], FILTER_SANITIZE_STRING);
$iframe=filter_var($_POST['iframe'], FILTER_SANITIZE_URL);
$orderid = filter_var($_POST['orderid'], FILTER_SANITIZE_STRING);
if(!$category) $category="user";

$total = count($_FILES['fileToUpload']['name']);
$count=0;

for($i=0;$i<$total;$i++){
	if(!empty($_FILES['fileToUpload']['name'][$i])) {
		
		$target_dir = $_SERVER["DOCUMENT_ROOT"]."/img/gallery/".$category."/";
	
		$temp = explode(".", $_FILES["fileToUpload"]["name"][$i]);
		$newfilename = clean($temp[0]).'.'.end($temp);
			
			if($total>1)
				$nameOfImg = ucfirst(str_replace("-"," ",clean($temp[0])));
			else
				$nameOfImg = $name;
		
		$mini_target_dir = $_SERVER["DOCUMENT_ROOT"]."/img/gallery/".$category."-min/";
		
		
		//$target_file = $target_dir."/".$newfilename;
	
		$uploadOk = 1;
		$imageFileType = strtolower(pathinfo($newfilename,PATHINFO_EXTENSION));
		
		list($width, $height) = getimagesize($_FILES["fileToUpload"]["tmp_name"][$i]);
		if ($width > $height) 
			$orientation="landscape";
		else if ($width < $height)
			$orientation="portrait";
		else
			$orientation="";
			
		if(isset($_POST["submit"])) {
			$check = getimagesize($_FILES["fileToUpload"]["tmp_name"][$i]);
			if($check !== false) {
				$uploadOk = 1;
			} else {
				$error= "File is not an image.";
				$uploadOk = 0;
			}
		}
		// Allow certain file formats
		if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
			$error= "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
			$uploadOk = 0;
		}
		
		// Check if file already exists
		$target_file = checkFile($target_dir,$newfilename);
		$newfilename=end(explode("/", $target_file));
		$chunk = explode(".", $newfilename);
		$mini_newfilename=$chunk[0].'-min.'.$chunk[1];
		
		if (!is_dir($target_dir)) {
			mkdir($target_dir, 0777, true);
		}
		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0) {
			$_SESSION['error'] =  "Sorry, there was an error uploading your file: ".$error;
		// if everything is ok, try to upload file
		} else {
			$nameOfImg=filter_var($nameOfImg, FILTER_SANITIZE_STRING);
			if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"][$i], $target_file)) {
				$newName  = $mini_target_dir.$mini_newfilename;
				copy($target_file, $newName);
				//compressImage($target_file,$newName,100);
	
				$sql= "insert into ".$_SESSION['database'].".gallery (name,image,category,month,tags,orientation,active,storeid,apply_all, width, height,iframe) values ('".$nameOfImg."','".$newfilename."','".$category."','".$month."','".$tags."','".$orientation."','x','".$_SESSION["storeid"]."',NULL,'".$width."','".$height."','".$iframe."')";
				if(mysqli_query($conn, $sql)){
					$_SESSION['success']="Your Image has been successfully uploaded.";
					track($_SESSION["username"],$_SESSION["storeid"],"gallery",$newfilename);
					$sqlupdate = "update corebridge_order set image = CONCAT(COALESCE(`image`,''),',','$newfilename') where storeid = '".$_SESSION["storeid"]."' and orderid = '".$orderid."'";
					$result_update = $conn->query($sqlupdate);
					$count++;
				}else
					$_SESSION['error'] = "Sorry, there was an error uploading your file. ".$sql;
			}
			 else 
				$_SESSION['error'] = "Sorry, there was an error uploading your file. ";
		}
	}
}
notify_jessi($_SESSION["username"],$count,$client,$category);
if (isset($_POST['Save'])) {
	header("location:/reviews/order-detail.php?orderid=".$orderid);
}
elseif (isset($_POST['Review'])) {
	header("location:/reviews/order-template.php?orderid=".$orderid);
}