<?
include ($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include ($_SERVER['DOCUMENT_ROOT']."/includes/functions.php");
session_start();

$sql = "select a.*,group_concat(storelist) as storelist from advtrack.campaign_call_log a,advtrack.campaign_call_log_store b where a.id=b.id and a.id='".$_POST['id']."' and a.id=b.id group by a.id";
$row = $db->rawQueryOne($sql);
?>
<div class="form-group">
  <label><strong>Campaign</strong></label>
    <input name="campaign" class="form-control" type="text" value="<?=$row['campaign']?>" />
</div>
<div class="form-group">
  <label><strong>DAS Caller</strong></label>
  <select name="caller" class="form-control design" required>
  <?php $callers=['Brianna',"Christina","Dean","Jimmy","Katrina","Lisa","Mirian","Ralph"];
    foreach($callers as $caller){
    ?>
    <option value="<?=$caller?>" <? if($caller==$row['caller']) echo "selected" ?>><?=$caller?></option>
    <? } ?>
  </select>
</div>
<div class="form-group">
  <label><strong>Call Date</strong></label>
    <input name="calldate" class="form-control datepicker" type="text" value="<?=$row['calldate']?>" required />
</div>
<div class="form-group">
    <label><strong>Store ID</strong></label>
    <select name="storeid[]" class="form-control" multiple required>
      <option value="0" <?php echo ($store['storeid'] == $storelist) ? "selected" : ''; ?>>Corporate</option>
		<?
		$sql_stores = "SELECT storeid,companyname FROM ".$_SESSION['database'].".locationlist order by companyname";
		$stores = $db->rawQuery($sql_stores);
		
		$storelist = explode(",",$row['storelist']);

		if($db->count > 0){
			foreach($stores as $store){
			?>
				<option value="<?=$store['storeid']?>" <? if (in_array($store['storeid'], $storelist)) echo "selected" ?>><?=$store['companyname'].' ('.$store['storeid'].')'?></option>
			<?
			}
		}
		?>
    </select>
</div>
<div class="form-group">
  <label><strong>Call Type</strong></label>
  <select name="calltype" class="form-control design" required>
  <? $types=["Answered","VoiceMail","Re-Schedule","CallBack","Webinar","Email"];
    foreach($types as $type){
    ?>
    <option value="<?=$type?>" <? if($type==$row['calltype']) echo "selected" ?>><?=$type?></option>
    <? } ?>
  </select>
</div>
<div class="form-group">
  <label><strong>Call Length (min)</strong></label>
    <input name="duration" class="form-control" type="text" value="<?=$row['duration']?>" required />
</div>
<div class="form-group">
  <label><strong>Reason</strong></label>
  <select name="reason" class="form-control design" required>
  <? $reasons=["Intro Call","High Performer","Low Performer","Inbound","Webinar","Misc","Non-Participant"];
    foreach($reasons as $reason){ ?>
    <option value="<?=$reason?>" <? if($reason==$row['reason']) echo "selected" ?>><?=$reason?></option>
    <? } ?>
  </select>
</div>
<div class="form-group">
  <label><strong>Notes</strong></label>
    <textarea class="form-control" name="notes"><?=$row['notes']?></textarea>
</div>
<div class="form-group">
  <label><strong>Action</strong></label>
    <textarea class="form-control" name="action"><?=$row['action']?></textarea>
</div>

<input name="id" type="hidden" value="<?=$row['id']?>" />