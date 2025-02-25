<!doctype html>
<html lang="en">
  <head>
	  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/head.php");?>

    <title>Support | <?php echo CLIENT_NAME; ?></title>
    <style type="text/css"></style>
  </head>
  <body class="bg-light cbp-spmenu-push">
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/top.php");  ?>

    <div class="container-fluid">
      <div class="row">
        <?php 
        include ($_SERVER['DOCUMENT_ROOT']."/includes/nav.php"); 
        include_once ($_SERVER['DOCUMENT_ROOT']."/includes/ClassDasPost.php");
        ?>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 p-0 mb-4 dashboard">
        	<?php include $_SERVER['DOCUMENT_ROOT']."/includes/alerts.php"; ?>
			
			<div class="p-0 border-bottom mb-4">
				<div class="d-flex d-block align-items-center clearfix py-2 px-4">
					<h1 class="h2 font-light mb-0 text-center text-sm-left"><i class="fas fa-envelope mr-2"></i> Contact Support</h1>
				</div>
			</div>
			
			<div class="py-2 px-4 d-block">
				<div class="row">
					<div class="col-md-8 offset-md-2">
						<form action="xt_support.php" method="POST" >
							<div class="row">
								<div class="form-group col-6">
									<label for="nameSupport">Name: </label>
									<input type="text" class="form-control" name="name" id="nameSupport" required aria-describedby="nameSupport" placeholder="Enter name">

								</div>

								<div class="form-group col-6">
									<label for="emailSupport">Email address:</label>
									<input type="email" class="form-control" id="emailSupport" name="email" required aria-describedby="emailSupport" placeholder="Enter email">
								</div>
								<div class="form-group col-12">
									<label for="subjectSupport">Subject: </label>
									<input type="text" class="form-control" id="subjectSupport" name="subject" required aria-describedby="subjectSupport" placeholder="Enter Subject">
								</div>

								<div class="form-group col-12">
									<label for="msgSupport">Message: </label>
									<textarea required maxlength="250" class="form-control" name="msg" id="msgSupport" rows="3"></textarea>
									<small id="remain" class="form-text text-muted">0/200</small>
								</div>
							</div>	
							<div class="text-center">
								<button type="submit" class="btn save bg-dark-blue text-white px-4">Send</button>			
							</div>
						</form>
					</div>
				</div>
			</div>
        
        </main>
      </div>
    </div>  


    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>

<script type="text/javascript">

	var textlimit = 200;
    $('#msgSupport').on('keyup',function() {

        var tlength = $(this).val().length;   
        remain = textlimit - parseInt(tlength);
        if(remain > 0){
        	$('#remain').text(tlength+"/200");
        }else{
        	$(this).val($(this).val().substring(0, textlimit));
        	$('#remain').text(textlimit+"/200");
        }

        
     });  
  </script>
  </body>
  
</html>