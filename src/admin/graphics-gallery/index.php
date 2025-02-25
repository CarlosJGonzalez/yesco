<!doctype html>
<html lang="en">
  <head>
	<link rel="stylesheet" href="/css/fresco.css">
	<link rel="stylesheet" href="/css/checkbox.css">
	<link rel="stylesheet" href="/css/croppie.css" />
	<link rel="stylesheet" href="/css/pajinatify.css" />
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/head.php"); ?>

    <title>Manage Graphics Library | <?php echo CLIENT_NAME; ?></title>
  </head>
  <body class="bg-light cbp-spmenu-push">
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/top.php"); ?>

    <div class="container-fluid">
      <div class="row">
        <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/nav.php"); ?>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 p-0 mb-4">
			<div class="p-0 border-bottom mb-4">
				<div class="border-bottom-dotted d-flex d-block align-items-center clearfix py-2 px-4">
					<h1 class="h2 font-light mb-0 text-center text-sm-left"><i class="far fa-images mr-2"></i> Graphics Library</h1>
					<div class="ml-auto">
						<div class="dropdown d-inline-block">
						  <button type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="border-0 bg-transparent">
							<i class="fas fa-2x text-muted fa-ellipsis-v"></i>
						  </button>
						  <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
							  <a href="#uploadImageModal" title="Add Image" data-toggle="modal" data-target="#uploadImageModal" class="dropdown-item small">Add Image</a>
							  <a href="#uploadVideoModal" title="Add Video" data-toggle="modal" data-target="#uploadVideoModal" class="dropdown-item small">Add Video</a>
							  <?php if(roleHasPermission('general_permission', $_SESSION['role_permissions'])){ ?>
							  <a href="manage.php" title="Manage Orders" class="dropdown-item small">Manage Orders</a>
							  <?php } ?>
						   </div>
						</div>
						
					</div>
				</div>
				<div class="py-3 px-4 d-block d-xl-flex align-items-center">
					<a class="small text-blue d-block d-lg-none" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">Advanced Search</a>
					
					<div class="collapse show w-100" id="collapseExample">
						<div class="d-xl-flex">
							<div class="d-flex d-xl-inline-block mb-2 mb-xl-0 mr-2 align-items-center">
								<span class="letter-spacing-1 text-uppercase small mr-2 mr-xl-0">Category:</span>
								<select name="category_search" class="flex-grow d-xl-inline-block form-control form-control-sm w-auto rounded-pill custom-select-arrow pr-4 column_filter_select" data-column="4">
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
								<select name="col6_filter" class="flex-grow d-xl-inline-block form-control form-control-sm w-auto rounded-pill custom-select-arrow pr-4 column_filter_select" data-column="6">
									<option value="">All Months</option>
								</select>
							</div>
-->
							<div class="search-post d-flex d-xl-inline-block mb-2 mb-xl-0 mr-2 align-items-center">
								<span class="letter-spacing-1 text-uppercase small mr-2 mr-xl-0">Search:</span>
								<div class="flex-grow d-xl-inline-block">
									<div class="input-group input-group-sm">
									  <div class="input-group-prepend">
										<span class="input-group-text border-right-0 bg-white pr-0" id="basic-addon1"><i class="fas fa-search"></i></span>
									  </div>
									  <input type="text" class="form-control border-left-0" placeholder="Search for an image..." aria-label="Search" aria-describedby="basic-addon1" id="searchPost">
									</div>
								</div>
							</div>
							<div class="ml-xl-auto">
								<select name="sort" class="form-control form-control-sm rounded-pill custom-select-arrow pr-4 d-block d-xl-inline-block">
									<option value="">Sort By</option>
									<option value="favorites">Favorites</option>
									<option value="newest">Newest</option>
									<option value="a-z">A-Z</option>
									<option value="z-a">Z-A</option>
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

							<div class="form-group">
								<label class="text-uppercase small">Image Label<span class="text-danger">*</span></label>
								<input type="text" name="name" class="form-control" required>
							</div>
							<div class="form-group">
								<label class="text-uppercase small">Image(s)<span class="text-danger">*</span></label>
								<div class="input-group mb-3">
								  <div class="custom-file">
									<input type="file" class="custom-file-input image-to-validate" id="inputGroupFile01" name="fileToUpload[]" onchange="validateFiles(this.id,'imgMsgContainer','image','uploadImgBtn',20,2000000)" accept="image/jpg, image/png, image/jpeg, image/gif" required multiple >
									<label class="custom-file-label" for="inputGroupFile01">Choose file</label>
								  </div>
								</div>
								<small id="imgMsgContainer">Only image files are accepted.</small>
							</div>
							<div class="form-group">
								<label class="text-uppercase small">Category<span class="text-danger">*</span></label>
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
								<label class="text-uppercase small">Tags</label>
								<input type="text" name="tags" class="form-control">
							</div>


					  </div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
							<input type="submit" id="uploadImgBtn" class="btn bg-blue text-white btn-sm" value="Upload Image">
							<input type="hidden" name="type" value="admin">
						</div>

					</div>
				  </div>
				</div>
			</form>
			<!-- Upload Video Modal -->
			<form action="/admin/graphics-gallery/xt_upload_video.php" method="POST" enctype="multipart/form-data">
				<div class="modal fade" id="uploadVideoModal" tabindex="-1" role="dialog" aria-labelledby="uploadVideoModalTitle" aria-hidden="true">
				  <div class="modal-dialog modal-dialog-centered" role="document">
					<div class="modal-content">
					  <div class="modal-header">
						<h5 class="modal-title" id="uploadVideoModalTitle">Upload</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						  <span aria-hidden="true">&times;</span>
						</button>
					  </div>
					  <div class="modal-body">
							<div class="form-group">
								<label class="text-uppercase small">Video Label<span class="text-danger">*</span></label>
								<input type="text" name="name" class="form-control" required>
							</div>
							<div class="form-group">
								<label class="text-uppercase small">Video File<span class="text-danger">*</span></label>
								<div class="input-group mb-3">
								  <div class="custom-file">
									<input type="file" class="custom-file-input" id="inputGroupFile02" name="fileToUpload" onchange="validateFiles(this.id,'videoImgMsgContainer','video','uploadVideoBtn',1,50000000)" accept="video/mp4, video/mvo" required>
									<label class="custom-file-label" for="inputGroupFile02">Choose file</label>
								  </div>
								</div>
								<small id="videoImgMsgContainer">Only image files are accepted.</small>
							</div>
							<div class="form-group">
								<label class="text-uppercase small">Video Link<span class="text-danger">*</span></label>
								<input type="text" name="video_link" class="form-control" required>
							</div>
							<div class="form-group">
								<label class="text-uppercase small">Category<span class="text-danger">*</span></label>
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
								<label class="text-uppercase small">Tags</label>
								<input type="text" name="tags" class="form-control" value="videos">
							</div>
					  </div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
							<input type="submit" id="uploadVideoBtn" class="btn bg-blue text-white btn-sm" value="Upload Video">
							<input type="hidden" name="type" value="admin">
						</div>

					</div>
				  </div>
				</div>
			</form>
			
			<div class="px-4 py-3">	
				<?php include $_SERVER['DOCUMENT_ROOT']."/includes/alerts.php"; ?>
				
				<!-- Contains gallery images -->
				<div class="gallery">
				
				</div>
				
				<!-- Contains pagination link numbers -->
				<div class='pagination' data-total-count='1' data-take='24'></div>
				
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
		
		$(document).ready(function(e) {
			printPagination();	
			get_images();	
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
		
		function get_images(category = "",month = "",search = "",sort = "",current_page = 1){
			$.ajax({
                type: "POST",
                url: "/includes/get_gallery_images.php",
                data: {"sort":sort,"category":category,"search":search,"month":month,"status":"admin", "current_page":current_page},
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
                data: {"sort":sort,"category":category,"search":search,"month":month,"status":"admin"},
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
		
		var uploadCrop = $("#img-thumb").croppie({
		  viewport: {
			width: 250,
			height: 250
		  }
		});
		
		$("#editThumbnail").on("shown.bs.modal", function(event) {
			var el = event.relatedTarget;
			//var src = $(el).siblings(".fresco").attr("href");
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
		
		$(document).on("click",".deleteImg", function(e) {
			e.preventDefault();
			var url = $(this).attr("href");
			var id = $(this).data("id");
			var type = $(this).data("type");
			if(confirm("Are you sure you want to delete this photo?")){
				window.location.href = url+"?id="+id+"&type="+type;
			}
		});
	</script>
	<script src="https://www.adjack.net/validate-files-js/validate-files.js"></script>
  </body>
</html>