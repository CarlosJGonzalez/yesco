<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");

$sort = $db->escape($_POST['sort']);
switch($sort){
	case "oldest":
		$orderBy["field"] = "date";
		$orderBy["val"] = "asc";
		break;
	case "a-z":
		$orderBy["field"] = "name";
		$orderBy["val"] = "asc";
		break;
	case "z-a":
		$orderBy["field"] = "name";
		$orderBy["val"] = "desc";
		break;
	default:
		$orderBy["field"] = "date";
		$orderBy["val"] = "desc";
}

$db->orderBy($orderBy["field"],$orderBy["val"]);

if(!empty($_POST['category'])){
	$category = $db->escape($_POST['category']);
	//$db->where("category",$category);
	$db->where("FIND_IN_SET('".$category."',category)");
	if($category == 'videos'){
		$db->orWhere ("video_raw", NULL, 'IS NOT');
		$db->orWhere ("video_raw",'','!=');
		/*SELECT  * FROM gallery 
		WHERE  FIND_IN_SET('videos',category) 
		OR video_raw IS NOT NULL 
		OR video_raw != ''  
		AND active = '1'  
		ORDER BY date DESC  
		LIMIT 0, 24*/
	}
}

if(!empty($_POST['month'])){
	$month = $db->escape($_POST['month']);
	$db->where("month",$month);
}

if(!empty($_POST['search'])){
	$search = $db->escape($_POST['search']);
	/*$db->where ("name", '%'.$search.'%', 'like');
	$db->orWhere ("tags", '%'.$search.'%', 'like');
	$db->orWhere ("category", '%'.$search.'%', 'like');*/
	$db->Where("(name like '%$search%' OR tags like '%$search%' OR category like '%$search%')");
}

$db->where("active",1);

if($_POST['status']=="user"){
	$db->Where("(storeid = ? OR storeid IS NULL OR apply_all = 1)",array($_SESSION['storeid']));
}

//total records found
$images = $db->get("gallery");

if($db->count > 0)
	echo count($images);
else
	echo '0';
?>