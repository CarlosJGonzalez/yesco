<!doctype html>
<html lang="en">
  <head>
	<?php
	session_start(); 
	
	if(!isset($_SESSION['email_forgot_pass'])){
		header("location:/forgot-password/");
		exit;
	}
	?>
  	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<link rel="stylesheet" href="/css/chosen.min.css">
	<link rel="stylesheet" href="/css/styles.css">
    <title>Fully Promoted</title>
    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
    </style>
	<link href="/css/styles.css" rel="stylesheet">
    <link href="/css/floating-labels.css" rel="stylesheet">
  </head>
  <body>
    <form action="xt_reset_password.php" method="POST" class="form-signin">
		<div class="bg-white p-3 mb-2">
			<div class="text-center mb-4">
				<img class="img-fluid" src="/img/fp-color.png" alt="Fully Promoted Logo">
			</div>
			
			<?php include ($_SERVER['DOCUMENT_ROOT']."/includes/alerts.php"); ?>

			<div class="form-label-group">
				<input type="password" name="new_pass" id="new_pass" class="form-control" placeholder="New Password" required autofocus>
				<label for="new_pass">New Password<span class="text-danger">*</span></label>
			</div>
			
			<div class="form-label-group">
				<input type="password" name="confirm_pass" id="confirm_pass" class="form-control" placeholder="Confirm Password" required autofocus>
				<label for="confirm_pass">Confirm Password<span class="text-danger">*</span></label>
			</div>
			<?php if(isset($_SESSION['email_forgot_pass']) && isset($_SESSION['token_forgot_pass'])){ ?>
			<div class="form-label-group">
				<input name="user_email" type="hidden" value="<?php echo $_SESSION['email_forgot_pass']; ?>">
				<input name="user_token" type="hidden" value="<?php echo $_SESSION['token_forgot_pass']; ?>">
			</div>
			<?php 
			unset($_SESSION['email_forgot_pass']);
			unset($_SESSION['token_forgot_pass']);
			} 
			?>
			
			<button name="submitBtnUpdateMyPassword" class="btn btn-lg bg-blue text-white btn-block" type="submit" value="Save Password">Save Password</button>
		</div>
	</form>
</body>
</html>