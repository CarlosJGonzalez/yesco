<?php
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");
session_start();

require ($_SERVER['DOCUMENT_ROOT']."/includes/DasApiSDK/vendor/autoload.php");
use Das\CallRail;

$sql = "select * from advtrack.calls where callid='".$_POST['callid']."' limit 1";
$call = $db->rawQueryOne($sql);
if($call['recordurl']){
  $callurl=$call['recordurl'];
}else{
  $callRail = new CallRail($token_api);
  $recordurl = $callRail->getRecording( $_POST['callid'] );
  $callurl = isset($recordurl['data'][0]['url']) ? $recordurl['data'][0]['url'] : getrecordurl($_POST['callid'],$_POST['vendorid']);
}

if($callurl){ ?>
<audio controls>
	<source src="<?=$callurl?>" type="audio/mpeg">
</audio>
<small class="d-block mb-2">If you are having trouble listening to the call, <a href="<?php echo $callurl?>" class="text-blue" download>try downloading it here</a>.</small>
<?php }else{ ?>
<span class="d-block mb-2"><strong>Note:</strong> There is no audio file for this call.</span>
<?php } ?>
<div class="form-group"><label><strong>Date/Time</strong></label><br>
<?=date("m/d/Y g:i:s A",strtotime($call['start']))?></div>

<div class="form-group"><label><strong>Caller #</strong></label><br>
<?=format_phone($call['caller'])?></p>

<div class="form-group"><label><strong>Caller ID</strong></label><br>
<?=format_phone($call['called'])?></p>

<div class="form-group flagged"><label><strong>Flag</strong></label><br>
	<input id="box1" type="checkbox" name="flagged" value="1" <?php if('1'==$call['flagged']) echo "checked" ?> />
	<label for="box1" id="box1label"></label>
</div>

<div class="form-group"><label><strong>Rating</strong></label><br>
	<fieldset>
        <span class="star-cb-group">
          <input type="radio" id="rating-5" name="rating" value="5" <?php if('5'==$call['rating']) echo "checked" ?> /><label for="rating-5">5</label>
          <input type="radio" id="rating-4" name="rating" value="4" <?php if('4'==$call['rating']) echo "checked" ?> /><label for="rating-4">4</label>
          <input type="radio" id="rating-3" name="rating" value="3" <?php if('3'==$call['rating']) echo "checked" ?> /><label for="rating-3">3</label>
          <input type="radio" id="rating-2" name="rating" value="2" <?php if('2'==$call['rating']) echo "checked" ?> /><label for="rating-2">2</label>
          <input type="radio" id="rating-1" name="rating" value="1" <?php if('1'==$call['rating']) echo "checked" ?> /><label for="rating-1">1</label>
          <input type="radio" id="rating-0" name="rating" value="0" <?php if('0'==$call['rating']) echo "checked" ?> class="star-cb-clear" /><label for="rating-0">0</label>
        </span>
    </fieldset>
</div>

<div class="form-group"><label><strong>Disposition</strong></label>
<select name="disposition" class="form-control design">
<option value="">Select Disposition</option>
<?
if ($_SESSION['storeid'] > 0)
{
$sql = "select disposition from advtrack.client where client='".$_SESSION['client']."-".$_SESSION['storeid']."' and disposition is not null limit 1";
}else{
  $sql = "select disposition from advtrack.client where client='".$_SESSION['client']."' and disposition is not null limit 1";
}
$d = $db->rawQueryOne($sql);
//if ($db->count > 0)
$vals=explode(",",$d['disposition']);

foreach($vals as $val){
?>
<option value="<?=$val?>" <? if($val==$call['disposition']) echo "selected" ?>><?=$val?></option>
<? }?>
</select>
</div>

<!--<div class="form-group"><label><strong>Sale Value</strong></label><br>
<input type="text" name="callvalue" class="form-control" value="<?=$call['callvalue']?>" placeholder="$0.00" />
</div>-->

<div class="form-group"><label><strong>Comments</strong></label>
<textarea name="comment" class="form-control"><?=$call['comment']?></textarea>
</div>

<input name="callid" type="hidden" value="<?=$_POST['callid']?>" />
<? if($_POST['url']){ ?>
	<input name="header" type="hidden" value="<?=$_POST['url']?>" />
<? }?>

<script type="text/javascript">
var logID = 'log',
  log = $('<div id="'+logID+'"></div>');
$('body').append(log);
  $('[type*="radio"]').change(function () {
    var me = $(this);
    log.html(me.attr('value'));
  });
</script>