<?php
	session_start();
	include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
	include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");
					$favs = $db->rawQuery("select * from gallery where id in (select gallery_id from gallery_favs where storeid = ?)",array($_SESSION['storeid']));
					if($db->count > 0){
				 ?>
				<div class="featured bg-white border-bottom mb-4 p-3">
					<div class="px-4">
						<div class="slider">
							<?php 
								foreach($favs as $fav){
							 ?>
							<div class="text-center"><div class="hexagon hexagon2 m-auto"><div class="hexagon-in1"><div class="hexagon-in2" style="background-image:url(<?php echo $fav['thumbnail'] ?>)">
								<div class="overlay">
									<p class="mb-0 text-center position-absolute">
										<a href="" title="Download" class="text-white"><i class="fas fa-download"></i></a>
										<a href="" title="Favorite" class="text-white ml-2 favImg fav"><i class="fas fa-heart icon"></i></a>
										<a href="" title="Customize" class="text-white ml-2"><i class="fas fa-paint-brush"></i></a>
										<a href="<?php echo $fav['image'] ?>" data-fresco-caption="<?php echo $fav['name'] ?>" title="Expand" class="fresco text-white ml-2"><i class="fas fa-expand-arrows-alt"></i></a>
									</p>
								</div>
							</div></div></div></div>
							<?php } ?>

						</div>
					</div>
				</div>
				<?php } ?>