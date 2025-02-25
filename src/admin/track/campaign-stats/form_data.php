<!doctype html>
<html lang="en">
  <head>
	<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.21/b-1.6.3/b-html5-1.6.3/datatables.min.css"/>
    <?php 
		include ($_SERVER['DOCUMENT_ROOT']."/includes/head.php");
		include ($_SERVER['DOCUMENT_ROOT'].'/includes/ClassDasWebsite.php');	
	?>
    <title>Form Data | Local <?php echo CLIENT_NAME; ?></title>
  </head>
  <body class="bg-light cbp-spmenu-push">
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/top.php"); ?>

    <div class="container-fluid">
      <div class="row">
        <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/nav.php");?>
		
        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 p-0">
			
			<div class="p-0 border-bottom mb-4">
				<div class="d-flex d-block align-items-center clearfix py-2 px-4">
					<h1 class="h2 font-light mb-0 text-center text-sm-left"><i class="far fa-desktop-alt mr-2"></i> Form Data</h1>
					<div class="ml-auto d-flex align-items-center">
						<div id="reportrange" class="rounded border bg-white py-2 px-3 cursor-pointer rounded-right-0">
							<i class="far fa-calendar-alt"></i>&nbsp;
							<span></span> <i class="fa fa-caret-down"></i>
						</div>
					</div>
				</div>
				
			</div>

			
			<div class="py-3 px-4">
				<?php include $_SERVER['DOCUMENT_ROOT']."/includes/alerts.php"; 
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

				$qforms = $db->rawQuery("select campid,count(*) as count from form_data  where (date between ? and ? and email not like '%@das-group.com' and email not like '%@test.com' and email not like '%@testing.com' and email not like '%@test.com' AND `http_referer` IS NULL ) group by campid",array($from,$to));
				$local_stats = $db->rawQuery("SELECT '24' as campid,count(*) FROM facebook_lead.lead fl INNER JOIN facebook_lead.leadgen_forms flf ON fl.form_id = flf.face_id where flf.status = 'ACTIVE' and fl.client like '9018%' and fl.created_time between ? and ? group by campid",array($from,$to,$from,$to));

				$qLeads = array_merge($qforms,$local_stats); 
				$leadsByCamp = array_combine(array_column($qLeads, 'campid'), $qLeads);

				$sum = 0;
				foreach ($qLeads as $item) {
					$sum += $item['count'];
				}
				 //get previous forms
				$prevqforms = $db->rawQuery("select campid,count(*) as count from form_data where ( date between ? and ? and email not like '%@das-group.com' and email not like '%@test.com' and email not like '%@testing.com' and email not like '%@test.com' AND `http_referer` IS NULL ) group by campid",array($previousFrom,$previousTo));
				$prev_local_stats = $db->rawQuery("SELECT '24' as campid,count(*) FROM facebook_lead.lead fl INNER JOIN facebook_lead.leadgen_forms flf ON fl.form_id = flf.face_id where flf.status = 'ACTIVE' and fl.client like '9018%' and fl.created_time between ? and ? group by campid",array($previousFrom,$previousTo,$previousFrom,$previousTo));

				$prevqLeads = array_merge($prevqforms,$prev_local_stats);
				$prevLeadsByCamp = array_combine(array_column($prevqLeads, 'campid'), $prevqLeads);
//				echo "<pre>";var_dump($prevLeadsByCamp);echo "</pre>";
				$prevsum = 0;
				foreach ($prevqLeads as $item) {
					$prevsum += $item['count'];
				}
				
				?>
				<div class="row justify-content-center mb-4">
					<!--Organic Leads -->
					<div class="col col-lg-3 col-sm-6 mb-3">
						<div class="h-100 bg-white box-shadow">
							<div class="border-bottom p-2">
								<span class="d-block h5 mb-0">Organic</span>
							</div>
							<div class="p-2">
								<span class="d-block h2 font-weight-bold"><?php echo number_format($leadsByCamp["0"]["count"]); ?></span>
								<p class="d-inline-block mb-0 text-right">
								<?php 
								//calculate diff percentage
								if(isset($prevLeadsByCamp["0"])) {
									$diffPct = round((($leadsByCamp["0"]["count"]-$prevLeadsByCamp["0"]["count"])/$prevLeadsByCamp["0"]["count"])*100,2);
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
								<?php }else{ ?>
									<span class="text-black">No results from previous period</span>
								<?php } ?>
										<br>
										<span class="small d-block">(<?php echo date("m/d/Y", strtotime($previousFrom))." - ".date("m/d/Y", strtotime($previousTo)); ?>)</span>
								
								</p>
							</div>
							
						</div>
					</div>
					<?php
					//current
					unset($leadsByCamp["0"]);
					//	echo "<pre>";var_dump($leadsByCamp);echo "</pre>";
					$paid = 0;
					foreach ($leadsByCamp as $item) {
						$paid += $item['count'];
					}
					unset($prevLeadsByCamp["0"]);
					$prevpaid = 0;
					foreach ($prevLeadsByCamp as $item) {
						$prevpaid += $item['count'];
					}
					?>
					<!--Paid Campaigns -->
					<div class="col col-lg-3 col-sm-6 mb-3">
						<div class="h-100 bg-white box-shadow">
							<div class="border-bottom p-2">
								<span class="d-block h5 mb-0">Paid Campaigns</span>
							</div>
							<div class="p-2">
								<span class="d-block h2 font-weight-bold"><?php echo number_format($paid); ?></span>
								<p class="d-inline-block mb-0 text-right">
								<?php 
								//calculate diff percentage
									$diffPct = round((($paid-$prevpaid)/$prevpaid)*100,2);
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
					<div class="col col-lg-3 col-sm-6 mb-3">
						<div class="h-100 bg-white box-shadow">
							<div class="border-bottom p-2">
								<span class="d-block h5 mb-0">Total Forms</span>
							</div>
							<div class="p-2">
								<span class="d-block h2 font-weight-bold"><?php echo number_format($sum); ?></span>
								<p class="d-inline-block mb-0 text-right">
								<?php 
								//calculate diff percentage
									$diffPct = round((($sum-$prevsum)/$prevsum)*100,2);
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
				
				<div class="table-responsive">
					<table class="table" id="book_tourTable">
						<thead class="thead-dark">
							<tr>
								<th class="min-mobile">Date</th>
								<th class="min-mobile">Location</th>
								<th class="min-mobile">Name</th>  
								<th class="min-mobile">Email</th>                                          
								<th class="min-mobile">Phone Number</th>
								<th class="min-mobile">Type</th>
								<th class="min-mobile">Comments</th>
								<th class="min-mobile">Campaign</th>
							</tr>
						</thead>
						<tbody>
							<?php 
							$qforms = $db->rawQuery("select * from form_data where ( date between '".$from."' and '".$to."' and email not like '%@das-group.com' and email not like '%@test.com' and email not like '%@testing.com' and email not like '%@test.com' AND `http_referer` IS NULL )");
							$local_stats = $db->rawQuery(" SELECT fl.created_time as date,replace(fl.client,'9018-','') as storeid,fl.first_name,fl.last_name,fl.email,fl.phone_number as phone,'Lead' as type,'24' as campid, '' as ip_address FROM facebook_lead.lead fl INNER JOIN facebook_lead.leadgen_forms flf ON fl.form_id = flf.face_id where flf.status = 'ACTIVE' and fl.client like '".$_SESSION['client']."%' and fl.created_time between '".$from."' and '".$to."'");
							
							$qLeads = array_merge((array)$qforms,(array)$local_stats);

							foreach($qLeads as $form){ 
								
								$campaign = "";
								if (!empty($form['campid']))
									$campaign = $db->where("campid",$form['campid'])->where("client","9018")->getOne("advtrack.campid_data");
	
								$location = $db->where("storeid",$form['storeid'])->getOne("fullypromoted.locationlist",null,array("companyname,zip"));
								$date = convertDateTime($db,$location['zip'],$form['date'],'m/d/Y h:i a');
								$companyname = ($location['storeid']) ? $location['companyname']." (".$form['storeid'].")" : 'Corporate';
								?>
								<tr data-id="<?php echo $form['id']?>">
									<td><?php echo $date; ?></td>
									<td><?php echo $companyname;?></td>
									<td><?php echo $form['first_name'].' '.$form['last_name']; ?></td>
									<td><?php echo $form['email']; ?></td>
									<td><?php echo format_phone($form['phone']); ?></td>
									<td><?php echo $form['type']; ?></td>
									<td><?php echo stripcslashes($form['comments']); ?></td>
									<td><?php echo !empty($campaign['name']) ? $campaign['name'] : "Organic"; ?></td>
								</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
			</div>
        </main>
      </div>
    </div>

    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>
	<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
	<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
	<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.21/b-1.6.3/b-html5-1.6.3/datatables.min.js"></script>
	<script src="//cdn.datatables.net/plug-ins/1.10.20/sorting/datetime-moment.js"></script>

	<script>
		$(document).ready( function () {
			$.fn.dataTable.moment('MM/DD/YYYY hh:mm a');
		var table = $('#book_tourTable').DataTable( {
	        responsive: true,
            "pageLength": 50,
			dom: '<"row"<"col-sm-6"l><"col-sm-6 text-right"Bf>>rt<"row"<"col-sm-6"i><"col-sm-6"p>>',
	        buttons: [
					{ extend: 'excel',text: 'Export'},
				],
			"order": [[ 0, "desc" ]]
	    } );
	 
	    table.buttons().container()
	        .appendTo( '#book_tourTable_wrapper .col-md-6:eq(0)' );
		
        var dateFormat = "mm/dd/yy",
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
		
		} );
		
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