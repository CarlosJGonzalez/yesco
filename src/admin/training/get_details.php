<?php
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");
session_start();
$id = $db->escape($_POST['id']);
$db->where("id",$id);
$details = $db->getOne("training");
?>
	<div class="form-group">
		<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Name<span class="text-danger">*</span></label>
		<input type="text" value="<?php echo $details['name']; ?>" name="name" placeholder="Name" class="form-control" required />
	</div>
	<?php if(!empty($item['show_link'])){ ?>
	<div class="form-group">
		<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Video Link</label>
		<input type="text" value="<?php echo $details['show_link']; ?>" name="show_link" placeholder="Video Link" class="form-control" />
		<small id="imgMsgContainer"><strong>Note:</strong> Vimeo or Youtube (if applicable)</small>
	</div>
	<?php } ?> 
	<div class="form-group">
		<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Description</label>
		<textarea name="description" placeholder="Description" class="form-control"><?php echo $details['description']; ?></textarea>
	</div>
	<div class="form-group">
		<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Category<span class="text-danger">*</span></label>
		<select name="category" class="form-control rounded-bottom rounded-right custom-select-arrow" required>
			<?php
			foreach ($trainingCategories as $cat) {?>
				<option value="<?php echo $cat?>" <?php if($details['category']==$cat) echo "selected"; ?>><?php echo $cat?></option>
			<?php } ?>
		</select>
	</div>
	<div class="form-group">
		<input type="hidden" name="id" value="<?php echo $details['id']; ?>">
	</div>