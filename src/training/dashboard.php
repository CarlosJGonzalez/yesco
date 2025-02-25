<?php set_time_limit(300);
date_default_timezone_set('America/New_York');
?>
<!doctype html>
<html lang="en">
  <head>
	  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/head.php"); 
        require_once ($_SERVER['DOCUMENT_ROOT']."/includes/ClassDasPost.php");

	  	$from = date("Y-m-d 00:00:00", strtotime("-1 months"));
		$to = date("Y-m-d 23:59:59");

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
		
		$url_d = "?from=".$from."&to=".$to;
	?>

    <title>Dashboard | <?php echo CLIENT_NAME; ?></title>
  </head>
  <body class="bg-light cbp-spmenu-push">
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/top.php");  ?>

    <div class="container-fluid">
      <div class="row">
        <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/nav.php"); ?>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 p-0 mb-4 dashboard">
			<div class="p-0 border-bottom mb-4">
				<div class="border-bottom-dotted d-flex d-block align-items-center clearfix py-2 px-4">
					<h1 class="h2 font-light mb-0 text-center text-sm-left"><i class="fas fa-tachometer-alt mr-2"></i> Dashboard</h1>
					<div class="ml-auto">
						<div id="reportrange" class="rounded border bg-white py-2 px-3 cursor-pointer rounded-right-0">
							<i class="far fa-calendar-alt"></i>&nbsp;
							<span></span> <i class="fa fa-caret-down"></i>
						</div>
					</div>
				</div>
				<div class="py-2 px-4 d-block d-xl-flex align-items-center">
					<div>
						<h2 class="h4 mb-0 text-dark"><?php echo CLIENT_NAME ?>&nbsp;<span class="text-blue"><?php echo $active_location['companyname']; ?></span></h2>
						<a href="<?php echo CLIENT_URL.'locations/'.$active_location['url'].'/'; ?>" class="text-blue" target="_blank"><?php echo CLIENT_URL.'locations/'; ?><?php echo $active_location['url']; ?>/</a>
					</div>
					<div class="ml-auto mt-3 mt-xl-0 text-right">
						<p class="bg-white rounded-pill text-uppercase py-1 px-3 text-dark border ml-auto mb-0 d-inline-block mb-1"><?php echo $active_location['suspend'] == 1 ? "Inactive" : "Active";?></p>
						<small class="text-muted d-block"><?php echo !empty($last_login['lastlogin']) ? "Last Login: ".date("m/d/Y g:i a",strtotime($last_login['lastlogin'])) : "";?></small>
					</div>
				</div>
			</div>
        	<div class="px-4 py-3">
				<?php include $_SERVER['DOCUMENT_ROOT']."/includes/alerts.php"; ?>
				
				<?php 
				//Get Leads data from calls and track table
				//$_SESSION['client'] .'-'.$_SESSION['storeid']
				$time = strtotime("-1 year", strtotime($to));
  				$ytdate = date("Y-m-01", $time);
				$data_all = $db->rawQuery("select year,mon,month,sum(leads) as leads from ((select year(date) as year,month(date) as mon,monthname(date) as month,sum(goal_completions) as leads from advtrack.campaign_leads where date(date) between ? and ? and client=? group by monthname(date) order by date)) as a group by month order by year,mon",array($ytdate,$to,$_SESSION['client'] .'-'.$_SESSION['storeid']));

				if ($db->count > 0){
					foreach($data_all as $data){ 
						
						$month[] = substr($data["month"],0,3);
						$leads[] = $data["leads"];
					}
					$month_leads = '"'.implode('","',$month).'"';
					$leads = implode(',',$leads);
				}

						
				
				$data_all = $db->where('client',$_SESSION['client'].'-'.$_SESSION['storeid'])
							   ->where('date(date)',array($from,$to),'BETWEEN')				   
				               ->groupBy ('year,month')->orderBy('mon','asc')
				               ->get('advtrack.facebookstats_new',null,'year(date) as year,month(date) as mon,monthname(date) as month,sum(imps) as imps,sum(clicks) as clicks,sum(reach) as reach,sum(engagement) as engagement');

				if (count($data_all)){

					foreach($data_all as $data){ 
						$fb_month_ads[] = substr($data["month"],0,3);	
						$clicks_ads[] = $data["clicks"];	
						$imps_ads[] = $data["imps"];
						$reach_ads[] = $data["reach"];						
						$engagement_ads[] = $data["engagement"];						
					}

					$fb_month_ads = '"' . implode('","',$fb_month_ads) . '"';
					$clicks_ads = implode(',',$clicks_ads);	
					$imps_ads = implode(',',$imps_ads);	
					$reach_ads = implode(',',$reach_ads);
					$engagement_ads = implode(',',$engagement_ads);

				}
				?>
				<div class="row justify-content-center mb-4">
					<!--Traffic-->
					<div class="col col-lg-2 col-sm-6 col-xl-2 mb-3">
						<div class="h-100 bg-white box-shadow">
							<?php
							//get sessions
							$ga = $db->rawQueryOne("select sum(sessions) as sessions,sum(users) as users from advtrack.ga_acquisitions_traffic where date(date) between ? and ? and client=?", array($from,$to,$_SESSION['client']."-".$_SESSION['storeid']));

							//get prev sessions
							$pga = $db->rawQueryOne("select sum(sessions) as sessions,sum(users) as users from advtrack.ga_acquisitions_traffic where date(date) between ? and ? and client=?", array($previousFrom,$previousTo,$_SESSION['client']."-".$_SESSION['storeid']));
							?>
							<div class="border-bottom p-2">
								<span class="d-block h5 mb-0">Visitors</span>
							</div>
							<div class="p-2">
								<span class="d-block h2 font-weight-bold"><?php echo number_format($ga["users"]); ?></span>
								<p class="d-inline-block mb-0 text-right">
								<?php 
								//calculate diff percentage
									if($pga["users"]==0) 
										$diffPct = round($ga["users"]*100,2);
									else
										$diffPct = round((($ga["users"]-$pga["users"])/$pga["users"])*100,2);
									if($diffPct==0){
										$icon = "";
										$textColor = "text-dark";
									}
									else if($diffPct>0){
										$textColor = "text-success";
										$icon = "arrow-up";
									}
									else{
										$icon = "arrow-down";
										$textColor = "text-danger";
									}
									?>
									
										<span class="<?php echo $textColor; ?>"><?php if(!empty($icon)){ ?><i class="fas fa-<?php echo $icon; ?>"></i><?php } ?> <?php echo $diffPct; ?>% Previous Period</span>
										<br>
										<span class="small d-block">(<?php echo date("m/d/Y", strtotime($previousFrom))." - ".date("m/d/Y", strtotime($previousTo)); ?>)</span>
								
								</p>
							</div>
							
						</div>
					</div>
					<?php
					//get leads
					$boxleads = $db->rawQuery("select goal_name,sum(goal_completions) as total from advtrack.campaign_leads where date between ? and ? and client=? group by goal_name",array($from,$to,$_SESSION['client']."-".$_SESSION['storeid']));
					
					//get previous leads
					$pleads = $db->rawQuery("select goal_name,sum(goal_completions) as total from advtrack.campaign_leads where date between ? and ? and client=? group by goal_name",array($previousFrom,$previousTo,$_SESSION['client']."-".$_SESSION['storeid']));

					$leadsByType = array_combine(array_column($boxleads, 'goal_name'), $boxleads);
					$prevLeadsByType = array_combine(array_column($pleads, 'goal_name'), $pleads);
					$accepted_leads = array("Call","Website Forms");
					$sum = 0;
					foreach ($boxleads as $item) {
						if(in_array($item['goal_name'],$accepted_leads))
							$sum += $item['total'];
					}
					$psum = 0;
					foreach ($pleads as $item) {
						if(in_array($item['goal_name'],$accepted_leads))
							$psum += $item['total'];
					}
					
					//get calls
					$db->setTrace(true);
					$totalCalls = $db->rawQueryOne("select count(*) as count, avg(duration) as duration from advtrack.calls where client = '".$_SESSION['client']."-".$_SESSION['storeid']."' and start between '".$from."' and '".$to."'");
//					print_r($db->trace);
					$totalCallsPrev = $db->rawQueryOne("select count(*) as count, avg(duration) as duration from advtrack.calls where client  = '".$_SESSION['client']."-".$_SESSION['storeid']."' and start between '".$previousFrom."' and '".$previousTo."'");

					?>
					<!--Forms-->
					<div class="col col-lg-2 col-sm-6 col-xl-2 mb-3">
						<div class="h-100 bg-white box-shadow">
							<div class="border-bottom p-2">
								<span class="d-block h5 mb-0">Forms</span>
							</div>
							<div class="p-2">
								<span class="d-block h2 font-weight-bold"><?php echo number_format($leadsByType['Website Forms']['total']); ?></span>
								<p class="d-inline-block mb-0 text-right">
								<?php 
								//calculate diff percentage
									if($prevLeadsByType['Website Forms']['total']==0) 
										$diffPct = round($leadsByType['Website Forms']['total']*100,2);
									else
										$diffPct = round((($leadsByType['Website Forms']['total']-$prevLeadsByType['Website Forms']['total'])/$prevLeadsByType['Website Forms']['total'])*100,2);
									if($diffPct==0){
										$icon = "";
										$textColor = "text-dark";
									}
									else if($diffPct>0){
										$textColor = "text-success";
										$icon = "arrow-up";
									}
									else{
										$icon = "arrow-down";
										$textColor = "text-danger";
									}
									?>
									
										<span class="<?php echo $textColor; ?>"><?php if(!empty($icon)){ ?><i class="fas fa-<?php echo $icon; ?>"></i><?php } ?> <?php echo $diffPct; ?>% Previous Period</span>
								
										<br>
										<span class="small d-block">(<?php echo date("m/d/Y", strtotime($previousFrom))." - ".date("m/d/Y", strtotime($previousTo)); ?>)</span>
								
								</p>
							</div>
							
						</div>
					</div>
					<!--Calls-->
					<div class="col col-lg-2 col-sm-6 col-xl-2 mb-3">
						<div class="h-100 bg-white box-shadow">
							
							<div class="border-bottom p-2">
								<span class="d-block h5 mb-0">Calls</span>
							</div>
							<div class="p-2">
								<span class="d-block h2 font-weight-bold"><?php echo number_format($totalCalls['count']); ?></span>
								<p class="d-inline-block mb-0 text-right">
								<?php 
								//calculate diff percentage
									if($totalCallsPrev['count']==0) 
										$diffPct = round($totalCalls['count']*100,2);
									else
										$diffPct = round((($totalCalls['count']-$totalCallsPrev['count'])/$totalCallsPrev['count'])*100,2);
									if($diffPct==0){
										$icon = "";
										$textColor = "text-dark";
									}
									else if($diffPct>0){
										$textColor = "text-success";
										$icon = "arrow-up";
									}
									else{
										$icon = "arrow-down";
										$textColor = "text-danger";
									}
									?>
									
										<span class="<?php echo $textColor; ?>"><?php if(!empty($icon)){ ?><i class="fas fa-<?php echo $icon; ?>"></i><?php } ?> <?php echo $diffPct; ?>% Previous Period</span>
								
										<br>
										<span class="small d-block">(<?php echo date("m/d/Y", strtotime($previousFrom))." - ".date("m/d/Y", strtotime($previousTo)); ?>)</span>
								
								</p>
							</div>
							
						</div>
					</div>
					<!--Total-->
					<div class="col col-lg-2 col-sm-6 col-xl-2 mb-3">
						<div class="h-100 bg-white box-shadow">
							
							<div class="border-bottom p-2">
								<span class="d-block h5 mb-0">Total Leads</span>
							</div>
							<div class="p-2">
								<span class="d-block h2 font-weight-bold"><?php echo number_format($sum); ?></span>
								<p class="d-inline-block mb-0 text-right">
								<?php 
								//calculate diff percentage
									if($psum==0) 
										$diffPct = round($sum*100,2);
									else
										$diffPct = round((($sum-$psum)/$psum)*100,2);
									if($diffPct==0){
										$icon = "";
										$textColor = "text-dark";
									}
									else if($diffPct>0){
										$textColor = "text-success";
										$icon = "arrow-up";
									}
									else{
										$icon = "arrow-down";
										$textColor = "text-danger";
									}
									?>
									
										<span class="<?php echo $textColor; ?>"><?php if(!empty($icon)){ ?><i class="fas fa-<?php echo $icon; ?>"></i><?php } ?> <?php echo $diffPct; ?>% Previous Period</span>
								
										<br>
										<span class="small d-block">(<?php echo date("m/d/Y", strtotime($previousFrom))." - ".date("m/d/Y", strtotime($previousTo)); ?>)</span>
								
								</p>
							</div>
							
						</div>
					</div>
					<!--Support-->
					<div class="col col-lg-2 col-sm-6 col-xl-2 mb-3">
						<div class="h-100 bg-white box-shadow">
							<div class="border-bottom p-2">
								<span class="d-block h5 mb-0"><i class="fas fa-headset fa-fw mr-1"></i> Support</span>
							</div>
							<?php if (!empty($active_location['rep'])){ 
								$rep = $db->where("id", $active_location['rep'])->getOne ("reps");
							  ?>
								<div class="p-2 border-bottom">
								
									<span class="d-block"><strong>Rep Name:</strong> <?php echo $rep['name']; ?></span>
									<span class="d-block"><strong>Rep Email:</strong> <?php echo $rep['email']; ?></span>
									<span class="d-block"><strong>Rep Phone:</strong> <a href="tel:954-893-8112;<?php echo $rep['phone']; ?>">954-893-8112 x <?php echo $rep['phone']; ?></a></span>
							  
								</div>
							<?php 
							   }
							  ?>
							<div class="py-1 px-2 text-center">
								<a href="/support/" class="text-blue font-weight-bold">Contact Support</a>
							</div>
						</div>
					</div>
				</div>
				<div class="row mb-4 align-items-stretch">
					<div class="col-12 mt-4 mt-md-0">
						<h2 class="text-uppercase h4 mb-0 text-dark d-flex flex-wrap">Your&nbsp;<span class="text-blue font-weight-bold">Leads</span></h2>
						<small class="d-block mb-3">(<?php echo date("m/d/Y",strtotime($ytdate))." - ".date("m/d/Y",strtotime($to)) ?>)</small>
						<canvas class="my-2" id="myChart"></canvas>
					</div>
					
				</div>
				<div class="row align-items-stretch">
					<div class="col-md-6 mt-4 mt-md-0">
						<h2 class="text-uppercase h4 text-dark d-flex flex-wrap">Recent&nbsp;<span class="text-blue font-weight-bold">Lead Activity</span> </h2>
						<?php
						$leadData = $db->rawQuery("SELECT a.date, a.type, a.campid,b.name from ((select date,'Form' as type, campid,CONCAT('9018-',storeid) as  client from form_data  where email not like '%@das-group.com' and email not like '%@test.com' and email not like '%@testing.com' and email not like '%@test.com' and storeid = ?) union (SELECT fl.created_time as date,'Form' as type, '24' as campid,fl.client FROM facebook_lead.lead fl INNER JOIN facebook_lead.leadgen_forms flf ON fl.form_id = flf.face_id where flf.status = 'ACTIVE' and fl.client = ?) union (select datesubmitted as date,'Form' as type, '1' as campid,client from advtrack.loopnetstats where client = ?) union (select start as date,'Call' as type, campid,client from advtrack.calls  where client = ? and caller <> '9548938112' and caller <> '9548373583'  and duplicate <> 1 and duration >= 30 )) a left join advtrack.campid_data b on a.campid=b.campid and a.client = b.client order by date desc limit 10",array($_SESSION['storeid'],$_SESSION['client']."-".$_SESSION['storeid'],$_SESSION['client']."-".$_SESSION['storeid'],$_SESSION['client']."-".$_SESSION['storeid']));
						if( count($leadData)>0 ){
						?>
						
						<table class="table table-sm">
							<thead class="thead-dark">
								<tr>
									<th>Date</th>
									<th>Type</th>
									<th>Campaign</th>
								</tr>
							</thead>
							<tbody>
								<?php
								foreach($leadData as $line){
								?>
								<tr>
									<td><?php echo date("m/d/Y H:i:s",strtotime($line['date'])); ?></td>
									<td><?php echo $line['type']; ?></td>
									<td><?php echo !empty($line['name']) ? $line['name'] : "Organic"; ?></td>
								</tr>
								<?php } ?>
							</tbody>
						</table>
						<?php }else{ echo "<span class='font-italic'>You have no recent lead activity.</span>"; ?>
						<img src="/img/no-leads.png" class="img-fluid d-block m-auto p-3">
						<?php } ?>
						<div class="text-center">
							<a href="/call-stats/<?php echo $url_d;?>" class="btn bg-blue text-white btn-sm font-12 m-auto rounded-pill">View Calls</a>
							<a href="/track/campaign-stats/form_data.php<?php echo $url_d;?>" class="ml-3 btn bg-blue text-white btn-sm font-12 m-auto rounded-pill">View Forms</a>
						</div>
					</div>
					<div class="col-md-6 mt-4 mt-md-0">
						<h2 class="text-uppercase h4 text-dark d-flex flex-wrap">Facebook&nbsp;<span class="text-blue font-weight-bold">Stats</span> </h2>
						<?php
						$dasPost = new Das_Post($db,$_SESSION['client'],$_SESSION['storeid']);	
						$data = $dasPost->getStats($from,$to);
						
						$pdata = $dasPost->getStats($previousFrom,$previousTo);
						?>
						<div class="row justify-content-center mb-4">
							<div class="col col-sm-6 mb-3">
								<div class="h-100 bg-white box-shadow">
									<div class="border-bottom p-2">
										<span class="d-block h5 mb-0">Impressions</span>
									</div>
									<div class="p-2">
										<span class="d-block h2 font-weight-bold"><?php echo number_format($data["imps"]); ?></span>
										<p class="d-inline-block mb-0 text-right">
										<?php 
											if($pdata["imps"]==0) 
												$diffPct = round($data["imps"]*100,2);
											else
												$diffPct = round((($data["imps"]-$pdata["imps"])/$pdata["imps"])*100,2);
											if($diffPct==0){
												$icon = "";
												$textColor = "text-dark";
											}
											else if($diffPct>0){
												$textColor = "text-success";
												$icon = "arrow-up";
											}
											else{
												$icon = "arrow-down";
												$textColor = "text-danger";
											}
											?>
											<span class="<?php echo $textColor; ?>"><?php if(!empty($icon)){ ?><i class="fas fa-<?php echo $icon; ?>"></i><?php } ?> <?php echo $diffPct; ?>% Previous Period</span>
											<br>
											<span class="small d-block">(<?php echo date("m/d/Y", strtotime($previousFrom))." - ".date("m/d/Y", strtotime($previousTo)); ?>)</span>
										</p>
									</div>
								</div>
							</div>
							<div class="col col-sm-6 mb-3">
								<div class="h-100 bg-white box-shadow">
									<div class="border-bottom p-2">
										<span class="d-block h5 mb-0">Clicks</span>
									</div>
									<div class="p-2">
										<span class="d-block h2 font-weight-bold"><?php echo number_format($data["clicks"]); ?></span>
										<p class="d-inline-block mb-0 text-right">
										<?php 
											if($pdata["clicks"]==0) 
												$diffPct = round($data["clicks"]*100,2);
											else
												$diffPct = round((($data["clicks"]-$pdata["clicks"])/$pdata["clicks"])*100,2);
											if($diffPct==0){
												$icon = "";
												$textColor = "text-dark";
											}
											else if($diffPct>0){
												$textColor = "text-success";
												$icon = "arrow-up";
											}
											else{
												$icon = "arrow-down";
												$textColor = "text-danger";
											}
											?>
											<span class="<?php echo $textColor; ?>"><?php if(!empty($icon)){ ?><i class="fas fa-<?php echo $icon; ?>"></i><?php } ?> <?php echo $diffPct; ?>% Previous Period</span>
											<br>
											<span class="small d-block">(<?php echo date("m/d/Y", strtotime($previousFrom))." - ".date("m/d/Y", strtotime($previousTo)); ?>)</span>
										</p>
									</div>
								</div>
							</div>
							
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
		gradientStroke.addColorStop(0, '#0067b1');
		gradientStroke.addColorStop(1, '#0d216a');

		var gradientFill = ctx.createLinearGradient(500, 0, 100, 0);
		gradientFill.addColorStop(0, "rgba(0,103,177, 0.6)");
		gradientFill.addColorStop(1, "rgba(13,33,106, 0.6)");

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
					display: false
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
				},
				aspectRatio: 5
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
			}else{
				var start = moment().subtract(1, 'months');
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