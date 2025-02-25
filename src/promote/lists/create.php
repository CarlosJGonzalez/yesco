<!doctype html>
<html lang="en">
  <head>
	 <link href="/css/smart_wizard.min.css" rel="stylesheet" type="text/css" />
	<link href="/css/smart_wizard_theme_arrows.min.css" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" href="/css/checkbox.css">
    <?php 
	include ($_SERVER['DOCUMENT_ROOT']."/includes/head.php");
	if(!(roleHasPermission('show_promote_link', $_SESSION['role_permissions']))){
		$_SESSION['error'] = "Sorry! You must be authorized to see this page.";
		header('location: /');
		exit;
	}
	?>

    <title>Create List | <?php echo CLIENT_NAME; ?></title>
	  
  </head>
  <body class="bg-light cbp-spmenu-push">
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/top.php"); ?>

    <div class="container-fluid">
      <div class="row">
        <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/nav.php"); ?>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 p-0 mb-4">
			<div class="p-0 border-bottom mb-4">
				<div class="breadcrumbs bg-white px-3 py-1 border-bottom small">
					<a href="/promote/" class="text-muted">Promote</a>
					<span class="mx-1">&rsaquo;</span>
					<a href="/promote/lists/" class="text-muted">Lists</a>
					<span class="mx-1">&rsaquo;</span>
					<span class="font-weight-bold text-muted">Create List</span>
				</div>
				<div class="d-flex d-block align-items-center clearfix py-2 px-4">
					<h1 class="h2 font-light mb-0 text-center text-sm-left">
						<span class="fa-layers mr-2">
							<i class="fas fa-list-ul"></i>
							<span class="fa-layers-counter bg-dark fa-lg"><i class="fas fa-plus"></i></span>
							
						</span>
						 Create List</h1>
				</div>
			</div>
        	<div class="px-4 py-3">
			
				<?php include ($_SERVER['DOCUMENT_ROOT']."/includes/alerts.php"); ?>
			
				<div class="row">
					<div class="col-xl-6">
						<div class="box p-3">
							<form action="xt_createList.php" method="POST">
								<div class="form-group">
									<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">List name</label>
									<input type="text" class="form-control rounded-bottom rounded-right" name="list_name" required>
								</div>
								<div class="form-group">
									<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Default From name</label>
									<input type="text" class="form-control rounded-bottom rounded-right" name="from_name" required>
								</div>
								<div class="form-group text-center">
									<input type="submit" class="btn bg-blue text-white" value="Create">
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
	
  </body>
</html>