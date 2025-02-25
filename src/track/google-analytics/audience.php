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

    <title>Audience Overview | <?php echo CLIENT_NAME; ?></title>
  </head>
  <body class="bg-light cbp-spmenu-push">
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/top.php"); ?>

    <div class="container-fluid">
      <div class="row">
        <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/nav.php"); ?>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 p-0 mb-4 dashboard">
			<div class="p-0 border-bottom mb-4">
				<div class="d-flex d-block align-items-center clearfix py-2 px-4">
					<h1 class="h2 font-light mb-0 text-center text-sm-left"><i class="fas fa-users mr-2"></i> Google Analytics Audience Overview</h1>
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
				//Get google analytics by channel
				$GAchannel = $db->rawQuery("select channel_group,sum(sessions) as sessions,sum(users) as users,sum(pageviews) as pageviews,sum(new_users) as new_users from advtrack.ga_acquisitions_traffic where date(date) between ? and ? and client=? group by channel_group",array($from,$to,$_SESSION['client']."-".$_SESSION['storeid']));
				if ($db->count > 0)
					foreach($GAchannel as $data){ 
						$channel_group[] = $data["channel_group"];
						$sessions[] = $data["sessions"];
						$total_sessions += $data["sessions"];
						$total_users += $data["users"];
						$total_pageviews += $data["pageviews"];
						$total_new_users += $data["new_users"];
					}
					$channel_group = '"' . implode('","',$channel_group) . '"';
					$sessions = implode(',',$sessions);	
				
				//Get google analytics by source
				$GAsource = $db->rawQuery("select source,sum(sessions) as sessions,sum(users) as users,sum(pageviews) as pageviews,sum(new_users) as new_users from advtrack.ga_acquisitions_traffic where date(date) between ? and ? and client=? group by source order by sessions desc limit 10",array($from,$to,$_SESSION['client']."-".$_SESSION['storeid']));

				if ($db->count > 0)
					foreach($GAsource as $data){  
						$source[] = $data["source"];
						$sessions_source[] = $data["sessions"];
					}
					$source = '"' . implode('","',$source) . '"';
					$sessions_source = implode(',',$sessions_source);						
					
				//Page Views
				$pageViews = $db->rawQuery("select year(date) as year,month(date) as mon,monthname(date) as month,sum(sessions) as sessions,sum(pageviews) as pageviews from advtrack.ga_acquisitions_traffic where date(date) between ? and ? and client=? group by year,mon",array($from,$to,$_SESSION['client']."-".$_SESSION['storeid']));
				if ($db->count > 0)
					foreach($pageViews as $data){  	
						$ga_month[] = substr($data["month"],0,3);	
						$sessions_data[] = $data["sessions"];	
						$pageviews_data[] = $data["pageviews"];
	
					}				
					$ga_month = '"' . implode('","',$ga_month) . '"';
					$sessions_data = implode(',',$sessions_data);	
					$pageviews_data = implode(',',$pageviews_data);	
				
				//Get google analytics by device
				$devices = $db->rawQuery("select device_category,sum(sessions) as sessions from advtrack.ga_device_traffic where date(date) between ? and ? and client=? group by device_category",array($from,$to,$_SESSION['client']."-".$_SESSION['storeid']));

				if ($db->count > 0)
					foreach($devices as $data){  	
						$device_category[] = $data["device_category"];
						$sessions_device[] = $data["sessions"];
					}
					$device_category = '"' . implode('","',$device_category) . '"';
					$sessions_device = implode(',',$sessions_device);			


				//Get google analytics by city
				$GAcity = $db->rawQuery("select city,sum(sessions) as sessions,sum(users) as users,sum(pageviews) as pageviews,sum(new_users) as new_users from advtrack.ga_acquisitions_traffic where date(date) between ? and ? and client=? group by city order by sessions desc limit 10",array($from,$to,$_SESSION['client']."-".$_SESSION['storeid']));

				if ($db->count > 0)
					foreach($GAcity as $data){  	
						$city[] = $data["city"];
						$sessions_city[] = $data["sessions"];
					}
					$city = '"' . implode('","',$city) . '"';
					$sessions_city = implode(',',$sessions_city);	
				
				?>

				<div class="row align-items-stretch">
					<div class="col-md-2">
						<div class="h-100 d-flex flex-column justify-content-between">
							<div class="box p-2 mb-2 d-flex flex-column justify-content-center box-border-left">
								<span class="stat d-block text-center text-muted h3 mb-1 font-weight-bold"><span class="counter-value" data-count="<?php echo $total_pageviews;?>">0</span></span>
								<span class="label d-block text-center text-dark font-12 text-uppercase letter-1">Page Views</span>
							</div>
							<div class="box p-2 mb-2 d-flex flex-column justify-content-center box-border-left">
								<span class="stat d-block text-center text-muted h3 mb-1 font-weight-bold"><span class="counter-value" data-count="<?php echo $total_users;?>">0</span></span>
								<span class="label d-block text-center text-dark font-12 text-uppercase letter-1">Users</span>
							</div>
							<div class="box p-2 d-flex flex-column justify-content-center box-border-left">
								<span class="stat d-block text-center text-muted h3 mb-1 font-weight-bold"><span class="counter-value" data-count="<?php echo number_format(($total_new_users/$total_users)*100,2);?>">0</span>%</span>
								<span class="label d-block text-center text-dark font-12 text-uppercase letter-1">% of New Sessions</span>
							</div>
						</div>
					</div>
					<div class="col-md-5">
						<div class="box p-2">
							<h2 class="text-uppercase h4 text-dark d-flex flex-wrap mb-2">Sessions By&nbsp;<span class="text-blue">Page Views</span></h2>						
							<canvas class="my-2" id="pageViews"></canvas>
						</div>
					</div>
					<div class="col-md-5 mt-4 mt-md-0">
						<div class="box p-2">
							<h2 class="text-uppercase h4 text-dark d-flex flex-wrap mb-2">Sessions By&nbsp;<span class="text-blue">Channel</span></h2>
							<canvas class="my-2" id="sessionsChannel" width="600" height="250"></canvas>
						</div>
					</div>
				</div>

				<div class="row mt-0 mt-md-4 align-items-stretch">
					<div class="col-md-4  mt-4 mt-md-0">
						<div class="box p-2">
							<h2 class="text-uppercase h4 text-dark d-flex flex-wrap mb-2">Sessions By&nbsp;<span class="text-blue">Device</span></h2>
							<canvas class="my-2" id="sessionsDevice" height="250"></canvas>
						</div>
					</div>
					<div class="col-md-4 mt-4 mt-md-0">
						<div class="box p-2">
							<h2 class="text-uppercase h4 text-dark d-flex flex-wrap mb-2">Sessions By&nbsp;<span class="text-blue">Top 10 Locations</span></h2>
							<canvas class="my-2" id="sessionsLocations" width="600" height="400"></canvas>
						</div>
					</div>
					<div class="col-md-4 mt-4 mt-md-0">
						<div class="box p-2">
							<h2 class="text-uppercase h4 text-dark d-flex flex-wrap mb-2">Sessions By&nbsp;<span class="text-blue">Top 10 Sources</span></h2>
							<canvas class="my-2" id="sessionsSources" width="600" height="400"></canvas>
						</div>
					</div>
				</div>
			</div>
			

          

        
        </main>
      </div>

    </div>


    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js"></script>
	<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
	<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>
//		Sessions By Device
		var ctx = document.getElementById('sessionsDevice').getContext("2d");
		var gradientStroke = ctx.createLinearGradient(500, 0, 100, 0);
		gradientStroke.addColorStop(0, '#cceaff');
		gradientStroke.addColorStop(1, '#003c66');

		var gradientFill = ctx.createLinearGradient(500, 0, 100, 0);
		gradientFill.addColorStop(0, "rgba(204,234,255, 0.6)");
		gradientFill.addColorStop(1, "rgba(0,60,102, 0.6)");
		
		var myBarChart = new Chart(ctx, {
			type: 'bar',
			data: {
				labels: [<?php echo $device_category;?>],
				datasets: [{
					label: "Data",
					borderColor: gradientStroke,
					borderWidth: 1,
					backgroundColor: gradientFill,
					data: [<?php echo $sessions_device;?>]
				}]
			},
			options: {
				legend: {
					position: "bottom"
				}
			}
		});
		
//		Sessions by Channel
		var ptx = document.getElementById("sessionsChannel");
		var myPieChart = new Chart(ptx,{
			type: 'doughnut',
			data: {
				datasets: [{
					data: [<?php echo $sessions;?>],
					backgroundColor:  [
						"#778A9F",
						"#0067b1",
						"#4C535A",
						"#E7E5E5",
						"#475c6b",
						"#C2C6CA",
						"#365f7d"
					]
				}],

				// These labels appear in the legend and in the tooltips when hovering different arcs
				labels: [<?php echo $channel_group;?>]
			},
			options: {
				legend: {
					position: "bottom"
				}
			}
		});
		
//		Sessions by Top Locations
		var ptx = document.getElementById("sessionsLocations");
		var myPieChart = new Chart(ptx,{
			type: 'doughnut',
			data: {
				datasets: [{
					data: [<?php echo $sessions_city;?>],
					backgroundColor:  [
						"#16649c",
						"#0086e6",
						"#0067b1",
						"#004a80",
						"#4e89bf",
						"#808080",
						"#778A9F",
						"#778A9F",
						"#97a5b5",
						"#b3dfff",
						
						
					]
				}],

				// These labels appear in the legend and in the tooltips when hovering different arcs
				labels: [<?php echo $city;?>]
			},
			options: {
				legend: {
					position: "bottom"
				}
			}
		});
		
//		Sessions by Page Views
		var ltx = document.getElementById("pageViews");
		var myLineChart = new Chart(ltx, {
			type: 'line',
			data: {
				labels: [<?php echo $ga_month;?>],
				datasets: [{ 
					data: [<?php echo $sessions_data;?>],
					label: "Sessions",
					borderColor: "#0067b1",
					fill: false
				  }, { 
					data: [<?php echo $pageviews_data;?>],
					label: "Page Views",
					borderColor: "#1d3349",
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
		
//		Sessions By Top Sources
		var ptx = document.getElementById("sessionsSources");
		var myPieChart = new Chart(ptx,{
			type: 'doughnut',
			data: {
				datasets: [{
					data: [<?php echo $sessions_source;?>],
					backgroundColor:  [
						"#778A9F",
						"#E7E5E5",
						"#0067b1",
						"#475c6b",
						"#365f7d",
						"#005999",
						"#80caff",
						"#0086e6",
						"#28618a",
						"#C2C6CA",
					]
				}],

				// These labels appear in the legend and in the tooltips when hovering different arcs
				labels: [<?php echo $source;?>]
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
				window.location.href='?from='+start.format('YYYY-MM-DD')+'&to='+end.format('YYYY-MM-DD');
			});

			cb(start, end);
	
		});
		
		$(document).ready(function(){
			$('.counter-value').each(function() {
			  var $this = $(this),
				countTo = $this.attr('data-count');
			  $({
				countNum: $this.text()
			  }).animate({
				  countNum: countTo
				},

				{

				  duration: 2000,
				  easing: 'swing',
				  step: function() {
					$this.text(Math.floor(this.countNum));
				  },
				  complete: function() {
					$this.text(numberWithCommas(this.countNum));
				  }

				});
			});
			
		});
		function numberWithCommas(number) {
			var parts = number.toString().split(".");
			parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
			return parts.join(".");
		}
		
    </script>
  </body>
</html>