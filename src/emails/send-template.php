<?php
exit();
include($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
function getEmailTemplate($template,$args){
	if(!file_exists($template)) return;
	$defaults = array("YEAR"=>date("Y"));
	$swap = array_merge($args,$defaults);
	$lines = file($template); 
	foreach ($lines as $line) { 
	   $message .= $line; 
	} 
	if(count($swap)>0){
		foreach($swap as $key=>$value){
			$message = str_replace("%%".$key."%%",$value,$message);
		}
	}
	return $message;
}
$locations = $db->rawQuery("SELECT  a.email, b.token FROM locationlist a 
    INNER JOIN
    (
        SELECT *
        FROM storelogin
        GROUP BY storeid
    ) b ON a.storeid = b.storeid
WHERE suspend!=1 and a.country='usa' and a.adfundmember='Y'");
//$locations = $db->rawQuery("SELECT * from locationlist WHERE suspend!=1 and country='usa' and adfundmember='Y'");
foreach($locations as $location){
	//Send email to customer
//	$args = array("FILELINK"=>"https://localfullypromoted.com/emails/assets/FP-1st-Quarter-White-Paper-2021.pdf",
//				  "FILENAME"=>"the Quarter 1 Whitepaper – Developing a Promotional Strategy For Your Home Services Company",
//				 "COMPANYNAME"=>$location['companyname']);
//	$template = getEmailTemplate($_SERVER['DOCUMENT_ROOT']."/emails/whitepaper-q1-2021.php",$args);
	$args = array("LOGINURL"=>"https://localfullypromoted.com/xt_login.php?token=".$location['token']);
	$template = getEmailTemplate($_SERVER['DOCUMENT_ROOT']."/emails/social-media-calendar.php",$args);

	$to = $location['email'];
	//$to = "adrian@das-group.com";
	
//	$subject = "Fully Promoted - Quarter 1 Whitepaper";
	$subject = "The social media content calendar is now available!";

	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
	$headers .= 'From: DAS Group <noreply@localfullypromoted.com>' . "\r\n";

	mail($to,$subject,$template,$headers);
	echo $location['email']."<br>";
}