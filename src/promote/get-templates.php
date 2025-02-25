<?php
include_once ($_SERVER['DOCUMENT_ROOT'].'/includes/connect.php');

if(!empty($_POST['values']['sort'])){
	$order = $db->escape($_POST['values']['sort']);
	switch($order){
		case "a-z":
			$db->orderBy("title","asc");
		break;
		case "z-a":
			$db->orderBy("title","desc");
		break;
		case "newest":
			$db->orderBy("date","desc");
		break;
		case "oldest":
			$db->orderBy("date","asc");
		break;
		default:
			$db->orderBy("date","desc");
		break;
	}
}
if(!empty($_POST['values']['search'])){
	$search = $db->escape($_POST['values']['search']);
	$db->where ("title", '%'.$search.'%', 'like');

}
$db->where("active","1");
$templates = $db->get("email_templates");
if($db->count>0){
foreach ($templates as $template){ ?>
<div class="col-6 col-md-4 col-lg-3 col-xl-2 template text-center mb-5 d-flex flex-column cursor-pointer">
	<img src="<?php echo !empty($template['thumbnail']) ? $template['thumbnail'] :"https://via.placeholder.com/150x200"; ?>" class="img-fluid mb-2">
	<span class="d-block rounded-pill text-white bg-blue px-4 py-2 text-center mt-auto"><?php echo $template['title']; ?></span>
	<input type="radio" name="template" data-id="<?php echo $template['id']?>" value="<?php echo $template['template_name']?>" class="d-none">
</div>
<?php } 
}else echo '<div class="col-12"><p class="text-muted font-italic">There are no templates matching your search criteria.</p></div>'?>