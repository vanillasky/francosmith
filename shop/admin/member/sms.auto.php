<?

$location = "SMS���� > SMS �ڵ��߼�/����";
include "../_header.php";
include dirname(__FILE__)."/../../lib/callNumber.class.php";

$r_deny = array(
		'join'			=> '000',
		'id_pass'		=> '011',
		'order'			=> '000',
		'incash'		=> '000',
		'account'		=> '011',
		'delivery'		=> '011',
		'dcode'			=> '011',
		'cancel'		=> '011',
		'repay'			=> '011',
		'runout'		=> '100',
		'birth'			=> '011',
		'qna'			=> '011',
		'member'		=> '011',
		'dormant'		=> '011',
		);

$r_code = array(
		'join'			=> 'ȸ�����Խ� �߼�',
		'id_pass'		=> '��й�ȣã��� �߼�',
		'order'			=> '�ֹ������� �߼� <font class="small1">(�������ֹ��� �ش�, ī������ֹ��� �߼��� �ȵ˴ϴ�)',
		'incash'		=> '�Ա�Ȯ�ν� �߼� <font class="small1">(�������Ա�Ȯ��, ī��������ν� �߼۵˴ϴ�)',
		'account'		=> '�Աݿ�û �߼� <font class="small1">(�������ֹ��� �ش�, �ֹ������� �߼۵˴ϴ�)',
		'delivery'		=> '��ǰ��۽� �߼� <font class="small1">(����� ���·� �ٲ���� �� �߼۵˴ϴ�)',
		'dcode'			=> '�����ȣ �߼� <font class="small1">(����� ���·� �ٲ���� �� �߼۵˴ϴ�)',
		'cancel'		=> '�ֹ���ҽ� �߼� <font class="small1">(�ֹ���� ���·� �ٲ���� �� �߼۵˴ϴ�)',
		'repay'			=> 'ȯ�ҿϷ�� �߼�',
		'runout'		=> '��ǰǰ���� �߼� <font class="small1">(�ֹ��� ��ǰ�� ǰ���Ǿ����� �����ڿ��� �߼۵˴ϴ�)',
		'birth'			=> '����ȸ�� �ڵ��߼� <font class="small1">(���� �����ڰ� �ִ°�� �����ڸ��ο��� Ȯ��)',
		'qna'			=> '1:1���� �亯��Ͻ� �߼�'
		);

$smsRecall	= explode("-",$cfg['smsRecall']);
$smsAdmin	= explode("-",$cfg['smsAdmin']);

# �߰������� ����
$smsAddAdminArr	= explode("|",$cfg['smsAddAdmin']);
$smsAddAdmin[0]	= explode("-",$smsAddAdminArr[0]);

if(!$cfg['smsPass'])$cfg['smsPass']="1111";
if(!$cfg['smsAutoSendType'])$cfg['smsAutoSendType']="LIMIT";
$checked = array(
    'smsAutoSendType' => array($cfg['smsAutoSendType'] => ' checked="checked"'),
);

$info_cfg = $config->load('member_info');

$callNumber = new callNumber;
$callbackData = $callNumber->getCallNumberData('callback');
?>
<script type="text/javascript" src="../godo_ui.js?ts=<?=date('Ym')?>"></script>
<script Language=javascript>
/*** �߰������� ***/
function addfld(obj)
{
	var tb = document.getElementById(obj);
	oTr = tb.insertRow();
	oTd = oTr.insertCell();
	oTd.innerHTML = "<span>" + tb.rows[0].cells[0].getElementsByTagName('span')[0].innerHTML + "</span> <a href='javascript:void(0);' onClick='delfld(this)'><img src='../img/i_del.gif' align='absmiddle' /></a>";
	oTd = oTr.insertCell();
	oTd = oTr.insertCell();
}

function delfld(obj)
{
	var tb = obj.parentNode.parentNode.parentNode.parentNode;
	tb.deleteRow(obj.parentNode.parentNode.rowIndex);
}
</script>
<form method="post" action="indb.php">
<input type="hidden" name="mode" value="sms_auto" />

<div class="title title_top"><font face="����" color="black"><b>SMS</b></font> ���������� �Է�<span>SMS ������������ �Է��ϼ���</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=member&no=8')"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2" /></a></div>

<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>SMS ��й�ȣ ����</td>
	<td><input type="password" name="smsPass" value="<?=$cfg['smsPass']?>" maxlength="4" onkeydown="onlynumber();"  class="line"/>
	<font class="extext"><a href="https://www.godo.co.kr/mygodo/login.php?returnURL=<?=$returnURL?>" target="_blank"><font class=extext_l>[���̰� > ���� ���θ�]</font></a> ���� ��й�ȣ�� ���� ����ϰ�, ������ ��й�ȣ�� �Է��ϼ���</font></td>
</tr>
<tr>
	<?
	$tooltipMsg = "
		<span class='red'>�ڵ��߼�/������ ����� �߽Ź�ȣ�� �Ʒ��� ���� �߼� �� �߽Ź�ȣ�� ���˴ϴ�.</span>
		<ul style='margin:0;padding-left:20px;'>
			<li style='list-style:disc'>ȸ������ Ȯ�� ����</li>
			<li style='list-style:disc'>��й�ȣ ã�� Ȯ�� ����</li>
			<li style='list-style:disc'>������ �ֹ� Ȯ�� ����</li>
			<li style='list-style:disc'>ī�����/������ �Ա� Ȯ�� ����</li>
			<li style='list-style:disc'>������ �ֹ� �Ա� ��û ����</li>
			<li style='list-style:disc'>��ǰ ����� �ȳ� ����</li>
			<li style='list-style:disc'>���� ��ȣ Ȯ�� ����</li>
			<li style='list-style:disc'>�ֹ���� Ȯ�� ����</li>
			<li style='list-style:disc'>ȯ�ҿϷ� Ȯ�� ����</li>
			<li style='list-style:disc'>��ǰ ǰ���� �ȳ� ����</li>
			<li style='list-style:disc'>����ȸ�� ���� ����</li>
			<li style='list-style:disc'>1:1���� �亯 �Ϸ� ����</li>
			<li style='list-style:disc'>��ٱ��� �˸�����</li>
		</ul>
	";
	?>
	<td>�߽Ź�ȣ <img src="../img/btn_question.gif" style="vertical-align:middle;cursor:pointer;" class="godo-tooltip" tooltip="<?echo $tooltipMsg?>"></td>
	<td>
		<input type="text" name="smsRecall" value="<?=str_replace("-","",$cfg[smsRecall])?>" size="12"  class="line" readonly="readonly" />
		<a onclick="popup_return('../member/popup.callNumber.php?target=smsRecall&changeColor=Y','callNumber',450,250,0,0,'yes')" class="hand"><img src="../img/call_number_btn.gif" align="absmiddle"></a>
		<span id="smsRecallText" class="red"></span>
	</td>
</tr>
<tr>
	<td>������ �ڵ���</td>
	<td>
	<input type="text" name="smsAdmin[]" size="4" maxlength="3" value="<?=$smsAdmin[0]?>" onkeydown="onlynumber();"  class="line"/> -
	<input type="text" name="smsAdmin[]" size="4" maxlength="4" value="<?=$smsAdmin[1]?>" onkeydown="onlynumber();"  class="line"/> -
	<input type="text" name="smsAdmin[]" size="4" maxlength="4" value="<?=$smsAdmin[2]?>" onkeydown="onlynumber();"  class="line"/>
	<font class="extext">�����ڿ��Ե� �޼����� �뺸�� �� �ʿ��� ��ȭ��ȣ (������ �ڵ��� ��ȣ)</td>
</tr>
<tr>
	<td>�߰� ������</td>
	<td>

	<table id="addadminField" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse">
	<tr>
		<td>
		<span>
		<input type="text" name="smsAddAdmin1[]" size="4" maxlength="3" value="<?=$smsAddAdmin[0][0]?>" onkeydown="onlynumber();" class="line" /> -
		<input type="text" name="smsAddAdmin2[]" size="4" maxlength="4" value="<?=$smsAddAdmin[0][1]?>" onkeydown="onlynumber();" class="line" /> -
		<input type="text" name="smsAddAdmin3[]" size="4" maxlength="4" value="<?=$smsAddAdmin[0][2]?>" onkeydown="onlynumber();" class="line" />
		</span>
				<a href="javascript:addfld('addadminField');"><img src="../img/i_add.gif" align="absmiddle" /></a>
		<font class="extext">������ �̿��� �߰��� �޾ƾ� �� ����ڰ� ������ �ʿ��� ��ȭ��ȣ</td>

		</td>
	</tr>
	</table>
<?
	for($i = 1; $i < sizeof($smsAddAdminArr); $i++){
		$smsAddAdmin[$i]	= explode("-",$smsAddAdminArr[$i]);
?>
	<table id="addadminField<?=$i?>" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse">
	<tr>
		<td>
		<a href="javascript:void(0);" onClick="delfld(this)"><img src="../img/i_del.gif" align="absmiddle" /></a>
		<span>
		<input type="text" name="smsAddAdmin1[]" size="4" maxlength="3" value="<?=$smsAddAdmin[$i][0]?>" onkeydown="onlynumber();" /> -
		<input type="text" name="smsAddAdmin2[]" size="4" maxlength="4" value="<?=$smsAddAdmin[$i][1]?>" onkeydown="onlynumber();" /> -
		<input type="text" name="smsAddAdmin3[]" size="4" maxlength="4" value="<?=$smsAddAdmin[$i][2]?>" onkeydown="onlynumber();" />
		<font class="extext">�������̿� �߰��� �޾ƾ� �� ����ڰ� ������ �ʿ��� ��ȭ��ȣ</td>
		</span>
		</td>
	</tr>
	</table>
<?
	}
?>
	</td>
</tr>
<tr>
	<td>90Byte �ʰ���<br>�޽��� ���� ���</td>
	<td>
		<input type="radio" name="smsAutoSendType" value="LIMIT" <?php echo $checked['smsAutoSendType']['LIMIT']; ?> />90Byte ������ SMS �߼�
		<input type="radio" name="smsAutoSendType" value="MULTI" <?php echo $checked['smsAutoSendType']['MULTI']; ?> />���� SMS �߼�<br>
		<font class="extext"><?=$lmsPatchText?>�ڵ��߼� ������ ���θ���, �ֹ���ȣ ������ ���Ͽ� 90Byte�� �ʰ��� ����� �޽��� ���� ��� �Դϴ�.<br>��90Byte ������ SMS �߼ۡ����� ������ ��� 90Byte �ʰ��� �޽����� ©�� �� �ֽ��ϴ�.</td>
</tr>
</table>


<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />�߽Ź�ȣ�� ������ �޼����� �߼۽� �߽Ź�ȣ�� ������ ��ȭ��ȣ�Դϴ�. ������ȭ��ȣ �Ǵ� �ڵ�����ȣ�� �Է��ϼ���</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />�������ڵ����� �Ʒ� �ڵ��߼۱�ɿ��� �����ڰ� �޼����� �ް��� �� �� �ʿ��մϴ�</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />�߰������ڴ� �������̿� �߰��� �޾ƾ� �� ����ڰ� ������ ����� �ϽǼ� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />SMS ����Ʈ�� �����Ǿ� �־�� �߼��� �����մϴ�. <a href="sms.pay.php"><font color=white><u>[SMS ����Ʈ �����ϱ�]</u></font></a> ���� �����ϼ���</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />������ �߼� �ý��ۻ� ������ �ƴ� ��Ż� ������å �� ��Ÿ ������ ���� ���ڹ߼� ���п� ���� ������ å���� ������, �� ��Ż翡 ����Ȯ���� ���θ��� �����մϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />�߽Ź�ȣ�� ���� ��ϵ��� ������ SMS�� �߼۵��� �ʽ��ϴ�. <a href="http://www.godo.co.kr/news/notice_view.php?board_idx=1247&page=2
" target="_blank"><font color=white><u>�߽Ź�ȣ ��������� �ȳ�</u></font></a></td></tr>

</table>
</div>
<script>cssRound('MSG01');</script>

<div style="padding-top:20px"></div>

<div class="title title_top"><font face="����" color="black"><b>SMS</b></font> �ڵ��߼�/�������� <span>�Ʒ� ������ üũ�Ͻø� �޼����� �ڵ��߼۵˴ϴ�. ������ ������ �� ��Ϲ�ư�� ��������.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=member&no=8')"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2" /></a></div>

<table width="800">
<tr>
	<?
	$idx=0;
	foreach ($r_code as $k=>$v){
		unset($checked);
		unset($sms_auto);
		@include "../../conf/sms/$k.php";
		if ($sms_auto['send_c']) $checked['send_c'] = "checked";
		if ($sms_auto['send_a']) $checked['send_a'] = "checked";
		if ($sms_auto['send_m']) $checked['send_m'] = "checked";

		$deny['member']	= substr($r_deny[$k],0,1);
		$deny['admin']	= substr($r_deny[$k],1,1);
		$deny['madmin']	= substr($r_deny[$k],2,1);

		$disabled['member']	= ($deny['member']) ? "disabled" : "";
		$disabled['admin']	= ($deny['admin']) ? "disabled" : "";
		$disabled['madmin']	= ($deny['madmin']) ? "disabled" : "";

		if ($k == 'id_pass' && isset($info_cfg['finder_use_mobile'])) {
			$disabled['member']	= "disabled";
			$disabled['admin']	= "disabled";
			$disabled['madmin']	= "disabled";
			$checked['send_c'] = "";
			$checked['send_a'] = "";
			$checked['send_m'] = "";
		}
		$receiveRefuseMessage = '';
		if($k == 'birth'){
			$receiveRefuseMessage = '<div style="color: red; margin-top: 2px;">*���Űźΰ� ����</div>';
		}
	?>
	<td>

	<table border="1" bordercolor="#cccccc" style="border-collapse:collapse;">
	<tr>
		<td colspan="2" class="noline" width="350" height="25">&nbsp;&nbsp;<font color="#0074ba"><b><?=$v?></b></font></td>
	</tr>
<?php
	if (in_array($k, $r_sendDateCode['sms'])) {
		// �⺻�� ó��
		if (empty($sms_auto['sendDate'])) {
			$sms_auto['sendDate']	= $r_sendDateDefault['sms'];
		}
?>
	<tr>
		<td colspan="2" class="noline" width="350" height="25">
			&nbsp;&nbsp;<font color="#0074ba">�߼۴�� : �ֱ�</font>
			<select name="auto[<?php echo $k;?>]['sendDate']">
				<?php foreach ($r_sendDatePeriod['sms'] as $dayVal) {?>
				<option value="<?php echo $dayVal;?>" <?php if ($sms_auto['sendDate'] == $dayVal) echo 'selected="selected"';?>><?php echo $dayVal;?>��</option>
				<?php }?>
			</select>
			<font color="#0074ba">�ֹ��Ǹ�</font>
		</td>
	</tr>
<?php
	}
?>
	
	<?php if($k === 'dormant'){ ?>
	<tr>
		<td colspan="2" class="noline" width="350" height="25">
			<input type="checkbox" name="auto[<?=$k?>]['sendBeforeDay_30']" value='y' <?php if($sms_auto['sendBeforeDay_30'] == 'y') echo 'checked="checked"'; ?> /> <span style="color: #0074ba;">�߼� ��� : �޸�ȸ�� ��ȯ �Ѵ� �� �߼�</span>
		</td>
	</tr>
	<tr>
		<td colspan="2" class="noline" width="350" height="25">
			<input type="checkbox" name="auto[<?=$k?>]['sendBeforeDay_7']" value='y' <?php if($sms_auto['sendBeforeDay_7'] == 'y') echo 'checked="checked"'; ?> /> <span style="color: #0074ba;">�߼� ��� : �޸�ȸ�� ��ȯ ������ �� �߼�</span>
		</td>
	</tr>
	<?php } ?>
	<tr>
		<td align="center" style="padding-bottom:5px" valign="top">

		<? if (!$deny['member']){ ?>
		<table cellpadding="0" cellspacing="0">
		<tr><td><img src="../img/sms_top.gif" /></td></tr>
		<tr>
			<td background="../img/sms_bg.gif" align="center" height="81" align="center">
			<textarea name="auto[<?=$k?>]['msg_c']" cols="16" rows="5" style="font:9pt ����ü;overflow:hidden;border:0;background-color:transparent;" onkeydown="return chkLength(this)"><?=$sms_auto['msg_c']?></textarea>
			</td>
		</tr>
		<tr><td><img src="../img/sms_bottom.gif" /></td></tr>
		<tr><td height=3></td></tr>
		</table>
		<? } else {?>
		<img src="../img/sms_only_admin.gif" />
		<? } ?>
		<div><input type="checkbox" name="auto[<?=$k?>]['send_c']" <?=$checked['send_c']?> <?=$disabled['member']?> class="null" />������ �ڵ��߼�</div>
		<?php echo $receiveRefuseMessage; ?>
		</td>
		<td align="center" style="padding-bottom:5px" valign="top">

		<? if (!$deny['admin']){ ?>
		<table cellpadding="0" cellspacing="0">
		<tr><td><img src="../img/sms_top.gif" /></td></tr>
		<tr>
			<td background="../img/sms_bg.gif" align="center" height="81" align="center">
			<textarea name="auto[<?=$k?>]['msg_a']" cols="16" rows="5" style="font:9pt ����ü;overflow:hidden;border:0;background-color:transparent;" onkeydown="return chkLength(this)"><?=$sms_auto['msg_a']?></textarea>
			</td>
		</tr>
		<tr><td><img src="../img/sms_bottom.gif" /></td></tr>
		<tr><td height=3></td></tr>
		</table>
		<? } else {?>
		<img src="../img/sms_only_user.gif" />
		<? } ?>
		<div style="text-align:left;padding-left:13px;"><input type="checkbox" name="auto[<?=$k?>]['send_a']" <?=$checked['send_a']?> <?=$disabled['admin']?> class="null" />�����ڿ��Ե� �߼�</div>
		<div style="text-align:left;padding-left:13px;"><input type="checkbox" name="auto[<?=$k?>]['send_m']" <?=$checked['send_m']?> <?=$disabled['madmin']?> class="null" />�߰������ڿ��Ե� �߼�</div>
		</td>
	</tr>
	</table>

	</td>
	<? if ($idx++%2){ ?></tr><tr><? } ?>
	<? } ?>
</tr>
</table>

<div class="button">
<table width="800" border="0" align="left">
<tr><td width="343" align="right"><input type="image" src="../img/btn_register.gif" /></td>
<td width="5"></td>
<td width="452" align="left"><a href="javascript:history.back();"><img src="../img/btn_cancel.gif" /></a></td>
</tr></table>
</div>

</form>
<script type="text/javascript">
smsRecallColor('smsRecall','<?echo str_replace("-","",$cfg[smsRecall])?>','<?echo @implode($callbackData, ",")?>');
</script>

<? include "../_footer.php"; ?>