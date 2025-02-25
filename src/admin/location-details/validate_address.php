<?php
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");

$components = Array();
foreach($_POST as $key => $val){
	$components[$key] = $db->escape($val);
}

$formatted_add = ss_getStreetAddress($components);
$form_compenents["street"] = $formatted_add[0]->components->primary_number." ".$formatted_add[0]->components->street_name." ".$formatted_add[0]->components->street_suffix;
$form_compenents["secondary"] = $formatted_add[0]->components->secondary_designator." ".$formatted_add[0]->components->secondary_number;
$form_compenents["city"] = $formatted_add[0]->components->city_name;
$form_compenents["zipcode"] = $formatted_add[0]->components->zipcode;
$diff=array_diff($components,$form_compenents);
if(!$diff) echo "true";
else{
	echo json_encode($formatted_add);
}
