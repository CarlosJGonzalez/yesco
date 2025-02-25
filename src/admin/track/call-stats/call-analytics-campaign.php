<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <? include ($_SERVER['DOCUMENT_ROOT'].'/includes/head.php'); ?>
    <title>Call Details By Campaign | Call Stats | Local <?php echo CLIENT_NAME; ?></title>
	
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
	
	if($_GET['campid'] != "")
		$strcampid = " and a.campid = '".$_GET['campid']."'";
	
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
    	<h1>Call Details By Campaign</h1>
        
        <form name="dates" id="dates" method="get" class="form-inline">
            <small class="text-uppercase">From</small>
            <input type="date" name="analyticsStartDate" value="<?=$from?>" class="form-control">
            <small class="text-uppercase">to</small>
            <input type="date" name="analyticsEndDate" value="<?=$to?>" class="form-control">
             <input type="hidden" name="campid" value="<?=$_GET['campid']?>">
            <input type="submit" value="Go" class="btn btn-primary">
        </form>
        
		<div class="break"></div>
        <?
        $sql="select a.callid as callid,start,end,caller,called,duration,Agent_Politeness,Agitation_Level,Determine_Needs,Acquired_Email,Acquired_Address,Acquired_Phone_Number,Appointment_Set,vendorid from advtrack.calls_conversion_analytics a,advtrack.calls c where a.client ='".$_SESSION['client']."-".$_SESSION['storeid']."' and a.callid=c.callid and c.start between '".$from."' and '".$to."' and a.callid=c.callid and duration>29 and duplicate<>'1' and source='Convirza'".$strcampid;
		$result = $conn->query($sql);
		?>
        <table id="callsTable">
            <thead>
                <tr>
                    <th>Start</th>
                    <th>End</th>
                    <th>Caller</th>
                    <th>Called</th>
                    <th>Duration</th>
                    <th>Agent Politeness </th>
                    <th>Agitation Level</th>
                    <th>Determine Needs</th>
                    <th>Acquired Email</th>
                    <th>Acquired Address</th>
                    <th>Acquired Phone#</th>
                    <th>Appointment Set</th>
                    <th>Tools</th>
                </tr>
            </thead>
            <tbody>
            <?
            while($data = $result->fetch_assoc()){
            ?>
            <tr>
            	<td><?=date('m/d/Y g:i A',strtotime($data['start']))?></td>
                <td><?=date('m/d/Y g:i A',strtotime($data['end']))?></td>
                <td><?=$data['caller']?></td>
                <td><?=$data['called']?></td>
                <td><?=$data['duration']?></td>
                <td><?=round($data['Agent_Politeness'],2)?></td>
                <td><?=round($data['Agitation_Level'],2)?></td>
                <td><?=round($data['Determine_Needs'],2)?></td>
                <td><?=round($data['Acquired_Email'],2)?></td>
                <td><?=round($data['Acquired_Address'],2)?></td>
                <td><?=round($data['Acquired_Phone_Number'],2)?></td>
                <td><?=round($data['Appointment_Set'],2)?></td>
                <td><button type="button" class="btn btn-primary callBtn" data-callid="<?=$data['callid']?>" data-vendorid="<?=$data['vendorid']?>" data-url="/call-stats/call-analytics-campaign.php?campid=<?=$_GET['campid']?>">Listen</button></td>
            </tr>
            <? } ?>
            </tbody>

        </table>

		<form action="xt_call.php" method="POST">
            <div class="modal fade" id="callModal2" tabindex="-1" role="dialog" aria-labelledby="callModalLabel">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Call Details</h4>
                  </div>
                  <div class="modal-body">
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <input type="submit" class="btn btn-primary" value="Save changes">
                  </div>
                </div>
              </div>
            </div>
        </form>
        
    </div>

    <? include ($_SERVER['DOCUMENT_ROOT'].'/includes/footer.php'); ?>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
    <script type="text/javascript" src="//cdn.datatables.net/plug-ins/1.10.13/sorting/datetime-moment.js"></script>
  	<script type="text/javascript">
		$(document).ready(function(){
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
			$.fn.dataTable.moment('MM/DD/YYYY h:mm A');
			$('table').DataTable( {
				responsive: true,
				pageLength:25,
				"order": [[ 0, "desc" ]],
			} );
			
		} );
		$(document).on('click','.callBtn',function(){
			var callid = $(this).data("callid");
			var vendorid = $(this).data("vendorid");
			var url = $(this).data("url");
			$.ajax({
				url: "get_call.php", 
				type:"POST",
				data:{"callid":callid,"vendorid":vendorid,"url":url},
				success: function(result){
					$("#callModal2 .modal-body").html(result);
					$('#callModal2').modal('show'); 
				}
			});
			
		});
	</script> 
    </body>
</html>