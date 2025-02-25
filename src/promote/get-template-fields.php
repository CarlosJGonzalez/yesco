<?php
session_start();
include_once ($_SERVER['DOCUMENT_ROOT'].'/includes/connect.php');
$active_location = $db->where("storeid",$_SESSION['storeid'])->getOne("locationlist");

$id = $db->escape($_POST['id']);
$db->orderBy("sort","asc");
$db->where("template_id",$id);
$fields = $db->get("email_template_fields");
if($db->count>0){
	foreach ($fields as $field){
		switch ($field['type']) {
			case "textarea": 
			
			$company_name = (isset($active_location['companyname']) && !empty($active_location['companyname'])) ? $active_location['companyname'] : '';
			$default_field = $field['default_text'];
																													
			if (strpos($default_field, '[[company_name]]') !== false)
				$default_field = str_replace("[[company_name]]",$company_name,$default_field);
			
			$city = (isset($active_location['city']) && !empty($active_location['city'])) ? $active_location['city'] : '';
			$default_field = $field['default_text'];
																													
			if (strpos($default_field, '[[city]]') !== false)
				$default_field = str_replace("[[city]]",$city,$default_field);
			?>
				<div class="form-group">
					<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark"><?php echo $field['display_name']; ?></label>
					<textarea name="<?php echo $field['field_name']; ?>" id="<?php echo $field['field_name']; ?>" class="form-control emailText rounded-bottom rounded-right"><?php echo $default_field; ?></textarea>
				</div>
	<?php	break;
				case "textarea_rich": ?>
				<div class="form-group">
					<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark"><?php echo $field['display_name']; ?></label>
					<textarea name="<?php echo $field['field_name']; ?>" class="form-control emailText rounded-bottom rounded-right rich"><?php echo $field['default_text']; ?></textarea>
				</div>
	<?php	break;
			case "file": ?>
				<div class="form-group">
					<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark"><?php echo $field['display_name']; ?></label>
					<div class="d-flex align-items-center">
						<div class="input-group w-auto">
						  <div class="custom-file">
							<input type="file" name="<?php echo $field['field_name']; ?>" class="form-control emailText rounded-bottom rounded-right custom-file-input" id="inputGroupFile<?php echo $field['field_name']; ?>" onchange="validateFiles(this.id,'imgMsgContainer','image','showCustomOnlyTest',1,4000000)" accept="image/jpg, image/png, image/jpeg, image/gif">
							<label class="custom-file-label" for="inputGroupFile<?php echo $field['field_name']; ?>">Choose file</label>
						  </div>
						</div>
						<small class="d-block text-uppercase letter-spacing-1 my-1 mx-3">&mdash; or &mdash;</small>
						<button name="gallery-field-<?php echo $field['field_name']; ?>" value="" class="btn btn-sm border bg-light text-dark py-2 px-3 browseGallery" data-toggle="modal" data-target=".graphics">Select From Library</button>
						<input type="hidden" name="hidden-file-<?php echo $field['field_name']; ?>" value="<?php echo $field['default_text']; ?>">
					</div>
					<small id="imgMsgContainer">Only image files are accepted.</small>
				</div>
	<?php	break;
			case "contact_url": ?>
				<div class="form-group">
					<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark"><?php echo $field['display_name']; ?></label>
					<input type="text" name="<?php echo $field['field_name']; ?>" class="form-control emailText rounded-bottom rounded-right" value="<?php echo CLIENT_URL."locations/".$active_location['url']."/contact/"; ?>">
				</div>
	<?php	break;
			case "shop_url": ?>
			<div class="form-group">
				<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark"><?php echo $field['display_name']; ?></label>
				<input type="text" name="<?php echo $field['field_name']; ?>" class="form-control emailText rounded-bottom rounded-right" value="<?php if(isset($active_location['shop_url']) && !empty($active_location['shop_url'])) echo $active_location['shop_url']; else echo SHOP_CLIENT_URL; ?>">
			</div>
	<?php	break;
			default: 
			
			$company_name = (isset($active_location['companyname']) && !empty($active_location['companyname'])) ? $active_location['companyname'] : '';
			$default_field = $field['default_text'];
																													
			if (strpos($default_field, '[[company_name]]') !== false)
				$default_field = str_replace("[[company_name]]",$company_name,$default_field);
			
			$city = (isset($active_location['city']) && !empty($active_location['city'])) ? $active_location['city'] : '';
			$default_field = $field['default_text'];
																													
			if (strpos($default_field, '[[city]]') !== false)
				$default_field = str_replace("[[city]]",$city,$default_field);
			?>
				<div class="form-group">
					<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark"><?php echo $field['display_name']; ?></label>
					<input type="text" name="<?php echo $field['field_name']; ?>" class="form-control emailText rounded-bottom rounded-right" value="<?php echo $default_field; ?>">
				</div>
	<?php	}
	?>

	<?php } ?>
	<div class="form-group">
		<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Phone</label>
		<input type="text" name="phone" class="form-control emailText rounded-bottom rounded-right" value="<?php echo $active_location['phone']; ?>">
	</div>
	<div class="form-group">
		<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Address</label>
		<input type="text" name="address" class="form-control emailText rounded-bottom rounded-right" value="<?php echo $active_location['address'].' '.$active_location['address2'].', '.$active_location['city'].', '.$active_location['state'].' '.$active_location['zip']; ?>">
	</div>

	<div class="text-right my-2">
	<button id="showCustom" class="btn bg-blue text-white text-uppercase btn-sm letter-spacing-1">Show Customizations</button>
	<input type="hidden" id="showCustomOnlyTest">
	</div>
<?php } ?>