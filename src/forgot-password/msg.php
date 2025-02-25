<!doctype html>
<html lang="en">
  <head>
	<?php 
	session_start();
	
	if(!isset($_SESSION['show_msg_forgot_pass'])){
		header("location:/forgot-password/");
		exit;
	}
	?>
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
  </head>
  <body>
	<div style="margin:auto;max-width:100%">
		<div class="bg-white p-3 mb-2">
			<div class="text-center mb-4">
				<img class="img-fluid" src="https://www.yesco.com/franchising/wp-content/themes/yesco-franchising/img/logo.png" alt="Yesco Logo">
			</div>
			
			<?php if(isset($_SESSION['success'])){ ?>
			<div class="alert alert-success alert-dismissible fade show" role="alert">
			  <strong>Success!</strong> <?php echo $_SESSION['success'];?>
			</div>
			<?php unset($_SESSION['success']); } ?>
			<?php if(isset($_SESSION['error'])){ ?>
			<div class="alert alert-danger alert-dismissible fade show" role="alert">
			  <strong>Error!</strong> <?php echo $_SESSION['error'];?>
			</div>
			<?php unset($_SESSION['error']); } ?>
			
			<?php unset($_SESSION['show_msg_forgot_pass']); ?>

		</div>
	</div>
</body>
</html>