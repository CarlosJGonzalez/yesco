<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");

function checkaddslashes($str){       
    if(strpos(str_replace("\'",""," $str"),"'")!=false)
        return addslashes($str);
    else
        return $str;
}

function utf8_fopen_read($fileName) {
    $fc = iconv('windows-1250', 'utf-8', file_get_contents($fileName));
    $handle=fopen("php://memory", "rw");
    fwrite($handle, $fc);
    fseek($handle, 0);
    return $handle;
} 

/**
* @param string $filename
* @param string $delimiter
*
* @return array|bool
*/
function csv_to_array($filename = '', $delimiter = ',', $enclosure='"', $escape = '\\') {
    if (!file_exists($filename) || !is_readable($filename))
        return false;

    $header = null;
    $data = array();
    $info = array();

    if (($handle = utf8_fopen_read($filename)) !== false) {
        while (($data = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
	        if(!$header){
	        	$info['header']= $data;
	        	$header=1;
	        }else{	        	
	        	$data1 = array_filter($data);
	        	if(count($data1)){
					array_push($info,$data);
	        	}	
	        }
    	}
    fclose($handle);
    }
    return $info;
}


$file= $_FILES['documents'];

if ($file['error'] == 0 ){
	$data = csv_to_array($file['tmp_name'], ',');

	
	$header= $data['header'];
	unset($data['header']);

	$keys = Array();

	//Headers
	$clumns="(id,";
	$date_position=0;
	$optional_position=false;
	$optional_flag=false;
	$keys[]= 'id';
	foreach ($header as $key => $value) {
		if($value == 'date'){
			$date_position = $key;
		}

		if($value == 'optional'){
			$optional_position = $key;
			continue;
		}

		$keys[]= $value;
		$clumns.=$value.",";
	}

	$clumns=substr($clumns, 0, -1);
	$clumns.=")";

	//Values
	$all_values="";
	$count =0;

	$rautoId = $db->rawQueryOne("SELECT MAX(id) as id FROM ".$_SESSION['database'].".social_media__local_posts");
	
	$nextId = $rautoId['id'];
	$save_ids = array();
	$post = array();
	$post_optionals = array();
	foreach ($data as $value) {
		$val="(";
		$nextId += 1;
		nextId($db,$nextId);
		$val.="$nextId ,";
		$postTmp = array();
		$postTmp[]=$nextId;
		$save_ids[] = $nextId;
		foreach ($value as $key => $tmp) {

			if($key == $date_position){		
				$date_new = date('Y-m-d H:i:s', strtotime($tmp));
				$val.="\"".checkaddslashes($date_new)."\"";
				$postTmp[]=checkaddslashes($date_new);
				$val.=",";

			}else{
				if($key == $optional_position){
					if($tmp == '1'){
						$optional_flag = true;
					}
					continue;
				}
				//$tmp1=mysqli_real_escape_string($conn,$tmp);
				$tmp1=$db->escape($tmp);
				$val.="\"".checkaddslashes($tmp1)."\"";
				$postTmp[]=checkaddslashes($tmp1);
				$val.=",";
			}			
			
		}

		//unset($postTmp[$optional_position]);

		if( !$optional_flag ){			
			$post[] = $postTmp;
		}else{
			$post_optionals[] = $postTmp;
		}
		$optional_flag=false;
		
		$val=substr($val, 0, -1);
		$val.="),";
		$all_values.=$val;
		$count++;
	}
	
	$all_values=substr($all_values, 0, -1);

	$tmp=explode(".",$file['name']);
	$type=explode("_",$tmp[0]);	
	    
	$table = false;
	$sql=null;
	$case = $false;
	if(count($type) > 0){
		$case=end($type);
		
		switch ($case) {
			case 'local':
				$table = $_SESSION['database'].".social_media__local_posts";
				$sql="INSERT INTO ".$table." $clumns VALUES $all_values;";
				break;
			case 'corp':
				$table = $_SESSION['database'].".social_media_posts";
				$sql="INSERT INTO ".$table." $clumns VALUES $all_values;";
				break;
		}

	}
	
	if($sql){
		try{

			$db->startTransaction();
			//$db->rawQuery($sql);
			$flag= true;
			if( count($post) ){
				if(!$db->insertMulti($table, $post, $keys)){
					$flag = false;
				}
			}
			

			if( count($post_optionals) ){
				if(!$db->insertMulti($_SESSION['database'].".social_media__local_posts_optional", $post_optionals, $keys)){
					$flag = false;
				}
			}

			$flag ? $db->commit() : $db->rollback();

			if($case == 'local'){
				$storeidExcl = getExclusionAllPost($db,$_SESSION['client']);
				sendToOptOut($db,$storeidExcl,$save_ids);
			}
			
			$_SESSION["success"]="Your publications were added.";

		} catch (Exception $e) {
			print_r($db->getLastError());
			$_SESSION["error"]="There was an error adding your Posts";
		}

	}else{
		$_SESSION["error"]="Sorry please check your file name";
	}
	

}else{
	$_SESSION["error"]="There was an error adding your Posts";
}
//echo "<pre>";print_r($_SESSION);echo "</pre>";
header("location:/admin/plan-and-publish/social-media/?".$_SERVER['QUERY_STRING']);
exit;

function sendToOptOut(&$db,$storeidExcl,$postIds){
	$data= array();
	foreach ($storeidExcl as $storeid) {
		$storeid = $storeid['storeid'];

		foreach ($postIds as $postid) {
			$data[] = array(
							'id' => $postid,
							'storeid' => $storeid,
							'optout' => 1,
							'date' => $db->now(),
							);
		}
	}
	$table = $_SESSION['database'].'.social_media_local_posts_optout';
	$ids = $db->insertMulti($table, $data);
	return $ids;
}

function getExclusionAllPost(&$db,$client,$storeid = null){
	$db->where('client',$client)->where('active',1);

	if( isset($storeid) ){
		$db->where('storeid',$storeid);
	}

	return $db->get('das_contract.exclusion_post');
}
function nextId(&$db,&$lastId){
	$rautoId = $db->rawQueryOne("SELECT id FROM ".$_SESSION['database'].".social_media_local_posts_store where id = '$lastId'");
	$rautoId2 = $db->rawQueryOne("SELECT id FROM ".$_SESSION['database'].".social_media_posts where id = '$lastId'");
	$rautoId3 = $db->rawQueryOne("SELECT id FROM ".$_SESSION['database'].".social_media__local_posts_optional where id = '$lastId'");

	if(isset($rautoId['id']) || isset($rautoId2['id']) || isset($rautoId3['id']) ){
		$lastId +=1;
		$lastId = nextId($db,$lastId);
	}
	return $lastId;

}
