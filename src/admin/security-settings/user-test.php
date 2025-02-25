<!doctype html>
<html lang="en">
  <head>
	<link href="//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
	<link href="//cdn.datatables.net/buttons/1.5.6/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/head.php");?>

    <title>Call Log | Fully Promoted</title>
	<link rel="stylesheet" href="/admin/security-settings/style.css">
  </head>
  <body class="bg-light cbp-spmenu-push">
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/top.php"); ?>

    <div class="container-fluid">

      <div class="row">
        <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/nav.php"); ?>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 p-0 mb-4">
			
			<div class="p-0 border-bottom mb-4">
				<div class="border-bottom-dotted d-flex d-block align-items-center clearfix py-2 px-4">
					<h1 class="h2 font-light mb-0 text-center text-sm-left"><i class="fas fa-users-cog mr-2"></i> User Management</h1>
					<div class="ml-auto">
						<!--<button type="button" data-toggle="modal" data-target="#addUser" class="border-0 bg-transparent">
							<i class="fas fa-2x text-muted fa-plus-circle"></i>
						</button>
						<button type="button" data-toggle="modal" data-target="#addPermission" class="border-0 bg-transparent">
							<i class="fas fa-2x text-muted fa-plus-circle"></i>
						</button>
						<button type="button" data-toggle="modal" data-target="#addRole" class="border-0 bg-transparent">
							<i class="fas fa-2x text-muted fa-plus-circle"></i>
						</button>-->
						<div class="btn-toolbar">
							<? if(roleHasPermission('show_add_user_element', $_SESSION['role_permissions'])){ ?>
							<a href="#" class="btn btn-primary pull-right" data-toggle="modal" data-target="#addUser"><i class="fa fa-plus-circle" aria-hidden="true"></i> Add User</a>
							<? } ?> 
							<? if(roleHasPermission('show_add_role_element', $_SESSION['role_permissions'])){ ?>
							<a href="#" class="btn btn-primary pull-right" data-toggle="modal" data-target="#addPermission"><i class="fa fa-plus-circle" aria-hidden="true"></i> Add Permission</a>
							<a href="#" class="btn btn-primary pull-right" data-toggle="modal" data-target="#addRole"><i class="fa fa-plus-circle" aria-hidden="true"></i> Add Role</a>
							<? } ?> 
						</div>
					</div>
				</div>
			</div>
        	<div class="px-4 py-3">
				
				<?php if(isset($_SESSION['success'])){ ?>
				<div class="alert alert-success alert-dismissible fade show mt-4" role="alert">
				  <strong>Success!</strong> <?php echo $_SESSION['success'];?>
				  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				  </button>
				</div>
				<?php unset($_SESSION['success']); } ?>
				<?php if(isset($_SESSION['error'])){ ?>
				<div class="alert alert-danger alert-dismissible fade show mt-4" role="alert">
				  <strong>Error!</strong> <?php echo $_SESSION['error'];?>
				  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				  </button>
				</div>
				<?php unset($_SESSION['error']); } ?>

			  <!-- Tabs go here -->
				
		<? 
		//Creates customized tabs depending on the user role
		createTabs();
		
		//Creates customized tabs depending on the user role
		function createTabs(){
			
			if($_SESSION['admin'] && $_SESSION["user_role_name"] == 'admin_root'){
			?>
			  <!-- Nav tabs -->
			  <ul class="nav nav-tabs" role="tablist">
				<li class="nav-item">
				  <a class="nav-link active" data-toggle="tab" href="#tabs-admin-users">Admin Users</a>
				</li>
				<li class="nav-item">
				  <a class="nav-link" data-toggle="tab" href="#tabs-store-users">Store Users</a>
				</li>
				<li class="nav-item">
				  <a class="nav-link" data-toggle="tab" href="#tabs-inactive-stores">Inactive Store Users</a>
				</li>
				<li class="nav-item">
				  <a class="nav-link" data-toggle="tab" href="#tabs-all-permissions">All Permissions</a>
				</li>
				<li class="nav-item">
				  <a class="nav-link" data-toggle="tab" href="#tabs-all-roles">All Roles</a>
				</li>
			  </ul>
			  
			  <!-- Tab panes -->
			  <div class="tab-content">
				<div id="tabs-admin-users" class="container tab-pane active">
					<?
					//$sql = "SELECT strl.* FROM ".$_SESSION['database'].".storelogin strl WHERE strl.storeid='".$_SESSION['storeid']."'";
					$sql = "SELECT strl.* FROM ".$_SESSION['database'].".storelogin strl WHERE strl.storeid < '0'";
					createForm($sql, 'tab_user_form_update');
					?>
				</div>
				<div id="tabs-store-users" class="container tab-pane fade"><br>
					<?
					$sql = "SELECT strl.* FROM ".$_SESSION['database'].".locationlist locl, ".$_SESSION['database'].".storelogin strl WHERE locl.storeid = strl.storeid";
					createForm($sql, 'tab_user_form_update');
					?>
				</div>
				<div id="tabs-inactive-stores" class="container tab-pane fade"><br>
					<?
					$sql = "SELECT t1.* FROM ".$_SESSION['database'].".storelogin t1 LEFT JOIN ".$_SESSION['database'].".locationlist t2 ON t2.storeid = t1.storeid WHERE t2.storeid IS NULL AND t1.storeid > 0";
					createForm($sql, 'tab_user_form_update');
					?>
				</div>
				<div id="tabs-all-permissions" class="container tab-pane fade"><br>
					<?
					$sql = "SELECT * FROM ".$_SESSION['database'].".permissions";
					createForm($sql, 'tab_permission_form_update');
					?>
				</div>
				<div id="tabs-all-roles" class="container tab-pane fade"><br>
					<?
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
			  <div class="tab-content">
				<div id="tabs-rep-admin-users" class="container tab-pane fade"><br>
					<?
					//Retrieves all admin_rep that were assigned to a store
					//$sql = "SELECT strl.*, strlur.id_user_roles FROM ".$_SESSION['database'].".storelogin strl, ".$_SESSION['database'].".storelogin_user_roles strlur, ".$_SESSION['database'].".reps rep WHERE strl.storeid='".$_SESSION['storeid']."' AND strl.id = strlur.id_storelogin AND strlur.id_user_roles = (SELECT id FROM ".$_SESSION['database'].".user_roles WHERE name = 'admin_rep') AND strl.email IN (SELECT email FROM ".$_SESSION['database'].".reps) AND strl.email = rep.email AND rep.id IN (SELECT rep FROM ".$_SESSION['database'].".locationlist)";
					$sql = "SELECT strl.*, strlur.id_user_roles FROM ".$_SESSION['database'].".storelogin strl, ".$_SESSION['database'].".storelogin_user_roles strlur, ".$_SESSION['database'].".reps rep WHERE strl.storeid<'0' AND strl.id = strlur.id_storelogin AND strlur.id_user_roles = (SELECT id FROM ".$_SESSION['database'].".user_roles WHERE name = 'admin_rep') AND strl.email IN (SELECT email FROM ".$_SESSION['database'].".reps) AND strl.email = rep.email AND rep.id IN (SELECT rep FROM ".$_SESSION['database'].".locationlist)";
					//createForm($sql, 'tab_rep_admin_users');
					?>
				</div>
				<div id="tabs-rep-store-users" class="container tab-pane fade"><br>
					<?
					//Retrieves the users from the store that were assigned to a representative
					$sql = "SELECT strl.*, strlur.id_user_roles, locl.storeid, locl.companyname, locl.rep FROM ".$_SESSION['database'].".locationlist locl, ".$_SESSION['database'].".storelogin strl, ".$_SESSION['database'].".reps rept, ".$_SESSION['database'].".storelogin_user_roles strlur WHERE locl.rep != '' AND locl.storeid = strl.storeid AND rept.id = locl.rep AND rept.id = (SELECT id FROM ".$_SESSION['database'].".reps WHERE email = '".$_SESSION['username']."') AND strl.id = strlur.id_storelogin";
					//createForm($sql, 'tab_rep_store_users');
					?>
				</div>
			</div>
			<?
			}elseif($_SESSION['admin'] && ($_SESSION["user_role_name"] == 'admin_root' || $_SESSION["user_role_name"] == 'admin_rep') && $_SESSION['storeid'] > 0){
				$sql = "SELECT * FROM ".$_SESSION['database'].".storelogin WHERE storeid='".$_SESSION['storeid']."'";
				//createForm($sql, 'tab_user_form_update');
			}else{
				$sql = "SELECT * FROM ".$_SESSION['database'].".storelogin WHERE email='".$_SESSION['username']."' and id='".$_SESSION['user_id']."'";
				//createForm($sql, 'tab_user_form_update');
            }
		}// End function createTabs()
		
		
		function createForm($sql, $requested_form){
			
			global $db;
			
			if(($requested_form == "tab_user_form_update") || ($requested_form == "tab_rep_admin_users") || ($requested_form == "tab_rep_store_users")  ){
			?>
				<div class="row">
					<?
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
					<form action="xt_user.php" method="post">
						
							<div class="box">
								<h2>Login Information</h2>
								<div>
									<div class="row">
										<div class="col-xs-12 col-sm-6">
											<div class="field">
												<label>Username</label>
												<input type="text" class="form-control" name="login_username" value="<?=$logins['email']?>" autocomplete="off" required>
											</div>
										</div>
										<div class="col-xs-12 col-sm-6">
											<div class="field">
												<label>Password</label>
												<?if(($_SESSION["user_role_name"] == 'admin_rep')){?>
													<?if(($_SESSION["name"] == $logins['email']) && ($requested_form == "tab_rep_admin_users")){ ?>
													<input type="password" class="form-control" name="login_password" value="<?=$logins['password']?>" autocomplete="off" required>
													<? }elseif(($_SESSION["name"] != $logins['email']) && ($requested_form == "tab_rep_admin_users")){ ?>
													<input type="password" class="form-control" name="login_password" value="<?=$logins['password']?>" autocomplete="off" readonly >
													<? }elseif(($requested_form == "tab_rep_store_users")){ ?>
													<input type="password" class="form-control" name="login_password" value="<?=$logins['password']?>" autocomplete="off" required>
													<? }else{?>
													<input type="password" class="form-control" name="login_password" value="<?=$logins['password']?>" autocomplete="off" required>
													<? } ?>
												<? }else{ ?>
													<input type="password" class="form-control" name="login_password" value="<?=$logins['password']?>" autocomplete="off" required>
												<? } ?>
											</div>
										</div>
										<?if($_SESSION["user_role_name"] == 'admin_root' || $_SESSION["user_role_name"] == 'admin_rep'){?>
										<div class="col-xs-12 col-sm-6">
											<div class="field">
												<label>Role:</label>
												<select name="user_role" class="design" required>
													<option value="n/a">N/A</option>
													<?
													$roles = getUserRoles($_SESSION["user_role_name"], $requested_form);
													foreach ($roles as $key => $value) {?>
													<option value="<?=$key?>" <? if($id_user_role == $key) echo "selected";?>><?=strtoupper($value)?></option>
													<? } ?>
												</select>
											</div>
										</div>
										<? } ?>
										<?if($_SESSION["user_role_name"] == 'admin_root' || $_SESSION["user_role_name"] == 'admin_rep'){?>
										<div class="col-xs-12 col-sm-6">
											<div class="field">
												<label>Status:</label>
												<select name="user_status" class="design" required>
													<?
													$statuses = array("active"=>"1", "inactive"=>"0");
													foreach ($statuses as $keyArray => $status) {?>
													<option value="<?=$status?>" <? if($logins['status'] == $status) echo "selected";?>><?=strtoupper($keyArray)?></option>
													<? } ?>
												</select>
											</div>
										</div>
										<? } ?>
										<div class="col-xs-12 col-sm-6">
                                            <div class="field">
												<?if(($_SESSION["user_role_name"] == 'admin_rep') && ($requested_form == "tab_rep_admin_users")){?>
												<label style="display:none;">Store Id</label>
                                                <input type="hidden" class="form-control" name="storeid" value="<?=$logins['storeid']?>" autocomplete="off" readonly >
												<? }elseif(($_SESSION["user_role_name"] == 'admin_general')){ ?>
												<label style="display:none;">Store Id</label>
                                                <input type="hidden" class="form-control" name="storeid" value="<?=$logins['storeid']?>" autocomplete="off" readonly >
												<? }else{ ?>
												<label>Store Id</label>
												<input type="text" class="form-control" name="storeid" value="<?=$logins['storeid']?>" autocomplete="off" readonly >
												<? } ?>
                                            </div>
                                        </div>
									</div>
										<div class="text-center" clear>
											<input type="hidden" name="user_id" value="<?=$logins['id']?>">
											<input type="submit" value="SAVE" name="submitBtnUpdateUser" class="btn bg-blue text-white btn-sm">
										</div>
								</div>
							</div>
						
					</form>

						<? } ?>
				
					<?
					}else{
						echo '<p class="alert alert-danger">There is not data available at the moment.</p>';
					}
		
				?>
				</div><!--End row -->
				<?
				
			}//end if tab_user_form_update
				
			elseif($requested_form == "tab_permission_form_update"){ ?>
				<div class="row">
					<?
					$tab_data = $db->rawQuery($sql);
					
					if ($db->count>0){
						foreach($tab_data as $logins){
					?>
					<form action="xt_permission.php" method="post">
							<div class="box">
								<h2>Permission Information</h2>
								<div>
									<div class="row">
										<div class="col-xs-12">
											<div class="field">
												<label>Name</label>
												<input type="text" class="form-control" name="permission_name" value="<?=$logins['name']?>" autocomplete="off" required>
											</div>
										</div>
										<div class="col-xs-12">
											<div class="field">
												<label>Description:</label>
												<textarea name="permission_description" class="form-control" required><?=$logins['description']?></textarea>
											</div>
										</div>
									</div>
									<div class="text-center" clear>
										<input type="hidden" name="permission_id" value="<?=$logins['id']?>">
										<input type="submit" value="SAVE" name="submitBtnUpdatePermission" id="submitBtnUpdatePermission" class="btn bg-blue text-white btn-sm">
									</div>
								</div>
							</div>
					</form>
						<? } ?>
				
					<?
					}else{
						echo '<p class="alert alert-danger">There are not permissions in the database.</p>';
					}
					?>
					</div><!--End row -->
					<?
			}//end if tab_permission_form_update
		
			elseif($requested_form == "tab_role_form_update"){ ?>
				<div class="row">
					<?
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
					<form action="xt_role.php" method="post">
							<div class="box">
								<h2>Role Information</h2>
								<div>
									<div class="row">
										<div class="col-xs-12">
											<div class="field">
												<label>Name</label>
												<input type="text" class="form-control" name="role_name" value="<?=$role['name']?>" autocomplete="off" required>
											</div>
										</div>
										<div class="col-xs-12">
											<div class="field">
												<label>Description:</label>
												<textarea name="role_description" class="form-control" required><?=$role['description']?></textarea>
											</div>
										</div>
										<div class="col-xs-12">
											<div class="field">
												<table>
													<tr>
														<th>Permission Name</th>
														<th>Yes</th>
													</tr>
													<?php 
													$sql_all_permissions = "SELECT * FROM ".$_SESSION['database'].".permissions";
													//$result_all_permissions = $conn->query($sql_all_permissions);
													
													$result_all_permissions = $db->rawQuery($sql_all_permissions);

													if ($db->count>0){
														foreach($result_all_permissions as $permission_field){ ?>
														<tr>
															<td><?=$permission_field['name']?></td>
															<td><input class="form-check-input" type="checkbox" name="permissions[]" value="<?=$permission_field['id']?>" <? if(in_array($permission_field['id'], $permissions)) echo "checked";?>></td>
														</tr>
													<?
														}//End while
													}else{
														echo '<p class="alert alert-danger">There are not roles in the database.</p>';
													}
													?>
												</table>
											</div>
										</div>
									</div>
									<div class="text-center" clear>
										<input type="hidden" name="role_id" value="<?=$role['id']?>">
										<input type="hidden" name="role_permissions_db" value="<?php print base64_encode(serialize($permissions)) ?>">
										<input type="submit" value="SAVE" name="submitBtnUpdateRole" id="submitBtnUpdateRole" class="btn bg-blue text-white btn-sm">
									</div>
								</div>
							</div>
					</form>
				
						<?
						} ?>
				
			<?
			}else{
				echo '<p class="alert alert-danger">There are not roles in the database.</p>';
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
									<label class="text-uppercase small">Username<span class="text-danger">*</span></label>
									<input type="text" name="login_username_new" placeholder="User Name" class="form-control" autocomplete="off" required />
									<label class="text-uppercase small">Password<span class="text-danger">*</span></label>
									<input type="password" name="login_password_new" placeholder="Password" class="form-control" autocomplete="off" required />
								</div>
								<div class="form-group">
									<label class="text-uppercase small">Role<span class="text-danger">*</span></label>
									<select class="form-control custom-select-arrow pr-4" name="user_role_new" id="user_role_new" required onchange="showStores()">
										<?
											$roles = getUserRoles($_SESSION["user_role_name"]);
											foreach ($roles as $key => $value) {?>
											<option label="<?=strtoupper(str_replace('_',' ',$value));?>" value="<?=$key?>"><?=$value;?></option>
										<? } ?>
									</select>
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
									<label class="text-uppercase small">Country<span class="text-danger">*</span></label>
									<select class="form-control custom-select-arrow pr-4" id="input-country" name="user_country" required>
										  <option value="AU">Australia</option>
										  <option value="AT">Austria</option>
										  <option value="BE">Belgium</option>
										  <option value="BR">Brazil</option>
										  <option value="CAN">Canada</option>
										  <option value="CN">China</option>
										  <option value="DK">Denmark</option>
										  <option value="FI">Finland</option>
										  <option value="FR">France</option>
										  <option value="DE">Germany</option>
										  <option value="HK">Hong Kong</option>
										  <option value="IE">Ireland</option>
										  <option value="IT">Italy</option>
										  <option value="JP">Japan</option>
										  <option value="LU">Luxembourg</option>
										  <option value="MX">Mexico</option>
										  <option value="NL">Netherlands</option>
										  <option value="NZ">New Zealand</option>
										  <option value="NO">Norway</option>
										  <option value="PT">Portugal</option>
										  <option value="SG">Singapore</option>
										  <option value="ES">Spain</option>
										  <option value="SE">Sweden</option>
										  <option value="CH">Switzerland</option>
										  <option value="GB">United Kingdom</option>
										  <option value="USA" selected="selected">United States</option>
									</select>
									<label class="text-uppercase small">Status<span class="text-danger">*</span></label>
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
									<label class="text-uppercase small">Name<span class="text-danger">*</span></label>
									<input type="text" name="role_name" placeholder="Name" class="form-control" required />
								</div>
								<div class="form-group">
									<label class="text-uppercase small">Description<span class="text-danger">*</span></label>
									<textarea name="role_description" placeholder="Description" class="form-control" required></textarea>
								</div>
								<table>
									<tr>
										<th>Permission Name</th>
										<th>Yes</th>
									</tr>
									<?php 
									$sql_all_permissions = "SELECT * FROM ".$_SESSION['database'].".permissions";
									
									$permissions_result = $db->rawQuery($sql_all_permissions);
									
									if ($db->count>0){
										foreach($permissions_result as $permission_field){
											
										?>
										<tr>
											<td><?=$permission_field['name']?></td>
											<td><input class="form-check-input" type="checkbox" name="permissions[]" value="<?=$permission_field['id']?>"></td>
										</tr>
									<?
										}//End while
									}else{
										echo '<p class="alert alert-danger">There are not roles in the database.</p>';
									}
									?>
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
	<script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
	<script src="//cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
	<script src="//cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
	<script src="//cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
	<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
    <script type="text/javascript" src="//cdn.datatables.net/plug-ins/1.10.13/sorting/datetime-moment.js"></script>  
	<script>
		$(document).ready( function () {
			$.fn.dataTable.moment('MM/DD/YYYY');
			var table = $('table').DataTable({
				pageLength: 50,
				dom: '<"row mb-2"<"col-6"B>><"row"<"col-6"l><"col-6"f>>rt<"row"<"col-6"i><"col-6"p>>',
				buttons: [
					{ extend: 'excelHtml5', 
					text: 'Export',
					 className: 'btn btn-sm bg-blue rounded-pill text-white border-0 text-uppercase px-3'
					}
				],
				order:[2,"desc"],
				"columnDefs": [ {
					"targets": -1,
					"orderable": false
				} ]
			});
			
		});
		if($( window ).width()<992){
			$('.collapse').collapse('hide')
		}
		$( window ).resize(function() {
			if($( window ).width()<992){
				$('.collapse').collapse('hide')
			}else{
				$('.collapse').collapse('show')
			}
		});
	
		function showStores() {
		  //Send ajax request
		  $.ajax({
			  url: "<?php  echo getFullUrl();  ?>/admin/security-settings/xt_user_store.php",
			  method: "POST",
			  data: {user_role_new_ajax:$( "#user_role_new" ).val()},
			  cache: false,
			  dataType: "html"
			  }).success(function( html ) { 

				if(html != ''){	
					if($("#input-store-select").length){
						$("#input-store-select").replaceWith(html);
					}else{
						$("#input-store-select-div").append('<label for="store_select">Store:</label>');
						$("#input-store-select-div").append(html);
					}
				}else{
					$("#input-store-select").remove();
					$("label[for='store_select']").remove();
				}
			  });
		}
	
	</script>	
  </body>
</html>