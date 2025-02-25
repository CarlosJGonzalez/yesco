<?
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");

if($_SESSION["user_role_name"] == "admin_root"){
	#IN#
	$sql_in = "SELECT fpi.store_id, fpi.name_page, fpi.link_page FROM facebook_post.fb_pages fp, facebook_post.facebook_post_issues fpi WHERE fpi.store_id = fp.store_id";

	#NOT IN#
	$sql_not_in = "SELECT  l.store_id FROM facebook_post.facebook_post_issues l WHERE l.store_id !='' AND NOT EXISTS (SELECT NULL FROM facebook_post.fb_pages r WHERE r.store_id = l.store_id)";
	
	#LAST POSTED 2020-01-13 00:00:00
	$sql_last_date_13 = "SELECT fpi.store_id, fpi.name_page, fpi.link_page FROM facebook_post.fb_pages fp, facebook_post.facebook_post_issues fpi WHERE fpi.store_id = fp.store_id AND fpi.last_date_posted = '2020-01-13 00:00:00'";
	
	#LAST POSTED 2020-01-15 00:00:00
	$sql_last_date_15 = "SELECT fpi.store_id, fpi.name_page, fpi.link_page FROM facebook_post.fb_pages fp, facebook_post.facebook_post_issues fpi WHERE fpi.store_id = fp.store_id AND fpi.last_date_posted = '2020-01-15 00:00:00'";
	
	#LAST POSTED 2020-01-20 00:00:00
	$sql_last_date_20 = "SELECT fpi.store_id, fpi.name_page, fpi.link_page FROM facebook_post.fb_pages fp, facebook_post.facebook_post_issues fpi WHERE fpi.store_id = fp.store_id AND fpi.last_date_posted = '2020-01-20 00:00:00'";
	
	$locations = $db->rawQuery($sql_last_date_20);
	
	echo '<pre>'; print_r($locations); echo '</pre>';

	$string = 'array(';
	foreach($locations as $location){
	
		//$pending_stores = array('30663', '30254', '30223', '30632', '30217'); 
		
		$string .= "'".$location['store_id']."',";
		
		/*$updated_stores = array('30663', '30254', '30223', '30632', '30217'); 
		
		if(in_array($post_stores["storeid"], $locations_to_update)){
			echo "<pre>"; print_r($post_stores); echo "</pre>";
		}else{
			continue;
		}*/
	}
	
	$string = rtrim($string, ',') . ');';
	
	echo "<pre>"; print_r($string); echo "</pre>";
	
	$pending_stores = array('30332','30408','30602','30400','30006','30062','30063','30648','30591','30405','30635','30534','30652','30448','30051','30603','30128','30541','30372','30570','30335','30581','30611','30560','30656','30657', '30217', '30223', '30632', '30254','30661','30651','30175','30077','30663','30410','30142','30597','30567','30664','30465','30531','30649','30208','30666','30263','30653','30512');
	echo "<pre>"; print_r($pending_stores); echo "</pre>";					
}else{
	pageRedirect("Access Denied: You must be authorized to view this page.", "error", "/admin/call-log/");
}