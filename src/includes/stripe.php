<?
include_once __DIR__ ."/vendor/autoload.php";

//first day calendar
$BASE_URL = ($_SERVER["HTTPS"] == "on") ? "https://".$_SERVER["SERVER_NAME"] : "http://".$_SERVER["SERVER_NAME"];