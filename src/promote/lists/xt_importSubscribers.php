<?php
set_time_limit(0); // no limit

session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/ClassDasMC.php");

$cols = Array ("storeid", "loyalty_promotions_key");
$locationList = $db->Where("storeid", $_SESSION['storeid'])->getOne("locationlist", $cols);

if(empty($locationList['loyalty_promotions_key'])){
	$_SESSION['error'] = "Please enter a key.";
	header('location: /settings/promote/');
	exit;
}else{
	$mc_api_key = $locationList['loyalty_promotions_key'];
}

$mc = new Das_MC($mc_api_key);

$file_types = Array("text/csv","application/vnd.ms-excel","ms-excel");
$listid = $db->escape($_POST['listid']);

$error_msg = '';
$success_msg = '';

if(isset($_POST['importSubmit']) && !empty($_FILES['file'])){
    
    // Allowed mime types
    $csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');
    
    // Validate whether selected file is a CSV file
    if(!empty($_FILES['file']['name']) && in_array($_FILES['file']['type'], $csvMimes)){

        // If the file is uploaded
        if(is_uploaded_file($_FILES['file']['tmp_name'])){
            
            // Open uploaded CSV file with read-only mode
            $csvFile = fopen($_FILES['file']['tmp_name'], 'r');
            
            // Skip the first line
            fgetcsv($csvFile);
          
			$membersInsertedOk = array();
		  
            // Parse data from CSV file line by line
            while(($line = fgetcsv($csvFile)) !== FALSE){
				
                $params = Array("email_address"=>$line[0],
							   "status"=>"subscribed",
							   "merge_fields"=>[
								   "FNAME"=>$line[1],
								   "LNAME"=>$line[2]
							   ]);
				$member = $mc->addMember($listid,json_encode($params));
				
				//If there is an error trying to add an specific member, "fail" will be added to the array membersInsertedOk
				if($member ["is_error"] == 1){
					$error_msg .= "<p>There was an error adding the member: ". $line[0]. '. '. $member ["detail"] .'</p>';
					array_push($membersInsertedOk, "fail");
				}
				
				////If there is not any error trying to add an specific member, "success" will be added to the array membersInsertedOk
				if($member ["is_error"] == 0){
					$success_msg .= "<p>The member : ". $line[0]. ' was created sucessfully! </p>';
					array_push($membersInsertedOk, "success");
				}
				
            }
            
            // Close opened CSV file
            fclose($csvFile);
            
			//If there were not any error trying to add any of the imported members, the expected html will be print 
			if(!in_array("fail", $membersInsertedOk)){
				$_SESSION['success'] = "Your changes have been successfully saved.";
				header("Location:/promote/lists/members.php?id=".$listid);
				exit;
			}else{
				if(!in_array("success", $membersInsertedOk)){
					$_SESSION['error'] = $error_msg;
					header("Location:/promote/lists/members.php?id=".$listid);
					exit;
				}else{
					$_SESSION['success'] = $success_msg;
					$_SESSION['error'] = $error_msg;
					header("Location:/promote/lists/members.php?id=".$listid);
					exit;
				}
			}
			
        }else{
            $_SESSION['error'] = "There was an error saving your changes.";
			header("Location:/promote/lists/members.php?id=".$listid);
			exit;
        }
    }else{
        $_SESSION['error'] = "Only .csv files can be imported.";
		header("Location:/promote/lists/members.php?id=".$listid);
		exit;
    }
}else{
	$_SESSION['error'] = "There was an error saving your changes.";
	header("Location:/promote/lists/members.php?id=".$listid);
	exit;
}

?>