<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	  
    <? include ($_SERVER['DOCUMENT_ROOT'].'/includes/head.php'); ?>
	  <link rel="stylesheet" href="/corebridge/css/styles.css" type="text/css">
	<link rel="stylesheet" href="template/101.css">  
    <title> Customer | Local <?=$client?></title>
	<style>
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
		.form-control.holder {
			height:auto;
		}
		.form-control.holder label{
			padding: 5px;
			width:50%;
			float:left;
		}
		.form-control.holder input[type=radio] {
			display:none;
		 }
		

		.form-control.holder input[type=radio]:checked + label{
			border: 2px solid #F00;
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

		
	
			<div id="tabs-template">
				<!--Email Template-->
				<form class="cleared" action="action_corebridge.php" method="POST">
					<div class="row">
						<div class="col-xs-12 col-sm-4">
							<div id="email-edit">
								<div class="options-bg">
									 <div class="options-header">Custom text</div>
								</div>
								<div class="form-group clearfix">
									<label for="phone" class="grey">Owner Name</label>
									<input type="text" name="name" class="form-control emailText" data-variable="name" value="<? if($row['fname1']) echo $row['fname1']." ".$row['lname1']; else echo "555-555-5555";?>">
								</div>
								<div class="form-group clearfix">
									<label for="address" class="grey">Company Name</label>
									<input type="text" name="companyname" class="form-control emailText" data-variable="companyname" value="<? if($row['companyname']) echo $row['companyname'];?>">
								</div>
								
									<div class="form-group clearfix">
										<label for="header" class="grey">Heading</label>
										<input type="text" name="header" class="form-control emailText" data-variable="header" value="Thank you for choosing Signarama for your Vehicle Wraps!">
									</div>
									<div class="form-group clearfix">
										<label for="body" class="grey">Body</label>
										<textarea class="form-control emailText" name="body" rows="5" data-variable="body">We’d love for you to share your recent experience with us. Your feedback not only helps us, it helps other potential customers. </textarea>
									</div>
									<div class="form-group clearfix">
										<label for="body" class="grey">Bottom Body</label>
										<textarea class="form-control emailText" name="body_bottom" rows="5" data-variable="body_bottom">Here are a few pictures of your vehicle wraps that we’re proud of.</textarea>
									</div>
								
									<?
									$sql = "SELECT * from corebridge_order where orderid='".$_GET['orderid']."' limit 1";
									$result = $conn->query($sql);
									if ($result->num_rows > 0){
										$t = $result->fetch_assoc();
						
										if($t['image']){
											$images = explode(",",$t['image']);
									?>
									<div class="form-group clearfix">
										<label class="grey">Image 1</label>
										<div class="form-control clearfix holder">
											<? $c=0;
											foreach($images as $img){ if($img){ $c++; ?>
												<input type='radio' name='image1' value='<?=$img?>' id="img1<?=$c?>" data-variable="image1" class="emailText" <? if($c==1) echo "checked"; ?> /><label for="img1<?=$c?>"><img src="/img/gallery/products/<?=$img?>" alt="Product Image" class="img-responsive" /></label>
												
											<? } } ?>
										</div>
									</div>
									<? if($c>1){?>
									<div class="form-group clearfix">
										<label class="grey">Image 2</label>
										<div class="form-control clearfix holder">
											<? $c=0;
											foreach($images as $img){ if($img){ $c++; ?>
												<input type='radio' name='image2' value='<?=$img?>' id="img2<?=$c?>" data-variable="image2" class="emailText" <? if($c==2) echo "checked"; ?> /><label for="img2<?=$c?>"><img src="/img/gallery/products/<?=$img?>" alt="Product Image" class="img-responsive" /></label>
												
											<? } } ?>
										</div>
									</div>
								<? } ?>
								
									<? }
									}?>
								


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



						
						<input type="hidden" name="orderid" value="<?=$_GET['orderid']?>" />

					</div>
					<div class="op-button text-center clear" style="margin-top:20px;">
						<button type="submit" class="btn btn-primary opmargin w-auto">Send</button>
					</div>
				</form>
				<!--End Template-->


			</div>
		  
		
		
		
		
	  </div>
    <? include ($_SERVER['DOCUMENT_ROOT'].'/includes/footer.php'); ?>
    
	<script type="text/javascript">
		$(document).ready(function(){
			loadEmail();
			//$.fn.dataTable.moment('MM/DD/YYYY');
			var table = $('#userTable').DataTable({
				responsive: true,
				"pageLength": 50,
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
					text: 'Export'}					
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
			$( "#selectAction" ).change(function() {
				var count = table.rows( { selected: true } ).count();
				if(count>0){
					var action = $(this).val();
					var values = [];
					$('tr.selected').each(function(){
					   var id=$(this).data('id');
						values.push(id);
					});
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
				if($(this).attr('type')=="radio"){
					vars[$(this).data("variable")] = $("input[name="+$(this).data("variable")+"]"+":checked").val();
				}else
					vars[$(this).data("variable")] = $(this).val();
			});
			console.log(vars);
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
		$(".emailText").change(function(){
			loadEmail();
		});

    </script>
  </body>
</html>