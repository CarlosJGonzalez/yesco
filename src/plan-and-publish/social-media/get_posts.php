<div id="posts-pag">	
	<?php
	session_start();
	include_once ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
	include_once ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");
	include_once ($_SERVER['DOCUMENT_ROOT']."/includes/ClassDasPost.php");

	$fullUrl = getFullUrl();
	$cols = Array("option","value");
	$db->where("option", Array("admin_start_date","admin_end_date","client_start_date","client_end_date"), 'IN');
	$show_date = $db->get("option_values",null,$cols);
	foreach($show_date as $row){
		$dates[$row["option"]] = $row["value"];
	}

	if(isset($_SESSION['admin'])){
		$start_date = $dates["admin_start_date"];
		$end_date = $dates["admin_end_date"];
	}else{
		$start_date = $dates["client_start_date"];
		$end_date = $dates["client_end_date"];
	}

	$start_date = date("Y-m-d H:i:s");

	$sort = isset($_POST["sort"]) ? $_POST["sort"] : "desc";

	if(!empty(($_POST["portal"])))
		$posts = $db->rawQuery("(select a.date,a.id as id,post,link,image,optout,a.date as curdate, portal, '' as img,  notes,boost,'0' as optional from social_media__local_posts a left join social_media_local_posts_optout b on a.id=b.id and b.storeid=? where date(a.date) BETWEEN ? and ? and a.id not in (select id from social_media_local_posts_store where storeid=? and date(date) BETWEEN ? and ?) and portal=?) UNION  (select date,id,post,link,image,null,date as curdate, portal, img,'' as notes,'0' as boost,'0' as optional from social_media_local_posts_store where storeid=? and date(date) BETWEEN ? and ? and portal=?) UNION  (select date,id,post,link,image,null,date as curdate, portal, '' as img,  notes,'0' as boost,'1' as optional from social_media__local_posts_optional where date(date) BETWEEN ? and ? and portal=? and  id not in (select id from social_media_local_posts_store where storeid=? and date(date) BETWEEN ? and ? and portal=? )) order by curdate ".$sort."",Array($_SESSION['storeid'], $start_date, $end_date, $_SESSION['storeid'], $start_date, $end_date,$_POST["portal"], $_SESSION['storeid'], $start_date, $end_date,$_POST["portal"], $start_date, $end_date,$_POST["portal"], $_SESSION['storeid'], $start_date, $end_date,$_POST["portal"]));

	else
		$posts = $db->rawQuery("(select a.date,a.id as id,post,link,image,optout,a.date as curdate, portal, '' as img,  notes,boost,'0' as optional from social_media__local_posts a left join social_media_local_posts_optout b on a.id=b.id and b.storeid=? where date(a.date) BETWEEN ? and ? and a.id not in (select id from social_media_local_posts_store where storeid=? and date(date) BETWEEN ? and ?)) UNION  (select date,id,post,link,image,null,date as curdate, portal, img,'' as notes,'0' as boost,'0' as optional  from social_media_local_posts_store where storeid=? and date(date) BETWEEN ? and ?) UNION  (select date,id,post,link,image,null,date as curdate, portal, '' as img,  notes,'0' as boost,'1' as optional from social_media__local_posts_optional where date(date) BETWEEN ? and ? and id not in (select id from social_media_local_posts_store where storeid=? and date(date) BETWEEN ? and ?  ) ) order by curdate ".$sort."",Array($_SESSION['storeid'], $start_date, $end_date, $_SESSION['storeid'], $start_date, $end_date, $_SESSION['storeid'], $start_date, $end_date, $start_date, $end_date, $_SESSION['storeid'], $start_date, $end_date));

	
	if($db->count>0){
		$dasboost = new Das_Post($db,$_SESSION['client'],$_SESSION['storeid']);
		foreach($posts as $post){
			
			$isOut = $dasboost->isOut($post['id']);
			$isBoostOut = $dasboost->isOutBoost($post['id']);
			$isDefaultBoost = $dasboost->isDefaultBoost($post['id']);
			$is_store = $dasboost->isStore($post['id']);
			$postType = $dasboost->getPostType($post);
			$postlink = $dasboost->getPostLink($post['link']);
			$postComment = $dasboost->replaceVariable($post['post']);
			$isOptionalPost = (isset($post['optional']) && $post['optional'] == '1') ? true : false;
			
			$folder = 'video';   		
			if(in_array($postType, [0,1])){
				$folder = 'img';
			}

			$img_path = '/uploads/social-media-calendar/'.$folder.'/'.$item;	    		
			if($is_store){
				$img_path = ($post['img'] != '') ? '/uploads/social-media-calendar/'.$folder.'/'.$post['id'].'_'.$_SESSION['storeid'].'/' : '/uploads/social-media-calendar/'.$folder.'/';
			}    	   		

			?>		
			<div>
				<?php	if($prevDate != date("F d",strtotime($post['date']))){ ?>
					<span class="text-uppercase text-secondary font-12 font-bold mb-2 d-block"><?php echo date("F d",strtotime($post['date']))?></span>
				<?php } ?>
				<div class="post d-flex align-items-center mb-4">
					<?php if(!$isOut) { ?> 
						<label class="label cusor-pointer d-flex text-center mr-2" for="post_<?php echo $post['id']?>">
							<input class="label__checkbox post_check" type="checkbox" name="posts[]" value="<?php echo $post['id']?>" type="checkbox" id="post_<?php echo $post['id']?>" />
							<span class="label__text d-flex align-items-center">
								<span class="label__check d-flex rounded-circle mr-2">
									<i class="fa fa-check icon small"></i>
								</span>
							</span>
						</label>
					<?php } ?>
					<div class="border flex-grow rounded position-relative">
						<?php if($post['boost']){?>
							<i class="fa fa-star position-absolute top-right mr-1 mt-1 small text-gold" aria-hidden="true"></i>
						<?php } if(!empty($post['notes'])){?>
							<div class="notes py-1 px-2 rounded-top"><i class="fas fa-edit mr-1"></i> <?php echo $post['notes']?></div>
						<?php } ?>
						<div class="p-2 position-relative">
							<div class="d-block mb-1">
								<span class="font-16 font-bold text-uppercase mb-1"><?php echo $post['portal'];?></span>
								<span class="text-muted text-uppercase mb-1"><?php echo ' | '.date("F d h:i A",strtotime($post['date']));?></span>
								<?php if( $isOptionalPost ){?>
									<span class="font-16 font-bold text-uppercase mb-1"><?php echo ' | Optional Post';?></span>
								<?php } ?>
								
							</div>


							<div class="row" id="cont_inf_<?php echo $post['id']?>" <?=($isOut)? 'style="opacity: 0.4;"':''?>>
								<div class="col-sm-4">								
									<?php 

									if($is_store){
										$gallery = explode(";",trim($post['img'],';'));
									}else{
										$gallery = explode(";",$post['image']);	
									}																		
									
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
													$img = $fullUrl.$img_path.$gallery[$i];?>

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

										$img = $fullUrl.$img_path.$post['image'];
										?>
										<a href="<?php echo $img?>" class="fresco"><img src="<?php echo $img?>" class="img-fluid" /></a>

									<?php }else if(!empty($post['img'])){

										$img_post = trim($post['img'],";");	
										$img = $fullUrl.$img_path.$img_post;

										?>
										<a href="<?php echo $img?>" class="fresco"><img src="<?php echo $img?>" class="img-fluid" /></a>

									<?php }else{
										if(empty($post['image']) && empty($post['img'])){?>
											<img src="img/no_image_available.jpg" class="img-fluid" />
										<?php }
									} ?>
									

								</div>
								<div class="col-sm-8">								
									<a><?php echo $postComment;?></a>
									<?php if(!empty($post['link']) && strtolower(trim($post['portal']))!="instagram"){?><a href="<?php echo $postlink;?>" target="_blank"><?php echo $postlink; ?></a><?php } ?>
								</div>
							</div>
							<?php if(strtotime($post['date']) > strtotime("now") ){ ?>
								<div class="dropup position-absolute p-bottom-right cursor-pointer">
									<div class=" rounded-bottom-right rounded-bottom-right py-1 px-2 border bg-dark-blue text-white" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="optionsButton">Options </div>
									<div class="dropdown-menu dropdown-menu-right font-12 p-0" aria-labelledby="optionsButton">
										<?php if( !$isOptionalPost ){ ?>
											<a class="dropdown-item px-2" id="action_edit_<?php echo $post['id']?>" <?=($isOut)?'style="display:none;"':'href="edit.php?id='.$post["id"].'"'?> >Edit </a>
											<a class="dropdown-item px-2 post_opts" id="opt_<?php echo $post['id']?>" data-value="<?=$isOut?>" data-postid="<?=$post['id']?>" href="javascript:void(0)"><?=(!$isOut) ?'Opt Out':'Opt In'?></a>

											<?php
											if(strtolower(trim($post['portal']))=="facebook" ){ ?>
												<a class="dropdown-item px-2" id="action_boost_post_<?php echo $post['id']?>" <?=($isOut)?'style="display:none;"':'href="/plan-and-publish/social-media/fbBoost/?post_id='.$post["id"].'"'?> >Boost Post</a>
											<?php } ?>	

											<?php if( isset($post['boost']) && $post['boost'] && false){?>
												<a class="dropdown-item px-2"  id ='opt_boost' data-value="<?=$isBoostOut?>" data-postid="<?=$post['id']?>" href="javascript:void(0)"><?=(!$isBoostOut)?'Remove Boosted Post':'Boost this Post'?></a>
											<?php }
											if(strtolower(trim($post['portal']))=="facebook" && !$isDefaultBoost && false){ ?><a class="dropdown-item px-2" href="#">Boost this Post</a>
										<? } ?>
									<?php }else{?>
										<a class="dropdown-item px-2 post_optional" id="optional_<?php echo $post['id']?>" data-value="<?php echo $isOptionalPost;?>" data-postid="<?=$post['id']?>" href="javascript:void(0)">Accept Optional Post</a>
									<?php }?>

								</div>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
			<?php 
			$prevDate = date("F d",strtotime($post['date'])); ?>
		</div>
	<?php }
}else echo '<div class="notice notice-danger box-shadow">There are no posts to display yet!</div>'; ?>
</div>