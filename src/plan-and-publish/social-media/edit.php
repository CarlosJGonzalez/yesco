<?php

/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/
include_once ($_SERVER['DOCUMENT_ROOT']."/includes/head.php"); 
include_once ($_SERVER['DOCUMENT_ROOT']."/includes/ClassDasPost.php");
 
if(!isset($_SESSION["user_id"])){
    $_SESSION['error']="You must be logged in to view this page.";
    header('location: /');
    exit;
}

$store_id=$_SESSION['storeid'];
	
$dasboost = new Das_Post($db,$_SESSION['client'],$store_id);

$row = $dasboost->getPost($_GET['id']);
$is_store_post = $dasboost->__get('is_store');
$where_img = '1';

if($is_store_post){
  if ($row['img'] != '') {
    $where_img = '0';
  }
}

$type_post = $dasboost->getPostType($row);//integer 0 is Image | 1 is Carrusel | 2 is Video
$media_post = $dasboost->getMediaPost($row,$type_post);

$amounts= $dasboost->getAvailableOptions('2019-02-15','2019-03-14');

if(isset($row['boost_start']) && $row['boost_start'] !="0000-00-00"){
	 	$boost_start=date("m/d/Y",strtotime($row['boost_start']));
}else{
		$boost_start=date("m/d/Y",strtotime($row['date']));
}

$datetime_post = date('m/d/Y g:i a', strtotime($row['date']));

if(isset($row['boost_end']) && $row['boost_end'] != "0000-00-00"){
    $boost_end=date("m/d/Y",strtotime($row['boost_end']));
}else{
    $boost_end='';
}
$store = $dasboost->getLocation();

if(!$store){
    $_SESSION['error']="You must be logged in to view this page.";
    header('location: /plan-and-publish/social-media/');
    exit;
}

//$link = str_replace("[[site_url]]",$store['url'], $row['link']);
$link = $dasboost->getPostLink($row['link']);
//$post = str_replace("[[site_name]]",$store['seo_city'], $row['post']);
$post = $dasboost->replaceVariable($row['post']);
$portal = $row['portal'];

?>
<!doctype html>
<html lang="en">
  <head>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="/css/jquery.datetimepicker.min.css">
    <link rel="stylesheet" href="/css/fresco.css">
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>
    <style type="text/css">
        .remove.img {
            border: 1px solid #eee;
            margin: 5px;
            padding: 0px;
            text-align: center;
            display: block;
        }

        .p-bottom-right{
            right: 22px;
            bottom: 0;
        }
        .slick-slide{
            height:85% !important;
        }
        .none_upload{ display:none;text-align:center;}
        .loader {
              position: fixed;
              left: 0px;
              top: 0px;
              width: 100%;
              height: 100%;
              z-index: 9999;
              background: url('/../../yextAPI/spinner_preloader.gif') 50% 50% no-repeat rgba(255, 255, 255, 0.3);
            }  
	  </style>
    <title>Edit Post | Yes We're Open</title>
	  
  </head>
  <body class="bg-light cbp-spmenu-push">
    <? include ($_SERVER['DOCUMENT_ROOT']."/includes/top.php"); ?>

    <div class="container-fluid">
      <div class="row">
        <? include ($_SERVER['DOCUMENT_ROOT']."/includes/nav.php"); ?>
        <div id="spinner_loading" class="none_upload loader"></div>
        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 pt-3 px-4 mb-4">

        <?php include $_SERVER['DOCUMENT_ROOT']."/includes/alerts.php"; ?>


        	<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
            	<h1 class="h2">Edit Post</h1>
        	</div>
			<div class="border flex-grow rounded box-shadow">
				<div class="post bg-white">
					<?php 
						if(isset($row['notes']) && $row['notes']!=''){?>
							<div class="notes py-1 px-2 rounded-top"><i class="fas fa-edit mr-1"></i> <?=$row['notes']?></div>
						<?php } ?>
					
					<div class="p-2 position-relative">
						<div class="row">
							<div class="col-sm-4">
								<span class="font-16 font-bold text-uppercase mb-1 d-block"><?=$portal?></span>
								<?php 
								echo getHtmlMedia($type_post,$media_post,$is_store_post,$where_img,$row['id'],$store_id);
                ?>
								<div class="row mt-2">
		
									<?php
										if (in_array($type_post, [0,1])) {?>
											<div class="text-center col-sm-6 col-md-3" id="delete_img">
												<i class="far fa-trash-alt fa-lg"></i>												
												<span class="d-block text-uppercase small cursor-pointer">Delete Image(s)</span>
											</div>
											<div class="text-center col-sm-6 col-md-3" id="change_img">
												<i class="far fa-images fa-lg"></i>
												<span class="d-block text-uppercase small cursor-pointer">Update Media</span>
											</div>
											<!--<div class="text-center col-sm-6 col-md-3" id="change_img">
												<i class="far fa-edit fa-lg"></i>
												<span class="d-block text-uppercase small">Change Image</span>
											</div>-->											
											<div class="text-center col-sm-6 col-md-3" id="ch_img_video">
												<i class="fas fa-video fa-lg"></i>
												<span class="d-block text-uppercase small cursor-pointer">Upload Video</span>
											</div>
										<?php }else{ ?>
											<div class="text-center col-sm-6 col-md-3" id="delete_video">
												<i class="far fa-trash-alt fa-lg"></i>
												<span class="d-block text-uppercase small cursor-pointer">Delete Video</span>
											</div>
											<div class="text-center col-sm-6 col-md-3" id="ch_video">
												<i class="far fa-edit fa-lg"></i>
												<span class="d-block text-uppercase small cursor-pointer">Change Video</span>
											</div>
											<!--<div class="text-center col-sm-6 col-md-3" id="video_image">
												<i class="far fa-edit fa-lg"></i>
												<span class="d-block text-uppercase small">Video to Image</span>
											</div>-->
										<?php } ?>
									
								</div>
							</div>
							<div class="col-sm-8">
                	<form action="xt_update_post.php" autocomplete="off" method="POST">
                				<input type="hidden" name="postid" value="<?=$row['id']?>">
								<input type="hidden" name="is_store" id='post_is_store' value="<?=($is_store_post)?1:0?>">
                
                <div class="field form-group">
                 <label class="text-uppercase small">Post Date </label>
                 <input type="text" name="postdate" required class="form-control datetimepicker" value="<?php echo $datetime_post;?>">
                </div>
								<div class="field form-group">
									<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Link </label>
									<input type="text" class="form-control rounded-bottom rounded-right" name="link" value="<?=$link?>">
								</div>
								<div class="field form-group">
									<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Content </label>
									<textarea class="form-control rounded-bottom rounded-right" name="post"><?=$post?></textarea>
								</div>
                <?php if(false ){ ?>
								<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Boost Post </label>
								<div class="field form-inline">
									
						
									<div class="form mx-sm-3 mb-2">
										<label for="exampleFo">Amount</label>
									    <select class="form-control rounded-bottom rounded-right bg-white border valid" name="boost_amount" id="boost_amount">
									      <option value="0">None</option>
				                    <?php foreach($amounts as $amount){ ?>
				                    <option value="<?=$amount?>" <?php if(isset($row['boost_amount']) && $row['boost_amount']==$amount) echo "selected";?>>$<?=$amount?></option>
				                    <?php } ?>
									    </select>
									</div>
									  <div class="form mx-sm-3 mb-2 ">
									    <label for="boost_start" >Start Date</label>
									    <input type="text" id="boost_start_<?=$row['id']?>" class="form-control rounded-bottom rounded-right datepicker" disabled name="boost_start" id="boost_start" value="<?=$boost_start?>"/>
									    <input type="hidden" name="boost_start" value="<?=$boost_start?>">
									  </div>
									  <div class="form mx-sm-3 mb-2">
									    <label for="boost_end" > End Date</label>
									    <input type="text" id="boost_end_<?=$row['id']?>" class="form-control rounded-bottom rounded-right datepicker" name="boost_end" for="boost_end" disable value="<?=$boost_end?>"/>
									    <input type="hidden" id="boost_end" name="boost_end" value="<?=$boost_end?>">
									  </div>
                   
								</div>
              <?php } ?>
								<br/>
								<div class="dropup position-absolute p-bottom-right cursor-pointer">
								<button type="button"  id="close_edit_post" class="btn-secondary rounded-bottom-right rounded-bottom-right cursor-pointer text-white py-1 px-3 border-0" data-dismiss="modal">Cancel</button>
								<input type="submit" value="Save" class="rounded-bottom-right rounded-bottom-right cursor-pointer bg-primary-red text-white py-1 px-3 border-0">
							</div>
							 </form>
							</div>

						</div>
					</div>
				</div>
			</div>
        </main>
      </div>
    </div>

    <form action="xt_update_media.php" method="POST" enctype="multipart/form-data" id="updatemediapost">	
		<div class="modal fade" tabindex="-1" role="dialog" id="updatemediapost_modal" aria-labelledby="exampleModalLabel" aria-hidden="true">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		        <div class="modal-header">
			        <h5 class="modal-title" >Social Media Post</h5>
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			          <span aria-hidden="true">&times;</span>
			        </button>
			    </div>

              <div class="modal-body"></div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                <input type="hidden" name="postid" value="<?php echo $row['id']?>">
                <input type="submit" class="btn bg-blue btn-sm text-white" value="Save changes">
              </div>
            </div><!-- /.modal-content -->
          </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    </form>

    <? include ($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="/js/jquery.datetimepicker.full.min.js"></script>
    <script type="text/javascript" src="/js/fresco.js"></script>
    <script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <script type="text/javascript">
      $('#test').slick();
		function changePropertyBoostEnd(id_elemnt,value){
			if(value > 0){
              if(value == 5){
                   $(id_elemnt).prop('required',false); 
                   $(id_elemnt).css('border-color','#ccc');
                   $(id_elemnt).prop('disabled',true);
              }else{
                  $(id_elemnt).prop('required',true);
                  $(id_elemnt).prop('disabled',false);
                  $(id_elemnt).css('border-color','red');
              }                 
            }else{
              $(id_elemnt).prop('required',false); 
              $(id_elemnt).css('border-color','#ccc');
              $(id_elemnt).prop('disabled',true);
            }
		}

		function getConfirmation(msg) {
			let flag = false;
			var rtn = confirm(msg);
			
			if( rtn == true ) {
				flag= true;
			}
			return flag;
		}

		$(function(){
			$('.datetimepicker').datetimepicker({
			  minDate:"+1970/01/02",
			  format:'m/d/Y g:i a',
			  formatTime:'g:i a'
			});

			let amount_value = $('#boost_amount').val();
			let boost_end_id= '#boost_end_'+<?=$row['id']?>;
			changePropertyBoostEnd(boost_end_id,amount_value);
		});

    $('#close_edit_post').on('click',function(e){
      window.location.href = "/plan-and-publish/social-media/";
    });

		$(document.body).on('focus', '#boost_start_<?=$row['id']?>' ,function(){
            $( this ).datepicker({ minDate:new Date(<?=date('Y', strtotime($row['date']))?>, <?=date('m', strtotime($row['date']))?> - 1, <?=date('d', strtotime($row['date']))?>), dateFormat: 'mm/dd/yy' });
        });
        
        $(document.body).on('focus', '#boost_end_<?=$row['id']?>' ,function(){
            $( this ).datepicker({ minDate:new Date(<?=date('Y', strtotime($row['date']))?>, <?=date('m', strtotime($row['date']))?> - 1, <?=date('d', strtotime($row['date']))?>), dateFormat: 'mm/dd/yy',
                onSelect: function(dateText, inst) {
                  $('#boost_end').val(dateText);
                }
        	});
        });

        $(document.body).on('click', '#ch_img_video,#change_img,#ch_video' ,function(e){
        	let type = $(this).attr('id');
        	let media = JSON.stringify(<?=json_encode($media_post)?>);
        	let postid = <?=$row['id']?>;
            let where_img = <?=$where_img?>;
         	let is_store =$('#post_is_store').val();
            let ch_img_video = (type == 'ch_img_video') ? 1 : 0 ;
            let url_video = $('#url_video'); 

            $.ajax({
                    type: "POST",
                    url: 'xt_media_info.php',
                    data: {
                           'media':media,
                           'type':type,
                           'is_store':is_store,
                           'id_post':postid,
                           'where_img':where_img,
                           'ch_img_video':ch_img_video
                         },
                  beforeSend: function(){
                    $('#spinner_loading').removeClass("none_upload");
                  },
                  success: function(html){
                        $("#updatemediapost_modal .modal-body").html(html);
                        $('#updatemediapost_modal').modal('show');
                        $('#spinner_loading').addClass("none_upload");
                  },
                  error: function(xhr, status, error) {
                            var err = eval("(" + xhr.responseText + ")");
                            $('#spinner_loading').addClass("none_upload");
                          } 
            });    
           
        	e.stopPropagation();
        });

        $( "#updatemediapost" ).submit(function( event ) {
            $('#spinner_loading').removeClass("none_upload");
            $('#updatemediapost_modal').modal('hide');
            $(this).submit();

            event.preventDefault();
        });

        $(document.body).on('click','#delete_video,#delete_img',function(e){
        		let type = $(this).attr('id');
        		let media = JSON.stringify(<?=json_encode($media_post)?>);
        		let is_store =$('#post_is_store').val();
        		let postid = <?=$row['id']?>;
        		let flag=getConfirmation('Are you sure you want to delete this image or video?”');

        		if (flag) {
        			$.ajax({
        				type: "POST",
		                url: 'xt_media_info.php',
		                data: {'media':media,'type':type,'is_store':is_store,'id_post':postid},
                    beforeSend: function(){
                        $('#spinner_loading').removeClass("none_upload");
                    },
		                success: function(html){
                      location.reload();
		                	$('#img_post').replaceWith(html);                      
                      if(type == 'delete_video'){
                         // $('#img_post_url').prop('href','');
                          location.reload();
                      }
		                },
		                error: function(xhr, status, error) {
              						    var err = eval("(" + xhr.responseText + ")");
              						    $('#spinner_loading').addClass("none_upload");
              						} 
        			});

        		}
        	e.stopPropagation();
        });
	
        $('#boost_amount').on('change',function(e){
        	let amount_value = $('#boost_amount').val();
			let boost_end_id= '#boost_end_'+<?=$row['id']?>;
			changePropertyBoostEnd(boost_end_id,amount_value);

            if(amount_value > 0){
                if(amount_value == 5){
                    let tomorrow = new Date($('#boost_start_'+<?=$row['id']?>).val());
                    tomorrow.setDate(tomorrow.getDate() + 2);
                    let value= (tomorrow.getMonth() + 1) + '/' + tomorrow.getDate() + '/' +  tomorrow.getFullYear();

                     $(boost_end_id).val(value);
                     $('#boost_end').val(value);
                }                
            }else{
            	 $(boost_end_id).val('');
                 $('#boost_end').val('');	
            }
        });

       	$(document.body).on('click', '.deleteImg' ,function(e){
			e.preventDefault();
			if( getConfirmation("Are you sure you want to delete this photo?")){
				let target=$(this).data( "id" );
				let del_media=$('#del_media').val();
				console.log(target);
				console.log(del_media);

				if(del_media == ''){
					$('#del_media').val(target);
				}else{
					$('#del_media').val(del_media+';'+target);
				}

				$("div").find("[data-target='" + target + "']").html("<em>Removed</em>").fadeOut("slow");
			}             
        });
    </script>
  </body>
</html>
<?php 

function getHtmlMedia($type,$media,$is_store,$where_img,$id_post,$store_id){

    if($type == 1){
        $media = ltrim($media['image'],';');
        if($media !='' ){
          $gallery = explode(";",$media);
          $count = count($gallery);

          $img_path = '/uploads/social-media-calendar/img/';         
          if($is_store){
              $img_path = ($where_img == '0') ? '/uploads/social-media-calendar/img/'.$id_post.'_'.$store_id.'/' : '/uploads/social-media-calendar/img/';
          }

          $carrusel = '<div id="test"  class="carousel slide" data-ride="carousel" >';

          for($i = 0; $i < $count; $i ++){
            $tmp = ($i == 0 ) ? "active":"";

              $carrusel .='<div class="position-relative">                       
                   <a href="'.getFullUrl().$img_path.$gallery[$i].'"  class="fresco" data-fresco-group="'.$id_post.'">
                   <img src="'.getFullUrl().$img_path.$gallery[$i].'" class="d-block img-fluid"></a>
                         </div>';
          }

          $carrusel .='</div>';
      }else{
          $img_path ='/plan-and-publish/social-media/img/no_image_available.jpg';
      } 

      return $carrusel;
        

    }

    if($type == 0){
      if($media['image'] !='' ){
          $media = ltrim($media['image'],';');
          $img_path = '/uploads/social-media-calendar/img/'.$media;         
          if($is_store){
              $img_path = ($where_img == '0') ? '/uploads/social-media-calendar/img/'.$id_post.'_'.$store_id.'/'.$media : '/uploads/social-media-calendar/img/'.$media;
          }
      }else{
          $img_path ='/plan-and-publish/social-media/img/no_image_available.jpg';
      }       
   
    return '<a href="'.getFullUrl().$img_path.'" class="fresco"><img src="'.getFullUrl().$img_path.'" class="img-fluid" /></a>';
    }

    if($type == 2){
        $thumbnail = 'http://placekitten.com/1200/628';
        $media = $media['url'];
        
       if (strpos($media, "vimeo") !== false){
            $thumbnail= grab_vimeo_thumbnail(urlencode($media));
        }

        if (strpos($media, "youtube") !== false){
            $vid = explode("=",$media);
            $thumbnail= "https://img.youtube.com/vi/$vid/mqdefault.jpg";
        }

        
        return '<div class="position-relative">
                <i class="fas fa-play-circle fa-4x position-absolute text-white center-both z-top"></i>
                <a id="img_post_url" target="_blank" href="'.$media.'" class="fresco position-relative d-block">
                  <div class="position-absolute bg-overlay w-100 h-100"></div>
                  <img src="'.$thumbnail.'" class="img-fluid" id="img_post"/>
                </a>
              </div>';
    }      

}

?>