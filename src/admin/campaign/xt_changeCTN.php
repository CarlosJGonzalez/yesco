<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");
include $_SERVER['DOCUMENT_ROOT'].'/includes/ClassDasCampaign.php';

$campaigns_info = new Das_Campaign($db,$token_api,$_SESSION['client']);
$id = $db->escape($_POST['id']);

$campid = $campaigns_info->getCampIdInfo($id);
$terminatingnum = '';
$callRail_areacode = '';
$id_callFireRouting = false;
$callRail = false;

if($campid){
	$callFireRouting = $campaigns_info->getCallFireRouting($campid[0]['campid'],array('is_inactive' => 0 ));
	$callFireRouting = isset($callFireRouting['data'][0]['phone']) ? $callFireRouting['data'][0] : false;

	if($callFireRouting){
		$terminatingnum = isset($callFireRouting['terminatingnum']) ? $callFireRouting['terminatingnum'] : '';
		$callRail = $campaigns_info->getExistTracker($callFireRouting['phone'],$callFireRouting['terminatingnum']);
		$callRail_areacode = isset($callRail[0]['tracking_number']) ? substr($callRail[0]['tracking_number'], 0, 3) : '';
		$callRail = isset($callRail[0]['id']) ? $callRail[0]['id'] : false;				
		$id_callFireRouting = $callFireRouting['id'];
	}
	
}else{
	echo 0;exit();
}
?>
<input type="hidden" name="id" value="<?php echo $id;?>">
<input type="hidden" name="id_callFireRouting" value="<?php echo $id_callFireRouting;?>">
<input type="hidden" name="id_callRail" value="<?php echo $callRail;?>">
<input type="hidden" name="action" value="change_ctn">

<div class="form-group">
	<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Client</label>
	<select name="client" id="company" class="form-control rounded-bottom rounded-right custom-select-arrow">
		<option value="">Select Client</option>									
	</select>
</div>


<div class="form-group">
	<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Number Name</label>
	<input type="text" value="<?php echo CLIENT_NAME; ?> {<?php echo $_SESSION['client']; ?>}[<?php echo $campid[0]['campid']; ?>] " name="numberName" id="numberNamePortalAdd" class="form-control"  />
</div>

<div class="form-group">
	<label class="font-12 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Forward Calls To</label>
	<select name="country" id="country" class="form-control rounded-bottom rounded-right custom-select-arrow mb-1" >
		<option  value="">Select Country</option>
		<option value="+1" selected>US & Canada</option>
		<option value="+44">Australia</option>
		<option value="+66">United Kingdom</option>
	</select>
	<div class="input-group mb-3">
		<div class="input-group-append">
			<span class="input-group-text rounded-left country-code" id="basic-addon2">+1</span>
		</div>
		<input type="phone" name="forward" id="forwardPortalAdd" class="form-control" value="<?php echo $terminatingnum; ?>" />
	</div>
</div>

<div class="form-group">
	<div class="form-row">
		<div class="col">
			<label class="font-11 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Diff AreaCode</label>
			<input type="checkbox" name="forwardAreacodeToggle" <?php echo ($callRail_areacode == '') ? '' : 'checked';?> id="forwardAreacodeToggle" data-toggle="toggle" data-size="sm">
			<div id="forwardAreacodeDiv" class="mt-1" <?php echo ($callRail_areacode == '') ? '' : 'style="display: unset;"';?>>
				<input type="tel" name="forwardAreacode" id="forwardAreacode" value = "<?php echo $callRail_areacode;?>" class="form-control" size="3" pattern="\d{3}" title="3-digit area code" />
			</div>
		</div>
		<div class="col">
			<label class="font-11 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Recording</label>
			<input type="checkbox" name="call_recording" checked data-toggle="toggle" data-size="sm">
		</div>
		<div class="col">
			<label class="font-11 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">SMS</label>
			<input type="checkbox" name="sms_enabled" data-toggle="toggle" data-size="sm">
		</div>								
	</div>
</div>

<div class="form-group">
	<div class="form-row">	
		<div class="col">
			<label class="font-11 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Toll Free</label>
			<input type="checkbox" name="is_tollfree" id="is_tollfree" data-toggle="toggle" data-size="sm">
			<div id="tollFreeDiv" class="mt-1">
				<select name="tollFreeAreaCode" id="tollFreeAreaCode" class="form-control rounded-bottom rounded-right custom-select-arrow mb-1" >
					<option value="">Select Area Code</option>
					<option value="800">800</option>
					<option value="888">888</option>
					<option value="877">877</option>
					<option value="866">866</option>
					<option value="855">855</option>
					<option value="844">844</option>
					<option value="833">833</option>
				</select>
			</div>
		</div>
		<div class="col">
			<label class="font-11 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Greeting</label>
			<input type="checkbox" name="call_greeting" id="greetingToggle" checked data-toggle="toggle" data-size="sm">
			<div id="greetingMessage" class="mt-1">
				<input type="text" name="greetingrMessage" id="greetingrMessagePortalAdd" value="This call will be recorded for quality assurance" placeholder="Message" class="form-control" />
			</div>
		</div>
		<div class="col">
			<label class="font-11 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Whisper</label>
			<input type="checkbox" name="whisper_message" data-toggle="toggle" id="whisperToggle" data-size="sm">
			<div id="whisperMessage" class="mt-1">
				<input type="text" name="whisperMessage" id="whisperMessagePortalAdd" placeholder="Message" class="form-control" />
			</div>
		</div>
	</div>
</div>