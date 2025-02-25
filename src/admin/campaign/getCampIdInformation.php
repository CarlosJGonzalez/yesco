<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");
include $_SERVER['DOCUMENT_ROOT'].'/includes/ClassDasCampaign.php';

$campaigns_info = new Das_Campaign($db,$token_api,$_SESSION['client'],$_SESSION['storeid']);
$id = $db->escape($_POST['id']);

$campidInfo = $campaigns_info->getCampIdInfo($id);

if( !isset($campidInfo[0]) ){
	die;
}
$campidInfo = $campidInfo[0];

?>
<input type="hidden" name="id" value="<?php echo $campidInfo['id']; ?>">
<input type="hidden" name="action" value="update_portal">
<div class="form-group">
	<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Description</label>
	<input type="text" pattern="[a-z A-Z:-_]+?" name="name" id="namePortalAdd" value="<?php echo $campidInfo['name']; ?>" class="form-control" required />
</div>
<div class="form-group">
	<div class="form-row">								   
		<!--<div class="col">
			<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Campaign ID</label>
			<input type="text" pattern="[0-9]+?" name="campid" id="input" value="<?php echo $campidInfo['campid']; ?>" class="form-control" required />
		</div>-->
		<div class="col">
			<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Channel</label>
			<select name="channel" id="utm_channel" class="form-control rounded-bottom rounded-right custom-select-arrow" required>
				<option  value="">Select Channel</option>
				<?php
				$channels = $campaigns_info->getChannels();
				foreach($channels as $channel) {
					$select ='';
					if($campidInfo['channel'] == $channel['name']){
						$id_channel = $channel['id'];
						$select = 'selected';
					}												
					?>
					<option data-id = "<?php echo $channel['id']; ?>" value="<?php echo $channel['name']; ?>" <?php echo $select ;?> ><?php echo $channel['name']; ?></option>
				<?php } ?>
			</select>
		</div>
	</div>
</div>

<div class="form-group">
	<div class="form-row">
		<div class="col">
			<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Source</label>
			<input type="text" value="<?php echo ($campidInfo['source']) ? $campidInfo['source'] : ''; ?>" name="source" id="source" class="form-control" required />			
		</div>

		<div class="col">
			<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Medium</label>
			<select name="medium" id="utm_medium" class="form-control rounded-bottom rounded-right custom-select-arrow" required>
				<option  value="">Select Medium</option>
				<?php
				$mediums = $campaigns_info->getMedium( $id_channel );																					
				foreach($mediums as $medium) {
					?>
					<option value="<?php echo $medium['name']; ?>" <?php echo ($campidInfo['medium'] == $medium['name']) ? 'selected': '' ;?> ><?php echo $medium['name']; ?></option>
				<?php } ?>
			</select>
		</div>
	</div>
</div>


<!--<div class="form-group">
	<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">MarkUp</label>
	<div class="input-group mb-3">

		<input type="text" pattern="[0-9]+(\.[0-9]{1,2})?%?" name="markup" value="<?php echo $campidInfo['markup'];?>" id="markupPortalAdd" class="form-control" required />
		<div class="input-group-append">
			<span class="input-group-text" id="basic-addon2">%</span>
		</div>
	</div>
</div>-->
<div class="form-group">
	<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Notes</label>
	<textarea name="note" placeholder="Notes" class="form-control"><?php echo isset($campidInfo['note']) ? $campidInfo['note'] : '';?></textarea>
</div>