<div class="navbar-expand-md">
	<div class="collapse navbar-collapse" id="navbarSupportedContent">
		<nav class="col-md-3 col-lg-2  bg-blue sidebar">
			<button class="navbar-toggler d-block d-md-none mr-2 float-right" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
				<i class="fas fa-times text-white"></i>
			</button>
		  <div class="sidebar-fixed">
			  <!--<div class="text-center logo border-bottom d-none d-md-block"><a href=""><img src="/img/FP-logo-white.png" alt="Yes We're Open" class="img-fluid py-2"></a></div>-->
			<ul class="nav flex-column mt-2 w-100"> 
				<?php if($_SESSION['view']=="user") { ?>
				  <?php if(roleHasPermission('show_dashboard', $_SESSION['role_permissions'])){ ?>
				  <li class="nav-item">
					<a class="nav-link text-white bg-dark-blue-hover" href="/dashboard.php">
					  <i class="fas fa-tachometer-alt fa-fw mr-1"></i>
					  Dashboard
					</a>
				  </li>
				  <?php } ?>
				  <?php if(roleHasPermission('general_permission', $_SESSION['role_permissions'])){ ?>
				  <li class="nav-item">
					<a class="nav-link text-white bg-dark-blue-hover" href="/location-details/">
					  <i class="fas fa-map-marker-alt fa-fw mr-1"></i>
					  Location Details
					</a>
				  </li>
				  <?php } ?>
				<?php if(roleHasPermission('show_plan_and_publish', $_SESSION['role_permissions'])){ ?>
					<li class="nav-item">
					<a class="nav-link text-white bg-dark-blue-hover" href="/plan-and-publish/social-media/">
					  <i class="fas fa-edit fa-fw mr-1"></i>
					  Plan &amp; Publish
					</a>
				  </li>
				<?php } ?>
				<?php if(roleHasPermission('show_graphics_gallery', $_SESSION['role_permissions'])){ ?>
				  <li class="nav-item">
					<a class="nav-link text-white bg-dark-blue-hover" href="/graphics-gallery/">
					  <i class="fas fa-images fa-fw mr-1"></i>
					  Graphics Gallery
					</a>
				  </li>
				<?php } ?>
				<?php if(roleHasPermission('show_reputation_management', $_SESSION['role_permissions'])){ ?>
				<li class="nav-item">
					<a class="nav-link text-white bg-dark-blue-hover" href="/reputation-management/">
					  <i class="fas fa-star fa-fw mr-1"></i>
					  Reputation Management 
					</a>
				</li>
				<?php } ?>
				<?php if(roleHasPermission('show_ongoing_campaigns', $_SESSION['role_permissions'])){ ?>
					<li class="nav-item">
						<a class="nav-link text-white bg-dark-blue-hover" href="/campaign/campaign-info.php">
							<i class="fas fa-clipboard-list fa-fw mr-1"></i> Campaigns Management 
						</a>
					</li>
				<?php } ?>
				<?php if(roleHasPermission('show_promote_link', $_SESSION['role_permissions']) || roleHasPermission('show_promote_settings', $_SESSION['role_permissions'])){ ?>
				 <li class="nav-item">
					<a class="nav-link text-white bg-dark-blue-hover collapsed" href="#submenuPsub1" data-toggle="collapse" data-target="#submenuPsub1">
					  <i class="fas fa-bullhorn fa-fw"></i> Promote
					</a>
					<div class="collapse" id="submenuPsub1" aria-expanded="false">
						<ul class="flex-column nav">
							<?php if(roleHasPermission('show_promote_link', $_SESSION['role_permissions']) && $active_location && isset( $active_location['promote_platform'] ) && ($active_location['promote_platform'] == 'mc' || $active_location['promote_platform'] == '')){ ?>
							<li class="nav-item">
								<a class="nav-link text-white pl-4 py-1 bg-dark-blue-hover collapsed" href="/promote/">
									<i class="fas fa-mail-bulk fa-fw"></i> Campaign Management
								</a>
							</li>
							<?php } ?>
							<?php if(roleHasPermission('show_promote_link', $_SESSION['role_permissions']) && $active_location && isset( $active_location['promote_platform'] ) && ($active_location['promote_platform'] == 'cc') ){ ?>
							<li class="nav-item">
								<a class="nav-link text-white pl-4 py-1 bg-dark-blue-hover collapsed" href="/promote-cc/">
									<i class="fas fa-mail-bulk fa-fw"></i> Campaign Management
								</a>
							</li>
							<?php } ?>
							<?php if(roleHasPermission('show_promote_settings', $_SESSION['role_permissions'])){ ?>
							<li class="nav-item">
								<a class="nav-link text-white pl-4 py-1 bg-dark-blue-hover collapsed" href="/settings/promote/">
									<i class="fas fa-envelope fa-fw"></i> Promote Settings
								</a>
							</li>
							<?php } ?>
						</ul>
					</div>
				 </li>
				 <?php } ?>
					<!--<li class="nav-item">
						<a class="nav-link text-white bg-dark-blue-hover collapsed" href="#submenu1sub1" data-toggle="collapse" data-target="#submenu1sub1"><i class="fas fa-chart-line fa-fw mr-1"></i> Track</a>
						<div class="collapse" id="submenu1sub1" aria-expanded="false">
							<ul class="flex-column nav">
								<li class="nav-item bg-dark-blue-hover">
									<a class="nav-link text-white pl-4 py-1" href="/track/google-analytics/audience.php">
										<i class="fas fa-users fa-fw "></i> Audience Overview
									</a>
								</li>
								<li class="nav-item bg-dark-blue-hover">
									<a class="nav-link text-white pl-4 py-1" href="/track/google-analytics/landing.php">
										<i class="fas fa-file-alt fa-fw"></i> Content Overview
									</a>
								</li>
								<li class="nav-item bg-dark-blue-hover">
									<a class="nav-link text-white pl-4 py-1" href="/track/forms.php">
										<i class="fas fa-table fa-fw"></i> Form Data
									</a>
								</li>
								<li class="nav-item bg-dark-blue-hover">
									<a class="nav-link text-white pl-4 py-1" href="/track/campaign-stats/campaign-data.php">
										<i class="fas fa-chart-line fa-fw"></i> Campaign Data
									</a>
								</li>
							</ul>
						</div>
					</li>-->
					<?php if(roleHasPermission('general_permission', $_SESSION['role_permissions'])){ ?>
					<li class="nav-item">
						<a class="nav-link text-white bg-dark-blue-hover collapsed" href="#submenu1sub1" data-toggle="collapse" data-target="#submenu1sub1"><i class="fas fa-chart-line fa-fw mr-1"></i> Track</a>
						<div class="collapse" id="submenu1sub1" aria-expanded="false">
							<ul class="flex-column nav">
								<li class="nav-item">
									<a class="nav-link text-white pl-4 py-1 bg-dark-blue-hover collapsed" href="#submenu1sub2" data-toggle="collapse" data-target="#submenu1sub2">
										<i class="fas fa-chart-pie fa-fw"></i> Website Analytics
									</a>
									<div class="collapse" id="submenu1sub2" aria-expanded="false">
										<ul class="flex-column nav">
											<li class="nav-item bg-dark-blue-hover">
												<a class="nav-link text-white pl-5 py-1" href="/track/google-analytics/audience.php">
													<i class="fas fa-users fa-fw"></i> Audience Overview
												</a>
											</li>
											<li class="nav-item bg-dark-blue-hover">
												<a class="nav-link text-white pl-5 py-1" href="/track/google-analytics/landing.php">
													<i class="fas fa-file-alt fa-fw"></i> Content Overview
												</a>
											</li>
										</ul>
									</div>
								</li>
								<li class="nav-item">
									<a class="nav-link text-white pl-4 py-1 bg-dark-blue-hover collapsed" href="#submenu2sub1" data-toggle="collapse" data-target="#submenu2sub1">
										<i class="fas fa-chart-line fa-fw"></i> Campaign Analytics
									</a>
									<div class="collapse" id="submenu2sub1" aria-expanded="false">
										<ul class="flex-column nav">
											
												<li class="nav-item bg-dark-blue-hover">
													<a class="nav-link text-white pl-5 py-1 bg-dark-blue-hover" href="/track/campaign-stats/keyword_performance.php">
														<i class="fas fa-keyboard fa-fw"></i> Search Terms 
													</a>
												</li>
											<?php if(roleHasPermission('show_campaign_performance', $_SESSION['role_permissions'])){ ?>
												<li class="nav-item bg-dark-blue-hover">
													<a class="nav-link text-white pl-5 py-1 bg-dark-blue-hover" href="/track/campaign-stats/geo_performance.php">
														<i class="fas fa-location-arrow fa-fw"></i> Geo Performance
													</a>
												</li>
											<?php } ?>
											<li class="nav-item bg-dark-blue-hover">
												<a class="nav-link text-white pl-5 py-1" href="/track/campaign-stats/campaign-details.php">
													<i class="fas fa-chart-line fa-fw"></i> Campaign Data
												</a>
											</li>
											<li class="nav-item bg-dark-blue-hover">
												<a class="nav-link text-white pl-5 py-1" href="/track/campaign-stats/form_data.php">
													<i class="fas fa-table fa-fw"></i> Form Data
												</a>
											</li>
											<li class="nav-item bg-dark-blue-hover">
												<a class="nav-link text-white pl-5 py-1" href="/call-stats/">
													<i class="fas fa-phone fa-fw mr-1"></i>Calls
												</a>
											</li>											
										</ul>
									</div>
								</li>
							</ul>
						</div>
					</li>
					<?php } ?>
				<?php
				$web = $db->where("id",$_SESSION['user_id'])->getOne("storelogin");
				if(!empty($web['token'])){ ?>
				<li class="nav-item">
					<a class="nav-link text-white bg-dark-blue-hover" href="<?php echo CLIENT_URL.'admin/xt_login.php?token='.$web['token']; ?>" target="_blank">
					  <i class="fas fa-user-lock fa-fw mr-1"></i>
					  Website Admin
					</a>
				  </li>
				<?php } ?>
				   <?php if(roleHasPermission('general_permission', $_SESSION['role_permissions'])){ ?>
				   <li class="nav-item">
					<a class="nav-link text-white bg-dark-blue-hover" href="/training/">
					  <i class="fas fa-book fa-fw mr-1"></i>
					  Training 
					</a>
				   </li>
				   <?php } ?>
				<?php } else if($_SESSION['view']=="das_admin"){ ?>
					<?php if(roleHasPermission('general_permission', $_SESSION['role_permissions'])){ ?>
					<li class="nav-item">
						<a class="nav-link text-white bg-dark-blue-hover" href="/admin/location-details/">
						  <i class="fas fa-map-marker-alt fa-fw mr-1"></i>
						  Location Details
						</a>
					</li>
					<?php } ?>
					<?php if(roleHasPermission('show_ongoing_campaigns', $_SESSION['role_permissions'])){ ?>
					<li class="nav-item">
						<a class="nav-link text-white bg-dark-blue-hover" href="/admin/campaign/campaign-info.php">
							<i class="fas fa-clipboard-list fa-fw mr-1"></i> Campaigns Management
						</a>
					</li>
				<?php } ?>
				<?php if(roleHasPermission('show_post_exceptions', $_SESSION['role_permissions'])){ ?>
						  <li class="nav-item">
							<a class="nav-link text-white bg-dark-blue-hover" href="/admin/plan-and-publish/social-media/add_post_exception.php">
							  <i class="fas fa-times-circle fa-fw mr-1"></i>
							  Post Exceptions
							</a>
						  </li>
					  <?php } ?>
					<?php if(roleHasPermission('show_plan_and_publish', $_SESSION['role_permissions'])){ ?>
						<li class="nav-item">
						<a class="nav-link text-white bg-dark-blue-hover" href="/admin/plan-and-publish/social-media/">
						  <i class="fas fa-edit fa-fw mr-1"></i>
						  Social Media Calendar
						</a>
					  </li>
					<?php } ?>

					<?php if(roleHasPermission('show_graphics_gallery', $_SESSION['role_permissions'])){ ?>
					<li class="nav-item">
						<a class="nav-link text-white bg-dark-blue-hover collapsed" href="#aggSub" data-toggle="collapse" data-target="#aggSub">
						  <i class="fas fa-images fa-fw mr-1"></i> Graphics
						</a>
						<div class="collapse" id="aggSub" aria-expanded="false">
							<ul class="flex-column nav">
								<li class="nav-item">
									<a class="nav-link text-white pl-4 py-1 bg-dark-blue-hover collapsed" href="/admin/graphics-gallery/">
										<i class="fas fa-th fa-fw"></i> View Gallery
									</a>
								</li>
								<?php if(roleHasPermission('general_permission', $_SESSION['role_permissions'])){ ?>
								<li class="nav-item">
									<a class="nav-link text-white pl-4 py-1 bg-dark-blue-hover collapsed" href="/admin/graphics-gallery/manage.php">
										<i class="fas fa-paint-brush fa-fw"></i> Manage Requests
									</a>
								</li>
								<?php } ?>
							</ul>
						</div>
					</li>
					<?php } ?>
					  <?php if(roleHasPermission('general_permission', $_SESSION['role_permissions'])){ ?>
					  <li class="nav-item">
						<a class="nav-link text-white bg-dark-blue-hover" href="/admin/call-log/">
						  <i class="fas fa-phone fa-fw mr-1"></i>
						  Call Log
						</a>
					  </li>
					  <?php } ?>
					  <?php if(roleHasPermission('show_user_management', $_SESSION['role_permissions'])){ ?>
					  <li class="nav-item">
						<a class="nav-link text-white bg-dark-blue-hover" href="/admin/security-settings/user.php">
						  <i class="fas fas fa-users-cog fa-fw mr-1"></i>
						  User Management
						</a>
					  </li>
					  <?php } ?>
					  <?php if(roleHasPermission('general_permission', $_SESSION['role_permissions'])){ ?>
						<li class="nav-item">
							<a class="nav-link text-white bg-dark-blue-hover collapsed" href="#submenu1sub1" data-toggle="collapse" data-target="#submenu1sub1"><i class="fas fa-chart-line fa-fw mr-1"></i> Track</a>
							<div class="collapse" id="submenu1sub1" aria-expanded="false">
								<ul class="flex-column nav">
									<li class="nav-item">
										<a class="nav-link text-white pl-4 py-1 bg-dark-blue-hover collapsed" href="#submenu2sub1" data-toggle="collapse" data-target="#submenu2sub1">
											<i class="fas fa-chart-line fa-fw"></i> Campaign Analytics
										</a>
										<div class="collapse" id="submenu2sub1" aria-expanded="false">
											<ul class="flex-column nav">
												<li class="nav-item bg-dark-blue-hover">
													<a class="nav-link text-white pl-5 py-1 bg-dark-blue-hover" href="/admin/track/campaign-stats/organic/google.php">
														<i class="fab fa-google fa-fw"></i> GMB Stats
													</a>
												</li>
												<li class="nav-item bg-dark-blue-hover">
													<a class="nav-link text-white pl-5 py-1 bg-dark-blue-hover" href="/admin/track/campaign-stats/organic/facebook.php">
														<i class="fab fa-facebook-f fa-fw"></i> FB Organic
													</a>
												</li>
												<li class="nav-item bg-dark-blue-hover">
													<a class="nav-link text-white pl-5 py-1 bg-dark-blue-hover" href="/admin/track/campaign-stats/organic/linkedin.php">
														<i class="fab fa-linkedin fa-fw"></i> LinkedIn Organic
													</a>
												</li>
												<li class="nav-item bg-dark-blue-hover">
													<a class="nav-link text-white pl-5 py-1" href="/admin/track/campaign-stats/campaign-details.php">
														<i class="fas fa-chart-line fa-fw"></i> Campaign Data
													</a>
												</li>
												<li class="nav-item bg-dark-blue-hover">
													<a class="nav-link text-white pl-5 py-1" href="/admin/track/campaign-stats/form_data.php">
														<i class="fas fa-table fa-fw"></i> Form Data
													</a>
												</li>
												<li class="nav-item bg-dark-blue-hover">
													<a class="nav-link text-white pl-5 py-1" href="/admin/track/call-stats/">
														<i class="fas fa-phone fa-fw mr-1"></i>Calls
													</a>
												</li>
												<?php if(roleHasPermission('show_account_management_overview', $_SESSION['role_permissions'])){ ?>
												<li class="nav-item bg-dark-blue-hover">
													<a class="nav-link text-white pl-5 py-1 bg-dark-blue-hover" href="/admin/track/activity/loginsReport.php">
														<i class="fas fa-sign-in-alt fa-fw mr-1"></i>Account Management Overview
													</a>
												</li>
												<?php } ?>
											</ul>
										</div>
									</li>
									<?php if(roleHasPermission('region_report', $_SESSION['role_permissions'])){ ?>
										<li class="nav-item">
											<a class="nav-link pl-4 py-1 text-white bg-dark-blue-hover" href="/admin/track/region/">
												<i class="fas fa-globe-americas fa-fw"></i> Forms By Country
											</a>
										</li>
									<?php } ?>
								</ul>
							</div>
						</li>
					<?php } ?>
					<?php if(roleHasPermission('general_permission', $_SESSION['role_permissions'])){ ?>
						<li class="nav-item">
						<a class="nav-link text-white bg-dark-blue-hover" href="/admin/training/">
							<i class="fas fa-book fa-fw mr-1"></i>
							Training
						</a>
						</li>	
					<?php } ?>
					<?php if(!empty($_GET['storeid'])){
					$web = $db->where("storeid",$_GET['storeid'])->getOne("storelogin");
					if(!empty($web['token'])){ ?>
					<li class="nav-item">
						<a class="nav-link text-white bg-dark-blue-hover" href="<?php echo CLIENT_URL.'admin/xt_login.php?token='.$web['token']; ?>" target="_blank">
						  <i class="fas fa-user-lock fa-fw mr-1"></i>
						  Website Admin
						</a>
					  </li>
					<?php } 
					}
					?>
				<?php } ?>

			</ul>

		  </div>
		</nav>
	</div>
</div>
<nav class="cbp-spmenu cbp-spmenu-vertical cbp-spmenu-right bg-white border-left" id="cbp-spmenu-s2">
	<?php
		if($_SESSION && isset( $_SESSION['storeid'] ) && $_SESSION['storeid'] > 0 && $_SESSION['view'] == 'user'){
			$cols = array ("email_notification", "notifications");
			$db->where ("storeid", $_SESSION['storeid']);
			$notification = $db->getOne ("locationlist", null, $cols);
		}else{
			$cols = array ("email_notification", "notifications");
			$db->where ("email", $_SESSION['email']);
			$notification = $db->getOne ("storelogin", null, $cols);
		}
	?>
	<div class="tab-pane p-3 active show" id="settings" role="tabpanel">
	<h6>Notification Settings</h6>
	<form action="" method="POST">
		<div class="aside-options">
			<div class="clearfix mt-4 d-flex align-items-center">
				<small>
				<b>Receive email notifications</b>
				</small>

				<label class="switch mb-0 ml-auto">
				  <input type="checkbox" name="notifications_check" value="<?php echo ($notification && isset( $notification['notifications'] ) ) ? $notification['notifications'] : 0; ?>" <?php if ( $notification && isset( $notification['notifications'] ) && $notification['notifications'] == '1') echo "checked"; ?>>
				  <span class="slider round"></span>
				</label>

			</div>
			<div>
				<small class="text-muted">If enabled, you will receive an email in addition to the notification in the platform.</small>
			</div>
		</div>
		<div class="aside-options">
			<div class="clearfix mt-4">
				<small>
				<b>Email for Notifications</b>
				</small>
			</div>
			<div>
				<input type="text" name="email_notification" value="<?php echo (  $notification && isset( $notification['email_notification'] ) ) ? $notification['email_notification'] : ''; ?>" class="form-control" required>
			</div>
			<div>
				<small class="text-muted"><b>Note: </b>For multiple emails, separate with a comma. (ex. test@test.com,test2@test.com)</small>
			</div>
		</div>
		<div class="mt-3">
			<input type="submit" class="btn save bg-dark-blue text-white btn-sm px-4 rounded-pill" id="submitNotificationSett" value="Save">
			<input type="hidden" name="storeid_notifications_sett" value="<?php echo ( isset( $_SESSION['storeid'] ) ? $_SESSION['storeid'] : '' ); ?>">
			<input type="hidden" name="email_notifications_sett" value="<?php echo $_SESSION['email']; ?>">
			<input type="hidden" name="user_view_notifications_sett" value="<?php echo $_SESSION['view']; ?>">
		</div>
		<div class="mt-3 loading_data_info"></div>
	</form>
	
<!--
	<div class="aside-options">
		<div class="clearfix mt-3">
			<small>
			<b>Option 2</b>
			</small>

			<label class="switch float-right">
			  <input type="checkbox">
			  <span class="switch-slider round"></span>
			</label>

		</div>
		<div>
			<small class="text-muted">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</small>
		</div>
	</div>
-->
</nav>