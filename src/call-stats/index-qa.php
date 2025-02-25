<!doctype html>
<html lang="en">
  <head>
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" href="/css/styles-campaign-stats.css">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<link href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.css" rel="stylesheet" type="text/css" />
	<link href="//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
	<link href="//cdn.datatables.net/buttons/1.5.6/css/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
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
	</style>

    <title>Calls | Call Stats | Local <? echo CLIENT_NAME; ?></title>
  </head>
  <body class="bg-light cbp-spmenu-push">
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/top.php"); ?>

    <div class="container-fluid">
      <div class="row">
        <?php 
		include ($_SERVER['DOCUMENT_ROOT']."/includes/nav.php"); 
		
		if($_GET['portal']){
			$portal=$_GET['portal'];
			$strcampid= "and campid='".$portal."'";
		}else{
			$strcampid="";
		}

		$from = date("Y-m-d", strtotime("-1 months"));
		$to = date("Y-m-d");

		if (!empty($_GET["analyticsStartDate"]))
			$from = date("Y-m-d", strtotime($db->escape($_GET["analyticsStartDate"])));
		if (!empty($_GET["analyticsEndDate"]))
			$to = date("Y-m-d", strtotime($db->escape($_GET["analyticsEndDate"])));
		  
		$datefilter = "a.start between '".$from." 00:00:00' and '".$to." 23:59:59'";
		?>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 p-0">
			
			<div class="p-0 border-bottom mb-4">
				<div class="d-flex d-block align-items-center clearfix py-2 px-4">
					<h1 class="h2 font-light mb-0 text-center text-sm-left"><i class="fas fa-clock mr-2"></i> Calls</h1>
					<div class="ml-auto">
						<div id="reportrange" class="rounded border bg-white py-2 px-3 cursor-pointer rounded-right-0">
							<i class="far fa-calendar-alt"></i>&nbsp;
							<span></span> <i class="fa fa-caret-down"></i>
						</div>
					</div>
				</div>
			</div>
			<div class="px-4 py-3">	

			
				<?php
				$rct = 1;
				$sql= "select avg(time_to_sec(timediff(end,start))) as timediff,sum(if(duration>29,1,0)) as b,count(*) as c,sum(if(duplicate=1,1,0)) as d from advtrack.calls where client = '".$_SESSION['client']."-".$_SESSION['storeid']."' and start between '".$from." 00:00:00' and '".$to." 23:59:59'".$strcampid." and CASE WHEN campid=20 THEN duration>30 ELSE duration>14 END";
				
				$row2 = $db->rawQueryOne($sql);
				
				if ($db->count > 0){
					if (!$row2['timediff']) $avgtime=0; else $avgtime = $row2['timediff']/60;
					if (!$row2['b']) $callsgreaterthan30s=0; else $callsgreaterthan30s = $row2['b'];
					if (!$row2['c']) $allcalls=0; else $allcalls = $row2['c'];
					if (!$row2['d']) $dupecalls=0; else $dupecalls = $row2['d'];
					//$rct = 0;
				}
				
				$sqlw = "select disposition,count(*) as c from advtrack.calls where client='".$_SESSION['client']."-".$_SESSION['storeid']."' and start between '".$from." 00:00:00' and '".$to." 23:59:59' ".$strcampid." and CASE WHEN campid=20 THEN duration>30 ELSE duration>14 END group by disposition";				

				$roww = $db->rawQuery($sqlw); 
				
				if ($db->count == 1 && !$roww['disposition'])
					$rct = 0;
				if(getActiveCampaigns($_SESSION['storeid'])==0){
					echo "<div class='alert alert-warning mb-4'>You don't currently have any active local digital marketing campaigns. To learn more about promoting your business locally contact <a href='mailto:support@das-group.com'>support@das-group.com</a>.</div>";
				}
				?>
				
				<div class="row mx-1 justify-content-xl-center">
					<div class="col col-lg-2 col-sm-6 col-xl-2 mb-1 text-center hours">
						<div class="day">
							<span class="d-block h4 text-uppercase"><font size="4">TOTAL CALLS</font></span>
							<span class="d-block h2"><b><?=$allcalls?></b></span>
						</div>
					</div>
					<div class="col col-lg-2 col-sm-6 col-xl-2 mb-1 text-center hours">
						<div class="day">
							<span class="d-block h4 text-uppercase"><font size="4">UNIQUE CALLS</font></span>
							<span class="d-block h2"><b><?=$allcalls-$dupecalls?></b></span>
						</div>
					</div>
					<div class="col col-lg-2 col-sm-6 col-xl-2 mb-1 text-center hours">
						<div class="day">
							<span class="d-block h4 text-uppercase"><font size="4">CALLS &gt;= 30 SECONDS</font></span>
							<span class="d-block h2"><b><?=$callsgreaterthan30s?></b></span>
						</div>
					</div>
					<div class="col col-lg-2 col-sm-6 col-xl-2 mb-1 text-center hours">
						<div class="day">
							<span class="d-block h4 text-uppercase"><font size="4">FLAGGED AS DUPLICATE</font></span>
							<span class="d-block h2"><b><?=$dupecalls?></b></span>
						</div>
					</div>
					<div class="col col-lg-2 col-sm-6 col-xl-2 mb-1 text-center hours">
						<div class="day">
							<span class="d-block h4 text-uppercase"><font size="4">AVERAGE DURATION</font></span>
							<span class="d-block h2"><b><?=round($avgtime,2)?> min</b></span>
						</div>
					</div>
				</div>
				
				<? if($rct>0){ ?>
				<div class="row justify-content-center">
					<div class="col-xs-12 col-md-6 col-md-offset-3">
						<div class="box">
							<h2 class="bg-blue">All Calls Chart by Day <i class="fa fa-angle-up pull-right" aria-hidden="true"></i></h2>
							<div>
								<div id="dispositionChart"></div>
							</div>
						</div>
					</div>
				</div>
				<? }else{ ?>
						<div class="alert alert-danger alert-dismissible fade show mx-2 my-2" role="alert">
						  No graph to display as none of the calls where assigned a <strong>"Call Disposition"</strong>.
						</div>
				<? } ?>
					
				<div class="py-3 px-4 d-block d-xl-flex align-items-center">
					<a class="small text-blue d-block d-lg-none" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">Advanced Search</a>
					
					<div class="collapse show" id="collapseExample">
					<form name="portal_form" method="get" class="form-inline">
						<div class="d-flex d-xl-inline-block mb-2 mb-xl-0 mr-2 align-items-center">
							<span class="letter-spacing-1 text-uppercase small mr-2 mr-xl-0">CAMPAIGN:</span>
							    <select name="portal" class="flex-grow d-xl-inline-block form-control form-control-sm w-auto">
								<option value="">All</option>
								<?
								$sql="select name,campid from advtrack.campid_data where client='".$_SESSION['client']."-".$_SESSION['storeid']."' and active='Y' order by name";
								
								$camps_data = $db->rawQuery($sql);
								
								if ($db->count > 0){
									foreach($camps_data as $camps){ ?>
										<option value="<?=$camps['campid']?>" <? if($camps['campid']==$portal) echo "selected" ?>><?if ($camps['name'] == 'None'){ echo 'Organic';}else{ echo $camps['name'];}?></option>
								<?	}
								}
								?>
							</select>
							
						</div>
						<div class="d-flex d-xl-inline-block mb-2 mb-xl-0 mr-2 align-items-center">
							<input type="hidden" name="analyticsStartDate" value="<?=$from?>">
							<input type="hidden" name="analyticsEndDate" value="<?=$to?>">
							<input type="submit" value="Go" class="text-white bg-blue bg-dark-blue-hover flex-grow d-xl-inline-block form-control form-control-sm w-auto border-0">
						</div>
					</form>
					</div>
				</div>
			</div>
			
			<div class="row mx-1">
				<div class="col-xs-12 col-sm-6">
					<div class="box">
						<h2 class="bg-blue">All Calls Chart by Day <i class="fa fa-angle-up pull-right" aria-hidden="true"></i></h2>
						<div>
							<div>
								<div id="chart-1"></div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xs-12 col-sm-6">
					<div class="box">
						<h2 class="bg-blue">All Calls Chart by Hour <i class="fa fa-angle-up pull-right" aria-hidden="true"></i></h2>
						<div>
							<div>
								<div id="chart-2"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<div class="py-3 px-4">
				<?php include $_SERVER['DOCUMENT_ROOT']."/includes/alerts.php"; ?>
	
				<div class="table-responsive">
					<table class="table" id="callsTable">
						<thead class="thead-dark">
							<tr>
								<th data-priority="1">Date/Time</th>
								<th data-priority="2">Campaign</th>
								<th data-priority="2">Caller ID</th>
								<th data-priority="2">Caller #</th>
								<th data-priority="2">Called #</th>
								<th data-priority="3">Duplicate</th>
								<th data-priority="3">Duration</th>
								<th data-priority="2">Caller Location</span></th>
								<th data-priority="3">Quality</th>
								<th data-priority="3">Flagged</th>
								<th data-priority="3">Disposition</th>
								<th data-priority="1">Comments</th>
								<th data-priority="1">Tools</th>
							</tr>
						</thead>
						<tbody>
							<? //$sql= "select a.*,areacodeworld.city,areacodeworld.state from advtrack.calls a left join rates.areacodeworld on npa=left(a.caller,3) and nxx=mid(a.caller,4,3) where a.client='".$_SESSION['client']."-".$_SESSION['storeid']."' and ".$datefilter." and a.duration>29 and a.duplicate<>'1' ".$strcampid." group by date(start)";
							$sql="(select SQL_CALC_FOUND_ROWS duplicate,comment,client,disposition,campid,start,caller,called,cid,vendorid,recorded,ifnull(keywords,'') as keywords,rating,flagged,callid,recordurl,tmlogin,timediff(end,start) as d,(select concat(city,', ',state,' (',zipcode_postalcode,')') as loc from rates.areacodeworld where npa=left(a.caller,3) and nxx=mid(a.caller,4,3) order by zipcode_count desc limit 1) as loc from advtrack.calls a where client='".$_SESSION['client']."-".$_SESSION['storeid']."' ".$strcampid." and start between '".$from." 00:00:00' and '".$to." 23:59:59' and length(caller)=10 and CASE WHEN campid=20 THEN timediff(end,start)>'00:00:30' ELSE timediff(end,start)>'00:00:14' END) union all (select '0' as duplicate,comment,client,disposition,campid,start,caller,called,cid,'' as vendorid,recorded,'' as keywords,rating,flagged,callid,'' as recordurl,null as tmlogin,timediff(end,start) as d,(select concat(city,', ',state,' (',zipcode_postalcode,')') as loc from rates.areacodeworld where npa=left(a.caller,3) and nxx=mid(a.caller,4,3) order by zipcode_count desc limit 1) as loc from vxml.click2callcalls a where client='".$_SESSION['client']."-".$_SESSION['storeid']."' ".$strcampid." and start between '".$from." 00:00:00' and '".$to." 23:59:59' and length(caller)=10 and CASE WHEN campid=20 THEN timediff(end,start)>'00:00:30' ELSE timediff(end,start)>'00:00:14' END) "; 
							 		echo '<pre>'; print_r($sql); echo '</pre>';
exit;
							//echo $sql;
							$calls_stats = $db->rawQuery($sql);
							if ($db->count > 0)
								foreach($calls_stats as $data){  ?>
								
							<tr>
								<td><?=date("m/d/Y g:i:s A", strtotime($data['start']))?></td>
								<td><?=getPortal($_SESSION['client']."-".$_SESSION['storeid'], $data['campid'])?></td>
								<td><?=$data['cid']?></td>
								<td><?=format_phone($data['caller'])?></td>
								<td><?=format_phone($data['called'])?></td>
								<td><? if($data['duplicate']==0) echo "No"; else echo "Yes";?></td>
								<td><?=$data['d']?></td>
								<td><?=$data['loc']?></td>
								<td><? $quality = $data['rating']>0 ? $data['rating'] : "None"; echo $quality; ?></td>
								<td><? $flagged = isset($data['flagged']) ? "Yes" : "No";  echo $flagged; ?></td>
								<td><? $disposition = isset($data['disposition']) ? $data['disposition'] : "None"; echo $disposition;?></td>
								<td><? if(!$data['comment']) echo "None"; else echo $data['comment'];?></td>
								<td><button type="button" class="btn btn-primary callBtn" data-callid="<?=$data['callid']?>" data-vendorid="<?=$data['vendorid']?>">Listen</button></td>
							</tr>
							<? } ?>
						</tbody>
					</table>
				</div>
			</div>
			
			<form action="xt_call.php?<?php echo $_SERVER["QUERY_STRING"];?>" method="POST">
				<div class="modal fade" id="callModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalTitle" aria-hidden="true">
				  <div class="modal-dialog modal-dialog-centered" role="document">
					<div class="modal-content">
					  <div class="modal-header">
						<h5 class="modal-title" id="uploadModalTitle">Call Details</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						  <span aria-hidden="true">&times;</span>
						</button>
					  </div>
					  <div class="modal-body">
					  </div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
							<input type="submit" class="btn bg-blue text-white btn-sm" value="Save changes">
						</div>

					</div>
				  </div>
				</div>
			</form>
				
        </main>
      </div>
    </div>

    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>
	<script type="text/javascript" src="//static.fusioncharts.com/code/latest/fusioncharts.js"></script>
	    <?
		//$sql="select disposition as label,count(*) as value from advtrack.calls where client like '".$_SESSION['client']."%' and start between '".$from." 00:00:00' and '".$to." 23:59:59' group by disposition";
		$arrData = array(
			"chart" => array(
				"caption" => "All Calls Chart",
				"subCaption"=>"By Day of Week",
				"showValues"=> "0",
				"numberSuffix"=> " calls",
				"xAxisname"=>"Day",
		        "yAxisName"=>"Calls",
			),
		);

		$arrData["data"] = array();

		$tracker = [];
		$sql="select hour,sum(c) as c from ((select dayofweek(a.start) as hour,count(*) as c from vxml.click2callcalls a where client='".$_SESSION['client']."-".$_SESSION['storeid']."' and campid='".$portal."' and ".$datefilter." group by hour) union (select dayofweek(a.start) as hour,count(*) as c from advtrack.calls a where client='".$_SESSION['client']."-".$_SESSION['storeid']."' ".$strcampid." and ".$datefilter."  and CASE WHEN campid=20 THEN duration>30 ELSE duration>14 END group by hour)) as b group by hour";
		
		$result = $db->rawQuery($sql);
		
		if ($db->count > 0){
			foreach($result as $calls ){
				array_push($arrData["data"], array(
					"label" => $daysOfWeek[$calls["hour"]],
					"value" => $calls["c"],
					)
				);
				array_push($tracker,$calls["hour"]);
			}

			for($i=1;$i<=7;$i++){
				if(array_search($i, $tracker)===false){
					$insert=array ($i => array("label" => $daysOfWeek[$i],"value" => 0));
					array_insert($arrData["data"], $i-1, $insert);
				}
			}
			
		}

		$jsonEncodedData = json_encode($arrData);
			
		include ($_SERVER['DOCUMENT_ROOT'].'/includes/fusioncharts.php');
		$columnChart = new FusionCharts("column2d", "ex1", "100%", 400, "chart-1", "json", $jsonEncodedData);
		$columnChart->render();
		
		$arrData = array(
				"chart" => array(
					"caption" => "All Calls Chart",
					"subCaption"=>"By Hour of Day",
					"showValues"=> "0",
					"numberSuffix"=> " calls",
					"xAxisname"=>"Hour of Day (Local to Caller)",
					"yAxisName"=>"Calls"
				),
			);

		$arrData["data"] = array();

		$tracker=[];
		$sql="select hour,sum(c) as c from ((select if(b.dst='1',hour(date_add(a.start, interval b.offset+0 hour)),hour(date_add(a.start, interval b.offset+5 hour))) as hour,count(*) as c from vxml.click2callcalls a left join rates.nxx2tz b on left(a.caller,6)=b.area where a.client='".$_SESSION['client']."-".$_SESSION['storeid']."' and CASE WHEN campid=20 THEN timediff(end,start)>'00:00:30' ELSE timediff(end,start)>'00:00:14' END and a.campid='".$portal."' and ".$datefilter. " group by hour) union (select if(b.dst=1,hour(date_add(a.start, interval b.offset+5 hour)),hour(date_add(a.start, interval b.offset+5 hour))) as hour,count(*) as c from advtrack.calls a left join rates.nxx2tz b on left(a.caller,6)=b.area where a.client='".$_SESSION['client']."-".$_SESSION['storeid']."' and CASE WHEN campid=20 THEN timediff(end,start)>'00:00:30' ELSE timediff(end,start)>'00:00:14' END ".$strcampid." and ".$datefilter. " group by hour)) as b group by hour having hour is not null";
		//echo $sql;
		
		$result = $db->rawQuery($sql);
		if ($db->count > 0){
			foreach($result as $calls){
				array_push($arrData["data"], array(
					"label" => $calls["hour"],
					"value" => $calls["c"],
					)
				);
				array_push($tracker,$calls["hour"]);
			}
			for($i=0;$i<=23;$i++){
				if(array_search($i, $tracker)===false){
					$insert=array ($i => array("label" => (string)$i,"value" => 0));
					array_insert($arrData["data"], $i, $insert);
				}
			}
		}
		$jsonEncodedData = json_encode($arrData);
		
		$columnChart = new FusionCharts("column2d", "ex2", "100%", 400, "chart-2", "json", $jsonEncodedData);
		$columnChart->render();
		
		$arrData = array(
			"chart" => array(
				"caption" => "Call Disposition Breakout Of Calls Reviewed",
				"showValues"=> "1",
			),
		);

		$arrData["data"] = array();

		$tracker=[];
		
		$result = $db->rawQuery($sqlw);
		if ($db->count > 0){
			foreach($result as $disp){
				if(isset($disp["disposition"])){
					array_push($arrData["data"], array(
						"label" => $disp["disposition"],
						"value" => $disp["c"],
						)
					);
				}
			}
		}
		$jsonEncodedData = json_encode($arrData);
		
		$columnChart = new FusionCharts("pie2d", "ex5", "100%", 400, "dispositionChart", "json", $jsonEncodedData);
		$columnChart->render();
	
	 ?>

<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
	  <script type="text/javascript" src="//cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
	  <script type="text/javascript" src="//cdn.datatables.net/plug-ins/1.10.19/sorting/datetime-moment.js"></script>

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
		$(document).ready(function(){
			var dateFormat = "mm/dd/yyyy h:mm:ss A",
			  from = $( "input[name='analyticsStartDate']" )
				.datepicker({
				  defaultDate: "+1w",
				  changeMonth: true,
				  numberOfMonths: 3,
				  dateFormat: 'yy-mm-dd'
				})
				.on( "change", function() {
				  to.datepicker( "option", "minDate", getDate( this ) );
				}),
			  to = $( "input[name='analyticsEndDate']" ).datepicker({
				defaultDate: "+1w",
				changeMonth: true,
				numberOfMonths: 3,
				  dateFormat: 'yy-mm-dd'
			  })
			  .on( "change", function() {
				from.datepicker( "option", "maxDate", getDate( this ) );
			  });
		 
			function getDate( element ) {
			  var date;
			  try {
				date = $.datepicker.parseDate( dateFormat, element.value );
			  } catch( error ) {
				date = null;
			  }
		 
			  return date;
			}
		
		$.fn.dataTable.moment('dddd, MMMM D, YYYY');
		var table = $('#callsTable').DataTable( {
	        responsive: true,
            "pageLength": 25,
			"order": [[ 0, "desc" ]],
	        dom: 'B<"clear"><"row"<"col-sm-6"l><"col-sm-6"f>>rt<"row"<"col-sm-6"i><"col-sm-6"p>>',
	        buttons: [
					{ extend: 'excel',text: 'Export'},
					'print'
				],
	    } );
	 
	    table.buttons().container()
	        .appendTo( '#callsTable_wrapper .col-md-6:eq(0)' );
		
		});

		$(document).on('click','.callBtn',function(){
			var callid = $(this).data("callid");
			var vendorid = $(this).data("vendorid");

			$.ajax({
				url: "get_call.php", 
				type:"POST",
				data:{"callid":callid,"vendorid":vendorid},
				success: function(result){
					$("#callModal .modal-body").html(result);
					$('#callModal').modal('show'); 
				}
			});
			
		});
		
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
			if(getUrlParameter('analyticsStartDate') && getUrlParameter('analyticsEndDate')){
				var start = moment(getUrlParameter('analyticsStartDate'));
				var end = moment(getUrlParameter('analyticsEndDate'));
				//$('#reportrange span').html(getUrlParameter('from').format('MMMM D, YYYY') + ' - ' + getUrlParameter('to').format('MMMM D, YYYY'));
			}else{
				var start = moment().subtract(29, 'days');
				var end = moment();
			}
			
			if(getUrlParameter('portal')){
				$portal = '&portal='+getUrlParameter('portal');
			}else{
				$portal = '';
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
				$('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
				window.location.href='?analyticsStartDate='+start.format('YYYY-MM-DD')+'&analyticsEndDate='+end.format('YYYY-MM-DD')+$portal;
			});

			cb(start, end);
	
		});
	</script>
  </body>
</html>