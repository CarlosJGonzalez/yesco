<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include_once ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include_once ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");
include_once ($_SERVER['DOCUMENT_ROOT']."/includes/ClassDasPost.php");
	

$action = isset($_POST['type']) ? $_POST['type']:'';
$is_store = isset($_POST['is_store']) ? $_POST['is_store']:'';
$id_post = isset($_POST['id_post']) ? $_POST['id_post']:'';
$where_img = isset($_POST['where_img']) ? $_POST['where_img']:'';
$media_json = json_decode($_POST['media']);
$media = isset($media_json->image) ? $media_json->image : $media_json->video;
$media_url = isset($media_json->image) ? '': $media_json->url;
   
$store_id=$_SESSION['storeid'];	
$dasboost = new Das_Post($db,$_SESSION['client'],$store_id);

if($action == 'delete_img'){
	$id_post = $dasboost->storeUpdCreatePost($id_post);
	$flag = $dasboost->delImage($id_post,$is_store);
	echo '<img src="img/no_image_available.jpg" id="img_post" class="img-fluid" alt="Post Image" />';
	exit;
}

if($action == 'delete_video'){
	$id_post = $dasboost->storeUpdCreatePost($id_post);
	$flag = $dasboost->delVideo($id_post,$is_store);
	echo '<img src="img/no_image_available.jpg" id="img_post" class="img-fluid" alt="Post Image" />';
	exit;
}

$form= '<div class="row">';

if ($action =='change_img' || $action == 'ch_img_video' || $action ==  'ch_video') {
	if ($action == 'ch_img_video' || $action == 'ch_video') {
		$form .= '<div class="col-12 text-center"><p class="alert alert-warning"> Please remember to remove all images before uploading a video.</p></div>';
		$url_path='<div class="col-12 my-2"><input type="url" class="form-control rounded-bottom rounded-right" name="url_video" value ="'.$media_url.'" id="url_video" required placeholder = "https://vimeo.com/"></div>';
	}

	if ($media != '' ) { 
		$list = explode(";",$media);   		   	
		$col=(count($list) == 1 || (count($list) == 2 && $list[0] == '' ))  ? 'col-12' : 'col-12 col-6';

		if($media_url != '' ){    			

			$form.='<div class="'.$col.'"><div class="remove img" data-target="'.$media.'">';
	        $form.= video_info($media_url);   
	        $form.= ' <a href="javascript:;" data-id="'.$media.'" data-storeid="'.$store_id.'" data-postid="'.$id_post.'" data-name="'.$media.'" data-defaultimg="yes" class="deleteImg small text-danger"><i class="fa fa-trash" aria-hidden="true"></i> Remove</a>
	                  <span class="error"></span>
              		</div></div>';	
    	}else{

			$folder = 'img';   		
			if($action == 'ch_video'){
				$folder = 'videos';
			}
	    	
	    	foreach($list as $item){
	    		if ($item == '') {
	    			continue;
	    		}
	    		$img_path = '/uploads/social-media-calendar/'.$folder.'/'.$item;	    		
	    		if($is_store){
	    			$img_path = ($where_img == '0') ? '/uploads/social-media-calendar/'.$folder.'/'.$id_post.'_'.$store_id.'/'.$item : '/uploads/social-media-calendar/'.$folder.'/'.$item;
	    		}

	    		
	    			 $form.='<div class="'.$col.'">
	           			<div class="remove img" data-target="'.$item.'">
		                  <img class = "img-fluid" src="'.$img_path.'" /><br>
		                  <a href="javascript:;" data-id="'.$item.'" data-storeid="'.$store_id.'" data-postid="'.$id_post.'" data-name="'.$item.'" data-defaultimg="yes" class="deleteImg small text-danger"><i class="fa fa-trash" aria-hidden="true"></i> Remove</a>
		                  <span class="error"></span>
	              		</div></div>';	
	    		}    
          
        }

    }
    
    
    $form.=isset($url_path) ? $url_path : '';
}

$form.='<div class="col-12">
		<div class="input-group mb-3">
		  <div class="custom-file">
			<input type="file" class="custom-file-input" id="inputGroupFile01" name="fileToUpload[]" multiple accept="video/mp4,video/mov,image/png, image/jpg, image/jpeg">
			<label class="custom-file-label" for="inputGroupFile01">Choose file</label>
		  </div>
		</div>
		
            <small><strong>Note: </strong> Hold CTRL or shift to select multiple images.</small><br>
            <small><strong>Photo Post: </strong> For best display the file size should be below 1 MB. PNG files larger than 1 MB may appear pixelated after upload. Facebook recommends uploading photos under 4MB.<br>
            <strong>Link Carousel Post: </strong> 1:1 aspect ratio and a minimum of 458 x 458 px for best display.
            </small>
            <input type="hidden" name="is_store" value="'.$is_store.'">
			<input type="hidden" name="orig_media" id ="orig_media" value="'.$media.'">
			<input type="hidden" id="del_media" name="del_media" value="">
        </div></div>';

echo $form;
exit;

function video_info($url){
    $thumbnail = 'http://placekitten.com/1200/628';
    $media = $url;
    
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
?>