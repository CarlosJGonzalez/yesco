<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <? include ($_SERVER['DOCUMENT_ROOT'].'/includes/head.php'); ?>
    <title> Orders | Local <?=$client?></title>
	<style>
		.green {
			margin-bottom: 15px;
		}
		.browse {
			background: #fff;
    		color: #939598;
    		border-top: 1px solid #f4f4f4;
    		border-right: 1px solid #f4f4f4;
    		border-bottom: 1px solid #f4f4f4;
		}
		#custom-search-input .input-group-btn>.btn:active, 
		#custom-search-input .input-group-btn>.btn:focus, 
		#custom-search-input .input-group-btn>.btn:hover {
    		background: none;
    		color: #000;
		}
		.d-block{
			display: block;
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
    	<h1>Orders</h1>
		<?
		$orderid = $_GET['orderid'];
		$sql="select * from corebridge_order where storeid =  '".$_SESSION['storeid']."' and orderid='".$orderid."'";
		$result = $conn->query($sql);
		if ($result->num_rows > 0)
			$orders = $result->fetch_assoc();
			$imagelist = $orders["image"];
		?>

        
           <form action="xt_addImgAdmin.php" method="POST" enctype="multipart/form-data" name="addImg">
                    <div class="box" style="margin-bottom:15px;">
      				    <h2>
                        <ul class="no-bullets inline d-flex justify-content-between" id="store-info">
                            <li class="form-inline">Invoice Number: <span><?=$orders["InvoiceNumber"];?></span></li>
                            <li class="form-inline">Order Date: <span><?=date("m/d/Y",strtotime($orders["DateCompleted"]));?></span></li>
                        </ul>
                        </h2>
                        <div>
                            <div class="row">
                                <div class="col-xs-12 col-md-4">
                                    <div class="field">
                                        <label>Company Name</label>
                                        <span class="d-block"><?=$orders["CompanyName"];?></span>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-4">
                                    <div class="field">
                                        <label>Contact</label>
                                        <span class="d-block"><?=$orders["OrderContact"];?></span>
                                    </div>
                                </div>
                                
                                <div class="col-xs-12 col-md-4">
                                    <div class="field">
                                        <label>Order Total</label>
                                        <span class="d-block"><?=$orders["OrderTotal"];?></span>
                                    </div>
                                </div>
								<div class="col-xs-12">
                                    <div class="field">
                                        <label>Description</label>
                                        <span class="d-block"><?=$orders["OrderDescription"];?></span>
                                    </div>
                                </div>
                                <div class="col-xs-12">
                                       <div class="form-group clearfix">
                                    <label>Product Images</label>
									<div class="input-group">
                                        <label class="input-group-btn">
                                            <span class="btn btn-primary">
                                                <i class="fa fa-folder-open-o" aria-hidden="true"></i> Browse&hellip; <input type="file" name="fileToUpload[]" class="form-control" style="display: none;" multiple />
                                            </span>
                                        </label>
                                        <input type="text" class="form-control" readonly>
                                    </div>
										
                                     
                                 </div>
                                </div>
<?
	$imagelist=explode(',',$imagelist);
	$image_count = count($imagelist);
?>							
                          	<div class="col-xs-12"> 
								<input type="hidden" name="category" id="category" value="products"/>
								<input type="hidden" name="orderid" id="orderid" value="<?=$orders["orderid"];?>"/>
                         	    <input type="submit" class="btn green pull-right" name="Save" value="Save">
								<?
									if ($image_count > 1){
								?>
								<input type="submit" class="btn green pull-right" name="Review" value="Request Review">
								<?
									}
								?>
							</div>
							</div>
                        </div>
                    </div>
               </form> 
		
			   <div class='projects'>
				<?
				//echo $imagelist;
				foreach($imagelist as $image){
					if($image){
						$filename=explode(".", $image);
						$compressedImg=$filename[0]."-min.".$filename[1];
						$imgpath='/img/gallery/products-min/'.$compressedImg;
				?>
					
				    <div class="project" >
					  <a href='/img/gallery/products/<?=$image?>' 
						 class='fresco' 
						 >
						 <div><img  src="<?=$imgpath?>" alt='' /></div>
						</a>
						
					</div>
				<?
				}
				   }
				?>
			   </div>
        
       
      
       
    </div>

    <? include ($_SERVER['DOCUMENT_ROOT'].'/includes/footer.php'); ?>
  </body>
</html>