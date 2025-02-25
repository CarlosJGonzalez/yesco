<!doctype html>
<html lang="en">
  <head>
	  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
	  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.6/b-html5-1.5.6/datatables.min.css"/>

    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/head.php"); 
	  include ($_SERVER['DOCUMENT_ROOT']."/includes/ClassDasWebsite.php"); 
	  $from = date("Y-m-d 00:00:00", strtotime("-1 months"));
		$to = date("Y-m-d 23:59:59");

		if (!empty($_GET["from"]))
			$from = date("Y-m-d 00:00:00", strtotime($db->escape($_GET["from"])));
		if (!empty($_GET["to"]))
			$to = date("Y-m-d 23:59:59", strtotime($db->escape($_GET["to"])));
	  ?>

    <title>Form Data | <?php echo CLIENT_NAME; ?></title>
  </head>
  <body class="bg-light cbp-spmenu-push">
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/top.php"); ?>

    <div class="container-fluid">
      <div class="row">
        <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/nav.php"); ?>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 p-0 mb-4 dashboard">
			<div class="p-0 border-bottom mb-4">
				<div class="border-bottom-dotted d-flex d-block align-items-center clearfix py-2 px-4">
					<h1 class="h2 font-light mb-0 text-center text-sm-left"><i class="fas fa-table mr-2"></i> Form Data</h1>
					<div class="ml-auto">
						<div id="reportrange" class="rounded border bg-white py-2 px-3 cursor-pointer rounded-right-0">
							<i class="far fa-calendar-alt"></i>&nbsp;
							<span></span> <i class="fa fa-caret-down"></i>
						</div>
					</div>
				</div>
				<div class="py-3 px-4 d-block d-xl-flex align-items-center">
					<a class="small text-blue d-block d-lg-none" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">Advanced Search</a>
					
					<div class="collapse show" id="collapseExample">
						<div class="d-flex d-xl-inline-block mb-2 mb-xl-0 mr-2 align-items-center">
							<span class="letter-spacing-1 text-uppercase small mr-2 mr-xl-0">Form:</span>
							<select name="col6_filter" class="flex-grow d-xl-inline-block form-control form-control-sm w-auto rounded-pill custom-select-arrow pr-4 column_filter_select" data-column="6">
								<option value="">All Forms</option>
								<?php $forms = $db->orderBy("name")->get("form_types");
								foreach($forms as $form){
								?>
								<option value="<?php echo $form['name']; ?>"><?php echo $form['name']; ?></option>
								<?php } ?>
							</select>
						</div>

					</div>
						
					
				</div>
			</div>
        	<div class="px-4 py-3">
				<?php include $_SERVER['DOCUMENT_ROOT']."/includes/alerts.php"; ?>
				<?php
				$data = new Das_Website($_SESSION['storeid']);
				$forms = $data->getFormData(array("startDate"=>$from,"endDate"=>$to));				
				//$forms = $db->where("storeid",$_SESSION['storeid'])->where("date", Array ($from, $to), 'BETWEEN')->get("form_data");
				
				?>

				<table class="table table-striped">
					<thead class="thead-dark">
						<th>Date</th>
						<th>Name</th>
						<th>Email</th>
						<th>Phone</th>
						<th>Product</th>
						<th>Comments</th>
						<th>Type</th>
						<th>Campaign</th>
					</thead>
					<tbody>
						<?php foreach($forms["records"] as $form){ 
							$sql_campid = $db->rawQueryOne("select name from advtrack.campid_data where client='9018-".$_SESSION['storeid']."' and campid='".$form['campid']."'");
						?>
						<tr>
							<td>
								<?php
								$date = new DateTime($form['date'], new DateTimeZone('UTC'));
								$date->setTimezone(new DateTimeZone('EST'));
								echo $date->format('m/d/Y g:i A'); ?>
							</td>
							<td><?php echo $form['first_name']." ".$form['last_name']; ?></td>
							<td><?php echo $form['email']; ?></td>
							<td><?php echo format_phone($form['phone']); ?></td>
							<td><?php echo $form['product']; ?></td>
							<td><?php echo nl2br(stripcslashes($form['comments'])); ?></td>
							<td><?php echo $form['type']; ?> <?php if(!empty($form['appt_date'])){ ?><i class="far fa-question-circle cursor-pointer ml-1" data-container="body" data-toggle="popover" data-html="true" data-content="<?php echo date("m/d/Y",strtotime($form['appt_date']));?>" data-trigger="focus" tabindex="0"></i> <?php } ?></td>
							<td><?php echo isset($sql_campid['name']) ? $sql_campid['name'] : 'Organic'; ?></td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
			

        
        </main>
      </div>

    </div>


    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>
	<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
	  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
	<script src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.6/b-html5-1.5.6/datatables.min.js"></script>
	  <script type="text/javascript" src="//cdn.datatables.net/plug-ins/1.10.19/sorting/datetime-moment.js"></script>
    <script>
		$(document).ready( function () {
			$.fn.dataTable.moment( 'mm/dd/YYYY hh:mm a' );
			$('table').DataTable({
				"order": [[ 0, "desc" ]],
				dom: '<"form-group" B><"row" <"col-6" l > <"col-6" f >> rt <"row" <"col-6" i > <"col-6" p >>',
				buttons: [
						{ extend: 'excel',
						 text: 'Export',
                		className: 'dt-button-custom'
						}
					]
			});
			$(function () {
			  $('[data-toggle="popover"]').popover({
				  trigger : "focus"
			  })
			})
		} );
		$(function() {
			var getUrlParameter = function getUrlParameter(sParam) {
				var sPageURL = window.location.search.substring(1),
					sURLVariables = sPageURL.split('&'),
					sParameterName,
					i;

				for (i = 0; i < sURLVariables.length; i++) {
					sParameterName = sURLVariables[i].split('=');

					if (sParameterName[0] === sParam) {
						return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
					}
				}
			};
			if(getUrlParameter('from') && getUrlParameter('to')){
				var start = moment(getUrlParameter('from'));
				var end = moment(getUrlParameter('to'));
				//$('#reportrange span').html(getUrlParameter('from').format('MMMM D, YYYY') + ' - ' + getUrlParameter('to').format('MMMM D, YYYY'));
			}else{
				var start = moment().subtract(29, 'days');
				var end = moment();
			}

			function cb(start, end) {
				console.log(start);
				console.log(end);
				$('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
			}
		
			$('#reportrange').daterangepicker({
				opens: 'left',
				startDate: start,
				endDate: end,
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
				window.location.href='?from='+start.format('YYYY-MM-DD')+'&to='+end.format('YYYY-MM-DD');
			});

			cb(start, end);
	
		});
		$('select.column_filter_select').on( 'change', function () {
			filterColumnSelect( $(this).attr('data-column') );
		} );
		function filterColumnSelect ( i ) {
			var searchStr;
			if($('select[name="col'+i+'_filter"] option:selected').val()=="")
				searchStr = $('select[name="col'+i+'_filter"] option:selected').val();
			else
				searchStr = "^"+$('select[name="col'+i+'_filter"] option:selected').val();
			$('table').DataTable().column( i ).search(searchStr, true, false, true).draw();
		}
    </script>
  </body>
</html>