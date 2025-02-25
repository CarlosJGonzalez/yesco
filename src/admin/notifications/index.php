<!doctype html>
<html lang="en">
  <head>
	  <link href="//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/head.php"); ?>

    <title>Notifications | <?php echo CLIENT_NAME; ?></title>
	  
  </head>
  <body class="bg-light cbp-spmenu-push">
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/top.php"); ?>

    <div class="container-fluid">
      <div class="row">
        <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/nav.php"); ?>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 p-0">
			
			<div class="p-0 border-bottom mb-4">
				<div class="border-bottom-dotted d-flex d-block align-items-center clearfix py-2 px-4">
					<h1 class="h2 font-light mb-0 text-center text-sm-left"><i class="fas far fa-bell mr-2"></i> Notifications</h1>
					<!--<div class="ml-auto">
						<a href="new.php"><i class="fas fa-plus-circle fa-2x text-muted"></i></a>
					</div>-->
				</div>
			</div>
			
			<div class="py-3 px-4">
				<?php include $_SERVER['DOCUMENT_ROOT']."/includes/alerts.php"; ?>
				  <div class="text-right">
					<a href class="markRead font-12" data-storeid="<?php echo $_SESSION['storeid']; ?>" data-usertype="<?php echo $_SESSION['view']; ?>">Mark All as Read</a>
				  </div>
				  <div class="notes">
					  <?php
					  $db->orderBy("date","desc");
					  if($_SESSION['view']=="user"){
					  	$db->where("storeid",$_SESSION['storeid']);
					  }else{
						$db->where("user_type",$_SESSION['view']);
					  }
					  $notifications = $db->get("notifications");
					  foreach($notifications as $notification){?>
					  <a href="<?php echo $notification['link']; ?>" class="no-dec noteItem" data-id="<?php echo $notification['id']; ?>">
						  <div class="d-flex p-2 align-items-center hover-bg-light notification-status <?php if($notification['unread'] == 1) echo 'unread'; ?>">
							  <i class="<?php echo empty($notification['icon']) ? "fas fa-comment" : $notification['icon']; ?> text-dark-blue mr-2"></i>
							  <div>
								<p class="white-space-normal line-height-1 text-dark mb-2"><?php echo $notification['message']; ?></p>
								<small class="d-block text-dark text-muted"><i class="far fa-clock"></i> <?php echo date("m/d/Y h:i A",strtotime($notification['date'])); ?></span></small>
							  </div>
						  </div>
					  </a>
					  <? } ?>

			   </div>
			</div>
        	
        </main>
      </div>
    </div>


    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>
	<script>

	</script>
  </body>
</html>