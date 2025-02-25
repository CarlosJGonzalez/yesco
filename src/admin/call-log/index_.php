<!doctype html>
<html lang="en">
  <head>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<link href="//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
	<link href="//cdn.datatables.net/buttons/1.5.6/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/head.php");?>

    <title>Call Log | <?php echo CLIENT_NAME; ?></title>
	  
  </head>
  <body class="bg-light cbp-spmenu-push">
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/top.php"); ?>

    <div class="container-fluid">

      <div class="row">
        <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/nav.php"); ?>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 p-0 mb-4">
			
			<div class="p-0 border-bottom mb-4">
				<div class="border-bottom-dotted d-flex d-block align-items-center clearfix py-2 px-4">
					<h1 class="h2 font-light mb-0 text-center text-sm-left"><i class="fas fa-phone mr-2"></i> Call Log</h1>
					<div class="ml-auto">
						<!--<button type="button" class="border-0 bg-transparent">
							<i class="fas fa-2x text-muted fa-plus-circle"></i>
						</button>-->
						<button type="button" data-toggle="modal" data-target="#newCall" class="border-0 bg-transparent">
							<i class="fas fa-2x text-muted fa-plus-circle"></i>
						</button>
					</div>
				</div>
				<div class="py-3 px-4 d-block d-xl-flex align-items-center">
					<a class="small text-blue d-block d-lg-none" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">Advanced Search</a>
					
					<div class="collapse show w-100" id="collapseExample">
						<div class="d-xl-flex">
							<div class="d-flex d-xl-inline-block mb-2 mb-xl-0 mr-2 align-items-center">
								<span class="letter-spacing-1 text-uppercase small mr-2 mr-xl-0">Location:</span>
								<select name="col0_filter" class="flex-grow d-xl-inline-block form-control form-control-sm w-auto rounded-pill custom-select-arrow pr-4 column_filter_select" data-column="0">
									<option value="">All Locations</option>
									<?
									$sql_all_locations = "SELECT storeid,companyname FROM ".$_SESSION['database'].".locationlist order by companyname";
									$all_locations = $db->rawQuery($sql_all_locations);
									if($db->count > 0){
										foreach($all_locations as $location){
										?>	
										<option value="<?=$location['storeid']?>"><?=$location['companyname'].' ('.$location['storeid'].')'?></option>
										<? 
										}
									}
									?>
								</select>
							</div>
							<div class="d-flex d-xl-inline-block mb-2 mb-xl-0 mr-2 align-items-center">
								<span class="letter-spacing-1 text-uppercase small mr-2 mr-xl-0">Caller:</span>
								<select name="col1_filter" class="flex-grow d-xl-inline-block form-control form-control-sm w-auto rounded-pill custom-select-arrow pr-4 column_filter_select" data-column="1">
									<option value="">All Callers</option>
									<?
									$sql_all_callers = "SELECT distinct caller FROM advtrack.campaign_call_log WHERE client=".$_SESSION['client']." order by caller";
									$all_callers = $db->rawQuery($sql_all_callers);
									if($db->count > 0){
										foreach($all_callers as $caller){
										?>	
										<option value="<?=$caller['caller']?>"><?=$caller['caller']?></option>
										<? 
										}
									}
									?>
								</select>
							</div>
							<div class="d-flex d-xl-inline-block mb-2 mb-xl-0 mr-2 align-items-center">
								<span class="letter-spacing-1 text-uppercase small mr-2 mr-xl-0">Call Type:</span>
								<select name="col3_filter" class="flex-grow d-xl-inline-block form-control form-control-sm w-auto rounded-pill custom-select-arrow pr-4 column_filter_select" data-column="3">
									<option value="">All Call Types</option>
									<?
									$types = ["Answered","VoiceMail","Re-Schedule","CallBack","Webinar","Email"];
									foreach($types as $type){
									?>	
									<option value="<?=$type?>"><?=$type?></option>
									<? 
									}
									?>
								</select>
							</div>
							<div class="d-flex d-xl-inline-block mb-2 mb-xl-0 mr-2 align-items-center">
								<span class="letter-spacing-1 text-uppercase small mr-2 mr-xl-0">Reason:</span>
								<select name="col4_filter" class="flex-grow d-xl-inline-block form-control form-control-sm w-auto rounded-pill custom-select-arrow pr-4 column_filter_select" data-column="4">
									<option value="">All Reasons</option>
									<?
									$reasons=["Intro Call","High Performer","Low Performer","Inbound","Webinar","Misc","Non-Participant"];
									foreach($reasons as $reason){
									?>	
									<option value="<?=$reason?>"><?=$reason?></option>
									<? 
									}
									?>
								</select>
							</div>
						</div>

					</div>
				</div>
				
			</div>
        	<div class="px-4 py-3">
				
				<?php include $_SERVER['DOCUMENT_ROOT']."/includes/alerts.php"; ?>

				<div class="table-responsive">
					<table class="table">
						<thead class="thead-dark">
							<tr>
								<th>Location ID</th>
								<th>DAS Caller</th>
								<th>Call Date</th>
								<th>Call Type</th>
								<th>Reason</th>
								<th>Duration (min)</th>
								<th>Call Notes</th>
								<th>Action</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<?php
							$logs = $db->rawQuery("select a.*,group_concat(storelist) as storelist from advtrack.campaign_call_log a,advtrack.campaign_call_log_store b where a.id=b.id and a.client=? group by b.id order by calldate",Array($_SESSION['client']));
							if($db->count > 0){
								foreach($logs as $log){
							?>
							<tr>
								<td><?php
								$storelist= explode(",",$log['storelist']);
								$stores="";
								foreach($storelist as $store){
									$rowstore = $db->rawQueryOne("select concat(companyname,'-',storeid) as companyname from locationlist where storeid=?",Array($store));
									$stores .= $rowstore['companyname'].", ";
								}
								$stores= rtrim($stores,", ");
								echo $stores;
								?></td>
								<td><?php echo $log['caller']?></td>
								<td><?php if($log['calldate']>0) echo date("m/d/Y",strtotime($log['calldate']))?></td>
								<td><?php echo $log['calltype']?></td>
								<td><?php echo $log['reason']?></td>
								<td><?php echo $log['duration']?></td>
								<td><?php echo $log['notes']?></td>
								<td><?php echo $log['action']?></td>
								<td><a href="" class="btn bg-blue btn-sm text-white edit" data-toggle="modal" data-target="#editCallModal" data-id="<?=$log['id']?>">Edit</a></td>
							</tr> 
							<?php }
							}?>

						</tbody>
					</table>
				</div>
			
			</div>
			
			<!-- Add New Call Log modal form-->
			<form action="xt_call_log_actions.php" method="POST" name="addNewCall">
				<div class="modal fade" id="newCall" tabindex="-1" role="dialog" aria-labelledby="uploadModalTitle" aria-hidden="true">
				  <div class="modal-dialog modal-dialog-centered" role="document">
					<div class="modal-content">
					  <div class="modal-header">
						<h5 class="modal-title" id="uploadModalTitle">Add Call Log</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						  <span aria-hidden="true">&times;</span>
						</button>
					  </div>
					  <div class="modal-body">
							<div class="form-group">
								<label class="text-uppercase small">Campaign<span class="text-danger">*</span></label>
								<input type="text" name="campaign" class="form-control" />
							</div>
							<div class="form-group">
								<label class="text-uppercase small">DAS Caller<span class="text-danger">*</span></label>
								<select class="form-control custom-select-arrow pr-4" name="caller" required>
									<? $callers=["Alyssa","Brett","Christina","Jimmy","Lisa","Mirian","Ralph","Sam","John"];
									foreach($callers as $caller){
									?>
									<option value="<?=$caller?>"><?=$caller?></option>
									<? } ?>
								</select>
							</div>
							<div class="form-group">
								<label class="text-uppercase small">Call Date<span class="text-danger">*</span></label>
								<input type="text" name="calldate" class="form-control datepicker" required />
							</div>
							<div class="form-group">
								<label class="text-uppercase small">Store ID<span class="text-danger">*</span></label>
								<select class="form-control custom-select-arrow pr-4" name="storeid[]" multiple required>
									<?
									$sql_stores = "SELECT storeid,companyname FROM ".$_SESSION['database'].".locationlist order by companyname";
									$stores = $db->rawQuery($sql_stores);
									if($db->count > 0){
										foreach($stores as $store){
										?>	
										<option value="<?=$store['storeid']?>"><?=$store['companyname'].' ('.$store['storeid'].')'?></option>
									<? 
										}
									}
									?>
								</select>
								<label class="text-uppercase small">Call Type<span class="text-danger">*</span></label>
								<select class="form-control custom-select-arrow pr-4" name="calltype" required>
									<?
									$types = ["Answered","VoiceMail","Re-Schedule","CallBack","Webinar","Email"];
									foreach($types as $type){
									?>	
									<option value="<?=$type?>"><?=$type?></option>
									<? 
									}
									?>
								</select>
							</div>
							<div class="form-group">
								<label class="text-uppercase small">Call Lenght (min)<span class="text-danger">*</span></label>
								<input type="text" name="duration" class="form-control" required />
							</div>
							<div class="form-group">
								<label class="text-uppercase small">Reason<span class="text-danger">*</span></label>
								<select class="form-control custom-select-arrow pr-4" name="reason" required>
									<?
									$reasons=["Intro Call","High Performer","Low Performer","Inbound","Webinar","Misc","Non-Participant"];
									foreach($reasons as $reason){
									?>	
									<option value="<?=$reason?>"><?=$reason?></option>
									<? 
									}
									?>
								</select>
							</div>
							<div class="form-group">
								<label class="text-uppercase small">Notes<span class="text-danger">*</span></label>
								<textarea name="notes" class="form-control" required></textarea>
							</div>
							<div class="form-group">
								<label class="text-uppercase small">Action<span class="text-danger">*</span></label>
								<textarea name="action" class="form-control" required></textarea>
							</div>
					  </div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
							<input type="submit" class="btn bg-blue text-white btn-sm" value="SAVE" id="submitBtnAddNewCall" name="submitBtnAddNewCall">
							<input type="hidden" name="type" value="admin">
						</div>

					</div>
				  </div>
				</div>
			</form>
			<!-- End Add New Call Log modal form-->
			
			<!-- Edit Call Log modal form-->
			<form action="xt_call_log_actions.php" method="POST" name="editCallForm">
				<div class="modal fade" id="editCallModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalTitle" aria-hidden="true">
				  <div class="modal-dialog modal-dialog-centered" role="document">
					<div class="modal-content">
					  <div class="modal-header">
						<h5 class="modal-title" id="uploadModalTitle">Edit Call Log</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						  <span aria-hidden="true">&times;</span>
						</button>
					  </div>
					  <div class="modal-body">

					  </div>
					  <div class="modal-footer">
							<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
							<input type="submit" class="btn bg-blue text-white btn-sm" value="SAVE" id="submitBtnUpdateCall" name="submitBtnUpdateCall">
							<input type="hidden" name="type" value="admin">
					  </div>
					</div>
				  </div>
				</div>
			</form>
			<!-- End Edit Call modal form-->

        </main>
      </div>
    </div>


    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>
	<script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
	<script src="//cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
	<script src="//cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
	<script src="//cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
	<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
    <script type="text/javascript" src="//cdn.datatables.net/plug-ins/1.10.13/sorting/datetime-moment.js"></script> 
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>	
	<script>
		$(document).ready( function () {
			$.fn.dataTable.moment('MM/DD/YYYY');
			var table = $('table').DataTable({
				pageLength: 50,
				dom: '<"row mb-2"<"col-6"B>><"row"<"col-6"l><"col-6"f>>rt<"row"<"col-6"i><"col-6"p>>',
				buttons: [
					{ extend: 'excelHtml5', 
					text: 'Export',
					 className: 'btn btn-sm bg-blue rounded-pill text-white border-0 text-uppercase px-3'
					}
				],
				order:[2,"desc"],
				"columnDefs": [ {
					"targets": -1,
					"orderable": false
				} ]
			});
			
			/* begin datepicker */
			$( function() {
				  $("input[name=calldate]").datepicker({
					  defaultDate: "+1w",
					  changeMonth: true,
					  dateFormat: "yy-mm-dd",
					  numberOfMonths: 1
					});
			  });
			/* end datepicker */
		});
		
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
		
		$('select.column_filter_select').on( 'change', function () {
			filterColumnSelect( $(this).attr('data-column') );
		} );
		function filterColumnSelect ( i ) {
			$('table').DataTable().column( i ).search(
				$('select[name="col'+i+'_filter"] option:selected').val()
			).draw();
		}
	
		$(document).on('focus', '.datepicker', function(e){
			$(this).datepicker();
		});
		$(document).on('click','.edit',function(){
			var id = $(this).data("id");
			$.ajax({
				url: "get_data.php", 
				type:"POST",
				data:{"id":id},
				success: function(result){
					$("#editCallModal .modal-body").html(result);
					$('#editCallModal').modal('show'); 
				}
			});
		});
	</script>	
  </body>
</html>