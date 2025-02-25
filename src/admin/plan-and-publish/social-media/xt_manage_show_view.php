<?php
	session_start();
	include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
	include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");
    
    $info = false;
    if (function_exists($_POST['function'])) {

    	if (isset($_POST['info'])) {
    		$info = $_POST['info'];
    	}

      	echo json_encode($_POST['function']($db,$info));
      	exit;
    }

    function deletePost(&$db,$info = false){
    	$flag = -1;

    	if(isset($info['id']) && $info['id'] != ''){
    		$table = getDB($info['type']);
			$db->where('id',$info['id']);

			if( $db->delete ($table) ){				
				
    			$dataAct = array(
					 "updates"=>json_encode(array('action'=>'delete','id'=>$info['id'])),
					 "section"=>"plan-and-publish",
					 "details"=> 'Type View: '.$info['type'].' Post Id: '.$info['id'].' Delete Post'
				 );
					 
				track_activity($dataAct);
				return 1;
			}
			return -1;
    	}
    } 	
	
    function updatePostInformation(&$db,$info = false){
    	$flag = -1;

    	if(isset($info['id']) && $info['id'] != ''){
    		$data = array();
    		if($info['post'] != '-1'){
    			$post = mysql_escape_mimic(clear_input($info['post']));
    			$data['post'] = $post;
    			$action = 'Update Post';
    		}

			if($info['link'] != '-1'){
    			$link = mysql_escape_mimic(clear_input($info['link']));
    			$data['link'] = $link;
    			$action = 'Update Link';
    		}
    		
    		if(count($data)){
    			//$table = $info['type'] == 'store' ? 'social_media__local_posts' : 'social_media_posts';
    			$table = getDB($info['type']);
    			$dataAct = array(
					 "updates"=>json_encode($data),
					 "section"=>"plan-and-publish",
					 "details"=> 'Type View: '.$info['type'].' Post Id: '.$info['id'].' '.$action
				 );
					 
				track_activity($dataAct);

    			$db->where('id',$info['id']);
    			return $db->update ($table, $data) ? 1 : -1;
    		}else{
    			return 1;
    		}

    		
    	}
    	
    	return $flag;
    }

    function getDB($type){
    	$db_table = 'social_media__local_posts';
    	switch ($type) {
    		case 'optionals':
    			$db_table = 'social_media__local_posts_optional';
    			break;	
			case 'corp':
    			$db_table = 'social_media_posts';
    			break;    		
    		default:
    			$db_table = 'social_media__local_posts';
    			break;
    	}
    	return $db_table;
    	
    }

    function clear_input($data) {
	  	$data = trim($data);
	  	$data = stripslashes($data);
	  	$data = htmlspecialchars($data);
	  	return $data;
	}

	function updateInformation(&$db,$info = false){   

		$admin = explode(' - ', $info['admin_view_dates']);
		$client = explode(' - ', $info['client_view_dates']);
		
		$cols = Array("option","value",'id');
		$db->where("option", Array("admin_start_date","admin_end_date","client_start_date","client_end_date"), 'IN');
		$show_date = $db->get("option_values",null,$cols);

		foreach($show_date as $row){
			$db->where('id',$row["id"]);

			switch ($row["option"]) {
				case 'admin_start_date':
					$value = array('value' => date('Y-m-d',strtotime($admin[0])));
					break;		
				case 'admin_end_date':
					$value = array('value' => date('Y-m-d',strtotime($admin[1])));
					break;		
				case 'client_start_date':
					$value = array('value' => date('Y-m-d',strtotime($client[0])));
					break;		
				case 'client_end_date':
					$value = array('value' => date('Y-m-d',strtotime($client[1])));
					break;		
				default:
					unset($value);					
					break;
			}
			if(isset($value)){
				$db->update('option_values',$value);
			}
			
		}
	}

	function getInformation(&$db,$info = false){
		$dates = array();

		$cols = Array("option","value");
		$db->where("option", Array("admin_start_date","admin_end_date","client_start_date","client_end_date"), 'IN');
		$show_date = $db->get("option_values",null,$cols);

		foreach($show_date as $row){
			$dates[$row["option"]] = date('m/d/Y',strtotime($row["value"]));
		}

		return $dates;
	}

?>