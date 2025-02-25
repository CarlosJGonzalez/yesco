<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");

$data= $_POST['data'];
$info= $_POST['info'];

if(!isset($info['token'])){	
	return false;
}
else if($info['token']!="DAS%])p6Eu8SUuqN9U"){
	return false;
}
else{
	$table_name = 'fullypromoted.'.$info['endpoint'];  
	$sql = 'SHOW COLUMNS FROM '.$table_name;
	$rows = $db->rawQuery($sql);
	foreach ($rows as $row) {
		$columns[]=$row['Field'];
	}

	//Update profile
	if($info['action']=="modify"){
		$str = [];
		foreach ($data as $key => $value){
			if(in_array($key,$columns)){
				$str[$key] = $value;
			}
		}
	
		$db->where('storeid', $info['storeid']);

		if ($db->update($table_name, $str))
		    return true;
		else
	    	return false;
		
		if (mysqli_query($conn, $sql)) {
			return true;
		} else {
			return false;
		}
		
	}else if($info['action']=="new"){
		$info = [];
		foreach ($data as $key => $value){
			if(in_array($key,$columns)){
				$info[$key]=$value;
			}
		}

		$id = $db->insert ($table_name, $info);

		if ($id)
		    return true;
		else
		    return false;
	}else
		return false;
}
return;