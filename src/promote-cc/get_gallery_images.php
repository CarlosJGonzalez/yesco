<?php
	session_start();
	include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
	include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");

?>
<div id="gallery-pag">
	<?php 
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
	}
	if(!empty($_POST['month'])){
		$month = $db->escape($_POST['month']);
		$db->where("month",$month);
	}
	if(!empty($_POST['search'])){
		$search = $db->escape($_POST['search']);
		$db->where ("name", '%'.$search.'%', 'like');
		$db->orWhere ("tags", '%'.$search.'%', 'like');
		$db->orWhere ("category", '%'.$search.'%', 'like');
	}

	$db->where("active",1);
	$db->where("storeid",$_SESSION['storeid']);
	$db->orWhere("storeid",NULL,"IS");
	$db->orWhere("apply_all",1);
	//$db->setTrace (true);
	$images = $db->get("gallery");
	//print_r ($db->trace);
	if($db->count > 0){
		foreach($images as $img){
			$companyname = "";
			if(!empty($img['storeid'])){
				$db->where("storeid",$img['storeid']);
				$loc = $db->getOne("locationlist",array("companyname"));
				$companyname = $loc['companyname'];
			}
		?>
			<div class="col-sm col-md-4 col-lg-2 mb-4">
				<div class="photo">
					<div class="imageForBanner" id="imageForBanner-<?php echo $img['id'] ?>">
						<img id="imageFromLibrary-<?php echo $img['id'] ?>" src="<?php echo $img['thumbnail'] ?>" alt="<?php echo $img['name'] ?>" class="img-fluid">
						<div class="bg-white">
							<div class="p-2">
								<span class="d-block font-semibold"><?php echo $img['name'] ?></span>
								<span class="d-block small"><?php echo ucwords(str_replace("-"," ",$img['category'])) ?></span>
								<span class="d-block text-muted small"><?php echo $img['video_raw'] ? "Video" : "Image" ?></span>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php } 
	}else echo "<div class='col-12'><p class='text-muted font-italic'>There are no images matching your search criteria.</p></div>"?>
</div>