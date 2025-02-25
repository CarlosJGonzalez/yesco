<!doctype html>
<html lang="en">
  <head>
	<link href="//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
	<link href="//cdn.datatables.net/select/1.2.7/css/select.bootstrap4.min.css" rel="stylesheet" type="text/css" />
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
    <title>List | <?php echo CLIENT_NAME; ?></title>
  </head>
  <body class="bg-light cbp-spmenu-push">
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/top.php"); ?>

    <div class="container-fluid">

      <div class="row">
        <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/nav.php"); ?>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 p-0 mb-4">
			<?php
				$list_id = $db->escape($_GET['id']);
				$list = $cc->getList($list_id);
			?>
			<div class="p-0 border-bottom mb-4">
				<div class="breadcrumbs bg-white px-3 py-1 border-bottom small">
					<a href="/promote-cc/" class="text-muted">Promote</a>
					<span class="mx-1">&rsaquo;</span>
					<a href="/promote-cc/lists/" class="text-muted">Lists</a>
					<span class="mx-1">&rsaquo;</span>
					<span class="font-weight-bold text-muted"><?php echo $list["name"]; ?></span>
				</div>
				<div class="d-flex d-block align-items-center clearfix py-2 px-4">
					<h1 class="h2 font-light mb-0 text-center text-sm-left"><i class="fas fa-users mr-2"></i> <?php echo $list["name"]; ?></h1>
					<div class="ml-auto">
						<button class="btn btn-secondary btn-sm bg-dark-blue text-white mb-2 border-0 deleteBtn mr-1 disabled" type="button"><span>Delete</span></button>
						<div class="dropdown d-inline-block">
						  <button class="btn btn-secondary btn-sm bg-dark-blue text-white mb-2 border-0 dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							Add Contacts
						  </button>
						  <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
							<a class="dropdown-item small" href="" data-toggle="modal" data-target="#add">Add a Subscriber</a>
							<a class="dropdown-item small" href="" data-toggle="modal" data-target="#import">Import Contacts</a>
						  </div>
						</div>
					</div>
				</div>
			</div>
        	<div class="px-4 py-3">
				
			<?php include ($_SERVER['DOCUMENT_ROOT']."/includes/alerts.php"); ?>

				<table class="table">
					<thead class="thead-dark">
						<tr>
							<th></th>
							<th>Email Address</th>
							<th>Name</th>
							<th>Status</th>
							<th>Date Added</th>
						</tr>
					</thead>
					<tbody>
						<?php
							$members = $cc->getContactsFromList($list_id);

							foreach($members as $member){

								if(empty($member['info']['email_address'])) continue;
						?>
								<tr data-id="<?php echo $member['info']['id']; ?>">
									<td></td>
									<td><?php echo $member['info']['email_address']; ?></td>
									<td><?php echo $member['info']['fname']." ".$member['info']['lname']; ?></td>
									<td><span class="rounded-pill py-1 px-3 alert <?php echo $member['info']['status']=="ACTIVE" ? "alert-success" : "alert-danger"; ?>"><?php echo ucfirst($member['info']['status']); ?></span></td>
									<td><?php echo date("m/d/Y", strtotime($member['info']['opt_in_date'])); ?></td>
								</tr> 
						<?php } ?>

					</tbody>
				</table>
				<span class="d-none" id="listid"><?php echo $list_id; ?></span>
				<!-- Add a Subscriber Modal -->
				<form action="xt_addSubscriber.php" method="POST">
					<div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="addLabel" aria-hidden="true">
						<div class="modal-dialog" role="document">
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title" id="addLabel">Add Subscriber</h5>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									  <span aria-hidden="true">&times;</span>
									</button>
								</div>
								<div class="modal-body">
									<div class="alert alert-primary text-dark">
										Want to subscribe more than one person at a time? <a href="" class="text-blue" data-toggle="modal" data-target="#import" data-dismiss="modal">Import a list</a>
									</div>
									<div class="form-group">
										<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Email Address</label>
										<input type="email" class="form-control rounded-bottom rounded-right" name="email" required>
									</div>
									<div class="form-group">
										<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">First Name</label>
										<input type="text" class="form-control rounded-bottom rounded-right" name="fname">
									</div>
									<div class="form-group">
										<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Last Name</label>
										<input type="text" class="form-control rounded-bottom rounded-right" name="lname">
									</div>
									<div class="form-group">
										<div class="mt-2">
										  <label class="label cusor-pointer d-flex text-center" for="optin">
											<input  class="label__checkbox" type="checkbox" name="optin" value="1" type="checkbox" id="optin" required />
											<span class="label__text d-flex align-items-center">
											  <span class="label__check d-flex rounded-circle mr-2">
												<i class="fa fa-check icon small"></i>
											  </span>
												<span class="text-uppercase small letter-spacing-1 d-inline-block">This person gave me permission to email them </span>
											</span>
										  </label>
										</div>
									</div>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
									<input type="hidden" name="listid" value="<?php echo $list_id; ?>">
									<input type="submit" class="btn bg-blue text-white" value="Add Subscriber">
								</div>
							</div>
						</div>
					</div>
				</form>

				<!-- Import Subscribers Modal -->
				<form action="xt_importSubscribers.php" method="POST" enctype="multipart/form-data">
					<div class="modal fade" id="import" tabindex="-1" role="dialog" aria-labelledby="importLabel" aria-hidden="true">
						<div class="modal-dialog" role="document">
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title" id="importLabel">Import from CSV file</h5>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									  <span aria-hidden="true">&times;</span>
									</button>
								</div>
								<div class="modal-body">

									<div class="form-group">
										<div class="d-flex align-items-center">
											<div class="input-group">
											  <div class="custom-file">
												<input type="file" name="file" class="form-control emailText rounded-bottom rounded-right custom-file-input" id="contactfile" accept=".csv">
												<label class="custom-file-label" for="contactfile">Choose file</label>
											  </div>
											</div>
										</div>
										<small><i class="fas fa-exclamation-triangle mr-1"></i> Only .csv files can be imported.</small>
										<a href="/promote-cc/promote-list-example.csv" download><small id="download-csv-example"> Download Sample.</small></a>
									</div>

									<div class="form-group">
										<div class="mt-2">
										  <label class="label cusor-pointer d-flex text-center" for="optins">
											<input  class="label__checkbox" type="checkbox" name="optins" value="1" type="checkbox" id="optins" required />
											<span class="label__text d-flex align-items-center">
											  <span class="label__check d-flex rounded-circle mr-2">
												<i class="fa fa-check icon small"></i>
											  </span>
												<span class="text-uppercase small letter-spacing-1 d-inline-block">These users gave me permission to email them </span>
											</span>
										  </label>
										</div>
									</div>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
									<input type="hidden" name="listid" value="<?php echo $list_id; ?>">
									<input type="submit" class="btn bg-blue text-white" value="Import" name="importSubmit">
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
			
        </main>
      </div>
    </div>


    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>
	<script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
	<script src="//cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
	<script src="//cdn.datatables.net/select/1.2.7/js/dataTables.select.min.js"></script>

	<script>
		$(document).ready( function () {
			
			var table = $('table').DataTable({
				columnDefs: [ {
					orderable: false,
					className: 'select-checkbox',
					targets:   0
				} ],
				select: {
					style:    'multi',
					selector: 'tr'
				},
				order: [[ 1, 'asc' ]]
			});
			table
				.on( 'select', function ( e, dt, type, indexes ) {
					handleDeleteBtn();
				} )
				.on( 'deselect', function ( e, dt, type, indexes ) {
					handleDeleteBtn();
				} );
			function handleDeleteBtn(){
				var count = table.rows( { selected: true } ).count();
				if(count>0){
					$(".deleteBtn").removeClass("disabled");
				}else
					$(".deleteBtn").addClass("disabled");
			}
			$(".deleteBtn").click(function(e){
				e.preventDefault();
			
				var count = table.rows( { selected: true } ).count();
				if(count>0){
					var ids = $("tr.selected").map(function() {
					  return $(this).data('id');
					}).get();
					var listid = $("#listid").text();
					if(confirm("Are you sure you want to proceed?")){
						window.location.href = "xt_deleteSubscriber.php?listid="+listid+"&id="+ids;
					}
				}
				
			});
		});
		$(document).on('change','input[type="file"]',function(e){
			var fileName = e.target.files[0].name;
			$(this).siblings('.custom-file-label').html(fileName);
		});
	</script>	
  </body>
</html>