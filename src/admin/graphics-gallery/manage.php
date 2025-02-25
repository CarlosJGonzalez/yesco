<!doctype html>
<html lang="en">
  <head>
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.21/b-1.6.3/b-html5-1.6.3/datatables.min.css"/>
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/head.php"); ?>

    <title>Graphics Library Orders | <?php echo CLIENT_NAME; ?></title>
  </head>
  <body class="bg-light cbp-spmenu-push">
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/top.php"); ?>

    <div class="container-fluid">
      <div class="row">
        <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/nav.php"); ?>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 p-0 mb-4">
			<div class="p-0 border-bottom mb-4">
				<div class="breadcrumbs bg-white px-3 py-1 border-bottom small">
					<a href="/admin/graphics-gallery/" class="text-muted">Graphics Library</a>
					<span class="mx-1">&rsaquo;</span>
					<span class="font-weight-bold text-muted">Graphic Requests</span>
				</div>
				<div class="border-bottom-dotted d-flex d-block align-items-center clearfix py-2 px-4">
					<h1 class="h2 font-light mb-0 text-center text-sm-left"><i class="fas fa-paint-brush mr-2"></i> Requests</h1>
				</div>
				<div class="py-3 px-4 d-block d-xl-flex align-items-center">
					<a class="small text-blue d-block d-lg-none" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">Advanced Search</a>
					
					<div class="collapse show w-100" id="collapseExample">
						<div class="d-xl-flex">
							<div class="d-flex d-xl-inline-block mb-2 mb-xl-0 mr-2 align-items-center">
								<span class="letter-spacing-1 text-uppercase small mr-2 mr-xl-0">Status:</span>
								<select name="col4_filter" class="flex-grow d-xl-inline-block form-control form-control-sm w-auto rounded-pill custom-select-arrow pr-4 column_filter_select" data-column="4">
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
	
			<div class="px-4 py-3">	
				
				<?php include $_SERVER['DOCUMENT_ROOT']."/includes/alerts.php"; ?>
			
				<div class="table-responsive">
					<table class="table table-striped">
						<thead class="thead-dark">
							<tr>
								<th>Date Requested</th>
								<th>Location</th>
								<th>Title</th>
								<th>Details</th>
								<th>Status</th>
								<?php if(roleHasPermission('update_custom_graphic_request', $_SESSION['role_permissions'])){ ?>
								<th>Notes</th>
								<?php } ?>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$requests = $db->rawQuery("select custom_requests.*,locationlist.companyname from custom_requests left join locationlist on locationlist.storeid=custom_requests.storeid");
							if($db->count > 0){
								foreach($requests as $request){
							?>
							<tr>
								<td><?php echo date('m/d/Y g:i A',strtotime($request['start_date']))?></td>
								<td><?php echo $request['companyname'].' ('.$request['storeid'].')'?></td>
								<td><?php echo stripcslashes($request['title'])?></td>
								<td><?php echo stripcslashes($request['job_details'])?></td>
								<td><?php echo $request['status']?></td>
								<?php if(roleHasPermission('update_custom_graphic_request', $_SESSION['role_permissions'])){ ?>
								<td><?php echo $request['notes']?></td>
								<?php } ?>
								<td class="nowrap">
								<!-- View Request -->
								<a href="details.php?id=<?php echo $request['id']?>" class="text-dark" title="View Request"><i class="fas fa-eye mr-2"></i></a>
								
								<?php if(roleHasPermission('update_custom_graphic_request', $_SESSION['role_permissions'])){ ?>
								<!-- Edit Request Status and notes -->
								<a href="#updateRequestModal" title="Update Request" data-toggle="modal" data-target="#updateRequestModal" class="text-dark" title="Edit Request" data-id-request="<?php echo $request['id']?>"><i class="fas fa-edit mr-2"></i></a>
								<?php } ?>
							</tr> 
							<?php }
							}?>

						</tbody>
					</table>
				</div>						
			</div>
        
			<!-- Edit Request Modal -->
			<form action="/admin/graphics-gallery/xt_update_request.php" method="POST">
				<div class="modal fade" id="updateRequestModal" tabindex="-1" role="dialog" aria-labelledby="updateRequestLabel" aria-hidden="true">
				  <div class="modal-dialog" role="document">
					<div class="modal-content">
					  <div class="modal-header">
						<h5 class="modal-title" id="updateRequestLabel">Update Request</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						  <span aria-hidden="true">&times;</span>
						</button>
					  </div>
					  <div class="modal-body">
						
					  </div>
					  <div class="modal-footer">
						<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
						<input type="submit" class="btn bg-blue text-white btn-sm" value="Save">
					  </div>
					</div>
				  </div>
				</div>
			</form>
			<!-- /Edit Image -->				
			</div>
		
        </main>
      </div>
    </div>


    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>
	<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.21/b-1.6.3/b-html5-1.6.3/datatables.min.js"></script>
	<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
    <script type="text/javascript" src="//cdn.datatables.net/plug-ins/1.10.13/sorting/datetime-moment.js"></script>
	<script>
	$(document).ready( function () {
		$.fn.dataTable.moment('MM/DD/YYYY h:mm A');
		var table = $('table').DataTable({
			pageLength: 50,
			dom: '<"row"<"col-sm-6"l><"col-sm-6 text-right"f>>rt<"row"<"col-sm-6"i><"col-sm-6"p>>',
			buttons: [
				{ extend: 'excelHtml5', 
				text: 'Export',
				 className: 'btn btn-sm bg-blue rounded-pill text-white border-0 text-uppercase px-3'
				}
			],
			order:[0,"desc"],
			"columnDefs": [ {
				"targets": -1,
				"orderable": false
			} ]
		});
		
	});
	
	if($( window ).width()<992){
		$('.collapse').collapse('hide')
	}
	
	$( window ).resize(function() {
		if($( window ).width()<992){
			$('.collapse').collapse('hide')
		}else{
			$('.collapse').collapse('show')
		}
	});
	
	$('select.column_filter_select').on( 'change', function () {
		filterColumnSelect( $(this).attr('data-column') );
	} );
	
	function filterColumnSelect ( i ) {
		$('table').DataTable().column( i ).search(
			$('select[name="col'+i+'_filter"] option:selected').val()
		).draw();
	}
	
	$("#updateRequestModal").on("show.bs.modal", function(event) {
		var el = event.relatedTarget;
		var id = $(el).data("id-request");
		$.ajax({
			type: "POST",
			url: "get_custom_request_info.php",
			data: {"id":id},
			dataType:"html",
			cache: false,
			success: function(result){
				$("#updateRequestModal .modal-body").html(result);
			},
			error: function(xhr, status, error) {
			  var err = eval("(" + xhr.responseText + ")");
			  console.log(err.Message);
			} 
		});
	});
	</script>
  </body>
</html>