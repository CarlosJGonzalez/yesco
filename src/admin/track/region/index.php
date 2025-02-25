<!doctype html>
<html lang="en">
  <head>
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.24/b-1.7.0/b-html5-1.7.0/datatables.min.css"/>
	  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <?php 
	include ($_SERVER['DOCUMENT_ROOT']."/includes/head.php");
	if(!(roleHasPermission('region_report', $_SESSION['role_permissions']))){
		header('location: /');
		exit;
	}
	$from = strtotime("-1 months");
	$to = strtotime('now');


	if (isset($_GET["from"])){
		$from = strtotime( $db->escape($_GET["from"]) );
	}

	if (isset($_GET["to"])){
		$to = strtotime($db->escape($_GET["to"]));
	}
	?>

    <title>Region Report | <?php echo CLIENT_NAME; ?></title>
  </head>
  <body class="bg-light cbp-spmenu-push">
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/top.php"); ?>

    <div class="container-fluid">
      <div class="row">
        <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/nav.php"); ?>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 p-0">
			
			<div class="p-0 border-bottom mb-4">
				<div class="d-flex d-block align-items-center clearfix py-2 px-4">
					<h1 class="h2 font-light mb-0 text-center text-sm-left"><i class="fas fa-globe-americas mr-2"></i> Forms By Country</h1>
					<div class="ml-auto">
						<div id="reportrange" class="rounded border bg-white py-2 px-3 cursor-pointer rounded-right-0">
							<i class="far fa-calendar-alt"></i>&nbsp;
							<span></span> <i class="fa fa-caret-down"></i>
						</div>
					</div>
				</div>
			</div>
			<div class="py-3 px-4">
				<?php include $_SERVER['DOCUMENT_ROOT']."/includes/alerts.php"; ?>
				<?php
					$locations = $db->rawQuery("select ip_country,count(1) as count from form_data where date between '".date("Y-m-d h:i:s", $from)."' and '".date("Y-m-d h:i:s", $to)."' and email not like '%@das-group.com' and email not like '%@test.com' and email not like '%@testing.com' and email not like '%@test.com' and ip_country is not null group by ip_country");
					$all = $db->rawQueryOne("select count(*) as count from form_data where date between '".date("Y-m-d h:i:s", $from)."' and '".date("Y-m-d h:i:s", $to)."' and email not like '%@das-group.com' and email not like '%@test.com' and email not like '%@testing.com' and email not like '%@test.com' and ip_country is not null");
					$total = $all['count'];
				?>
				<div class="table-responsive">
					<table class="table ">
						<thead class="thead-dark">
							<tr>
								<th>Country</th>
								<th class="text-right">Count</th>
								<th class="text-right">Percentage</th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach($locations as $location){
								$country = $db->where("country_code",$location['ip_country'])->getOne("country_codes");
							?>
							<tr>
								<td class="align-middle"><?php echo $country['country_name']; ?></td>
								<td class="align-middle text-right"><?php echo number_format($location['count']); ?></td>
								<td class="align-middle text-right"><?php echo number_format(($location['count']/$total)*100,2); ?>%</td>
								
							</tr>
							<?php } ?>
						</tbody>
						<tfoot>
							<tr>
								<th colspan="2" style="text-align:right">Total:</th>
								<th></th>
							</tr>
						</tfoot>
					</table>
				</div>

			</div>
        </main>
      </div>
    </div>

    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>
	<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.24/b-1.7.0/b-html5-1.7.0/datatables.min.js"></script>
	<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
	<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
	<script>
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
			
		}else{
			var start = moment().subtract(29, 'days');
			var end = moment();
		}
		function cb(start, end) {
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
			$('#spinner_loading').removeClass("none_upload");
			$('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
			window.location.href='?from='+start.format('YYYY-MM-DD')+'&to='+end.format('YYYY-MM-DD');
		});

		cb(start, end);
		
		$('table').DataTable( {
			'responsive': true,
			"pageLength": 50,
			"order": [[ 1, "desc" ]],
			dom: '<"row"<"col-sm-6"l><"col-sm-6 text-right"Bf>>rt<"row"<"col-sm-6"i><"col-sm-6"p>>',
			buttons: [
					{ extend: 'excel',text: 'Export'}
				],
			"footerCallback": function ( row, data, start, end, display ) {
				var api = this.api(), data;

				// Remove the formatting to get integer data for summation
				var intVal = function ( i ) {
					return typeof i === 'string' ?
						i.replace(/[\$,]/g, '')*1 :
						typeof i === 'number' ?
							i : 0;
				};

				// Total over all pages
				total = api
					.column( 1 )
					.data()
					.reduce( function (a, b) {
						return intVal(a) + intVal(b);
					}, 0 );

				// Total over this page
				pageTotal = api
					.column( 1, { page: 'current'} )
					.data()
					.reduce( function (a, b) {
						return intVal(a) + intVal(b);
					}, 0 );

				// Update footer
				if(pageTotal>total){
					$( api.column( 1 ).footer() ).html(
						addCommas(pageTotal) +' ('+ addCommas(total) +' total)'
					);
				}else{
					$( api.column( 1 ).footer() ).html(
						addCommas(total)
					);
				}
			}
		} );
		function addCommas(nStr)
		{
			nStr += '';
			x = nStr.split('.');
			x1 = x[0];
			x2 = x.length > 1 ? '.' + x[1] : '';
			var rgx = /(\d+)(\d{3})/;
			while (rgx.test(x1)) {
				x1 = x1.replace(rgx, '$1' + ',' + '$2');
			}
			return x1 + x2;
		}
	</script>
  </body>
</html>