<?php
$host="localhost"; // Host name.
$db_user="root"; //mysql user
$db_password="dasflorida"; //mysql pass
$db='signarama'; // Database name.
$conn=mysql_connect($host,$db_user,$db_password) or die (mysql_error());
mysql_select_db($db) or die (mysql_error());


echo $filename=$_FILES["file"]["name"];
$ext=substr($filename,strrpos($filename,"."),(strlen($filename)-strrpos($filename,".")));
echo $ext;
//we check,file must be have csv extention
if($ext==".csv")
{
  $file = fopen($filename, "r");
         while (($emapData = fgetcsv($file, 100, ',', '')) !== FALSE)
         {
				if ($emapData[0] != null){
				$sql = "INSERT into review_recipient(name,email,storeid) values('$emapData[0]','$emapData[1]','".$_SESSION['storeid']."')";
				echo $sql;
				exit;
				mysql_query($sql);
			}
         }
         fclose($file);
         echo "CSV File has been successfully Imported.";
}
else {
    echo "Error: Please Upload only CSV File";
}


?>