<!doctype html>
<html lang="en">
  <head>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.21/b-1.6.3/b-html5-1.6.3/datatables.min.css"/>
    <?php 
	include ($_SERVER['DOCUMENT_ROOT']."/includes/head.php");
	/*if( (!(roleHasPermission('show_ongoing_campaigns', $_SESSION['role_permissions']))) && (isset($_SESSION['email']))){
		header('location: /dashboard.php');
		exit;
	}*/
	?>

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
		
		.btn-primary{
			background-color: #003d4c;
    		border-color: #003d4c;
		}
		.btn-primary:hover{
			background-color: #012a34;
    		border-color: #012a34;
		}
		.btn-primary:not(:disabled):not(.disabled).active, .btn-primary:not(:disabled):not(.disabled):active, .show>.btn-primary.dropdown-toggle{
			background-color: #012a34;
    		border-color: #012a34;
		}
	</style>
    <title>Post Exceptions | Local <?php echo CLIENT_NAME; ?></title>
  </head>
  <body class="bg-light cbp-spmenu-push">
    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/top.php"); ?>

    <div class="container-fluid">
    	<div class="row">
    		<?php 
    			include ($_SERVER['DOCUMENT_ROOT']."/includes/nav.php"); 
    		 	require_once ($_SERVER['DOCUMENT_ROOT']."/includes/ClassDasClient.php");
    		?>



    		<main role="main" class="col-md-9 ml-sm-auto col-lg-10 p-0">

    			<div class="modal fade" id="addNewException" tabindex="-1" role="dialog" aria-labelledby="addNewExceptionTitle" aria-hidden="true">
    				<form id="frmAddNewException"  action="/admin/plan-and-publish/social-media/xt_addNewException.php" method="POST">
					<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title" id="addNewExceptionTitle">Opt Out of Posts</h5>
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								  <span aria-hidden="true">&times;</span>
								</button>
					  		</div>
							<div class="modal-body">
								
								<div class="form-group">
									<label class="text-uppercase small">Location</label>
									<select name="storeid" class="form-control custom-select-arrow pr-4" required>
										<option value="">-- Select a Location --</option>
										<?php
											$locations = $db->where('adfundmember','Y')->where('suspend',0)->get('locationlist',null,'storeid'); 
											foreach ($locations as $location) { 
												$dasClient = new Das_Client($db,$token_api,$_SESSION['client'],$location['storeid']);
			                                    $clientInfo = $dasClient->getClient();
			                                ?>

												<option value="<?php echo $location['storeid']?>">
													<?php echo isset( $clientInfo['data'][0]['name'] ) ? $clientInfo['data'][0]['name'] : $location['storeid'];?>
												</option>
										<?php } ?>


									</select>
								</div>							
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
								<input type="submit" class="btn bg-blue btn-sm text-white" value="Save" name="inpAddException">
							</div>
						</div>
				  	</div>
				</div>
    			<div class="p-0 border-bottom mb-4">
    				<div class="d-flex d-block align-items-center clearfix py-2 px-4">
    					<h1 class="h2 font-light mb-0 text-center text-sm-left"><i class="fas fa-clipboard-list mr-2"></i>Post Exceptions</h1>  
    					<div class="ml-auto">
						<div class="dropdown d-inline-block">
						  <button type="button" title="Add Exception" data-toggle="modal" data-target="#addNewException" class="border-0 bg-transparent">
							<i class="fas fa-2x text-muted fa-plus-circle"></i>
						  </button>
						</div>
					</div> 					
    				</div>
    			</div>

    			<div class="py-3 px-4">
    				<?php include $_SERVER['DOCUMENT_ROOT']."/includes/alerts.php"; ?>
                 <div class="table-responsive">
                    <table class="table" id="campaignIdDataTable">
                        <thead class="thead-dark">
                            <tr>
                                <th>Location Name</th>
                                <th>Portal</th>
                                <th># of Skipped Posts</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                                               
                                $locations = getExclusionAllPost($db,$_SESSION['client']);                                 
                                
                                foreach($locations as $location){   
                                    $dasClient = new Das_Client($db,$token_api,$_SESSION['client'],$location['storeid']);
                                    $clientInfo = $dasClient->getClient();
                                    $qtt = getCountOptOutPost($db,$location['storeid']);
                                   
                                    ?>
                                    <tr>
                                        <td><?php echo isset( $clientInfo['data'][0]['name'] ) ? $clientInfo['data'][0]['name'] : $location['storeid'];?></td>
                                        <td><?php echo ($location['portal'] == 'A') ? 'All' : $location['portal'];?></td>
                                        <td><?php echo isset($qtt['qtt']) ? $qtt['qtt'] : 0;?></td>
                                    </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

            </div>   
        </main>   
    </div>
</div>



    <?php include ($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>
	<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
	<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.21/b-1.6.3/b-html5-1.6.3/datatables.min.js"></script>
	<script src="//cdn.datatables.net/plug-ins/1.10.20/sorting/datetime-moment.js"></script>
	
	<script>
		
		$(document).ready( function () {

			$('#campaignIdDataTable').DataTable({
				"order": [[ 0, "desc" ]],
				responsive: true,
				dom: '<"row"<"col-sm-6"l><"col-sm-6 text-right"f>>rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
			});
		} );	

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
  </body>
</html>

<?php 

function getCountOptOutPost(&$db,$storeid){
	$table = $_SESSION['database'].'.social_media_local_posts_optout';
	return $db->where('storeid',$storeid)->getOne($table,'Count("id") as qtt');
}

function getExclusionAllPost(&$db,$client,$storeid = null){
    $db->where('client',$client)->where('active',1);

    if( isset($storeid) ){
        $db->where('storeid',$storeid);
    }

    return $db->get('das_contract.exclusion_post');
}

?>