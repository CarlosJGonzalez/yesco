<!doctype html>
<html lang="en">
  <head>
	 
	<link rel="stylesheet" href="/css/fresco.css">
	  <link rel="stylesheet" href="/css/checkbox.css">
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/head.php");
	  include ($_SERVER['DOCUMENT_ROOT'].'/includes/stripe.php');
	  if(!(roleHasPermission('show_graphics_gallery', $_SESSION['role_permissions']))){
		header('location: /');
		  exit;
		}
	  ?>

    <title>Graphics Library Request Details | <?php echo CLIENT_NAME; ?></title>
	  
  </head>
  <body class="bg-light cbp-spmenu-push">
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/top.php"); ?>

    <div class="container-fluid">
      <div class="row">
        <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/nav.php"); ?>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 p-0 mb-4">
			<?php $_SESSION['admin'] = 0;
			$id = $db->escape($_GET['id']);
			$db->where("id",$id);
			$detail = $db->getOne("custom_requests");
			?>
			<div class="p-0 border-bottom mb-4">
				<div class="breadcrumbs bg-white px-3 py-1 border-bottom small">
					<a href="/graphics-gallery/" class="text-muted">Graphics Library</a>
					<span class="mx-1">&rsaquo;</span>
					<a href="/graphics-gallery/requests/" class="text-muted"> Requests</a>
					<span class="mx-1">&rsaquo;</span>
					<span class="font-weight-bold text-muted"> Details</span>
				</div>
				<div class="d-flex d-block align-items-center clearfix py-2 px-4">
					<h1 class="h2 font-light mb-0 text-center text-sm-left"><i class="fas fa-info-circle mr-2"></i> Details</h1>
					<div class="ml-auto">
						<span class="bg-white rounded-pill text-uppercase py-1 px-3 text-dark border"><?php echo $detail['status'] ?></span>
						<?php if(!in_array($detail['status'],array("Completed","Canceled"))){ ?>
						<a href="xt_cancel.php" title="Cancel Request" class="text-danger" id="cancelRequest" data-id="<?php echo $detail['id'] ?>"><i class="fas fa-ban ml-2 fa-lg"></i></a>
						<?php } ?>
					</div>
				</div>

			</div>
			
		
			<div class="px-4 py-3">		
				<?php include ($_SERVER['DOCUMENT_ROOT']."/includes/alerts.php"); ?>
				<div class="row">
					<div class="col-md-6">
						<h2 class="h3 mb-1 text-dark font-weight-bold"><?php echo stripcslashes($detail['title']) ?></h2>
						<span class="text-muted d-block mb-2"><?php echo date("m/d/Y g:i A T", strtotime($detail['start_date'])) ?></span>
						
						<p class="mb-1"><strong>Email: </strong> <?php if (isset($detail['email_address'])) echo $detail['email_address']; else echo 'N/A'; ?></p>
                        <p class="mb-1"><?php echo stripcslashes($detail['job_details']) ?></p>
						<ul class="fa-ul ml-4">
						  <li><span class="fa-li"><i class="fas fa-angle-right"></i></span> <span class="font-semibold" >Dimensions:</span> <?php echo !empty($detail['dimensions']) ? $detail['dimensions'] : "Not specified"; ?></li>
						  <li><span class="fa-li"><i class="fas fa-angle-right"></i></span> <span class="font-semibold" >Orientation:</span> <?php echo !empty($detail['orientation']) ? $detail['orientation'] : "Not specified"; ?></li>
						</ul>
						
						<div class="my-4">
							<h2 class="border-bottom d-block pb-2 mb-3 text-dark">Quote</h2>
							<p>Please allow up to 2 business days to receive the quote. If your request is urgent, contact <a href="mailto:support@das-group.com">support@das-group.com</a> regarding order #<?php echo $detail['id'] ?>. Rush fees may apply to graphics needed within 24 hours.</p>
							<div class="row">
								<div class="col-sm-6">
									<?php if(!is_null($detail['quote'])){ ?>
										<span class="h2 font-weight-light">$<?php echo $detail['quote']==0 ? "0" : number_format($detail['quote'], 2, '.', '')?></span>
									<?php }else if($_SESSION['admin']){ ?>
										<form action="xt_update.php" method="POST">
											<div class="input-group mb-3">
											  <div class="input-group-prepend">
												<span class="input-group-text" id="basic-addon-dollar">$</span>
											  </div>
											  <input type="text" class="form-control" aria-label="quote" aria-describedby="basic-addon-dollar">
												<div class="input-group-append">
													<input type="submit" value="Submit" class="btn bg-blue text-white">
												  </div>
											</div>
											<input type="hidden" name="action" value="quote">
											<input type="hidden" name="id" value="<?php echo $detail['id']?>">
										</form>
									<?php } ?>
								</div>
								<div class="col-sm-6 text-right">
									<?php if(!is_null($detail['quote']) && !in_array($detail['status'],array("Completed","Canceled")) && $detail['quote'] > 0){ ?>
										<button class="btn bg-blue text-white" type="button" data-toggle="collapse" data-target="#payCollapse" aria-expanded="false" aria-controls="payCollapse" id="payTrigger">
											Pay
										</button>
									<?php  }else if($detail['approved']=="Paid"){ ?>
										<div class="text-success h2 font-weight-light align-items-center justify-content-end d-flex"><span class="mr-2"><?php echo $detail['approved']?> </span>
											<svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2" class="checkAnimate show width-2">
											  <circle class="path circle" fill="none" stroke="#73AF55" stroke-width="6" stroke-miterlimit="10" cx="65.1" cy="65.1" r="62.1"/>
											  <polyline class="path check" fill="none" stroke="#73AF55" stroke-width="6" stroke-linecap="round" stroke-miterlimit="10" points="100.2,40.2 51.5,88.8 29.8,67.5 "/>
											</svg>
										</div>
									<?php }else if($detail['status']=="Canceled"){ ?>
										<div class="text-danger h2 font-weight-light align-items-center justify-content-end d-flex"><span class="mr-2">Canceled </span>
											<svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2" class="checkAnimate show width-2">
											  <circle class="path circle" fill="none" stroke="#dc3545" stroke-width="6" stroke-miterlimit="10" cx="65.1" cy="65.1" r="62.1"/>
											  <line class="path line" fill="none" stroke="#dc3545" stroke-width="6" stroke-linecap="round" stroke-miterlimit="10" x1="34.4" y1="37.9" x2="95.8" y2="92.3"/>
											  <line class="path line" fill="none" stroke="#dc3545" stroke-width="6" stroke-linecap="round" stroke-miterlimit="10" x1="95.8" y1="38" x2="34.4" y2="92.2"/>
											</svg>
										</div>

									<?php } ?>
								</div>
							</div>
							<div class="collapse mt-3" id="payCollapse">
								<div class="card card-body">
									<form action="xt_pay.php" method="POST" id="coForm">
										<?php
										//$active_location['customer_id']="cus_DkwMyZvt7Je3QV";
										$active_location['customer_id'];
										if(!empty($active_location['customer_id'])){ 
											try {
												$customer= \Stripe\Customer::retrieve($active_location['customer_id']);
											} catch(Stripe_CardError $e) {
											  $error1 = $e->getMessage();
											} catch (Stripe_InvalidRequestError $e) {
											  // Invalid parameters were supplied to Stripe's API
											  $error2 = $e->getMessage();
											} catch (Stripe_AuthenticationError $e) {
											  // Authentication with Stripe's API failed
											  $error3 = $e->getMessage();
											} catch (Stripe_ApiConnectionError $e) {
											  // Network communication with Stripe failed
											  $error4 = $e->getMessage();
											} catch (Stripe_Error $e) {
											  // Display a very generic error to the user, and maybe send
											  // yourself an email
											  $error5 = $e->getMessage();
											} catch (Exception $e) {
											  // Something else happened, completely unrelated to Stripe
											  $error6 = $e->getMessage();
											}
											if(!$error1 && !$error2 && !$error3 && !$error4 && !$error5 && !$error6){
												if($customer->default_source){
													echo "<span class='d-block mb-2 text-uppercase letter-spacing-1 font-bold text-dk-blue'>Select a Saved Card</span><div class='radio-group'>"; 
													foreach($customer->sources->data as $source){ ?>
													<div class="d-flex mb-3">
														<label class="label cusor-pointer d-flex text-center mr-2" for="<?php echo $source->id?>">
															<input class="label__checkbox post_check" type="radio" name="source" value="<?php echo $source->id?>" type="checkbox" id="<?php echo $source->id?>" required />
															<span class="label__text d-flex align-items-center">
															  <span class="label__check d-flex rounded-circle mr-2">
																<i class="fa fa-check icon small"></i>
															  </span>
															</span>
														</label>
														<div class="border p-3 flex-grow" data-value="<?=$source->id?>">
															<div class="row">
																<div class="col-sm-6">
																	<i class="fab fa-cc-<?php echo strtolower($source->brand)?> fa-2x pull-left" aria-hidden="true"></i> <br>
																	Ending: <strong><?php echo $source->last4?></strong><br>
																	Expiration: <strong><?php echo $source->exp_month.'/'.$source->exp_year?></strong>
																</div>
																<div class="col-sm-6">
																	<?php if(isset($source->name)) echo strtoupper($source->name);?>
																	<?php if(isset($source->address_line1)) echo '<br>'.strtoupper($source->address_line1);?>
																	<?php if(isset($source->address_city)) echo '<br>'.strtoupper($source->address_city).' '.strtoupper($source->address_state).', '.$source->address_zip;?>
																</div>
															</div>
														</div>
													</div>

											<?php		} ?>

											<?php  echo "</div>";  }
											}else{
												echo $error1.'-1<br>';
												echo $error2.'-2<br>';
												echo $error3.'-3<br>';
												echo $error4.'-4<br>';
												echo $error5.'-5<br>';
												echo $error6.'-6<br>';
											}
										?>
											<div class="text-center checkout_btn"><button class="btn bg-blue text-white" type="submit">Pay</button></div>
										<?php }else{ ?>
											<div class="notice notice-danger box-shadow">
												You have not set up any payment methods yet. To proceed, please <a href="/my-account/payment-methods/" class="text-blue">set up a payment method.</a>
											</div>
										<?php } ?>
										<input type="hidden" name="id" value="<?php echo $detail['id']?>">
										<input type="hidden" name="customer_id" value="<?=$active_location['customer_id']?>">
										
									</form>
								</div>
							</div>

						</div>
						
						
						
						<?php
						$db->orderBy("date","desc");
						$db->where("request_id",$id);
						$revisions = $db->get("custom_requests_revisions");
						if ($db->count > 0){ ?>
						<div class="my-4">
							<h2 class="border-bottom d-block pb-2 mb-3 text-dark">Graphics</h2>
							<div class="row">
							<?php $images = array('jpg','png' ,'jpeg');
							foreach($revisions as $file){
								$ext = end(explode(".",$file['filename']));
								?>
								<div class="col-sm-6 col-md-3 text-center clearfix">
									<?php if(in_array(strtolower($ext),$images)){ ?><a href="<?php echo $file['filename']?>" class="fresco thumbnail"><img src="<?php echo $file['filename']?>" alt="Revision" class="img-fluid border" /></a><?php } ?>
									<a href="<?php echo $file['filename']?>" class="bg-blue p-1 mb-1 d-block text-white small text-uppercase" download>Download</a>
									<p class="small">Uploaded By <?php echo $file['uploaded_by']?><br><?php echo date("m/d/Y g:i A T",strtotime($file['date']))?></p>
								</div>
							<?php } ?>
							</div>
						</div>
						<?php } ?>
						
					</div>
					<div class="col-md-6">
						<?php
						if($_SESSION['view']=="user") $person="client";
							else $person="admin";
							
							$db->orderBy("date","asc");
							$db->where("request_id",$id);
							$comments = $db->get("custom_requests_comments");
							if ($db->count > 0){
						?>
                        <div class="comments mb-2 bg-white p-3 border rounded">
                        	<?php
								foreach($comments as $comment){
									$comment = replace_characters($comment, "<br>");
							?>
                        		<div class="msg <?php if($comment['person']==$person) echo "float-right"; else echo "float-left"; ?> ">
                                    <p><?php echo $comment['message'];?></p>
                                    <small class="d-block text-right"><?php echo $comment['user'].' '.date("m/d/Y g:i A T",strtotime($comment['date']));?></small>
                                </div>
                            <?php } ?>
                            
                        </div>
						<?php } ?>
						<form action="/admin/graphics-gallery/xt_addComment.php" method="POST">
							<div class="form-group">
								<textarea class="form-control" name="message"></textarea>
							</div>
							<input type="hidden" name="id" value="<?php echo $id?>">
							<input type="hidden" name="person" value="<?php echo $person?>">
							<input type="hidden" name="storeid" value="<?php echo $detail['storeid'] ?>">
							<div class="text-center"><input type="submit" value="Add Comment" class="btn btn-sm bg-blue text-white"></div>
						</form>
						
					</div>
				</div>
			</div>
        
        </main>
      </div>
    </div>


    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>
	  <script type="text/javascript" src="/js/fresco.js"></script>
	<script type="text/javascript">
		$(document).ready(function(){
			$(".comments").animate({ scrollTop: $('.comments').prop("scrollHeight")}, 1000);
		});
		$("#cancelRequest").click(function(e){
			e.preventDefault();
			var id = $(this).data("id");
			if(confirm("Are you sure you want to cancel this request?")){
				window.location.replace("xt_cancel.php?id="+id+"&type=user");
			}
		});
		$('#payCollapse').on('hide.bs.collapse', function (e) {
			$("#payTrigger").text("Pay");
		});
		$('#payCollapse').on('show.bs.collapse', function (e) {
			$("#payTrigger").text("Close");
		});
    </script>
  </body>
</html>