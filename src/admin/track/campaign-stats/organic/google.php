<!doctype html>
<html lang="en">
  <head>
	<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<link href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.css" rel="stylesheet" type="text/css" />
	<link href="//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
	<link href="//cdn.datatables.net/buttons/1.5.6/css/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/head.php"); ?>
    <title>GMB Stats | Local <?php echo CLIENT_NAME; ?></title>
  </head>
  <body class="bg-light cbp-spmenu-push">
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/top.php"); ?>

    <div class="container-fluid">
      <div class="row">
        <?php 
		include ($_SERVER['DOCUMENT_ROOT']."/includes/nav.php"); 
		?>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 p-0">
	
			<div class="p-0 border-bottom mb-4">
				<div class="d-flex d-block align-items-center clearfix py-2 px-4">
					<h1 class="h2 font-light mb-0 text-center text-sm-left"><i class="fab fa-google mr-2"></i> GMB Stats</h1>
					<div class="ml-auto d-flex align-items-center">
						<div id="reportrange" class="rounded border bg-white py-2 px-3 cursor-pointer rounded-right-0">
							<i class="far fa-calendar-alt"></i>&nbsp;
							<span></span> <i class="fa fa-caret-down"></i>
						</div>
					</div>
				</div>
			</div>
						
			<div class="py-3 px-4">
				<?php include $_SERVER['DOCUMENT_ROOT']."/includes/alerts.php"; ?>
				
				<div class="row justify-content-center">
					<div class="col col-lg-2 col-sm-6 col-xl-2 mb-1 text-center">
						<div class="h-100 bg-blue text-white py-4 px-2 rounded">
							<span class="d-block h4 text-uppercase"><font size="4">TOTAL SEARCHES</font></span>
							<span class="d-block h2" id="totalSearch"><b>0</b></span>
						</div>
					</div>
					<div class="col col-lg-2 col-sm-6 col-xl-2 mb-1 text-center">
						<div class="h-100 bg-blue text-white py-4 px-2 rounded">
							<span class="d-block h4 text-uppercase"><font size="4">TOTAL VIEWS</font></span>
							<span class="d-block h2" id="totalView"><b>0</b></span>
						</div>
					</div>
					<div class="col col-lg-2 col-sm-6 col-xl-2 mb-1 text-center">
						<div class="h-100 bg-blue text-white py-4 px-2 rounded">
							<span class="d-block h4 text-uppercase"><font size="4">TOTAL ACTIONS</font></span>
							<span class="d-block h2" id="totalAction"><b>0</b></span>
						</div>
					</div>
					<div class="col col-lg-2 col-sm-6 col-xl-2 mb-1 text-center">
						<div class="h-100 bg-blue text-white py-4 px-2 rounded">
							<span class="d-block h4 text-uppercase"><font size="4">TOTAL POST VIEWS</font></span>
							<span class="d-block h2" id="postViews"><b>0</b></span>
						</div>
					</div>
				</div>
				
				<div class="table-responsive">
					<table class="table table-striped" id="log_history_table">
						<thead class="thead-dark">
							<tr>
								<!--<th>Date</th>-->
								<th>Location Name</th>
								<th title="The number of times the resource was shown when searching for the location directly." >Direct Searches </th>
								<th title="The number of times the resource was shown as a result of a categorical search." >Discovery Searches </th>
								<th title="The number of times the resource was viewed on Google Maps." >Map Views</th>
								<th title="The number of times the resource was viewed on Google Search." >Search Views</th>
								<th title="The number of times the phone number was clicked." >Calls</th>
								<th title="The number of times the website was clicked." >Actions WebSite</th>
								<th title="The number of times driving directions were requested." >Actions Driving direction</th>
								<th title="The number of times driving directions were requested." >Post Views</th>
							</tr>
						</thead>
						<tfoot align="right">
							<!--<th></th>-->
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
						</tfoot>
					</table>
				</div>
			</div>

        </main>
      </div>
    </div>

    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>
	<script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
	<script src="//cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
	<script src="//cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
	<script src="//cdn.datatables.net/buttons/1.5.6/js/buttons.bootstrap4.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
	<script src="//cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
	<script src="//cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>
	<script src="//cdn.datatables.net/buttons/1.5.6/js/buttons.colVis.min.js "></script>
	<script src="//cdn.datatables.net/select/1.2.2/js/dataTables.select.min.js"></script>
	  
	<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
	<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

	<script type="text/javascript">
	$(document).ready(function(){
		//Loads information on the table without the start_date and end_date parameters
		fetch_data('no');
	
	} );
	
	//Loads the information of the table
	function fetch_data(is_date_search, start_date='', end_date='',client = <?php echo $_SESSION['client'];?>){
		var userTable = $('#log_history_table').DataTable({
			'processing': true,
			'responsive': true,
			'serverSide': true,
			"pageLength": 50,
			"lengthMenu": [[50, 100,200,500], [50, 100,200,500]],
			'serverMethod': 'post',
			'ajax': {
			  'url':'xt_google.php',
			   data:{is_date_search:is_date_search, start_date:start_date, end_date:end_date}
			},
			'searching': true,
			"order": [[ 0, "desc" ]],
			'aoColumnDefs': [{ "bSortable": false, "aTargets": [ 1 ] }, 
			{ "bSearchable": false, "aTargets": [ 0, 1 ]}],
			'columns': [
			 //{ data: 'date' },
			 { data: 'companyname' },
			 { data: 'queries_direct' },
			 { data: 'queries_indirect' },
			 { data: 'views_map' },
			 { data: 'views_search' },
			 { data: 'actions_phone' },
			 { data: 'actions_website' },
			 { data: 'actions_driving_directions' },
			 { data: 'local_post_views_search' },
			],
			columnDefs: [ {
			orderable: false
			} ],
			dom: '<"row"<"col-sm-6"l><"col-sm-6 text-right"Bf>>rt<"row"<"col-sm-6"i><"col-sm-6"p>>',
			buttons: [
					{ extend: 'excel',text: 'Export'},
					'print'
				],
			"footerCallback": function ( row, data, start, end, display ) {
				var api = this.api(), data,x;

				// Remove the formatting to get integer data for summation
				var intVal = function ( i ) {
					return typeof i === 'string' ?
						i.replace(/[\$,]/g, '')*1 :
						typeof i === 'number' ?
							i : 0;
				};
				var totalSearch = 0;
				var totalView = 0;
				var totalAction = 0;
				var postViews = 0;

				for(x=1;x<9;x++){
					// Total over all pages
					total = api
						.column( x )
						.data()
						.reduce( function (a, b) {
							return intVal(a) + intVal(b);
						}, 0 );

					// Total over this page
					pageTotal = api
						.column( x, { page: 'current'} )
						.data()
						.reduce( function (a, b) {
							return intVal(a) + intVal(b);
						}, 0 );
						
									
					if(x == 1 || x == 2){
						totalSearch += total;
					}

					if(x == 3|| x == 4){
						totalView += total;
					}

					if(x == 5 || x == 6 || x ==7){
						totalAction += total;
					}

					if(x == 8){
						postViews += total;
					}
					//pageTotal = pageTotal.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
					total = total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
					// Update footer
					$( api.column( x ).footer() ).html(
						total +' total'
					);
				}
				
				$('#totalSearch').text(totalSearch.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
				$('#totalAction').text(totalAction.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
				$('#totalView').text(totalView.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
				$('#postViews').text(postViews.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
			}
		});
	}

		//Datepicker
		$(function() {		
			$('#reportrange').daterangepicker({
				autoUpdateInput: false,
				  locale: {
					  cancelLabel: 'Clear'
				  },
				opens: 'left',
				ranges: {
				   'Last 7 Days': [moment().subtract(6, 'days'), moment()],
				   'Last 30 Days': [moment().subtract(29, 'days'), moment()],
				   'This Month': [moment().startOf('month'), moment()],
				   'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
				   'This Year': [moment().startOf('year'), moment()],
				   'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')]
				}
			}, function(start, end, label) {

				$('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
				$('#log_history_table').DataTable().destroy();
				fetch_data('yes', start.format('MM/DD/YYYY'), end.format('MM/DD/YYYY'));
			});

			$('#reportrange').on('cancel.daterangepicker', function(ev, picker) {
				$(this).val('');
				$('#reportrange span').html("");
				$('#log_history_table').DataTable().destroy();
				fetch_data('no');
			});
		});
		//End Datepicker
	</script>
  </body>
</html>