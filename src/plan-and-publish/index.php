<!doctype html>
<html lang="en">
  <head>
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/head.php");
	  if(!(roleHasPermission('show_plan_and_publish', $_SESSION['role_permissions']))){
		header('location: /');
		  exit;
		}?>

    <title>Plan and Publish | Yes We're Open</title>
	  
  </head>
  <body class="bg-light cbp-spmenu-push">
    <? include ($_SERVER['DOCUMENT_ROOT']."/includes/top.php"); ?>

    <div class="container-fluid">
      <div class="row">
        <? include ($_SERVER['DOCUMENT_ROOT']."/includes/nav.php"); ?>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 pt-3 px-4 mb-4">
        	<div class="row">
				<div class="col-sm-6 col-md-4">
					<h2 class="text-uppercase h4 text-dark d-flex flex-wrap">Social Media&nbsp;<span class="text-primary-red">Calendar</span></h2>
					<div class="card">
						<div class="card-header bg-white text-center">
							<span class="font-bold text-uppercase"><i class="far fa-clock"></i>  Next post scheduled for: </span>
							<span class="text-muted text-uppercase font-12">Feb 22, 2018 12:00 PM </span>
						</div>
						<img src="http://placebear.com/1200/628" class="img-fluid card-img-top" alt="Placeholder Image" />
						<div class="card-body">
						  <h5 class="card-title">Facebook</h5>
						  <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Cum consectetur quia porro, reprehenderit nesciunt.</p>
						</div>
						<div class="card-footer text-center">
							<a href="social-media/" class="small text-muted">View All Posts</a>
						</div>
					</div>
				</div>
				<div class="col-sm-6 col-md-4">
					<h2 class="text-uppercase h4 text-dark d-flex flex-wrap">Blog&nbsp;<span class="text-primary-red">Posts</span></h2>
					<div class="card">
						<div class="card-header bg-white text-center">
							<span class="font-bold text-uppercase"><i class="far fa-clock"></i>  Next post scheduled for: </span>
							<span class="text-muted text-uppercase font-12">Feb 24, 2018 11:00 PM </span>
						</div>
						<img src="http://placekitten.com/1200/628" class="img-fluid card-img-top" alt="Placeholder Image" />
						<div class="card-body">
						  <h5 class="card-title">Lorem ipsum dolor sit.</h5>
						  <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Cum consectetur quia porro, reprehenderit nesciunt.</p>
						</div>
						<div class="card-footer text-center">
							<a href="social-media/" class="small text-muted">View All Posts</a>
						</div>
					</div>
				</div>
			</div> 
        </main>
      </div>
    </div>
    <? include ($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>
  </body>
</html>