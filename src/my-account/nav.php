<?php
	$db->where("email",$_SESSION['email']);
	//$db->where("email",'sicwing@das-group.com');
	$user_detail = $db->getOne("storelogin");
	$full_name = explode(" ", $user_detail['name']); 
?>
<div class="box p-3 mb-4">
	<div class="d-flex align-items-center">
		<div class="bg-dark rounded-circle text-white square-80 justify-content-center d-flex">
			<span class="font-weight-bold p-3 h1">
			<?php 
			$name = $full_name[0];
			$last_name = $full_name[1];
			
			// Accessing single characters in a string
			// can also be achieved using "square brackets"
			echo strtoupper($name[0].$last_name[0]); 
			?></span>
		</div>
		<div class="ml-4">
			<p class="mb-0 h5">Hi,<br><span class="font-weight-bold"><?php echo $full_name[0]." ".$full_name[1]; ?></span></p>
		</div>
	</div>
</div>
<div class="account-nav">
	<div class="box mb-2">
		<a href="/my-account/" class="d-block w-100 p-2 p-xl-3 text-dark h5 font-weight-light"><i class="far fa-user fa-fw mr-1"></i> My Details</a>
	</div>
	<div class="box mb-2">
		<a href="/my-account/change-password.php" class="d-block w-100 p-3 text-dark h5 font-weight-light"><i class="fas fa-lock fa-fw mr-1"></i> Change Password</a>
	</div>
	<?php if(roleHasPermission('general_permission', $_SESSION['role_permissions'])){ ?>
	<div class="box mb-2">
		<a href="/my-account/payment-methods/" class="d-block w-100 p-3 text-dark h5 font-weight-light"><i class="fas fa-credit-card fa-fw mr-1"></i> Payment Methods</a>
	</div>
	<?php } ?>
	<?php if(roleHasPermission('general_permission', $_SESSION['role_permissions'])){ ?>
	<div class="box mb-2">
		<a href="/my-account/history.php" class="d-block w-100 p-3 text-dark h5 font-weight-light"><i class="fas fa-history fa-fw mr-1"></i> Payment History</a>
	</div>
	<?php } ?>
</div>
