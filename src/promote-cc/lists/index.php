<!doctype html>
<html lang="en">
  <head>
	 <link href="/css/smart_wizard.min.css" rel="stylesheet" type="text/css" />
	<link href="/css/smart_wizard_theme_arrows.min.css" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" href="/css/checkbox.css">
    <?php 
	include ($_SERVER['DOCUMENT_ROOT']."/includes/head.php");
	include ($_SERVER['DOCUMENT_ROOT']."/includes/ClassDasConstantContact.php");
	if(!(roleHasPermission('show_promote_link', $_SESSION['role_permissions']))){
		$_SESSION['error'] = "Sorry! You must be authorized to see this page.";
		header('location: /');
		exit;
	}
	
	//Only for test purpose
	/*$active_location['constant_contact_api_key'] = 'j3bn9adcxrgg2jvxd6nmg75b';
	$active_location['constant_contact_access_token'] = '138e5b8a-ad09-419b-92f7-399d64875e4f';*/

	if(empty($active_location['constant_contact_api_key']) || empty($active_location['constant_contact_access_token'])){
		$_SESSION['error'] = "Please enter a valid api key and token.";
		header('location: /settings/promote/');
		exit;
	}else{
		$cc_api_key = $active_location['constant_contact_api_key'];
		$cc_access_token = $active_location['constant_contact_access_token'];
	}

	//ClassDasConstantContact 
	$cc = new Das_ConstantContact($cc_api_key, $cc_access_token);
	?>
    <title>Manage Lists | <?php echo CLIENT_NAME; ?></title>
  </head>
  <body class="bg-light cbp-spmenu-push">
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/top.php"); ?>

    <div class="container-fluid">
      <div class="row">
        <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/nav.php"); ?>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 p-0 mb-4">
			<div class="p-0 border-bottom mb-4">
				<div class="breadcrumbs bg-white px-3 py-1 border-bottom small">
					<a href="/promote-cc/" class="text-muted">Promote</a>
					<span class="mx-1">&rsaquo;</span>
					<span class="font-weight-bold text-muted">Lists</span>
				</div>
				<div class="d-flex d-block align-items-center clearfix py-2 px-4">
					<h1 class="h2 font-light mb-0 text-center text-sm-left"><i class="fas fa-list-ul mr-2"></i> Manage Lists</h1>
					<div class="ml-auto">
						<a href="create.php" class="btn bg-blue ml-auto text-white btn-sm">Create List</a>
					</div>
				</div>
			</div>
        	<div class="px-4 py-3">
			
				<?php include ($_SERVER['DOCUMENT_ROOT']."/includes/alerts.php"); ?>
			
				<?php 
				$db->where('storeid',$_SESSION['storeid']);
				$db->orderBy("id","desc");
				$lists = $db->get('promote_lists');
				
				if ($db->count > 0){
				foreach($lists as $list){
					$list_details = $cc->getLists($list['list_id']);
				?>
					<div class="border rounded p-2 mb-3">
						<div class="row align-items-center">

							<div class="col-sm-4">
								<span class="h3 mb-1"><?php echo ucfirst($list_details['name']); ?></span>
								<div class="d-block">
									<span class="text-blue text-uppercase font-weight-bold">Created</span>
									<span class="text-muted"><?php echo date("M d, Y",strtotime($list_details['created_date'])) ?></span>
								</div>
							</div>
							<div class="col-sm-8">
								<div class="d-flex justify-content-between align-items-center">
									<div class="mr-2">
										 <span class="h4 text-blue d-block mb-1 font-weight-bold"><?php echo $list_details['contact_count']; ?></span>
										 <span class="text-muted text-uppercase d-block">Subscribers</span>
									</div>
									<!--<div class="mr-2">
										 <span class="h4 text-blue d-block mb-1 font-weight-bold"><?php //echo $list_details['stats']['open_rate']; ?> %</span>
										 <span class="text-muted text-uppercase d-block">Opens</span>
									</div>
									<div class="mr-2">
										 <span class="h4 text-blue d-block mb-1 font-weight-bold"><?php //echo $list_details['stats']['click_rate']; ?> %</span>
										 <span class="text-muted text-uppercase d-block">Clicks</span>
									</div>-->

									<div class="text-right">
										 <div class="dropdown">
										  <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											More
										  </button>
										  <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
											<a class="dropdown-item" href="members.php?id=<?php echo $list_details['id']; ?>">Manage Contacts</a>
											<a class="dropdown-item delete-list" href="#" id="list-<?php echo $list_details['id']?>">Delete</a>
										  </div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php }
				}else{ ?>
						<p class="text-muted font-italic">You have no lists yet.</p>
				<?php } ?>
			</div>
        
        </main>
      </div>
    </div>

    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>
	<script>
	/****** Delete List option *****/
	$(".delete-list").click(function(event) {
	
		var list_id, list_id_attr, res, field;

		list_id_attr = $(this).attr("id");
		
		res = list_id_attr.split("list-");
		list_id = res[1].replace(",", "");
	
		if(confirm("Are you sure you want to proceed?")){
			window.location.href = "list_actions.php?list_id_to_delete="+list_id;
		}
	});
	/****** End Delete list option *****/
	</script>
  </body>
</html>