<div id="posts-pag">	
<?php
	session_start();
	include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
	include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");

	$fullUrl = getFullUrl();
	//$fullUrl = "https://localexperimac.com";
	
	$cols = Array("option","value");
	$db->where("option", Array("admin_start_date","admin_end_date","client_start_date","client_end_date"), 'IN');
	$show_date = $db->get("option_values",null,$cols);
	foreach($show_date as $row){
		$dates[$row["option"]] = $row["value"];
	}

	$start_date = $dates["admin_start_date"];
	$end_date = $dates["admin_end_date"];

	/*if(isset($_SESSION['admin'])){
		$start_date = $dates["admin_start_date"];
		$end_date = $dates["admin_end_date"];
	}else{
		$start_date = $dates["client_start_date"];
		$end_date = $dates["client_end_date"];
	}*/

	
	$db->where('date',array( $start_date.' 00:00:00', $end_date.' 23:59:59'),'BETWEEN');
	//$current_date = date("Y-m-d H:i:s");
	//$db->where ('date', $current_date, ">");

	if(!empty($_POST["portal"]) && $_POST["portal"] != 1){
		$db->where('portal',$_POST["portal"]);
	}
	
	if(isset($_POST["sort"]) && !empty($_POST["sort"])){
		$db->orderBy('curdate',$_POST["sort"]);
	}else{
		$db->orderBy('curdate','desc');
	}

	if(isset($_POST["search"]) && !empty($_POST["search"])){
		$db->where('post','%'.$_POST["search"].'%','like');
	}

	if(isset($_POST["type"]) && $_POST["type"] == 'store'){
		$posts = $db->get('social_media__local_posts a',null,"concat(DATE_FORMAT(a.date,'%W'),',',DATE_FORMAT(a.date,'%b %d %Y')) as date,a.id as id,post,link,image, a.date as curdate, portal, notes,video,boost,'0' as optional");
	}elseif(isset($_POST["type"]) && $_POST["type"] == 'corp'){
		$posts = $db->get('social_media_posts a',null,"concat(DATE_FORMAT(a.date,'%W'),',',DATE_FORMAT(a.date,'%b %d %Y')) as date,a.id as id,post,link,image, a.date as curdate, portal, '' as notes,video,'' as boost,'0' as optional");
	}else{
		$posts = $db->get('social_media__local_posts_optional a',null,"concat(DATE_FORMAT(a.date,'%W'),',',DATE_FORMAT(a.date,'%b %d %Y')) as date,a.id as id,post,link,image, a.date as curdate, portal, notes,video,boost,'1' as optional");
	}

	if(count($posts)){ 
		foreach($posts as $post){ ?>
	<div>
		<?php	if($prevDate != date("F d",strtotime($post['date']))){ ?>
			<span class="text-uppercase text-secondary font-12 font-bold mb-2 d-block"><?php echo date("F d",strtotime($post['date']))?></span>
		<?php } ?>
			<div class="post d-flex align-items-center mb-4">
				<label class="label cusor-pointer d-flex text-center mr-2" for="post_<?php echo $post['id']?>">
					<input class="label__checkbox post_check" type="checkbox" name="posts[]" value="<?php echo $post['id']?>" type="checkbox" id="post_<?php echo $post['id']?>" />
					<span class="label__text d-flex align-items-center">
					  <span class="label__check d-flex rounded-circle mr-2">
						<i class="fa fa-check icon small"></i>
					  </span>
					</span>
				</label>
<!--				<input type="checkbox" name="posts" class="mr-2" />-->
				<div class="border flex-grow rounded box-shadow">
					<?php if(!empty($post['notes'])){?>
					<div class="notes py-1 px-2 rounded-top">
						<i class="fas fa-exclamation-triangle mr-1"></i> 
							<?php echo $post['notes']?>
						<i class="fas fa-edit mr-1 edit_in_place" data-value="note_<?=$post['id']?>" style="color: #0067b1;"></i>
						<input type="text" class="d-none edit_in_place_input form-control input-lg"  value="<?=$post['notes']?>" id="input_note_<?=$post['id']?>">
					</div>
					<?php } ?>
					<div class="p-2 position-relative">
						<span class="font-16 font-bold text-uppercase mb-1 d-block"><?php echo $post['portal']?></span>

						<div class="row">
							<div class="col-sm-4">
								
								<?php if(strpos($post['image'], ";") !== false){ 
									$gallery = explode(";",$post['image']);
									$count = count($gallery);?>
								<div id="carouselIndicators<?php echo $post["id"]?>" class="carousel slide" data-ride="carousel">
								  <ol class="carousel-indicators">
									  <?php for($i=0;$i<$count;$i++){ ?>
									<li data-target="#carouselIndicators<?php echo $post["id"]?>" data-slide-to="<?php echo $i?>" class="active"></li>
									  <?php	} ?>
								  </ol>
								  <div class="carousel-inner">
								<?php for($i=0;$i<$count;$i++){ ?>
									<div class="carousel-item <?php if($i==0) echo "active"; ?>">
										<a href="<?php echo $fullUrl?>/uploads/social-media-calendar/img/<?php echo $gallery[$i]?>" class="fresco" data-fresco-group="<?php echo $post["id"]?>"><img src="<?php echo $fullUrl?>/uploads/social-media-calendar/img/<?php echo $gallery[$i]?>" class="d-block img-fluid"></a>
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
								 //else if(!empty($post['image']) && glob('img/'.$post['image'])){
								else if(!empty($post['image'])){?>
									<a href="<?php echo $fullUrl?>/uploads/social-media-calendar/img/<?php echo $post['image']?>" class="fresco"><img src="<?php echo $fullUrl?>/uploads/social-media-calendar/img/<?php echo $post['image']?>" class="img-fluid" /></a>
								<?php }
									else
										echo $post['image'];   ?>

							</div>
							<div class="col-sm-8">
								<?php if(!empty($post['link']) && strtolower(trim($post['portal']))!="instagram"){?>
									<a href="<?php echo $post['link']?>" target="_blank" id="label_link_<?=$post['id']?>" class="text-blue">
										<?php echo str_replace("[[site_url]]","url",$post['link']); ?>
									</a>

									<i class="fas fa-edit mr-1 edit_in_place text-muted ml-2 cursor-pointer" data-value="link_<?=$post['id']?>" id="link_<?=$post['id']?>" title="Edit Link"></i>
									
									<input type="text" class="d-none edit_in_place_input form-control input-lg"  value="<?php echo $post['link']?>" id="input_link_<?=$post['id']?>">
								<?php } ?>



								<div>
									<a id="label_postinfo_<?=$post['id']?>"><?php echo $post['post']?></a>

									<i class="fas fa-edit mr-1 edit_in_place text-muted ml-2 cursor-pointer" data-value="postinfo_<?=$post['id']?>" id="postinfo_<?=$post['id']?>" title="Edit Post"></i>

									<textarea class="d-none edit_in_place_input form-control input-lg" rows="3" id="input_postinfo_<?=$post['id']?>"><?=$post['post']?></textarea>

								</div>
							</div>
						</div>
						<!--<div class="dropup position-absolute p-bottom-right cursor-pointer">
							<div class=" rounded-bottom-right rounded-bottom-right py-1 px-2 border bg-dark-blue text-white" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="optionsButton">Options</div>
							<div class="dropdown-menu dropdown-menu-right font-12 p-0" aria-labelledby="optionsButton">
								<a class="dropdown-item px-2" href="edit.php?id=<?php echo $post['id']?>">Edit</a>
								<a class="dropdown-item px-2" href="#">Opt Out</a>
								<?php if(strtolower(trim($post['portal']))=="facebook"){ ?><a class="dropdown-item px-2" href="#">Boost Post</a><? } ?>
							</div>
						</div>-->
					</div>
				</div>
			</div>
		<?php 
			$prevDate = date("F d",strtotime($post['date'])); ?>
		</div>
		<?php }
		}else echo "Sorry there are no posts. Please, change your search and try again."; ?>
</div>