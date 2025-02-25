<?php ini_set('max_execution_time', 300); ?>
<!doctype html>
<html lang="en">
<head>
	<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<link href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.css" rel="stylesheet" type="text/css" />
	<link href="//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
	<link href="//cdn.datatables.net/buttons/1.5.6/css/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
	<link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" type="text/css" />
	<?php 
	include ($_SERVER['DOCUMENT_ROOT']."/includes/head.php");

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
			background-color:#1d3349;
			padding: .25rem 1rem;
			margin-right: .5rem !important;
			border:none;
		}
		.popover{
			width:300px;
		}
		.cp-font{
			font-variant: all-petite-caps;
			font-size: 12px;
			margin-bottom: 1px;
			font-weight: 600;
			letter-spacing: .25px;
		}
		.min-td{
			min-width: 100px;
		}
		.text-grey{
			color:#969696;
		}
		.prev_border{
			border-top: 1px dotted lightgray;
			margin-top: 3px;
		}
	</style>

	<title>Campaign Data | Local <?php echo CLIENT_NAME; ?></title>
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
					<div class="border-bottom-dotted d-flex d-block align-items-center clearfix py-2 px-4">
						<h1 class="h2 font-light mb-0 text-center text-sm-left"><i class="fas fa-sync mr-2"></i> Campaign Details "<?php echo isset($_GET['campidName']) ? $_GET['campidName'] : "" ;?>"</h1>
						<div class="ml-auto">
							<div id="reportrange" class="rounded border bg-white py-2 px-3 cursor-pointer rounded-right-0">
								<i class="far fa-calendar-alt"></i>&nbsp;
								<span></span> <i class="fa fa-caret-down"></i>
							</div>
						</div>
					</div>
				</div>

				<div class="py-3 px-4">
					<?php 
					include $_SERVER['DOCUMENT_ROOT']."/includes/alerts.php"; 
					date_default_timezone_set('America/New_York');
					require ($_SERVER['DOCUMENT_ROOT']."/includes/DasApiSDK/vendor/autoload.php");
					use Das\Report;
					use Das\Client;
					use Das\CampId;
					use Das\Markup;

					if( isset($_GET['campid']) ){
						$campid=$_GET['campid'];
						$strcampid= "&campid='".$campid."'";
					}else{
						$strcampid="";
					}

					$from = strtotime("-1 months");
					$to = strtotime('now');

					if (isset($_GET['client'])){
						$client = $_GET['client'];
					}else{
						$client = $_SESSION['client'];
					}
					
					if (isset($_GET["from"])){
						$from = strtotime( $db->escape($_GET["from"]) );
					}

					if (isset($_GET["to"])){
						$to = strtotime($db->escape($_GET["to"]));
					}
					$compare = false;
					if (isset($_GET['compare'])){
						if (isset($_GET['from_compare']) && isset($_GET["to_compare"])){

							$from_compare = strtotime($db->escape($_GET['from_compare']));
							$to_compare   = strtotime($db->escape($_GET['to_compare']));

							$from_compare_ymd = date('Y-m-d',$from_compare);
							$to_compare_ymd = date('Y-m-d',$to_compare);

							$compare =true;
						}				
					}
					?>

					<div class="table-responsive">
						<table class="table" id="campaign_details">						
							<thead>
								<?php if($compare){ ?>
									<tr class="text-white" style="background-color:#343a40;">
										<th>StoreId</th>
										<th>Location</th>									
										<th class="text-center" colspan="2" scope="colgroup">Impressions</th>
										<th class="text-center" colspan="2" scope="colgroup">Clicks</th>
										<th class="text-center" colspan="2" scope="colgroup">Total leads</th>
										<th class="text-center" colspan="2" scope="colgroup">Cost</th>
										<th class="text-center" colspan="2" scope="colgroup">Cost Per Lead</th>
										<th class="text-center" colspan="2" scope="colgroup">CTR</th>
										<th class="text-center" colspan="2" scope="colgroup">Conv. Rate</th>

									</tr>

									<tr class="text-center" style="background-color:#dedede;">
										<th></th>
										<th scope="col" class="cp-font">Previous</th>
										<th scope="col" class="cp-font">Current</th>
										<th scope="col" class="cp-font">Previous</th>
										<th scope="col" class="cp-font">Current</th>
										<th scope="col" class="cp-font">Previous</th>
										<th scope="col" class="cp-font">Current</th>
										<th scope="col" class="cp-font">Previous</th>
										<th scope="col" class="cp-font">Current</th>
										<th scope="col" class="cp-font">Previous</th>
										<th scope="col" class="cp-font">Current</th>
										<th scope="col" class="cp-font">Previous</th>
										<th scope="col" class="cp-font">Current</th>
										<th scope="col" class="cp-font">Previous</th>
										<th scope="col" class="cp-font">Current</th>
									</tr>

								<?php }else{ ?>
									<tr class="text-white" style="background-color:#343a40;">
										<th>StoreId</th>
										<th>Location</th>
										<th class="text-center" >Impressions</th>
										<th class="text-center" >Clicks</th>
										<th class="text-center" >Total leads</th>
										<th class="text-center" >Cost</th>
										<th class="text-center" >Cost Per Lead</th>
										<th class="text-center" >CTR</th>
										<th class="text-center" >Conv. Rate</th>							
									</tr>
								<?php }  ?>	
							</thead>
							<tbody>
								<tr>			
									<?php 

									$report = new Report($token_api);
									$clientObj = new Client($token_api);
									$campidObj = new CampId($token_api);
									$campIdMarkupObj = new Markup($token_api);

									$params  = array(
										'groupby_store' => 1, 
										'gte' => (string)$from, 
										'lte' => (string)$to,
										'campid' => $campid,
									);

									$locations = $db->where('suspend',0)->get('locationlist',null,'storeid');
									$locations[] = array('storeid' => 0);
									foreach ($locations as $location) {
										$storeid = (string)$location['storeid'];					
										$params['storeid'] = $storeid;

										$reportCampiagn = $report->getCampaignReport($client,$params);
					
										unset($params['groupby_store']);

										$params_leads = array(
											'storeid' => $storeid,
											'gte' => (string)$from,
											'lte' => (string)$to,
											'campid' => $campid,
										);

										$leads = $report->getLeadReport($client,$params_leads);
										$tot_leads = 0;
										$lead_desc = "";

										if(!$leads['is_error'] && $leads['info']['count'] > 0){
											$leads_info = getLeadInfo($leads['data']);
											$tot_leads = $leads_info['lead_total'];
											$lead_desc = $leads_info['lead_desc'];
										}

										$clicks = 0;
										$imps   = 0;
										$cost   = 0;
											 
										if(!$reportCampiagn['is_error'] && $reportCampiagn['info']['count'] > 0  ){
											$campaign = $reportCampiagn['data'][0]; 
											$clicks = $campaign['clicks'];
											$imps   = $campaign['imps'];							
											$cost   = $campaign['cost'];	
										}
										
										if( ($clicks + $cost + $imps + $tot_leads) == 0){
											continue;
										}

										$diff = abs( $to - $from );
										$days = floor( $diff / (60*60*24) );

										if($compare){
											$diff = abs( $to_compare - $from_compare );
											$days_prev = floor( $diff / (60*60*24) );
										}
										 
										$markup = 0;

										$cpl = 0;
										$convrate = 0;
										$ctr = 0;						

										$campidInfo = $campidObj->getCampId($campid,$client,( $storeid == 0 ) ? null : $storeid );
										$debitInfo= $campidObj->getDebits($client,$storeid,$params);
										$debit = 0;

										if( $debitInfo['info']['count'] > 0){				    	    	
											$debit = $debitInfo['data'][0]['amount_range'];  	
										}

										if(!$campidInfo['is_error'] && $campidInfo['count'] > 0){
										//	$markup = ( isset($campidInfo['data'][0]['markup']) ) ? $campidInfo['data'][0]['markup'] : 0;
											$markup = $campIdMarkupObj->getMarkUpActiveByDate($campidInfo['data'][0]['id'],array('start' => (string)$from, 'end' => (string)$to ))['data'][0]['markup'];
											$gross_cost = $cost * ( 1 + ( $markup / 100 )) + $debit;
										}else{
											$gross_cost = $cost + $debit;
										}

										if($compare){

											$params_prev  = array(
												'storeid' => $storeid, 
												'gte' => (string)$from_compare, 
												'lte' => (string)$to_compare,
												'campid' => $campid,
											);

											$reportCampiagnPrev = $report->getCampaignReport( $client,$params_prev );

											$debit_prev = 0;
											unset($params['storeid']);
											$debitInfo_prev= $campIdObj->getDebits($client,null,$params_prev);

											if( $debitInfo_prev['info']['count'] > 0){
												$debit_prev = $debitInfo_prev['data'][0]['amount_range'];
											}

											if(!$reportCampiagnPrev['is_error'] && $reportCampiagnPrev['info']['count'] > 0){
												$campaign_prev = $reportCampiagnPrev['data'][0];
												$clicks_prev = $campaign_prev['clicks'];
												$imps_prev = $campaign_prev['imps'];
												$cost_prev = $campaign_prev['cost'];

												if( $markup ){
													$gross_cost_prev = $cost_prev * ( 1 + ($markup / 100) ) +$debit_prev;
												}else{
													$gross_cost_prev = $cost_prev + $debit_prev;
												}
											}else{
												$clicks_prev = 0;
												$imps_prev   = 0;
												$cost_prev   = 0;
												$comm_prev   = 0;
												$gross_cost_prev = 0;
											} 	

											$leads = $report->getLeadReport($client,$params_prev);
											$tot_leads_prev = 0;
											$lead_desc_prev = "";
											if(!$leads['is_error'] && $leads['info']['count'] > 0){
												$leads_info = getLeadInfo($leads['data']);
												$tot_leads_prev = $leads_info['lead_total'];
												$lead_desc_prev = $leads_info['lead_desc'];
											}

											$cpl_prev = ($tot_leads_prev > 0) ? $gross_cost_prev/$tot_leads_prev : 0;
											if ($clicks_prev > 0) $convrate_prev = ($tot_leads_prev/$clicks_prev)*100;
											if ($imps_prev > 0) $ctr_prev = ($clicks_prev/$imps_prev)*100;
										}


										$storeidInfo = $clientObj->getClient($client,( $storeid == 0 ) ? null : $storeid );

										$client_name = 'N/A';
										if(!$storeidInfo['is_error'] && $storeidInfo['info']['count'] > 0){
											$client_name = $storeidInfo['data'][0]['name'];
										}

										$cpl = ($tot_leads > 0) ? $gross_cost/$tot_leads : 0;
										if ($clicks > 0) $convrate = ($tot_leads/$clicks)*100;
										if ($imps > 0) $ctr = ($clicks/$imps)*100;

										$up_arrow = '<span class="text-success"><i class="fas fa-arrow-up ml-1"></i></span>';
										$down_arrow = '<span class="text-danger"><i class="fas fa-arrow-down ml-1"></i></span>';

										?>

										<th scope="row"><?php echo $storeid;?></th>						
										<th scope="row"><?php echo $client_name;?></th>						

										<!--impressions-->
										<?php if($compare){ ?>
											<td class="text-center"><?php echo number_format($imps_prev,0);?></td>
										<?php } ?>
										<td class="text-center nowrap"><?php echo number_format($imps,0);?> <?php if($imps == $imps_prev || !$compare) echo ' ' ?><?php elseif($imps > $imps_prev){ echo $up_arrow; ?><?php }else {echo $down_arrow; ?><?php } ?></td>


										<!--clicks-->
										<?php if($compare){ ?>
											<td class="text-center"><?php echo number_format($clicks_prev,0);?></td>
										<?php } ?>
										<td class="min-td text-center nowrap"><?php echo number_format($clicks,0);?> <?php if($clicks == $clicks_prev || !$compare) echo ' ' ?><?php elseif($clicks > $clicks_prev){ echo $up_arrow; ?><?php }else {echo $down_arrow; ?><?php } ?></td>

										<!--total leads-->
										<?php if($compare){ ?>
											<td class="text-center"><?php echo number_format($tot_leads_prev,0); ?> <?php if($tot_leads_prev > 0){?> <span data-toggle="popover" data-container="body" data-html="true" data-content="<?php echo $lead_desc_prev;?>" data-trigger="focus" tabindex="0"><i class="far fa-question-circle cursor-pointer ml-1 text-grey"></i></span> <?php } ?></td>
										<?php } ?>

										<td class="text-center nowrap"><?php echo number_format($tot_leads,0); ?> <?php if($tot_leads == $tot_leads_prev || !$compare) echo ' ' ?><?php elseif($tot_leads > $tot_leads_prev){ echo $up_arrow; ?><?php }else {echo $down_arrow; ?><?php } ?> <?php if($tot_leads > 0){?> <span data-toggle="popover" data-container="body" data-html="true" data-content="<?php echo $lead_desc;?>" data-trigger="focus" tabindex="0"><i class="far fa-question-circle cursor-pointer ml-1 text-grey"></i></span> <?php } ?></td>

										<!--cost-->
										<?php if($compare){ ?>
											<td class="text-center">$<?php echo number_format($gross_cost_prev,2);?></td>
										<?php } ?>
										<td class="min-td text-center nowrap">$<?php echo number_format($gross_cost,2);?> <?php if($gross_cost == $gross_cost_prev || !$compare) echo ' ' ?><?php elseif($gross_cost > $gross_cost_prev){ echo $up_arrow; ?><?php }else {echo $down_arrow; ?><?php } ?></td>

										<!--CPL-->
										<?php if($compare){ ?>
											<td class="text-center">$<?php echo number_format($cpl_prev,2);?></td>
										<?php } ?>
										<td class="text-center nowrap">$<?php echo number_format($cpl,2);?> <?php if($cpl == $cpl_prev || !$compare) echo ' ' ?><?php elseif($cpl > $cpl_prev){ echo $up_arrow; ?><?php }else {echo $down_arrow; ?><?php } ?></td>

										<!--CTR-->
										<?php if($compare){ ?>
											<td class="text-center"><?php echo number_format($ctr_prev,2);?>%</td>
										<?php } ?>
										<td class="min-td text-center nowrap"><?php echo number_format($ctr,2);?> <?php if($ctr == $ctr_prev || !$compare) echo ' ' ?><?php elseif($ctr > $ctr_prev){ echo $up_arrow; ?><?php }else {echo $down_arrow; ?><?php } ?></td>

										<!--conversion rate-->
										<?php if($compare){ ?>
											<td class="text-center"><?php echo number_format($convrate_prev,2);?>%</td>
										<?php } ?>
										<td class="text-center nowrap">
											<?php echo number_format($convrate,2);?>% <?php if($convrate == $convrate_prev || !$compare) echo ' '; elseif($convrate > $convrate_prev){ echo $up_arrow; }else {echo $down_arrow; } ?>
										</td>				
									</tr>
								<?php }  ?>	
							</tbody>
							<tfoot>
								<tr>
									<?php if($compare){ ?>
										<th class="text-center" ></th>
										<th class="text-center" ></th>
										<th class="text-center" ></th>
										<th class="text-center" ></th>
										<th class="text-center" ></th>
										<th class="text-center" ></th>
										<th class="text-center" ></th>
									<?php } ?>
									<th class="text-center" ></th>
									<th class="text-center" ></th>
									<th class="text-center" ></th>
									<th class="text-center" ></th>
									<th class="text-center" ></th>
									<th class="text-center" ></th>
									<th class="text-center" ></th>
									<th class="text-center" ></th>
								</tr>
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
		$(function () {
			$('[data-toggle="popover"]').popover({
				trigger : "focus"
			})
		})
		function formatNumber(num) {
			return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
		}
		$(document).ready( function () {

			var table = $('#campaign_details').DataTable( {
				'responsive': true,
				"pageLength": 50,
				"order": [[ 0, "desc" ]],
				dom: 'B<"clear"><"row"<"col-sm-6"l><"col-sm-6"f>>rt<"row"<"col-sm-6"i><"col-sm-6"p>>',
				buttons: [{
					extend: 'excelHtml5',
					text: 'Export',
					customize: function ( xlsx ){
						var sheet = xlsx.xl.worksheets['sheet1.xml'];
						console.log($('row c[r^="G"]', sheet).attr('s', '0'));
					}
				},
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

					var num_row = 7;
					var compare = false;
					if(getUrlParameter('compare')){
						num_row = 10;
						compare = true;
					}
					var total_lead = 1;
					var total_cost = 0;
					for(x=2;x<num_row;x++){
						// Total over all pages
						total = api
						.column( x )
						.data()
						.reduce( function (a, b) {
							if(b.includes("<")) b = b.substr(0, b.indexOf('<'));
							return intVal(a) + intVal(b);
						}, 0 );

						// Total over this page
						pageTotal = api
						.column( x, { page: 'current'} )
						.data()
						.reduce( function (a, b) {
							if(b.includes("<")) b = b.substr(0, b.indexOf('<')); 
							return intVal(a) + intVal(b);
						}, 0 );

						// Update footer
						var preText = "";
						var str = "";
						if(compare){

							if(x==8 || x==8) 
								str =  "$"+preText+formatNumber(total.toFixed(2));
							else
								str = preText+formatNumber(total.toFixed(0));
						}else{
							if( x == 4){
								total_lead = total.toFixed(2);
							}							

							if( x == 6 ){
								var lead_cost = total_cost / total_lead;
								str =  "$"+preText+formatNumber(lead_cost.toFixed(2));
							}else{
								if( x == 5 ){
									total_cost = total.toFixed(2);
									str =  "$"+preText+formatNumber(total_cost);
								}
								else
									str = preText+formatNumber(total.toFixed(0));
							}
							
						}
						$( api.column( x ).footer() ).html(str);
					}
				}
			} );
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
		
		
		$('select.column_filter_select').on( 'change', function () {
			
			filterColumnSelect( $(this).attr('data-column'), $(this).val() );
		} );

		
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

		$(function() {			

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
				campid = '';
				if (getUrlParameter('campid'))
					campid = '&campid='+getUrlParameter('campid');
				$('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
				window.location.href='?from='+start.format('YYYY-MM-DD')+'&to='+end.format('YYYY-MM-DD')+campid;
			});

			cb(start, end);

		});
	</script>
	<script type='text/javascript' src='/js/compare.js'></script>
</body>
</html>
<?php

function getLocationByRegion(&$db,$region){
	$storeids = $db->rawQuery("SELECT storeid FROM `locationlist`  WHERE region = '".$region."'");

	$str = '';
	foreach ($storeids as $storeid) {
		$str .= $storeid["storeid"].',';
	}
	$str = rtrim($str, ',');

	return $str;
}

function getLeadInfo($leads){

	$lead_desc = "<div class='row font-weight-bold'><div class='col-8'>Campaign Name</div><div class='col-4'>Leads</div></div>";
	foreach($leads as $lead){ 
		$lead_desc .= "<div class='row'>";
		$lead_desc .= "<div class='col-8'>" . $lead['goal_name'] . "</div><div class='col-4'>" . $lead['leads'] . '</div>';
		$lead_desc .= "</div>";
		$tot_leads += $lead['leads'];
	}

	return array('lead_desc'=>$lead_desc,'lead_total' => $tot_leads);
}

?>