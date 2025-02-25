<!doctype html>
<html lang="en">
  <head>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<link href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.css" rel="stylesheet" type="text/css" />
	<link href="//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
	<link href="//cdn.datatables.net/buttons/1.5.6/css/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <?php 
	include ($_SERVER['DOCUMENT_ROOT']."/includes/head.php"); 
	include ($_SERVER['DOCUMENT_ROOT']."/includes/ClassEssensys.php");
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
	</style>

    <title>Yelp Stats | Local <?php echo CLIENT_NAME; ?></title>
  </head>
  <body class="bg-light cbp-spmenu-push">
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/top.php"); ?>

    <div class="container-fluid">
      <div class="row">
        <?php 
		include ($_SERVER['DOCUMENT_ROOT']."/includes/nav.php"); 
		
        if($_GET['analyticsStartDate'] && $_GET['analyticsEndDate']){
            $from =$_GET['analyticsStartDate'];
            $to = $_GET['analyticsEndDate'];
        }else{
            $from = date("Y-m-d", strtotime("-1 month"));
            $to = date("Y-m-d");
        }
		
		function getLeads($client,$campid,$start,$end){
			$url = 'https://www.adjack.net/api/leads/read.php?client='.$client.'&campid='.$campid.'&start='.$start.'&end='.$end;
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
				<div class="border-bottom-dotted d-flex d-block align-items-center clearfix py-2 px-4">
					<h1 class="h2 font-light mb-0 text-center text-sm-left"><i class="fab fa-yelp mr-2"></i> Yelp Stats</h1>
				</div>
				<div class="py-3 px-4 d-block d-xl-flex align-items-center">
					<a class="small text-blue d-block d-lg-none" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">Advanced Search</a>
					
					<div class="collapse show" id="collapseExample">
					<form name="dates" id="dates" method="get" class="form-inline">
						<div class="d-flex d-xl-inline-block mb-2 mb-xl-0 mr-2 align-items-center">
							<span class="letter-spacing-1 text-uppercase small mr-2 mr-xl-0">FROM:</span>
							<input type="text" name="analyticsStartDate" id="analyticsStartDate" value="<?=$from?>" class="flex-grow d-xl-inline-block form-control form-control-sm w-auto rounded-pill datepicker">
						</div>
						<div class="d-flex d-xl-inline-block mb-2 mb-xl-0 mr-2 align-items-center">
							<span class="letter-spacing-1 text-uppercase small mr-2 mr-xl-0">TO:</span>
							<input type="text" name="analyticsEndDate" id="analyticsEndDate" value="<?=$to?>" class="flex-grow d-xl-inline-block form-control form-control-sm w-auto rounded-pill datepicker">
							<input type="submit" value="Go" class="text-white bg-blue bg-dark-blue-hover flex-grow d-xl-inline-block form-control form-control-sm w-auto rounded-pill">
						</div>
					</form>
					</div>
				</div>

			</div>
			
			<div class="py-3 px-4">
				<?php include $_SERVER['DOCUMENT_ROOT']."/includes/alerts.php"; 
					$forms = $db->query("SELECT client,companyname,sum(num_desktop_search_appearances+num_mobile_search_appearances+billed_impressions) as appearances,sum(num_total_page_views+billed_clicks) as clicks,sum(num_messages_to_business) as messages,sum(num_directions_and_map_views) as directions,sum(ad_cost) as cost FROM advtrack.yelp_metrics a,experimac.locationlist b WHERE client LIKE '".$_SESSION['client']."%' AND start_date BETWEEN '".$from." 00:00:00' AND '".$to." 23:59:59' and a.client=concat('".$_SESSION['client']."-',b.storeid) group by client");
				?>
				<div class="table-responsive">
					<table class="table table-striped" id="yelp_stats_table">
						<thead class="thead-dark">
							<tr>
								<th>Client</th>
								<th>Name</th>
								<th>Search Appearances</th>
								<th>Clicks</th>
								<th>Messages to Business</th>
								<th>Directions & Map Views</th>
								<th>Calls</th>
								<th>Ad Cost</th>
							</tr>
						</thead>
						<tbody>
							<?php 
							foreach($forms as $data){
								$campid = '20';
								$yelp_leads = getLeads($data['client'],$campid,$from,$to);
								$yelp_leads = $yelp_leads->records;
								$tot_leads = 0;
								foreach($yelp_leads as $yelp_lead){ 
									if ($yelp_lead->goal_name == 'Call' ) $tot_leads = $yelp_lead->goals;
								}
							?>
								<tr>
									<td><?php echo $data['client']; ?></td>
									<td><?php echo $data['companyname']; ?></td>
									<td><?php echo number_format($data['appearances'],0); ?></td>
									<td><?php echo number_format($data['clicks'],0); ?></td>
									<td><?php echo $data['messages']; ?></td>
									<td><?php echo $data['directions']; ?></td>
									<td><?php echo $tot_leads; ?></td>
									<td><?php echo '$' . number_format(1.25 * $data['cost'],2);?></td>

								</tr>
							<? } ?>
						</tbody>
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

	<script>
	$(document).ready(function() {
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

		$('#yelp_stats_table').DataTable( {
			responsive: true,
			"pageLength": 25,
			"order": [[ 0, "desc" ]],
			dom: 'B<"clear"><"row"<"col-sm-6"l><"col-sm-6"f>>rt<"row"<"col-sm-6"i><"col-sm-6"p>>',
			buttons: [
				{ extend: 'excel',text: 'Export'},
				'print'
			],
		} );
		
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
   </script>
  </body>
</html>