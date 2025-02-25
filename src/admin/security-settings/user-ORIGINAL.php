<!doctype html>
<html lang="en">
  <head>
	<link rel="stylesheet" href="/css/checkbox.css">
    <?php 
	include ($_SERVER['DOCUMENT_ROOT']."/includes/head.php");
	if(!(roleHasPermission('show_user_management', $_SESSION['role_permissions']))){
		header('location: /');
		exit;
	}
	?>

    <title>User Management | <?php echo CLIENT_NAME; ?></title>
  </head>
  <body class="bg-light cbp-spmenu-push">
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/top.php"); ?>

    <div class="container-fluid">

      <div class="row">
        <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/nav.php"); ?>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 p-0 mb-4">
			
			<div class="p-0 border-bottom mb-4">
				<div class="d-flex d-block align-items-center clearfix py-2 px-4">
					<h1 class="h2 font-light mb-0 text-center text-sm-left"><i class="fas fa-users-cog mr-2"></i> User Management</h1>
					<div class="ml-auto">
						<div class="dropdown d-inline-block">
						  <button type="button" id="dropdownMenuButton"  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="border-0 bg-transparent">
							<i class="fas fa-2x text-muted fa-ellipsis-v"></i>
						  </button>
						  <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
							  <?php if(roleHasPermission('show_add_user_element', $_SESSION['role_permissions'])){ ?>
							  	<a href="#addUser" title="Add User" data-toggle="modal" data-target="#addUser" class="dropdown-item small">Add User</a>
							  <?php } if(roleHasPermission('show_add_role_element', $_SESSION['role_permissions'])){ ?>
							  	<a href="#addPermission" title="Add Permission" data-toggle="modal" data-target="#addPermission" class="dropdown-item small">Add Permission</a>
							  	<a href="#addRole" title="Add Role" data-toggle="modal" data-target="#addRole" class="dropdown-item small">Add Role</a>
							  <?php } ?>
							</div>
						</div>
					
					</div>
				</div>
			</div>
        	<div class="px-4 py-3">
				
			  <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/alerts.php"); ?>

			  <!-- Tabs go here -->
				
		<?php
		if($_SESSION['admin'] && $_SESSION["user_role_name"] == 'admin_root'){
		?>
		  <!-- Nav tabs -->
		  <ul class="nav nav-tabs" role="tablist">
			<li class="nav-item">
			  <a class="nav-link text-blue active" data-toggle="tab" href="#tabs-admin-users">Admin Users</a>
			</li>
			<li class="nav-item">
			  <a class="nav-link text-blue" data-toggle="tab" href="#tabs-store-users">Store Users</a>
			</li>
			<!--<li class="nav-item">
			  <a class="nav-link text-blue" data-toggle="tab" href="#tabs-inactive-stores">Inactive Store Users</a>
			</li>-->
			<li class="nav-item">
			  <a class="nav-link text-blue" data-toggle="tab" href="#tabs-all-permissions">All Permissions</a>
			</li>
			<li class="nav-item">
			  <a class="nav-link text-blue" data-toggle="tab" href="#tabs-all-roles">All Roles</a>
			</li>
		  </ul>
		  
		  <!-- Tab panes -->
		  <div class="tab-content p-2">
			<div id="tabs-admin-users" class="tab-pane active">
				<?php
				//$sql = "SELECT strl.* FROM ".$_SESSION['database'].".storelogin strl WHERE strl.storeid='".$_SESSION['storeid']."'";
				$sql = "SELECT strl.* FROM ".$_SESSION['database'].".storelogin strl WHERE strl.storeid < '0' AND strl.storeid != ''";
				createForm($sql, 'tab_user_form_update');
				?>
			</div>
			<div id="tabs-store-users" class="tab-pane fade"><br>
				<?php
				//$sql = "SELECT strl.* FROM ".$_SESSION['database'].".locationlist locl, ".$_SESSION['database'].".storelogin strl WHERE locl.storeid = strl.storeid";
				$sql = "SELECT DISTINCT strl.* FROM ".$_SESSION['database'].".locationlist locl, ".$_SESSION['database'].".storelogin strl WHERE strl.storeid LIKE CONCAT('%', locl.storeid) AND strl.storeid NOT LIKE '-%'";
				createForm($sql, 'tab_user_form_update');
				?>
			</div>
			<!--<div id="tabs-inactive-stores" class="tab-pane fade"><br>
				<?php
				//$sql = "SELECT t1.* FROM ".$_SESSION['database'].".storelogin t1 LEFT JOIN ".$_SESSION['database'].".locationlist t2 ON t2.storeid = t1.storeid WHERE t2.storeid IS NULL AND t1.storeid > 0";
				//createForm($sql, 'tab_user_form_update');
				?>
			</div>-->
			<div id="tabs-all-permissions" class="tab-pane fade"><br>
				<?php
				$sql = "SELECT * FROM ".$_SESSION['database'].".permissions";
				createForm($sql, 'tab_permission_form_update');
				?>
			</div>
			<div id="tabs-all-roles" class="tab-pane fade"><br>
				<?php
				$sql = "SELECT * FROM ".$_SESSION['database'].".user_roles";
				createForm($sql, 'tab_role_form_update');
				?>
			</div>
		  </div>
		<?
		}elseif($_SESSION['admin'] && $_SESSION["user_role_name"] == 'admin_rep'){
		?>
		 <!-- Nav tabs -->
		  <ul class="nav nav-tabs" role="tablist">
			<li class="nav-item">
			  <a class="nav-link active" data-toggle="tab" href="#tabs-rep-admin-users">Representative Admin Users</a>
			</li>
			<li class="nav-item">
			  <a class="nav-link" data-toggle="tab" href="#tabs-rep-store-users">Store Users</a>
			</li>
		  </ul>
		  <!-- Tab panes -->
		  <div class="tab-content p-2">
			<div id="tabs-rep-admin-users" class="tab-pane active"><br>
				<?php
				//Retrieves all admin_rep that were assigned to a store
				//$sql = "SELECT strl.*, strlur.id_user_roles FROM ".$_SESSION['database'].".storelogin strl, ".$_SESSION['database'].".storelogin_user_roles strlur, ".$_SESSION['database'].".reps rep WHERE strl.storeid='".$_SESSION['storeid']."' AND strl.id = strlur.id_storelogin AND strlur.id_user_roles = (SELECT id FROM ".$_SESSION['database'].".user_roles WHERE name = 'admin_rep') AND strl.email IN (SELECT email FROM ".$_SESSION['database'].".reps) AND strl.email = rep.email AND rep.id IN (SELECT rep FROM ".$_SESSION['database'].".locationlist)";
				$sql = "SELECT strl.*, strlur.id_user_roles FROM ".$_SESSION['database'].".storelogin strl, ".$_SESSION['database'].".storelogin_user_roles strlur, ".$_SESSION['database'].".reps rep WHERE strl.storeid<'0' AND strl.id = strlur.id_storelogin AND strlur.id_user_roles = (SELECT id FROM ".$_SESSION['database'].".user_roles WHERE name = 'admin_rep') AND strl.email IN (SELECT email FROM ".$_SESSION['database'].".reps) AND strl.email = rep.email AND rep.id IN (SELECT rep FROM ".$_SESSION['database'].".locationlist)";
				createForm($sql, 'tab_rep_admin_users');
				?>
			</div>
			<div id="tabs-rep-store-users" class="tab-pane fade"><br>
				<?php
				//Retrieves the users from the store that were assigned to a representative
				//$sql = "SELECT strl.*, strlur.id_user_roles, locl.storeid, locl.companyname, locl.rep FROM ".$_SESSION['database'].".locationlist locl, ".$_SESSION['database'].".storelogin strl, ".$_SESSION['database'].".reps rept, ".$_SESSION['database'].".storelogin_user_roles strlur WHERE locl.rep != '' AND locl.storeid = strl.storeid AND rept.id = locl.rep AND rept.id = (SELECT id FROM ".$_SESSION['database'].".reps WHERE email = '".$_SESSION['email']."') AND strl.id = strlur.id_storelogin";
				//$sql = "SELECT strl.*, strlur.id_user_roles, locl.storeid as storeid_loc, locl.companyname, locl.rep FROM ".$_SESSION['database'].".locationlist locl, ".$_SESSION['database'].".storelogin strl, ".$_SESSION['database'].".reps rept, ".$_SESSION['database'].".storelogin_user_roles strlur WHERE locl.rep != '' AND CONCAT('%', strl.storeid, '%') LIKE CONCAT('%', locl.storeid, '%') AND rept.id = locl.rep AND rept.id = (SELECT id FROM ".$_SESSION['database'].".reps WHERE email = '".$_SESSION['email']."') AND strl.id = strlur.id_storelogin GROUP BY strl.email HAVING ( COUNT(strl.email) >= 1 )";
				$sql = "SELECT DISTINCT strl.* FROM ".$_SESSION['database'].".locationlist locl, ".$_SESSION['database'].".storelogin strl WHERE strl.storeid LIKE CONCAT('%', locl.storeid) AND strl.storeid NOT LIKE '-%'";
				createForm($sql, 'tab_rep_store_users');
				?>
			</div>
		</div>
		<?
		}elseif($_SESSION['admin'] && ($_SESSION["user_role_name"] == 'admin_root' || $_SESSION["user_role_name"] == 'admin_rep') && $_SESSION['storeid'] > 0){
			$sql = "SELECT * FROM ".$_SESSION['database'].".storelogin WHERE storeid='".$_SESSION['storeid']."'";
			createForm($sql, 'tab_user_form_update');
		}else{
			$sql = "SELECT * FROM ".$_SESSION['database'].".storelogin WHERE email='".$_SESSION['email']."' and id='".$_SESSION['user_id']."'";
			createForm($sql, 'tab_user_form_update');
		}
		
		
		function createForm($sql, $requested_form){
			global $db;
			
			if(($requested_form == "tab_user_form_update") || ($requested_form == "tab_rep_admin_users") || ($requested_form == "tab_rep_store_users")  ){
			?>
				<div class="row">
					
					<?php
					//$result = $conn->query($sql);
					
					$tab_data = $db->rawQuery($sql);
					
					if ($db->count>0){
						foreach($tab_data as $logins){		
							$sql_user_role_id = "SELECT sur.id_user_roles FROM ".$_SESSION['database'].".storelogin strl, ".$_SESSION['database'].".storelogin_user_roles sur WHERE strl.id = '".$logins['id']."' AND sur.id_storelogin = strl.id";
							
							$row_user_role_id = $db->rawQuery($sql_user_role_id);
							
							//$result_user_role_id = $conn->query($sql_user_role_id);
							//$row_user_role_id = $result_user_role_id->fetch_assoc();
							$id_user_role = $row_user_role_id[0]['id_user_roles'];
					?>
					<div class="col-sm-6 col-md-4">
						<form action="xt_user.php" method="post" class="updateUserForm" name="updateUserForm">
								<div class="box p-3 mb-3">
										<div class="row">
											<div class="col-sm-6">
												<div class="form-group">
													<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Username</label>
													<input type="text" class="form-control" name="login_username" value="<?=$logins['email']?>" autocomplete="off" required>
												</div>
											</div>
											<?php if($_SESSION["user_role_name"] == 'admin_root' || $_SESSION["user_role_name"] == 'admin_rep'){?>
											<div class="col-sm-6">
												<div class="form-group">
													<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Role</label>
													<select name="user_role" class="form-control rounded-bottom rounded-right custom-select-arrow" required>
														<option value="n/a">N/A</option>
														<?
														$roles = getUserRoles($_SESSION["user_role_name"], $requested_form);
														foreach ($roles as $key => $value) {
															?>
														<option value="<?=$key?>" <? if($id_user_role == $key){ echo "selected"; $role_name = $value; }?>><?=strtoupper($value)?></option>
														<? } ?>
													</select>
												</div>
											</div>
											<?php } ?>
											<?php if($_SESSION["user_role_name"] == 'admin_root' || $_SESSION["user_role_name"] == 'admin_rep'){?>
											<div class="col-sm-6">
												<div class="form-group">
													<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Status</label>
													<select name="user_status" class="form-control rounded-bottom rounded-right custom-select-arrow" required>
														<?php
														$statuses = array("active"=>"1", "inactive"=>"0");
														foreach ($statuses as $keyArray => $status) {?>
														<option value="<?php echo $status?>" <?php if($logins['status'] == $status) echo "selected";?>><?php echo strtoupper($keyArray)?></option>
														<?php } ?>
													</select>
												</div>
											</div>
											<?php } ?>
											<div class="col-sm-6">
												<div class="form-group">
													<?php if(($_SESSION["user_role_name"] == 'admin_rep') && ($requested_form == "tab_rep_admin_users")){?>
													<input type="hidden" name="storeid" value="<?=$logins['storeid']?>" autocomplete="off" readonly >
													<?php }elseif(($_SESSION["user_role_name"] == 'admin_general')){ ?>
													<input type="hidden" name="storeid" value="<?=$logins['storeid']?>" autocomplete="off" readonly >
													<?php }else{ ?>
													<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Store Id</label>
													<input type="text" class="form-control rounded-bottom rounded-right" name="storeid" value="<?php echo $logins['storeid']?>" autocomplete="off" <?php if($_SESSION["user_role_name"] == 'admin_root' && $role_name != "store_user") echo 'readonly'; elseif($_SESSION["user_role_name"] == 'admin_rep') echo 'readonly';?> >
													<?php } ?>
												</div>
											</div>
										</div>
										<div class="text-center mb-2" clear>
											<?php if(roleHasPermission('change_user_password', $_SESSION['role_permissions'])){ ?>
												<a href="#" id="<?php echo $logins['id']?>" data-toggle="modal" data-target="#changePasswordModal" onclick="setUserId(this.id)"><i class="fas fa-lock fa-fw mr-1"></i> Change Password</a>
											<?php } ?>
										</div>
										<? if(roleHasPermission('delete_user', $_SESSION['role_permissions'])){ ?>
										<div class="text-center mb-2" clear>
											<a href="#" class="del_user" data-toggle="modal" data-target="#deleteUserModal" data-userid="<?php echo $logins['id']?>"><i class="fas fa-trash fa-fw mr-1"></i> Delete User</a>
										</div>
										<?php } ?>
										
										<? if(roleHasPermission('log_as_user', $_SESSION['role_permissions'])){ ?>
										<? if($_SESSION["user_role_name"] == 'admin_root' || $logins['storeid'] > 0){ ?>
										<div class="text-center" clear>
											<a href="#" class="log_as_user" data-token="<?php echo $logins['token']?>"><i class="fas fa-user fa-fw mr-1"></i> Login as this User</a>
										</div>
										<?php } ?>
										<?php } ?>
										
										<br>
										<div class="text-center" clear>
											<input type="hidden" name="user_id" class="user_id" value="<?php echo $logins['id']?>">
											<input type="submit" value="SAVE" name="submitBtnUpdateUser" class="btn bg-blue text-white btn-sm">
										</div>
								</div>
						</form>
					</div>

						<? } ?>
				
					<?
					}else{
						echo '<div class="col-12"><p class="notice notice-danger bg-white">There is no data available at the moment.</p></div>';
					}
		
				?>
				</div><!--End row -->
				<?php
				
			}//end if tab_user_form_update
				
			elseif($requested_form == "tab_permission_form_update"){ ?>
				<div class="row">
					<?php
					$tab_data = $db->rawQuery($sql);
					
					if ($db->count>0){
						foreach($tab_data as $logins){
					?>
					<div class="col-sm-6 col-md-4">
						<form action="xt_permission.php" method="post">
							<div class="box p-3 mb-3">
								<div class="row">
									<div class="col-12">
										<div class="form-group">
											<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Name</label>
											<input type="text" class="form-control rounded-bottom rounded-right" name="permission_name" value="<?=$logins['name']?>" autocomplete="off" required>
										</div>
									</div>
									<div class="col-12">
										<div class="form-group">
											<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Description</label>
											<textarea name="permission_description" class="form-control rounded-bottom rounded-right" required><?=$logins['description']?></textarea>
										</div>
									</div>
								</div>
								<div class="text-center" clear>
									<input type="hidden" name="permission_id" value="<?=$logins['id']?>">
									<input type="submit" value="SAVE" name="submitBtnUpdatePermission" class="btn bg-blue text-white btn-sm">
								</div>
							</div>
						</form>
					</div>
						<?php } ?>
				
					<?php
					}else{
						echo '<div class="col-12"><p class="notice notice-danger bg-white">There is no data available at the moment.</p></div>';
					}
					?>
					</div><!--End row -->
					<?php
			}//end if tab_permission_form_update
		
			elseif($requested_form == "tab_role_form_update"){ ?>
				<div class="row">
					<?php
					$tab_data = $db->rawQuery($sql);
					
					if ($db->count>0){
						foreach($tab_data as $role){
							
							//Getting user role permissions
							$sql_user_role_permissions = "SELECT urp.id_permission FROM ".$_SESSION['database'].".user_roles_permissions urp WHERE urp.id_user_role = '".$role['id']."'";
							$row_result_user_role_permissions = $db->rawQuery($sql_user_role_permissions);
							
							//Stores userrole permissions
							$permissions = array();
							
							foreach($row_result_user_role_permissions as $permission_id) {
								//Gets permissions names
								array_push($permissions, $permission_id['id_permission']);	
							}
					?>
					<div class="col-sm-6 col-md-4">
						<form action="xt_role.php" method="post" class="updateRoleForm" name="updateRoleForm">
							<div class="box p-3 mb-3">
								<div class="row">
									<div class="col-12">
										<div class="form-group">
											<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Name</label>
											<input type="text" class="form-control" name="role_name" value="<?php echo $role['name']?>" autocomplete="off" required>
										</div>
									</div>
									<div class="col-12">
										<div class="form-group">
											<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Description:</label>
											<textarea name="role_description" class="form-control" required><?php echo $role['description']?></textarea>
										</div>
									</div>
									<div class="col-12">
										<div class="form-group">
											<table class="table table-striped border">
												<thead>
													<tr>
														<th>Permission Name</th>
														<th>Yes</th>
													</tr>
												</thead>
												<tbody>
													<?php 
													$sql_all_permissions = "SELECT * FROM ".$_SESSION['database'].".permissions";

													$result_all_permissions = $db->rawQuery($sql_all_permissions);

													if ($db->count>0){
														foreach($result_all_permissions as $permission_field){
															?>
														<tr>
															<td><?php echo $permission_field['name']?></td>
															<td>
																<label class="label cusor-pointer d-flex text-center mb-0" for="perm_<?php echo $permission_field['id'].'-'.$role['id']?>">
																	<input class="label__checkbox" type="checkbox" name="permissions[]" value="<?php echo $permission_field['id']?>" type="checkbox" id="perm_<?php echo $permission_field['id'].'-'.$role['id']?>" <? if(in_array($permission_field['id'], $permissions)) echo "checked";?> />
																	<span class="label__text d-flex align-items-center">
																	  <span class="label__check d-flex rounded-circle mr-2">
																		<i class="fa fa-check icon small"></i>
																	  </span>
																	</span>
																</label>
															 </td>
														</tr>
													<?php
														}//End while
													}else{
														echo '<p class="alert alert-danger">There are not roles in the database.</p>';
													}
													?>
												</tbody>
											</table>
										</div>
									</div>
								</div>
								<div class="text-center" clear>
									<input type="hidden" name="role_id" value="<?=$role['id']?>">
									<input type="hidden" name="role_permissions_db" value="<?php print base64_encode(serialize($permissions)) ?>">
									<input type="submit" value="SAVE" name="submitBtnUpdateRole" class="btn bg-blue text-white btn-sm">
								</div>
							</div>
						</form>
					</div>
				
						<?php
						} ?>
				
			<?php
			}else{
				echo '<div class="col-12"><p class="notice notice-danger bg-white">There is no data available at the moment.</p></div>';
			}
			?>
			</div><!--End row -->
			<?
				
			}//end if tab_role_form_update
		
		}//End function createForm
		?>
		<!-- Tabs go here -->
				<? if(roleHasPermission('show_add_user_element', $_SESSION['role_permissions'])){ ?>

				<!-- Add User modal form-->
				<form action="xt_user.php" method="POST" name="addUserForm">
					<div class="modal fade" id="addUser" tabindex="-1" role="dialog" aria-labelledby="uploadModalTitle" aria-hidden="true">
					  <div class="modal-dialog modal-dialog-centered" role="document">
						<div class="modal-content">
						  <div class="modal-header">
							<h5 class="modal-title" id="uploadModalTitle">Add User</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							  <span aria-hidden="true">&times;</span>
							</button>
						  </div>
						  <div class="modal-body">
						  		<div class="form-group">
									<label class="text-uppercase small">First Name<span class="text-danger">*</span></label>
									<input type="text" name="first_name" placeholder="First Name" class="form-control" autocomplete="off" required />
								</div>	
							  	<div class="form-group">
									<label class="text-uppercase small">Last Name<span class="text-danger">*</span></label>
									<input type="text" name="last_name" placeholder="Last Name" class="form-control" autocomplete="off" required />
							  	</div>
								<div class="form-group">
									<label class="text-uppercase small">Email<span class="text-danger">*</span></label>
									<input type="text" name="login_username_new" placeholder="Email" class="form-control" autocomplete="off" required />
								</div>	
							  	<div class="form-group">
									<label class="text-uppercase small">Password<span class="text-danger">*</span></label>
									<input type="password" name="login_password_new" placeholder="Password" class="form-control" autocomplete="off" required />
								</div>
								<div class="form-group">
									<label class="text-uppercase small">Role<span class="text-danger">*</span></label>
									<select class="form-control custom-select-arrow pr-4" name="user_role_new" id="user_role_new" required>
										<?
											$roles = getUserRoles($_SESSION["user_role_name"]);
											foreach ($roles as $key => $value) {?>
											<option label="<?=strtoupper(str_replace('_',' ',$value));?>" value="<?=$key?>"><?=$value;?></option>
										<? } ?>
									</select>
								</div>	
							  	<div class="form-group">
									<label class="text-uppercase small">Status<span class="text-danger">*</span></label>
									<select class="form-control custom-select-arrow pr-4" name="user_status_new" required>
										<?
										$statuses = array("active"=>"1", "inactive"=>"0");
										foreach ($statuses as $keyArray => $status) {?>
										<option value="<?=$status?>" ><?=strtoupper($keyArray)?></option>
										<? } ?>
									</select>
								</div>
								<div class="form-group">
									<div id="input-store-select-div"></div>
								</div>
								<div class="form-group login_credentials">
									<span class="switch">
										<input type="checkbox" class="switch" id="switch-send-password" name="switch-send-password" value="">
										<label for="switch-send-password" class="font-weight-light font-lg letter-spacing-1 d-inline-block">Send password by email <span class="text-danger"> *</span></label>
									</span>
								</div>
						  </div>
							<div class="modal-footer">
								<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
								<input type="submit" class="btn bg-blue text-white btn-sm" value="Save" id="submitBtnAddUser" name="submitBtnAddUser">
								<input type="hidden" name="type" value="admin">
							</div>

						</div>
					  </div>
					</div>
				</form>
				<!-- End Add User modal form-->

				<? } ?>
				
				<? if(roleHasPermission('show_change_password_element', $_SESSION['role_permissions'])){ ?>
				
				<!-- Change User Password modal form-->
				<form action="xt_user.php" method="POST" name="changeUserPassword" id="changeUserPassword">
					<div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalTitle" aria-hidden="true">
					  <div class="modal-dialog modal-dialog-centered" role="document">
						<div class="modal-content">
						  <div class="modal-header">
							<h5 class="modal-title" id="uploadModalTitle">Change Password</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							  <span aria-hidden="true">&times;</span>
							</button>
						  </div>
						  <div class="modal-body">
								<div class="form-group userModalMsgContainer">
									<div class="alert alert-danger alert-dismissible fade show" role="alert">
										<strong>Error!</strong> <span class="userModalMsg"></span> 
										<button type="button" class="close" data-dismiss="alert" aria-label="Close">
											<span aria-hidden="true">×</span>
										</button>
									</div>
								</div>
								<div class="form-group">
									<label class="text-uppercase small">New Password<span class="text-danger">*</span></label>
									<input type="password" id="new_pass" name="new_pass" placeholder="New Password" class="form-control" autocomplete="off" required />
									<label class="text-uppercase small">Confirm Password<span class="text-danger">*</span></label>
									<input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm Password" class="form-control" autocomplete="off" required />
								</div>
						  </div>
							<div class="modal-footer">
								<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
								<input type="hidden" name="user_id_change_pass" id="user_id_change_pass" value="">
								<input type="hidden" name="action" value="change_user_pass_form">
								<input type="submit" class="btn bg-blue text-white btn-sm" value="Save Password" id="submitBtnUpdateMyPassword" name="submitBtnUpdateMyPassword">
							</div>

						</div>
					  </div>
					</div>
				</form>
				<!-- End Change User Password modal form-->
				
				<? } ?>
				
				<? if(roleHasPermission('delete_user', $_SESSION['role_permissions'])){ ?>
				
				<!-- Delete User modal form-->
				<form action="xt_user.php" method="POST" name="deleteUserForm" id="deleteUserForm">
					<div class="modal fade" id="deleteUserModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalTitle" aria-hidden="true">
					  <div class="modal-dialog modal-dialog-centered" role="document">
						<div class="modal-content">
						  <div class="modal-header">
							<h5 class="modal-title" id="uploadModalTitle">Delete User</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							  <span aria-hidden="true">&times;</span>
							</button>
						  </div>
						  <div class="modal-body">
							<div class="form-group delete_confirmation_input">
								<label class="text-uppercase small font-weight-bold">Type DELETE to confirm <span class="text-danger">*</span></label>
								<input type="text" name="delete_confirmation_ok" id="delete_confirmation_ok" class="form-control" autocomplete="off" required="">
							</div>
							<div id="delete_confirmation_msg" class="d-none">
								<p class="alert alert-danger" id="delete_confirmation_msg_para"></p>
							</div>
						  </div>
							<div class="modal-footer">
								<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
								<input type="hidden" name="user_id_delete" id="user_id_delete" value="">
								<input type="submit" class="btn bg-blue text-white btn-sm" name="submitBtnDeleteUser" id="submitBtnDeleteUser" value="Delete User">
							</div>
						</div>
					  </div>
					</div>
				</form>
				<!-- End Delete User modal form-->
				
				<? } ?>
				
				
				<? if(roleHasPermission('show_add_role_element', $_SESSION['role_permissions'])){ ?>
				<!-- Add Role modal form-->
				<form action="xt_role.php" method="POST" name="addRoleForm">
					<div class="modal fade" id="addRole" tabindex="-1" role="dialog" aria-labelledby="uploadModalTitle" aria-hidden="true">
					  <div class="modal-dialog modal-dialog-centered" role="document">
						<div class="modal-content">
						  <div class="modal-header">
							<h5 class="modal-title" id="uploadModalTitle">Add Role</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							  <span aria-hidden="true">&times;</span>
							</button>
						  </div>
						  <div class="modal-body">
								<div class="form-group">
									<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Name<span class="text-danger">*</span></label>
									<input type="text" name="role_name" placeholder="Name" class="form-control" required />
								</div>
								<div class="form-group">
									<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Description<span class="text-danger">*</span></label>
									<textarea name="role_description" placeholder="Description" class="form-control" required></textarea>
								</div>
								<table class="table table-striped border">
									<thead>
										<tr>
											<th>Permission Name</th>
											<th>Yes</th>
										</tr>
									</thead>
									<tbody>
										<?php 
										$sql_all_permissions = "SELECT * FROM ".$_SESSION['database'].".permissions";

										$permissions_result = $db->rawQuery($sql_all_permissions);

										if ($db->count>0){
											foreach($permissions_result as $permission_field){

											?>
											<tr>
												<td><?php echo $permission_field['name']?></td>
												<td>
													<label class="label cusor-pointer d-flex text-center mb-0" for="perm_<?php echo $permission_field['id']?>">
														<input class="label__checkbox" type="checkbox" name="permissions[]" value="<?php echo $permission_field['id']?>" type="checkbox" id="perm_<?php echo $permission_field['id']?>" <? if(in_array($permission_field['id'], $permissions)) echo "checked";?> />
														<span class="label__text d-flex align-items-center">
														  <span class="label__check d-flex rounded-circle mr-2">
															<i class="fa fa-check icon small"></i>
														  </span>
														</span>
													</label>
												</td>
											</tr>
										<?php
											}//End while
										}else{
											echo '<p class="alert alert-danger">There are not roles in the database.</p>';
										}
										?>
									</tbody>
								</table>
						  </div>
							<div class="modal-footer">
								<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
								<input type="submit" class="btn bg-blue text-white btn-sm" value="Save" id="submitBtnAddRole" name="submitBtnAddRole">
								<input type="hidden" name="type" value="admin">
							</div>

						</div>
					  </div>
					</div>
				</form>
				<!-- End Add Role modal form-->
				
				<!-- Add Permission modal form-->
				<form action="xt_permission.php" method="POST" name="addPermissionForm">
					<div class="modal fade" id="addPermission" tabindex="-1" role="dialog" aria-labelledby="uploadModalTitle" aria-hidden="true">
					  <div class="modal-dialog modal-dialog-centered" role="document">
						<div class="modal-content">
						  <div class="modal-header">
							<h5 class="modal-title" id="uploadModalTitle">Add permission</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							  <span aria-hidden="true">&times;</span>
							</button>
						  </div>
						  <div class="modal-body">
								<div class="form-group">
									<label class="text-uppercase small">Name<span class="text-danger">*</span></label>
									<input type="text" name="permission_name" placeholder="Name" class="form-control" required />
								</div>
								<div class="form-group">
									<label class="text-uppercase small">Description<span class="text-danger">*</span></label>
									<textarea name="permission_description" placeholder="Description" class="form-control" required></textarea>
								</div>
						  </div>
							<div class="modal-footer">
								<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
								<input type="submit" class="btn bg-blue text-white btn-sm" value="Save" id="submitBtnAddPermission" name="submitBtnAddPermission">
								<input type="hidden" name="type" value="admin">
							</div>

						</div>
					  </div>
					</div>
				</form>
				<!-- End Add Permission modal form-->
				<? } ?>


        </main>
      </div>
    </div>


    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>
	
	<!--<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
    <script type="text/javascript" src="//cdn.datatables.net/plug-ins/1.10.13/sorting/datetime-moment.js"></script>-->
	<script>
	$(document).ready(function(){
		$('.userModalMsgContainer').hide();	
	});
	
	//Send ajax request
	$("#user_role_new").change(function(event) {
		$.ajax({
			type: "POST",
			url: "<?php  echo getFullUrl();  ?>/admin/security-settings/xt_user_store.php",
			data: {"user_role_new_ajax":$( "#user_role_new" ).val()},
			dataType:"html",
			cache: false,
			success: function(result){	  
				if(result != ''){	
					if($("#input-store-select").length){
						$("#input-store-select").replaceWith(result);
					}else{
						$("#input-store-select-div").append('<label for="store_select" class="text-uppercase small">Store:</label>');
						$("#input-store-select-div").append(result);
					}
				}else{
					
					$("#input-store-select").remove();
					$("label[for='store_select']").remove();
				}
			},
			error: function(xhr, status, error) {
			  var err = eval("(" + xhr.responseText + ")");
			  console.log(err.Message);
			} 
		});
	});
	
	$("#submitBtnUpdateMyPassword").click(function(event) {
		event.preventDefault();
		
		if($('#new_pass').val() == $('#password_confirmation').val()){
			$('.userModalMsgContainer').hide();
			$("#changeUserPassword").submit();
		}else{
			$('.userModalMsg').text('Passwords are not matching');
			$('.userModalMsgContainer').show();
		}
	});
	
	// Delete User
	$(".del_user").click(function(event) {
		var user_id = $( this ).data('userid');
		
		$("#user_id_delete").val(user_id);
	});
	
	// Delete User
	$("#submitBtnDeleteUser").click(function(event) {
		event.preventDefault();
	
		if($("#delete_confirmation_ok").val() == "DELETE"){
			$("#deleteUserForm").submit();
		}else{
			$("#delete_confirmation_msg_para").text('Please enter the text exactly as it is displayed to confirm.');
			$( "#delete_confirmation_msg" ).removeClass("d-none");
		}
	});
	
	$("input[name=switch-send-password]").click(function(){
		if($(this).is(":checked")){
			$(this).attr("value", "1");
		}
		else if($(this).is(":not(:checked)")){
			$(this).attr("value", "0");
		}
	});
	
	// Login as this user
	$(".log_as_user").click(function(event) {
		event.preventDefault();
		
		var user_token = $( this ).data('token');
		
		if(confirm("Are you sure you want to login as this user?")){
			window.location.href = location.protocol+'//'+location.hostname+'/xt_login.php?token='+user_token;
		}
	});
	
	function setUserId(linkElement){
		$("#user_id_change_pass").val(linkElement);	
	}
	</script>
  </body>
</html>