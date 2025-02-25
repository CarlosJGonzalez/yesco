<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	  
    <?php 
	    include_once ($_SERVER['DOCUMENT_ROOT'].'/includes/head.php'); 
	    require_once ($_SERVER['DOCUMENT_ROOT'].'/includes/ClassDasMC.php');
	    
		if(!(roleHasPermission('show_nav_link_option', $_SESSION['role_permissions']))){
			header('location: /');
		}
	?>
	<link rel="stylesheet" href="/css/corebridge.css" type="text/css">
	<link rel="stylesheet" href="template/101.css">  
    <title> Customer | Local <?=$client?></title>
	<style>
	#userTable_mailchimp{
		width: 100% !important;
	}
		.ui-state-active, .ui-widget-content .ui-state-active, .ui-widget-header .ui-state-active, a.ui-button:active, .ui-button:active, .ui-button.ui-state-active:hover{
			border: 1px solid #ac1f2d;
			background-color: #ac1f2d;
		}
		#email-edit .form-group {
			padding: 0 15px;
		}
		#email-edit .options-bg {
			margin-bottom: 15px;
		}
		#email-preview img {
			padding: 0;
		}
		i.activity {
		  position: relative;
		  font-size: 2em;
		  color: grey;
		  cursor: default;
		}
		span.fa-comment {
		  position: absolute;
		  font-size: 0.7em;
		  top: -4px;
		  color: #ac1f2d;
		  right: -4px;
		}
		span.num {
		 position: absolute;
		  font-size: 0.4em;
		  top: 1px;
		  color: #fff;
		  right: 2px;
		}
		.pr-2, .px-2 {
			padding-right: .5rem!important;
		}
		#send_emial_info .infoContainer {
			margin-bottom: .5rem;
		}
		.align-items-center {
			-ms-flex-align: center!important;
			align-items: center!important;
		}
	</style>
  </head>
  <body>
  	<? include ($_SERVER['DOCUMENT_ROOT'].'/includes/nav.php');	 ?>
    <div class="main location">
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

		
		<div id="tabs">
			<ul>
				<li><a href="#tabs-pendig">Pending</a></li>
				<li><a href="#tabs-email">Active</a></li>
				<li><a href="#tabs-template">Template</a></li>
			</ul>
			<div id="tabs-pendig">
				<div class="pull-right form-inline">
					<span>Action:</span>
					<select name="action" class="form-control design w-auto" id="selectAction">
						<option value="">---</option>
						<option value="send">Send Review Email</option>
						<option value="delete">Delete</option>
					</select>
				</div>
				<!-- Displaying imported users -->
				<div class="table-responsive clear">
					<table class="table table-striped" id="userTable">
						<thead>
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
							//$sql = "select * from review_recipient where storeid='".$_SESSION['storeid']."' and sent_flag <> 'S' order by id desc";
							$sno = 1;

							$result = $db->where('storeid',$_SESSION['storeid'])
										 ->where('sent_flag','S','<>')
										 ->orderBy('id','desc')
										 ->get('review_recipient');

							//$result = $conn->query($sql);
						
							foreach ($results as $rr) {
							
								$flag = '';
								if ($rr['sent_flag'] == 'S')
									$flag = 'Sent';
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

				<form method="post" action="xt_import.php" enctype="multipart/form-data" id="import_form">
					<div class="modal fade" tabindex="-1" role="dialog" id="importModal">
					  <div class="modal-dialog" role="document">
						<div class="modal-content">
						  <div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title">Import Customers</h4>
						  </div>
						  <div class="modal-body">
								<div class="input-group">
									<label class="input-group-btn">
										<span class="btn btn-primary">
											<i class="fa fa-folder-open-o" aria-hidden="true"></i> Browse&hellip; <input type="file" name="importfile" id="importfile" class="form-control" style="display: none;" onchange="validateFiles(this.id,'fileMsgContainer','excel','submitButton',1,40000000)" accept=".csv" required />
										</span>
									</label>
									<input type="text" class="form-control" readonly>
								</div>
							  <small id="fileMsgContainer">Only .csv files are accepted</small>
						  </div>
						  <div class="modal-footer">
								<button type="submit" class="btn btn-primary" name="but_import" id="submitButton" disabled >Import</button>
								<button type="button" class="btn btn-default" data-dismiss="modal" name="but_import">Close</button>
						  </div>
						</div><!-- /.modal-content -->
					  </div><!-- /.modal-dialog -->
					</div><!-- /.modal -->
				</form>
				
				<form method="post" action="xt_add_customer.php" enctype="multipart/form-data" id="add_customer">
					<div class="modal fade" tabindex="-1" role="dialog" id="addModal">
					  <div class="modal-dialog" role="document">
						<div class="modal-content">
						  <div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title">Add Customer</h4>
						  </div>
						  <div class="modal-body">
							  <div class="form-group">
								  <label>Name</label>
								  <input type="text" name="name" class="form-control" required>
							  </div>
							  <div class="form-group">
								  <label>Email</label>
								  <input type="text" name="email" class="form-control" required>
							  </div>
						  </div>
						  <div class="modal-footer">
								<button type="submit" class="btn btn-primary">Add</button>
								<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						  </div>
						</div><!-- /.modal-content -->
					  </div><!-- /.modal-dialog -->
					</div><!-- /.modal -->
				</form>
				
			</div>



			<div id="tabs-email">
				<!-- Displaying imported users -->
				<div class="table-responsive clear">
					<table id="userTable_mailchimp" class="table table-striped">
				       <thead>
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

			<div id="tabs-template">
				<!--Email Template-->
				<form class="cleared" action="xt_saveTemplate.php" method="POST">
					<div class="row">
						<div class="col-xs-12 col-sm-4">

							<?
								$sql_store_template = "SELECT * from review_template where storeid='".$_SESSION['storeid']."' limit 1";
								$result_store_template = $conn->query($sql_store_template);
								$default=0;
								if ($result_store_template->num_rows == 0){
									$sql_store_template = "SELECT * from review_template where storeid='' limit 1";
										$result_store_template = $conn->query($sql_store_template);	
									$default=1;
								}

								if ($result_store_template->num_rows > 0){
									$t = $result_store_template->fetch_assoc();
								?>
							<div id="email-edit">
								<div class="options-bg">
									 <div class="options-header">Custom text</div>
								</div>
					<div class="form-group clearfix">
									<label for="phone" class="grey">Owner Name</label>
									<input type="text" name="name" class="form-control emailText" data-variable="name" value="<? if($t['owner_name']) echo $t['owner_name']; else $row['fname1']." ".$row['lname1'];?>">
								</div>
								<div class="form-group clearfix">
									<label for="address" class="grey">Company Name</label>
									<input type="text" name="companyname" class="form-control emailText" data-variable="companyname" value="<? if($row['companyname']) echo $row['companyname'];?>">
								</div>
								
									<div class="form-group clearfix">
										<label for="header" class="grey">Heading</label>
										<input type="text" name="header" class="form-control emailText" data-variable="header" value="<?=$t['heading'];?>">
									</div>
									<div class="form-group clearfix">
										<label for="body" class="grey">Body</label>
										<textarea class="form-control emailText" name="body" rows="5" data-variable="body"><?=$t['body'];?></textarea>
									</div>
									<div class="form-group clearfix">
										<label for="body" class="grey">Number of resend</label>
										<input type="text" minlength="1" maxlength="1" pattern="[1-4]" readonly id = "num_resend" name="num_resend" class="form-control emailText" data-variable="num_resend" value="<?=$t['num_resend'];?>">
									</div>
								<input type="hidden" name="default" value="<?=$default?>">
								<div class="form-group clearfix">
								<div class="form-group clearfix">
									<div clas="col-sm" id="send_emial_info"><?=makeHtmlSendConfig(json_decode($t['info_send_email']));?></div>

										<small class="fakeLink" id="addSendEmailInfo" style="cursor: pointer;">+ Add Another</small>
								</div>
							</div>
								
								<? } ?>
								


							</div>
						</div>

						<div class="col-xs-12 col-sm-7 col-sm-offset-1">
							<div id="email-preview" class="clearfix">
								<div class="options-bg text-center">
									 <div class="options-header">Email Preview</div>
									 <div class="col-xs-12 choices-bin">
									 </div>
								</div>
							</div>
						</div>

						<div class="modal fade" tabindex="-1" role="dialog" id="galleryModal">
						  <div class="modal-dialog modal-lg" role="document">
							<div class="modal-content">
							  <div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h4 class="modal-title">Search Graphics Library</h4>
								<div class="row filters">
									<div class="col-xs-12 col-sm-4">
										<select name="month" class="design filter">
											<option value="">All Months</option>
											<? 
											foreach ($months as $month){
											?>
											<option value="<?=strtolower($month)?>"><?=$month?></option>
											<? } ?>
										</select>
									</div>
									<div class="col-xs-12 col-sm-4">
										<select name="category" class="design filter">
											<option value="">All Categories</option>
											<? 
											foreach ($categories as $key => $value) {
											?>
											<option value="<?=$key?>"><?=$value?></option>
											<? } ?>
											<option value="by_me" selected>Uploaded by Me</option>
										</select>
									</div>
									<div class="col-xs-12 col-sm-4 pull-right">
										<input name="search" placeholder="Search" class="design" id="searchText" />

									</div>
								</div>
							  </div>
							  <div class="modal-body">
								<? //include ($_SERVER['DOCUMENT_ROOT'].'/corebridge/gallery.php'); ?>
							  </div>
							</div><!-- /.modal-content -->
						  </div><!-- /.modal-dialog -->
						</div><!-- /.modal -->


						<div class="col-xs-12 op-button text-center clear" style="margin-top:20px;">
							<button type="submit" class="btn btn-primary opmargin w-auto">Save</button>
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
    <? include ($_SERVER['DOCUMENT_ROOT'].'/includes/footer.php'); ?>
    
	<script type="text/javascript">
		$(document).ready(function(){
			
			loadEmail();
			//$.fn.dataTable.moment('MM/DD/YYYY');
			var table = $('#userTable').DataTable({
				responsive: true,
				"pageLength": 10,
				dom: 'B<"clear">lfrtip',
				buttons: [
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
					},
					{ extend: 'excel', 
					text: 'Export'},
					'selectAll',
					'selectNone'
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
				var remove=$('<i>',{'style':'cursor:pointer','class':"fa fa-trash-o removeSendEmailInfo pr-2",'aria-hidden':'true'}).on('click',function(){
									$(this).parent().remove();
									$('#num_resend').val($('#num_resend').val()-1);
							});
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
						if(confirm("Are you sure you want to delete the selected customers?")){
							window.location.href = "action.php?action="+action+"&values="+values;
						}
					}else if(action=="send"){
						if(confirm("Are you sure you want to send an email to the selected customers?")){
							window.location.href = "action.php?action="+action+"&values="+values;
						}
					}
					 
				}else{
					$(this).val("");
				}
			});
			$( function() {
				$( "#tabs" ).tabs();
			  } );
		});

		function loadEmail(){
			var vars = {};
			$( ".emailText" ).each(function() {
			  vars[$(this).data("variable")] = $(this).val();
			});

			$.ajax({
				url: "template/101.php", 
				type:"POST",
				dataType:"html",
				data:{"vars":vars,"storeid":<?=$_SESSION['storeid'];?>,"client":"<?=$_SESSION['client'];?>"},
				success: function(result){
					$(".choices-bin").html(result);
				}
			});
		}
		$(".emailText").focusout(function(){
			loadEmail();
		});

    </script>
	<script src="https://www.adjack.net/validate-files-js/validate-files.js"></script>
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
						$remeveTag="<i aria-hidden='true' style='cursor:pointer' onclick='$(this).parent().remove();' class='fa fa-trash-o removeSendEmailInfo pr-2' ></i>";
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
	$html="";
	$icons=array('open'=>'fa-envelope-open-o','click'=>'fa-mouse-pointer','sent'=>'fa-paper-plane');

	foreach ($rr['activity'] as $key => $value) {
		//$info_act.= " ".$key.":".$value." ";

		if(isset($icons[$key])){
			$html.='<div style="display:inline-block;width:22.333333%;margin-left:-2px"><i  title="'.ucfirst($key).'" class="fa '.$icons[$key].' activity">
				<span class="fa fa-comment"></span>
				<span class="num">'.$value.'</span>
				</i></div>';

		}
	}

	return $html;
}
?>