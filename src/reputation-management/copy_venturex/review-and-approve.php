<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <? include ($_SERVER['DOCUMENT_ROOT'].'/includes/head.php'); ?>
    <title>Manage Reviews | Local <?=$client?></title>
	
    
  </head>
  <body>
  	<? include ($_SERVER['DOCUMENT_ROOT'].'/includes/nav.php'); ?>
	
    <div class="main">
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
        
    	<h1>Reviews <!--<a href="/includes/gradeus.php?orm_email=<?=$row["ORM_user"]?>" target="_blank" class="btn green pull-right">ORM</a>--></h1>
            <table>
                <thead>
                    <tr>
                        <th class="min-mobile">Portal</th>
                        <th class="min-tablet">Date</th>
                        <th class="min-mobile">Rating</th>
                        <th class="min-tablet">Author</th>
                        <th class="min-tablet">Review</th>
                        <th class="min-mobile">Approved</th>
                        <th class="min-mobile">View</th>
                    </tr>
                </thead>
                <tbody>
                    <? 
					$sql="select * from advtrack.client_review where client =  '".$_SESSION['client']."-".$_SESSION['storeid']."' order by date desc,portal";
					$result = $conn->query($sql);
					if ($result->num_rows > 0)
					while($reviews = $result->fetch_assoc()){?>
                        <tr>
                            <td><?=ucfirst($reviews['portal'])?></td>
                            <td><?=date('m/d/Y',strtotime($reviews['date']))?></td>
                            <td><?=$reviews['rating']?></td>
                            <td><?=$reviews['author']?></td>
                            <td><?=$reviews['review']?></td>
                            <td>
                                <select name="approved" data-id="<?=$reviews['id']?>">
                                    <option value="" data-reviewid="<?=$reviews['id']?>">Select</option>
                                    <option value="1" data-reviewid="<?=$reviews['id']?>" <? if ($reviews['approved']=="1") echo 'selected';?>>Yes</option>
                                    <option value="0" data-reviewid="<?=$reviews['id']?>" <? if ($reviews['approved']=="0") echo 'selected';?>>No</option>
                                </select>
							</td>
                            <td><a href="<?=$reviews['link']?>" class="btn" target="_blank">View</a></td>
                            
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
			$.fn.dataTable.moment('MM/DD/YYYY');
			$('table').DataTable({
				responsive: true,
				"pageLength": 25,
				"order": [[ 0, "desc" ]]
			});
			$('select[name="approved"]').change(function() {
			  var value = $(this).val();
			  var reviewid = $(this).find(':selected').data('reviewid');
			  var dataString = 'value='+ value + '&reviewid=' + reviewid;
			  $.ajax
			  ({
			   type: "POST",
			   url: "xt_updateApproval.php",
			   data: dataString,
			   cache: false,
			   success: function(html){
					if(html=="success"){
						$( "<p class='alert alert-success'>Your changes have been successfully saved.</p>" ).prependTo($(".main")).delay(2500).fadeOut("slow");
					}
			   } 
			   });
			});
		});

    </script>
  </body>
</html>