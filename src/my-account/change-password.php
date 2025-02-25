<!doctype html>
<html lang="en">
  <head>
	  
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/head.php");?>

    <title>Change Password | <?php echo CLIENT_NAME; ?></title>
	  
  </head>
  <body class="bg-light cbp-spmenu-push">
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/top.php"); ?>

    <div class="container-fluid">
      <div class="row">
        <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/nav.php"); ?>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 p-0 mb-4">
		<?php
		$db->where("email",$_SESSION['email']);
		//$db->where("email",'sicwing@das-group.com');
		$user_detail = $db->getOne("storelogin");
		?>
			<div class="p-0 border-bottom mb-4">
				<div class="d-flex d-block align-items-center clearfix py-2 px-4">
					<h1 class="h2 font-light mb-0 text-center text-sm-left"><i class="fas fa-lock mr-2"></i> Change Password</h1>
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
				<div class="row">
					<div class="col-sm-3">
						<?php include "nav.php"; ?>
					</div>
					<div class="col-sm-9">
						<div class="box p-3">
							<form action="xt_user.php" method="POST">
								<div class="form-group">
									<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Current Password</label>
									<input type="text" class="form-control" name="current_pass">
								</div>
								<div class="form-group">
									<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">New Password</label>
									<input type="text" class="form-control" name="new_pass">
								</div>
								<div class="form-group text-center">
									<input type="submit" class="btn bg-blue text-white" name="submitBtnUpdateMyPassword" value="Save Password">
									<input type="hidden" name="user_id" value="<?php echo $user_detail['id'] ?>">
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
			
        </main>
      </div>
    </div>


    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>
	<script src="/my-account/scripts.js"></script>
  </body>
</html>