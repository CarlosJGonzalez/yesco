<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
require_once ($_SERVER['DOCUMENT_ROOT']."/includes/ClassDasReview.php");


if( count($_POST) ){
	$draw = $_POST['draw'];
	$start = $_POST['start'];
	$rowperpage = $_POST['length']; // Rows display per page

	$params = false;

	if (isset($_POST['start_date']) && isset($_POST['end_date'])) {
		$params['gte'] = strtotime($_POST['start_date']);
		$params['lte'] = strtotime($_POST['end_date']);
	}

	if (isset($_POST['start']) && isset($_POST['length'])) {

		$params['start_p'] = $_POST['start'];
		$params['end_p'] = $_POST['length'];

	}
	$filter = false;
	if( isset($_POST['filter_star']) && $_POST['filter_star'] != '' ){
		filterStar($params,$_POST['filter_star']);
		$filter = true;
	}

	if( isset($_POST['filter_portal']) && $_POST['filter_portal'] != '' ){
		$params['source'] = trim( $_POST['filter_portal'] , ',' );
		$filter = true;
	}

	$itotal1  = 0;
	$itotal  = 0;
	$data = array();

	$storeid = isset($_SESSION['storeid']) ? $_SESSION['storeid'] : null;
	$dasReview = new Das_Review($db,$token_api,$_SESSION['client'],$storeid);

	$reviews = $dasReview->getReviews( $params);

	$google_link   = $dasReview->getLinkGoogle();
	$facebook_link = $dasReview->getLinkFB();	

	$itotal = ( $filter ) ? $reviews['info']['count'] : $reviews['info']['total_items'];
	$itotal1 = $reviews['info']['total_items'];

	foreach ($reviews['data'] as $review) {

		$reviw_id = base64_encode($_SESSION['client'].$review['review_id'] );

		switch ($review['portal']) {
			case 'Google':
				$google_link_reviews = $dasReview->getGMBReviewsLink();
				$google_link_reviews = ($google_link_reviews != '') ? $google_link_reviews : $google_link;
				$icon = '<span class="h4 google"><i class="fab fa-google"></i></span>';
				$view = '<a href="'.$google_link_reviews.'" target="_blank"><i class="mx-1 text-blue fas fa-eye"></i></a>';
				$replay ='<a class="respondModal" href="#" data-toggle="modal" data-target="#respondModal" data-id="'.$reviw_id.'" data-portal="'.$review['portal'].'"><i class="mx-1 text-blue fas fa-bullhorn"></i></a>';
				break;
			case 'Facebook':
				$icon = '<span class="h4 facebook"><i class="fab fa-facebook-f"></i></span>';
				$view = '<a href="'.$facebook_link.'" target="_blank"><i class="mx-1 text-blue fas fa-eye"></i></a>';
				$replay ='<a class="respondModal" href="#" data-toggle="modal" data-target="#respondModal" data-id="'.$reviw_id.'" data-portal="'.$review['portal'].'"><i class="mx-1 text-blue fas fa-bullhorn"></i></a>';
				break;
			case 'Yelp':
				$icon = '<span class="h4 yelp"><i class="fab fa-yelp"></i></span>';
				$view = '<a href="'.$active_location["yelp"].'" target="_blank"><i class="mx-1 text-blue fas fa-eye"></i></a>';
				$replay ='<a href="https://biz.yelp.com/login" target="_blank" ><i class="mx-1 text-blue fas fa-bullhorn"></i></a>';
				break;												
			default:
				$icon = '';
				$view = '';
				$replay = '';
				break;
		}

		$rating = '<span class="nowrap">';

		if($review['rating'] > 0 ){
			for ($i = 1; $i <= 5; $i++) {
				if($i <= $review['rating']){
					$rating .= '<i class="fas fa-star"></i>';
				}else{
					break;$rating .= '<i class="fas fa-star-half"></i>';
				}
			}
		}

		$rating .= '</span>';

		$comment = '<p>'.$review['comment'].'</p>';
		$comment .= '<span class="d-block"> Reviewer: <strong>'.$review['name'].'</strong></span><hr>';
		$comment .= '<div class="p-3 c-thru"><strong class="d-block">Owner Response:</strong><span class="d-block"><em>';
		$comment .= ( isset($review['reply_comment']) && $review['reply_date'] > '0000-00-00 00:00:00' ) ? $review['reply_comment'] : "Has not replied";
		$comment .= '</em></span></div>';

		$action = isset($view) ? $view : '';
		$action .= isset($replay) ? $replay: '';

		$data[] = array(
								"date"  	  => $review['create_date'],
								"source"  	  => $icon,
								"rating"  	  => $rating,
								"review"  	  => $comment,
								"actions"  	  => $action,								
						    );
	}

	$response = array(
		"draw" => intval($draw),
		"iTotalRecords" => $itotal1,
		"iTotalDisplayRecords" => $itotal,
		"aaData" => $data
	);

	echo json_encode($response);
}

function filterStar(&$params,$filter){
	$filters = explode(',', trim($filter,','));
	
	if( count( $filters ) == 1){
		$params['rating'] = $filters[0];
	}else{
		sort($filters);
		$params['gte_rating'] = $filters[0];
		$params['lte_rating'] = end($filters);		
	}
}
?>