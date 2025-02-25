<!doctype html>
<html lang="en">
  <head>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	  <link href="//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
	<link href="//cdn.datatables.net/select/1.2.7/css/select.bootstrap4.min.css" rel="stylesheet" type="text/css" />
	  <link rel="stylesheet" href="/css/checkbox.css">
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/head.php");?>

    <title>Payment History | <?php echo CLIENT_NAME; ?></title>
	  
  </head>
  <body class="bg-light cbp-spmenu-push">
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/top.php"); ?>

    <div class="container-fluid">
      <div class="row">
        <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/nav.php"); ?>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 p-0 mb-4">
		<?php
			$db->where("storeid",$_SESSION['storeid']);
			//$db->where("email",'sicwing@das-group.com');
			$user_detail = $db->getOne("locationlist", "customer_id");
			//$customer_id = $user_detail["customer_id"] = 'cus_EcTbJXRDMb0vYq';
			$customer_id = $user_detail["customer_id"];
		?>
			<div class="p-0 border-bottom mb-4">
				<div class="d-flex d-block align-items-center clearfix py-2 px-4">
					<h1 class="h2 font-light mb-0 text-center text-sm-left"><i class="fas fa-history mr-2"></i> Payment History</h1>
				</div>
			</div>
			
        	<div class="px-4 py-3">
				<div class="row">
					<div class="col-sm-3">
						<?php include "nav.php"; ?>
					</div>
					<div class="col-sm-9">
						<!-- Displaying payments -->
						<div class="form-inline">
						  <div class="form-group mx-sm-3 mb-2">
							<label for="inputPassword2" class="sr-only">From</label>
							<input type="text" name="startdate" id="startdate" class="form-control" placeholder="From:" />
						  </div>
						  <div class="form-group mx-sm-3 mb-2">
							<label for="inputPassword2" class="sr-only">To</label>
							<input type="text" name="end_date" id="end_date" class="form-control" placeholder="To:" />
						  </div>
						  <input type="button" name="search_by_date" id="search_by_date" value="Search" class="btn btn-primary mb-2" />
						  <input type="hidden" name="customer_id" value="<?=$customer_id?>">
						</div>
						<div class="table-responsive">
							<table id="payment_history_table" class="table">
								<thead class="thead-dark">
									<tr>
										<th>Date</th>
										<th>Description</th>
										<th>Amount</th>
										<th>Status</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				
			</div>
			
        </main>
      </div>
    </div>

    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script src="/my-account/scripts.js"></script>
	<script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
	<script src="//cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
	<script src="//cdn.datatables.net/plug-ins/1.10.19/sorting/datetime-moment.js"></script>

	<script>
	$(document).ready( function () {
		/*$.fn.dataTable.moment('MM/DD/YYYY H:mm A');
		var table = $('table').DataTable({
			order: [[ 0, 'desc' ]]
		});*/
		
		//Creates the from date picker
		$( "#startdate" ).datepicker({
			defaultDate: "-1d",
			changeMonth: true,
			changeYear: true,
			numberOfMonths: 1,
			dateFormat: 'mm/dd/yy',
			maxDate: "0d",
			onClose: function (selectedDate) {
				$("#end_date").datepicker("option", "minDate", selectedDate);
			}
			//beforeShowDay: available,
			//minDate: "0d",
		});
			
		//Creates the to date picker	
		$( "#end_date" ).datepicker({
			defaultDate: "-1d",
			changeMonth: true,
			changeYear: true,
			numberOfMonths: 1,
			dateFormat: 'mm/dd/yy',
			maxDate: "0d",
			onClose: function (selectedDate) {
				$("#startdate").datepicker("option", "maxDate", selectedDate);
			}
			//beforeShowDay: available,
		  });
		
		//Loads information on the table without the start_date and end_date parameters
		fetch_data('no');
		
		//Loads the information of the table
		function fetch_data(is_date_search, start_date='', end_date=''){
			var userTable = $('#payment_history_table').DataTable({
				'processing': true,
				'responsive': true,
				'serverSide': true,
				'serverMethod': 'post',
				//"pagingType": "full_numbers",
				"paging": true,
				//"lengthMenu": [10, 25, 50, 75, 100],
				'ajax': {
				'url':'xt_payment.php',
				data:{
					customer_id:$("input[name='customer_id']").val(), is_date_search:is_date_search, start_date:start_date, end_date:end_date
				}
				},
				'searching': false,
				"order": [[ 0, "desc" ]],
				"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
				  if ( aData['status'] == "succeeded" )
				  {
					$('td', nRow).css('background-color', '#dff0d8' );
					$('td:eq(3)', nRow).html( '<b>Succeeded</b>' );
				  }
				  else if ( aData['status'] == "failed" )
				  {
					$('td', nRow).css('background-color', '#f2dede');
					$('td:eq(3)', nRow).html( '<b>Failed</b>' );
				  }
				  else
				  {
					$('td', nRow).css('background-color', '#d9edf7');
				  }
				},
				'aoColumnDefs': [{ "bSortable": false, "aTargets": [ 1, 2, 3 ] }, 
				{ "bSearchable": false, "aTargets": [ 0, 1, 2, 3 ]}],
				'columns': [
				 { data: 'date' },
				 { data: 'description' },
				 { data: 'amount' },
				 { data: 'status' },
				],
				columnDefs: [ {
				orderable: false
				} ],
			});
		}
				
		$('#search_by_date').click(function(){
			var start_date = $('#startdate').val();
			var end_date = $('#end_date').val();
			 
			//If both input were selected, it will send the request
			if(start_date != '' && end_date !=''){
				$('#payment_history_table').DataTable().destroy();
				//Loads information on the table baes on the start_date and end_date parameters
				fetch_data('yes', start_date, end_date);
			}else{
				alert("Both Date is Required");
			}
		}); 
		
	});
	</script>
  </body>
</html>