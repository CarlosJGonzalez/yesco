<!doctype html>
<html lang="en">
  <head>
	<link href="//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
	<link href="//cdn.datatables.net/select/1.2.7/css/select.bootstrap4.min.css" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" href="/css/checkbox.css">
    <?php 
	  include ($_SERVER['DOCUMENT_ROOT']."/includes/head.php");
	  if(!(roleHasPermission('show_graphics_gallery', $_SESSION['role_permissions']))){
		header('location: /');
		  exit;
		}
	  ?>

    <title>Graphics Library Requests | <?php echo CLIENT_NAME; ?></title>
	  
  </head>
  <body class="bg-light cbp-spmenu-push">
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/top.php"); ?>

    <div class="container-fluid">

      <div class="row">
        <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/nav.php"); ?>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 p-0 mb-4">
			
			<div class="p-0 border-bottom mb-4">
				<div class="breadcrumbs bg-white px-3 py-1 border-bottom small">
					<a href="/graphics-gallery/" class="text-muted">Graphics Library</a>
					<span class="mx-1">&rsaquo;</span>
					<span class="font-weight-bold text-muted">Requests</span>
				</div>
				<div class="border-bottom-dotted d-flex d-block align-items-center clearfix py-2 px-4">
					<h1 class="h2 font-light mb-0 text-center text-sm-left"><i class="fas fa-paint-brush mr-2"></i> Requests</h1>
					<div class="ml-auto">
						<button type="button" id="dropdownMenuButton" data-toggle="modal" data-target="#requestModal" class="border-0 bg-transparent">
							<i class="fas fa-2x text-muted fa-plus-circle"></i>
						</button>
					</div>
				</div>
				<div class="py-3 px-4 d-block d-xl-flex align-items-center">
					<a class="small text-blue d-block d-lg-none" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">Advanced Search</a>
					
					<div class="collapse show w-100" id="collapseExample">
						<div class="d-xl-flex">
							<div class="d-flex d-xl-inline-block mb-2 mb-xl-0 mr-2 align-items-center">
								<span class="letter-spacing-1 text-uppercase small mr-2 mr-xl-0">Status:</span>
								<select name="col3_filter" class="flex-grow d-xl-inline-block form-control form-control-sm w-auto rounded-pill custom-select-arrow pr-4 column_filter_select" data-column="3">
									<option value="">All Statuses</option>
									<?php $statuses = array("Canceled","Completed","Paid","Pending");
									foreach ($statuses as $status){?>
										<option value="<?php echo $status?>"><?php echo $status?></option>
									<? } ?>
								</select>
							</div>
						</div>

					</div>
						
				</div>
			</div>
			<div class="px-4">
				<?php include ($_SERVER['DOCUMENT_ROOT']."/includes/alerts.php"); ?>
			</div>

			<!-- Customize-->
			<form action="xt_request.php" method="POST">
				<div class="modal fade" id="requestModal" tabindex="-1" role="dialog" aria-labelledby="requestModalLabel" aria-hidden="true">
				  <div class="modal-dialog modal-dialog-centered" role="document">
					<div class="modal-content">
					  <div class="modal-header">
						<h5 class="modal-title" id="requestModalLabel">Request Image</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						  <span aria-hidden="true">&times;</span>
						</button>
					  </div>
					  <div class="modal-body">
						<div class="form-group">
							<label class="text-uppercase small">Title<span class="text-danger">*</span></label>
							<input type="text" name="title" class="form-control" required>
						</div>
						  <div class="form-group">
							<label class="text-uppercase small">Dimensions <span data-toggle="tooltip" data-placement="top" title="Enter the dimensions needed for the image (i.e., 350 x 475)."><i class="far fa-question-circle"></i></span></label>
							<input type="text" name="dimensions" class="form-control">
						</div>
						<div class="form-group">
							<label class="text-uppercase small">Orientation </label>
							<select name="orientation" class="form-control custom-select-arrow">
								<option value="No Preference">No Preference</option>
								<option value="Portrait">Portrait</option>
								<option value="Landscape">Landscape</option>
								<option value="Square">Square</option>
							</select>
						</div>
						<div class="form-group">
							<label class="text-uppercase small">Special Instructions<span class="text-danger">*</span> </label>
							<textarea name="job_details" class="form-control" required></textarea>
						</div>
					  </div>
					  <div class="modal-footer">
						<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel </button>
						<input type="submit" class="btn bg-blue text-white btn-sm" value="Request">
					  </div>
					</div>
				  </div>
				</div>
			</form>
			<!-- /Customize-->
        	<div class="px-4 py-3">
				<div class="table-responsive">
					<table class="table table-striped">
						<thead class="thead-dark">
							<tr>
								<th>Date Requested</th>
								<th>Title </th>
								<th>Details </th>
								<th>Status </th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<?php
							$db->where("storeid",$_SESSION['storeid']);
							$requests = $db->get("custom_requests");
							if($db->count > 0){
								foreach($requests as $request){
							?>
							<tr>
								<td><?php echo date('m/d/Y g:i A',strtotime($request['start_date']))?></td>
								<td><?php echo $request['title']?></td>
								<td><?php echo $request['job_details']?></td>
								<td><?php echo $request['status']?></td>
								<td class="text-center"><a href="details.php?id=<?php echo $request['id']?>" class="btn btn-sm bg-blue text-white">View</a></td>
							</tr> 
							<?php }
							}?>

						</tbody>
					</table>
				</div>						
				
			</div>
        </main>
      </div>
    </div>

    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>
	<script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
	<script src="//cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
	<script src="//cdn.datatables.net/select/1.2.7/js/dataTables.select.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
	<script src="//cdn.datatables.net/plug-ins/1.10.19/sorting/datetime-moment.js"></script>

	<script>
	$(document).ready( function () {
		$.fn.dataTable.moment('MM/DD/YYYY H:mm A');
		var table = $('table').DataTable({
			order: [[ 0, 'desc' ]]
		});
		
		/*$('.dataTables_filter input').attr('data-toggle', 'tooltip')
									 .attr('data-placement', 'top')
									 .attr('title', 'Search for images using keywords.')
									 .tooltip();
									 
		$("<span class='ml-1' data-toggle='tooltip' data-placement='top' title='Search for images using keywords.'><i class='far fa-question-circle'></i></span>").insertAfter(".dataTables_filter input");
		
		$("<span class='ml-1' data-toggle='tooltip' data-placement='top' title='Increase or decrease the number of items the dashboard will show you at a time.'><i class='far fa-question-circle'></i></span>").insertAfter(".dataTables_length label");
		
		$("<span class='ml-1' data-toggle='tooltip' data-placement='top' title='These buttons will allow you to navigate through the pages of your requests.'><i class='far fa-question-circle'></i></span>").insertAfter(".dataTables_paginate ul");
		*/
	});
	
	$(document).on('change','input[type="file"]',function(e){
		var fileName = e.target.files[0].name;
		$(this).siblings('.custom-file-label').html(fileName);
	});
	
	$('select.column_filter_select').on( 'change', function () {
		filterColumnSelect( $(this).attr('data-column') );
	} );
	
	function filterColumnSelect ( i ) {
		$('table').DataTable().column( i ).search(
			$('select[name="col'+i+'_filter"] option:selected').val()
		).draw();
	}
	
	$(function () {
	  $('[data-toggle="tooltip"]').tooltip()
	})
	</script>	
  </body>
</html>