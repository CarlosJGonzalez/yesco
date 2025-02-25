<?
include_once $_SERVER['DOCUMENT_ROOT'].'/includes/connect.php';
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");
session_start();

$error = '';

if ( $_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST) && empty($_FILES) && $_SERVER['CONTENT_LENGTH'] > 0 ){
	$postLength = '';
	$sizeType = '';
	$displayMaxSize = ini_get('post_max_size');
	$sizeType = substr($displayMaxSize,-1);

	switch ($sizeType) {
		case 'G':
			$postLength = $_SERVER['CONTENT_LENGTH'] / 1000000000;
			break;
		case 'M':
			$postLength = $_SERVER['CONTENT_LENGTH'] / 1000000;
			break;
		case 'K':
			$postLength = $_SERVER['CONTENT_LENGTH'] / 1000;
			break;
	}
 
	$error = 'Posted data is too large. All your files are '.$postLength.' '.$sizeType.', which exceed the maximum size of '.$displayMaxSize;
	$_SESSION['error'] = $error;
	
}else{
	if($_FILES["importfile"]["error"] == 0){

		//Getting the file size value in the $_FILES array
		$arraySum = $_FILES["importfile"]["size"];
			
		//if file size value is less or equal to 40MB, the file will be uploaded
		if($arraySum <= 40000000){

			if(isset($_POST['but_import'])){
			    $target_dir = $_SERVER['DOCUMENT_ROOT']."/uploads/";
			    $target_file = $target_dir . basename($_FILES["importfile"]["name"]);

			    $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
				//echo '$imageFileType' . $imageFileType;
			    $uploadOk = 1;
			    if(strtolower($imageFileType) != "csv" ) {
					$uploadOk = 0;
			    }

			    if ($uploadOk != 0) {
				    if (move_uploaded_file($_FILES["importfile"]["tmp_name"], $target_dir.'importfile.csv')) {

						$target_file = $target_dir . 'importfile.csv';
						$fileexists = 0;
						echo $target_file."<bR>";
						if (file_exists($target_file)) {
						   $fileexists = 1;
						}
						if ($fileexists == 1 ) {

						    // Reading file
						    $file = fopen($target_file,"r");
						    $i = 0;

						    $importData_arr = array();

						    while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
								$num = count($data);

								for ($c=0; $c < $num; $c++) {
									$importData_arr[$i][] = $data[$c];
								}
								$i++;
						    }
						    fclose($file);

						    $skip = 0;
						    // insert import data
						    $data_insert= array();
						    foreach($importData_arr as $data){
								$username = $data[0];
								$email = $data[1];

								$count=$db->where('email',$email)
								   		  ->where('storeid',$_SESSION['storeid'])
								   		  ->getOne('review_recipient','count(*) as allcount');

								if($count['allcount'] == 0){
									$data_insert[]= array(
															'name'	 	=> $username, 
															'email' 	=> $email, 
															'storeid'   => $_SESSION['storeid'], 
															'sent_flag' => 'N', 
														 );
								}
						    }

						    if(count($data_insert)){
						    	$ids = $db->insertMulti('review_recipient', $data_insert);
								if(!$ids) {
								    $_SESSION['error']="There was an error uploading your information";
								    header("location:/reputation-management/");
								}
						    }
						    $newtargetfile = $target_file;
						    if (file_exists($newtargetfile)) {
								unlink($newtargetfile);
						    }
							$_SESSION['success']="Your file was successfully uploaded.";
						}
				    }
				    else $_SESSION['error']="There was an error uploading your file";
			    }else{
				   $_SESSION['error']="There was an error uploading your file";
			    }
			}
		}else {
			$_SESSION['error'] = "Your file size must be less than 40MB. Please, optimize your file before uploading.";
		}	
	}else{
		$_SESSION["error"]="Sorry, there was an error uploading your file.";
	}

}
header("location:/reputation-management/");