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

		.dt-buttons{
			margin-bottom:.5rem;
			margin-top:.5rem;
		}

		.dt-buttons > button{
			border-radius: 50rem !important;
			font-size: .875rem;
			line-height: 1.5;
			background-color:#003d4c;
			padding: .25rem 1rem;
			margin-right: .5rem !important;
			border:none;
		}
		#reviewTable th {
			border-bottom: 1px solid #dedede !important;
		}

		#reviewTable td {
			border-bottom: 1px solid #dedede !important;
		}

		.google { color: #DB4437; }
		.yelp { color: #D32323; }
		.facebook {color: #3A559F;}
			
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
		if(!(roleHasPermission('show_reputation_management', $_SESSION['role_permissions']))){
			header('location: /');
		    exit;
		}
	?>

    <title>Review Report | Local <?=$client?></title>
	  
  </head>
  <body class="bg-light cbp-spmenu-push">
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/top.php"); ?>

    <div class="container-fluid">
      <div class="row">
        <?php 
        	include ($_SERVER['DOCUMENT_ROOT']."/includes/nav.php");
		    require_once ($_SERVER['DOCUMENT_ROOT']."/includes/ClassDasReview.php");

			$from = date('Y-01-01 00:00:00');
			$to = date("Y-m-d 23:59:59",strtotime("yesterday"));

			if (!empty($_GET["from"]))
				$from = date("Y-m-d 00:00:00", strtotime($db->escape($_GET["from"])));
			if (!empty($_GET["to"]))
				$to = date("Y-m-d 23:59:59", strtotime($db->escape($_GET["to"])));
			
			$url_d = "?from=".$from."&to=".$to;

				$dasReview = new Das_Review($db,$token_api,$_SESSION['client'],isset($_SESSION['storeid']) ? $_SESSION['storeid'] : null);
			$params = array(
								'gte' => strtotime($from),				
								'lte' => strtotime($to)
							);

			$review_info = $dasReview->getReviewsStats($params);	
			$rating = round($review_info['data']["avg"],2);

			$by_star_rating = array_column($review_info['data']['by_star'],'rating');
			$by_star_qtt 	= array_column($review_info['data']['by_star'],'qtt');
			$by_source_portal = array_column($review_info['data']['by_source'],'portal');
			$by_source_qtt = array_column($review_info['data']['by_source'],'qtt');

			$google_link   = $dasReview->getLinkGoogle();
			$facebook_link = $dasReview->getLinkFB();			

			if($facebook_link != ""){
				$fb_html ='&#x3C;a href=&#x22;'.$facebook_link.'&#x22; target=&#x22;_blank&#x22;&#x3E;&#x3C;img src=&#x22;'.LOCAL_CLIENT_URL.'img/icon-fb.png&#x22;/&#x3E;&#x3C;/a&#x3E;';
				$fb_html_view = '<a href="'.$facebook_link.'" target="_blank"><img src="'.LOCAL_CLIENT_URL.'img/icon-fb.png"/></a>';
			}
			if($google_link != "" ){
				$goo_html='&#x3C;a href=&#x22;'.$google_link.'&#x22; target=&#x22;_blank&#x22;&#x3E;&#x3C;img src=&#x22;'.LOCAL_CLIENT_URL.'img/icon-google.png&#x22;/&#x3E;&#x3C;/a&#x3E;';
				$goo_html_view = '<a href="'.$google_link.'" target="_blank"><img src="'.LOCAL_CLIENT_URL.'img/icon-google.png"/></a>';
			}
		 ?>
        <div id="spinner_loading" class="none_upload loader"></div>
        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 p-0">			

			<div class="p-0 border-bottom mb-4">
				<div class="d-flex d-block align-items-center clearfix py-2 px-4">
					<h1 class="h2 font-light mb-0 text-center text-sm-left"><i class="fas fa-calendar-alt mr-2"></i> Review Report</h1>
					<div class="ml-auto">
						<div id="reportrange" class="rounded border bg-white py-2 px-3 cursor-pointer rounded-right-0">
							<i class="far fa-calendar-alt"></i>&nbsp;
							<span></span> <i class="fa fa-caret-down"></i>
						</div>
					</div>
				</div>
			</div>
			<?php include $_SERVER['DOCUMENT_ROOT']."/includes/alerts.php"; ?>
			<div class="py-3 px-4">
				

				<!-- Nav tabs -->
				<ul class="nav nav-tabs" role="tablist">
					<li class="nav-item">
						<a class="nav-link text-blue active" data-toggle="tab" href="#tabs-overview">Overview</a>
					</li>
					<li class="nav-item">
						<a class="nav-link text-blue" data-toggle="tab" href="#tabs-reviews">Reviews</a>
					</li>
				</ul>

				<div class="tab-content p-2">

					<div id="tabs-reviews" class="tab-pane fade">
						<h2 class="font-light mb-3">Reviews</h2>
						<div class="row">
						<div class="col-md-4 col-lg-3 col-xl-2">
								<span class="text-muted d-block text-uppercase letter-spacing-1 font-weight-bold">Filters</span>
								<input type="hidden" id="source_filter" name="source_filter" value="">
								<div class="my-2">
									<span class="d-block mb-2">Source</span>
									<?php foreach ($by_source_portal as $key => $portal) { ?>
										<div class="custom-control custom-checkbox">
											<input type="checkbox" class="custom-control-input source_filter" id="filter_<?php echo $portal;?>" data-source="<?php echo $portal;?>" name="filter_portal" >

											<label class="custom-control-label d-flex align-items-center" for="filter_<?php echo $portal;?>">
											  	<?php echo $portal;?> 
									  			<span class="ml-auto bg-secondary px-2 rounded text-white text-uppercase small rounded-pill d-inline-block letter-spacing-1">
									  				<?php 
									  					echo $by_source_qtt[$key];
									  				?>
								  				</span>
							  				</label>
										</div>
									<?php } ?>
								</div>	

								<br>
								<div class="my-2">
									<span class="d-block mb-2">Star Rating</span>
									<input type="hidden" id="rating_filter" name="rating_filter" value="">
									<?php for( $i = 5; $i > 0; $i-- ){ ?>
										<div class="custom-control custom-checkbox">
										  	<input type="checkbox" class="custom-control-input rating_filter" id="rating<?php echo $i;?>" data-rating="<?php echo $i;?>"  name="rating">
										  	<label class="custom-control-label d-flex align-items-center" for="rating<?php echo $i;?>">
											  	<?php for( $x = 0; $x < $i; $x++ ){ ?>
											  		<i class="fas fa-star text-warning"></i>
										  		<?php } ?> 
									  			<span class="ml-auto bg-secondary px-2 rounded text-white text-uppercase small rounded-pill d-inline-block letter-spacing-1">
									  				<?php 
									  				
									  				$stars_post = array_search ($i.' Stars', $by_star_rating);
							  				
									  				if ( $stars_post !== false  ){
									  					echo $by_star_qtt[$stars_post];
									  				}else{
									  					echo 0;	
									  				}
									  				?>
								  				</span>
							  				</label>
										</div>
									<?php } ?>
								</div>
							</div>
							<!--- End Filters -->
						<div class="col-md-8 col-lg-9 col-xl-10">
							<?php if((roleHasPermission('show_export_btn', $_SESSION['role_permissions']))){ ?>
								<div class="py-2">
									<div class="row">
										<div class="col col-lg-2 col-sm-6 col-xl-2text-center">
											<button type="button" id="export_btn" class="btn btn-secondary">
												<i class="fa fa-download" aria-hidden="true"></i>
												Export
											</button>

										</div>
									</div>
								</div>
							<?php } ?>
							<div class="table-responsive">					
								<table id="reviewTable" class="display table table-striped dataTable" style="width:100%">
									<thead>
										<tr>
											<th class="p-3 sorting">Date</th>
											<th class="p-3">StoreId</th>
											<th class="p-3">Source</th>
											<th class="p-3 sorting">Rating</th>
											<th class="p-3">Review</th>
										</tr>
									</thead>
								</table>
							</div>
						</div>
						</div>
					</div>

					<div class="tab-pane active" id="tabs-overview">
						<h2 class="font-light mb-3">Overview</h2>
						<div class="row pb-4">
							<div class="col-12 col-lg-3">
								<h3 class="h5 text-blue">Rating & Reviews</h3>
								<div class="box py-4 bg-blue mb-4 text-center text-white">
									<p class="h6 font-light">Average Star Rating</p>
									<p class="h1 py-4"><?php echo $rating;?></p>
									<span class="h5 text-yellow">
										<?php 
											if($rating > 0 ){											
												for ($i = 1; $i <= 5; $i++) {
													if($i <= $rating){
													?>
														<i class="fas fa-star"></i>
													<?php 
													}else{ ?>
														<i class="fas fa-star-half"></i>

											<?php  break;} } }?>
									</span>
								</div>
								<div class="box py-4 bg-light-custom mb-4 border text-center">
									<p class="h6">Total Reply / Total Reviews</p>
									<p class="h1 text-blue"><?php echo round($review_info['data']["qtt_reply"]). ' / '.round($review_info['data']["qtt"])?></p>
								</div>
							</div>
							<div class="col-12 col-lg-9">
								<h3 class="h5 text-blue">Collected Reviews</h3>
								<canvas class="my-2" id="allReviews" height="100"></canvas>
							</div>
						</div>
						<div class="row">
							<div class="col-4 col-xl-4">
								<div class="box p-3">
									<h3 class="h5 text-blue underline">Reviews by Star Rating</h3>
									<canvas class="my-2" id="star" width="400" height="400"></canvas>
								</div>
							</div>
							<div class="col-4 col-xl-4">
								<div class="box p-3">
									<h3 class="h5 text-blue underline">Reviews by Source</h3>
									<canvas class="my-2" id="source" width="400" height="400"></canvas>
								</div>
								
							</div>
							<div class="col-4 col-xl-4">
								<div class="box p-3">
									<h3 class="h5 text-blue underline">Reply by Source</h3>
									<canvas class="my-2" id="reply" width="400" height="400"></canvas>
								</div>
								
							</div>
						</div>
					</div>
					
					

					  <!--Respond-->
					  <form action="xt_reply_review.php" id="frm_reply" method="POST">
						<div class="modal fade" id="respondModal" tabindex="-1" role="dialog" aria-labelledby="respondModalLabel" aria-hidden="true">
						  <div class="modal-dialog" role="document">
							<div class="modal-content">
							  <div class="modal-header">
								<h5 class="modal-title d-inline-block h4">Respond to Review</h5>
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								  <span aria-hidden="true">&times;</span>
								</button>
							  </div>
							  <div class="modal-body">
								  <textarea class="form-control" name="txt_reply" required></textarea>
							  </div>
							  <div class="modal-footer">
								<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
								<button type="button" class="btn btn bg-blue text-white" id="btn_reply">Send</button>
							  </div>
							</div>
						  </div>
						</div>
					  </form>

				</div>
			</div>
		</main>
      </div>
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
	<script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
	<script src="//cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
	<script src="//cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>
	<script src="//cdn.datatables.net/buttons/1.5.6/js/buttons.colVis.min.js "></script>
	<script src="//cdn.datatables.net/select/1.2.2/js/dataTables.select.min.js"></script>
	<script src="https://www.adjack.net/validate-files-js/validate-files.js"></script>

<script type="text/javascript">		    
			    
	//
	var ltx = document.getElementById("allReviews");
	
	var myLineChart = new Chart(ltx, {
		type: 'line',
		data: {
			labels: [<?php echo '"'.implode('","', array_column($review_info['data']["by_month"],'date_group')) . '"';?>],
			datasets: [{ 
				data: [<?php echo implode(',', array_column($review_info['data']["by_month"],'qtt'));?>],
				label: "Reviews",
				borderColor: "#de1737",
				fill: true,
				backgroundColor: "rgba(101,12,26, 0.6)"
			  }
			]
		  },
		  options: {
			legend: {
				display: false
			}
		  }
	});
	
		//Reviews by Star
		var ptx = document.getElementById("star");
		var myPieChart = new Chart(ptx,{
			type: 'doughnut',
			data: {
				datasets: [{					
					data: [<?php echo implode(',', $by_star_qtt);?>],

					backgroundColor:  [
						"#de1737",
						"#ab112a",
						"#650c1a",
						"#000000",
						"#525252",
						"#aaaaaa",
						"#efefef"
					]
				}],

				// These labels appear in the legend and in the tooltips when hovering different arcs
				labels: [<?php echo '"'.implode('","', $by_star_rating) . '"';?>],
			
			},
			options: {
				legend: {
					position: "right"
				}
			}
		});
		
		//Reviews by Source
		var ptx = document.getElementById("source");
		var myPieChart = new Chart(ptx,{
			type: 'doughnut',
			data: {
				datasets: [{
					data: [<?php echo implode(',', $by_source_qtt);?>],

					backgroundColor:  [
						"#de1737",
						"#ab112a",
						"#650c1a",
						"#000000",
						"#525252",
						"#aaaaaa",
						"#efefef"
					]
				}],

				// These labels appear in the legend and in the tooltips when hovering different arcs
				labels: [<?php echo '"'.implode('","', $by_source_portal) . '"';?>],
			
			},
			options: {
				legend: {
					position: "right"
				}
			}
		});

		//Reviews by reply
		var ptx = document.getElementById("reply");
		var myPieChart = new Chart(ptx,{
			type: 'doughnut',
			data: {
				datasets: [{
					data: [<?php echo implode(',', array_column($review_info['data']['reply_source'],'qtt'));?>],

					backgroundColor:  [
						"#de1737",
						"#ab112a",
						"#650c1a",
						"#000000",
						"#525252",
						"#aaaaaa",
						"#efefef"
					]
				}],

				// These labels appear in the legend and in the tooltips when hovering different arcs
				labels: [<?php echo '"'.implode('","', array_column($review_info['data']['reply_source'],'portal')) . '"';?>],
			
			},
			options: {
				legend: {
					position: "right"
				}
			}
		});

	function fetch_data(start_date, end_date,filter_star = '',filter_portal = ''){
		var userTable = $('#reviewTable').DataTable({
			'processing': true,
			'responsive': true,
			'serverSide': true,
			"pageLength": 9,
			"lengthMenu": [[9,90,120,240],[9,90,120,240]],
			'serverMethod': 'POST',
			'ajax': {
			  'url':'xt_review.php',
			   data:{start_date:start_date, end_date: end_date,filter_star : filter_star,filter_portal : filter_portal }
			},
			'searching': false,
			"order": [[ 0, "desc" ]],
			'columns': [
					{ data: 'date' },
					{ data: 'storeid' },
					{ data: 'source' },
					{ data: 'rating' },
					{ data: 'review' },
			],
			columnDefs: [
			             {
			                 targets: [1],
			                 orderable: false,
			                 searchable: false,
			             }
		             ],
             'rowCallback': function(row, data, index){
             	$(row).find('td:eq(0)').addClass('p-3');
             	$(row).find('td:eq(1)').addClass('p-3');
             	$(row).find('td:eq(2)').addClass('p-3');
             	$(row).find('td:eq(3)').addClass('p-3 text-yellow');
             	$(row).find('td:eq(4)').addClass('p-3');
             	$(row).find('td:eq(5)').css('max-width','600px');

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
				var start = moment().startOf('year');
				var end = moment().subtract(1, 'days');
			}

			$('#export_btn').on('click',function(e){
				$('#spinner_loading').removeClass("none_upload");

				ajaxRequest= $.ajax({
		            url: "xt_export_data.php",
		            type: "POST",
		            data:{start_date:start.format('MM/DD/YYYY'), end_date:end.format('MM/DD/YYYY'),filter_star : $('#rating_filter').val(),filter_portal : $('#source_filter').val() }
		        });


			    ajaxRequest.done(function (response, textStatus, jqXHR){
			    		$('#spinner_loading').addClass("none_upload");
			    		//download (response, 'people.xls')
			    	    var hiddenElement = document.createElement('a');
					    hiddenElement.href = response;
					    hiddenElement.target = '_blank';
					    hiddenElement.download = 'ReviewsDownload' + new Date()+'.xls';
					    hiddenElement.click();
			    });

			    ajaxRequest.fail(function (){
			    	$('#spinner_loading').addClass("none_upload");
			        $("#result").html('There is error while submit');
			    });
			});

			$('.rating_filter').on('change',function(e){
				e.preventDefault();		
				var rating_select = $(this).data("rating");
				if( $(this).is(':checked') ){
					$('#rating_filter').val( $('#rating_filter').val() + rating_select + ',');
				}else{
					$('#rating_filter').val( $('#rating_filter').val().replace( rating_select + ',',''));
				}

				$('#reviewTable').DataTable().destroy();
				fetch_data(start.format('MM/DD/YYYY'), end.format('MM/DD/YYYY'),$('#rating_filter').val(),$('#source_filter').val());
			});



			$('.source_filter').on('change',function(e){
				e.preventDefault();		
				var source_select = $(this).data("source");

				if( $(this).is(':checked') ){
					$('#source_filter').val( $('#source_filter').val() + source_select + ',');
				}else{
					$('#source_filter').val( $('#source_filter').val().replace( source_select + ',',''));
				}

				$('#reviewTable').DataTable().destroy();
				fetch_data(start.format('MM/DD/YYYY'), end.format('MM/DD/YYYY'),$('#rating_filter').val(),$('#source_filter').val());				
			});


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
		$(".vCode").on('click', function (e) {
			$(this).siblings("code").slideToggle();
		});

		$('#respondModal').on('hide.bs.modal', function (e) {		
			$("input[name='review_id']").remove();
			$("input[name='review_portal']").remove();	
		});

		$("#btn_reply").click(function(e){
			if(($("textarea[name='txt_reply']").val() === "") || 
			($("textarea[name='txt_reply']").length === 0) || 
			($("textarea[name='txt_reply']").val() === "undefined" )){
				return false;
			}
			$('<input />').attr('type', 'hidden')
					  .attr('name', "xt")
					  .attr('value', "reply")
					  .appendTo('#frm_reply');
					  
			//console.log($("#frm_reply").serialize());
			$("#frm_reply").submit();
			
		});	
		
		$(document).on('click','.respondModal', function (e) {
			e.stopPropagation();
			console.log(this.dataset["id"]);
			$('<input />').attr('type', 'hidden')
				  .attr('name', "review_id")
				  .attr('value',this.dataset["id"])
				  .appendTo('#frm_reply');
			$('<input />').attr('type', 'hidden')
				  .attr('name', "review_portal")
				  .attr('value',this.dataset["portal"])
				  .appendTo('#frm_reply');
				  
			$('#respondModal').modal("show");
			
		});
	
	
</script>
  </body>
</html>