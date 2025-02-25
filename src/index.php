<!doctype html>
<html lang="en">
  <head>
     <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/head.php"); ?>

    <title>Yesco</title>

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
    <form action="xt_login.php" method="POST" class="form-signin">
		<div class="bg-white p-3 mb-2">
			<div class="text-center mb-4">
				<img class="img-fluid" src="https://www.yesco.com/franchising/wp-content/themes/yesco-franchising/img/logo.png" alt="Yesco Logo">
			</div>
			
			<?php if(isset($_SESSION['success'])){ ?>
			<div class="alert alert-success alert-dismissible fade show my-3" role="alert">
			  <strong>Success!</strong> <?php echo $_SESSION['success'];?>
			  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			  </button>
			</div>
			<?php unset($_SESSION['success']); } ?>
			<?php if(isset($_SESSION['error'])){ ?>
			<div class="alert alert-danger alert-dismissible fade show my-3" role="alert">
			  <strong>Error!</strong> <?php echo $_SESSION['error'];?>
			  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			  </button>
			</div>
			<?php unset($_SESSION['error']); } ?>

			<div class="form-label-group">
				<input type="text" name="email" id="inputEmail" class="form-control" placeholder="Email address" required autofocus>
				<label for="inputEmail">Email address</label>
			</div>

			<div class="form-label-group">
				<input type="password" name="password" id="inputPassword" class="form-control" placeholder="Password" required>
				<label for="inputPassword">Password</label>
			</div>

			<button class="btn btn-lg bg-blue text-white btn-block" type="submit">Sign in</button>
		</div>
		<div class="text-center">
			<a href="/forgot-password/" class="text-dark-blue">Forgot your password?</a>
		</div>
	</form>
</body>
</html>
