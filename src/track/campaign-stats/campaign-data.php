<!doctype html>
<html lang="en">
  <head>
<!--	<link rel="stylesheet" href="/css/styles-campaign-stats.css">-->
	  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.6/b-html5-1.5.6/datatables.min.css"/>

    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/head.php"); ?>
   	<style>
		.dt-buttons{
			margin-bottom:.5rem;
			margin-top:.5rem;
		}
		.dt-buttons > button{
			border-radius: 50rem !important;
			font-size: .875rem;
			line-height: 1.5;
			background-color:#0067b1;
			padding: .25rem 1rem;
			margin-right: .5rem !important;
			border:none;
		}
		.popover{
			width:300px;
		}
	</style>
    <title>Campaign Data | Local <? echo CLIENT_NAME; ?></title>
  </head>
  <body class="bg-light cbp-spmenu-push">
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/top.php"); ?>

    <div class="container-fluid">
      <div class="row">
        <?php 
		include ($_SERVER['DOCUMENT_ROOT']."/includes/nav.php"); 
		
		$from = date("Y-m-d", strtotime("-1 months"));
		$to = date("Y-m-d");

		if (!empty($_GET["from"]))
			$from = date("Y-m-d", strtotime($db->escape($_GET["from"])));
		if (!empty($_GET["to"]))
			$to = date("Y-m-d", strtotime($db->escape($_GET["to"])));

		function getCampaignDetails($client,$campid,$start,$end){
			$url = 'https://www.adjack.net/api/campaigns/read.php?client='.$client.'&campid='.$campid.'&start='.$start.'&end='.$end.'&source=';
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch,CURLOPT_URL, $url);
			$result = curl_exec($ch);
			curl_close($ch);
			return json_decode($result);
		}  

		function getLeads($client,$campid,$start,$end){
			$url = 'https://www.adjack.net/api/leads/read.php?client='.$client.'&campid='.$campid.'&start='.$start.'&end='.$end;
			//echo $url;
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch,CURLOPT_URL, $url);
			$result = curl_exec($ch);
			curl_close($ch);
			return json_decode($result);
		}        


		function getClient($client){
			$url = 'https://www.adjack.net/api/client/read_one.php?client='.$client;
	   		$ch = curl_init();
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch,CURLOPT_URL, $url);
			$result = curl_exec($ch);
			curl_close($ch);
			return json_decode($result);
		} 


		function getCampid($client, $campid){
			$url = 'https://www.adjack.net/api/campid/read_one.php?client='.$client.'&campid='.$campid;
	   		$ch = curl_init();
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch,CURLOPT_URL, $url);
			$result = curl_exec($ch);
			curl_close($ch);
			return json_decode($result);
		} 
		?>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 p-0">
			<div class="p-0 border-bottom mb-4">
				<div class="d-flex d-block align-items-center clearfix py-2 px-4">
					<h1 class="h2 font-light mb-0 text-center text-sm-left"><i class="fas fa-chart-line mr-2"></i> Campaign Data</h1>
					<div class="ml-auto">
						<div id="reportrange" class="rounded border bg-white py-2 px-3 cursor-pointer rounded-right-0">
							<i class="far fa-calendar-alt"></i>&nbsp;
							<span></span> <i class="fa fa-caret-down"></i>
						</div>
					</div>
				</div>
			</div>
			
			
			
			<div class="row mx-3">
				<?php include $_SERVER['DOCUMENT_ROOT']."/includes/alerts.php"; ?>
				
				<div class="col-12">
				   <table class="datatable table" id="campaignDataTable">
						<thead class="thead-dark">
							<tr>
								<th>Client</th>
								<th>Name</th>
								<th>Campaign</th>
								<th>Total leads</th>
								<th>Impressions</th>
								<th>Clicks</th>
								<th>Cost</th>
								<th>Cost Per Lead</th>
							</tr>
						</thead>
					   <tfoot>
							<tr>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
							</tr>
						</tfoot>
						<tbody>
							<tr>			
						<?php 
						$flag_security=true;
						if($_SESSION['storeid'] > 0){
							$flag_security=false;

							//$db->setTrace (true);

							$diff = abs(strtotime(date("Y-m-d",strtotime($to))) - strtotime(date("Y-m-d",strtotime($from))));
							$days = floor($diff/ (60*60*24));
							$campids = $db->rawQuery("select * from advtrack.campid_data where client = ? and active='Y'",array($_SESSION['client']."-".$_SESSION['storeid']));
							if(count($campids)){
								foreach($campids as $campid){
									$campid = $campid['campid'];
								//echo $campid .'<br/>';
									$campaign = getCampaignDetails($_SESSION['client']."-".$_SESSION['storeid'],$campid,date("Y-m-d",strtotime($from)),date("Y-m-d",strtotime($to)));
									if(!$campaign->message) {
										$campaigns = $campaign->records;
									//echo '<pre>'.print_r(json_encode($campaigns)).'</pre>';
									//$db->setTrace (true);
										$clients = getClient($_SESSION['client']."-".$_SESSION['storeid']);
										$client_id = $clients->client;
										$client_name = $clients->name;

										$campids = getCampid($_SESSION['client']."-".$_SESSION['storeid'], $campid);
										$campid_name = $campids->name;

										$leads = getLeads($_SESSION['client']."-".$_SESSION['storeid'],$campid,date("Y-m-d",strtotime($from)),date("Y-m-d",strtotime($to)));
										$leads = $leads->records;
									//print_r ($db->trace);
									//echo '<pre>'.var_dump($leads).'</pre>';

										foreach($campaigns as $campaign){
											$client_var = $campaign->client;
											$clicks = $campaign->clicks;
											$source = $campaign->portal;
											$imps = $campaign->imps;
											$cost = $campaign->cost;
											$comm = $campaign->commission;
											
											if($campaign->fixed_budget_perday){
												$gross_cost = ($campaign->fixed_budget_perday) * $days;
											}else{
												$gross_cost = $cost * ( 1 + ($comm/100));
											}
										}
										$tot_leads = 0;
										$cpl = 0;
										if(count($leads) > 0){
											$lead_desc = "<div class='row font-weight-bold'><div class='col-8'>Campaign</div><div class='col-4'>Leads</div></div>";
											foreach($leads as $lead){ 
												$lead_desc .= "<div class='row'>";
												$lead_desc .= "<div class='col-8'>" . $lead->goal_name . "</div><div class='col-4'>" . $lead->goals . '</div>';
												$lead_desc .= "</div>";
												$tot_leads += $lead->goals;
											}
										}else $lead_desc = "";
										if ($tot_leads > 0) $cpl = $gross_cost/$tot_leads;
									//$lead_desc .= "</tbody></table>";
										?>

										<td><?php echo $client_id?></td>
										<td><?php echo $client_name?></td>
										<td><?php echo $campid_name;?></td>
										<td><?php echo number_format($tot_leads,0);?> <?php if(!empty($lead_desc)){ ?><span data-toggle="popover" data-container="body" data-html="true" data-content="<?php echo $lead_desc;?>" data-trigger="focus" tabindex="0"><i class="far fa-question-circle cursor-pointer ml-1"></i></span> <?php } ?></td>
										<td><?php echo number_format($imps,0);?></td>
										<td><?php echo number_format($clicks,0);?></td>
										<td>$<?php echo number_format($gross_cost,2);?></td>
										<td>$<?php echo number_format($cpl,2);?></td>
									</tr>

									<?php

								}
							}
						}else{
							?>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						<?php
					}

				}
						?>
						</tbody>
					</table>
				</div>
			</div>

        </main>
      </div>
    </div>

    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.6/b-html5-1.5.6/datatables.min.js"></script>
	<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
	<script src="//cdn.datatables.net/plug-ins/1.10.19/sorting/datetime-moment.js"></script>
	<script>
		$(function () {
		  $('[data-toggle="popover"]').popover({
			  trigger : "focus"
		  })
		})
		function formatNumber(num) {
		  return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
		}
		$(document).ready( function () {
			var table = $('#campaignDataTable').DataTable({
				responsive: true,
				"pageLength": 50,
				dom: 'B<"clear"><"row" <"col-6" l > <"col-6" f >> rt <"row" <"col-6" i > <"col-6" p >>',
				buttons: [
						{ extend: 'excel',text: 'Export'}
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
				for(x=3;x<7;x++){
					// Total over all pages
					total = api
						.column( x )
						.data()
						.reduce( function (a, b) {
							if(x==3) b = b.substr(0, b.indexOf('<'));
							return intVal(a) + intVal(b);
						}, 0 );

					// Total over this page
					pageTotal = api
						.column( x, { page: 'current'} )
						.data()
						.reduce( function (a, b) {
							if(x==3) b = b.substr(0, b.indexOf('<'));
							return intVal(a) + intVal(b);
						}, 0 );

					// Update footer
					var preText = "";
					if(x==6){
						preText = "$";
						//$( api.column( x ).footer() ).html(preText + total.toFixed(2) +' Total');
						$( api.column( x ).footer() ).html(
							preText + pageTotal.toFixed(2) +' ('+ preText + total.toFixed(2) +' Total)'
						);
					}else{
						//$( api.column( x ).footer() ).html(preText + formatNumber(total) +' Total');
						$( api.column( x ).footer() ).html(
							preText + formatNumber(pageTotal) +' ('+ preText+formatNumber(total) +' Total)'
						);
					}
					
				}
			}
			});
			
			table.buttons().container()
	        .appendTo( '#campaignDataTable_wrapper .col-md-6:eq(0)' );
			
			$(function () {
			  $('[data-toggle="popover"]').popover()
			})
			
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
	</script>
  </body>
</html>