<?php
session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");
include $_SERVER['DOCUMENT_ROOT'].'/includes/ClassDasCampaign.php';

$campaigns_info = new Das_Campaign($db,$token_api,$_SESSION['client']);
$campaign = $campaigns_info->getCampaign($db->escape($_POST['id']));

if($campaign['end_date'] == '0000-00-00 00:00:00'){
	$date = '';
}else{
	$date = date("m/d/Y",strtotime($campaign['end_date']));
}
?>
<!--<div class="form-group">
	<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Portal<span class="text-danger">*</span></label>
	<select name="portal" class="form-control rounded-bottom rounded-right custom-select-arrow" required>
		<?php
		$portals = $campaigns_info->getPortals(); 
		foreach($portals as $portal) {?>
			<option value="<?php echo $portal['value']; ?>" <?php if($campaign['portal']==$portal['value']) echo "selected"; ?>><?php echo $portal['display_name']; ?></option>
		<?php } ?>
	</select>
</div>
<div class="form-group">
	<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Campaign Name<span class="text-danger">*</span></label>
	<input type="text" name="campaign_name" class="form-control" value="<?php echo $campaign['campaign_name']?>" required />
</div>
<div class="form-group">
	<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Start Date<span class="text-danger">*</span></label>
	<input type="text" name="start_date" class="form-control datepicker" value="<?php echo date("m/d/Y",strtotime($campaign['start_date']))?>" required />
</div>
-->
<div class="form-group">
	<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">End Date<span class="text-danger">*</span></label>
	<input type="text" name="end_date" class="form-control datepicker" value="<?php echo $date;?>"/>
</div>
<!--
<div class="form-group">
	<div class="form-row">
	    <div class="col">
	    	<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">budget</label>
			<input type="text" pattern="[0-9]+(\.[0-9]{1,2})?%?" name="budget" id="budget" class="form-control" value="<?php echo $campaign['budget']?>"  required />
	    </div>
	    <div class="col">
		    <label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Payment Period</label>
			<select name="payment_period" class="form-control rounded-bottom rounded-right custom-select-arrow" required>
				<?php
				$payPeriodTypes = $campaigns_info->getPayPeriodType();																					
				foreach($payPeriodTypes as $payPeriodType) {
					$selected = ($payPeriodType['unique_name'] == $campaign['payment_period'])? 'selected':'';
					?>
					<option <?php echo $selected;?> value="<?php echo $payPeriodType['unique_name']; ?>"><?php echo $payPeriodType['name']; ?></option>
				<?php } ?>
			</select>
	    </div>
	  </div>
</div>
<div class="form-group">
	<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Notes</label>
	<textarea name="notes" placeholder="Notes" class="form-control"><?php echo preg_replace('/\v+|\\\r\\\n/Ui',PHP_EOL,$campaign['notes'])?></textarea>
</div>
-->
<div class="form-group">
	<input type="hidden" name="id" value="<?php echo $campaign['id']?>">
</div>