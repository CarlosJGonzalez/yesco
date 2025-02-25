<!doctype html>
<html lang="en">
<head>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<!--<link rel="stylesheet" href="/css/styles-campaign-stats.css">-->
<!--
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.css" rel="stylesheet" type="text/css" />
<link href="//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<link href="//cdn.datatables.net/buttons/1.5.6/css/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
-->
<!--<link rel="stylesheet" href="/css/monthly.css">-->	  
<link href="//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />

<?php include ($_SERVER['DOCUMENT_ROOT']."/includes/head.php"); ?>
<style>
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
		/*position: fixed;
		left: 0px;
		top: 0px;
		width: 100%;
		height: 100%;
		z-index: 9999;*/
		background: url('../../googleads-php-lib/spinner_preloader.gif') 50% 50% no-repeat rgba(255, 255, 255, 0.8);
	}        
	.google-visualization-table-table td{
		vertical-align: middle  !important;
		text-align:center !important;
	}
	 .google-visualization-table-table td span{
		font-size: 10px !important;
		padding-left: 5px !important;
	 }
	 .google-visualization-table-table td .better_per {
		 color: green;
	 }
	 .google-visualization-table-table td .bad_per {
		 color: red;
	 }
	.google-visualization-table-table tr td:first-child {
			text-align:left !important;
			width: 25%;

	}
	.fa .fa-arrow-down .bad_per{
		  color: red;
	}
	.fa .fa-arrow-up .better_per{
		  color: green;
	}
	.gmnoprint span {
				font-size: 10px !important;			
	}
	table thead th,table thead td {
		color: #fff;
		border-color: #454d55;
		background: #0067b1 !important;
		border-bottom: 2px solid #dee2e6;
	}
	tbody td{
		padding: .5rem !important;
	}
</style>
<title>Geo Performance | Local <?php echo CLIENT_NAME; ?></title>
</head>
  <body class="bg-light cbp-spmenu-push">
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/top.php"); ?>

    <div class="container-fluid">
      <div class="row">
        <?php 
		include ($_SERVER['DOCUMENT_ROOT']."/includes/nav.php"); 
	
		require  __DIR__."/../../googleads-php-lib/config/config.php";
		require  __DIR__."/../../googleads-php-lib/analytics/adwordsData.php";    
		//include($_SERVER['DOCUMENT_ROOT']."/googleads-php-lib/analytics/MysqliDb.php");

		$client_settings = array_merge(["client"=>$_SESSION['client']],$settings[$_SESSION['client']]);
		/*
		echo "<pre>"; print_r($_SESSION);echo "</pre>"; 
		echo "<pre>"; print_r($client_settings);echo "</pre>"; 
		*/
		$store_id = (isset($_GET["storeid"]))?$_GET["storeid"]:$_SESSION['storeid'];
		
		$adwordsModel = new AdwordsData($client_settings, $store_id); 

		$filter =    $_SESSION['client']."-".$store_id;    

	    $date = DateTime::createFromFormat("U",strtotime("first day of last month"));
			
		$s_last_cycle =  isset($client_settings["first_cycle_day"])?        
						$date->modify("+ ".($client_settings["first_cycle_day"]-1)." days") :
						$date;
			
		$e_last_cycle = isset($client_settings["last_cycle_day"])?
						new DateTime(date("Y-m-".$client_settings["last_cycle_day"])):
						(new DateTime())->modify("last day of last month");
			
		$yesterday = DateTime::createFromFormat("U",strtotime( '-1 days' ));
		
		if((new DateTime())  <= $e_last_cycle ){  
			$s_last_cycle -> modify("-1 month");
			$e_last_cycle -> modify("-1 month");
		}

		//validating cycles 
		/*
		if last_day and start_day are in the config file then
		if the months are diferent compare against the previus cycle,
		else compare against the previus month
		*/

		if($_GET["from"] && $_GET["to"]){
			$from = new DateTime($_GET["from"]);
			$to = new DateTime($_GET["to"]);
			$interval = $to->diff($from)->days;        
			$prev_period_to = clone $from;
			$prev_period_to->modify("-1 day");
			$prev_period_from = clone $prev_period_to;
			$prev_period_from = $prev_period_from->sub(new DateInterval("P".$interval."D"));
		}else{
			$from = date("Y-m-d", strtotime("-1 months"));
			$to = date("Y-m-d");
			$start_date = new DateTime($from);
			$end_date = new DateTime($to);
			$interval = $end_date->diff($start_date)->days;
			$prev_period_to = clone $start_date;
			$prev_period_to->modify("-1 day");
			$prev_period_from  =  clone  $prev_period_to;
			$prev_period_from->sub(new DateInterval("P".$interval."D"));
			$from = new DateTime(date('Y-m-01'));
			$to = new DateTime(date('Y-m-d', strtotime('-2 day', time())));
		}
		
		$previous_period = "Previous Period: ".$prev_period_from->format("M, d Y")." to ".$prev_period_to->format("M, d Y");

		$dateRanges = array(
						  [$from ->format("Y-m-d"), $to->format("Y-m-d") ],
						  [$prev_period_from->format("Y-m-d"), $prev_period_to->format("Y-m-d") ]
				  );
		
		   
		if($adwordsModel->getCampaigns($from->format("Y-m-d"),$to->format("Y-m-d"))) {
			$arrayGeoPerformance = $adwordsModel->getGeoPerformance( $dateRanges );   
		}  else{
			error_log( 'Adwords Campaigns were not found. '. __FILE__ );
			$arrayGeoPerformance = [];
		}
		if( !$arrayGeoPerformance || !is_countable( $arrayGeoPerformance ) || count( $arrayGeoPerformance ) == 0 ){
			error_log( 'Adwords Geo Performance were not found. ' . __FILE__ );
		}
		error_log( print_r( $arrayGeoPerformance, true ) );
		$from = $from->format("Y-m-d");
		$to = $to->format("Y-m-d"); 
		$prev_period_from = $prev_period_from->format("Y-m-d");
		$prev_period_to = $prev_period_to->format("Y-m-d");
		?>
        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 p-0">
			<div class="p-0 border-bottom mb-4">
				<div class="d-flex d-block align-items-center clearfix py-2 px-4">
					<h1 class="h2 font-light mb-0 text-center text-sm-left"><i class="fas fa-location-arrow mr-2"></i> Location Performance</h1>
					<div class="ml-auto">
						<div id="reportrange" class="rounded border bg-white py-2 px-3 cursor-pointer rounded-right-0">
							<i class="far fa-calendar-alt"></i>&nbsp;
							<span></span> <i class="fa fa-caret-down"></i>
						</div>
					</div>
				</div>
			</div>
			
<!--
			<div class="p-0 border-bottom mb-4">
				<div class="border-bottom-dotted d-flex d-block align-items-center clearfix py-2 px-4">
					<h1 class="h2 font-light mb-0 text-center text-sm-left"><i class="fas fa-location-arrow mr-2"></i> Location Performance</h1>
				</div>
				<div class="py-3 px-4 d-block d-xl-flex align-items-center">
					<a class="small text-blue d-block d-lg-none" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">Advanced Search</a>
					
					<div class="collapse show" id="collapseExample">
					<form name="dates" id="dates" method="get" class="form-inline">
						<div class="d-flex d-xl-inline-block mb-2 mb-xl-0 mr-2 align-items-center">
							<span class="letter-spacing-1 text-uppercase small mr-2 mr-xl-0">FROM:</span>
							<input type="text" name="analyticsStartDate" value="<?=$from?>" class="flex-grow d-xl-inline-block form-control form-control-sm w-auto rounded-pill datepicker">
						</div>
						<div class="d-flex d-xl-inline-block mb-2 mb-xl-0 mr-2 align-items-center">
							<span class="letter-spacing-1 text-uppercase small mr-2 mr-xl-0">TO:</span>
							<input type="text" name="analyticsEndDate" value="<?=$to?>" class="flex-grow d-xl-inline-block form-control form-control-sm w-auto rounded-pill datepicker">
							<input type="submit" value="Go" class="text-white bg-blue bg-dark-blue-hover flex-grow d-xl-inline-block form-control form-control-sm w-auto rounded-pill">
						</div>
					</form>
					</div>
				</div>

			</div>
-->
			<input type="hidden" value="<?=$filter?>" id ="filter"/> 
			
			<div class="px-4 py-3">
				<?php include $_SERVER['DOCUMENT_ROOT']."/includes/alerts.php"; ?>
				<div class="box p-2 mb-4">
					 <h2 class="text-uppercase h4 text-dark d-flex flex-wrap mb-2">Locations</h2>
					 <div class="position-relative">                    
						<div class="loader" style="min-height:400px;"  id="map"></div>    
					 </div>
				</div>

				<div class="table-responsive styled-table clear">      
				  <div class="position-relative">  
					<div class="loader" id="tb_geo_performance"> </div> 
				  </div>                                       
				</div> 

			</div>
        </main>
      </div>
    </div>

    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>
	<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBtibCaLVowJINTgPUGn3VuXQLZQErBFwA&libraries=geometry" ></script>
	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script> 
	<!--<script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js"></script>-->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/markerclustererplus/2.1.4/markerclusterer.min.js"></script>
	<script type="text/javascript" src="drawMap.js"></script> 
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
	<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
	<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
	<script>
	var map;
	var bounds;
	var target ;

	var url = window.location.protocol + "//" +window.location.hostname;
	var filter = $("#filter").val();		

	$(document).ready(function(){
		var d = new Date();            
		var dateFormat = "mm/dd/yy";
		from = $( "input[name='analyticsStartDate']" )
		.datepicker({
			defaultDate: "-8d",
			changeMonth: true,
			numberOfMonths: 1,
			dateFormat: 'yy-mm-dd',
			maxDate: "-1d"
		})
		.on( "change", function() {
			to.datepicker( "option", "minDate", getDate( this ) );
		});
		to = $( "input[name='analyticsEndDate']" ).datepicker({
			defaultDate: "-1d",
			changeMonth: true,
			numberOfMonths: 1,
			dateFormat: 'yy-mm-dd',
			maxDate: "-1d"
		});

		$(function () {
			$('[data-toggle="tooltip"]').tooltip();
		});
		
		target =  <?=(count($arrayGeoPerformance )> 0) ? json_encode($arrayGeoPerformance):'{}'?>;  
		//console.log('ok');
		//console.log('ok');
		//console.log('ok');
		
		//console.log(target);
		drawMap();
		/*
		geoTable = <?=(count($arrayGeoPerformance ) > 0)?json_encode($arrayGeoPerformance):'{}'?>;
		drawTableLocations();
		*/
		google.charts.load('current', {packages: ['corechart','table'], 'callback':drawTableLocations});	
	});

    function getDate( element ) {
        var date;
        try {
              date = $.datepicker.parseDate( dateFormat, element.value );
        } catch( error ) {
              date = null;
        }
        //console.log(date);
        return date;
    };
    function hideAllInfoWindows(map) {
        markers.forEach(function(marker) {
        marker.infowindow.close(map, marker);
       }); 
    }
    function drawTableLocations(){
		divTableChart = document.getElementById('tb_geo_performance');

		var result = target;

		var rows = [];
		var data = new google.visualization.DataTable();
		data.addColumn('string', 'Location');  
		data.addColumn('number', "<?=date("n/j/y",strtotime($from)).' - <br>'.date("n/j/y",strtotime($to))?>");         
		//data.addColumn('number',  "<?=date("n/j/y",strtotime($prev_period_from)).' - <br>'.date("n/j/y",strtotime($prev_period_to))?>");
		//data.addColumn('number', 'Change(%)');
		data.addColumn('number', "<?=date("n/j/y",strtotime($from)).' - <br>'.date("n/j/y",strtotime($to))?>");
		//data.addColumn('number', "<?=date("n/j/y",strtotime($prev_period_from)).' - <br>'.date("n/j/y",strtotime($prev_period_to))?>"); 
		//data.addColumn('number', 'Change(%)');
		data.addColumn('number',  "<?=date("n/j/y",strtotime($from)).' - <br>'.date("n/j/y",strtotime($to))?>");
		//data.addColumn('number', "<?=date("n/j/y",strtotime($prev_period_from)).' - <br>'.date("n/j/y",strtotime($prev_period_to))?>"); 
		//data.addColumn('number', 'Change');

		$.each( result, function( index, value ){
			var string_clicks = value.clicks ;
			var clicks_1 = parseInt(value.clicks); 
			var clicks_2 = parseInt(value.clicks_2);
			var change_clicks = (clicks_2 !== 0 )?((clicks_1/clicks_2)*100-100).toFixed(2):0;
			
			if( clicks_1 >clicks_2 ){     
				change_clicks = (change_clicks === 0)?100:change_clicks;
			}
			else if(clicks_1 < clicks_2){ 
				change_clicks = (change_clicks === 0)?-100:change_clicks;
			}

			var string_imps = value.imps;
			var imps_1 = parseInt(value.imps);
			var imps_2 = parseInt(value.imps_2);
			var change_imps = (imps_2 !== 0 )?((imps_1/imps_2)*100-100).toFixed(2):0;;

			if(imps_1 > imps_2){
				change_imps =  (change_imps === 0)?100:change_imps;
			}
			else if(imps_1 < imps_2){
				change_imps = (change_imps === 0)?-100:change_imps;
			}

			var ctr_1 = (imps_1 === 0 )?0.00:parseFloat(Number(value.clicks/value.imps*100).toFixed(2));
			var ctr_2 = (imps_2 === 0 )?0.00:parseFloat(Number(value.clicks_2/value.imps_2*100).toFixed(2));
			var change_ctr = ctr_1 - ctr_2 ;

			//data.addRow([value.canonicalName,clicks_1, clicks_2, Number(change_clicks), imps_1, imps_2,Number(change_imps),ctr_1,ctr_2,Number(change_ctr)]);
			data.addRow([value.canonicalName,imps_1,clicks_1,ctr_1]); 				
		});

		var options = { width: '100%', 
						height: '100%',
						page: 'enable',   
						sortColumn:1,
						pageSize:10,
						sortAscending:false,                          
						allowHtml: true
		};
		
		var table = new google.visualization.Table(document.getElementById('tb_geo_performance'));
		
		google.visualization.events.addListener(table, 'ready', function () {			
			var headerRow;
			var newRow;
			// get header row and clone to keep google chart style
			headerRow = divTableChart.getElementsByTagName('THEAD')[0].rows[0];
			var newRow = document.createElement("tr"); 
			// modify new row to combine cells and add labels
			newRow.insertCell(0);
			newRow.insertCell(1);
			newRow.insertCell(2);
			newRow.insertCell(3);

			newRow.className = "google-visualization-table-tr-head";
			newRow.cells[0].innerHTML = '';
			newRow.cells[0].className  = "google-visualization-table-th  unsorted";
			//newRow.cells[1].colSpan = 3;
			newRow.cells[1].innerHTML = 'Impressions';
			newRow.cells[1].className  = "google-visualization-table-th  unsorted";
			//newRow.cells[2].colSpan = 3;
			newRow.cells[2].innerHTML = 'Clicks';
			newRow.cells[2].className  = "google-visualization-table-th  unsorted";
			//newRow.cells[3].colSpan = 3;
			newRow.cells[3].innerHTML = 'CTR';
			newRow.cells[3].className  = "google-visualization-table-th  unsorted";
			$(newRow).insertBefore( headerRow);					
		});

		var formatter_clicks = new google.visualization.NumberFormat({suffix: '%', negativeColor: 'red'});
		//formatter_clicks.format(data, 3); 
		//formatter_clicks.format(data, 6); 
		formatter_clicks.format(data, 3);
		//formatter_clicks.format(data, 8); 
		//formatter_clicks.format(data, 9); 
		//var formatter_arrow = new google.visualization.ArrowFormat();
		//formatter_arrow.format(data, 3); // Apply formatter to second column// Apply formatter to second column
		//formatter_arrow.format(data, 6); // Apply formatter to second column// Apply formatter to second column
		//formatter_arrow.format(data, 9);

		table.draw(data, options);			
		$("#tb_geo_performance").removeClass("loader");
    } 
    function initLocation (){   
        var the_url = "https://www.googleapis.com/geolocation/v1/geolocate?key=<?=GOOGLE_MAPS_API_KEY;?>";//AIzaSyCM7g2WHx0t9jk8tv3Yf-dKlH5kKWeU0ls";

        $.ajax({
            url: the_url,
            dataType: 'json',
            type: 'post',
            success: function(data) {               
                var map = new google.maps.Map(document.getElementById('map'), {
                mapTypeId : google.maps.MapTypeId.ROADMAP,
                center: {lat: data.location.lat, lng: data.location.lng},
                zoom: 10
              });
            }
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
			var start = moment().startOf('month');
			var end = moment().subtract(2, 'days');
		}

		function cb(start, end) {
			//console.log(start);
			//console.log(end);
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