<?
include ($_SERVER['DOCUMENT_ROOT']."/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/functions.php");
include ($_SERVER['DOCUMENT_ROOT']."/corebridge/includes/functions.php");
session_start();
$apiKey = '49898dd89044c0fc823b4735f320ddeb-us17';

$fields = array();
if(isset($_POST['orderid']))
	$orderid = $_POST['orderid'];
	
foreach($_POST as $field => $value) {
	$fields[$field] = filter_var($value, FILTER_SANITIZE_STRING);
}

	$sql = "SELECT * FROM ".$_SESSION['database'].".locationlist where storeid='".$_SESSION['storeid']."' limit 1";
	$result = $conn->query($sql);

	//$dt = new DateTime(filter_var($_POST['registration_date'], FILTER_SANITIZE_STRING) . ' ' . filter_var($_POST['registration_time'], FILTER_SANITIZE_STRING));
	//$dt->setTimezone(new DateTimeZone('UTC'));
	//$sched_date = $dt->format('Y-m-d H:i:s');

	if ($result->num_rows > 0){
		$row = $result->fetch_assoc();
		if(!$row['mailchimp_listid']){
			//Create list
			$url = 'lists/';
			$json = json_encode([
				'name' => $_SESSION['client']."-".$row['storeid'],
				'contact'  => [
					'company'     => $_SESSION['client']." ".$row['companyname'],
					'address1'     => $row['address'],
					'city'     => $row['city'],
					'state'     => $row['state'],
					'zip'     => $row['zip'],
					'country'     => "US",
				],
				'permission_reminder' => "You are receiving this as a test.",
				'campaign_defaults'  => [
					'from_name'     => $client." ".$row['companyname'],
					'from_email'     => $row['email'],
					'subject'     => "Welcome to ".$client." ".$row['companyname'],	
					'language'     => "EN",
				],
				'email_type_option' => true
			]);
			$result = mailChimp($apiKey,"POST",$url,$json);
			
			$listid=$result["id"];
			$sql = "UPDATE ".$_SESSION['database'].".locationlist set mailchimp_listid='".$listid."' where storeid='".$_SESSION['storeid']."'";
			mysqli_query($conn, $sql);
		}
		else
			$listid=$row['mailchimp_listid'];
		
		$yelpurl = $row['yelp'];

		$sql_review = "select concat('https://search.google.com/local/writereview?placeid=',place_id) as googleurl,concat(link_page,'reviews/') as fburl from facebook_post.gmb_locations a,facebook_post.fb_pages b where a.client='".$_SESSION['client']."' and a.store_id='".$_SESSION['storeid']."' and b.client=concat(a.client,'-',a.store_id) and a.store_id=b.store_id";
		$result_review = $conn->query($sql_review);
		if ($result_review->num_rows > 0){
			$review = $result_review->fetch_assoc();
			$google_url = $review['googleurl'];
			$fb_url = $review['fburl'];
		}
		
		$templates = '<!doctype html><html><head><meta charset="utf-8"><title>Invitation</title><style>body {background: #dfdede;height: 100%;width: 100%;margin: 0;padding: 0;}</style></head><body><!--wrapper--><table cellpadding="0" cellspacing="0" align="center" width="100%" bgcolor="dfdede"><tr><td height="30">&nbsp;</td></tr><tr> <td> <!--top--> <table cellpadding="0" cellspacing="0" align="center" width="600" style="margin: 0 auto;"> <tr bgcolor="#ffffff"> <td> <img src="http://'.$_SERVER['HTTP_HOST'].'/corebridge/email/img/logo.jpg" width="305" style="display: block;" /> </td><td width="273" align="center"><span style="font-family: \'Century Gothic\', Helvetica, Arial, sans-serif; font-size: 12px; line-height: 14px; color: #c3122e;"><strong>'.$fields['name']. '</strong>, '.$fields['companyname'].'</span></td><td width="22"> <img src="http://'.$_SERVER['HTTP_HOST'].'/corebridge/email/img/header-right.jpg" width="22" style="display: block;" /> </td> </tr><tr><td>&nbsp;</td></tr></table> <!--/top--> </td> </tr><tr> <td> <table cellpadding="0" cellspacing="0" align="center" width="600" style="margin: 0 auto;" bgcolor="#c3122e"><tr><td colspan="3"><img src="https://'.$_SERVER['HTTP_HOST'].'/corebridge/email/img/red-top.jpg" style="display: block;" /></td></tr><tr><td colspan="3">&nbsp;</td></tr><tr><td colspan="3" align="center"><img src="https://'.$_SERVER['HTTP_HOST'].'/corebridge/email/img/bubble.jpg" style="display: block;" /></td></tr><tr><td colspan="3">&nbsp;</td></tr><tr><td colspan="3" align="center"><img src="https://'.$_SERVER['HTTP_HOST'].'/corebridge/email/img/how.png" style="display: block;" /></td></tr><tr><td colspan="3" height="30">&nbsp;</td></tr><tr><td width="30">&nbsp;</td><td width="540" align="center"><span style="font-family: \'Century Gothic\', Helvetica, Arial, sans-serif; font-size: 17px; line-height: 28px; color: #ffffff;"><strong style="font-size: 24px; line-height: 34px;">'.$fields['header'].'</strong><br><br>'.$fields['body'].' </span></td><td width="30">&nbsp;</td></tr><tr><td colspan="3" height="30">&nbsp;</td></tr><tr><td colspan="3" align="center"><a href="'.$fb_url.'"><img src="https://'.$_SERVER['HTTP_HOST'].'/corebridge/email/img/facebook.png" /></a>&nbsp;&nbsp;&nbsp;<a href="'.$google_url.'"><img src="https://'.$_SERVER['HTTP_HOST'].'/corebridge/email/img/google.png" /></a>&nbsp;&nbsp;&nbsp;<a href="'.$yelpurl.'"><img src="https://'.$_SERVER['HTTP_HOST'].'/corebridge/email/img/yelp.png" /></a></td></tr><tr><td colspan="3" height="50">&nbsp;</td></tr></table></td></tr><tr> <td> <table cellpadding="0" cellspacing="0" align="center" width="600" style="margin: 0 auto;" bgcolor="#ffffff"><tr><td colspan="5" height="30">&nbsp;</td></tr><tr><td width="30">&nbsp;</td><td colspan="3" align="center"><strong style="font-family: \'Century Gothic\', Helvetica, Arial, sans-serif; font-size: 24px; line-height: 30px; color: #c3122e;"><em>'.$fields['body_bottom'].'</em></strong></td><td width="30">&nbsp;</td></tr><tr><td colspan="5" height="30">&nbsp;</td></tr><tr><td width="30">&nbsp;</td><td width="260"><img src="https://'.$_SERVER['HTTP_HOST'].'/img/gallery/products/'.$fields['image1'].'" style="display: block;width:100%;max-width:260px;" width="260"/></td><td width="20"></td>';
		 
		 
		 if ($fields['image2'] <> ''){
			 $templates .= '<td width="260"><img src="http://'.$_SERVER['HTTP_HOST'].'/img/gallery/products/'.$fields['image2'].'" style="display: block;width:100%;max-width:260px;" width="260"/></td>';
		 }else{
			 $templates .= '<td width="260">&nbsp;</td>';
		 }
		 $templates .= '<td width="30">&nbsp;</td></tr><tr><td colspan="5">&nbsp;</td></tr><tr><td colspan="5"><img src="http://'.$_SERVER['HTTP_HOST'].'/email/reviews/img/bottom.jpg" style="display: block;" /></td></tr></table></td></tr> <tr> <td colspan="3" height="40">&nbsp;</td> </tr></table><!--/wrapper--></body></html>';

		
		//Add users to list	
		$URL = 'https://' . substr($apiKey,strpos($apiKey,'-')+1) . '.api.mailchimp.com/3.0/batches';
		$data = new stdClass();
		$data->operations = array();
		
		$sql_order = "SELECT * FROM ".$_SESSION['database'].".corebridge_order where orderid ='".$orderid."' and storeid='".$_SESSION['storeid']."' limit 1";
		$result_order = $conn->query($sql_order);		
		if ($result_order->num_rows > 0){
			$row_order = $result_order->fetch_assoc();
			$to_email = $row_order['OrderContactEmail'];
		}
		$to_email = 'seema@das-group.com'; // comment before going live
		$to_email = explode(',', $to_email);

		foreach ( $to_email as $user ) {
			$batch =  new stdClass();
			$batch->method = 'POST';
			$batch->path = 'lists/' . $listid . '/members';
			$batch->body = json_encode( array(
				'email_address' => $user,
				'status'        => 'subscribed'
			) );
			$data->operations[] = $batch;
		}
		$data = json_encode($data);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$URL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);                                                                                                         
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
			'Authorization: Basic '.$apiKey,
			'Content-Type: application/json')                                                                       
		);  																														 
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);          
		$result = curl_exec($ch);
		sleep(5);	
		//Create Segment
		$url = 'lists/'.$listid.'/segments/';
		$json = json_encode([
			'name' => 'Review Email',
			'static_segment' => $to_email,
		]);
		$result = mailChimp($apiKey,"POST",$url,$json);
		$segmentid=$result["id"];
		
		if(!$row['mailchimp_corebridge_templateid']){
		//Create template
		$url = 'templates/';
		$json = json_encode([
			'name' => "Review Template",
			'html' => $templates,
		]);
		$result = mailChimp($apiKey,"POST",$url,$json);
		$templateid=$result["id"];
		$sql = "UPDATE ".$_SESSION['database'].".locationlist set mailchimp_corebridge_templateid='".$templateid."' where storeid='".$_SESSION['storeid']."'";
		mysqli_query($conn, $sql);
		}else{
			//Update Template
			$templateid = $row['mailchimp_corebridge_templateid'];
			$templateid = (int)$templateid;
			$url = 'templates/'.$templateid;
			$json = json_encode([
				'name' => "Review Template",
				'html' => $templates,
			]);
			$result = mailChimp($apiKey,"PATCH",$url,$json);
		}
		$templateid = (int)$templateid;
		echo 'Templateid-' . $templateid . '<br/>';
		$url = 'campaigns/';
		$json = json_encode([
			'type' => "regular",
			'recipients' => [
				"list_id"=>$listid,
				"segment_opts" => [
					"saved_segment_id"=>$segmentid
				]
			],
			'settings' => [
				"subject_line"=>"Request for Review",
				"from_name"=>$row['fname1']. ' ' .$row['lname1'],
				"reply_to"=>"no-reply@das-group.com",
				"auto_footer"=>false,
				"template_id"=>$templateid,
			],
			'tracking' => [
				"opens"=>true,
				"html_clicks"=>true,
				"text_clicks"=>true,
				"goal_tracking"=>true,
			]
		]);
		$result = mailChimp($apiKey,"POST",$url,$json);

		$campaignid=$result["id"];
		
		$url = 'campaigns/'.$campaignid.'/actions/send/';

		/*$url = 'campaigns/'.$campaignid.'/actions/schedule/';
		$json = json_encode([
			'schedule_time' => $sched_date
		]);*/

		$result = mailChimp($apiKey,"POST",$url,$json);
		$sqlins = "insert into ".$_SESSION['database'].".email_campaigns(campaignid,storeid,date) values('".$campaignid."','".$_SESSION['storeid']."',now())";
		$result_ins = $conn->query($sqlins);
	}

	$sql= "update corebridge_order set review_sent = 'Y' where orderid ='".$orderid."' and storeid = '".$_SESSION['storeid']."'";

	if(mysqli_query($conn, $sql)){
		$_SESSION['success']="Your email has been sent.";
		track($_SESSION["username"],$_SESSION["storeid"],"review_template",$sql);
	}else
		$_SESSION['error'] = "Sorry, there was an error sending your email.";


header("location:/reviews/orders.php");