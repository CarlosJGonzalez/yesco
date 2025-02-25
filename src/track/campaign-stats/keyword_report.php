<!doctype html>
<html lang="en">
  <head>
	  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<!--    <link rel="stylesheet" href="/css/styles-campaign-stats.css">-->
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<link href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.css" rel="stylesheet" type="text/css" />
	<link href="//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
	<link href="//cdn.datatables.net/buttons/1.5.6/css/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/head.php");
	  $from = date("Y-m-01 00:00:00");
	  $to = date("Y-m-d 23:59:59", strtotime("-1 days"));

		if (!empty($_GET["from"]))
			$from = date("Y-m-d 00:00:00", strtotime($db->escape($_GET["from"])));
		if (!empty($_GET["to"]))
			$to = date("Y-m-d 23:59:59", strtotime($db->escape($_GET["to"])));
	  
		$fromStr = strtotime($from);
		$toStr = strtotime($to);
		$datediff = $toStr - $fromStr;
		$diff = round($datediff / (60 * 60 * 24));
		$previousFrom = date("Y-m-d 00:00:00", strtotime('-'.$diff.' days', $fromStr));
		$previousTo = date("Y-m-d 23:59:59", strtotime('-'.$diff.' days', $toStr));

		$client_filter = isset($_SESSION['storeid']) ? "ac.client = '".$_SESSION['client'].'-'.$_SESSION['storeid']."'" : "ac.client LIKE '".$_SESSION['client']."%'";
	  
	  ?>
   	<style>
	.dt-buttons{
		margin-bottom:.5rem;
		margin-top:.5rem;
	}
	.dt-buttons > button{
		border-radius: 50rem !important;
		font-size: .875rem;
		line-height: 1.5;
		background-color:#c3122f;
		padding: .25rem 1rem;
		margin-right: .5rem !important;
		border:none;
	}
	

	.starrating > input {
	  display: none;
	} /* Remove radio buttons */
	.starrating > label:before {
		display: inline-block;
	  font-style: normal;
	  font-variant: normal;
	  text-rendering: auto;
	  -webkit-font-smoothing: antialiased;
	  content: "\f005"; /* Star */
	  margin: 2px;
	  font-family: "Font Awesome 5 Free";
	  display: inline-block;
		font-size:1.5rem;
		font-weight: 900;
	}

	.starrating > label {
	  color: #222222; /* Start color when not clicked */
	}

	.starrating > input:checked ~ label {
	  color: #ffca08;
	} /* Set yellow color when star checked */

	.starrating > input:hover ~ label {
	  color: #ffca08;
	} /* Set yellow color when star hover */
	</style>
    <title>Report | Keywords by Category | Local <?php echo CLIENT_NAME; ?></title>
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
					<h1 class="h2 font-light mb-0 text-center text-sm-left"><i class="fas fa-keyboard mr-2"></i> Category</h1>
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

	
				<div class="table-responsive mt-4">
					<table class="table" id="categoryTable">
						<thead class="thead-dark">
							<tr class="text-white" style="background-color:#343a40;">
								<th>Category</th>								
								<th class="text-center" colspan="3" scope="colgroup">Impressions</th>
								<th class="text-center" colspan="3" scope="colgroup">Clicks</th>
								<th class="text-center" colspan="3" scope="colgroup">CTR</th>

							</tr>
							<tr class="text-center" style="background-color:#dedede;">
								<th></th>
								<th scope="col" class="cp-font">This</th>
								<th scope="col" class="cp-font">Last</th>
								<th scope="col" class="cp-font">% Change</th>
								<th scope="col" class="cp-font">This</th>
								<th scope="col" class="cp-font">Last</th>
								<th scope="col" class="cp-font">% Change</th>
								<th scope="col" class="cp-font">This</th>
								<th scope="col" class="cp-font">Last</th>
								<th scope="col" class="cp-font">% Change</th>
							</tr>
						</thead>
						<tbody>
							<?php

							$sql="SELECT akp.adgroupname,(SELECT category FROM `advtrack`.`group_signs` WHERE `product` = akp.adgroupname LIMIT 1 ) as category,SUM(clicks) as clicks,SUM(impressions) as impressions,(SUM(clicks)/SUM(impressions)) as ctr FROM `advtrack`.`adwords_campaigns` ac INNER JOIN advtrack.adwords_keyword_performance akp ON ac.campaignId = akp.campaignId WHERE ".$client_filter." and akp.date_start BETWEEN '".$from."' and '".$to."' GROUP BY category"; 
							 

							$sql1="SELECT akp.adgroupname,(SELECT category FROM `advtrack`.`group_signs` WHERE `product` = akp.adgroupname LIMIT 1 ) as category,SUM(clicks) as clicks,SUM(impressions) as impressions,(SUM(clicks)/SUM(impressions)) as ctr FROM `advtrack`.`adwords_campaigns` ac INNER JOIN advtrack.adwords_keyword_performance akp ON ac.campaignId = akp.campaignId WHERE ".$client_filter." and akp.date_start BETWEEN '".$previousFrom."' and '".$previousTo."' GROUP BY category"; 


							$info = $db->rawQuery($sql);
							$info1 = $db->rawQuery($sql1);
							if ($db->count > 0)
								for ($i=0; $i < count($info); $i++) { 
									$data = $info[$i];
									$data1 = $info1[$i];
								?>
							<tr >
								<td><?php echo isset($data['category']) ? $data['category'] : $data['adgroupname']; ?></td>
								<td class="text-right" ><?php echo isset($data['clicks']) ? $data['clicks']: 0 ; ?></td>
								<td class="text-right" ><?php echo isset($data1['clicks']) ? $data1['clicks']: 0 ; ?></td>
								<td class="text-right" ><?php echo round(yoy($data['clicks'] ,$data1['clicks']),2); ?></td>
								<td class="text-right" ><?php echo isset($data['impressions']) ? $data['impressions']: 0 ; ?></td>
								<td class="text-right" ><?php echo isset($data1['impressions']) ? $data1['impressions']: 0 ; ?></td>
								<td class="text-right" ><?php echo round(yoy($data['impressions'] ,$data1['impressions']),2); ?></td>
								<td class="text-right" ><?php echo isset($data['ctr']) ? $data['ctr']: 0 ; ?></td>
								<td class="text-right" ><?php echo isset($data1['ctr']) ? $data1['ctr']: 0 ; ?></td>
								<td class="text-right" ><?php echo round(yoy($data['ctr'] ,$data1['ctr']),2); ?></td>
							</tr>
							<? } ?>
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
	  
	  <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
	<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

	<script>
		//Date
		$(document).ready(function(){
			
			//$.fn.dataTable.moment('dddd, MMMM D, YYYY');
			var table = $('#categoryTable').DataTable( {
				responsive: true,
				"pageLength": 25,
				"order": [[ 0, "desc" ]],
				dom: 'B<"clear"><"row"<"col-sm-6"l><"col-sm-6"f>>rt<"row"<"col-sm-6"i><"col-sm-6"p>>',
				buttons: [
						{ extend: 'excel',text: 'Export'},
						'print'
					],
			} );		
		});

		
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
				var start = moment().startOf('month');
				var end = moment().subtract(1, 'days');
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
				$('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
				window.location.href='?from='+start.format('YYYY-MM-DD')+'&to='+end.format('YYYY-MM-DD');
			});

			cb(start, end);
	
		});
	</script>
  </body>
</html>
<?php
	function yoy($thisYear ,$lastYear,$percent=true ){
		if( $lastYear <= 0 && $thisYear <= 0 )
			return 0;

		if( !$lastYear ){
			return $thisYear * (($percent) ? 100 : 1);
		}

		if( $thisYear <= 0 ){
			return ($thisYear - $lastYear) * (($percent) ? 100 : 1);
		}

		return (($thisYear - $lastYear) / $lastYear) * (($percent) ? 100 : 1);
	}
?>