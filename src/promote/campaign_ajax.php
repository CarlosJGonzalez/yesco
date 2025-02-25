<?
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/ClassDasMC.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");

if(isset($_SESSION["user_role_name"])){
	
	$cols = Array ("storeid", "loyalty_promotions_key");
	$locationList = $db->Where("storeid", $_SESSION['storeid'])->getOne("locationlist", $cols);

	if(empty($locationList['loyalty_promotions_key'])){
		$_SESSION['error'] = "Please enter a key.";
		header('location: /settings/promote/');
		exit;
	}else{
		$mc_api_key = $locationList['loyalty_promotions_key'];
	}

	$mc = new Das_MC($mc_api_key);

	if(isset($_POST["campaign_data_ajax"]) && $_POST["campaign_data_ajax"] != ""){
		
		$campaign_id = $_POST["campaign_data_ajax"];

		$campaign_details = $mc->getCampaign($campaign_id);

		if ($campaign_details['is_error'] == 0){
											
			echo "<tbody id='deleteCampaignModalTBody'>
					<tr>
						<td>".ucwords($campaign_details['settings']['title'])."</td>
						<td>".ucfirst($campaign_details['status'])."</td>
					 </tr>
				 </tbody>";
		}else{
			echo '<p class="alert alert-danger">This campaign does not exist.</p>';
		}
	}

}