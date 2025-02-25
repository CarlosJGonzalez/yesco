<?php
session_start();
date_default_timezone_set('America/New_York');
include_once ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include_once ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");
include_once ($_SERVER['DOCUMENT_ROOT']."/includes/ClassDasPost.php");

//$post_id = filter_var(filter_var($_GET['post_id'], FILTER_VALIDATE_INT)) ? $_GET['post_id'] : false;
$post_id = filter_var(filter_var($_POST['post_id'], FILTER_VALIDATE_INT)) ? $_POST['post_id'] : false;
if (!$post_id) {
	echo '<div class="notice notice-danger box-shadow">There are no posts to display yet!</div>';
}
$posts = $db->rawQuery("(select a.date,a.id as id,post,link,image,optout,a.date as curdate, portal, '' as img,a.video,notes,boost from social_media__local_posts a left join social_media_local_posts_optout b on a.id=b.id and b.storeid=? where a.id = ? and a.id not in (select id from social_media_local_posts_store where storeid=? and a.id = ? )) UNION  (select date,id,post,link,image,null,date as curdate, portal, img,video,'' as notes,'0' as boost  from social_media_local_posts_store where storeid=? and id = ?)",Array($_SESSION['storeid'], $post_id, $_SESSION['storeid'], $post_id, $_SESSION['storeid'], $post_id));	

if($db->count>0){
	$dasboost = new Das_Post($db,$_SESSION['client'],$_SESSION['storeid']);

	foreach($posts as $post){
		
		$post_date = $post['date'];
		$is_store = $dasboost->isStore($post['id']);
		$postType = $dasboost->getPostType($post);
		$postlink = $dasboost->getPostLink($post['link']);

		$folder = 'video';
		$media  = $post['video'];
		if(in_array($postType, [0,1])){
			$folder = 'img';

			if($is_store){
				$media = trim($post['img'],';');
				$gallery = explode(";",$media);
			}else{
				$media = $post['image'];
				$gallery = explode(";",$media);	
			}	
		}

		$img_path = '/uploads/social-media-calendar/'.$folder.'/'.$item;	    		
		if($is_store){
			$img_path = ($post['img'] != '') ? '/uploads/social-media-calendar/'.$folder.'/'.$post['id'].'_'.$_SESSION['storeid'].'/' : '/uploads/social-media-calendar/'.$folder.'/';
		}
		?>

		<div class="row py-3">

			<div class="col">
				<span class="text-uppercase text-secondary font-12 font-bold mb-2 d-block">Date : <?php echo date("F d,Y h:i A",strtotime($post_date))?></span>
				<p>
					<?php echo $post['post'];?>					
					<?php if(!empty($post['link']) && strtolower(trim($post['portal']))!="instagram"){ ?>
						<a href="<?php echo $postlink;?>" target="_blank"><?php echo $postlink; ?></a>
					<?php } ?>
				</p>
			</div>
		</div>
		<div class="row">
			<?php 																				

			$count = count($gallery);

			if($count > 1) { 									
				?>				
				<div id="carouselIndicators<?php echo $post["id"]?>" class="carousel slide" data-ride="carousel">	
					<ol class="carousel-indicators">
						<?php for($i=0;$i<$count;$i++){ ?>
							<li data-target="#carouselIndicators<?php echo $post["id"]?>" data-slide-to="<?php echo $i?>" class="active"></li>
						<?php	} ?>
					</ol>
					<div class="carousel-inner">
						<?php for($i=0;$i<$count;$i++){ 
							$img = LOCAL_CLIENT_URL.$img_path.$gallery[$i];?>

							<div class="carousel-item <?php if($i==0) echo "active"; ?>">
								<a href="<?php echo $img ?>" class="fresco" data-fresco-group="<?php echo $post["id"]?>"><img src="<?php echo $img?>" class="d-block img-fluid"></a>
							</div>
						<?php	} ?>
					</div>
					<a class="carousel-control-prev" href="#carouselIndicators<?php echo $post["id"]?>" role="button" data-slide="prev">
						<span class="carousel-control-prev-icon" aria-hidden="true"></span>
						<span class="sr-only">Previous</span>
					</a>
					<a class="carousel-control-next" href="#carouselIndicators<?php echo $post["id"]?>" role="button" data-slide="next">
						<span class="carousel-control-next-icon" aria-hidden="true"></span>
						<span class="sr-only">Next</span>
					</a>
				</div>
			<?php } else if (strpos($post['image'], "vimeo") !== false){ ?>
				<div class="position-relative d-inline-block">
					<i class="fas fa-play-circle fa-4x position-absolute text-white center-both z-top"></i>

					<a href="<?php echo $post['image']?>" class="fresco position-relative d-block">
						<div class="position-absolute bg-overlay w-100 h-100"></div>
						<img src="<?php echo grab_vimeo_thumbnail(urlencode($post['image']))?>" class="img-fluid" />
					</a>
				</div>
			<?php	}else if(strpos($post['image'], "youtube") !== false) {
				$vid = explode("=",$post['image']);?>
				<div class="position-relative d-inline-block">
					<i class="fas fa-play-circle fa-4x position-absolute text-white center-both z-top"></i>

					<a href="<?php echo $post['image']?>" class="fresco position-relative d-block">
						<div class="position-absolute bg-overlay w-100 h-100"></div>
						<img src="https://img.youtube.com/vi/<?php echo $vid[1];?>/mqdefault.jpg" class="img-fluid" />
					</a>
				</div>

			<?php }
			else if(!empty($post['image'])){

				$img = LOCAL_CLIENT_URL.$img_path.$post['image'];
				?>
				<a href="<?php echo $img?>" class="fresco"><img src="<?php echo $img?>" class="img-fluid" /></a>

			<?php }else if(!empty($post['img'])){

				$img_post = trim($post['img'],";");	
				$img = LOCAL_CLIENT_URL.$img_path.$img_post;
				
				?>
				<a href="<?php echo $img?>" class="fresco"><img src="<?php echo $img?>" class="img-fluid" /></a>

			<?php }else{
				if(empty($post['image']) && empty($post['img'])){?>
					<img src="img/no_image_available.jpg" class="img-fluid" />
				<?php }
			} ?>
			<input type="hidden" name="http_path" id="http_path" value="<?php echo LOCAL_CLIENT_URL.$img_path;?>">
			<input type="hidden" name="media" id="media" value="<?php echo $media;?>">
			<input type="hidden" name="link" id="link" value="<?php echo $postlink;?>">
			<input type="hidden" name="post" id="post" value="<?php echo $post['post'];?>">
			<input type="hidden" name="post_date" id="post_date" value="<?php echo $post_date;?>">
		</div>
		<?php 
	}
}else echo '<div class="notice notice-danger box-shadow">There are no posts to display yet!</div>'; ?>