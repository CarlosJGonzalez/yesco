<!doctype html>
<html lang="en">
  <head>
	<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
	<link href="//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
	<link href="//cdn.datatables.net/buttons/1.5.6/css/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/head.php"); ?>

    <title>Calls Stats | Local <?php echo CLIENT_NAME; ?></title>
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

		if (!empty($_GET["from"]))
			$from = date("Y-m-d", strtotime($db->escape($_GET["from"])));
		if (!empty($_GET["to"]))
			$to = date("Y-m-d", strtotime($db->escape($_GET["to"])));
		  
		$datefilter = "a.start between '".$from." 00:00:00' and '".$to." 23:59:59'";
		
		$client_xt="client like '".$_SESSION['client']."%'";
		?>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 p-0">
			
			<div class="p-0 border-bottom mb-4">
				<div class="border-bottom-dotted d-flex d-block align-items-center clearfix py-2 px-4">
					<h1 class="h2 font-light mb-0 text-center text-sm-left"><i class="fas fa-phone mr-2"></i> Calls</h1>
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
						<form name="portal_form" method="get" class="form-inline">
							<div class="d-flex d-xl-inline-block mb-2 mb-xl-0 mr-2 align-items-center">
								<span class="letter-spacing-1 text-uppercase small mr-2 mr-xl-0">CAMPAIGN:</span>
									<select name="portal" class="flex-grow d-xl-inline-block form-control form-control-sm w-auto">
									<option value="">All</option>
									<?php
									$sql = "SELECT name,campid from advtrack.campid WHERE ".$client_xt." AND type='C' GROUP BY name,campid ORDER BY name ";
									$camps_data = $db->rawQuery($sql);
									if ($db->count > 0){
										foreach($camps_data as $camps){ ?>
											<option value="<?=$camps['campid']?>" <? if($camps['campid']==$portal) echo "selected" ?>><?=$camps['name']?></option>
									<?php	}
									}
									?>
								</select>
							</div>
							<div class="d-flex d-xl-inline-block mb-2 mb-xl-0 mr-2 align-items-center">
								<input type="hidden" name="from" value="<?=$from?>">
								<input type="hidden" name="to" value="<?=$to?>">
								<input type="submit" value="Go" class="text-white bg-blue bg-dark-blue-hover flex-grow d-xl-inline-block form-control form-control-sm w-auto border-0">
							</div>
						</form>
					</div>
				</div>
			</div>

			<?php
			$rct = 1;
			//$sql= "select avg(time_to_sec(timediff(end,start))) as timediff,sum(if(duration>29,1,0)) as b,count(*) as c,sum(if(duplicate=1,1,0)) as d from advtrack.calls where ".$client_xt." ".$strcampid." and start between '".$from." 00:00:00' and '".$to." 23:59:59' and length(caller)=10 and duplicate <> 1 and duration >=30";
			$sql= "select avg(time_to_sec(timediff(end,start))) as timediff,sum(if(duration>29,1,0)) as b,count(*) as c,sum(if(duplicate=1,1,0)) as d from advtrack.calls where ".$client_xt." ".$strcampid." and start between '".$from." 00:00:00' and '".$to." 23:59:59' and CASE WHEN campid=20 THEN duration>30 ELSE duration>14 END";
				
			$row2 = $db->rawQueryOne($sql);
			
			if ($db->count > 0){
				if (!$row2['timediff']) $avgtime=0; else $avgtime = $row2['timediff']/60;
				if (!$row2['b']) $callsgreaterthan30s=0; else $callsgreaterthan30s = $row2['b'];
				if (!$row2['c']) $allcalls=0; else $allcalls = $row2['c'];
				if (!$row2['d']) $dupecalls=0; else $dupecalls = $row2['d'];
				//$rct = 0;
			}
			
			$sqlw = "select disposition,count(*) as c from advtrack.calls where ".$client_xt." ".$strcampid." and start between '".$from." 00:00:00' and '".$to." 23:59:59' and CASE WHEN campid=20 THEN duration>30 ELSE duration>14 END group by disposition";
			$roww = $db->rawQuery($sqlw); 
			
			if ($db->count == 1 && !$roww['disposition'])
				$rct = 0;
			?>

			<div class="py-3 px-4">
				<?php include $_SERVER['DOCUMENT_ROOT']."/includes/alerts.php"; ?>
				<div class="row justify-content-center mb-4">
					<div class="col col-lg-2 col-sm-6 col-xl-2 mb-1 text-center">
						<div class="h-100 bg-blue text-white py-4 px-2 rounded">
							<span class="d-block h5 text-uppercase">TOTAL CALLS</span>
							<span class="d-block h2 font-weight-bold"><?=$allcalls?></span>
						</div>
					</div>
					<div class="col col-lg-2 col-sm-6 col-xl-2 mb-1 text-center">
						<div class="h-100 bg-blue text-white py-4 px-2 rounded">
							<span class="d-block h5 text-uppercase">UNIQUE CALLS</span>
							<span class="d-block h2 font-weight-bold"><?=$allcalls-$dupecalls?></span>
						</div>
					</div>
					<div class="col col-lg-2 col-sm-6 col-xl-2 mb-1 text-center">
						<div class="h-100 bg-blue text-white py-4 px-2 rounded">
							<span class="d-block h5 text-uppercase">CALLS &gt;= 30 SECONDS</span>
							<span class="d-block h2 font-weight-bold"><?=$callsgreaterthan30s?></span>
						</div>
					</div>
					<div class="col col-lg-2 col-sm-6 col-xl-2 mb-1 text-center">
						<div class="h-100 bg-blue text-white py-4 px-2 rounded">
							<span class="d-block h5 text-uppercase">FLAGGED AS DUPLICATE</span>
							<span class="d-block h2 font-weight-bold"><?=$dupecalls?></span>
						</div>
					</div>
					<div class="col col-lg-2 col-sm-6 col-xl-2 mb-1 text-center">
						<div class="h-100 bg-blue text-white py-4 px-2 rounded">
							<span class="d-block h5 text-uppercase">AVERAGE DURATION</span>
							<span class="d-block h2 font-weight-bold"><?=round($avgtime,2)?> min</span>
						</div>
					</div>
				</div>
				
				<div class="row mb-4">
					<div class="col-sm-6">
						<div class="box p-2">
							<h2 class="text-uppercase h4 text-dark d-flex flex-wrap">Calls by&nbsp;<span class="text-blue">Day</span></h2>						
							<canvas id="chart-1" width="400" height="200"></canvas>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="box p-2">
							<h2 class="text-uppercase h4 text-dark d-flex flex-wrap">Calls by&nbsp;<span class="text-blue">Hour</span></h2>						
							<canvas id="chart-2" width="400" height="200"></canvas>
						</div>
					</div>
				</div>
				<?php 
				//$sql="select SQL_CALC_FOUND_ROWS duplicate,comment,client,disposition,campid,start,caller,called,cid,vendorid,recorded,ifnull(keywords,'') as keywords,rating,flagged,callid,recordurl,tmlogin,timediff(end,start) as d,(select concat(city,', ',state,' (',zipcode_postalcode,')') as loc from rates.areacodeworld where npa=left(a.caller,3) and nxx=mid(a.caller,4,3) order by zipcode_count desc limit 1) as loc from advtrack.calls a where ".$client_xt." ".$strcampid." and start between '".$from." 00:00:00' and '".$to." 23:59:59' and length(caller)=10 and duplicate <> 1 and duration >=30"; 
				$sql="(select SQL_CALC_FOUND_ROWS duplicate,comment,client,disposition,campid,start,caller,called,cid,vendorid,recorded,ifnull(keywords,'') as keywords,rating,flagged,callid,recordurl,tmlogin,timediff(end,start) as d,(select concat(city,', ',state,' (',zipcode_postalcode,')') as loc from rates.areacodeworld where npa=left(a.caller,3) and nxx=mid(a.caller,4,3) order by zipcode_count desc limit 1) as loc from advtrack.calls a where ".$client_xt." ".$strcampid." and start between '".$from." 00:00:00' and '".$to." 23:59:59' and length(caller)=10 and CASE WHEN campid=20 THEN timediff(end,start)>'00:00:30' ELSE timediff(end,start)>'00:00:14' END) union all (select '0' as duplicate,comment,client,disposition,campid,start,caller,called,cid,'' as vendorid,recorded,'' as keywords,rating,flagged,callid,'' as recordurl,null as tmlogin,timediff(end,start) as d,(select concat(city,', ',state,' (',zipcode_postalcode,')') as loc from rates.areacodeworld where npa=left(a.caller,3) and nxx=mid(a.caller,4,3) order by zipcode_count desc limit 1) as loc from vxml.click2callcalls a where ".$client_xt." ".$strcampid." and start between '".$from." 00:00:00' and '".$to." 23:59:59' and length(caller)=10 and CASE WHEN campid=20 THEN timediff(end,start)>'00:00:30' ELSE timediff(end,start)>'00:00:14' END) ";
				?>
				<div class="table-responsive">
					<table class="table" id="calls_leadTable">
						<thead class="thead-dark">
							<tr>
								<th>Date/Time</th>
								<th>Client Name</th>
								<th># Client</th>
								<th>Portal</th>
								<th>Caller ID</th>
								<th>Caller #</th>
								<th>Called #</th>
								<th>Duration</th>
								<th>Caller Location</th>
								<th>Quality</th>
								<th>Flagged</th>
								<th>Disposition</th>
								<th>Comments</th>
								<th>Tools</th>
							</tr>
						</thead>
						<tbody>
						<?php 
						$calls_stats = $db->rawQuery($sql);
		
						if ($db->count > 0)
						
						foreach($calls_stats as $data){
							$clien_storeid = $_SESSION['client']."-".$_SESSION['storeid'];
							
							$clien_storeid = $data['client'];
                            $info_tmp = explode("-", $data['client']);
                            $name_client = $client;

                            if (count($info_tmp) == 2) {
                                $sql_loc = "SELECT * FROM ".$_SESSION['database'].".locationlist WHERE storeid = '".$info_tmp[1]."'";
								
								$result_name = $db->rawQueryOne($sql_loc);
								if ($db->count > 0) {
								   $name_client = $result_name['companyname'];
								}
                            }
							?>
							<tr>
								<td><?=date("m/d/Y g:i:s A", strtotime($data['start']))?></td>
								<td><?=$name_client?></td>
								<td><?=$data['client']?></td>
								<td><?=getPortal($clien_storeid, $data['campid'])?></td>
								<td><?=$data['callid']?></td>
								<td><?=format_phone($data['caller'])?></td>
								<td><?=format_phone($data['called'])?></td>
							    <!--  <td><? if($data['duplicate']==0) echo "No"; else echo "Yes";?></td>-->
								<td><?=$data['d']?></td>
								<td><?=$data['loc']?></td>
								<td><? $quality = $data['rating']>0 ? $data['rating'] : "None"; echo $quality; ?></td>
								<td><? $flagged = isset($data['flagged']) ? "Yes" : "No";  echo $flagged; ?></td>
								<td><? $disposition = isset($data['disposition']) ? $data['disposition'] : "None"; echo $disposition;?></td>
								<td><? if(!$data['comment']) echo "None"; else echo $data['comment'];?></td>
								<td><button type="button" class="btn btn-primary callBtn bg-blue border-0 btn-sm" data-callid="<?=$data['callid']?>" data-vendorid="<?=$data['vendorid']?>">Listen</button></td>
							</tr>
						<?php } ?>
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
	
	<?php
	##################### Days ##########################
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
	
	$sql = "select dayofweek,sum(c) as c from ((select dayofweek(a.start) as dayofweek,count(*) as c from vxml.click2callcalls a where ".$client_xt." ".$strcampid." and ".$datefilter." group by dayofweek) union (select dayofweek(a.start) as dayofweek,count(*) as c from advtrack.calls a where ".$client_xt." ".$strcampid." and ".$datefilter."  and CASE WHEN campid=20 THEN duration>30 ELSE duration>14 END group by dayofweek)) as b group by dayofweek";
	$result = $db->rawQuery($sql);
	
	$daysOfWeek = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
	
	if ($db->count > 0){
		foreach($result as $calls ){
			array_push($arrData["data"], array(
				"label" => $daysOfWeek[$calls["dayofweek"]-1],
				"value" => $calls["c"],
				)
			);
			array_push($tracker,$calls["dayofweek"]);
		}

		for($i=1;$i<=7;$i++){
			if(array_search($i, $tracker)===false){
				$insert=array ($i => array("label" => $daysOfWeek[$i-1],"value" => 0));
				array_insert($arrData["data"], $i, $insert);
			}
		}
	}
	
	##################### Hours ##########################
	$arrDataHours = array(
				"chart" => array(
					"caption" => "All Calls Chart",
					"subCaption"=>"By Hour of Day",
					"showValues"=> "0",
					"numberSuffix"=> " calls",
					"xAxisname"=>"Hour of Day (Local to Caller)",
					"yAxisName"=>"Calls"
				),
			);

	$arrDataHours["data"] = array();
	$trackerHours = [];
	
	$sqlHours = "select hour,sum(c) as c from (select hour(start) as hour,count(*) as c from advtrack.calls a left join rates.nxx2tz b on left(a.caller,6)=b.area where  ".$client_xt." and CASE WHEN campid=20 THEN timediff(end,start)>'00:00:30' ELSE timediff(end,start)>'00:00:14' END ".$strcampid." and ".$datefilter. " group by hour) as b group by hour having hour is not null";
	$result = $db->rawQuery($sqlHours);
	
	if ($db->count > 0){
		foreach($result as $calls){
			array_push($arrDataHours["data"], array(
				"label" => date("g:i a",strtotime($calls["hour"].":00")),
				"value" => $calls["c"],
				)
			);
			array_push($trackerHours,$calls["hour"]);
		}
		for($i=0;$i<=23;$i++){
			if(array_search($i, $trackerHours)===false){
				$insert=array ($i => array("label" => date("g:i a",strtotime($i.":00")),"value" => 0));
				array_insert($arrDataHours["data"], $i, $insert);
			}
		}
	}
	?>
	<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
	<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
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
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js"></script>
	<script src="//cdn.datatables.net/plug-ins/1.10.20/sorting/datetime-moment.js"></script>
	<script>
	//Chart1
	var ctx = document.getElementById("chart-1");
	var myChart = new Chart(ctx, {
	  type: 'bar',
	  data: {
		labels: ['<?php echo implode("','",array_column($arrData['data'],"label"));?>'],
		datasets: [{
		  label: '# of Calls',
		  data: [<?php echo implode(",",array_column($arrData['data'],"value"));?>],
		  backgroundColor: [
			'rgba(255, 99, 132, 0.2)',
			'rgba(54, 162, 235, 0.2)',
			'rgba(255, 206, 86, 0.2)',
			'rgba(75, 192, 192, 0.2)',
			'rgba(153, 102, 255, 0.2)',
			'rgba(255, 159, 64, 0.2)',
			'rgba(255, 99, 132, 0.2)',
			'rgba(54, 162, 235, 0.2)',
			'rgba(255, 206, 86, 0.2)',
			'rgba(75, 192, 192, 0.2)',
			'rgba(153, 102, 255, 0.2)',
			'rgba(255, 159, 64, 0.2)'
		  ],
		  borderColor: [
			'rgba(255,99,132,1)',
			'rgba(54, 162, 235, 1)',
			'rgba(255, 206, 86, 1)',
			'rgba(75, 192, 192, 1)',
			'rgba(153, 102, 255, 1)',
			'rgba(255, 159, 64, 1)',
			'rgba(255,99,132,1)',
			'rgba(54, 162, 235, 1)',
			'rgba(255, 206, 86, 1)',
			'rgba(75, 192, 192, 1)',
			'rgba(153, 102, 255, 1)',
			'rgba(255, 159, 64, 1)'
		  ],
		  borderWidth: 1
		}]
	  },
	  options: {
		responsive: true,
		scales: {
		  xAxes: [{
			ticks: {
			  maxRotation: 90,
			  minRotation: 80
			}
		  }],
		  yAxes: [{
			ticks: {
			  beginAtZero: true
			}
		  }]
		}
	  }
	});
		
	//Chart 2
	var ctx = document.getElementById("chart-2");
	var myChart = new Chart(ctx, {
	  type: 'bar',
	  data: {
		labels: ['<?php echo implode("','",array_column($arrDataHours['data'],"label"));?>'],
		datasets: [{
		  label: '# of Calls',
		  data: [<?php echo implode(",",array_column($arrDataHours['data'],"value"));?>],
		  backgroundColor: [
			'rgba(255, 99, 132, 0.2)',
			'rgba(54, 162, 235, 0.2)',
			'rgba(255, 206, 86, 0.2)',
			'rgba(75, 192, 192, 0.2)',
			'rgba(153, 102, 255, 0.2)',
			'rgba(255, 159, 64, 0.2)',
			'rgba(255, 99, 132, 0.2)',
			'rgba(54, 162, 235, 0.2)',
			'rgba(255, 206, 86, 0.2)',
			'rgba(75, 192, 192, 0.2)',
			'rgba(153, 102, 255, 0.2)',
			'rgba(255, 159, 64, 0.2)',
			'rgba(255, 99, 132, 0.2)',
			'rgba(54, 162, 235, 0.2)',
			'rgba(255, 206, 86, 0.2)',
			'rgba(75, 192, 192, 0.2)',
			'rgba(153, 102, 255, 0.2)',
			'rgba(255, 159, 64, 0.2)',
			'rgba(255, 99, 132, 0.2)',
			'rgba(54, 162, 235, 0.2)',
			'rgba(255, 206, 86, 0.2)',
			'rgba(75, 192, 192, 0.2)',
			'rgba(153, 102, 255, 0.2)',
			'rgba(255, 159, 64, 0.2)'
		  ],
		  borderColor: [
			'rgba(255,99,132,1)',
			'rgba(54, 162, 235, 1)',
			'rgba(255, 206, 86, 1)',
			'rgba(75, 192, 192, 1)',
			'rgba(153, 102, 255, 1)',
			'rgba(255, 159, 64, 1)',
			'rgba(255,99,132,1)',
			'rgba(54, 162, 235, 1)',
			'rgba(255, 206, 86, 1)',
			'rgba(75, 192, 192, 1)',
			'rgba(153, 102, 255, 1)',
			'rgba(255, 159, 64, 1)',
			'rgba(255,99,132,1)',
			'rgba(54, 162, 235, 1)',
			'rgba(255, 206, 86, 1)',
			'rgba(75, 192, 192, 1)',
			'rgba(153, 102, 255, 1)',
			'rgba(255, 159, 64, 1)',
			'rgba(255,99,132,1)',
			'rgba(54, 162, 235, 1)',
			'rgba(255, 206, 86, 1)',
			'rgba(75, 192, 192, 1)',
			'rgba(153, 102, 255, 1)',
			'rgba(255, 159, 64, 1)'
		  ],
		  borderWidth: 1
		}]
	  },
	  options: {
		responsive: true,
		scales: {
		  xAxes: [{
			ticks: {
			  maxRotation: 90,
			  minRotation: 80
			}
		  }],
		  yAxes: [{
			ticks: {
			  beginAtZero: true
			}
		  }]
		}
	  }
	});
		
	//Date
	$(document).ready( function () {
		$.fn.dataTable.moment('MM/DD/YYYY');
		
		var table = $('#calls_leadTable').DataTable( {
			responsive: true,
			"pageLength": 25,
			"order": [[ 0, "desc" ]],
			dom: '<"row"<"col-sm-6"l><"col-sm-6 text-right"Bf>>rt<"row"<"col-sm-6"i><"col-sm-6"p>>',
			buttons: [
					{ extend: 'excel',text: 'Export'},
					'print'
				],
		} );
	} );
		
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
			$('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
			window.location.href='?from='+start.format('YYYY-MM-DD')+'&to='+end.format('YYYY-MM-DD');
		});

		cb(start, end);
	});
	</script>
	<script src="https://www.adjack.net/validate-files-js/validate-files.js"></script>
  </body>
</html>