	<!doctype html>
	<html lang="en">
	<head>
		<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
		<link href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.css" rel="stylesheet" type="text/css" />
		<link href="//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
		<link href="//cdn.datatables.net/buttons/1.5.6/css/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
		<link href="//cdn.datatables.net/select/1.2.2/css/select.dataTables.min.css" rel="stylesheet" type="text/css" />
		<style>
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
			#userTable_mailchimp{
				width: 100% !important;
			}

			code{
				display: block;
				padding: 5px;
				margin-top: 10px;
				display: none;
			}
			.vCode{
				cursor: pointer;
			}
			.c-thru {
				background: rgba(255,255,255,0.7);
			}
		</style>

		<?php include ($_SERVER['DOCUMENT_ROOT']."/includes/head.php"); 
		if(!(roleHasPermission('show_account_management_overview', $_SESSION['role_permissions']))){
			header('location: /');
			exit;
		}
		?>

		<title>Login Report | Local <?=$client?></title>
		
	</head>
	<body class="bg-light cbp-spmenu-push">
		<?php include ($_SERVER['DOCUMENT_ROOT']."/includes/top.php"); ?>

		<div class="container-fluid">
			<div class="row">
				<?php 
				include ($_SERVER['DOCUMENT_ROOT']."/includes/nav.php");
				require ($_SERVER['DOCUMENT_ROOT']."/includes/DasApiSDK/vendor/autoload.php");
				use Das\Report;
				use Das\Client;
				use Das\Zendesk;

				$from = date('Y-01-01 00:00:00');
				$to = date("Y-m-d 23:59:59",strtotime("yesterday"));

				if (!empty($_GET["from"]))
					$from = date("Y-m-d 00:00:00", strtotime($db->escape($_GET["from"])));
				if (!empty($_GET["to"]))
					$to = date("Y-m-d 23:59:59", strtotime($db->escape($_GET["to"])));
				
				$url_d = "?from=".$from."&to=".$to;

				$loginReport = new Report($token_api);
				$clientLogins = new Client($token_api);
				$zendesk = new Zendesk($token_api);
				$params = array(
					'gte' => (string)strtotime($from),				
					'lte' => (string)strtotime($to)
				);

				$loginInfo = $loginReport->getLoginReport($_SESSION['client'],$params);

				$loginInfo = (isset($loginInfo['data'][0])) ? $loginInfo['data'][0] : [];

				$repCallsInfo = $loginInfo['repcalls'];

				$calls = $repCallsInfo['calls'];
				$time = $repCallsInfo['time'];

				$logins =  $loginInfo['logins'];
				$users =  $loginInfo['users'];

				$tickets = $zendesk->getTickets(array_merge($params,array('fieldvalue'=>'fully_promoted/embroidme','tags'=>'fully_promoted/embroidme','status_gte' => 'solved')));
				$closedTickets = isset($tickets['info']['total_items']) ? $tickets['info']['total_items'] : 0;

				$tickets = $zendesk->getTickets(array_merge($params,array('fieldvalue'=>'fully_promoted/embroidme','tags'=>'fully_promoted/embroidme','status_lte' => 'hold')));
				$openTickets = isset($tickets['info']['total_items']) ? $tickets['info']['total_items'] : 0;

				?>
				<div id="spinner_loading" class="none_upload loader"></div>
				<main role="main" class="col-md-9 ml-sm-auto col-lg-10 p-0">			

					<div class="p-0 border-bottom mb-3">
						<div class="d-flex d-block align-items-center clearfix py-2 px-4">
							<h1 class="h2 font-light mb-0 text-center text-sm-left"><i class="fas fa-sign-in-alt mr-2"></i> Account Management Overview </h1>
							<div class="ml-auto">
								<div id="reportrange" class="rounded border bg-white py-2 px-3 cursor-pointer rounded-right-0">
									<i class="far fa-calendar-alt"></i>&nbsp;
									<span></span> <i class="fa fa-caret-down"></i>
								</div>
							</div>
						</div>
					</div>
					<?php include $_SERVER['DOCUMENT_ROOT']."/includes/alerts.php"; ?>
					<div class="py-2 px-4">
						<!-- Nav tabs -->
						<ul class="nav nav-tabs" role="tablist">
							<li class="nav-item">
								<a class="nav-link text-blue active" data-toggle="tab" href="#tabs-overview">Overview</a>
							</li>
							<li class="nav-item">
								<a class="nav-link text-blue" data-toggle="tab" href="#tabs-logins">Logins</a>
							</li>
						</ul>
						<div class="tab-content p-2">
							<div class="tab-pane active" id="tabs-overview" >
								<div class="row">
									<div class="col-12 col-lg-3">

										<h3 class="h5 text-blue">Zendesk Tickets Info</h3>								
										<div class="box py-4 bg-light-custom mb-4 border text-center">
											<p class="h6">Open Tickets / Closed Tickets</p>
											<p class="h1 text-blue"><?php echo $openTickets. ' / '.$closedTickets;?></p>
										</div>

										<h3 class="h5 text-blue">Login Info</h3>								
										<div class="box py-4 bg-light-custom mb-4 border text-center">
											<p class="h6">Total Login / Unique Users</p>
											<p class="h1 text-blue"><?php echo $logins. ' / '.$users;?></p>
										</div>
									</div>
									<div class="col-12 col-lg-9">
										<h3 class="h5 text-blue">Login Info by Month</h3>
										<canvas class="my-2" id="loginsInfo" height="88"></canvas>
									</div>
								</div>
								<div class="row">
									<div class="col-12 col-lg-3">
										<h3 class="h5 text-blue">Reps Calls Info</h3>								
										<div class="box py-4 bg-light-custom mb-4 border text-center">
											<p class="h6">Total Calls / Talk Time</p>
											<p class="h1 text-blue"><?php echo $calls. ' / '.$time;?></p>
										</div>
									</div>
									<div class="col-12 col-lg-9">
										<h3 class="h5 text-blue">Reps Calls Info by Month</h3>
										<canvas class="my-2" id="repsCallsInfo" height="88"></canvas>
									</div>
								</div>
							</div>	
							<div class="tab-pane fade" id="tabs-logins">
								<div class="row ">
									<div class="table-responsive">
										<table  id="LoginTable" class="display table table-striped dataTable" style="width:100%">
											<thead class="thead-dark">
												<tr>
													<th>Date/Time</th>
													<th>Storeid</th>
													<th>Location Name</th>
													<th>Username</th>
													<th>IP Address</th>

												</tr>
											</thead>									
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</main>
			
		</div>


		<?php include ($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>
		<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
		<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js"></script>
		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
		<script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
		<script src="//cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
		<script src="//cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
		<script src="//cdn.datatables.net/buttons/1.5.6/js/buttons.bootstrap4.min.js"></script>
		<script src="//cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
		<script src="//cdn.datatables.net/buttons/1.5.6/js/buttons.colVis.min.js "></script>

		<script type="text/javascript">		    
			var ltx = document.getElementById("repsCallsInfo");
			var myLineChart = new Chart(ltx, {
				type: 'line',
				data: {
					labels: [<?php echo '"'.implode('","', array_column($repCallsInfo["month"],'monthName')) . '"';?>],
					datasets: [
					{ 
						data: [<?php echo '"'.implode('","', array_column($repCallsInfo["month"],'hour')) . '"';?>],
						label: "Talk Time Hr",
						borderColor: "#003d4c",
						fill: false,
					}
					,{ 
						label: "Rep Calls",
						data: [<?php echo implode(',', array_column($repCallsInfo["month"],'qtt_call'));?>],
						borderColor: "#DBBE00",
						borderDash: [5, 5],
						fill: false,	
					}
					]
				},
				options: {
					responsive: true,				
					tooltips: {
						mode: 'index',
						intersect: false,
					},
					hover: {
						mode: 'nearest',
						intersect: true
					},
					scales: {
						xAxes: [{
							display: true,
							scaleLabel: {
								display: true,
								labelString: 'Month'
							}
						}],
						yAxes: [{
							display: true,
							scaleLabel: {
								display: true,
								labelString: 'Value'
							}
						}]
					}
				}
			});		
			var ltx = document.getElementById("loginsInfo");
			var myLineChart = new Chart(ltx, {
				type: 'line',
				data: {
					labels: [<?php echo '"'.implode('","', array_column($loginInfo["month"],'monthName')) . '"';?>],
					datasets: [{ 
						data: [<?php echo implode(',', array_column($loginInfo["month"],'qtt_login'));?>],
						label: "Logins",
						borderColor: "#003d4c",
						fill: false,
				//backgroundColor: "rgba(1,118,147,0.6)"
			},{ 
				data: [<?php echo implode(',', array_column($loginInfo["month"],'qtt_user'));?>],
				label: "Users",
				borderColor: "#DBBE00",
				borderDash: [5, 5],
				fill: false,	
			}			
			]
		},
		options: {
			responsive: true,				
			tooltips: {
				mode: 'index',
				intersect: false,
			},
			hover: {
				mode: 'nearest',
				intersect: true
			},
			scales: {
				xAxes: [{
					display: true,
					scaleLabel: {
						display: true,
						labelString: 'Month'
					}
				}],
				yAxes: [{
					display: true,
					scaleLabel: {
						display: true,
						labelString: 'Value'
					}
				}]
			}
		}
	});	

			function fetch_data(start_date, end_date){
				var userTable = $('#LoginTable').DataTable({
					'processing': true,
					'responsive': true,
					'serverSide': true,
					"pageLength": 10,
					"lengthMenu": [[10, 30,40,50], [10, 30,40,50]],
					'serverMethod': 'POST',
					'ajax': {
						'url':'xt_logins_log.php',
						data:{start_date:start_date, end_date: end_date }
					},
					'searching': true,
					"order": [[ 0, "desc" ]],
					'columns': [
					{ data: 'date' },
					{ data: 'storeid' },
					{ data: 'locationName' },
					{ data: 'username' },
					{ data: 'ip' },
					],
					columnDefs: [
					{
						targets: [1],
						orderable: false,
						searchable: false,
					}
					]
				});
			}

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
				var start = moment().startOf('year');
				var end = moment().subtract(1, 'days');
			}

			function cb(start, end) {
				$('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
			}
			
			$('#reportrange').daterangepicker({
				opens: 'left',
				startDate: start,
				endDate: end,
				maxDate: end,
				ranges: {
					'Today': [moment().subtract(1, 'days'), moment()],
					'Yesterday': [moment().subtract(2, 'days'), moment()],
					'Last 7 Days': [moment().subtract(6, 'days'), moment()],
					'Last 30 Days': [moment().subtract(29, 'days'), moment()],
					'This Month': [moment().startOf('month'), moment()],
					'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
					'This Year': [moment().startOf('year'), moment()],
					'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')],
					'Lifetime': [moment().subtract(3, 'year').startOf('year'), moment()],
				}
			}, function(start, end, label) {
				$('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
				window.location.href='?from='+start.format('YYYY-MM-DD')+'&to='+end.format('YYYY-MM-DD');
			});
			fetch_data(start.format('MM/DD/YYYY'), end.format('MM/DD/YYYY'));
			cb(start, end);

		});

	</script>
</body>
</html>