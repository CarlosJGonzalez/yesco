<!doctype html>
<html lang="en">
  <head>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<link rel="stylesheet" href="/css/fresco.css">
	<link href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.css" rel="stylesheet" type="text/css" />
	<link href="//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
	<link href="//cdn.datatables.net/buttons/1.5.6/css/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/head.php"); ?>
   	<style>
	.dt-buttons{
		margin-bottom:.5rem;
		margin-top:.5rem;
	}
	.dt-buttons > button{
		border-radius: 50rem !important;
		font-size: .875rem;
		line-height: 1.5;
		background-color:#0067b1;
		padding: .25rem 1rem;
		margin-right: .5rem !important;
		border:none;
	}
	</style>

    <title>Training Center | Local <?php echo CLIENT_NAME; ?></title>
  </head>
  <body class="bg-light cbp-spmenu-push">
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/top.php"); ?>

    <div class="container-fluid">
      <div class="row">
        <?php 
		include ($_SERVER['DOCUMENT_ROOT']."/includes/nav.php");
		?>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 p-0">
			
			<div class="p-0 border-bottom mb-4">
				<div class="border-bottom-dotted d-flex d-block align-items-center clearfix py-2 px-4">
					<h1 class="h2 font-light mb-0 text-center text-sm-left"><i class="fas fa-upload mr-2"></i> Training Center</h1>
					<div class="ml-auto">
						<div class="dropdown d-inline-block">
						  <button type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="border-0 bg-transparent">
							<i class="fas fa-2x text-muted fa-ellipsis-v"></i>
						  </button>
						  <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
							  <a href="#addFile" title="Add File" data-toggle="modal" data-target="#addFile" class="dropdown-item small">Add File</a>
							</div>
						</div>
					</div>
				</div>
				<div class="py-3 px-4 d-block d-xl-flex align-items-center">
					<a class="small text-blue d-block d-lg-none" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">Advanced Search</a>
					
					<div class="collapse show" id="collapseExample">
						<div class="d-flex d-xl-inline-block mb-2 mb-xl-0 mr-2 align-items-center">
							<select name="col3_filter" class="column_filter_select flex-grow d-xl-inline-block form-control form-control-sm w-auto rounded-pill" data-column="3">
								<option value="">All Categories</option>
								<?php 
								//$trainingCategories = array("essensys Operate Training Videos", "Monthly Franchisee Webinar","Monthly Training Webinar");
								foreach ($trainingCategories as $cat){ ?>
									<option value="<?php echo $cat?>" ><?php echo $cat?></option>
								<?php } ?>
							</select>
						</div>
					</div>
				</div>
			</div>
			
			<div class="py-3 px-4">
				<?php include $_SERVER['DOCUMENT_ROOT']."/includes/alerts.php"; ?>
				
				<div class="table-responsive">
					<table class="table" id="calls_leadTable">
						<thead class="thead-dark">
							<tr>
								<th>Date Added</th>
								<th>Name</th>
								<th>Description</th>
								<th>Category</th>
								<th class="nowrap"></th>
							</tr>
						</thead>
						<tbody>
							<?php
							$db->where("active",1);
							$items = $db->get("training");
							foreach($items as $item){
							?>
							<tr>
								<td><?php echo !empty($item['date_added']) ? date("m/d/Y h:i a", strtotime($item['date_added'])) : ""; ?></td>
								<td><?php echo $item['name']; ?></td>
								<td><?php echo $item['description']; ?></td>
								<td><?php echo $item['category']; ?></td>
								<td class="text-right nowrap">
									<?php if(!empty($item['download_link2'])){ ?>
									<a href="<?php echo $item['download_link2']; ?>" title="Download" class="btn btn-sm text-white bg-blue text-uppercase downloadImg" download>Download</a> 
									<?php } ?> 
									<?php if(!empty($item['show_link'])){ ?>
									<a href="<?php echo $item['show_link']; ?>" data-fresco-caption="<?php echo $item['name'] ?>" title="Expand" class="fresco text-white ml-2 btn btn-sm text-white bg-blue text-uppercase fresco">View</a>
									<?php } ?> 
									<?php if($_SESSION['admin']){ ?>
									<a href="#editFile" class="btn btn-sm text-white bg-blue text-uppercase" data-toggle="modal" data-target="#editFile" data-id="<?php echo $item['id']; ?>">Edit</a>
									<?php } ?>
								</td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
			</div>
			
			<!-- Add Training Video form-->
			<form action="xt_addFile.php" method="POST" enctype="multipart/form-data" name="addFile">
				<div class="modal fade" id="addFile" tabindex="-1" role="dialog" aria-labelledby="uploadModalTitle" aria-hidden="true">
				  <div class="modal-dialog modal-dialog-centered" role="document">
					<div class="modal-content">
					  <div class="modal-header">
						<h5 class="modal-title" id="uploadModalTitle">Add File</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						  <span aria-hidden="true">&times;</span>
						</button>
					  </div>
					  <div class="modal-body">
							<div class="form-group">
								<label class="text-uppercase small">Video or PDF File<span class="text-danger">*</span></label>
								<div class="input-group mb-3">
								  <div class="custom-file">
									<input type="file" class="custom-file-input" id="inputGroupFile02" name="fileToUpload" accept="video/mp4, video/mvo, application/pdf">
									<label class="custom-file-label" for="inputGroupFile02">Choose file</label>
								  </div>
								</div>
								<small id="videoImgMsgContainer"><strong>Note:</strong> Only videos or PDFs files are accepted.</small>
							</div>
							<div class="form-group">
								<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Name<span class="text-danger">*</span></label>
								<input type="text" name="name" placeholder="Name" class="form-control" required />
							</div>
							<div class="form-group">
								<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Video Link</label>
								<input type="text" name="show_link" placeholder="Video Link" class="form-control" />
								<small id="imgMsgContainer"><strong>Note:</strong> Vimeo or Youtube (if applicable)</small>
							</div>
							<div class="form-group">
								<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Description</label>
								<textarea name="description" placeholder="Description" class="form-control"></textarea>
							</div>
							<div class="form-group">
								<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Category<span class="text-danger">*</span></label>
								<select name="category" class="form-control rounded-bottom rounded-right custom-select-arrow" required>
									<?php
									foreach ($trainingCategories as $cat) {?>
										<option value="<?php echo $cat?>"><?php echo $cat?></option>
									<?php } ?>
								</select>
							</div>
					  </div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
							<input type="submit" class="btn bg-blue text-white btn-sm" value="Add File" name="submit" id="uploadVideoBtn">
						</div>

					</div>
				  </div>
				</div>
			</form>
			<!-- End Add Traiining Video modal form-->
			
			<!-- Change Training Video form-->
			<form action="xt_save.php" method="POST" name="editFile">
				<div class="modal fade" id="editFile" tabindex="-1" role="dialog" aria-labelledby="uploadModalTitle" aria-hidden="true">
				  <div class="modal-dialog modal-dialog-centered" role="document">
					<div class="modal-content">
					  <div class="modal-header">
						<h5 class="modal-title" id="uploadModalTitle">Edit File</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						  <span aria-hidden="true">&times;</span>
						</button>
					  </div>
					  <div class="modal-body">
					  
					  </div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
							<input type="submit" name="submit" class="btn btn-danger text-white btn-sm" value="Delete"  /> 
							<input type="submit" class="btn bg-blue text-white btn-sm" value="Save Changes" name="submit">
						</div>

					</div>
				  </div>
				</div>
			</form>
			<!-- End Change Traiining Video modal form-->
        </main>
      </div>
    </div>

    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>
	<script type="text/javascript" src="/js/fresco.js"></script>
	<script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
	<script src="//cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script src="//cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
	<script src="//cdn.datatables.net/buttons/1.5.6/js/buttons.bootstrap4.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
	<script src="//cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
	<script src="//cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>
	<script src="//cdn.datatables.net/buttons/1.5.6/js/buttons.colVis.min.js "></script>
	<script src="//cdn.datatables.net/select/1.2.2/js/dataTables.select.min.js"></script>

	<script>
		$(document).ready( function () {
			//$.fn.dataTable.moment('dddd, MMMM D, YYYY');
			$('table').DataTable({
				"order": [[ 0, "desc" ]],
				"columnDefs": [ {
				"targets": -1,
				"orderable": false
				} ]
			});
		} );
		
		$('select.column_filter_select').on( 'change', function () {
			filterColumnSelect( $(this).attr('data-column') );
		} );
		function filterColumnSelect ( i ) {
			$('table').DataTable().column( i ).search(
				$('select[name="col'+i+'_filter"] option:selected').val()
			).draw();
		}
		$('#editFile').on('show.bs.modal', function (e) {
			var button = $(e.relatedTarget);
            var id = $(button).data('id');
			//console.log(id);
            $.ajax({
                type: "POST",
                url: "get_details.php",
                data: {"id":id},
                cache: false,
                success: function(html){
                    $("#editFile .modal-body").html(html);
                },
				error: function(xhr, status, error) {
				  var err = eval("(" + xhr.responseText + ")");
				  console.log(err.Message);
				} 
            });
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
	</script>
	<script src="https://www.adjack.net/validate-files-js/validate-files.js"></script>
  </body>
</html>