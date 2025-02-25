<!doctype html>
<html lang="en">
  <head>
	<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
	<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>
	<link rel="stylesheet" href="/css/fresco.css">
	<link rel="stylesheet" href="/css/checkbox.css">
	<link rel="stylesheet" href="/css/croppie.css" />
	<link rel="stylesheet" href="/css/pajinatify.css" />
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/head.php");
	  if(!(roleHasPermission('show_graphics_gallery', $_SESSION['role_permissions']))){
		header('location: /');
		  exit;
		}
	  ?>

    <title>Graphics Library | <?php echo CLIENT_NAME; ?></title>
  </head>
  <body class="bg-light cbp-spmenu-push">
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/top.php"); ?>

    <div class="container-fluid">
      <div class="row">
        <?php 
		include ($_SERVER['DOCUMENT_ROOT']."/includes/nav.php");
		//Get location info
		$cols = Array ("email");
		$locationList = $db->where("storeid",$_SESSION['storeid'])->getOne("locationlist", $cols);
		?>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-0 mb-4">
			<div class="p-0 border-bottom">
				<div class="d-flex d-block align-items-center clearfix py-2 px-4">
					<h1 class="h2 font-light mb-0 text-center text-sm-left"><i class="far fa-images mr-2"></i> Graphics Library</h1>
					<div class="ml-auto">
						<div class="dropdown d-inline-block">
						  <button type="button" id="dropdownMenuButton"  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="border-0 bg-transparent">
							<i class="fas fa-2x text-muted fa-ellipsis-v"></i>
						  </button>
						  <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
							  <a href="#uploadImageModal" title="Add Image" data-toggle="modal" data-target="#uploadImageModal" class="dropdown-item small">Add Image</a>
							  <a href="requests/" title="View Orders" class="dropdown-item small">View Requests</a>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<div class="py-3">
				<?php include ($_SERVER['DOCUMENT_ROOT']."/includes/alerts.php"); ?>
			</div>
			
			<?php 
				$favs = $db->rawQuery("select * from gallery where id in (select gallery_id from gallery_favs where storeid = ?)",array($_SESSION['storeid']));
			 ?>
			<div class="featured bg-white border-bottom mb-4 p-3 <?php if($db->count == 0) echo "d-none"; ?>">
				<div class="px-4">
					<div class="slider">
						<?php 
							foreach($favs as $fav){
						 ?>
						<div class="text-center" data-id="<?php echo $fav['id'] ?>"><div class="hexagon hexagon2 m-auto"><div class="hexagon-in1"><div class="hexagon-in2" style="background-image:url(<?php echo $fav['thumbnail'] ?>)">
							<div class="overlay">
								<p class="mb-0 text-center position-absolute">
									<a href="<?php echo $img['image2'] ?>" title="Download" class="text-white downloadImg" data-id="<?php echo $fav['id'] ?>" download><i class="fas fa-download"></i></a>
									<a href="" title="Favorite" class="text-white ml-2 favImg fav" data-id="<?php echo $fav['id'] ?>"><i class="fas fa-heart icon"></i></a>
									<a href="#requestImage" title="Customize" class="text-light-d ml-2" data-toggle="modal" data-target="#requestImage" data-id="<?php echo $img['id'] ?>"><i class="fas fa-paint-brush"></i></a>
									<a href="<?php echo $fav['image'] ?>" data-fresco-caption="<?php echo $fav['name'] ?>" title="Expand" class="fresco text-white ml-2"><i class="fas fa-expand-arrows-alt"></i></a>
									<?php if($_POST['status']=="user" && $_SESSION['storeid']==$img['storeid']){ ?>
										<a href="#editImage" title="Edit" class="text-light-d ml-2" data-toggle="modal" data-target="#editImage" data-id="<?php echo $img['id'] ?>"><i class="fas fa-edit"></i></a>
										<a href="#editThumbnail" title="Edit Thumbnail" class="text-light-d ml-2 editThumb" data-toggle="modal" data-target="#editThumbnail"><i class="fas fa-magic"></i></a>
									<?php } ?>
								</p>
							</div>
						</div></div></div></div>
						<?php } ?>
						
					</div>
				</div>
			</div>
			
			<div class="bg-white p-3 mb-4 d-none d-md-block">
				<h3 class="font-light text-center">Share your photos with the <strong><?php echo CLIENT_NAME; ?></strong> community.</h3>
				<div class="row py-2">
					<div class="d-none d-sm-block col-sm-6">
						<div class="d-flex justify-content-center align-items-center">
							<div class="bg-blue p-3 text-white"><i class="far fa-2x fa-images"></i></div>
							<div class="ml-2 text-left">
								<span class="d-block text-muted text-uppercase font-12">Photos uploaded</span>
								<?php $photoCount = $db->rawQueryOne("select count(*) as count from gallery");?>
								<span class="d-block font-24 font-bold"><?php echo number_format($photoCount['count']); ?></span>
							</div>
						</div>
					</div>
					<div class="d-none d-sm-block col-sm-6">
						<div class="d-flex justify-content-center align-items-center">
							<div class="bg-secondary p-3 text-white"><i class="fas fa-2x fa-download"></i></div>
							<div class="ml-2 text-left">
								<?php $dlCount = $db->rawQueryOne("select count(*) as count from gallery_downloads");?>
								<span class="d-block text-muted text-uppercase font-12">Downloads</span>
								<span class="d-block font-24 font-bold"><?php echo number_format($dlCount['count']); ?></span>
							</div>
						</div>
					</div>
<!--
					<div class="d-none d-sm-block col-sm-4">
						<div class="d-flex justify-content-center align-items-center">
							<div class="bg-dark-blue p-3 text-white"><i class="fas fa-2x fa-users"></i></div>
							<div class="ml-2 text-left">
								<span class="d-block text-muted text-uppercase font-12">Franchisees</span>
								<?php $fCount = $db->rawQueryOne("select count(*) as count from locationlist where suspend <> 1");?>
								<span class="d-block font-24 font-bold"><?php echo $fCount['count'] ?></span>
							</div>
						</div>
					</div>
-->
				</div>
				
			</div>
			<div class="px-4 py-3">
				<div class="d-block d-xl-flex align-items-center mb-3">
					<a class="small text-blue d-block d-lg-none" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">Advanced Search</a>
					
					<div class="collapse show w-100" id="collapseExample">
						<div class="d-xl-flex">
							<div class="d-flex d-xl-inline-block mb-2 mb-xl-0 mr-2 align-items-center">
								<span class="letter-spacing-1 text-uppercase small mr-2 mr-xl-0">Category: </span>
								<select name="category_search" class="flex-grow d-xl-inline-block form-control form-control-sm w-auto rounded-pill custom-select-arrow pr-4">
									<option value="">All Categories</option>
									<?php
									$ids = $db->subQuery ();
									$ids->where ("category", '', "!=");
									$ids->get ("gallery", null, "category");
									
									$db->where("option","gallery_cat");
									$db->where ("value", $ids, 'IN');
									$vals = $db->get ("option_values", null, array("value", "display_name"));
									// Gives SELECT value, display_name FROM option_values WHERE option = 'gallery_cat' AND value IN (SELECT  category FROM gallery WHERE  category != '') 	
									
									foreach($vals as $val){
									?>
									<option value="<?php echo $val['value']; ?>"><?php echo $val['display_name']; ?></option>
									<?php } ?>
								</select>
							</div>
<!--
							<div class="d-flex d-xl-inline-block mb-2 mb-xl-0 mr-2 align-items-center">
								<span class="letter-spacing-1 text-uppercase small mr-2 mr-xl-0">Month:</span>
								<select name="month" class="flex-grow d-xl-inline-block form-control form-control-sm w-auto rounded-pill custom-select-arrow pr-4">
									<option value="">All Months</option>
									<?php $months = array(
												  'January', 
												  'February', 
												  'March', 
												  'April', 
												  'May', 
												  'June', 
												  'July', 
												  'August', 
												  'September', 
												  'October', 
												  'November', 
												  'December'
												);
									foreach($months as $month){?>
										<option value="<?php echo $month ?>"><?php echo $month ?></option>
									<?php } ?>
								</select>
							</div>
-->
							<div class="search-post d-flex d-xl-inline-block mb-2 mb-xl-0 mr-2 align-items-center">
								<span class="letter-spacing-1 text-uppercase small mr-2 mr-xl-0">Search: </span>
								<div class="flex-grow d-xl-inline-block">
									<div class="input-group input-group-sm">
									  <div class="input-group-prepend">
										<span class="input-group-text border-right-0 bg-white pr-0" id="basic-addon1"><i class="fas fa-search"></i></span>
									  </div>
									  <input type="text" class="form-control border-left-0" placeholder="Search for an image..." aria-label="Search" aria-describedby="basic-addon1" id="searchPost">
									</div>
								</div>
							</div>
							<div class="ml-auto">
								<div class="d-flex d-xl-inline-block mb-2 mb-xl-0 mr-2 align-items-center">
									<span class="letter-spacing-1 text-uppercase small mr-2 mr-xl-0">Sort By: </span>
									<select name="sort" class="flex-grow d-xl-inline-block form-control form-control-sm w-auto rounded-pill custom-select-arrow pr-4">
										<option value="newest">Newest to Oldest</option>
										<option value="oldest">Oldest to Newest</option>
<!--										<option value="favorites">Favorites</option>-->
										<option value="a-z">Name A-Z</option>
										<option value="z-a">Name Z-A</option>
									</select>
								</div>
							</div>
						</div>

					</div>
						
					
				</div>
				
				<!-- Upload Image Modal -->
			<form action="/admin/graphics-gallery/xt_upload.php" method="POST" enctype="multipart/form-data">
				<div class="modal fade" id="uploadImageModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalTitle" aria-hidden="true">
				  <div class="modal-dialog modal-dialog-centered" role="document">
					<div class="modal-content">
					  <div class="modal-header">
						<h5 class="modal-title" id="uploadModalTitle">Upload Image</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						  <span aria-hidden="true">&times;</span>
						</button>
					  </div>
					  <div class="modal-body">
							<div class="alert alert-primary" role="alert">
								<strong>Note:</strong> Images from the Gallery may be used for marketing, on the website, etc., please ensure that explicit written permission has been obtained when adding images to the Gallery. Email <b>fpmarketing@ufgcorp.com</b> for a release form.
							</div>
							<div class="form-group">
								<label class="text-uppercase small">Image Label<span class="text-danger"> * </span></label>
								<input type="text" name="name" class="form-control" required>
							</div>
							<div class="form-group">
								<label class="text-uppercase small">Image(s)<span class="text-danger"> *</span></label>
								<div class="input-group mb-3">
								  <div class="custom-file">
									<input type="file" class="custom-file-input" id="inputGroupFile01" name="fileToUpload[]" onchange="validateFiles(this.id,'imgMsgContainer','image','uploadImgBtn',20,2000000)" accept="image/jpg, image/png, image/jpeg, image/gif" multiple required>
									<label class="custom-file-label" for="inputGroupFile01">Choose file</label>
								  </div>
								</div>
								<small id="imgMsgContainer">Only image files are accepted.</small>
							</div>
							<div class="form-group">
								<label class="text-uppercase small">Category<span class="text-danger"> * </span></label>
								<select name="category" class="form-control custom-select-arrow pr-4" required>
									<?php
									$db->where("option","gallery_cat");
									$vals = $db->get("option_values");
									foreach($vals as $val){
									?>
									<option value="<?php echo $val['value']; ?>"><?php echo $val['display_name']; ?></option>
									<?php } ?>
								</select>
							</div>
							<div class="form-group">
								<label class="text-uppercase small">Tags <span data-toggle="tooltip" data-placement="top" title="Enter tags, or keywords, separated by commas. They should be descriptive of the image you are uploading (i.e., “drinkware, coffee mug, mug”). The more you add the better – this helps the images be “findable” in searches of the Graphics Gallery."><i class="far fa-question-circle"></i></span></label>
								<input type="text" name="tags" class="form-control">
							</div>
						  	<div class="form-group">
								<label class="label cusor-pointer d-flex text-center" for="apply_all">
									<input  class="label__checkbox" type="checkbox" name="apply_all" value="1" type="checkbox" id="apply_all" checked />
									<span class="label__text d-flex align-items-center">
									  <span class="label__check d-flex rounded-circle mr-2">
										<i class="fa fa-check icon small"></i>
									  </span>
										<span class="text-uppercase small letter-spacing-1 d-inline-block">Make available for all locations</span>
									</span>
								  </label>
						  	</div>

					 	</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
							<input type="submit" id="uploadImgBtn" class="btn bg-blue text-white btn-sm" value="Upload Image">
							<input type="hidden" name="type" value="user">
						</div>

					</div>
				  </div>
				</div>
			</form>
			
				
			<!-- Edit Thumbnail -->
			<form action="saveThumbnail.php" method="POST" id="cropImage">
				<div class="modal fade" id="editThumbnail" tabindex="-1" role="dialog" aria-labelledby="editThumbnailLabel" aria-hidden="true">
				  <div class="modal-dialog" role="document">
					<div class="modal-content">
					  <div class="modal-header">
						<h5 class="modal-title" id="editThumbnailLabel">Edit Thumbnail</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						  <span aria-hidden="true">&times;</span>
						</button>
					  </div>
					  <div class="modal-body mb-4">
						<div id="img-thumb" class="center-block"></div> 
					  </div>
					  <div class="modal-footer mt-4">
						  <input type="hidden" id="croppiebase64" name="croppiebase64">
						  <input type="hidden" id="ogpath" name="ogpath">
						<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
						<input type="submit" class="btn bg-blue text-white btn-sm" value="Save">
					  </div>
					</div>
				  </div>
				</div>
			</form>
			<!-- /Edit Thumbnail -->
				
			<!-- Edit Image -->
			<form action="/admin/graphics-gallery/xt_editImage.php" method="POST">
				<div class="modal fade" id="editImage" tabindex="-1" role="dialog" aria-labelledby="editImageLabel" aria-hidden="true">
				  <div class="modal-dialog" role="document">
					<div class="modal-content">
					  <div class="modal-header">
						<h5 class="modal-title" id="editImageLabel">Edit Image</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						  <span aria-hidden="true">&times;</span>
						</button>
					  </div>
					  <div class="modal-body">
						
					  </div>
					  <div class="modal-footer">
						<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
						<input type="submit" class="btn bg-blue text-white btn-sm" value="Save">
					  </div>
					</div>
				  </div>
				</div>
			</form>
			<!-- /Edit Image -->
				
			<!-- Customize-->
			<form action="requests/xt_request.php" method="POST">
				<div class="modal fade" id="requestImage" tabindex="-1" role="dialog" aria-labelledby="requestImageLabel" aria-hidden="true">
				  <div class="modal-dialog modal-dialog-centered" role="document">
					<div class="modal-content">
					  <div class="modal-header">
						<h5 class="modal-title" id="requestImageLabel">Request Image</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						  <span aria-hidden="true">&times;</span>
						</button>
					  </div>
					  <div class="modal-body">
					  	<div class="form-group">
							<label class="text-uppercase small">Email Address<span class="text-danger"> * </span></label>
							<input type="email" name="email_address" class="form-control" value="<?php echo $locationList['email'];?>" required>
						</div>
						<div class="form-group">
							<label class="text-uppercase small">Special Instructions<span class="text-danger"> * </span></label>
							<textarea name="job_details" class="form-control" required></textarea>
						</div>
					  </div>
					  <div class="modal-footer">
						<input type="hidden" name="gallery_id">
						<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
						<input type="submit" class="btn bg-blue text-white btn-sm" value="Request">
					  </div>
					</div>
				  </div>
				</div>
			</form>
			<!-- /Customize-->
				
			<!-- Contains gallery images -->
			<div class="gallery">
			
			</div>
			
			<!-- Contains pagination link numbers -->
			<div class='pagination' data-total-count='1' data-take='24'></div>
			
			</div>
			
        </main>
      </div>
    </div>


    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>
	<script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
	<script type="text/javascript" src="/js/fresco.js"></script>
	<!--<script src="/js/buzina-pagination.min.js"></script>-->
	<script src="/js/jquery.pajinatify.js"></script>
	<script src="/js/croppie.min.js"></script>
	<script type="text/javascript">
		$(document).ready(function(e){
			$('.slider').slick({
			  infinite: false,
			  slidesToShow: 6,
			  slidesToScroll: 1,
			  responsive: [
				{
				  breakpoint: 1300,
				  settings: {
					slidesToShow: 4,
				  }
				},
				  {
				  breakpoint: 1100,
				  settings: {
					slidesToShow: 3,
				  }
				},
				{
				  breakpoint: 800,
				  settings: {
					slidesToShow: 2,
					slidesToScroll: 2
				  }
				},
				{
				  breakpoint: 600,
				  settings: {
					slidesToShow: 1,
					slidesToScroll: 1
				  }
				}

			  ]
			});
			
			get_images();
			printPagination();				
        });
		$("select[name=sort]").change(function(e){
			e.preventDefault();
			printPagination($("select[name=category_search]").val(),$("select[name=month]").val(),$("#searchPost").val(),$("select[name=sort]").val());
			get_images($("select[name=category_search]").val(),$("select[name=month]").val(),$("#searchPost").val(),$("select[name=sort]").val())
		});
		$("select[name=month]").change(function(e){
			e.preventDefault();
			printPagination($("select[name=category_search]").val(),$("select[name=month]").val(),$("#searchPost").val(),$("select[name=sort]").val());
			get_images($("select[name=category_search]").val(),$("select[name=month]").val(),$("#searchPost").val(),$("select[name=sort]").val())
		});
		$("select[name=category_search]").change(function(e){
			e.preventDefault();
			printPagination($("select[name=category_search]").val(),$("select[name=month]").val(),$("#searchPost").val(),$("select[name=sort]").val());
			get_images($("select[name=category_search]").val(),$("select[name=month]").val(),$("#searchPost").val(),$("select[name=sort]").val())
		});
		$("#searchPost").keyup(function(e){
			e.preventDefault();
			printPagination($("select[name=category_search]").val(),$("select[name=month]").val(),$("#searchPost").val(),$("select[name=sort]").val());
			get_images($("select[name=category_search]").val(),$("select[name=month]").val(),$("#searchPost").val(),$("select[name=sort]").val())
		});
		$(document).on("click",".pajinatify__button", function(e) {
			current_page = $(this).data("page");
			get_images($("select[name=category_search]").val(),$("select[name=month]").val(),$("#searchPost").val(),$("select[name=sort]").val(),current_page)
		});
		
		function get_images(category="",month="",search="",sort="",current_page=1){
			$.ajax({
                type: "POST",
                url: "/includes/get_gallery_images.php",
                data: {"sort":sort,"category":category,"search":search,"month":month,"status":"user", "current_page":current_page},
                cache: false,
				beforeSend:function(html){
                    //$(".gallery").html('<div class="text-center"><img src="/img/loading.svg"></div>');
					$(".gallery").html('<div class="text-center"><h2><b>Loading...</b></h2></div>');
                },
                success: function(html){
                    $(".gallery").html(html);
                },
				error: function(xhr, status, error) {
				  var err = eval("(" + xhr.responseText + ")");
				  //console.log(err.Message);
				} 
            });
		}
		
		function printPagination(category="",month="",search="",sort=""){
			$.ajax({
                type: "POST",
                url: "/includes/get_gallery_images_data.php",
                data: {"sort":sort,"category":category,"search":search,"month":month,"status":"user"},
                cache: false,
				beforeSend:function(html){
					$(".pagination").html('<div class="text-center"><h4><b>Loading ...</b></h4></div>');
                },
                success: function(html){
                    $(".pagination").data("total-count", html);
			
					$('.pagination').pajinatify('destroy');

					$('.pagination').pajinatify({
						onChange:function (currentPage) {
						//console.log(currentPage);
						}
					});
                },
				error: function(xhr, status, error) {
				  var err = eval("(" + xhr.responseText + ")");
				  //console.log(err.Message);
				} 
            });
			
		}
		
		$(document).on('click','.favImg',function(e){
			e.preventDefault();
			var obj = $(this);
			var id = $(obj).data("id");
			$.ajax({
                type: "POST",
                url: "fav_update.php",
                data: {"id":id},
				dataType:"json",
                cache: false,
                success: function(result){
                    $(obj).addClass("clicked");
						setTimeout(function () {
						$('.favImg.clicked').removeClass('clicked');
					}, 1000);
						if($(obj).hasClass("fav")){
							$("[data-id='"+id+"']").removeClass("fav");
							var index = $('div[data-id="'+result.id+'"]').data('slick-index');
							$('.slider').slick('slickRemove',index);
						}else{
							$(obj).addClass("fav");
							if($(".featured").hasClass("d-none")){
								$(".featured").removeClass("d-none")
							}
							$('.slider').slick('slickAdd','<div class="text-center" data-id="'+result.id+'"><div class="hexagon hexagon2 m-auto"><div class="hexagon-in1"><div class="hexagon-in2" style="background-image:url('+result.thumbnail+')"><div class="overlay"><p class="mb-0 text-center position-absolute"><a href="" title="Download" class="text-white"><i class="fas fa-download"></i></a><a href="" title="Favorite" class="text-white ml-2 favImg fav"><i class="fas fa-heart icon"></i></a><a href="" title="Customize" class="text-white ml-2"><i class="fas fa-paint-brush"></i></a><a href="'+result.image+'" data-fresco-caption="'+result.name+'" title="Expand" class="fresco text-white ml-2"><i class="fas fa-expand-arrows-alt"></i></a></p></div></div></div></div></div>');
						}
					
                },
				error: function(xhr, status, error) {
				  var err = eval("(" + xhr.responseText + ")");
				  console.log(err.Message);
				} 
            });
		});
		var uploadCrop = $("#img-thumb").croppie({
		  viewport: {
			width: 250,
			height: 250
		  }
		});
		$("#editThumbnail").on("shown.bs.modal", function(event) {
			var el = event.relatedTarget;
//			var src = $(el).siblings(".fresco").attr("href");
			var src = $(el).data("image");
			var thumbsrc = $(el).parents(".photo").children("img").attr("src");
			$("#ogpath").val(thumbsrc);
		  // alert('Shown pop');
		  uploadCrop
			.croppie("bind", {
			  url: src
			})
		});
		$( "#cropImage" ).submit(function( event ) {
			var extension = $("#ogpath").val().substr( ($("#ogpath").val().lastIndexOf('.') +1) );
			var ext_format;
			switch(extension) {
			  case "png":
				ext_format = "png";
				break;
			  default:
				ext_format = "jpeg";
			}
			uploadCrop.croppie('result', {
                  type: 'base64',
                  format: ext_format
              }).then(function(result) {
				$("#croppiebase64").val(result);
				console.log(result);
			});
		});
		
		$("#editImage").on("show.bs.modal", function(event) {
			var el = event.relatedTarget;
			var id = $(el).data("id");
			$.ajax({
                type: "POST",
                url: "update_image_modal.php",
                data: {"id":id},
				dataType:"html",
                cache: false,
                success: function(result){
                    $("#editImage .modal-body").html(result);
                },
				error: function(xhr, status, error) {
				  var err = eval("(" + xhr.responseText + ")");
				  console.log(err.Message);
				} 
            });
		});
		$("#requestImage").on("show.bs.modal", function(event) {
			var el = event.relatedTarget;
			var id = $(el).data("id");
			console.log(id);
			$("#requestImage .modal-footer").children("input[name=gallery_id]").val(id);
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
		
		$(document).on('click','.downloadImg',function(e){
			var id = $(this).data("id");
			$.ajax({
                type: "POST",
                url: "/includes/count_download.php",
                data: {"id":id},
                cache: false
            });
		});
		
		$(document).on("click",".deleteImg", function(e) {
			e.preventDefault();
			var url = $(this).attr("href");
			var id = $(this).data("id");
			var type = $(this).data("type");
			if(confirm("Are you sure you want to delete this photo?")){
				window.location.href = url+"?id="+id+"&type="+type;
			}
		});
		
		$(function () {
		  $('[data-toggle="tooltip"]').tooltip()
		})
	</script>
	<script src="https://www.adjack.net/validate-files-js/validate-files.js"></script>
  </body>
</html>