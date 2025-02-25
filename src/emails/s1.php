<?php exit;
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
$locations = $db->rawQuery("SELECT linkedin,email from locationlist where storeid in ('30222','30127','30512','30005','30632','30002','30156','30662','30248','30062','30405','30591','30136','30142','30534','30283','30448','30051','30650','30572','30464','30370','30313','30131','30106','30584','30611','30074','30276','30407','30101','30573','30660','30372','30614','30004','30468','30419','30616','30507','30547','30560','30557','30158','30162','30435','30562','30238','30012','30124','30321','30284','30116','30010','30053','30594','30003','30327','30111','30598','30157','30539','30504','30334','30021','30039','30393','30533','30125','30344','30251','30537','30498','30550','30022','30553','30542','30249','30184','30228','30188','30618','30606','30095-02','30033','30081','30275','30439','30042','30593','30180','30009','30146','30517')");
foreach($locations as $location){
	//Send email to customer
	$args = array("LINKEDINURL"=>$location['linkedin']);
	$template = getEmailTemplate($_SERVER['DOCUMENT_ROOT']."/emails/linkedin.php",$args);

	$to = $location['email'];
//	$to = "jessicas@das-group.com";
	$subject = "New LinkedIn Business Page";

	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
	$headers .= 'From: DAS Group <noreply@localfullypromoted.com>' . "\r\n";

	mail($to,$subject,$template,$headers);
	echo $location['email']."<br>";
}