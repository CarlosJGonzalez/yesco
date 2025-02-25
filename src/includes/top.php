
<div class="d-md-none bg-blue">
	<div class="p-2 text-center">
		<a href=""><img src="/img/FP-logo-white.png" alt="Yes We're Open" class="img-fluid"></a>
	</div>
</div>
<div>
<nav class="navbar navbar-expand bg-light-custom flex-md-nowrap p-0">
	<button class="navbar-toggler d-block d-md-none" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
		<i class="fas fa-bars"></i>
	</button>
	<div class="offset-md-3 offset-lg-2">
		<div class="ml-0 ml-md-4">
			
			<?php if(roleHasPermission('general_permission', $_SESSION['role_permissions'])){ ?>
			<div class="dropdown d-inline-block justify-content-center ml-2 change-location">
				<div class="cursor-pointer d-flex align-items-center" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-offset="10,20">
					<span class="small bg-white text-dark border py-1 px-2 rounded-pill"><?php echo isset($active_location) ? $active_location['companyname']." (".$active_location['storeid'].")" : "No Location Selected"; ?></span>
					<i class="fas fa-map-marker-alt fa-lg ml-3 text-dark-blue" id="changeLocationButton"></i>
				</div>
			  <div class="dropdown-menu p-2" aria-labelledby="changeLocationButton">
				  <span class="dropdown-menu-arrow center"></span>
				  <label class="font-12 text-uppercase py-1 px-2 mb-0 letter-1 text-dark">Change location</label>
				  <div class="d-block">
					  <form method="GET" action="">
							<select name="storeid" class="border rounded custom-select-arrow chosen-select w-100" onchange="this.form.submit()">
								<option value="">--</option>
								<?php
								$locations = get_access_locations($_SESSION['user_id']);								
								foreach($locations as $loc){
								?>
								<option value="<?php echo $loc['storeid'];?>"<?php if($loc['storeid'] == $active_location['storeid']) echo "selected"; ?>><?php echo $loc['companyname']." (".$loc['storeid'].")";?></option>
								<?php } ?>
							</select>
						  <input type="hidden" name="view" value="user">
					  </form>
				  </div>
			  </div>
			</div>
			<?php } ?>

		</div>
	</div>
  <ul class="ml-auto list-inline mb-0 d-flex align-items-stretch">
		
		<?php if(roleHasPermission('general_permission', $_SESSION['role_permissions'])){ ?>
		<li class="list-inline-item text-nowrap d-flex align-items-stretch mr-0">
		  <div class="dropdown show fa-lg d-flex align-items-stretch">
			  <a class="text-dark nav-link d-flex align-items-center px-1 px-sm-3" href="#" role="button" id="dropdownMenuNote" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-storeid="<?php echo $_SESSION['storeid']; ?>" data-usertype="<?php echo $_SESSION['view']; ?>">
				  <span class="fa-layers fa-fw">
				  	<?php 
					if($_SESSION['view']=="user"){
						$db->where ("storeid", $_SESSION['storeid']);
						$db->where ("new", '1');
					}else{
						$db->where ("user_type", $_SESSION['view']);
						$db->where ("new", '1');
					}
					$cnt = $db->getOne ("notifications", "count(*) as count");
					?>
						<i class="far fa-bell"></i>
					<?php if($cnt['count'] > 0){ ?>
						<span class="fa-layers-counter fa-2x bg-danger notifications_number"><?php echo $cnt['count'];?></span>
					<?php } ?>
				  </span>
			  </a>

			  <div class="dropdown-menu notifications dropdown-menu-right rounded-0 p-0" aria-labelledby="dropdownMenuNote">
				  <span class="dropdown-menu-arrow"></span>
				  <?php
					  $db->orderBy("date","desc");
					  if($_SESSION['view']=="user"){
					  	$db->where("storeid",$_SESSION['storeid']);
					  }else{
						$db->where("user_type",$_SESSION['view']);
					  }
					  $notifications = $db->get("notifications",5);
				  	$count = $db->count;
				  ?>
				  <div class="p-2 bg-light d-flex">
					  <small><strong>Notifications</strong></small>
					  <?php if($count>0){?><small class="ml-auto"><a href class="markRead text-blue font-12" data-storeid="<?php echo $_SESSION['storeid']; ?>" data-usertype="<?php echo $_SESSION['view']; ?>">Mark All as Read</a></small><?php } ?>
				  </div>
				  <div class="notes">
					  <?php
					  if($count>0){
					  foreach($notifications as $notification){?>
					  <a href="<?php echo $notification['link']; ?>" class="no-dec noteItem" data-id="<?php echo $notification['id']; ?>">
						  <div class="d-flex p-2 align-items-center hover-bg-light notification-status <?php if($notification['unread'] == 1) echo 'unread'; ?>">
							  <i class="<?php echo empty($notification['icon']) ? "fas fa-comment" : $notification['icon']; ?> text-dark-blue mr-2"></i>
							  <div>
								<p class="white-space-normal line-height-1 text-dark small mb-2"><?php echo substr(rtrim($notification['message'], '.'), 0, 60); ?> ...</p>
								<small class="d-block text-dark text-muted"><i class="far fa-clock"></i> <?php echo date("m/d/Y h:i A",strtotime($notification['date'])); ?></span></small>
							  </div>
						  </div>
					  </a>
					  <?php }
					  }else echo "<p class='font-italic small p-2 mb-0 text-muted'>You have no notifications.</p>"?>

			   		</div>
			  	<?php if($count>0){?>
				  <div class="p-2 bg-light text-center">
				    <?php
					  if($_SESSION['view']=="user"){
					?>
					  <a class="font-12 text-dark" href="<?php echo getFullURL()."/notifications/"; ?>">View All Notifications</a>
					<? }else{ ?>
						<a class="font-12 text-dark" href="<?php echo getFullURL()."/admin/notifications/"; ?>">View All Notifications</a>
					<? } ?>
				  </div>
			  <?php } ?>

			  </div>
			</div>
		</li>
		<?php } ?>
		<li class="list-inline-item text-nowrap mr-0">
		  <div class="dropdown show">
			  <a class="text-dark nav-link px-1 px-sm-3" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo $_SESSION['name']; ?></a>

			  <div class="dropdown-menu dropdown-menu-right rounded-0 p-0" aria-labelledby="dropdownMenuLink">
				  <span class="dropdown-menu-arrow"></span>
				  <div class="text-center px-2 py-3">
					<span class="d-block font-weight-bold"><?php echo $_SESSION['name']; ?></span>
					  <small class="d-block mb-2"><?php echo isset($active_location) ? "Fully Promoted<br>".$active_location['companyname'] : "Admin View"; ?></small>
					  <?php if ($active_location['rep'] != ''){
						$db->where ("id", $active_location['rep']);
						$rep = $db->getOne ("reps");
						$repName = $rep['name'];
						$repEmail = $rep['email'];
						$repPhoneExt = $rep['phone'];
						if ($repName != '' && $repEmail != '' && $repPhoneExt != ''){
					  ?>
					        <small class="d-block"><b>Rep Name:</b> <?php echo $repName; ?></small>
							<small class="d-block"><a href="tel:954-893-8112;<?php echo $repPhoneExt; ?>">954-893-8112 x <?php echo $repPhoneExt; ?></a></small>
							<small class="d-block">For dashboard issues, contact Support at <br> <b><?php echo $repEmail; ?></b></small>
					  <?php 
						} 
					   }
					  ?>
				  </div>
				  <div class="p-2 bg-light ">
					  <div class="d-flex justify-content-between">
					 	<a class="btn bg-blue btn-sm text-white mr-2 font-12" href="/my-account/">My Account</a>
					  	<a class="btn bg-blue btn-sm text-white ml-2 font-12" href="/xt_logout.php">Sign Out</a>
					  </div>
					  <?php if(isset($_SESSION['admin']) && $_SESSION['view']=="user"){ ?>
						  <div class="text-center pt-2">
							  <form action="" method="GET" id="admin_switch">
								  <button class="btn bg-blue btn-sm text-white ml-2 font-12" type="submit">Switch to Admin View</button>
								  <input type="hidden" name="view" value="das_admin">
							  </form>
						  </div>
					  <?php } ?>
				  </div>

			  </div>
			</div>
		</li>
		<li class="list-inline-item text-nowrap d-flex align-items-stretch mr-0">
		  <a class="nav-link text-dark d-flex align-items-center px-1 px-sm-3" href="#" id="showRightPush"><i class="fas fa-cogs"></i></a>
		</li>
  </ul>
</nav>

</div>