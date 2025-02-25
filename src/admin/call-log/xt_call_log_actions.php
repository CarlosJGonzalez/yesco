<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");

if(!$_SESSION["email"] && ($_SESSION["user_role_name"] != "admin_root" || $_SESSION["user_role_name"] != "admin_rep")){
	pageRedirect("Access denied: You must be authorized to view this page.", "error", "/");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['type'] == 'admin') {
	$campaign = filter_var($_POST['campaign'], FILTER_SANITIZE_STRING);
	$caller = filter_var($_POST['caller'], FILTER_SANITIZE_STRING);
	$date = date("Y-m-d",strtotime($_POST['calldate']));
	$calltype = filter_var($_POST['calltype'], FILTER_SANITIZE_STRING);
	$duration = filter_var($_POST['duration'], FILTER_SANITIZE_STRING);
	$reason = filter_var($_POST['reason'], FILTER_SANITIZE_STRING);
	$notes = filter_var($_POST['notes'], FILTER_SANITIZE_STRING);
	$action = filter_var($_POST['action'], FILTER_SANITIZE_STRING);

	if(isset($_POST['submitBtnUpdateCall']) && $_POST['submitBtnUpdateCall'] == 'SAVE'){
		$data_call_log = array("client"=>$_SESSION['client'], "campaign"=>$campaign, 
							   "calltype"=>$calltype, "reason"=>$reason, 
							   "notes"=>$notes, "action"=>$action,
							   "duration"=>$duration, "caller"=>$caller,
							   "calldate"=>$date);
							   
		$db->where ('id', $_POST['id']);
		$call_log_updated_ok = $db->update ('advtrack.campaign_call_log', $data_call_log);
		
		if ($call_log_updated_ok){
			$db->where('id', $_POST['id']);
			$db->delete('advtrack.campaign_call_log_store');
			
			foreach($_POST['storeid'] as $storeid){
				$data_new_call_log_storelist = array("id"=>$_POST['id'], "storelist"=>$storeid);	
				$storelist_inserted_ok = $db->insert ('advtrack.campaign_call_log_store', $data_new_call_log_storelist);
			}
			
			$dataAct = array("username"=>$_SESSION['email'],
							 "storeid"=>$_SESSION['storeid'],
							 "updates"=>json_encode($data_call_log),
							 "section"=>"call-log",
							 "details"=>"Updated a call log."
				);

			track_activity($dataAct);
			
			pageRedirect("Your changes have been successfully saved.", "success", "/admin/call-log/");
		}else{
			pageRedirect("There was an error updating the call.", "error", "/admin/call-log/");
		}
		
	}elseif(isset($_POST['submitBtnAddNewCall']) && $_POST['submitBtnAddNewCall'] == 'SAVE'){
		
		$data_new_call_log = array("client"=>$_SESSION['client'], "campaign"=>$campaign, 
								   "calltype"=>$calltype, "reason"=>$reason, 
								   "notes"=>$notes, "action"=>$action,
								   "duration"=>$duration, "caller"=>$caller,
								   "calldate"=>$date);

		$new_call_log_inserted_id = $db->insert ('advtrack.campaign_call_log', $data_new_call_log);
		
		if ($new_call_log_inserted_id){
			
			foreach($_POST['storeid'] as $storeid){
				$data_new_call_log_storelist = array("id"=>$new_call_log_inserted_id, "storelist"=>$storeid);	
				$storelist_inserted_ok = $db->insert ('advtrack.campaign_call_log_store', $data_new_call_log_storelist);
			}
			
			$dataAct = array("username"=>$_SESSION['email'],
							 "storeid"=>$_SESSION['storeid'],
							 "updates"=>json_encode($data_new_call_log),
							 "section"=>"call-log",
							 "details"=>"Added a call log."
				);

			track_activity($dataAct);
			
			pageRedirect("New call log has been successfully added.", "success", "/admin/call-log/");
		}else{
			pageRedirect("There was an error adding the call.", "error", "/admin/call-log/");
		}
	}

}else{
	pageRedirect("Access Denied: You must be authorized to view this page.", "error", "/admin/call-log/");
}

