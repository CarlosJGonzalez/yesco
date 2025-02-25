<?php
session_start();
set_time_limit(0);
date_default_timezone_set('America/New_York');
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include_once ($_SERVER['DOCUMENT_ROOT']."/includes/ClassDasStripe.php");
require_once ($_SERVER['DOCUMENT_ROOT']."/includes/DasApiSDK/vendor/autoload.php");
use Das\facebook\Post;
use Das\facebook\Ad;


$storeid = 0;
$campid = '334';
$markup = 15;

$radius = isset($_POST['radius']) ?  filter_var($_POST['radius'], FILTER_SANITIZE_NUMBER_INT) : false;
$age_max = isset($_POST['age_max']) ?  filter_var($_POST['age_max'], FILTER_SANITIZE_NUMBER_INT) : false;
$age_min = isset($_POST['age_min']) ?  filter_var($_POST['age_min'], FILTER_SANITIZE_NUMBER_INT) : false;

$post_desc = isset($_POST['post']) ?  filter_var($_POST['post'], FILTER_SANITIZE_STRING) : false;

$total_budget = isset($_POST['input_total_budget']) ?  filter_var($_POST['input_total_budget'], FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION ) : false;
$ad_duration  = isset($_POST['input_duration']) ? filter_var($_POST['input_duration'], FILTER_SANITIZE_NUMBER_INT) : false;

$cardId       = ( isset($_POST['payment_method']) || $_POST['payment_method'] != '') ? $_POST['payment_method'] : false;
$customerId   = ( isset($_POST['customer_id']) || $_POST['customer_id'] != '' ) ? $_POST['customer_id'] : false;

$post_id   = isset($_POST['post_id'])  ? $_POST['post_id'] : false;


$return = array('error' => 1);
if( ( $total_budget / $ad_duration ) < 5 ){
	$return['error'] = 1;
	$_SESSION['error'] = 'You must spend a minimum of $5/day.';
	echo json_encode($return);
	exit;	
}

if( !$cardId || !$customerId ){
	$return['error'] = 1;
	$_SESSION['error'] = 'Please Check you Payment Information.';
	echo json_encode($return);
	exit;
}	


$fbPost = new Post($token_api);

$client_storeid = $_SESSION['client'];
$inCoorp = true;
if( isset($_SESSION['storeid']) ){
	$storeid =  $_SESSION['storeid'];
	$client_storeid = $_SESSION['client'].'-'.$_SESSION['storeid'];	
	$inCoorp = false;
}
$name = '('.$client_storeid.')['.$campid.']';

//$boostName = 'Boost Post '.date('Y-m-d',$_POST['post_date']).' '.$name;
//$trackUrl = "?utm_source=facebook_jobs&utm_medium=cpc&utm_campaign=".urlencode($boostName)."&campid=$campid&client=$client_storeid";

$post_interests = ( isset($_POST['post_interests']) && $_POST['post_interests'] != '' ) ? $_POST['post_interests'] : false;
$media 	   		= ( isset($_POST['media']) && $_POST['media'] != '' ) ? $_POST['media'] : false;
$http_path 		= ( isset($_POST['http_path']) && $_POST['http_path'] != '' ) ? $_POST['http_path'] : false;

$post_params = array(
	'date' => (string)strtotime($_POST['post_date']),
	'post' => $post_desc,
	'link' => $_POST['link']
);

if( $post_interests ){
	$post_params['interests'] = $post_interests;
}

if( $media && $http_path ){
	$post_params['http_path'] = $http_path;
	$post_params['media'] = $media;
}

$postInfo = $fbPost->create($_SESSION['client'],$storeid, $post_params);                	            

if( isset($postInfo['data']['post_id']) ){
	$postInfo = $postInfo['data'];
	$isPromotable = $fbPost->isPromotable( $_SESSION['client'],$storeid, $postInfo['post_id'] );

	if( isset( $isPromotable['data']['is_eligible_for_promotion'] ) && $isPromotable['data']['is_eligible_for_promotion'] ){
		$isPromotable = $isPromotable['data'];

		//Amount - markup
		$total_budget_ad =( $total_budget / ( 1 + ($markup/100)) );

		//Amount - fee
		//$amount_markup_fee = round( ( ($total_budget * 0.029) + 0.30 ), 2);
		//$total_budget_ad = ( $total_budget_ad - $amount_markup_fee );

		$ad = array(
			'name' 	  => 'Boost Post '.date('Y-m-d',strtotime($_POST['post_date'])).' '.$name, 
			'postId'  => $isPromotable['promotable_id'], 
			'postType'=> 0, 
			'amount'  => round($total_budget_ad,2),
			'startDate' => (string)strtotime($_POST['post_date'].' +1 hours'),
			'endDate' => (string)strtotime($_POST['post_date'].' +'.$ad_duration.' days +1 hours'), 
			'inCoorp' => $inCoorp
		);

		if( $age_min && $age_max){
			$ad['maxAge'] = $age_max;
			$ad['minAge'] = $age_min;
		}

		if( $_POST['latitude'] && $_POST['longitude']){
			$ad['latitude']  = $_POST['latitude'];
			$ad['longitude'] = $_POST['longitude'];
		}

		$interests = arrayToString($_POST['interests']);
		$behaviors = arrayToString($_POST['behaviors']);

		if( $radius ){
			$ad['radius']= $radius;
		}

		if( $behaviors != ""){
			$ad['behaviors']= $behaviors;
		}

		if( $interests != ""){
			$ad['interests']= $interests;
		}

		$fbAd = new Ad($token_api);
		$adInfo = $fbAd->boostPost($ad,$_SESSION['client'],$_SESSION['storeid']);
		if( isset($adInfo['is_error']) && !$adInfo['is_error'] ){

		//Stripe Info		
			$stripe = new Das_Stripe($db,$token_api,$_SESSION['client'],$_SESSION['storeid']);

			$chargeInfo = $stripe->createCharge(array(
					'customer' => $customerId,
					'amount'   =>  round($total_budget,2) * 100,
					'source' => $cardId,
					'description' => 'Charge for:' . $ad['name']
				)
			);

			if( isset($chargeInfo['is_error']) && !$chargeInfo['is_error'] ){
				$fb_ads = $adInfo['data'];
				$data_insert= array(
								'id_post' 				=> $post_id,
								'fb_id_post' 			=> $isPromotable['promotable_id'],
								'adaccount_id'  		=> $fb_ads["AdAccountId"],
								'campaign_id' 			=> $fb_ads["campaign_id"],
								'date_promoted' 		=> date("Y-m-d"),
								'client' 				=> $client_storeid,
								'daily_budget_markup'	=> $total_budget,
								'daily_budget'			=> $ad['amount'],
								'start_date' 			=> date('Y-m-d',strtotime($_POST['post_date'].' +1 hours')),
								'end_date' 				=> date('Y-m-d',strtotime($_POST['post_date'].' +'.$ad_duration.' days +1 hours'))
							 ); 


				$id= $db->insert('facebook_post.fb_promoted_post', $data_insert);

				$data_insert = array(
										'id_post' => $post_id,
										'fb_id_post' => $isPromotable['promotable_id'],
										'id_page' => $_POST['id_page'],
										'id_store' => $storeid,
										'date_posted' => $_POST['post_date'] ,
										'bitlink' => '',
										'portal' => 'facebook',
										'edited' => 0,
										'published' => 1,
							 ); 

				$id= $db->insert('facebook_post.fb_local_post', $data_insert);				

				$return['error'] = 0;
				$_SESSION['success'] = 'Your Post is promoted!!!';
				echo json_encode($return);
				exit;
			}else{		  	
				$fbAd->deleteCampaign($adInfo['data']['campaign_id'],$_SESSION['client'],$_SESSION['storeid']);
				$fbPost->delete( $_SESSION['client'],$storeid, $postInfo['id'] );
				$msg = 'There was an error processing your Payment Request. Please try again or <a href="'.LOCAL_CLIENT_URL.'"/support/">contact support</a>';
				
				if( isset($chargeInfo['is_error']) && $chargeInfo['is_error'] ){
					$msg = '';
					if ( is_array( $chargeInfo['data'] ) ) {

						foreach ($chargeInfo['data'] as $value) {
							$msg .= ' '.$value.'<br>';	
						}
					}else{
						$msg = $chargeInfo['data'];
					}		
				}

				$return['error'] = 1;
				$_SESSION['error'] = $msg;
				echo json_encode($return);
				exit;	
			}

		}else{
			$msg = 'Sorry, there was an error with your Facebook configuration. Please <a href="'.LOCAL_CLIENT_URL.'"/support/">contact support</a> for further assistance.';

			if( isset($adInfo['is_error']) && $adInfo['is_error'] ){
				if ( is_array( $adInfo['data'] ) ) {
					$msg = '';
					foreach ($adInfo['data'] as $value) {
						$msg .= ' '.$value.'<br>';	
					}
					$_SESSION['error'] = $msg;				
				}			
			}
			$fbPost->delete( $_SESSION['client'],$storeid, $postInfo['post_id'] );

			$return['error'] = 1;
			$_SESSION['error'] = $msg;
			echo json_encode($return);
			exit;	
		}
	}else{
		$fbPost->delete( $_SESSION['client'],$storeid, $postInfo['id'] );

		$msg = 'Sorry, this post is not Elegible for promotion. Please <a href="'.LOCAL_CLIENT_URL.'"/support/">contact support</a> for further assistance.';

		$return['error'] = 1;
		$_SESSION['error'] = $msg;
		echo json_encode($return);
		exit;
	}

}else{
	$msg = 'Sorry, there was an error with post. Please <a href="'.LOCAL_CLIENT_URL.'"/support/">contact support</a> for further assistance.';

	if( isset($postInfo['is_error']) && $postInfo['is_error'] ){
		if ( is_array( $postInfo['data'] ) ) {
			$msg = '';
			foreach ($postInfo['data'] as $value) {
				$msg .= ' '.$value.'<br>';	
			}
			$_SESSION['error'] = $msg;				
		}			
	}

	$return['error'] = 1;
	$_SESSION['error'] = $msg;
	echo json_encode($return);
	exit;
}

function arrayToString($array,$deli=',',$key = null){
	$str = '';
	foreach ($array as $value) {
		if( isset($key) ){
			$str .= $value[$key].$deli;
		}else{
			$str .= $value.$deli;
		}
		
	}

	$str = rtrim($str, $deli);
	return $str;
}
?>