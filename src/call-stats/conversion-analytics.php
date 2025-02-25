<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <? include ($_SERVER['DOCUMENT_ROOT'].'/includes/head.php'); ?>
    <title>Call Conversion Analytics | Call Stats | Local <?=$client?></title>
	
  </head>
  <body>
  	<? include ($_SERVER['DOCUMENT_ROOT'].'/includes/nav.php');
	if($_SESSION['storeid']=="-1" && !$_GET['storeid']){
		header("location:/profile/all.php");
		exit;
	}
	if($_GET['analyticsStartDate'] && $_GET['analyticsEndDate']){
		$from =$_GET['analyticsStartDate'];
		$to = $_GET['analyticsEndDate'];
	}else{
		$from = date("Y-m-d", strtotime("-1 month"));
		$to = date("Y-m-d");
	}
	
	 ?>
	
    <div class="main rank">
    	<?php
		 if (!empty($_SESSION['success'])) {
			echo '<p class="alert alert-success">'.$_SESSION['success'].'</p>';
			unset($_SESSION['success']);
		 }
		 if (!empty($_SESSION['error'])) {
			echo '<p class="alert alert-danger">'.$_SESSION['error'].'</p>';
			unset($_SESSION['error']);
		 }
		 if (!empty($_SESSION['warning'])) {
			echo '<p class="alert alert-warning">'.$_SESSION['warning'].'</p>';
			unset($_SESSION['warning']);
		 }
	
		?>
    	<h1>Call Conversion Analytics</h1>
        
        <form name="dates" id="dates" method="get" class="form-inline">
            <small class="text-uppercase">From</small>
            <input type="date" name="analyticsStartDate" value="<?=$from?>" class="form-control">
            <small class="text-uppercase">to</small>
            <input type="date" name="analyticsEndDate" value="<?=$to?>" class="form-control">
            <input type="submit" value="Go" class="btn btn-primary">
        </form>
        
        <div class="row">
            <div class="col-xs-12 col-md-6 col-md-offset-3">
                <div class="box">
                    <h2>Call Conversion Ratio <i class="fa fa-angle-up pull-right" aria-hidden="true"></i></h2>
                    <div>
                        <div>
                            <div id="call-conversion"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        
        
        <?
		$sql="select a.campid,name,count(*) as c,COUNT(IF(existing_customer < 50 and Percent_Silence < 70, 1, NULL)) as existing_customer, COUNT(IF((existing_customer < 50 and sales_inquiry > 50), 1, NULL)) as sales_inquiry, COUNT(IF((existing_customer < 50 and sales_inquiry > 50  and  Appointment_Set > 50), 1, NULL)) as Appointment_Set from advtrack.calls_conversion_analytics a,advtrack.campid b where a.client='".$_SESSION['client']."-".$_SESSION['storeid']."' and a.client = b.client and a.campid = b.campid and b.type='C' and callid in (select callid from advtrack.calls where a.client ='".$_SESSION['client']."-".$_SESSION['storeid']."' and start between '".$from."' and '".$to."' and duration>29 and duplicate<>'1' and source='Convirza') group by a.campid";
		$result = $conn->query($sql);
		?>
        <table id="campaign_table">
            <thead>
                <tr>
                    <th>Campaign</th>
                    <th>Calls Mined</th>
                    <th>New Customers</th>
                    <th>Sales Inquiries</th>
                    <th>Appointments Set</th>
                    <th>New Customers that Set an Appointment</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th>Total</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </tfoot>
            <tbody>
                <? while($data = $result->fetch_assoc()){
					$new_cust = $new_cust + $data['existing_customer'];
                    $total_calls = $total_calls + $data['c'];
                    $total_sales_inquiry = $total_sales_inquiry + $data['sales_inquiry'];
                    $total_Appointment_Set = $total_Appointment_Set + $data['Appointment_Set'];
					$percent_appt = 0;
					if($data['existing_customer']>0) $percent_appt=$data['Appointment_Set']/$data['existing_customer'];
					 ?>
                <tr>
                    <td><a href="call-analytics-campaign.php?campid=<?=$data['campid']?>"><?=$data['name']?></a></td>
                    <td><?=$data['c']?></td>
                    <td><?=$data['existing_customer']?></td>
                    <td><?=$data['sales_inquiry']?></td>
                    <td><?=$data['Appointment_Set']?></td>
                    <td><?=round($percent_appt,2)?>%</td>
                </tr>

                <? } ?>
            </tbody>

        </table>
                    
                    
                    
        <?  $sql="select ivr_data,a.ivr_type,a.ivr_value,count(if(a.ivr_value is not null and a.ivr_value <> '',1,NULL)) as c,AVG(Agent_Politeness) as Agent_Politeness,AVG(Agitation_Level) as Agitation_Level,AVG(Determine_Needs) as Determine_Needs,AVG(Acquired_Email) as Acquired_Email,AVG(Acquired_Address) as Acquired_Address,AVG(Acquired_Phone_Number) as Acquired_Phone_Number,AVG(Appointment_Set) as Appointment_Set from advtrack.calls_conversion_analytics a,advtrack.calls c,advtrack.ivr_data b where a.client ='".$_SESSION['client']."-".$_SESSION['storeid']."' and a.callid=c.callid and c.start between '".$from."' and '".$to."' and a.client = b.client and a.ivr_type=b.ivr_type and a.ivr_value=b.ivr_value group by a.ivr_value";
		$result = $conn->query($sql);
		if ($result->num_rows > 0){ ?>
        <table id="sales_table">
            <thead>
                <tr>
                    <th>IVR Type</th>
                    <th>IVR Value</th>
                    <th>Name</th>
                    <th>Calls Mined </th>
                    <th>Agent Politeness </th>
                    <th>Agitation Level</th>
                    <th>Determine Needs</th>
                    <th>Acquired Email</th>
                    <th>Acquired Address</th>
                    <th>Acquired Phone#</th>
                    <th>Appointment Set</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                	<th colspan="3"></th>
                    <th>Total</th>
                    <th colspan="7"></th>
                </tr>
            </tfoot>
            <tbody>
            <? while($data = $result->fetch_assoc()){?>
            <tr>
            	<td><?=$data['ivr_type']?></td>
                <td><?=$data['ivr_value']?></td>
                <td><a href="" target="_blank"><?=$data['ivr_data']?></a></td>
                <td><?=$data['c']?></td>
                <td><?=$data['Agent_Politeness']?></td>
                <td><?=$data['Agitation_Level']?></td>
                <td><?=$data['Determine_Needs']?></td>
                <td><?=$data['Acquired_Email']?></td>
                <td><?=$data['Acquired_Address']?></td>
                <td><?=$data['Acquired_Phone_Number']?></td>
                <td><?=$data['Appointment_Set']?></td>

            </tr>
            <? } ?>
            </tbody>
        </table> 
        <? } ?>   

        
    </div>

    <? include ($_SERVER['DOCUMENT_ROOT'].'/includes/footer.php'); ?>
    <script type="text/javascript" src="http://static.fusioncharts.com/code/latest/fusioncharts.js"></script>
    <?
		$arrData = array(
			"chart" => array(
				"caption" => "Call Conversion Ratio",
				"showPercentValues"=> "1",

			),
		);

		$arrData["data"] = array();


		$sql="select count(*) as c,COUNT(IF(existing_customer < 50 and Percent_Silence < 70, 1, NULL)) as existing_customer, COUNT(IF((existing_customer < 50 and sales_inquiry > 50), 1, NULL)) as sales_inquiry, COUNT(IF((existing_customer < 50 and sales_inquiry > 50  and  Appointment_Set > 50), 1, NULL)) as Appointment_Set from advtrack.calls_conversion_analytics a where client = '".$_SESSION['client']."-".$_SESSION['storeid']."' and callid in (select callid from advtrack.calls where client = '".$_SESSION['client']."-".$_SESSION['storeid']."' and start between '".$from."' and '".$to."' and duration>29 and duplicate<>'1' and source='Convirza')";
		$result = $conn->query($sql);
		if ($result->num_rows > 0){
			$data = $result->fetch_assoc();
			$calls = $data["c"];
            $newcustomer = $data["existing_customer"];
            $newcustomer_inquiry = $data["sales_inquiry"];
            $newcustomer_inquiry_appt = $data["Appointment_Set"];
			array_push($arrData["data"], array(
				"label" => "Calls Mined",
				"value" => $calls,
				)
			);
			array_push($arrData["data"], array(
				"label" => "New Customers",
				"value" => $newcustomer,
				)
			);
			array_push($arrData["data"], array(
				"label" => "Sales Inquiries",
				"value" => $newcustomer_inquiry,
				)
			);
			array_push($arrData["data"], array(
				"label" => "Appointments Set",
				"value" => $newcustomer_inquiry_appt,
				)
			);
			
		}

		$jsonEncodedData = json_encode($arrData);
		
	include ($_SERVER['DOCUMENT_ROOT'].'/includes/fusioncharts.php');
	$columnChart = new FusionCharts("funnel", "ex2", "100%", 400, "call-conversion", "json", $jsonEncodedData);
	$columnChart->render();

	 ?>
	<script type="text/javascript">
	
		$(document).ready(function(){
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
		});
		$(document).ready(function(){
			var i;
			$('#campaign_table').DataTable( {
				responsive: true,
				pageLength:25,
				"order": [[ 0, "desc" ]],
				"footerCallback": function ( row, data, start, end, display ) {
					var api = this.api(), data;
		 
					// Remove the formatting to get integer data for summation
					var intVal = function ( i ) {
						return typeof i === 'string' ?
							i.replace(/[\%,]/g, '')*1 :
							typeof i === 'number' ?
								i : 0;
					};
					
					for(i=1; i<6;i++){
						// Total over all pages
						total = api
							.column( i )
							.data()
							.reduce( function (a, b) {
								return intVal(a) + intVal(b);
							}, 0 );
			 
						// Total over this page
						pageTotal = api
							.column( i, { page: 'current'} )
							.data()
							.reduce( function (a, b) {
								return intVal(a) + intVal(b);
							}, 0 );
			 
						// Update footer
						$( api.column( i ).footer() ).html(
							pageTotal +' ('+ total +' total)'
						);
					}
				}
			} );
			
			$('#sales_table').DataTable( {
				responsive: true,
				"order": [[ 0, "desc" ]],
				"footerCallback": function ( row, data, start, end, display ) {
					var api = this.api(), data;
		 
					// Remove the formatting to get integer data for summation
					var intVal = function ( i ) {
						return typeof i === 'string' ?
							i.replace(/[\%,]/g, '')*1 :
							typeof i === 'number' ?
								i : 0;
					};
					
						// Total over all pages
						total = api
							.column( 3 )
							.data()
							.reduce( function (a, b) {
								return intVal(a) + intVal(b);
							}, 0 );
			 
						// Total over this page
						pageTotal = api
							.column( 3, { page: 'current'} )
							.data()
							.reduce( function (a, b) {
								return intVal(a) + intVal(b);
							}, 0 );
			 
						// Update footer
						$( api.column( 3 ).footer() ).html(
							pageTotal +' ('+ total +' total)'
						);
				}
			} );
			
		});
	</script>  
    </body>
</html>