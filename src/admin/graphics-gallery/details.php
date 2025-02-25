<!doctype html>
<html lang="en">
  <head>
	 
	<link rel="stylesheet" href="/css/fresco.css">
	  <link rel="stylesheet" href="/css/checkbox.css">
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/head.php"); ?>

    <title>Graphics Library Request Details | <?php echo CLIENT_NAME; ?></title>
	  
  </head>
  <body class="bg-light cbp-spmenu-push">
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/top.php"); ?>

    <div class="container-fluid">
      <div class="row">
        <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/nav.php"); ?>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 p-0 mb-4">
			<?php
			$id = $db->escape($_GET['id']);
			$detail = $db->where("id",$id)->getOne("custom_requests");
			$graphic = $db->where("id",$detail['gallery_id'])->getOne("gallery");
			?>
			<div class="p-0 border-bottom mb-4">
				<div class="breadcrumbs bg-white px-3 py-1 border-bottom small">
					<a href="/admin/graphics-gallery/" class="text-muted">Graphics Library</a>
					<span class="mx-1">&rsaquo;</span>
					<a href="/admin/graphics-gallery/manage.php" class="text-muted"> Graphic Requests</a>
					<span class="mx-1">&rsaquo;</span>
					<span class="font-weight-bold text-muted">Request Details</span>
				</div>
				<div class="d-flex d-block align-items-center clearfix py-2 px-4">
					<h1 class="h2 font-light mb-0 text-center text-sm-left"><i class="fas fa-info-circle mr-2"></i> Request Details</h1>
					<div class="ml-auto">
						<span class="bg-white rounded-pill text-uppercase py-1 px-3 text-dark border"><?php echo $detail['status'] ?></span>
					</div>
				</div>

			</div>
			
		
			<div class="px-4 py-3">	
				<?php include $_SERVER['DOCUMENT_ROOT']."/includes/alerts.php"; ?>
				<div class="row">
					<div class="col-md-6">
						<h2 class="h3 mb-1 text-dark font-weight-bold"><?php echo $detail['title'] ?></h2>
						<span class="text-muted d-block mb-1"><?php echo date("m/d/Y g:i A T", strtotime($detail['start_date'])) ?></span>
						<?php if(!empty($graphic)){ ?><a href="<?php echo $graphic['image']; ?>" class="text-blue d-inline-block mb-2 font-italic" target="_blank">View Graphic</a><?php } ?>
						
						<p><strong>Email: </strong> <?php if (isset($detail['email_address'])) echo $detail['email_address']; else echo 'N/A'; ?></p>
                        <p><?php echo stripcslashes($detail['job_details']); ?></p>
                        <?php
                        if(!empty($detail['dimensions'])){
                            $dim=explode(" ",$detail['dimensions']);
                            $d=explode("x",$dim[0]); 
                            if(strtolower($dim[1])=="pixels"){
                                if($d[0] > $d[1]){
                                    $greater=$d[0];
                                    $less=$d[1];
                                }else{
                                    $greater=$d[1];
                                    $less=$d[0];
                                }
                                if($detail['orientation']=="Landscape"){
                                    $width=$greater;
                                    $height=$less;
                                }else if($detail['orientation']=="Portrait"){
                                    $width=$less;
                                    $height=$greater;
                                }else{
									$width=$less;
                                    $height=$less;
								}?>
                            <img src="http://via.placeholder.com/<?php echo $width.'x'.$height?>" alt="<?php echo $width.'x'.$height.' '.$dim[1]?> " class="rounded border p-1 mx-auto d-block">
							<small class="d-block text-center"><?php echo $detail['dimensions']?></small>
                        <?php }else
                            echo "<strong>Dimensions: </strong>".$detail['dimensions'];
                        }?>
						
						<div class="my-4">
							<h2 class="border-bottom d-block pb-2 mb-3 text-dark">Quote</h2>
							<p>Please allow up to 2 business days to receive the quote. If your request is urgent, contact <a href="mailto:support@das-group.com">support@das-group.com</a> regarding order #<?php echo $detail['id'] ?>. Rush fees may apply to graphics needed within 24 hours.</p>
							<div class="row">
								<div class="col-sm-6">
									<?php if(!is_null($detail['quote'])){ ?>
										<span class="h2 font-weight-light">$<?php echo $detail['quote']==0 ? "0" : number_format($detail['quote'], 2, '.', '')?></span>
									<?php }else { ?>
										<form action="/admin/graphics-gallery/xt_addPrice.php" method="POST">
											<div class="input-group mb-3">
												<div class="input-group-prepend">
													<span class="input-group-text">$</span>
												</div>
											  <input type="text" name="quote" class="form-control"  aria-describedby="button-addon2">
											  <div class="input-group-append">
												<button class="btn bg-blue text-white" type="submit" id="button-addon2">Save</button>
											  </div>
											</div>
											<input type="hidden" name="action" value="quote">
											<input type="hidden" name="id" value="<?php echo $detail['id']?>">
										</form>
									<?php } ?>
								</div>
								<div class="col-sm-6 text-right">
									<?php
									if(!is_null($detail['quote'])){
										if($detail['approved']=="Approved" || $detail['approved']=="Paid"){
										?>
										<div class="text-success h2 font-weight-light align-items-center justify-content-end d-flex"><span class="mr-2"><?php echo $detail['approved']?> </span>
											<svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2" class="checkAnimate show width-2">
											  <circle class="path circle" fill="none" stroke="#73AF55" stroke-width="6" stroke-miterlimit="10" cx="65.1" cy="65.1" r="62.1"/>
											  <polyline class="path check" fill="none" stroke="#73AF55" stroke-width="6" stroke-linecap="round" stroke-miterlimit="10" points="100.2,40.2 51.5,88.8 29.8,67.5 "/>
											</svg>
										</div>
										<?php
										}else if($detail['status']=="Canceled"){
										?>
										<div class="text-danger h2 font-weight-light align-items-center justify-content-end d-flex"><span class="mr-2">Canceled </span>
											<svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2" class="checkAnimate show width-2">
											  <circle class="path circle" fill="none" stroke="#dc3545" stroke-width="6" stroke-miterlimit="10" cx="65.1" cy="65.1" r="62.1"/>
											  <line class="path line" fill="none" stroke="#dc3545" stroke-width="6" stroke-linecap="round" stroke-miterlimit="10" x1="34.4" y1="37.9" x2="95.8" y2="92.3"/>
											  <line class="path line" fill="none" stroke="#dc3545" stroke-width="6" stroke-linecap="round" stroke-miterlimit="10" x1="95.8" y1="38" x2="34.4" y2="92.2"/>
											</svg>
										</div>
										
										<?php } ?>
									
									<?php 
									}?>
								</div>
							</div>

						</div>
						

						<?php if($detail['approved']=="Paid"){?>
						<div class="my-4">
							<h2 class="border-bottom d-block pb-2 mb-3 text-dark">Upload Graphic</h2>
							<form action="xt_uploadRevision.php" method="POST" enctype="multipart/form-data">
								<div class="form-group">
									<div class="input-group mb-3">
									  <div class="custom-file">
										<input type="file" class="custom-file-input" name="fileToUpload" id="inputGroupFile01">
										<label class="custom-file-label" for="inputGroupFile01">Choose file</label>
									  </div>
									</div>
								</div>
<!--
								<div class="form-group">
									<label class="label cusor-pointer d-flex text-center mr-2" for="add_gallery">
										<input class="label__checkbox post_check" type="checkbox" name="source" value="1" type="checkbox" id="add_gallery" />
										<span class="label__text d-flex align-items-center">
										  <span class="label__check d-flex rounded-circle mr-2">
											<i class="fa fa-check icon small"></i>
										  </span>
										</span>
										<span class="text-uppercase letter-spacing-1">Add to Graphics Library</span>
									</label>
								</div>
-->
								<input type="hidden" name="id" value="<?php echo $detail['id']?>">
								<input type="hidden" name="storeid" value="<?php echo $detail['storeid']?>">
								<p class="text-center"><input type="submit" value="Upload" class="btn btn-sm bg-blue text-white"></p>
							</form>
						</div>
						<?php } ?>
						
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
									<?php if(in_array(strtolower($ext),$images)){ ?> <a href="<?php echo $file['filename']?>" class="fresco thumbnail"><img src="<?php echo $file['filename']?>" alt="Revision" class="img-fluid border" /></a><?php } ?>
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
						if ($db->count > 0){ ?>
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

                        
						<form action="xt_addComment.php" method="POST">
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
    </script>
  </body>
</html>