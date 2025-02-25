<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");

$id = $db->escape($_POST['id']);
$db->where("id",$id);
$custom_request = $db->getOne("custom_requests");
?>
<div class="form-group">
	<label class="text-uppercase small">Status<span class="text-danger">*</span></label>
	<select name="status" class="form-control custom-select-arrow pr-4" required>
		<?php
		$statuses = array("Canceled","Completed","Paid","Pending");
		foreach($statuses as $status){
		?>
		<option value="<?php echo $status; ?>" <?php if($custom_request['status']==$status) echo "selected"; ?>><?php echo $status; ?></option>
		<?php } ?>
	</select>
</div>
<div class="form-group">
	<label class="text-uppercase small">Notes</label>
	<textarea name="notes" id="notes" class="form-control"><?php echo $custom_request['notes']; ?></textarea>
</div>
<input type="hidden" name="id" value="<?php echo $custom_request['id']; ?>">
<input type="hidden" name="type" value="admin">