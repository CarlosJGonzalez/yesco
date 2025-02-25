<!doctype html>
<html lang="en">
  <head>
	  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
	  <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" />

    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/head.php"); 
	  include ($_SERVER['DOCUMENT_ROOT']."/includes/ClassDasWebsite.php"); 
	  $from = date("Y-m-d", strtotime("-1 months"));
		$to = date("Y-m-d");

		if (!empty($_GET["from"]))
			$from = date("Y-m-d", strtotime($db->escape($_GET["from"])));
		if (!empty($_GET["to"]))
			$to = date("Y-m-d", strtotime($db->escape($_GET["to"])));
	  ?>

    <title>Form Data | <?php echo CLIENT_NAME; ?></title>
  </head>
  <body class="bg-light cbp-spmenu-push">
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/top.php"); ?>

    <div class="container-fluid">
      <div class="row">
        <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/nav.php"); ?>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 p-0 mb-4 dashboard">
			<div class="p-0 border-bottom mb-4">
				<div class="border-bottom-dotted d-flex d-block align-items-center clearfix py-2 px-4">
					<h1 class="h2 font-light mb-0 text-center text-sm-left"><i class="fas fa-table mr-2"></i> Form Data</h1>
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
						<div class="d-flex d-xl-inline-block mb-2 mb-xl-0 mr-2 align-items-center">
							<span class="letter-spacing-1 text-uppercase small mr-2 mr-xl-0">Form:</span>
							<select name="col7_filter" class="flex-grow d-xl-inline-block form-control form-control-sm w-auto rounded-pill custom-select-arrow pr-4 column_filter_select" data-column="7">
								<option value="">All Forms</option>
								<?php $forms = $db->orderBy("name")->get("form_types");
								foreach($forms as $form){
								?>
								<option value="<?php echo $form['name']; ?>"><?php echo $form['name']; ?></option>
								<?php } ?>
							</select>
						</div>

					</div>
						
					
				</div>
			</div>

			
			
        	<div class="px-4 py-3">
				<?php include $_SERVER['DOCUMENT_ROOT']."/includes/alerts.php"; ?>
				<?php
				$forms = $db->where("date",array($from." 00:00:00",$to." 23:59:59"),"BETWEEN")->get("form_data");

				$local_stats = $db->rawQuery("(SELECT fl.created_time as date,replace(fl.client,'9018-','') as storeid,fl.full_name as name,fl.email as email,fl.phone_number as phone,'Lead' as type,'24' as campid, '' as ip_address FROM facebook_lead.lead fl INNER JOIN facebook_lead.leadgen_forms flf ON fl.form_id = flf.face_id where flf.status = 'ACTIVE' and fl.client like '".$_SESSION['client']."%' and fl.created_time between '".$from."' and '".$to."') union (select datesubmitted as date,replace(client,'9018-','') as storeid,concat(senderfirstname,' ',senderlastname) as name,senderemail as email,senderphonenumber as phone,'Book a tour' as type,'11' as campid, '' as ip_address from advtrack.loopnetstats where client like '".$_SESSION['client']."%' and datesubmitted between '".$from." 00:00:00' and '".$to." 23:59:59')");

				$leads = array_merge($forms,$local_stats);
				
				?>

				<table class="table table-striped">
					<thead class="thead-dark">
						<th>Date</th>
						<th>Location</th>
						<th>Name</th>
						<th>Email</th>
						<th>Phone</th>
						<th>Product</th>
						<th>Comments</th>
						<th>Type</th>
						<th>Campid</th>
					</thead>
					<tbody>
						<?php foreach($leads as $form){
							$campaign = null;
							$clientcampid = !empty($form['storeid']) ? $_SESSION['client'].'-'.$form['storeid'] : $_SESSION['client'];
							$loc = $db->where("storeid",$form['storeid'])->getOne("locationlist",array("companyname"));
							if (!empty($form['campid'])){
								$campaign = $db->where("campid",$form['campid'])->where("client",$clientcampid)->getOne("advtrack.campid_data");
							}
						?>
						<tr>
							<td>
								<?php
								$date = new DateTime($form['date'], new DateTimeZone('UTC'));
								$date->setTimezone(new DateTimeZone('EST'));
								echo $date->format('m/d/Y g:i A'); ?>
							</td>
							<td><?php echo !empty($form['storeid']) ? $loc["companyname"]." (".$form['storeid'].")" : "Corporate"; ?></td>
							<td><?php echo $form['first_name']." ".$form['last_name']; ?></td>
							<td><?php echo $form['email']; ?></td>
							<td><?php echo $form['phone']; ?></td>
							<td><?php echo $form['product']; ?></td>
							<td><?php echo nl2br(stripcslashes($form['comments'])); ?></td>
							<td><?php echo $form['type']; ?></td>
							<td><?php echo !empty($campaign['name']) ? $campaign['name'] : "Organic"; ?></td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
			

        
        </main>
      </div>

    </div>


    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>
	<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
	  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
	<script type="text/javascript" src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
	  <script type="text/javascript" src="//cdn.datatables.net/plug-ins/1.10.19/sorting/datetime-moment.js"></script>
	  
    <script>
		$(document).ready( function () {
			$.fn.dataTable.moment('MM/DD/YYYY H:mm A');
			$('table').DataTable({
				"order": [[ 0, "desc" ]]
			});
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
		$('select.column_filter_select').on( 'change', function () {
			filterColumnSelect( $(this).attr('data-column') );
		} );
		function filterColumnSelect ( i ) {
			var searchStr;
			if($('select[name="col'+i+'_filter"] option:selected').val()=="")
				searchStr = $('select[name="col'+i+'_filter"] option:selected').val();
			else
				searchStr = "^"+$('select[name="col'+i+'_filter"] option:selected').val()+"$";
			$('table').DataTable().column( i ).search(searchStr, true, false, true).draw();
		}
    </script>
  </body>
</html>