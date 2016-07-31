<style type="text/css">
.sms_layout	{ width: 146px; }
.sms_layout .sms_top { width: 146px; height: 56px; background:url('../img/sms_top.gif') no-repeat top left; text-align: right; }
.sms_layout .td_sms_msg { background: url('../img/sms_bg.gif') repeat-y; padding-top: 8px; width: 146px; height: 81px; text-align: center; }
.sms_layout .area_sms_msg { font: 9pt ±¼¸²Ã¼; overflow: hidden; border: 0px; width: 98px; height: 74px; background: url('../img/ong_message02_none.gif') repeat-y; }
.sms_layout .textLine { height: 31px; background: url('../img/sms_bottom.gif'); text-align: center; }
.sms_layout .textByte { color: #262626; }

.lms_layout	{ width: 146px; }
.lms_layout .lms_top { width: 146px; height: 56px; background:url('../img/lms_top.gif') no-repeat top left; text-align: right; }
.lms_layout .lms_subject { font: 9pt ±¼¸²Ã¼; overflow: hidden; border: 0px; width: 98px; height: 31px; background: url('../img/long_message01_none.gif') repeat-y; }
.lms_layout .td_lms_subject { background:url('../img/sms_subject_bg.gif') repeat-y; width:146px; height:38px; text-align:center; }
.lms_layout .td_lms_msg { background: url('../img/sms_long_bg.gif') repeat-y; padding-top: 8px; width: 146px; height: 170px; text-align: center; }
.lms_layout .area_lms_msg { font: 9pt ±¼¸²Ã¼; overflow: hidden; border: 0px; width: 98px; height: 150px; background: url('../img/long_message01_none.gif') repeat-y; }
.lms_layout .textLine { height: 31px; background: url('../img/sms_bottom.gif'); text-align: center; }
.lms_layout .textByte { color: #262626; }
</style>

<?php if($smsLog['sms_type'] == 'lms'){ ?>
<table cellpadding="0" cellspacing="0" border="0" class="lms_layout">
<tr>
	<td class="lms_top"></td>
</tr>
<tr class="tr_lms_subject">
	<td class="td_lms_subject"><textarea name="lms_subject" class="lms_subject" readonly="readonly"><?php echo $smsLog['subject']; ?></textarea></td>
</tr>
<tr>
	<td class="td_lms_msg"><textarea name="lms_msg" id="lms_msg" class="area_lms_msg" readonly="readonly"><?php echo $smsLog['msg']; ?></textarea></td>
</tr>
<tr>
	<td class="textLine"><span class="ver8 textByte"><span id="byteLength"></span>/2000 Bytes</span></td>
</tr>
</table>
<?php } else { ?>
<table cellpadding="0" cellspacing="0" border="0" class="sms_layout">
<tr>
	<td class="sms_top"></td>
</tr>
<tr>
	<td class="td_sms_msg"><textarea name="sms_msg" id="sms_msg" class="area_sms_msg" readonly="readonly"><?php echo $smsLog['msg']; ?></textarea></td>
</tr>
<tr>
	<td class="textLine"><span class="ver8 textByte"><span id="byteLength"></span>/90 Bytes</span></td>
</tr>
</table>
<?php } ?>
<script language="javascript" type="text/javascript">
if(document.getElementById("sms_msg")){
	var msgEl = document.getElementById("sms_msg");
}
else if(document.getElementById("lms_msg")){
	var msgEl = document.getElementById("lms_msg");
}

if(msgEl){
	document.getElementById("byteLength").innerHTML = chkByte(msgEl.value);
}
</script>