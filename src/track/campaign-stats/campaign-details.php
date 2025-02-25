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
	set_time_limit(300);

    include ($_SERVER['DOCUMENT_ROOT']."/includes/head.php"); 

	  ?>
   	<style>
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
	.none_upload{ display:none;text-align:center;}
    .loader {
          position: fixed;
          left: 0px;
          top: 0px;
          width: 100%;
          height: 100%;
          z-index: 9999;
          background: url('/../../yextAPI/spinner_preloader.gif') 50% 50% no-repeat rgba(255, 255, 255, 0.3);
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
		<div id="spinner_loading" class="none_upload loader"></div>
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
		
			
			<div class="py-3 px-4">
				<?php include $_SERVER['DOCUMENT_ROOT']."/includes/alerts.php"; 
				date_default_timezone_set('America/New_York');
					require ($_SERVER['DOCUMENT_ROOT']."/includes/DasApiSDK/vendor/autoload.php");
					use Das\Report;
					use Das\CampId;

					$from = strtotime("-1 months");
					$to = strtotime('now');

					if (isset($_GET['client'])){
						$client = $_GET['client'];
					}else{
						$client = $_SESSION['client'];
					}

					$compare = false;

					if (isset($_GET["from"])){
						$from = strtotime( $db->escape($_GET["from"]) );
					}
					if (isset($_GET["to"])){
						$to = strtotime($db->escape($_GET["to"]));
					}

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
				
				<!-- Data -->
				<div class="table-responsive">
					<table class="table" id="campaign_details">
						<thead>
							<?php if($compare){ ?>
							<tr class="text-white" style="background-color:#343a40;">
								<th>Campaign</th>								
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
								<th>Campaign</th>								
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
								
					<?php 

					
					$campIdObj = new CampId($token_api);
					$campids = $campIdObj->getCampIdByStoreId($client ,$_SESSION['storeid'])['data'];

					$diff = abs( $to - $from );
					$days = floor( $diff / (60*60*24) );

					if($compare){
						$diff = abs( $to_compare - $from_compare );
						$days_prev = floor( $diff / (60*60*24) );
					}

					$report = new Report($token_api);
					
					$params  = array(
										'gte' => (string)$from, 
										'lte' => (string)$to,
										'storeid'=> $_SESSION['storeid']
									);
					
				    foreach ($campids as $campid) {
				
				    	$params['campid']= $campid['campid'];
				    	$region_url='';
				        $reportCampiagn = $report->getCampaignReport($client,$params);
				        
					    $value = array();		        

					    if( !$reportCampiagn['is_error'] ){
					    	$value =  $reportCampiagn['data'][0]; 
					    }else{					    	
					    	if($campid['campid'] != 0){
					    		continue;
					    	}					    	
					    }

				    	$markup = 1;
				    	
						$clicks = 0;
						$imps = 0;
						$cost = (isset($value['cost'])) ? $value['cost'] : 0;
						$gross_cost = 0;
						$campid_name = '';

						$tot_leads = 0;
						$lead_desc = "";
						$cpl = 0;
						$convrate = 0;
						$ctr = 0;

			    		$markup = 1 + ( $campid['markup'] / 100 );
			    		
			    		$campid_name = $campid['name'];	 
						
				    	$leads = $report->getLeadReport($client,$params);
				    	
				    	if($leads['info']['count'] <= 0 && $reportCampiagn['info']['count'] <= 0 ){
				    		continue;
				    	}

				    	if(!$leads['is_error'] ){
				    		$leads_info = getLeadInfo($leads['data']);
				    		$tot_leads = $leads_info['lead_total'];
				    		$lead_desc = $leads_info['lead_desc'];
				    	}
				    	$debitParams = $params;
				    	$debitParams['campid'] = $campid['campid'];
				    	$debit = 0;

				    	$debitInfo= $campIdObj->getDebits($client,$_SESSION['storeid'],$debitParams);    	   
				    	
			    	    if( $debitInfo['info']['count'] > 0){
			    	    	$debit = $debitInfo['data'][0]['amount_range'];
			    	    }

				    	$gross_cost = ($value['cost'] * $markup) + $debit;
				    	$imps = $value['imps'];
				    	$clicks = $value['clicks'];

			    	    if($compare){
			    	    	$clicks_prev = 0;
							$imps_prev = 0;
							$cost_prev = 0;
							$gross_cost_prev = 0;
							$tot_leads_prev = 0;
							$cpl_prev = 0;
							$convrate_prev = 0;
							$ctr_prev = 0;
			    	    	$params_compare  = array(
									'gte' => (string)$from_compare,
									'lte' => (string)$to_compare,
									'campid' => $campid['campid'],
									'storeid'=> $_SESSION['storeid']
								);

			    	    	$debit_prev = 0;

					    	$debitInfo_prev= $campIdObj->getDebits($client,null,$params_compare);

				    	    if( $debitInfo_prev['info']['count'] > 0){
				    	    	$debit_prev = $debitInfo_prev['data'][0]['amount_range'];
				    	    }

			    			$reportCampiagnCompare = $report->getCampaignReport($client,$params_compare);
			    			$data = array();
						    if( !$reportCampiagnCompare['is_error'] && $reportCampiagnCompare['info']['count'] > 0){
						    	$dataCompare =  $reportCampiagnCompare['data'][0]; 
						    	$clicks_prev = $dataCompare['clicks'];
								$imps_prev = $dataCompare['imps'];
								$gross_cost_prev = $dataCompare['cost'] * $markup + $debit_prev;
						    }

					    	$leads_compare = $report->getLeadReport($client,$params_compare);
					    	if(!$leads_compare['is_error'] && $leads_compare['info']['count'] > 0){
					    		$leads_info_compare = getLeadInfo($leads_compare['data']);
					    		$tot_leads_prev = $leads_info_compare['lead_total'];
				    		    $lead_desc_prev = $leads_info_compare['lead_desc'];
					    	}

					    	$cpl_prev = ($tot_leads_prev > 0) ? $gross_cost_prev/$tot_leads_prev : 0;
							if ($clicks_prev > 0) $convrate_prev = ($tot_leads_prev/$clicks_prev)*100;
							if ($imps_prev > 0) $ctr_prev = ($clicks_prev/$imps_prev)*100;
			    	    }


						$cpl = ($tot_leads > 0) ? $gross_cost/$tot_leads : 0;
						if ($clicks > 0) $convrate = ($tot_leads/$clicks)*100;
						if ($imps > 0) $ctr = ($clicks/$imps)*100;


						$up_arrow = '<span class="text-success"><i class="fas fa-arrow-up ml-1"></i></span>';
						$down_arrow = '<span class="text-danger"><i class="fas fa-arrow-down ml-1"></i></span>';

						?>
						<tr>
						<td scope="row"><strong><?php echo $campid_name.'['.$campid['campid'].']';?></strong></td>
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
						<td class="min-td text-center nowrap"><?php echo number_format($ctr,2);?>% <?php if($ctr == $ctr_prev || !$compare) echo ' ' ?><?php elseif($ctr > $ctr_prev){ echo $up_arrow; ?><?php }else {echo $down_arrow; ?><?php } ?></td>

						<!--conversion rate-->
						<?php if($compare){ ?>
							<td class="text-center"><?php echo number_format($convrate_prev,2);?>%</td>
						<?php } ?>
						<td class="text-center"><span class="nowrap">
							<?php echo number_format($convrate,2);?>% <?php if($convrate == $convrate_prev || !$compare) echo ' '; elseif($convrate > $convrate_prev){ echo $up_arrow; }else {echo $down_arrow; } ?></span></td>

						<!--active status-->						

					</tr>
						<?php
							$cl_start = 1 ;
							$cl_end = 5 ;	
							}

						?>
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
				<!-- End Data -->

				
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

		function formatNumber(num) {
		  return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
		}
	
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

			var table = $('#campaign_details').DataTable( {
				'responsive': true,
				"pageLength": 50,
				"order": [[ 0, "desc" ]],
				dom: '<"row"<"col-sm-6"l><"col-sm-6 text-right"Bf>>rt<"row"<"col-sm-6"i><"col-sm-6"p>>',
				buttons: [
						{ extend: 'excel',text: 'Export'},
						//'print'
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
					var num_row = 6;
					var compare = false;
					if(getUrlParameter('compare')){
						num_row = 11;
						compare = true;
					}
					var totalCost, totalLeads,pageTotalLeads,pageTotalCost;
					var totalCostPrev, totalLeadsPrev,pageTotalLeadsPrev,pageTotalCostPrev;
					for(x=1;x<num_row;x++){
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
						//set cpl
						if(compare==false){
							if (x==3){
								pageTotalLeads = pageTotal;
								totalLeads = total;
							}
							if (x==4){
								pageTotalCost = pageTotal;
								totalCost = total;
							}
							if(x==5){
								total = totalCost / totalLeads;
								pageTotal = pageTotalCost / pageTotalLeads;
							}
						}else{
							if (x==6){
								pageTotalLeads = pageTotal;
								totalLeads = total;
							}
							if (x==5){
								pageTotalLeadsPrev = pageTotal;
								totalLeadsPrev = total;
							}
							if (x==8){
								pageTotalCost = pageTotal;
								totalCost = total;
							}
							if (x==7){
								pageTotalCostPrev = pageTotal;
								totalCostPrev = total;
							}
							if(x==10){
								total = totalCost / totalLeads;
								pageTotal = pageTotalCost / pageTotalLeads;
							}
							if(x==9){
								total = totalCostPrev / totalLeadsPrev;
								pageTotal = pageTotalCostPrev / pageTotalLeadsPrev;
							}
						}
						
						// Update footer
						var preText = "";
						var str = "";
						if(compare){
							var moneyCols = [7,8,9,10]
							if(jQuery.inArray(x, moneyCols) !== -1) 
								str =  "$"+preText+formatNumber(total.toFixed(2));
							else
								str = preText+formatNumber(total.toFixed(0));
						}else{
							if(x==4 || x==5) 
								str =  "$"+preText+formatNumber(total.toFixed(2));
							else
								str = preText+formatNumber(total.toFixed(0));
						}
						
						$( api.column( x ).footer() ).html(str);
					}
				}
			} );
			$('[data-toggle="popover"]').popover({
			  trigger : "focus"
		    })

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

			$('#region_filter').on('change', function() {
				$('#spinner_loading').removeClass("none_upload");
				if(typeof start != 'undefined' && typeof end != 'undefined'){
					window.location.href='?from='+start.format('YYYY-MM-DD')+'&to='+end.format('YYYY-MM-DD')+'&region='+this.value;	
				}else{
					window.location.href='?region'+this.value;
				}			  		
			});

			
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

function getLocation(&$db){
	$storeids = $db->rawQuery("SELECT storeid FROM `locationlist`  WHERE adfundmember = 'Y'");
	$str = '';
	foreach ($storeids as $storeid) {
		$str .= $storeid["storeid"].',';
	}

	$str = rtrim($str, ',');
	return $str;
}

function getRegion(&$db){
	return $db->rawQuery('SELECT DISTINCT region FROM `locationlist`');
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