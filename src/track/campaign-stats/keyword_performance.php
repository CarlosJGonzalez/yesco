<!doctype html>
<html lang="en">
  <head>
	<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
	<link rel="stylesheet" href="/css/styles-campaign-stats.css">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<link href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.css" rel="stylesheet" type="text/css" />
	<link href="//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
	<link href="//cdn.datatables.net/buttons/1.5.6/css/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" href="/css/monthly.css">
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/head.php"); ?>
	<?php
    session_start();
    require($_SERVER['DOCUMENT_ROOT']."/googleads-php-lib/analytics/getCampaigns.php");
	
    include($_SERVER['DOCUMENT_ROOT']."/googleads-php-lib/config/config.php");
    $config = $_SERVER["DOCUMENT_ROOT"]."/googleads-php-lib/config/adsapi_php.ini";
    $client = $settings [$_SESSION['client']];
   
    if(($_SESSION['storeid'])!= "-1"){
        $store_id = (isset($_GET["storeid"]))?$_GET["storeid"]:$_SESSION['storeid'];
        $filter = $_SESSION['client']."-".$store_id;
      
        $adwordsModel = new AdwordsModel($config);         
        $adwordsModel->switchCostumerId($client["costumerId"]);

        if($_GET['from'] && $_GET['to']){

        $from =$_GET['from'];
        $to = $_GET['to'];

        $start_date = new DateTime($from);
        $end_date = new DateTime($to);
        $interval = $end_date->diff($start_date)->days;
        $last_day_prev_cycle = clone $start_date;
        $last_day_prev_cycle->modify("-1 day");
        $first_day_prev_cycle  =  clone  $last_day_prev_cycle;
        $first_day_prev_cycle->sub(new DateInterval("P".$interval."D"));
                   
        }else{
			if(date("d") >= 15){
			  $last_day_last_cycle = new DateTime(date("Y-m-14")) ;
			  $first_day_last_cycle = (new DateTime(date("Y-m-15")))->modify('-1 month') ; 

			}else{
			  $last_day_last_cycle = (new DateTime(date("Y-m-14")))->modify('-1 month')  ;
			  $first_day_last_cycle = (new DateTime(date("Y-m-15")))->modify('-2 month') ; 
			}

			//$from = date('Y-m-01');
			//$to = date('Y-m-d', strtotime('-2 day', time()));
			$from = date("Y-m-d", strtotime("-1 months"));
			$to = date("Y-m-d");
			
			$start_date = new DateTime($from);
			$end_date = new DateTime($to);
			$interval = $end_date->diff($start_date)->days;
			$last_day_prev_cycle = clone $start_date;
			$last_day_prev_cycle->modify("-1 day");
			$first_day_prev_cycle  =  clone  $last_day_prev_cycle;
			$first_day_prev_cycle->sub(new DateInterval("P".$interval."D"));
		}
		
        $dateRange = array(
						[$from , $to ],
						[$first_day_prev_cycle->format("Y-m-d"), $last_day_prev_cycle->format("Y-m-d") ]
                    );
					
        $previous_period = "Previous Period: ".$first_day_prev_cycle->format("Y-m-d")." to ".$last_day_prev_cycle->format("Y-m-d");
        $geoPerformance = $targeting = $keywordsPerformance = [];        
        
        $chart_Data = [];
      
        $activeCampaigns = $adwordsModel->getCampaigns($filter,$from,$to );     

        if($activeCampaigns) {
            $tableData = $adwordsModel->getKeywordPerformance($dateRange); 
            /* $start_date = new DateTime($from);
            $end_date = new DateTime($to);
            $interval = $end_date->diff($start_date)->days; 
            $previous_period = $start_date->modify('-'.($interval+1). ' day')->format("Y-m-d")." to ".$start_date->modify('+'.$interval.' day')->format("   Y-m-d");*/
        }      

		//var_dump($tableData);

		/*     
		if(isset($_COOKIE["cm_$filter"])) {
		   $campaigns_list = json_decode($_COOKIE["cm_$filter"]);
		}
		else{
			$campaigns_list = json_decode($adwordsModel->getCampaigns($filter));
		}
		*/
		
		$keywords = $tableData["data"];
		
		$top_five_clicked_keywords = array();
		$top_five_searched_keywords = array();
		
		foreach($keywords as $keyword){
			$keyword_name = $keyword['name'];
			$keyword_clicks = $keyword['clicks_1'];
			$keyword_impressions = $keyword['imps_1'];
			$top_five_searched_keywords[$keyword_name] = $keyword_impressions;
			$top_five_clicked_keywords[$keyword_name] = $keyword_clicks;
		}
		
		arsort($top_five_searched_keywords);
		arsort($top_five_clicked_keywords);
		
		$top_five_searched_keywords = array_keys(array_slice($top_five_searched_keywords,0,5));
		$top_five_clicked_keywords = array_keys(array_slice($top_five_clicked_keywords,0,5));
	}	
?>
   	<style>
		.accordion .button:hover { background: #333 !important; color: #fff !important; text-decoration: none;}
		.cj-a {
            border: 1px solid transparent;
            max-width: 22px;
            padding: 0 5px 4px 0;
            border-radius: 2px;
            cursor: pointer;
        }
        .aw-status-enabled {
         background: transparent url(https://ssl.gstatic.com/awfe30/aw3_cm_20170814_030143_RC3/cm/CC99A8EB0F7970ADCFD3114521B64DB7.cache.png) no-repeat!important;
            margin: 7px 6px 0;
            height: 9px;
            width: 9px;
            overflow: hidden;
        }
        .aw-status-paused {
            background: transparent url(https://ssl.gstatic.com/awfe30/aw3_cm_20170814_030143_RC3/cm/FC669F49D0165D08FF8AAC9718167A8C.cache.png) no-repeat!important;
            margin: 7px 6px 0;
            height: 9px;
            width: 9px;
            overflow: hidden;
        }
        #map_container{
            position: relative;
        }
        #map{
              min-height: 100%;
              overflow: hidden;
              padding-top: 30px;
              position: relative;
              height: 400px;
        }
       .tooltip > .tooltip-inner {
            background-color: #FFFFFF; 
            color: #000000; 
            border: 1px solid red; 
            padding: 15px;
            font-size: 12px;
         }
        .loader {
          	position: fixed;
          	left: 0px;
          	top: 0px;
          	width: 100%;
          	height: 100%;
          	z-index: 9999;
          	background: url('../../googleads-php-lib/spinner_preloader.gif') 50% 50% no-repeat rgb(249,249,249);
         }
         #loading-indicator {
            width:50px;
            height: 50px;
            position:absolute;
            left:50%;
            top:50%;
            margin-top:-25px;
            margin-left:-25px;         
          }
         .better_per {
             color: green;
         }
         .bad_per {
             color: red;
         }
         .hours .day {           
            padding: 5px 5px /*!important*/;           
            
         }
         .hours .day p{
            font-size: 14px /*!important*/;
         }
         .inside_box{
             display:inline-block !important;
             width:45% ;
             align-items: center !important
         }
        .cpl_text{
             font-size: 12px !important;
         }
	</style>

   <title>Keyword Performance | Local <?php echo CLIENT_NAME; ?></title>
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
				<div class="d-flex d-block align-items-center clearfix py-2 px-4">
					<h1 class="h2 font-light mb-0 text-center text-sm-left"><i class="fas fa-keyboard mr-2"></i> Keyword Performance</h1>
					<div class="ml-auto">
						<div id="reportrange" class="rounded border bg-white py-2 px-3 cursor-pointer rounded-right-0">
							<i class="far fa-calendar-alt"></i>&nbsp;
							<span></span> <i class="fa fa-caret-down"></i>
						</div>
					</div>
				</div>
			</div>

			<input type="hidden" value="<?=$filter?>" id ="filter"/> 
			
			<div class="py-3 px-4">
				<?php include $_SERVER['DOCUMENT_ROOT']."/includes/alerts.php"; ?>
				
				<div class="loader" ></div>
				
				<div class="row">
					<div class="col-12 col-lg-6 mb-4">
						<div class="border p-3 bg-white">
							<h2 class="h3 text-blue font-light mb-3"><i class="fas fa-search"></i> Top 5 Keywords Searched</h2>
							<ol>
								<?php foreach($top_five_searched_keywords as $searched_keyword){ ?>			
								<li class="h5 font-light"><?php echo strtolower($searched_keyword); ?></li>
								<?php } ?>
							</ol>
						</div>
					</div>
					<div class="col-12 col-lg-6 mb-4">
						<div class="border p-3 bg-white">
							<h2 class="h3 text-blue font-light mb-3"><i class="fas fa-mouse-pointer"></i> Top 5 Clicked Keywords</h2>
							<ol>
								<?php foreach($top_five_clicked_keywords as $clicked_keyword){ ?>			
								<li class="h5 font-light"><?php echo strtolower($clicked_keyword); ?></li>
								<?php } ?>
							</ol>
						</div>
					</div>
				</div>
				
				<!-- <div class="accordion">-->
				<div class="table-responsive">
					<table class="table ">
						<thead class="thead-dark">
							<tr>
								<th class="text-center">Keyword</th>
								<th class="text-center">Clicks</th>
								<th class="text-center">Imps.</th>
								<th class="text-center">CTR</th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach($keywords as $keyword){
							$keyword_name = $keyword['name'];
							$keyword_clicks = $keyword['clicks_1'];
							$keyword_impressions = $keyword['imps_1'];
							$keyword_ctr = ($keyword_impressions == 0) ? "0.00" : number_format($keyword_clicks / $keyword_impressions * 100, 2)
							?>
							<tr>
								<td class="align-middle"><?php echo strtolower($keyword_name); ?></td>
								<td class="align-middle text-center"><?php echo number_format($keyword_clicks); ?></td>
								<td class="align-middle text-center"><?php echo number_format($keyword_impressions); ?></td>
								<td class="align-middle text-center"><?php echo $keyword_ctr; ?>%</td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
				<!--</div>-->
			</div>
        </main>
      </div>
    </div>

	<?php include ($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>
	<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAqwjm_4bhIDC2zYujGdHLpmVxCuz6KpRg" ></script>
	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>  
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js"></script>
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
	<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
	<script src="//cdn.datatables.net/plug-ins/1.10.19/sorting/datetime-moment.js"></script>
	<script>
	var url = window.location.protocol + "//" +window.location.hostname;
	var filter = $("#filter").val();
	var tableData;
	var global_csv;

	$(window).on('load', function(){
		setTimeout(removeLoader, 2000); //wait for page load PLUS two seconds.
	});  

	// google.charts.setOnLoadCallback(drawCharts);
	$(document).ready(function(){
		$('table').DataTable({
		  "pageLength": 10,
		  "order": [[ 1, 'desc' ]],
			dom: '<"row"<"col-sm-6"l><"col-sm-6 text-right"Bf>>rt<"row"<"col-sm-6"i><"col-sm-6"p>>',
			buttons: [
					{ extend: 'excel',text: 'Export',exportOptions: {
						columns: ':not(.noexport)'
					}},
					'print'
				]
		});
		$(function () {
		  $('[data-toggle="tooltip"]').tooltip();
		});
	});

	function removeLoader(){
		$(".loader").fadeOut(500, function() {
			// fadeOut complete. Remove the loading div
			$(".loader").remove(); //makes page more lightweight 
		}); 
	}

	// Datepicker
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
	// End datepicker

	$('.read').click(function(){
		$(this).siblings('.more').slideToggle();
		$(this).children('.tog').toggleClass('fa-angle-right').toggleClass('fa-angle-down');
	});
	</script>
  </body>
</html>