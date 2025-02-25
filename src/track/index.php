<!doctype html>
<html lang="en">
  <head>
	  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/head.php"); 
	  	$from = date("Y-m-d", strtotime("-1 months"));
		$to = date("Y-m-d");

		if (!empty($_GET["from"]))
			$from = date("Y-m-d", strtotime($db->escape($_GET["from"])));
		if (!empty($_GET["to"]))
			$to = date("Y-m-d", strtotime($db->escape($_GET["to"])));
	?>

    <title>Track | <?php echo CLIENT_NAME; ?></title>
  </head>
  <body class="bg-light cbp-spmenu-push">
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/top.php");  ?>

    <div class="container-fluid">
      <div class="row">
        <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/nav.php"); ?>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 p-0 mb-4 dashboard">
			<div class="p-0 border-bottom mb-4">
				<div class="d-flex d-block align-items-center clearfix py-2 px-4">
					<h1 class="h2 font-light mb-0 text-center text-sm-left"><i class="fas fa-chart-line mr-2"></i> Track</h1>
					<div class="ml-auto">
						<div id="reportrange" class="rounded border bg-white py-2 px-3 cursor-pointer rounded-right-0">
							<i class="far fa-calendar-alt"></i>&nbsp;
							<span></span> <i class="fa fa-caret-down"></i>
						</div>
					</div>
				</div>
			</div>
        	<div class="px-4 py-3">
				<?php include $_SERVER['DOCUMENT_ROOT']."/includes/alerts.php"; ?>
				
				<?php
				//Get Leads data from calls and track table
				//$_SESSION['client'] .'-'.$_SESSION['storeid']
				$data_all = $db->rawQuery("select year,mon,month,sum(leads) as leads from ((select year(date) as year,month(date) as mon,monthname(date) as month,sum(goal_completions) as leads from advtrack.campaign_leads where date(date) between ? and ? and client=? group by monthname(date) order by date)) as a group by month order by year,mon",array($from,$to,$_SESSION['client'] .'-'.$_SESSION['storeid']));
				if ($db->count > 0){
					foreach($data_all as $data){ 
						
						$month[] = substr($data["month"],0,3);
						$leads[] = $data["leads"];
					}
					$month_leads = '"'.implode('","',$month).'"';
					$leads = implode(',',$leads);
				}
				
				//Get google analytics traffic data
				$data_all = $db->rawQuery("select channel_group,sum(sessions) as traffic from advtrack.ga_acquisitions_traffic where date(date) between ? and ? and client=? group by channel_group",array($from,$to,$_SESSION['client'] .'-'.$_SESSION['storeid']));
				if ($db->count > 0){
					foreach($data_all as $data){ 
						$channel_group[] = $data["channel_group"];
						$traffic[] = $data["traffic"];
					}
					$channel_group = '"' . implode('","',$channel_group) . '"';
					$traffic = implode(',',$traffic);	
				}
				
				//Get google my business data
				$data_all = $db->rawQuery("select year(date) as year,month(date) as mon,monthname(date) as month,sum(queries_direct+queries_indirect+queries_chain) as queries,sum(views_map+views_search) as views,sum(actions_phone+actions_website+actions_driving_directions) as actions from advtrack.gmbstats where date(date) between ? and ? and client = ? and store_id = ? group by year,mon",array($from,$to,$_SESSION['client'],$_SESSION['storeid']));
				if ($db->count > 0){
					foreach($data_all as $data){ 
						$gmb_month[] = substr($data["month"],0,3);	
						$queries[] = $data["queries"];	
						$views[] = $data["views"];
						$actions[] = $data["actions"];						
					}				
					$gmb_month = '"' . implode('","',$gmb_month) . '"';
					$queries = implode(',',$queries);	
					$views = implode(',',$views);	
					$actions = implode(',',$actions);
				}
				
				//Get google my business summary data
				
				$data_all = $db->rawQuery("select sum(views_map) as views_map,sum(views_search) as views_search, sum(actions_phone) as actions_phone,sum(actions_website) as actions_website,sum(actions_driving_directions) as actions_driving_directions from advtrack.gmbstats where date(date) between ? and ? and client=? and store_id =?",array($from,$to,$_SESSION['client'],$_SESSION['storeid']));
				if ($db->count > 0)
					foreach($data_all as $data){ 
						$views_map[] = $data["views_map"];	
						$views_search[] = $data["views_search"];
						$actions_phone[] = $data["actions_phone"];	
						$actions_website[] = $data["actions_website"];						
						$actions_driving_directions[] = $data["actions_driving_directions"];
					}				
					$views_map = implode(',',$views_map);	
					$views_search = implode(',',$views_search);	
					$actions_phone = implode(',',$actions_phone);	
					$actions_website = implode(',',$actions_website);
					$actions_driving_directions = implode(',',$actions_driving_directions);
					$total_actions  = $views_map . ',' . $views_search . ',' . $actions_phone . ',' . $actions_website . ',' . $actions_driving_directions;
				
				?>
				<div class="row align-items-stretch">
					<div class="col-md-8">
						<div class="box p-2">
							<h2 class="text-uppercase h4 text-dark d-flex flex-wrap">Your&nbsp;<span class="text-blue">Leads</span> <a href="/track/" class="btn bg-light border-dark text-dark btn-sm font-12 text-uppercase ml-auto">View Details</a></h2>						
							<canvas class="my-2" id="myChart"></canvas>
						</div>
					</div>
					<div class="col-md-4 mt-4 mt-md-0">
						<div class="box p-2">
							<h2 class="text-uppercase h4 text-dark d-flex flex-wrap">Your&nbsp;<span class="text-blue">Traffic</span> <a href="/track/" class="btn bg-light border-dark text-dark btn-sm font-12 text-uppercase ml-auto">View Details</a></h2>
							<canvas class="my-2" id="myPieChart" width="600" height="630"></canvas>
						</div>
					</div>
				</div>

				<div class="row mt-0 mt-md-4 align-items-stretch">
					<div class="col-md-4  mt-4 mt-md-0">
						<div class="box p-2">
							<h2 class="text-uppercase h4 text-dark d-flex flex-wrap">Google Insights&nbsp;<span class="text-blue">Timeline</span> <a href="/track/" class="btn bg-light border-dark text-dark btn-sm font-12 text-uppercase ml-auto">View Details</a></h2>
							<canvas class="my-2" id="myLineChart" height="250"></canvas>
						</div>
					</div>
					<div class="col-md-4  mt-4 mt-md-0">
						<div class="box p-2">
							<h2 class="text-uppercase h4 text-dark d-flex flex-wrap">Google Insights&nbsp;<span class="text-blue">Summary</span> <a href="/track/" class="btn bg-light border-dark text-dark btn-sm font-12 text-uppercase ml-auto">View Details</a></h2>
							<canvas class="my-2" id="gisummary" height="250"></canvas>
						</div>
					</div>
					<div class="col-md-4 mt-4 mt-md-0">
						<div class="box p-2">
							<h2 class="text-uppercase h4 text-dark d-flex flex-wrap mb-4">Top&nbsp;<span class="text-blue">Pages</span> <a href="/track/" class="btn bg-light border-dark text-dark btn-sm font-12 text-uppercase ml-auto">View Details</a></h2>
								<?php
								//Get google analytics page data		
								$pages = $db->rawQuery("select page_title,concat('https://',hostname,page_path) as page_path,sum(page_views) as page_views from advtrack.ga_landing_pages where date(date) between ? and ? and client=? group by page_title order by page_views desc limit 6",array($from,$to,$_SESSION['client'] .'-'.$_SESSION['storeid']));
								if ($db->count > 0){ ?>
								<ul class="list-unstyled mb-0">
									<?php foreach($pages as $page){ ?>

									<li class="mb-3">
										<div class="d-flex align-items-center">
											<div class="text-center">
												<span class="d-block font-weight-bold"><?php echo number_format($page["page_views"],0);?></span>
												<span class="d-block text-uppercase small">Views</span>
											</div>
											<div class="ml-2 pl-2 border-left">
												<span class="d-block font-weight-bold text-blue"><?php echo $page["page_title"];?></span>
												<a href="<?php echo $page["page_path"];?>" class="d-block text-dark small"><?php echo $page["page_path"];?></a>
											</div>
										</div>

									</li>
									<?php } ?>
								</ul>
								<?php } ?>
							
						</div>
					</div>
				</div>


			</div>

          

        
        </main>
      </div>
    </div>


    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>
	<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
	  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js"></script>
    <script>
		
//		Leads
		var ctx = document.getElementById('myChart').getContext("2d");

		var gradientStroke = ctx.createLinearGradient(500, 0, 100, 0);
		gradientStroke.addColorStop(0, '#FD6E6A');
		gradientStroke.addColorStop(1, '#FFC600');

		var gradientFill = ctx.createLinearGradient(500, 0, 100, 0);
		gradientFill.addColorStop(0, "rgba(253,110,106, 0.6)");
		gradientFill.addColorStop(1, "rgba(255,198,0, 0.6)");

		var myChart = new Chart(ctx, {
			type: 'line',
			data: {
				labels: [<?php echo strtoupper($month_leads); ?>],
				datasets: [{
					label: "Data",
					borderColor: gradientStroke,
					pointBorderColor: gradientStroke,
					pointBackgroundColor: gradientStroke,
				   pointHoverBackgroundColor: gradientStroke,
					pointHoverBorderColor: gradientStroke,
					pointBorderWidth: 10,
					pointHoverRadius: 10,
					pointHoverBorderWidth: 1,
					pointRadius: 3,
					fill: true,
					backgroundColor: gradientFill,
					borderWidth: 4,
					data: [<?php echo $leads; ?>]
				}]
			},
			options: {
				legend: {
					position: "bottom"
				},
				scales: {
					yAxes: [{
						ticks: {
							fontColor: "rgba(0,0,0,0.5)",
							fontStyle: "bold",
							beginAtZero: true,
							maxTicksLimit: 5,
							padding: 20
						},
						gridLines: {
							drawTicks: false,
							display: false
						}

					}],
					xAxes: [{
						gridLines: {
							zeroLineColor: "transparent"
						},
						ticks: {
							padding: 20,
							fontColor: "rgba(0,0,0,0.5)",
							fontStyle: "bold"
						}
					}]
				}
			}
		});
		
//		Traffic
		var ptx = document.getElementById("myPieChart");
		var myPieChart = new Chart(ptx,{
			type: 'doughnut',
			data: {
				datasets: [{
					data: [<?php echo $traffic; ?>],
					backgroundColor:  [
						"#48CFAD",
						"#4FC1E9",
						"#A0D468",
						"#FFCE54",
						"#ED5565",
						"#FC6E51",
						"#AC92EC"
					]
				}],

				// These labels appear in the legend and in the tooltips when hovering different arcs
				labels: [<?php echo $channel_group; ?>]
			},
			options: {
				legend: {
					position: "bottom"
				}
			}
		});
		
//		Google Insights Timeline
		var ltx = document.getElementById("myLineChart");
		var myLineChart = new Chart(ltx, {
			type: 'line',
			data: {
				labels: [<?php echo $gmb_month;?>],
				datasets: [{ 
					data: [<?php echo $queries;?>],
					label: "Queries",
					borderColor: "#4FC1E9",
					fill: false
				  }, { 
					data: [<?php echo $views;?>],
					label: "Views",
					borderColor: "#FC6E51",
					fill: false
				  }, { 
					data: [<?php echo $actions;?>],
					label: "Actions",
					borderColor: "#48CFAD",
					fill: false
				  }
				]
			  },
			  options: {
				legend: {
					position: "bottom"
				}
			  }
		});
		
//		Google Insights Summary
		var myPieChart = new Chart(document.getElementById("gisummary"),{
			type: 'doughnut',
			data: {
				datasets: [{
					data: [<?php echo $total_actions; ?>],
					backgroundColor:  [
						"#48CFAD",
						"#4FC1E9",
						"#A0D468",
						"#FFCE54",
						"#ED5565"
					]
				}],

				// These labels appear in the legend and in the tooltips when hovering different arcs
				labels: ['Map Views','Search Views','Phone Call','Website Actions','Driving Directions']
			},
			options: {
				legend: {
					position: "bottom"
				}
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
				window.location.href='/dashboard.php?from='+start.format('YYYY-MM-DD')+'&to='+end.format('YYYY-MM-DD');
			});

			cb(start, end);
	
		});

    </script>
  </body>
</html>