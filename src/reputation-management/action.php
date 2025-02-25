<?php
date_default_timezone_set('America/New_York');
include_once ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include_once ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");
require_once $_SERVER['DOCUMENT_ROOT'].'/includes/ClassDasMC.php';
session_start();

$apiKey = '686a8f18f70a53080e689fe237f9ded9-us20';
$client = 'Fully Promoted';

if($_GET['action']=="delete"){

	$db->where('id', explode(',',$_GET['values']), 'IN');
	if($db->delete('review_recipient')){
		$_SESSION['success']="Your changes have been saved.";
	}else{
		$_SESSION['error'] = "Sorry, there was an error saving your changes.";	
	}

}else if($_GET['action'] == "send"){

	$row = $db->where ('storeid', $_SESSION['storeid'])->getOne('locationlist');
	
	if (count($row)){
		$info_id=explode(',',$_GET['values']);

		$mailchimp= new Das_MC($apiKey);
		$upd_inf_location = array();
		if(!$row['mailchimp_listid']){

			$json = json_encode([
				'name' => $_SESSION['client']."-".$row['storeid'],
				'contact'  => [
					'company'     => $client."-".$row['companyname'],
					'address1'    => $row['address'],
					'city'        => $row['city'],
					'state'       => $row['state'],
					'zip'         => $row['zip'],
					'country'     => "US",
				],
				'permission_reminder' => "You are receiving this from ".$client."-".$row['companyname'],
				'campaign_defaults'   => [
					'from_name'       => $client." ".$row['companyname'],
					'from_email'      => $row['email'],
					'subject'         => "Welcome to ".$client." ".$row['companyname'],	
					'language'        => "EN",
				],
				'email_type_option'   => true
			]);

			$result = $mailchimp->addList($json);
			
			$listid=$result["id"];
			$upd_inf_location['mailchimp_listid']=$listid;
		}else
			$listid=$row['mailchimp_listid'];
		
		$yelpurl = $row['yelp'];
		$owner_name = $row['fname1']." ".$row['lname1'];

		$row_users = $db->where('id', $info_id, 'IN')
		   				->getOne('review_recipient','group_concat(email) as emails,group_concat(name) as names');
  				
		$to_email = $row_users['emails'];
		$to_name = $row_users['names'];

		$t = $db->where('storeid',$_SESSION['storeid'])
				->orwhere('storeid','')
				->orderBy("storeid","desc")->getOne('review_template');
		
		if(!count($t)){

			$msg ="There was an error looking for your template information.";
			pageRedirect($msg , 'error', '/reputation-management/');
		}

		$owner_name = ($t['owner_name'] != "") ? $t['owner_name'] : $row['fname1']. ' ' .$row['lname1'];

	
		$sql_review = "SELECT (select concat(link_page,'reviews/') from facebook_post.fb_pages b where b.store_id='".$_SESSION['storeid']."' and b.client = '".$_SESSION['client'].'-'.$_SESSION['storeid']."') as fburl, (SELECT concat('https://search.google.com/local/writereview?placeid=',place_id) FROM facebook_post.gmb_locations a  WHERE a.client='".$_SESSION['client']."' and a.store_id='".$_SESSION['storeid']."') as googleurl";
		
		$review = $db->rawQuery($sql_review);
		if (count($review)){
			$google_url = $review['googleurl'];
			$fb_url = $review['fburl'];
		}
		
				if($google_url != "" || $fb_url != ""){
$fb_html=($fb_url != "")?'<a href="'.$fb_url.'"><img src="http://'.$_SERVER['HTTP_HOST'].'/email/review/img/facebook.png" /></a>':"";
$goo_html=($google_url != "")?'<a href="'.$google_url.'"><img src="http://'.$_SERVER['HTTP_HOST'].'/email/review/img/google.png" /></a>':"";
		}
		/*else{
			$msg = 'Sorry, you need to check your links(facebook and google)';
			pageRedirect($msg , 'error', '/reputation-management/');
		}*/

		//storeid=30378&client=9018
		$var = array(
						'name' => $owner_name, 
						'companyname' => $row['companyname'], 
						'header' => $t['heading'], 
						'body' => $t['body'], 
					);

		$templates=getTemplate($db,'30378','9018',$var);

			
		$data = new stdClass();
		$data->operations = array();

		$to_email = explode(',', $to_email);
		$to_name = explode(',', $to_name);
		foreach ( $to_email as $count => $user ) {
			if($count <= 100){
				$name = explode(" ", $to_name[$count]);

				$member_parms = json_encode( array(
					'email_address' => $user,
					'status'        => 'subscribed',
					'merge_fields'  => [
								            'FNAME'     => isset($name[0])? $name[0]:"",
								            'LNAME'     => isset($name[1])? $name[1]:""
								        ]
				) );
		
				$result=$mailchimp->addMember($listid,$member_parms);
			}else{
				
				$db->where('email',$user);
				if ($db->update ('review_recipient', array('w_sent' => 1) ) ){
					$tmp =1;
					//track($_SESSION["username"],$_SESSION["storeid"],"review_recipient",$sql);
				}
			}
		}

		$data = json_encode($data);
		$result = $mailchimp->batches($data);

		/*$status = $result['status'];
		$id_batch= $result['id'];
		while ( $status != 'finished') {
			$result=$mailchimp->getBatchStatus($id_batch);
			$status = $result['status'];
			//sleep(1);
		}*/

		$json = json_encode([
			'name' => 'Review Email '.strtotime(date("Y-m-d h:i:s")),
			'static_segment' => $to_email,
		]);


		
		$result = $mailchimp->addSegment($listid,$json);
		$segmentid=$result["id"];
	

		if(!$row['mailchimp_templateid']){

			$json = json_encode([
				'name' => "Review Template",
				'html' => $templates,
			]);

			$result = $mailchimp->addTemplate($json);
			$templateid=$result["id"];			
			$upd_inf_location['mailchimp_templateid']=$templateid;
		}else{
			//Update Template
			$templateid = $row['mailchimp_templateid'];
			$templateid = (int)$templateid;
			$url = 'templates/'.$templateid;
			$json = json_encode([
				'name' => "Review Template",
				'html' => $templates,
			]);

			$result = $mailchimp->updateTemplate($templateid,$json);
		}
		$templateid = (int)$templateid;
		

		if(count($upd_inf_location)){
			$db->where('storeid', $_SESSION['storeid'])->update('locationlist', $upd_inf_location);
		}

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
				"from_name"=>$owner_name,
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

		$result =$mailchimp->addCampaign($json);

		$campaignid = $result["id"];
	

		if($t['info_send_email'] != ""){
		
			$action=$t['info_send_email'];

			$action=json_decode($action,true)[0];

			
			if($action == '0'){

				$result =$mailchimp->actionsCampaign($campaignid,"send");

			}else{
				$url = 'campaigns/'.$campaignid.'/actions/schedule';

				$time_send= '+'.$action.' day';			
				$sched_date=date('Y-m-dT14:00:00+00:00',strtotime($time_send));
								
				$parameters= json_encode([
											'schedule_time' => $sched_date,
											'timewarp'=>'false',//Pay function
											'batch_delay' =>'false'
										]);

				$result =$mailchimp->actionsCampaign($campaignid,"schedule",$parameters);
			}
		}else{

			$result =$mailchimp->actionsCampaign($campaignid,"send");
		}


		$data = array(
						'campaignid' => $campaignid, 
						'storeid' => $_SESSION['storeid'], 
						'date' => $db->now(), 
					 );
		
		$id = $db->insert('email_campaigns', $data);
		$flag = false;

		if ($id)
		    $flag =true;

		$data = array(
						'sent_flag' => 'S', 
						'c_sent' => 1
					 );

		$db->where('id', $info_id, 'IN');

		if ($db->update ('review_recipient', $data) && $flag){

			$msg = "Your email has been scheduled.";
			//track($_SESSION["username"],$_SESSION["storeid"],"review_template",$sql);
			pageRedirect($msg , 'success', '/reputation-management/');
		}else{

		    $msg = "Sorry, there was an error scheduling your email.";
		    pageRedirect($msg , 'error', '/reputation-management/');
		}
	}
}
header("location:/reputation-management/");


function getTemplate(&$db,$storeid,$client,$data){
	$_POST['storeid']=$storeid;
	$_POST['client']=$client;
	$_POST['vars']=$data;
	ob_start();
	include "./template.php";
	$output = ob_get_clean();

	unset($_POST['storeid']);
	unset($_POST['client']);
	unset($_POST['vars']);
	return $output;
}
?>	