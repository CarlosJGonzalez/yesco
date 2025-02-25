<?
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");

/*if($_SERVER['REQUEST_URI']!="/login.php"){
	if(!$_SESSION["name"]){
		$_SESSION['error']="You must be logged in to view this page.";
		header('location: /login.php');
		exit;
	}
}*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	
	if(isset($_POST['submitBtnUpdateMyDetails']) && $_POST['submitBtnUpdateMyDetails'] == 'Save Changes'){
		$user_id = $db->escape($_POST['user_id']);
		$email = $db->escape($_POST['email']);
		$first_name = $db->escape($_POST['first_name']);
		$last_name = $db->escape($_POST['last_name']);
		$full_name = $first_name." ".$last_name;
		$user_store_id = $_SESSION['storeid'];
		//$user_store_id = '-1';
		
		if(!(empty($user_id)) && !(empty($email)) && !(empty($first_name)) && !(empty($last_name))){

			$cols = array("email","name");
			
			$db->setTrace (true);
			
			$db->where("email",$email,"=")
			   ->where("id",$user_id,"!=");

			$user = $db->getOne('storelogin', $cols);

			if(isset($db->trace[0])){

				//If the user doesn't exist in the database, it will be updated
				if ($db->count == 0) {
					
					$data = Array ('email' => $email, 'name' => $full_name);
					
					$db->where("id",$user_id,"=");
					
					if($db->update ('storelogin', $data)){
						//$db->where("id",$user_id,"=");
						$user = $db->getOne('storelogin', $cols);

						$input = array("email"=>$email,
								   "name"=>$full_name);

						$updates = array_diff($input,$user);		
						
						$data_track = array("updates"=>json_encode($updates),"section"=>"login", "details"=>"Updated ".$user_id);
						track_activity($data_track);
						
						$_SESSION["email"] = $email;
						
						pageRedirect("Changes updated successfully.", "success", "/my-account/");
					}else{
						pageRedirect("Error updating changes.", "error", "/my-account/");
					}
				}else{
					pageRedirect("That username is taken. Try another.", "error", "/my-account/");
				}
			}else{
				pageRedirect("Sorry, there was an error connecting to the database.", "error", "/my-account/");
			}
			
		}else{
			pageRedirect("All fields must be fill out.", "error", "/my-account/");
		}
	}elseif(isset($_POST['submitBtnUpdateMyPassword']) && $_POST['submitBtnUpdateMyPassword'] == 'Save Password'){

		$user_id = $db->escape($_POST['user_id']);
		$current_password = $db->escape($_POST['current_pass']);
		$new_password = password_hash($db->escape($_POST['new_pass']), PASSWORD_DEFAULT);
		
		$user_store_id = $_SESSION['storeid'];
		//$user_store_id = '-1';
		
		if(!(empty($user_id)) && !(empty($current_password)) && !(empty($new_password))){

			$cols = array("password");
			
			$db->setTrace (true);
			
			$db->where("id",$user_id,"=");

			$user = $db->getOne('storelogin', $cols);
			
			if(password_verify($current_password, $user['password'])){

				if(isset($db->trace[0])){

					//If the user doesn't exist in the database, it will be updated
					if ($db->count > 0) {
						
						$data = Array ('password' => $new_password);
						
						$db->where("id",$user_id,"=");
						
						if($db->update ('storelogin', $data)){

							$input = array("password_hash"=>$new_password);

							$updates = array_diff($input,$user);
							
							$data_track = array("updates"=>json_encode($updates),"section"=>"login", "details"=>"Updated ".$user_id);
							track_activity($data_track);
							
							pageRedirect("Changes updated successfully.", "success", "/my-account/change-password.php");
						}else{
							pageRedirect("Error updating changes", "error", "/my-account/change-password.php");
						}
					}else{
						pageRedirect("The username doesn't exist.", "error", "/my-account/change-password.php");
					}
				}else{
					pageRedirect("Sorry, there was an error connecting to the database.", "error", "/my-account/change-password.php");
				}
			
			}else{
				pageRedirect("The current password is incorrect.", "error", "/my-account/change-password.php");
			}
			
		}else{
			pageRedirect("All fields must be fill out.", "error", "/my-account/change-password.php");
		}
	}
}//End if ($_SERVER['REQUEST_METHOD'] === 'POST')

header('location:/my-account/');