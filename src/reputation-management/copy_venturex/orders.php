<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <? include ($_SERVER['DOCUMENT_ROOT'].'/includes/head.php'); ?>
    <title> Orders | Local <?=$client?></title>
	
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
    	<h1>Orders</h1>
		<div class="pull-right form-inline">
			<span>Action:</span>
			<select name="action" class="form-control design w-auto" id="selectAction">
				<option value="">---</option>
				<option value="add">Add to Recipient List</option>
			</select>
		</div>
        
        <table class="table">
        	<thead>
            	<tr>
                	<th></th>
					<th>Invoice Number</th>
                    <th>Order Date</th>
					<th>Company Name</th>
					<th>Contact</th>
					<th>Description</th>
					<th>Order Total</th>
					<th>Review Requested</th>
					<th>Actions</th>
				            
                </tr>
            </thead>
            <tbody>
				<? 
				$sql="select review_sent,customerid,orderid,InvoiceNumber,date(DateCompleted) as DateCompleted,OrderDescription,CompanyName,OrderContact,OrderTotal from corebridge_order where storeid =  '".$_SESSION['storeid']."' and status in ('CLOSED','COMPLETED')";
				$result = $conn->query($sql);
				if ($result->num_rows > 0)
				while($orders = $result->fetch_assoc()){?>
            	<tr data-id="<?=$orders['customerid']?>">
					<td></td>
                	<td><?=$orders["InvoiceNumber"];?></td>
                    <td><?=$orders["DateCompleted"];?></td>
					<td><?=$orders["CompanyName"];?></td>
					<td><?=$orders["OrderContact"];?></td>
					<td><?=$orders["OrderDescription"];?></td>
					<td><?=$orders["OrderTotal"];?></td>
					<td><?=$orders["review_sent"];?></td>
					<td><a href="/reviews/order-detail.php?orderid=<?=$orders["orderid"];?>" class="btn">Upload Images</a></td>
                    
                </tr>
				<? } ?>
            </tbody>
        </table>
       
      
       
    </div>

    <? include ($_SERVER['DOCUMENT_ROOT'].'/includes/footer.php'); ?>
    
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
    <script type="text/javascript" src="//cdn.datatables.net/plug-ins/1.10.13/sorting/datetime-moment.js"></script>
	<script type="text/javascript">
		$(document).ready(function(){
			$.fn.dataTable.moment('dddd, MMMM Do, YYYY h:mm A');
			var table = $('table').DataTable({
				"pageLength": 25,
				"order":["1","desc"],
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
					if(action=="add"){
						if(confirm("Are you sure you want to add these emails to the recipient list?")){
							window.location.href = "add.php?action="+action+"&values="+values;
						}
					}
					 
				}else{
					$(this).val("");
				}
			});		
			
		});
	

	</script>

  </body>
</html>