<!doctype html>
<html lang="en">
  <head>
	<link href="//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <?php 
	include ($_SERVER['DOCUMENT_ROOT']."/includes/head.php");
	if(!(roleHasPermission('general_permission', $_SESSION['role_permissions']))){
		header('location: /');
		exit;
	}
	?>

    <title>All Locations | <?php echo CLIENT_NAME; ?></title>
  </head>
  <body class="bg-light cbp-spmenu-push">
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/top.php"); ?>

    <div class="container-fluid">
      <div class="row">
        <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/nav.php"); ?>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 p-0">
			
			<div class="p-0 border-bottom mb-4">
				<div class="border-bottom-dotted d-flex d-block align-items-center clearfix py-2 px-4">
					<h1 class="h2 font-light mb-0 text-center text-sm-left"><i class="fas fa-map-marker-alt mr-2"></i> All Locations</h1>
					<div class="ml-auto">
						<a href="new.php"><i class="fas fa-plus-circle fa-2x text-muted"></i></a>
					</div>
				</div>
				<div class="py-3 px-4 d-block d-xl-flex align-items-center">
					<a class="small text-blue d-block d-lg-none" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">Advanced Search</a>
					
					<div class="collapse show" id="collapseExample">
						<div class="d-flex d-xl-inline-block mb-2 mb-xl-0 mr-2 align-items-center">
							<span class="letter-spacing-1 text-uppercase small mr-2 mr-xl-0">State:</span>
							<select name="col5_filter" class="flex-grow d-xl-inline-block form-control form-control-sm w-auto rounded-pill custom-select-arrow pr-4 column_filter_select" data-column="5">
								<option value="">All States</option>
								<?php echo stateSelect();?>
							</select>
						</div>
						<div class="d-flex d-xl-inline-block mb-2 mb-xl-0 mr-2 align-items-center">
							<span class="letter-spacing-1 text-uppercase small mr-2 mr-xl-0">Status:</span>
							<select name="col7_filter" class="flex-grow d-xl-inline-block form-control form-control-sm w-auto rounded-pill custom-select-arrow pr-4 column_filter_select" data-column="7">
								<option value="">All Statuses</option>
								<option value="Active">Active</option>
								<option value="Inactive">Inactive</option>
							</select>
						</div>
						<div class="d-flex d-xl-inline-block mb-2 mb-xl-0 mr-2 align-items-center">
							<span class="letter-spacing-1 text-uppercase small mr-2 mr-xl-0">Country:</span>
							<select name="col2_filter" class="flex-grow d-xl-inline-block form-control form-control-sm w-auto rounded-pill custom-select-arrow pr-4 column_filter_select" data-column="2">
								<option value="">All Countries</option>
								<option value="USA">USA</option>
								<option value="CAN">CAN</option>
							</select>
						</div>

					</div>
						
					
				</div>

			</div>
			
			<div class="py-3 px-4">
				<?php include $_SERVER['DOCUMENT_ROOT']."/includes/alerts.php"; ?>
				<div class="table-responsive">
					<table class="table ">
						<thead class="thead-dark">
							<tr>
								<th>Location ID</th>
								<th>Location Name</th>
								<th>Country</th>
								<th>Address</th>
								<th>City</th>
								<th>State</th>
								<th>Zip</th>
								<th>Location Manager</th>
								<th>Location Manager 2</th>
								<th>Email</th>
								<th>Status</th>
								<th>Edit</th>
								<th>Disable</th>
								<th>Last Login</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$locations = $db->get("locationlist");
							foreach($locations as $location){
								$last_login = 'N/A';
								$store_id_field = $location['storeid'];
								$row_last_login = $db->rawQuery("SELECT email, lastlogin FROM storelogin WHERE storeid = '$store_id_field' AND lastlogin = (SELECT MAX(lastlogin) FROM storelogin WHERE storeid = '$store_id_field') LIMIT 1");
								if ($db->count > 0) {
									$email_last_login = $row_last_login[0]['email'];
									$last_login_db_field = date('M d, o (h:i:s A)',strtotime($row_last_login[0]['lastlogin']));
									$last_login = ($email_last_login) ? '<b>('.$email_last_login.')</b><br> '.$last_login_db_field : $last_login = 'N/A';
								}
							?>
							<tr>
								<td class="align-middle"><?php echo $location['storeid']; ?></td>
								<td class="align-middle"><?php echo $location['companyname']; ?></td>
								<td class="align-middle"><?php echo $location['country']; ?></td>
								<td class="align-middle"><?php echo $location['address']; ?></td>
								<td class="align-middle"><?php echo $location['city']; ?></td>
								<td class="align-middle"><?php echo $location['state']; ?></td>
								<td class="align-middle"><?php echo $location['zip']; ?></td>
								<td class="align-middle"><?php echo $location['fname1'].' '.$location['lname1']; ?></td>
								<td class="align-middle"><?php echo $location['fname2'].' '.$location['lname2']; ?></td>
								<td class="align-middle"><?php echo $location['email']; ?></td>
								<td class="align-middle"><?php echo $location['suspend']==0 ? "Active" : "Inactive"; ?></td>
								<td class="align-middle text-center"><a href="edit.php?storeid=<?php echo $location['storeid']; ?>" class="btn btn-sm text-white bg-blue text-uppercase">Edit</a></td>
								<td class="align-middle text-center"><? if($location['suspend']){ ?><a href="javascript:void(0)" class="btn btn-sm text-white bg-blue text-uppercase suspend" data-storeid="<?=$location['storeid']?>">Enable</a><? }else{ ?><a href="javascript:void(0)" class="btn btn-sm text-white bg-blue text-uppercase suspend" data-storeid="<?=$location['storeid']?>">Disable</a><? } ?></td>
								<td class="align-middle"><?php echo $last_login; ?></td>
							</tr>
							<?php } ?>
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
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script src="//cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
	<script src="//cdn.datatables.net/buttons/1.5.6/js/buttons.bootstrap4.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
	<script src="//cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
	<script src="//cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>
	<script src="//cdn.datatables.net/buttons/1.5.6/js/buttons.colVis.min.js "></script>
	<script src="//cdn.datatables.net/select/1.2.2/js/dataTables.select.min.js"></script>
	<script>
		$(document).ready( function () {
			$('table').DataTable({
			  "pageLength": 25,
				"columnDefs": [ {
					//"targets": -1,
					//"orderable": false
					"targets": [7,8,9],
				    "visible":false,
				    "searchable": true
				} ],
				dom: '<"row"<"col-sm-6"l><"col-sm-6 text-right"Bf>>rt<"row"<"col-sm-6"i><"col-sm-6"p>>',
				buttons: [
						{ extend: 'excel',text: 'Export'},
						'print'
					]
			});
		} );
		$('select.column_filter_select').on( 'change', function () {
			filterColumnSelect( $(this).attr('data-column') );
		} );
		function filterColumnSelect ( i ) {
			var searchStr;
			if($('select[name="col'+i+'_filter"] option:selected').val()=="")
				searchStr = $('select[name="col'+i+'_filter"] option:selected').val();
			else
				searchStr = "^"+$('select[name="col'+i+'_filter"] option:selected').val()+"$";
			$('table').DataTable().column( i ).search(searchStr, true, false, true).draw();
		}
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
		$(".suspend").click(function(){
			var value = $(this).text().toLowerCase();
			var target = $(this);
			var storeid = $(this).data("storeid");
			
			if(confirm("Are you sure you want to "+value+" this location?")){
				$.ajax({
					url: "xt_suspend.php", 
					type:"POST",
					data:{'value':value, 'storeid':storeid},
					success: function(result){
						$(target).text(result);
					}
				});
			}
		});
	</script>
  </body>
</html>