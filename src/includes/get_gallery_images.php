<?php
	session_start();
	include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
	include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");
?>
<div id="gallery-pag">
<div class="gallery-pag--page-1 d-none row content-page-active d-flex">
	<?php 
	if($_POST['status']=="user"){
		$favs = $db->rawQueryOne("select GROUP_CONCAT(gallery_id) as ids from gallery_favs where storeid = ?",array($_SESSION['storeid']));
		$favs = explode(",",$favs['ids']);
	}
	
	$sort = $db->escape($_POST['sort']);
	switch($sort){
		case "oldest":
			$orderBy["field"] = "date";
			$orderBy["val"] = "asc";
			break;
		case "a-z":
			$orderBy["field"] = "name";
			$orderBy["val"] = "asc";
			break;
		case "z-a":
			$orderBy["field"] = "name";
			$orderBy["val"] = "desc";
			break;
		default:
			$orderBy["field"] = "date";
			$orderBy["val"] = "desc";
	}
	$db->orderBy($orderBy["field"],$orderBy["val"]);
	if(!empty($_POST['category'])){
		$category = $db->escape($_POST['category']);
		//$db->where("category",$category);
		$db->where("FIND_IN_SET('".$category."',category)");
		if($category == 'videos'){
			$db->orWhere ("video_raw", NULL, 'IS NOT');
			$db->orWhere ("video_raw",'','!=');
			/*SELECT  * FROM gallery 
			WHERE  FIND_IN_SET('videos',category) 
			OR video_raw IS NOT NULL 
			OR video_raw != ''  
			AND active = '1'  
			ORDER BY date DESC  
			LIMIT 0, 24*/
		}
	}
	if(!empty($_POST['month'])){
		$month = $db->escape($_POST['month']);
		$db->where("month",$month);
	}
	if(!empty($_POST['search'])){
		$search = $db->escape($_POST['search']);
		/*$db->where ("name", '%'.$search.'%', 'like');
		$db->orWhere ("tags", '%'.$search.'%', 'like');
		$db->orWhere ("category", '%'.$search.'%', 'like');*/
		$db->Where("(name like '%$search%' OR tags like '%$search%' OR category like '%$search%')");
	}

	$db->where("active",1);
	if($_POST['status']=="user"){
		$db->Where("(storeid = ? OR storeid IS NULL OR apply_all = 1)",array($_SESSION['storeid']));
	}
	
	//Set current page
	$page = $_POST['current_page'];
	$num_rows_per_page = 24;
	$offset = ($page-1) * $num_rows_per_page;
	
	$images = $db->get("gallery", Array($offset, 24));

	if($db->count > 0){
		foreach($images as $img){
			
			$companyname = "";
			if(!empty($img['storeid'])){
				$db->where("storeid",$img['storeid']);
				$loc = $db->getOne("locationlist",array("companyname"));
				$companyname = $loc['companyname'];
			}
		?>
			<div class="col-sm-6 col-md-4 col-lg-2 mb-4">
				<div class="photo">
					<img src="<?php echo $img['thumbnail'] ?>" alt="<?php echo stripslashes($img['name']) ?>" class="img-fluid">
					<div class="bg-white">
						<div class="p-2">
							<span class="d-block font-semibold"><?php echo stripslashes($img['name']) ?></span>
							<span class="d-block small"><?php echo ucwords(str_replace("-"," ",$img['category'])) ?> </span>
							<span class="d-block text-muted small"><?php echo $img['video_raw'] ? "Video" : "Image" ?></span>
							<?php if($_POST['status']!="user"){ ?>
							<span class="d-block text-muted font-italic small">Uploaded by <?php echo !empty($companyname) ? $companyname : "Admin"; ?></span>
							<span class="d-block text-muted small"><?php echo (!empty($img['storeid']) && $img['apply_all']==1) || empty($img['storeid']) || ($img['storeid'] < 0) ? "Available for all" : "Available for ".$companyname; ?></span>
							<?php } ?>
						</div>
						<hr class="m-0">
						<div class="p-2">
							<a href="<?php echo $img['video_raw'] ? $img['video_raw'] : $img['image'] ?>" title="Download" class="text-light-d downloadImg" data-id="<?php echo $img['id'] ?>" download><span data-toggle="tooltip" data-placement="bottom" title="Click here to download the image."><i class="fas fa-download"></i></span></a>
							
							<?php if($_POST['status']=="user"){ ?>
							<a href="" title="Favorite" class="text-light-d ml-2 favImg <?php echo in_array($img['id'],$favs) ? "fav" : "" ?>" data-id="<?php echo $img['id'] ?>"><span data-toggle="tooltip" data-placement="bottom" title="Click here to mark the image as a favorite."><i class="fas fa-heart icon"></i></span></a>
							<a href="#requestImage" title="Customize" class="text-light-d ml-2" data-toggle="modal" data-target="#requestImage" data-id="<?php echo $img['id'] ?>"><span data-toggle="tooltip" data-placement="bottom" title="Click here to request a custom image using this image."><i class="fas fa-paint-brush"></i></span></a>
							<?php } ?>
							<a href="<?php echo !empty($img['video_link']) ? $img['video_link'] : $img['image'] ?>" data-fresco-caption="<?php echo stripslashes($img['name']) ?>" title="Expand" class="fresco text-light-d ml-2"><span data-toggle="tooltip" data-placement="bottom" title="Click here to see the image full size."><i class="fas fa-expand-arrows-alt"></i></span></a>
							<?php if(($_POST['status']=="user" && $_SESSION['storeid']==$img['storeid']) || $_POST['status']=="admin"){ ?>
								<a href="#editImage" title="Edit" class="text-light-d ml-2" data-toggle="modal" data-target="#editImage" data-id="<?php echo $img['id'] ?>"><i class="fas fa-edit"></i></a>
								<a href="#editThumbnail" title="Edit Thumbnail" class="text-light-d ml-2 editThumb" data-toggle="modal" data-target="#editThumbnail" data-image="<?php echo $img['image'] ?>"><i class="fas fa-magic"></i></a>
								<a href="/admin/graphics-gallery/delete_img.php" title="Delete Image" class="text-light-d ml-2 deleteImg" data-id="<?php echo $img['id'] ?>" data-type="<?php echo $_POST['status'] ?>"><i class="fas fa-trash"></i></a>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
		<?php } 
	}else echo "<div class='col-12'><p class='text-muted font-italic'>There are no posts matching your search criteria.</p></div>"?>
</div>
</div>