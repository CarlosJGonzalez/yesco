<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");

$id = $db->escape($_POST['id']);
$db->where("id",$id);
$gallery_item = $db->getOne("gallery");

?>

<div class="form-group">
	<label class="text-uppercase small">Image Label<span class="text-danger">*</span></label>
	<input type="text" name="name" class="form-control" value="<?php echo $gallery_item['name']; ?>" required>
</div>

<div class="form-group">
	<label class="text-uppercase small">Category<span class="text-danger">*</span></label>
	<select name="category" class="form-control custom-select-arrow pr-4" required>
		<?php
		$db->where("option","gallery_cat");
		$vals = $db->get("option_values");
		foreach($vals as $val){
		?>
		<option value="<?php echo $val['value']; ?>" <?php if($gallery_item['category']==$val['value']) echo "selected"; ?>><?php echo $val['display_name']; ?></option>
		<?php } ?>
	</select>
</div>
<div class="form-group">
	<label class="text-uppercase small">Tags</label>
	<input type="text" name="tags" value="<?php echo $gallery_item['tags']; ?>" class="form-control">
</div>

<input type="hidden" name="id" value="<?php echo $gallery_item['id']; ?>">
<input type="hidden" name="type" value="admin">