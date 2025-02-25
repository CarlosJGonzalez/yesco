<!doctype html>
<html lang="en">
  <head>
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
			background-color:#0067b1;
			padding: .25rem 1rem;
			margin-right: .5rem !important;
			border:none;
		}
	</style>

    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/head.php");
	  if(!(roleHasPermission('show_reputation_management', $_SESSION['role_permissions']))){
		header('location: /');
		  exit;
		}
	  ?>

    <title>Reputation Management | <?php echo CLIENT_NAME; ?></title>
	  
  </head>
  <body class="bg-light cbp-spmenu-push">
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/top.php"); ?>

    <div class="container-fluid">
      <div class="row">
        <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/nav.php"); ?>
        <div id="spinner_loading" class="none_upload loader"></div>
        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 p-0">
			
			<div class="p-0 border-bottom mb-4">
				<div class="d-flex d-block align-items-center clearfix py-2 px-4">
					<h1 class="h2 font-light mb-0 text-center text-sm-left"><i class="fas fa-star mr-2"></i> Reputation Management</h1>
				</div>
			</div>
			
			<div class="py-3 px-4">
				<?php include $_SERVER['DOCUMENT_ROOT']."/includes/alerts.php"; ?>

				<!-- Nav tabs -->
				<ul class="nav nav-tabs" role="tablist">
					<li class="nav-item">
						<a class="nav-link text-blue active" data-toggle="tab" href="#tabs-pendig">Pending</a>
					</li>
					<li class="nav-item">
						<a class="nav-link text-blue" data-toggle="tab" href="#tabs-email">Active</a>
					</li>
					<li class="nav-item">
						<a class="nav-link text-blue" data-toggle="tab" href="#menu2">Template</a>
					</li>
				</ul>

				<div class="tab-content p-2">

					<div id="tabs-email" class="tab-pane  fade">
						<!-- Displaying imported users -->
						<div class="table-responsive">
							<table id="userTable_mailchimp" class="table table-striped">
							   <thead class="thead-dark">
								    <tr>
									   <th>No</th>
									   <th>Name</th>
									   <th>Email Address</th>
									   <th>Status</th>
									   <th>Actions</th>
								    </tr>
								  </thead>
							</table>					
						</div>			
					</div>
					
					<div class="tab-pane  active" id="tabs-pendig">
						<div class="float-right form-inline">
							<span class="mr-2 text-uppercase letter-spacing-1">Action:</span>
							<select name="action" class="form-control design w-auto" id="selectAction">
								<option value="">---</option>
								<option value="send">Send Review Email</option>
								<option value="delete">Delete</option>
							</select>
						</div>
						<div class="table-responsive">
							<table id="userTable" class="table table-striped">
								<thead class="thead-dark">
									<tr>
										<th></th>
										<th>ID</th>
										<th>Name</th>
										<th>Email Address</th>
										<th>Status</th>
									</tr>
								</thead>
								<tbody>
								<?php
									$sno = 1;
								
									$result = $db->where('storeid',$_SESSION['storeid'])
										 		 ->where('sent_flag','S','<>')
										 		 ->orderBy('id','desc')
										 		 ->get('review_recipient');	
									 		 									 		 									 		 

									foreach ($result  as $rr) {
										$flag = '';
										if ($rr['sent_flag'] == 'N')
											$flag = 'No Sent';
										if ($rr['sent_flag'] == 'Y')
											$flag = 'Sent';
								?>
									<tr data-id="<?=$rr['id']?>">
										<td></td>
										<td><?=$sno?></td>
										<td><?=$rr['name']?></td>
										<td><?=$rr['email']?></td>
										<td><?=$flag?></td>
									</tr>
								<? $sno++; } ?>
								</tbody>
							</table>					
						</div>
					</div>
					<div class="tab-pane  fade" id="menu2">
						<!--Email Template-->
						<form action="xt_saveTemplate.php" method="POST">
							<div class="row">
								<div class="col-sm-4">
									<?
										$t=$db->where('storeid',$_SESSION['storeid'])->getOne('review_template');
										$default=0;

										if(count($t) == 0){
											$t=$db->where('storeid','')->getOne('review_template');
											$default=1;
										}
										if (count($t)){
											$name_owner=($t['owner_name'])?$t['owner_name']:$active_location['fname1']." ".$active_location['lname1'];
									?>
									<div class="border-border bg-white mt-4 rounded">
										<div class="bg-blue text-white text-center p-2 rounded-top">
											 <span class="h4 text-uppercase letter-spacing-1">Email Preview</span>
										</div>
										<div class="p-3 rounded-bottom">
											<div class="form-group">
												<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Owner Name</label>
												<input type="text" name="name" class="form-control emailText" data-variable="name" value="<?=$name_owner?>">
											</div>
											<div class="form-group ">
												<label for="address" class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Company Name</label>
												<input type="text" name="companyname" class="form-control emailText" data-variable="companyname" value="<? if($active_location['companyname']) echo $active_location['companyname'];?>">
											</div>

												<div class="form-group ">
													<label for="header" class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Heading</label>
													<input type="text" name="header" class="form-control emailText" data-variable="header" value="<?=$t['heading'];?>">
												</div>
												<div class="form-group ">
													<label for="body" class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Body</label>
													<textarea class="form-control emailText" name="body" rows="5" data-variable="body"><?=$t['body'];?></textarea>
												</div>
												<div class="form-group ">
													<label for="body" class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Number of resend</label>
													<input type="text" minlength="1" maxlength="1" pattern="[1-4]" readonly id="num_resend" name="num_resend" class="form-control emailText" data-variable="num_resend" value="<?=$t['num_resend'];?>">
												</div>
												<input type="hidden" name="default" value="<?=$default?>">
											<div class="form-group clearfix">
											<div class="row">
												<div class="col-12">
													<div clas="col-sm" id="send_emial_info"><?=makeHtmlSendConfig(json_decode($t['info_send_email']));?></div>
													<div class="col-3">
														<small class="fakeLink" id="addSendEmailInfo" style="cursor: pointer;">+ Add Another</small>
													</div>
												</div>
											</div>
										</div>
									</div>
									<? } ?>
									</div>
								</div>

								<div class="col-sm-7 offset-sm-1">
									<div class="border-border bg-white mt-4 rounded">
										<div id="email-preview" class="clearfix">
											<div class="options-bg text-center">
												 <div class="bg-blue text-white text-center p-2 rounded-top">
													 <span class="h4 text-uppercase letter-spacing-1">Custom text</span>
												</div>
												 <div class="choices-bin">
												 </div>
											</div>
										</div>
									</div>
								</div>
						

								<div class="col-12 text-center mt-4">
									<button type="submit" class="btn bg-blue text-white rounded-pill btn-lg">Save</button>
								</div>
								<input type="hidden" name="custom_audience" value="<?=$_GET['custom_audience']?>" />
								<input type="hidden" name="template" value="<?=$_GET['template']?>" />
								<input type="hidden" name="industry_list" value="<?=$_GET['industry_list']?>" />
								<input type="hidden" name="product_list" value="<?=$_GET['product_list']?>" />
								<input type="hidden" name="custom_list" value="<?=$_GET['custom_list']?>" />
							</div>
						</form>
						<!--End Template-->
					</div>
				</div>
			</div>
		</main>
      </div>
    </div>


<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
	<form method="post" action="xt_import.php" enctype="multipart/form-data" id="import_form">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="importNewPostLabel">Import from CSV file</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					  <span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">

					<div class="form-group">
						<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark"><?php echo $field['display_name']; ?></label>
						<div class="d-flex align-items-center">
							<div class="input-group">
							  <div class="custom-file">
								<input type="file" class="form-control emailText rounded-bottom rounded-right custom-file-input" name="importfile" id="importfile" accept=".csv" required onchange="validateFiles(this.id,'fileMsgContainer','excel','submitButton',1,40000000)">
								<label class="custom-file-label" for="contactfile">Choose file</label>
							  </div>
							</div>
						</div>
						
						<small >
							<i class="fas fa-exclamation-triangle mr-1"></i>
							<span id="fileMsgContainer"> Only .csv files can be imported.</span>
						</small>
						
					</div>

				</div>
				<div class="modal-footer">
					<button type="submit" class="btn bg-blue text-white" name="but_import" id="submitButton" >Import</button>
					<button type="button" class="btn btn-secondary" data-dismiss="modal" name="but_import_close">Close</button>
				</div>
			</div>
		</div>
	</form>
</div>


    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>
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

	$(document).ready(function() {
	    var table = $('#userTable').DataTable( {
	        
	        dom: 'B<"clear"><"row"<"col-sm-6"l><"col-sm-6"f>>rt<"row"<"col-sm-6"i><"col-sm-6"p>>',
	        buttons: [
					'selectAll','selectNone',
					{ extend: 'excel',text: 'Export'},
					{ extend: 'pdf',text: 'Pdf'},

					{
						text: 'Download Sample',
						action: function ( e, dt, node, config ) {
							window.location.href = 'upload-cust.csv';
							}
					},
					{
						text: 'Import Customers',
						action: function ( e, dt, node, config ) {
							$('#importModal').modal('show');
						}
					}
				],
			columnDefs: [ {
					orderable: false,
					className: 'select-checkbox',
					targets:   0
				} ],
				select: {
					style:    'multi',
					selector: 'tr'
				} 
	    } );
	 
	    table.buttons().container()
	        .appendTo( '#userTable_wrapper .col-md-6:eq(0)' );


	    $( "#selectAction" ).change(function() {
			var count = table.rows( { selected: true } ).count();
			if(count>0){
				var action = $(this).val();
				var values = [];
				$('tr.selected').each(function(){
				   var id=$(this).data('id');
					values.push(id);
				});
				 /*var rowData = table.rows('.selected').nodes();
				 $.each(rowData, function(i, val) {
					var id=$(this).data('id');
					values.push(id);
				  }); */
				if(action=="delete"){
					$('#spinner_loading').removeClass("none_upload");
					if(confirm("Are you sure you want to delete the selected customers?")){
						window.location.href = "action.php?action="+action+"&values="+values;
					}
				}else if(action=="send"){
					$('#spinner_loading').removeClass("none_upload");
					if(confirm("Are you sure you want to send an email to the selected customers?")){
						window.location.href = "action.php?action="+action+"&values="+values;
					}
				}		 
			}else{
				$(this).val("");
				$('#spinner_loading').addClass("none_upload");
			}
		});



		$('#userTable_mailchimp').DataTable({
				'processing': true,
				'serverSide': true,
				'serverMethod': 'post',
				'ajax': {
					'url':'xt_load_data_mailchimp.php'
				},
				'columns': [
					{ data: 'id' },
					{ data: 'name' },
					{ data: 'email' },
					{ data: 'status' },
					{ data: 'actions' },
				],
				columnDefs: [ {
				orderable: false
				} ],
		  
		});
	} );



	 $( "#import_form" ).submit(function( event ) {
                  
            $('#spinner_loading').removeClass("none_upload");
            $('#importModal').modal('hide');
            $(this).submit();

            event.preventDefault();
        });
	
	function loadEmail(){
		var vars = {};
		$( ".emailText" ).each(function() {
		   vars[$(this).data("variable")] = $(this).val();
		});
		$.ajax({
			url: "template.php", 
			type:"POST",
			dataType:"html",
			data:{"vars":vars,"storeid":<?php echo $_SESSION['storeid'];?>,"client":"<?php echo $_SESSION['client'];?>"},
			success: function(result){
				$(".choices-bin").html(result);
			}
		});
	}
	$(".emailText").focusout(function(){
		loadEmail();
	});
	$(document).ready(function() {
		loadEmail();
	});

	$(document).on('click','.removeSendEmailInfo',function(){
		$('#num_resend').val($('#num_resend').val()-1);
		$(this).parent().remove();
	});

	$( "#addSendEmailInfo" ).on('click',function(){
		var max = 3;
		var elemnts=$('#send_emial_info').children('.infoContainer').length;
		if(elemnts <= max){
			var arr = [
		  {val : 0, text: 'Immediately'},
		  {val : 1, text: 'Next Day'},
		  {val : 3, text: 'In 3 days'},
		  {val : 7, text: 'In 1 Week'}
		];
		var divContainer= $('<div>',{'class':'infoContainer d-flex align-items-center'}).appendTo($('#send_emial_info'));
		var remove=$('<i>',{'style':'cursor:pointer','class':"far fa-trash-alt removeSendEmailInfo pr-2",'aria-hidden':'true'});
		var remove= remove.appendTo(divContainer);
		var sel = $("<select>",{'name':'optionSendEmial[]','class':'design'},{'id':'optionSendInfo'}).appendTo(divContainer);

		$(arr).each(function() {
			 sel.append($("<option>").attr('value',this.val).text(this.text));
		});
		$('#num_resend').val(elemnts +1);
		}else{
			alert('Only '+(max + 1)+' email will sent to customer.');
		}
	});
	
</script>
  </body>
</html>

<?php
function makeHtmlSendConfig($info){
	$html="";
	foreach ($info as $tmp) {					
	
		$info_select= array(
						array('val' => '0', 'text'=> 'Immediately'),
						array('val' => '1', 'text'=> 'Next Day'),
						array('val' => '3', 'text'=> 'In 3 days'),
						array('val' => '7', 'text'=> 'In 1 Week'),
					);
			$remeveTag="<i aria-hidden='true' style='cursor:pointer' onclick='$(this).parent().remove();' class='far fa-trash-alt removeSendEmailInfo pr-2' ></i>";
		$divContainer="<div class= 'infoContainer d-flex align-items-center'>".$remeveTag;				

		$selectTag="<select class='design' name='optionSendEmial[]' id='optionSendInfo'>";
		foreach ($info_select as $value) {	
				if($value['val'] == $tmp){
					$selectTag.="<option  selected value='".$value['val']."'>".$value['text']."</option>";
				}else{
					$selectTag.="<option value='".$value['val']."'>".$value['text']."</option>";
				}
		}
		$selectTag.="</select>";
		$divContainer.=$selectTag;
		$divContainer.="</div>";
		$html.=$divContainer;
	}
	return $html;
}

function get_html_activity($rr){

	$icons=array('open'=>'fa-envelope-open','click'=>'fa-mouse-pointer','sent'=>'fa-paper-plane');
	$html='<div style="font-size: 1rem;">';
	foreach ($rr['activity'] as $key => $value) {

		if(isset($icons[$key])){

			$html.='<span class="fa-layers fa-2x mr-2" style="pading:MistyRose">
    				<i title="'.ucfirst($key).'" class="fas '.$icons[$key].' activity"></i>
    				<span class="fa-layers-counter">'.$value.'</span>
  					</span>';
		}
	}
	return $html.'</div>';
}
?>