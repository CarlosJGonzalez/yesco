
<!doctype html>
<html lang="en">
  <head>
	  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
	<link rel="stylesheet" href="/css/jquery.datetimepicker.min.css">
	<link href="/css/buzina-pagination.min.css" rel="stylesheet">
	  <link rel="stylesheet" href="/css/checkbox.css">
	  <link rel="stylesheet" href="/css/fresco.css">

    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/head.php"); ?>

    <title>Plan and Publish | <?php echo CLIENT_NAME; ?></title>
	  
  </head>
  <body class="bg-light cbp-spmenu-push">
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/top.php"); ?>

    <div class="container-fluid">
      <div class="row">
        <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/nav.php"); ?>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 p-0 mb-5">
			<div class="p-0 border-bottom">
				<div class="border-bottom-dotted d-sm-flex d-block align-items-center clearfix py-2 px-4">
					<h1 class="h2 font-light mb-0 text-center text-sm-left"><i class="far fa-calendar-alt mr-2"></i> Social Media Calendar</h1>
					<div class="ml-auto">
						<button type="button" class="border-0 bg-transparent" title="Date Management" data-toggle="modal" data-target="#dateManage">
							<i class="fas fa-calendar-alt fa-2x text-muted"></i>
						  </button>
						<div class="dropdown d-inline-block">
						  <button type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="border-0 bg-transparent">
							<i class="fas fa-2x text-muted fa-plus-circle"></i>
						  </button>
						  <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
							  <a href="#" title="Add Post" data-toggle="modal" data-target="#addNewPost" class="dropdown-item small">Add Post</a>
							  <a href="#" title="Add Post" data-toggle="modal" data-target="#importNewPost" class="dropdown-item small">Import Posts</a>
						  </div>
						</div>
					</div>
				</div>
				<div class="py-2 px-4 d-block d-xl-flex align-items-center">
					<div class="d-flex align-items-center">
						<label class="label cusor-pointer d-flex text-center mb-0" for="post_all">
							<input class="label__checkbox" type="checkbox" name="post_all" value="post_all" type="checkbox" id="post_all" />
							<span class="label__text d-flex align-items-center">
							  <span class="label__check d-flex rounded-circle mr-2">
								<i class="fa fa-check icon small"></i>
							  </span>
								<span class="text-uppercase small letter-spacing-1 d-inline-block" id="selectLabel">Select All</span>
							</span>
						</label>
						<?php if( roleHasPermission('show_admin_actions_plan_and_publish', $_SESSION['role_permissions']) ){ ?>
							<button class="btn btn-light btn-sm ml-4" id="deleteallPost" disabled> Delete Post(s) </button>
							<button class="btn btn-light btn-sm ml-4" id="updateallPost" disabled> Edit Posts in Bulk </button>
							<button class="btn btn-light btn-sm ml-4" id="updateallLink" disabled> Edit Links in Bulk </button>
						<?php }?>
						<div class="ml-auto">
							<a class="small text-blue d-block d-lg-none" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">Advanced Search</a>
						</div>
					</div>
					<div class="ml-auto">
						
						<div class="collapse show" id="collapseExample">
							<div class="d-flex d-xl-inline-block mb-2 mb-xl-0 mr-2 align-items-center">
								<span class="letter-spacing-1 text-uppercase small mr-2 mr-xl-0">Filter By:</span>
								<select name="type_view" id="type_view" class="flex-grow d-xl-inline-block form-control form-control-sm w-auto rounded-pill custom-select-arrow pr-4">
									<option value="corp">Corporate</option>
									<option value="store" selected >Store</option>
								</select>
							</div>
							<div class="d-flex d-xl-inline-block mb-2 mb-xl-0 mr-2 align-items-center">
								<span class="letter-spacing-1 text-uppercase small mr-2 mr-xl-0">Sort By:</span>
								<select name="sort" class="flex-grow d-xl-inline-block form-control form-control-sm w-auto rounded-pill custom-select-arrow pr-4">
									<option value="asc">Date Ascending</option>
									<option value="desc">Date Descending</option>
								</select>
							</div>
							<div class="d-flex d-xl-inline-block mb-2 mb-xl-0 mr-2 align-items-center">
								<span class="letter-spacing-1 text-uppercase small mr-2 mr-xl-0">Show:</span>
								<select name="show" class="flex-grow d-xl-inline-block form-control form-control-sm w-auto rounded-pill custom-select-arrow pr-4">
									<option value="1">All Portals</option>
									<option value="Facebook">Facebook</option>
									<option value="Twitter">Twitter</option>
									<option value="Google">Google</option>
									<option value="Google">Google</option>
								</select>
							</div>
							<div class="d-inline-block search-post">
								<span class="letter-spacing-1 text-uppercase small">Search:</span>
								<div class="d-inline-block">
									<div class="input-group input-group-sm">
									  <div class="input-group-prepend">
										<span class="input-group-text border-right-0 bg-white pr-0" id="basic-addon1"><i class="fas fa-search"></i></span>
									  </div>
									  <input type="text" class="form-control border-left-0" placeholder="Search for a post..." aria-label="Search" aria-describedby="basic-addon1" id="searchPost">
									</div>
								</div>
							</div>
						</div>
						
					</div>
					
				</div>

			</div>
		
			<div class="modal fade" id="addNewPost" tabindex="-1" role="dialog" aria-labelledby="addNewPostTitle" aria-hidden="true">
				<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="addNewPostTitle">Add Post</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							  <span aria-hidden="true">&times;</span>
							</button>
				  		</div>
						<div class="modal-body">
							<div class="alert alert-primary text-dark">
									Want to add more than one post at a time? <a href="" class="text-blue" data-toggle="modal" data-target="#importNewPost" data-dismiss="modal">Import in bulk</a>
								</div>
							<div class="form-group">
								<label class="text-uppercase small">Post Date</label>
								<input type="text" name="" class="form-control datetimepicker">
							</div>
							<div class="form-group">
								<label class="text-uppercase small">Link</label>
								<input type="text" name="" class="form-control">
							</div>
							<div class="form-group">
								<label class="text-uppercase small">Post Content</label>
								<textarea name="" class="form-control"></textarea>
							</div>
							<div class="form-group">
								<div class="d-flex align-items-center justify-content-between">
									<div class="w-100">
										<label class="text-uppercase small">Image</label>
										<div class="input-group mb-3">
										  <div class="custom-file">
											<input type="file" class="custom-file-input" id="inputGroupFile01">
											<label class="custom-file-label" for="inputGroupFile01">Choose file</label>
										  </div>
										</div>
									</div>
									<span class="small text-uppercase mx-3 nowrap">&mdash; OR &mdash;</span>
									<div class="w-100">
										<label class="text-uppercase small">Video</label>
										<div class="input-group mb-3">
										  <div class="custom-file">
											<input type="file" class="custom-file-input" id="inputGroupFile02">
											<label class="custom-file-label" for="inputGroupFile02">Choose file</label>
										  </div>
										</div>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label class="text-uppercase small">Portal</label>
								<select name="show" class="form-control custom-select-arrow pr-4">
									<option>All Portals</option>
									<option>Facebook</option>
									<option>Twitter</option>
									<option>Google</option>
								</select>
							</div>							
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
							<button type="button" class="btn bg-blue btn-sm text-white">Add Post</button>
						</div>
					</div>
			  	</div>
			</div>
			
			<div class="modal fade" id="importNewPost" tabindex="-1" role="dialog" aria-labelledby="importNewPostLabel" aria-hidden="true">
				<form enctype="multipart/form-data" id="BulkFilefrm"  action="/admin/plan-and-publish/social-media/xt_bulkFile_qa.php" method="POST">
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
											<input type="file" name="documents" class="form-control emailText rounded-bottom rounded-right custom-file-input" id="contactfile" accept=".csv">
											<label class="custom-file-label" for="contactfile">Choose file</label>
										  </div>
										</div>
									</div>
									<small><i class="fas fa-exclamation-triangle mr-1"></i> Only .csv files can be imported.</small>
								</div>

							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
								<input type="hidden" name="listid" value="<?php echo $list_id; ?>">
								<input type="submit" class="btn bg-blue text-white" value="Import" name="importSubmit">
							</div>
						</div>
					</div>	
				</form>			
			</div>
			
			<div class="modal fade" id="dateManage" tabindex="-1" role="dialog" aria-labelledby="dateManageLabel" aria-hidden="true">
				<div class="modal-dialog modal-dialog-centered" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="dateManageLabel">Date Management</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							  <span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							<div class="form-group">
								<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Client View</label>
								<input type="text" class="form-control daterange" id="client_view_dates" value="">
							</div>
							<div class="form-group">
								<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Admin View</label>
								<input type="text" class="form-control daterange" id="admin_view_dates" value="">
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>							
							<button type="button" class="btn btn-primary bg-blue text-white" id="save_view_dates" >Save</button>
						</div>
					</div>
				</div>
			</div>

			<div class="bg-white py-3 px-4 pb-5">
				<div class="posts"></div>
			</div>        
        </main>
      </div>
    </div>

	<div class="modal fade" id="updatePostBulk" tabindex="-1" role="dialog" aria-labelledby="updatePostBulk" aria-hidden="true">				
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="nameTitle">Edit Posts in Bulk</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					  <span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<input type="text" class="d-none form-control input-lg" id="valueLinkUpdate"  value="" name="linkTextUpdate">
					<textarea class="d-none form-control input-lg" id="valuePostUpdate" rows="3" name="linkTextUpdate"></textarea>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<input type="hidden" name="updatePostList" id="updatePostList_id" value="0">
					<input type="hidden" name="typeUpdatePost" id="typeUpdatePost" value="0">
					<input type="button" id="btnUpdatePost" class="btn bg-blue text-white" value="Update Post" name="Update">
				</div>
			</div>
		</div>	
	</div>	

    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>
	<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
	<script type="text/javascript" src="//cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
	<script src="/js/jquery.datetimepicker.full.min.js"></script>
	<script src="/js/buzina-pagination.min.js"></script>
	  <script type="text/javascript" src="/js/fresco.js"></script>

	<script>
		$( document ).ready(function() {
			get_posts($('#type_view').val(),"asc");
			$('.datetimepicker').datetimepicker({
				minDate:0,
				format:'m/d/Y g:i a',
				formatTime:'g:i a'
			});
			$('.daterange').daterangepicker({
				opens: 'left'
			}, function(start, end, label) {
				//console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
			});

			$('#deleteallPost').on('click',function(){		
				if (window.confirm("Do you really want to edit this?")) {					
				
					$("input[name='posts[]']").each(function() {
						
						if($(this).is(":checked")){

							let info = {};
					    	info['id'] = $(this).val();
					    	info['type'] = $('#type_view').val();
							deletePost( info );

						}
						
						$(this).prop("checked", false);
						$( "input[name=post_all]" ).prop("checked", false);
					    
					});

					disable( true );
				}	
			});

			$('#updateallLink').on('click',function(){
				$('#nameTitle').text('Edit Links in Bulk');
				updatePostBulk('#valueLinkUpdate','link');
			});

			$('#updateallPost').on('click',function(){
				$('#nameTitle').text('Edit Posts in Bulk');			
				updatePostBulk('#valuePostUpdate','postinfo');
			});

			$('#btnUpdatePost').on('click',function(){

				if (window.confirm("Do you really want to edit this?")) {

					var part = $('#typeUpdatePost').val();
					let postinfo = '-1';
					let link = '-1';

					switch(part) {
					  case "postinfo":
					    postinfo = $('#valuePostUpdate').val();
					    break;
					  case "link":
					    link = $('#valueLinkUpdate').val();
					    break;
					  default:
					    // code block
					}
					
				
					$("input[name='posts[]']").each(function() {
						
						if($(this).is(":checked")){

							let info = {};
					    	info['id'] = $(this).val();
					    	info['post'] = postinfo;
					    	info['link'] = link;
					    	info['type'] = $('#type_view').val();

							updatePost( info );

						}
						
						$(this).prop("checked", false);
						$( "input[name=post_all]" ).prop("checked", false);
					    
					});

					resetModalUpdateallPost();
					$('#updatePostBulk').modal('hide');
					disable( true );
				}				
			});
		});


		$( document ).on('blur','.edit_in_place_input',function(e){

			let elem_id = $(this).prop('id');
			let info_this = elem_id .split('_');
			let post_id = info_this[info_this.length - 1];
			let part = info_this[1];
			$('#label_'+part+'_'+post_id).removeClass('d-none');
			$('#'+part+'_'+post_id).removeClass('d-none');

			$(this).addClass('d-none');
			
			/*
			 *
			 * Check URL is same.
			 *
			*/
			var flagComp = $('#label_'+part+'_'+post_id).text().localeCompare($(this).val());

			if( flagComp ){
				if (window.confirm("Do you really want to edit this?")) {					

					let postinfo = '-1';
					let link = '-1';

					switch(part) {
					  case "postinfo":
					    postinfo = $(this).val();
					    $('#label_'+part+'_'+post_id).text(postinfo);
					    break;
					  case "link":
					    link = $(this).val();
					    $('#label_'+part+'_'+post_id).text(link);
					    break;
					  default:
					    // code block
					}

					let info = {};
			    	info['id'] = post_id;
			    	info['post'] = postinfo;
			    	info['link'] = link;
			    	info['type'] = $('#type_view').val();

					updatePost( info );
				}
			}
		});

		$( document ).on('click','.edit_in_place',function(e){
			let info_this = $(this).data('value').split('_');
			let post_id = info_this[info_this.length - 1];
			let part = info_this[0];
			$('#input_'+part+'_'+post_id).removeClass('d-none');
			$('#input_'+part+'_'+post_id).focus();
			$('#label_'+part+'_'+post_id).addClass('d-none');
			$(this).addClass('d-none');
			event.preventDefault();
		});

		$( "#searchPost" ).keyup(function() {
			get_posts($('#type_view').val(),$("select[name=sort]").val(),$("select[name=show]").val(),$('#searchPost').val());
		});

		$( "select[name=sort]" ).change(function() {
			get_posts($('#type_view').val(),$("select[name=sort]").val(),$("select[name=show]").val(),$('#searchPost').val());
		});
		$( "select[name=show]" ).change(function() {
			get_posts($('#type_view').val(),$("select[name=sort]").val(),$("select[name=show]").val(),$('#searchPost').val());
		});

		$( "#type_view" ).change(function() {
			get_posts($('#type_view').val(),$("select[name=sort]").val(),$("select[name=show]").val(),$('#searchPost').val());
		});

		if($( window ).width()<1200){
			$('.collapse').collapse()
		}

	    $('#dateManage').on('show.bs.modal', function(e) {
	        $.ajax({
                type: "POST",
                url: "xt_manage_show_view.php",
                data: {'function':'getInformation'},
                cache: false,
                success: function(info){
                	let info_date = $.parseJSON(info);
                	$('#admin_view_dates').data('daterangepicker').setStartDate(info_date['admin_start_date']);
					$('#admin_view_dates').data('daterangepicker').setEndDate(info_date['admin_end_date']);

					$('#client_view_dates').data('daterangepicker').setStartDate(info_date['client_start_date']);
					$('#client_view_dates').data('daterangepicker').setEndDate(info_date['client_end_date']);
                },
				error: function(xhr, status, error) {
				  var err = eval("(" + xhr.responseText + ")");
				} 
            });
	    });

	    $('#save_view_dates').on('click',function(e){
	    

	    	let info = {};
	    	info['admin_view_dates'] = $('#admin_view_dates').val();
	    	info['client_view_dates'] = $('#client_view_dates').val();

	    	$.ajax({
                type: "POST",
                url: "xt_manage_show_view.php",
                data: {'function':'updateInformation','info':info},
                cache: false,
                success: function(info){
                	$('#dateManage').modal('toggle');
                	console.log(info);
                },
				error: function(xhr, status, error) {
				  var err = eval("(" + xhr.responseText + ")");
				} 
            });

	    });

	    $( "input[name=post_all]" ).change(function() {
			if($(this).prop("checked")){
				$('.post_check').prop("checked", true);
				$("#selectLabel").text("Unselect All");
				
				disable( false );

			}else{
				$('.post_check').prop("checked", false);
				$("#selectLabel").text("Select All");
				
				disable( true );

			}
		});

		$(document).on('change','.post_check',function(){
			if($(".post_check:checked").length>0)
				disable( false );
			else
				disable( true );
		});

		function resetModalUpdateallPost(){
			$('#valueLinkUpdate').removeClass('d-none');
			$('#valuePostUpdate').removeClass('d-none');
			$('#valueLinkUpdate').addClass('d-none');
			$('#valuePostUpdate').addClass('d-none');
			$('#typeUpdatePost').val(0);
		}

		function updatePostBulk(targetInput,type){
			resetModalUpdateallPost();
			$('#typeUpdatePost').val(type);
			$(targetInput).removeClass('d-none');
			$('#updatePostBulk').modal();
		}

		function disable( flag = true){
			$("#deleteallPost").prop('disabled', flag);
			$("#updateallPost").prop('disabled', flag);
			$("#updateallLink").prop('disabled', flag);			
		}

		function deletePost( info ){
			$.ajax({
                type: "POST",
                url: "xt_manage_show_view.php",
                data: {'function':'deletePost','info':info},
                success: function(info){
                	if(info == 1){
                		get_posts($('#type_view').val(),$("select[name=sort]").val(),$("select[name=show]").val(),$('#searchPost').val());
                	}
                },
				error: function(xhr, status, error) {
				  var err = eval("(" + xhr.responseText + ")");
				} 
            });
		}

		function updatePost( info ){
			$.ajax({
                type: "POST",
                url: "xt_manage_show_view.php",
                data: {'function':'updatePostInformation','info':info},
                success: function(info){
                	if(info == 1){
                		get_posts($('#type_view').val(),$("select[name=sort]").val(),$("select[name=show]").val(),$('#searchPost').val());
                	}
                },
				error: function(xhr, status, error) {
				  var err = eval("(" + xhr.responseText + ")");
				} 
            });
		}

		function get_posts(type,sort="",portal="",search=""){
			$.ajax({
                type: "POST",
                url: "get_posts.php",
                data: {'type':type,"sort":sort,"portal":portal,"search":search},
                cache: false,
				beforeSend:function(html){
                    $(".posts").html('<div class="text-center"><img src="/img/loading.svg"></div>');
                },
                success: function(html){
                    $(".posts").html(html);
					$('#posts-pag').buzinaPagination({
						itemsOnPage: 10
					  });
                },
				error: function(xhr, status, error) {
				  var err = eval("(" + xhr.responseText + ")");
				  //console.log(err.Message);
				} 
            });
		}
	  
	</script>
	  
	  

  </body>
</html>