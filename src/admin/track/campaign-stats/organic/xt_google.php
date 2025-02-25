<?php
include ($_SERVER['DOCUMENT_ROOT']."/includes/MysqliDb.php");
include ($_SERVER['DOCUMENT_ROOT']."/functions.php");
session_start();

if(count($_POST) and isset($_SESSION['user_id'])){
	
	$db = new MysqliDb ('localhost', 'root', 'dasflorida', 'advtrack');
	$master_tb = 'gmbstats';
	$rowperpage = $_POST['length']; // Rows display per page
	$order_by_date = $_POST['order']['0']['dir'];  // Search input value
	$response = array();
	$data = array();
	$draw = $_POST['draw'];
	$start = $_POST['start'];
	$start_date = '';
	$end_date = '';
	$search_value = $_POST['search']['value'];

	/*if($_SESSION['storeid'] > 0 ){
		$iTotalRecords = $db->where("client",$_SESSION['client'])->where("store_id",$_SESSION['storeid'])->getOne($master_tb,'Count(*) as qty')['qty'];
		$iTotalDisplayRecords = $db->where("client",$_SESSION['client'])->where("store_id",$_SESSION['storeid'])->getOne($master_tb,'Count(*) as qty')['qty'];
	}else{
		$iTotalRecords = $db->where("client",$_SESSION['client'])->where("store_id",'9013%','NOT LIKE')->getOne($master_tb,'Count(*) as qty')['qty'];
		$iTotalDisplayRecords = $db->where("client",$_SESSION['client'])->getOne($master_tb,'Count(*) as qty')['qty'];
	}*/
	
	$cols = array (
					//'date',
					"store_id",				
					'queries_direct',
					'queries_indirect',
					'views_map',
					'views_search',
					'actions_phone',
					'actions_website',
					'actions_driving_directions',					
					'local_post_views_search',					
				);

	$cols_db = array (
					//'date',
					"store_id",				
					'FORMAT(queries_direct, 0) as queries_direct',
					'FORMAT(queries_indirect, 0) as queries_indirect',
					'FORMAT(views_map, 0) as views_map',
					'FORMAT(views_search, 0) as views_search',
					'FORMAT(actions_phone, 0) as actions_phone',
					'FORMAT(actions_website, 0) as actions_website',
					'FORMAT(actions_driving_directions, 0) as actions_driving_directions',					
					'FORMAT(local_post_views_search, 0) as local_post_views_search',					
				);

	$order_by_direction = $_POST['order']['0']['dir'];  // asc or desc
	$order_by_colum = (int)$_POST['order']['0']['column'];  // table colum
	$order_by_colum = isset($cols[$order_by_colum ]) ? $cols[$order_by_colum ] : $cols[0]; // table colum ("client", "campid", "start", and the rest)
	$filters = Array();

	//If search was filled, a condition will be added to the query
	if(!empty($search_value)){
		filterBySearch($db, $search_value);
		$filters["bySearch"] = true;
	}
		//If the user has selected both dates, and the search button was clicked a condition will be added to the query
	if(($_POST["is_date_search"] == "yes") || (!empty($_POST["start_date"]) && !empty($_POST["end_date"]))) {
		//Getting datapickers values
		$start_date = date('Y-m-d', strtotime($_POST["start_date"]));
		$end_date = date('Y-m-d', strtotime($_POST["end_date"]. ' +1 day')); //by default the datepicker does not send the correct date, so 1 day is added
		
		// previous to PHP 5.1.0 you would compare with -1, instead of false
		if ($start_date === false || $end_date === false) {
			$start_date = strtotime('today');
			$end_date = strtotime('today');
		}
		
		$date_filter_data = Array("start_date" => $start_date, "end_date" => $end_date);
		filterByDate($db, $date_filter_data);
		$filters["byDate"] = true;
	}
	
	//If the user_role is rep or admin general, a condition will be added to the query
	/*if(($_SESSION["user_role_name"] == 'admin_rep') || ($_SESSION["user_role_name"] == 'admin_general')){
		//filterByRole($db);
		$filters["byRep"] = true;
	}*/
	//If a store was selected, a condition will be added to the query
	if((!empty($_SESSION['storeid'])) && ($_SESSION['storeid'] > 0)){
		filterByStoreId($db, $_SESSION['storeid']);
		$filters["byStoreId"] = true;
	}
	
	//If a store was selected, a condition will be added to the query
	if(!empty($order_by_direction) && !empty($order_by_colum)){
		$order_by_data = Array("order_by_colum" => $order_by_colum, "order_by_direction" => $order_by_direction);
		filterByColum($db, $order_by_data);
		$filters["byColum"] = true;
	}

	//Some datatable parameters are built base on the filters the user has selected
	if(count($filters)){
		$activity_result_set = $db->where("client",$_SESSION['client'])->get($master_tb, null, $cols); 
		//This query is only used to set  iTotalRecords and iTotalDisplayRecords
		$iTotalRecords = count($activity_result_set);
		$iTotalDisplayRecords = count($activity_result_set);
		
		if($filters["byDate"])
			filterByDate($db, $date_filter_data);
		if($filters["bySearch"])
			filterBySearch($db, $search_value);
		//if($filters["byRep"])
			//filterByRole($db);
		if($filters["byStoreId"])
			filterByStoreId($db, $_SESSION['storeid']);
		if($filters["byColum"])
			filterByColum($db, $order_by_data);
	}
	//The resul set that contains all activity logs is gotten here
	if($_SESSION['storeid'] <= 0){
		$cols1 = array (
					'date',
					"store_id",			
					'FORMAT(SUM(queries_direct), 0) as queries_direct',
					'FORMAT(SUM(queries_indirect), 0) as queries_indirect',
					'FORMAT(SUM(views_map), 0) as views_map',
					'FORMAT(SUM(views_search), 0) as views_search',
					'FORMAT(SUM(actions_phone), 0) as actions_phone',
					'FORMAT(SUM(actions_website), 0) as actions_website',
					'FORMAT(SUM(actions_driving_directions), 0) as actions_driving_directions',	
					'FORMAT(SUM(local_post_views_search), 0) as local_post_views_search'
				);
		
		$logs = $db->where("client",$_SESSION['client'])->where("store_id",'9013%','NOT LIKE')->groupBy('client,store_id')->get($master_tb, Array ($start, $rowperpage), $cols1);
		//This query is only used to set  iTotalRecords and iTotalDisplayRecords
		$logsNoLimit = $db->where("client",$_SESSION['client'])->where("store_id",'9013%','NOT LIKE')->groupBy('client,store_id')->get($master_tb, null, $cols1);
	
		$iTotalRecords = count($logsNoLimit);
		$iTotalDisplayRecords = count($logsNoLimit);
	}else{
		$logs = $db->where("client",$_SESSION['client'])->where("store_id",'9013%','NOT LIKE')->get ($master_tb, Array ($start, $rowperpage), $cols_db);
		$logsNoLimit = $db->where("client",$_SESSION['client'])->where("store_id",'9013%','NOT LIKE')->get ($master_tb, null, $cols_db);
	
		$iTotalRecords = count($logsNoLimit);
		$iTotalDisplayRecords = count($logsNoLimit);
	}

	//The data that will be display on the datatable is created here
	$db_table = $_SESSION['database'].'.locationlist';
	foreach($logs as $log){
		$info = array();
		foreach ($cols as $col) {
			if(isset($log[$col])){
				//Todo if no need show campanyname only deleted this if.
				if($col == 'store_id'){
					if ($log[$col] == 0) {
						$info['companyname'] = ucfirst($_SESSION['database']);
					}else{
						$companyname = $db->where('storeid',$log[$col])->getOne($db_table ,'companyname');
						$info['companyname'] = isset($companyname['companyname']) ? $companyname['companyname']:$log[$col];
						//$info['store_id'] = $log[$col];
					}
					
				}else if(in_array($col,['date','time'])){
					$info[$col] = date('M d, o',strtotime($log[$col]));
				}else{
					$info[$col] = $log[$col];
				}			
			}else{
				$info[$col] = 0;
			}
		}		

		$data[] = $info;
	}

	
	$response = array(
		"draw" => intval($draw),
		"iTotalRecords" => $iTotalRecords,
		"iTotalDisplayRecords" => $iTotalDisplayRecords,
		"aaData" => $data
	);

	echo json_encode($response);
}else{
	$_SESSION['error']="You must be logged in to view this page.";
	header('location: /');
	exit;
}

function filterByDate(&$db, $date_filter_data){
	$db->where('date', Array ($date_filter_data['start_date'], $date_filter_data['end_date']), 'BETWEEN');
}

function filterBySearch(&$db, $search_value){
	$db->where ("(store_id like ? or client like ? or date like ? )", Array("%$search_value%", "%$search_value%", "%$search_value%"));
}

function filterByRole(&$db){
	$emails = array();
	$role = $_SESSION["user_role_name"];

	switch ($role) {
		case "admin_rep":
			$sql_rep_users = "SELECT strl.email FROM ".$_SESSION['database'].".locationlist locl, ".$_SESSION['database'].".storelogin strl, ".$_SESSION['database'].".reps rept, ".$_SESSION['database'].".storelogin_user_roles strlur WHERE locl.rep != '' AND locl.storeid = strl.storeid AND rept.id = locl.rep AND rept.id = (SELECT id FROM ".$_SESSION['database'].".reps WHERE email = '".$_SESSION['username']."') AND strl.id = strlur.id_storelogin";					
			$emails = getFilteredEmailsByRole($sql_rep_users);
			break;
		case "admin_general":
			$sql_admin_gral_users = "SELECT strl.email FROM ".$_SESSION['database'].".storelogin strl, ".$_SESSION['database'].".storelogin_user_roles strlur WHERE strl.id = strlur.id_storelogin AND (strlur.id_user_roles = (SELECT id FROM ".$_SESSION['database'].".user_roles WHERE name = 'store_user') OR strl.email IN ('".$_SESSION['username']."'))";
			$emails = getFilteredEmailsByRole($db,$sql_admin_gral_users);
			break;
	}
	
	$db->where('username', $emails, 'IN');
}

function filterByStoreId(&$db, $storeid){
	$db->where('store_id', $storeid);
}

// The table will be ordered (ascending or descending) by the passed column.
function filterByColum(&$db, $order_by_data){
	$db->orderBy($order_by_data['order_by_colum'],$order_by_data['order_by_direction']);
}

function getFilteredEmailsByRole(&$db_link,$sql){
	//require_once ($_SERVER['DOCUMENT_ROOT'].'/connect_MysqliDb.php');

	$emails = array();

	// Create connection
	//$db_link = new MysqliDb (null, null, null, $database);
	$users = $db_link->rawQuery($sql);
	
	if($db_link->count > 0){
		foreach($users as $user){
			$emails[] = $user['email'];
		}
		array_push($emails, $_SESSION['username']);
	}else{
		array_push($emails, $_SESSION['username']);
	}
	
	return $emails;
}
?>