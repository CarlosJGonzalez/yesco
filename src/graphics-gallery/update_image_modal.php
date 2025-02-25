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
<div class="form-group">
	<label class="label cusor-pointer d-flex text-center" for="apply_all">
		<input  class="label__checkbox" type="checkbox" name="apply_all" value="1" type="checkbox" id="apply_all" <?php if($gallery_item['apply_all']==1) echo "checked"; ?> />
		<span class="label__text d-flex align-items-center">
		  <span class="label__check d-flex rounded-circle mr-2">
			<i class="fa fa-check icon small"></i>
		  </span>
			<span class="text-uppercase small letter-spacing-1 d-inline-block">Make available for all locations</span>
		</span>
	  </label>
</div>
<input type="hidden" name="id" value="<?php echo $gallery_item['id']; ?>">
<input type="hidden" name="type" value="user">