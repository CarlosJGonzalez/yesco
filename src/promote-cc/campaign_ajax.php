<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/ClassDasConstantContact.php");

if(isset($_SESSION["user_role_name"])){
	$locationList = $db->where("storeid",$_SESSION['storeid'])->getOne("locationlist");

	//Only for test purpose
	/*$active_location['constant_contact_api_key'] = 'j3bn9adcxrgg2jvxd6nmg75b';
	$active_location['constant_contact_access_token'] = '138e5b8a-ad09-419b-92f7-399d64875e4f';*/

	if(empty($locationList['constant_contact_api_key']) || empty($locationList['constant_contact_access_token'])){
		echo '<div class="alert alert-danger answer-fail"><a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>Please enter a valid api key and token.</div>';
		exit;
	}else{
		$cc_api_key = $locationList['constant_contact_api_key'];
		$cc_access_token = $locationList['constant_contact_access_token'];
	}

	//ClassDasConstantContact 
	$cc = new Das_ConstantContact($cc_api_key, $cc_access_token);

	if(isset($_POST["campaign_data_ajax"]) && $_POST["campaign_data_ajax"] != ""){
		
		$campaign_id = $_POST["campaign_data_ajax"];

		$campaign_details = $cc->getCampaign($campaign_id);

		if ($campaign_details['is_error'] == 0){
											
			echo "<tbody id='deleteCampaignModalTBody'>
					<tr>
						<td>".ucwords($campaign_details['name'])."</td>
						<td>".ucfirst($campaign_details['status'])."</td>
					 </tr>
				 </tbody>";
		}else{
			echo '<p class="alert alert-danger">This campaign does not exist.</p>';
		}
	}

}