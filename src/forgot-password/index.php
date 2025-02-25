<!doctype html>
<html lang="en">
  <head>
  <?php session_start(); ?>
  <meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<link rel="stylesheet" href="/css/chosen.min.css">
	<link rel="stylesheet" href="/css/styles.css">
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
	<script src='https://www.google.com/recaptcha/api.js'></script>
  </head>
  <body>
    <form action="xt_forgot_password.php" method="POST" class="form-signin">
		<div class="bg-white p-3 mb-2">
			<div class="text-center mb-4">
				<img class="img-fluid" src="https://www.yesco.com/franchising/wp-content/themes/yesco-franchising/img/logo.png" alt="Yesco Logo">
			</div>
			
			<?php include ($_SERVER['DOCUMENT_ROOT']."/includes/alerts.php"); ?>

			<div class="form-label-group">
				<input type="text" name="email" id="inputEmail" class="form-control" placeholder="Email address" required autofocus>
				<label for="inputEmail">Email address</label>
			</div>
			
			<div class="form-label-group d-flex justify-content-center">
				<div class="g-recaptcha" data-sitekey="6LdMzdoUAAAAAOrMpDGzgB_PCEr5IPJ7rkGjlbDE"></div>
			</div>
			
			<button class="btn btn-lg bg-blue text-white btn-block" type="submit">Send</button>
		</div>
	</form>
</body>
</html>