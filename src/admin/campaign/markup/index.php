<!doctype html>
<html lang="en">
  <head>
	 <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.23/b-1.6.5/b-html5-1.6.5/datatables.min.css"/>
	  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <?php 
    	include ($_SERVER['DOCUMENT_ROOT']."/includes/head.php");
    	if( (!(roleHasPermission('show_markup_history', $_SESSION['role_permissions']))) && (isset($_SESSION['email']))){
			header('location: /admin/campaign/campaign-info.php');
			exit;
		}

		if( !isset($_GET['campid']) ){
			header('location: /admin/campaign/campaign-info.php');
			exit;
		}else{
			$campid = $_GET['campid'];
		}
    ?>

    <title>Campaign Markup | <?php echo CLIENT_NAME; ?></title>
  </head>
  <body class="bg-light cbp-spmenu-push">
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/top.php"); ?>

    <div class="container-fluid">
      <div class="row">
        <?php 
        	include ($_SERVER['DOCUMENT_ROOT']."/includes/nav.php"); 
        	require ($_SERVER['DOCUMENT_ROOT']."/includes/DasApiSDK/vendor/autoload.php");
			use Das\MarkUp;

			$markupObj = new Markup($token_api);
        ?>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 p-0">
			
			<div class="p-0 border-bottom mb-4">
				<div class="d-flex d-block align-items-center clearfix py-2 px-4">
					<h1 class="h2 font-light mb-0 text-center text-sm-left"><i class="fas fa-history mr-2"></i> Campaign Markup</h1>
					<div class="ml-auto">
						<span class="cursor-pointer" data-toggle="modal" data-target="#markupModal"><i class="fas fa-plus fa-2x text-muted"></i></span>
					</div>
				</div>
			</div>
			
			<div class="py-3 px-4">
				<?php include $_SERVER['DOCUMENT_ROOT']."/includes/alerts.php"; ?>
				<div class="table-responsive">
					<table class="table">
						<thead class="thead-dark">
							<tr>
								<th>Start Date</th>
								<th>End Date</th>
								<th>Markup</th>
								<th>Active</th>
								<th></th>
							</tr>
						</thead>						
					</table>
				</div>
				<!-- Modal -->
				<form action="xt_add_update.php" method="POST">
					<div class="modal fade" id="markupModal" tabindex="-1" aria-labelledby="markupModalLabel" aria-hidden="true">
					  <div class="modal-dialog">
						<div class="modal-content">
						  <div class="modal-header">
							<h5 class="modal-title" id="markupModalLabel">MarkUp</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							  <span aria-hidden="true">&times;</span>
							</button>
						  </div>
						  <div class="modal-body">
							  <!--<div class="form-group">
								<label for="campid">Campaign ID</label>
								<input type="text" class="form-control" id="campid" name="campid_id">
							  </div> -->
							  <div class="form-group">
								<label for="campid">Markup</label>
								 <div class="input-group">
									<input type="number" class="form-control" id="markup" step="any" name="markup">
									  <div class="input-group-append">
										<label class="input-group-text" for="markup">%</label>
									  </div>
								  </div>
							  </div>
							  <div class="form-group">
								<label for="start">Start Date</label>
								<input type="text" class="form-control" id="start_date" name="start">
							  </div>
							  <div class="form-group">
								<label for="end">End Date</label>
								<input type="text" class="form-control" id="end_date" name="end">
							  </div>

						  </div>
						  <div class="modal-footer">
							<button type="button" class="btn bg-blue text-white" id="add_update_markup" data-id="0">Save changes</button>
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
	<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.23/b-1.6.5/b-html5-1.6.5/datatables.min.js"></script>
		<script src="//cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
	    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

	<script>
		var datatables;
		$(document).ready( function () {
			dataTable = $('table').DataTable({
			  "pageLength": 25,
				dom: '<"row"<"col-sm-6"l><"col-sm-6 text-right"Bf>>rt<"row"<"col-sm-6"i><"col-sm-6"p>>',
				buttons: [
						{ extend: 'excel',text: 'Export'}
				],
				'serverSide': true,
				'serverMethod': 'POST',
				'ajax': {
				  'url':'xt_history.php',
				   data: { id:<?php echo $campid;?> }
				},
				'columns': [
					{ data: 'start'   },
					{ data: 'end' },
					{ data: 'markup' },
					{ data: 'active' },
					{ data: 'actions' }
				],
			});
		

		    $( "#start_date" ).datepicker({
		      defaultDate: "+1w",
		      numberOfMonths: 3,
		      onClose: function( selectedDate ) {
		        $( "#end_date" ).datepicker( "option", "minDate", selectedDate );
		      }
		    });
		    $( "#end_date" ).datepicker({
		      defaultDate: "+1w",
		      numberOfMonths: 3,
		      onClose: function( selectedDate ) {
		        $( "#start_date" ).datepicker( "option", "maxDate", selectedDate );
		      }
		    });
		
			$( ".datepicker" ).datepicker();

		});

		$(document).on('click','#add_update_markup',function(e){
			e.preventDefault();

			var markup = $('#markup').val();
			var start = $('#start_date').val();
			var end = $('#end_date').val();
			var id = $(this).data("id"); 
			var campid = <?php echo $campid;?>; 

			$.ajax({
                url: "xt_add_update.php", 
                type:"POST",
                data:{ 
                		"id":id,
                		"markup":markup,
                		"start":start,
                		"end":end,
                		"campid": campid
                	 },
                success: function(result){
                	if( result == '1' ){                		
                		dataTable.ajax.reload();
                		clearFormFields('#markupModal');
                		$('#markupModal').modal('hide');
                		$('#add_update_markup').data('id',0);
                	}
                }
            });  

			
		});

		 function clearFormFields(area) {
                $(area).find('input,textarea,select').val('');
            };


		$(document).on('click','.delete',function(e){
			e.preventDefault();
			self = $(this);
			var id = self.data("id");    

			$.ajax({
                url: "xt_delete.php", 
                type:"POST",
                data:{ "id":id },
                success: function(result){
                	if( result == '1' ){
                		self.parent().parent().find("td").eq(3).html('No');
                		dataTable.ajax.reload();
                	}
                }
            });
		});

		$(document).on('click','.restore',function(e){
			e.preventDefault();
			self = $(this);
			var id = self.data("id");    

			$.ajax({
                url: "xt_restore.php", 
                type:"POST",
                data:{ "id":id },
                success: function(result){
                	if( result == '1' ){
                		self.parent().parent().find("td").eq(3).html('Yes');
                		dataTable.ajax.reload();
                	}
                }
            });
		});

		$(document).on('click','.edit',function(e){
			e.preventDefault();
			self = $(this);
			var id = self.data("id");    

			$.ajax({
                url: "xt_getmarkup.php", 
                type:"POST",
                data:{ "id":id },
                success: function(result){
                	var json = $.parseJSON(result);
                	$('#markup').val(json.markup);
					$('#start_date').val(json.start);
					$('#end_date').val(json.end);
					$('#add_update_markup').data('id',id);
					$('#markupModal').modal('show');
                }
            });
		});
		
	</script>
  </body>
</html>