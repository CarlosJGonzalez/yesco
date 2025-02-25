<?php 
session_start();
require ($_SERVER['DOCUMENT_ROOT']."/includes/ClassDasCampaign.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");

$campaigns_info = new Das_Campaign($db,$token_api,$_SESSION['client']);

$mediums = $campaigns_info->getMedium($_POST["id"]);
$html = "<option  value=''>Select Medium</option>";
foreach ($mediums as $medium) {
	$html .= '<option value = "'.$medium['name'].'">'.$medium['name'].'</option>';
}

echo $html;exit();
?>