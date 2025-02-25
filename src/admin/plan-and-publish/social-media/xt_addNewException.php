<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");

    if( isset($_POST['inpAddException']) && $_POST['inpAddException'] == 'Save' ){
    	$storeid = isset($_POST['storeid']) ? filter_var($_POST['storeid'], FILTER_SANITIZE_STRING) : false;
    	if($storeid){
    		$data = array(
							'client' => $_SESSION['client'],
							'storeid' => $storeid,
							'active' => 1,
    					  );
    		$ids = $db->insert('das_contract.exclusion_post', $data);
    		$_SESSION['success'] = 'Location was successfully added.';
    	}else{
    		$_SESSION['error'] = 'There was an error adding this location.';
    	}
    	
    }

header("location:/admin/plan-and-publish/social-media/add_post_exception.php?".$_SERVER['QUERY_STRING']);
exit;

?>