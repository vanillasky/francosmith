<?

include "../_header.popup.php";

$type = ( $_POST[type] ) ? $_POST[type] : $_GET['type'];

@include "../../conf/mail.cfg.php";

// ���ŵ���
$mailBaseAgree = array(
	'agreeFlag' => 'Y',
	'agreeMsg' => '�� ������ YYYY�� MM�� DD�� ����, ȸ������ ���ŵ��Ǹ� Ȯ���� ��� ȸ���Բ��� ���ŵ��Ǹ� �ϼ̱⿡ �߼۵Ǿ����ϴ�.'
);
if ($mailCfg['agreeFlag'] == '' && $mailCfg['agreeMsg'] == '') {
	$mailCfg['agreeFlag'] = $mailBaseAgree['agreeFlag'];
	$mailCfg['agreeMsg'] = $mailBaseAgree['agreeMsg'];
}
$checked['agreeFlag'][$mailCfg['agreeFlag']] = "checked";
$mailCfg['agreeMsg'] = stripslashes($mailCfg['agreeMsg']);

$where = '';

// �߼� ���
if ($type == 'select') { // ������ ȸ������ �߼�(����/��ü ���Ϻ�����)
	if($_POST['receiveRefuseType'] == 'N'){
		$where = " AND mailling = 'y' ";
	}
	$query = "select * from ".GD_MEMBER." where m_no in (".(is_array($_POST[chk]) ? implode(",",$_POST[chk]) : "''").")" . $where;
}
else if ($type == 'query') { // �˻�����Ʈ�� �ִ� ��� ȸ������ �߼�(����/��ü ���Ϻ�����)
	$query = stripslashes($_POST[query]);
	if($_POST['receiveRefuseType'] == 'N'){
		$where = " mailling = 'y' ";
		$query = preg_replace('/(order)(\s|\w|\D|\d)+/i', '', $query);
		if(preg_match('/where/i', $query)){
			$whereType = ' and ';
		}
		else {
			$whereType = ' WHERE ';
		}
		$query = $query . $whereType . $where;
	}
}
else if ($type == 'direct') { // Ư���ο��� �߼�
	if ($_GET['m_id'] != '') { // Ư��ȸ��
		$query = "select * from ".GD_MEMBER." where m_id='$_GET[m_id]'";
	}
	else if ($_GET['email'] != '') { // Ư������
		$toEmail = $_GET['email'];
		$total = 1;
	}
	else { // �����Է�
		$toEmail = '';
		$total = 1;
	}
}

if ($query){
	$s = strpos($query,"from");
	$e = strpos($query,"order by");
	if (!$e) $e = strlen($query);

	// ���Űź� ����� �ο� üũ
	$denyNum = 0;
	list ($denyNum) = $db->fetch("select count(*) ".substr($query,$s,$e-$s)." and mailling = 'n'");

	// ���Ŵ�� ���ο�
	$total = 0;
	list ($total) = $db->fetch("select count(*) ".substr($query,$s,$e-$s));
}

?>
<? if ($_GET[m_id] == '' && $_GET[email] == ''){ ?>
<body style="margin:0" scroll=no>
<? } ?>

<!-------------- ���� ���� ���� ���� ���� ---------------------->
<form name="sForm" method="post" action="indb.php" target=ifrmHidden>
<input type="hidden" name="mode" value="setEmailAgree"/>
<input type="hidden" name="set[agreeFlag]"/>
<input type="hidden" name="set[agreeMsg]"/>
</form>
<script type="text/javascript">
function saveAgree() {
	var sFobj = document.sForm;
	var agreeFlag = document.getElementsByName('agreeFlag');
	var agreeMsg = document.getElementsByName('agreeMsg');
	sFobj['set[agreeFlag]'].value = (agreeFlag[0].checked ? 'Y' : 'N');
	sFobj['set[agreeMsg]'].value = agreeMsg[0].value;
	sFobj.submit();
}
</script>
<!-------------- ���� ���� ���� ���� �� ------------------------>

<!-------------- �⺻������ ���� ---------------------->
<form>
<input type="hidden" name="base[agreeFlag]" value="<?=htmlspecialchars($mailBaseAgree['agreeFlag'])?>"/>
<input type="hidden" name="base[agreeMsg]" value="<?=htmlspecialchars($mailBaseAgree['agreeMsg'])?>"/>
</form>
<script type="text/javascript">
function putBaseAgree() {
	var agreeFlag = document.getElementsByName('agreeFlag');
	var agreeMsg = document.getElementsByName('agreeMsg');
	var baseAgreeFlag = document.getElementsByName('base[agreeFlag]');
	var baseAgreeMsg = document.getElementsByName('base[agreeMsg]');
	agreeFlag[0].checked = (baseAgreeFlag[0].value == 'Y' ? true : false);
	agreeMsg[0].value = baseAgreeMsg[0].value;
}
</script>
<!-------------- �⺻������ �� ------------------------>

<!-------------- ��üũ ���� ---------------------->
<script type="text/javascript">
function chkForm2(obj)
{
	if (!chkForm(obj)) return false;

	if(typeof(obj.agreeFlag) != 'undefined' && obj.agreeFlag.checked == true) {
		var baseAgreeMsg = document.getElementsByName('base[agreeMsg]');
		if( obj.agreeMsg.value == baseAgreeMsg[0].value) {
			alert("��¥������ ���� �ʽ��ϴ�. �ٽ� �Է����ּ���. \n��) 2013�� 11�� 18��");
			obj.agreeMsg.focus();
			return false;
		}
	}

	return true;
}
</script>
<!-------------- ��üũ �� ------------------------>

<form method=post action="iframe.email.php" onsubmit="return chkForm2(this)" target=boxEmail>
<input type=hidden name=mode value="sendmail">
<input type=hidden name=query value="<?=$query?>">
<input type=hidden name=total value="<?=$total?>">

<div class="title title_top">���Ϻ�����<span>ȸ���鿡�� ������ �����մϴ�</span></div>

<table width=100%>
<col class=cellC><col class=cellL>
<tr>
	<td>���ۿϷ� ����Ʈ<br><font class=small color=6d6d6d>���������� ���۵Ǹ�<br>�̰��� ���ϸ���Ʈ��<br>�������ϴ�</font></td>
	<td>

	<table width=100% cellpadding=0 cellspacing=0>
	<tr>
		<td style="padding-right:3px"><iframe name=boxEmail style="width:100%;height:100px" style="border:1 solid #cccccc" frameborder=0></iframe></td>
		<td width=100><input type=submit style="width:100%;height:104px;background:#4A3F38;color:#ffffff" value="���Ϲ߼��ϱ�"></td>
	</tr>
	<tr>
		<td style="padding-top:3px" colspan=2>
		<div style="height:8px;font:0;background:#f7f7f7;border:1 solid #cccccc">
		<div id=progressBar style="height:8px;background:#ff0000;width:0"></div>
		</div>
		</td>
	</tr>
	</table>

	</td>
</tr>
<? if ($type == 'direct' && $query == ''){ ?>
<tr>
	<td>�޴º� �̸���</td>
	<td><input type=text name=toEmail value="<?=$toEmail?>" class=lline required></td>
</tr>
<? } else { ?>
<tr>
	<td>���Ŵ��</td>
	<td><b><?=number_format($total)?>��</b> <? if ($denyNum) echo '<span style="color:#FF0066"><b>(�ؼ��Űź� ����ڰ� ���ԵǾ� �ֽ��ϴ�.)</b></span>'; ?></td>
</tr>
<? } ?>
<tr>
	<td>����</td>
	<td><input type=text name=subject style="width:100%" required class=lline></td>
</tr>
<tr>
	<td>����</td>
	<td>
	<textarea name=body style="width:100%;height:480px" type=editor class=tline><?=$tmp?></textarea>
	</td>
</tr>
</table>

<? if ($query != ''){ ?>
<table width="100%">
<col class=cellC><col class=cellL><col width="120">
<tr>
	<td class="noline">���ŵ��ǹ��� <input type="checkbox" name="agreeFlag" value="Y" <?=$checked['agreeFlag']['Y']?> /></td>
	<td><textarea name="agreeMsg" style="width:100%; height: 60px;" class=lline><?=$mailCfg['agreeMsg']?></textarea></td>
	<td>
		<div style="line-height:16px;">
		<a href="javascript:saveAgree();" class="extext" style="font-weight:bold;">[���� ���� ���� ����]</a><br/>
		<a href="javascript:putBaseAgree();" class="extext" style="font-weight:bold;">[�⺻������]</a>
		</div>
	</td>
</tr>
<tr>
	<td class="noline">���Űź� <input type="checkbox" name="denyFlag" value="Y" checked /></td>
	<td>
		<div style="padding:5px; background-color:rgb(250, 250, 250); border:solid 1px #cccccc;">
			�� [���ϳ���]�� ��ü ���Űźα���� ������ ���, üũ������ �ϸ� �˴ϴ�.<br/><br/>
			- �̸����� ������ �� �̻� ������ �����ø� [���Űź�]�� Ŭ���� �ּ���.<br/>
			- If you don��t want to receive this mail, click here.
		</div>
	</td>
	<td></td>
</tr>
</table>

<table border="4" bordercolor="#dce1e1" style="border-collapse:collapse" width="100%">
<tr><td style="padding:7px 0px 10px 10px">
<div style="padding-top:5px; color:#666666; font-weight:bold;" class="g9"><b>�� ���� ���� ���Űź� ����ȭ ��å���� ���� ���� ���� �ȳ� <a href="javascript:popupLayer('../member/popup.email_deny_law.php',550,230);" class="extext"><b>[��������Ȯ��]</b></a></b></div>
<div style="padding-top:10px; color:#666666;" class="g9">�ֿ� ���������� ������ ��ǰ�� Ȩ���� ��������, �̺�Ʈ �� ���� ������ �߼��ϰ� �� ���
	<b><u>���� ���ſ� ������ ȸ���� ���ؼ��� �߼�</u></b>�ؾ� �ϸ�, �� �� ������ ���� ȸ������ <b><u>�����ŵ��ǡ��� ���� ������ �ս��� �����ŰźΡ��� �� �� �ֵ��� ����� ����</u></b>�ؾ� �մϴ�.
</div>
<div style="padding-top:10px; color:#666666;" class="g9">- <b>���ŵ��ǹ���</b> : ���ŵ��Ǹ� �� �ñ�� ���뿡 ���� ��ü������ ����Ͽ��� �մϴ�.</div>
<div style="padding-top:3px; color:#666666;" class="g9">- <b>���Űźα��</b> : ���Űź� ����� ��ü������ �������� ���� ��쿡 ����� �� �ֽ��ϴ�.</div>
<div style="padding-top:3px; color:#666666;" class="g9">- <b>���ŰźοϷ� �ȳ� ���� �߼�</b> : ������ ������ ȸ���� �����ŰźΡ��� �� ���, ���Űź� ó�����뿡 ���� �ȳ������� �߼��ؾ� �մϴ�.
	�̿� ���� ������ <a href="../member/email.cfg.php?mode=30" class="extext" target="_blank"><b>[�ڵ����ϼ��� > ���ŰźοϷ� �ȳ�����]</b></a>���� �Ͻ� �� �ֽ��ϴ�.
</div>
</td></tr>
</table>
<? } ?>

</form>

<script src="../../lib/meditor/mini_editor.js"></script>
<script>mini_editor('../../lib/meditor/');</script>