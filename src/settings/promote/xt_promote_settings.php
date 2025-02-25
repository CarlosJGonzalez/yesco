<?
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");
session_start();

//Only atuthorized users have access
if((roleHasPermission('show_promote_settings', $_SESSION['role_permissions']))){
	
	//Set Constact Contact key and token
	if(isset($_POST['updateConstantContactInfo'])){
		$constant_contact_api_key = $_POST["constant_contact_api_key"];
		$constant_contact_access_token = $_POST["constant_contact_access_token"];
		
		$data = Array();
			
		if($constant_contact_api_key != '' || $constant_contact_access_token != ''){
			$data['constant_contact_api_key'] = $constant_contact_api_key;
			$data['constant_contact_access_token'] = $constant_contact_access_token;
		}else{
			pageRedirect("All fields must be filled out.", "error", "/settings/promote/");
		}
			
		$db->where ('storeid', $_SESSION['storeid']);
		
		if ($db->update ('locationlist', $data)){
			$dataAct = array("username"=>$_SESSION['email'],
							 "storeid"=>$_SESSION['storeid'],
							 "updates"=>json_encode($data),
							 "section"=>"profile",
							 "details"=>"Updated constact contact information of a location. Id: ". $_SESSION['storeid']
							 );
		
			track_activity($dataAct, $db);
			
			pageRedirect("Your changes have been saved.", "success", "/settings/promote/");
		}else
			pageRedirect("There was an error saving your changes.", "error", "/settings/promote/");
		
	}
	
	//Set Mailchimp Key and Token
	if(isset($_POST['updateMailchimpInfo'])){
		$mailchimp_key = $_POST["mailchimp_key"];
		
		$data = Array();
		
		if($mailchimp_key != ''){
			$data['loyalty_promotions_key'] = $mailchimp_key;
		}else{
			pageRedirect("All fields must be filled out.", "error", "/settings/promote/");
		}
		
		$db->where ('storeid', $_SESSION['storeid']);
			
		if ($db->update ('locationlist', $data)){
			$dataAct = array("username"=>$_SESSION['username'],
							 "storeid"=>$_SESSION['storeid'],
							 "updates"=>json_encode($data),
							 "section"=>"profile",
							 "details"=>"Updated mailchimp information of a location. Id: ". $_SESSION['storeid']
							 );
		
			track_activity($dataAct, $db);
			
			pageRedirect("Your changes have been saved.", "success", "/settings/promote/");
		}else
			pageRedirect("There was an error saving your changes.", "error", "/settings/promote/");
	
	}
	
	if(isset($_POST['updatePromotePlatform'])){
		$promote_platform = $_POST["promote_platform"];
		
		$data = Array();

		if($promote_platform != ''){
			$data['promote_platform'] = $promote_platform;
		}else{
			pageRedirect("All fields must be filled out.", "error", "/settings/promote/");
		}
		
		$db->where ('storeid', $_SESSION['storeid']);
			
		if ($db->update ('locationlist', $data)){
			$dataAct = array("username"=>$_SESSION['username'],
							 "storeid"=>$_SESSION['storeid'],
							 "updates"=>json_encode($data),
							 "section"=>"profile",
							 "details"=>"Updated promote platform information of a location. Id: ". $_SESSION['storeid']
							 );
		
			track_activity($dataAct, $db);
			
			pageRedirect("Your changes have been saved.", "success", "/settings/promote/");
		}else
			pageRedirect("There was an error saving your changes.", "error", "/settings/promote/");
	}
	
}else{
	pageRedirect("You must be authorized to see this page.", "error", "/settings/promote/");
}
?>