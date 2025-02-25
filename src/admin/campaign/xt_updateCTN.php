<?php

session_start();
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");
include $_SERVER['DOCUMENT_ROOT'].'/includes/ClassDasCampaign.php';

$campaigns_info = new Das_Campaign($db,$token_api,$_SESSION['client']);
$id = $db->escape($_POST['id']);
$callFireRouting = $campaigns_info->getCallFireRoutingInfo($id);

if( $callFireRouting ){
	$terminatingnum = isset($callFireRouting[0]['terminatingnum']) ? $callFireRouting[0]['terminatingnum'] : '';
	$ctn = isset($callFireRouting[0]['phone']) ? $callFireRouting[0]['phone'] : '';
}else{
	echo 0;
	exit();
}

?>
<input type="hidden" name="id" value="<?php echo $id;?>">
<input type="hidden" name="ctn_number" value="<?php echo $ctn;?>">
<input type="hidden" name="old_forward_number" value="<?php echo $terminatingnum;?>">
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
			<label class="font-11 text-uppercase bg-light py-1 px-2 mb-0 rounded-top letter-1 text-dark">Greeting</label>
			<input type="checkbox" name="call_greeting" id="greetingToggle" checked data-toggle="toggle" data-size="sm">
			<div id="greetingMessage" class="mt-1">
				<input type="text" name="greetingrMessage" id="greetingrMessagePortalAdd" value="This call will be recorded for quality assurance" placeholder="Message" class="form-control" />
			</div>
		</div>
	</div>
</div>