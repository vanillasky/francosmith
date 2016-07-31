<?php
include '../_header.popup.php';
include '../../lib/page.class.php';
include '../../lib/sms.class.php';
include '../../lib/sms_sendlist.class.php';
include '../../lib/smsAPI.class.php';
$sms = new sms();
$sms_sendlist = new sms_sendlist();
$smsAPI = new smsAPI();

if(!$_GET['sms_logNo']){
	msg('�ùٸ� ������ �ƴմϴ�.', 'close');
	exit;
}
$db_table = GD_SMS_SENDLIST . ' AS a LEFT JOIN ' . GD_MEMBER . ' AS b ON a.sms_memNo=b.m_no';
$headInfo = array();
if(!$_GET['page'])		$_GET['page'] = 1;
if(!$_GET['page_num'])	$_GET['page_num'] = 10;

$selected['page_num'][$_GET['page_num']] = " selected='selected'";
$selected['sms_failCode'][$_GET['sms_failCode']] = " selected='selected'";
$selected['sms_status'][$_GET['sms_status']] = " selected='selected'";

$where[] = "a.sms_logNo = '" . $_GET['sms_logNo'] . "'";
$defaultWhere = "sms_logNo = '" . $_GET['sms_logNo'] . "'";

//�˻� - �߼۰��
switch($_GET['sms_status']){
	case 'y' : case 'n' : case 'r' : case 'c' :
		$where[] = "a.sms_status = '" . $_GET['sms_status'] . "' and a.sms_send_status = 'y'";
		if($_GET['sms_status'] == 'n' && $_GET['sms_failCode']) $where[] = "a.sms_failCode = '".$_GET['sms_failCode']."'";
	break;

	case 'acceptN' :
		$where[] = "a.sms_status = 'r'and a.sms_send_status = 'n'";
	break;
}

//�˻� - �̸�
if ($_GET['sms_name']) {
	$where[] = "a.sms_name like '%" . $_GET['sms_name'] . "%'";
}

//�˻�-���Ź�ȣ
if ($_GET['sms_phoneNumber']) {
	$where[] = "a.sms_phoneNumber like '%" . $_GET['sms_phoneNumber'] . "%'";
}

$pg = new Page($_GET['page'], $_GET['page_num']);
$pg->field = " a.*, b.dormant_regDate ";
$pg->setQuery($db_table ,$where ,"a.sms_no desc");
$pg->exec();
$result = $db->query($pg->query);

list ($total) = $db->fetch("SELECT COUNT(*) FROM " . GD_SMS_SENDLIST . " WHERE " . $defaultWhere);

//sms_log ����
$smsLog = $db->fetch("SELECT * FROM " . GD_SMS_LOG . " WHERE sno='" . $_GET['sms_logNo'] . "' LIMIT 1");

//�߼۽ð�
if($smsLog['reservedt'] != '0000-00-00 00:00:00' && $smsLog['reservedt'] != ''){
	$headInfo['sendTime'] = $smsLog['reservedt'];
}
else{
	$headInfo['sendTime'] = $smsLog['regdt'];
}

//�߼ۻ���
$headInfo['status'] = $sms->getLogStatus($smsLog['status']);

//�߼۰Ǽ� (���������Ǽ�)
list($headInfo['smsAcceptSuccessCnt']) = $db->fetch("SELECT COUNT(*) FROM " . GD_SMS_SENDLIST . " WHERE sms_send_status = 'y' and sms_status != 'c' and " . $defaultWhere);

//�߼ۿ�û ����
list($headInfo['smsAcceptFailCnt']) = $db->fetch("SELECT COUNT(*) FROM " . GD_SMS_SENDLIST . " WHERE sms_send_status = 'n' and " . $defaultWhere);

//�������
list($headInfo['smsCancelCnt']) = $db->fetch("SELECT COUNT(*) FROM " . GD_SMS_SENDLIST . " WHERE sms_send_status = 'y' and sms_status='c' and " . $defaultWhere);

//�߼۰�� - ����
list ($headInfo['smsSendSuccessCnt']) = $db->fetch("SELECT COUNT(*) FROM " . GD_SMS_SENDLIST . " WHERE sms_send_status = 'y' and sms_status = 'y' and " . $defaultWhere);

//�߼۰�� - ����
list ($headInfo['smsSendFailCnt']) = $db->fetch("SELECT COUNT(*) FROM " . GD_SMS_SENDLIST . " WHERE sms_send_status = 'y' and sms_status = 'n' and " . $defaultWhere);

//�������Ʈ
list ($headInfo['smsUsePoint']) = $db->fetch("SELECT COUNT(*) FROM " . GD_SMS_SENDLIST . " WHERE sms_send_status = 'y' and (sms_status = 'y' || sms_status = 'r') and " . $defaultWhere);

//�߼۰�� - ������Ŵ��
list ($headInfo['smsSendReadyCnt']) = $db->fetch("SELECT COUNT(*) FROM " . GD_SMS_SENDLIST . " WHERE sms_send_status = 'y' and sms_status = 'r' and " . $defaultWhere);

$smsErrorCode = $sms_sendlist->errorCodeList();

//Ÿ��
if($smsLog['sms_type']=='lms'){
	$headInfo['type'] = 'LMS';
	$headInfo['smsUsePoint'] = $headInfo['smsUsePoint'] * 3;
	$headInfo['smsReturnPoint'] = $headInfo['smsReturnPoint'] * 3;
	$smsLog['cnt'] = floor($smsLog['cnt'] / 3);
}
else {
	$headInfo['type'] = 'SMS';
}

$apiStart = $smsAPI->apiStartCheck($smsLog['status'], $smsLog['reservedt']);
?>
<script type="text/javascript" src="../godo.loading.indicator.js"></script>
<script language="JavaScript" type="text/JavaScript">
var apiStart		= '<?php echo $apiStart; ?>';
var apiPermission	= '<?php echo $_GET[apiPermission]; ?>';
var smsLogNo		= '<?php echo $smsLog[sno]; ?>';

function smsApiErrorAlert()
{
	alert("������ �ҷ��� �� �����ϴ�.\n��� �� �ٽ� �߼۰�� ��ư�� Ŭ���Ͽ� ��ȸ���ּ���.");
}

function updateApi(){
	nsGodoLoadingIndicator.init({});

	var ajax = new Ajax.Request("./smsApiUpdate.php",
	{
		method: "post",
		parameters: "mode=smsAcceptApi&sms_logNo=" + smsLogNo,
		onComplete: function(req)
		{
			var data = new Array();
			data = req.responseText.split("|");

			if(data[0] == 'success') {
				window.location.href = './popup.sms.sendList.php?sms_logNo=' + smsLogNo;
				window.opener.location.reload();
			}
			else {
				if(data[1] != undefined){
					alert(data[1]);
				}
				else {
					smsApiErrorAlert();
				}
			}

			nsGodoLoadingIndicator.hide();
		},
		onLoaded : function()
		{
			nsGodoLoadingIndicator.hide();
		},
		onLoading : function()
		{
			nsGodoLoadingIndicator.show();
		},
		onFailure : function()
		{
			smsApiErrorAlert();
			nsGodoLoadingIndicator.hide();
		}
	});
}

function sendSMS(msgReload, reLogSno) {

	var target_type = document.getElementById("target_type").value;

	if(target_type == '8'){
		var chk = document.getElementsByName("chk[]");
		var checkNum = false;
		for(var i=0; i<chk.length; i++) if(chk[i].checked == true) checkNum++;
		if(checkNum === false){
			alert('�߼��� ����� ������ �ּ���.');
			return false;
		}
	}

	var x = (window.screen.width - 800) / 2;
	var y = (window.screen.height - 600) / 2;

	var smswin = window.open('about:blank', "smswin", "width=800, height=600, scrollbars=yes, left=" + x + ", top=" + y);

	var f = document.sendListForm;
	f.target = 'smswin';
	f.type.value = target_type;
	f.msgReload.value = msgReload;
	f.reLogSno.value = reLogSno;
	f.action = './popup.sms.php';
	f.submit();
}

function formCheck(f)
{
	var phone = f.sms_phoneNumber;
	var phoneValue = phone.value;

	if(phoneValue){
		if(phoneValue.length < 8){
			alert('���Ź�ȣ�� 8�� �̻��̾�� �մϴ�.');
			phone.focus();
			return false;
		}
	}

	return true;
}

function chgSmsFailCode()
{
	var sms_status = document.getElementById("sms_status");
	var sms_failCode = document.getElementById("sms_failCode");

	if(sms_status.value == 'n'){
		sms_failCode.disabled = false;
		sms_failCode.style.backgroundColor = '#ffffff';
	}
	else {
		sms_failCode.disabled = true;
		sms_failCode.style.backgroundColor = '#dddddd';
	}

}

window.onload = function(){
	chgSmsFailCode();
	if(apiStart == true && apiPermission == 'y'){
		updateApi();
	}
}
</script>
<style type="text/css">
.sendList-smsLayout					{ width: 146px; vertical-align: top;}
.sendList-contents					{ vertical-align: top;}
.sendList-contents .guideFont		{ color: #627dce; font-weight: bold; font-size: 11px; }
.button_top							{ margin-top: 10px; text-align: center; }
.sendList-smsInfo-title				{ margin: 13px 0px 3px 0px; }
#sendList-smsInfo th				{ color: #333333; background: #f6f6f6; font: 9pt tahoma; text-align: center; padding-top: 7px;}
#sendList-smsInfo td				{ color: #333333; font: 9pt tahoma; text-align: center; padding-top: 7px;}
#sendList-smsInfo2 th				{ color: #333333; background: #f6f6f6; font: 9pt tahoma; text-align: center; padding-top: 7px;}
#sendList-smsInfo2 td				{ color: #333333; font: 9pt tahoma; text-align: center; padding-top: 7px;}
#sendList-search					{ margin-top: 10px; }
#sendList							{ word-break: break-all; }
#sendList th						{ color: #333333; background: #f6f6f6; font: 9pt tahoma; text-align: center; }
#sendList tr td						{ color:#262626; font-family:Tahoma,Dotum; font-size:11px; height: 25px; text-align: center; }
.sendList-total						{ padding: 20px 0px 5px 0px; }
.sendList-TextAlignL				{ text-align: left; }
.sendList-TextAlignR				{ text-align: right; }
.sendList-btn						{ margin-bottom: 30px; }
.sendList-btn .sendList-btn-td2		{ padding-left: 10px; }
.sendList-btn .sendList-btn-td2 img { padding-left: 10px; border: 0px; cursor: pointer; }
.inputBorder						{ border: 0px;}
.sendList-smsInfo-warning			{ margin: 3px 0px 0px 0px; color: #627dce; }
</style>

<div class="title title_top">
	<font face="����" color="black"><strong>SMS</strong></font> �߼۰�� ��<span>SMS �߼۰���� ��ȸ�� �� �ֽ��ϴ�.</span>
</div>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
	<td class="sendList-smsLayout"><?php include './popup.sms.layout.php'; ?></td>
	<td class="sendList-contents">
		<form name="sendList_SearchForm" id="sendList_SearchForm" method="GET" onsubmit="return formCheck(this);">
		<input type="hidden" name="sms_logNo" value="<?php echo $_GET['sms_logNo']; ?>" />

		<!-- �߼����� -->
		<div class="sendList-smsInfo-title">��&nbsp;�߼�����</div>
		<table width="100%" id="sendList-smsInfo" class="tb">
		<colgroup>
			<col width="25%" />
			<col width="25%" />
			<col width="25%" />
			<col width="25%" />
		</colgroup>
		<tr>
			<th>�߼۽ð�</th>
			<th>Ÿ��</th>
			<th>�߼ۻ���</th>
			<th>�������Ʈ</th>
		</tr>
		<tr>
			<td><?php echo $headInfo['sendTime']; ?></td>
			<td><?php echo $headInfo['type']; ?></td>
			<td><?php echo $headInfo['status']; ?></td>
			<td><?php echo number_format($headInfo['smsUsePoint']); ?></td>
		</tr>
		</table>
		<div class="sendList-smsInfo-warning">�� �߼۽��� �Ǽ��� �Ϸ翡 �ѹ� ���� 1�ð濡 ����Ǿ� ����Ʈ�� �������˴ϴ�. </div>

		<!-- �߼۰�� -->
		<div class="sendList-smsInfo-title">��&nbsp;�߼۰��</div>
		<table width="100%" id="sendList-smsInfo2" class="tb">
		<colgroup>
			<col width="16%" />
			<col width="15%" />
			<col width="12%" />
			<col width="12%" />
			<col width="15%" />
			<col width="15%" />
			<col width="15%" />
		</colgroup>
		<tr>
			<th rowspan="2">�߼ۿ�û</th>
			<th rowspan="2">�߼ۿ�û����</th>
			<th rowspan="2">�������</th>
			<th rowspan="2">�߼۰Ǽ�</th>
			<th colspan="3">�߼۰��</th>
		</tr>
		<tr>
			<th>�߼ۼ���</th>
			<th>�߼۽���</th>
			<th>������Ŵ��</th>
		</tr>
		<tr>
			<td><?php echo number_format($smsLog['cnt']); ?></td>
			<td><?php echo number_format($headInfo['smsAcceptFailCnt']); ?></td>
			<td><?php echo number_format($headInfo['smsCancelCnt']); ?></td>
			<td><?php echo number_format($headInfo['smsAcceptSuccessCnt']); ?></td>
			<td><?php echo number_format($headInfo['smsSendSuccessCnt']); ?></td>
			<td><?php echo number_format($headInfo['smsSendFailCnt']); ?></td>
			<td><?php echo number_format($headInfo['smsSendReadyCnt']); ?></td>
		</tr>
		</table>
		<div class="sendList-smsInfo-warning">�� �߼ۿ�û����, �߼۽��а� �� �߼۰�� ���� �˻� �� ����Ʈ �ϴ��� ��SMS �ۼ����� ���� �޽����� ��߼� �� �� �ֽ��ϴ�.</div>

		<table width="100%" id="sendList-search" class="tb">
		<colgroup>
			<col class="cellC" />
			<col class="cellL" />
			<col class="cellC" />
			<col class="cellL" />
		</colgroup>
		<tr>
			<td>���</td>
			<td>
				<select name="sms_status" id="sms_status" onchange="javascript:chgSmsFailCode();">
					<option value=""> = ��ü =</option>
					<option value="y" <?php echo $selected['sms_status']['y']; ?>>�߼ۼ���</option>
					<option value="n" <?php echo $selected['sms_status']['n']; ?>>�߼۽���</option>
					<option value="r" <?php echo $selected['sms_status']['r']; ?>>������Ŵ��</option>
					<option value="acceptN" <?php echo $selected['sms_status']['acceptN']; ?>>�߼ۿ�û����</option>
					<option value="c" <?php echo $selected['sms_status']['c']; ?>>�������</option>
				</select>
			</td>
			<td>���л���</td>
			<td>
				<select name="sms_failCode" id="sms_failCode" disabled>
					<option value="" <?php echo $selected['sms_failCode']['']; ?>> - ��ü - </option>
					<?php foreach($smsErrorCode as $code => $value){ ?>
					<option value="<?php echo $code; ?>" <?php echo $selected['sms_failCode'][$code]; ?>><?php echo $value; ?></option>
					<? } ?>
				<select>
			</td>
		</tr>
		<tr>
			<td>�̸�</td>
			<td colspan="3"><input type="text" class="line" name="sms_name" value="<?php echo $_GET['sms_name']; ?>" /></td>
		</tr>
		<tr>
			<td>���Ź�ȣ</td>
			<td colspan="3"><input type="text" class="line" name="sms_phoneNumber" value="<?php echo $_GET['sms_phoneNumber']; ?>" />&nbsp;<span class="guideFont">ex) 010-1111-1111</span></td>
		</tr>
		</table>
		<div class="button_top"><input type="image" src="../img/btn_search2.gif" class="inputBorder" /></div>

		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="sendList-total">
		<tr>
			<td class="sendList-TextAlignL">�� <?php echo number_format($total); ?>��, �˻� <?php echo number_format($pg->recode['total']); ?>�� / <?php echo number_format($pg->page['total']); ?> of <?php echo number_format($pg->page['now']); ?> Pages</td>
			<td class="sendList-TextAlignR">
				<select name="page_num" onchange="javascript:this.form.submit();">
					<option value="10" <?php echo $selected['page_num'][10]; ?>>10�� ���</option>
					<option value="20" <?php echo $selected['page_num'][20]; ?>>20�� ���</option>
					<option value="40" <?php echo $selected['page_num'][40]; ?>>40�� ���</option>
					<option value="60" <?php echo $selected['page_num'][60]; ?>>60�� ���</option>
					<option value="100" <?php echo $selected['page_num'][100]; ?>>100�� ���</option>
				</select>
			</td>
		</tr>
		</table>
		</form>

		<form name="sendListForm" method="POST">
		<input type="hidden" name="type" value="" />
		<input type="hidden" name="query" value="<?php echo substr($pg->query, 0, strpos($pg->query, "limit")); ?>" />
		<input type="hidden" name="reLogSno" value="" />
		<input type="hidden" name="msgReload" value="" />
		<table width="100%" cellpadding="0" cellspacing="0" border="0" id="sendList" class="tb">
		<colgroup>
			<col width="7%" />
			<col width="8%" />
			<col width="20%" />
			<col width="13%" />
			<col width="15%" />
			<col width="14%" />
			<col width="23%" />
		</colgroup>
		<tr>
			<th><a href="javascript:;" onclick="chkBox(document.getElementsByName('chk[]'),'rev');" />����</a></th>
			<th>��ȣ</th>
			<th>�ڵ��������ð�</th>
			<th>�̸�</th>
			<th>���Ź�ȣ</th>
			<th>���</th>
			<th>���л���</th>
		</tr>
		<?php
		while ($data = $db->fetch($result, 1)){
			if(!$data['sms_name']) $data['sms_name'] = ' - ';
			$status		= ' - ';
			$failReason = ' - ';

			$status = $sms_sendlist->getSendListStatus($data['sms_send_status'], $data['sms_status'], $data['sms_mode'], $smsLog['reservedt']);

			if($data['sms_status'] == 'n'){
				$failReason = $smsErrorCode[$data['sms_failCode']];
			}
			$dormantCheckBoxDisabled = '';
			if($data['sms_memNo'] > 0 && $data['dormant_regDate'] != '0000-00-00 00:00:00'){
				$data['sms_name'] = '�޸�ȸ��';
				$data['sms_phoneNumber'] = ' - ';
				$dormantCheckBoxDisabled = 'disabled';
			}
		?>
		<tr>
			<td><input type="checkbox" name="chk[]" value="<?php echo $data['sms_no']; ?>" class="inputBorder" <?php echo $dormantCheckBoxDisabled; ?> /></td>
			<td><?php echo $pg->idx--; ?></td>
			<td><?php echo $data['sms_receiveTime']; ?></td>
			<td><?php echo $data['sms_name']; ?></td>
			<td><?php echo $data['sms_phoneNumber']; ?></td>
			<td><?php echo $status; ?></td>
			<td><?php echo $failReason; ?></td>
		</tr>
		<?php } ?>
		</table>
		</form>

		<div class="pageNavi" align="center"><font class="ver8"><?php echo $pg->page['navi']; ?></div>
	</td>
</tr>
</table>


<table class="sendList-btn" width="100%">
<tr>
	<td class="noline" width="57%" align="right">
		<select name="target_type" id="target_type">
			<option value="8">������ ��󿡰� SMS ������</option>
			<option value="9">�˻��� ��󿡰� SMS ������</option>
		</select>
	</td>
	<td width="43%" class="sendList-btn-td2"><a href="javascript:void(0);" onClick="javascript:sendSMS('y', '<?php echo $smsLog['sno']; ?>');"><img src="../img/btn_today_email_sm.gif" border="0" /></td>
</tr>
</table>

<table cellpadding="0" cellspacing="2" width="100%" border="0" style="margin: 0px 0px 5px 0px; border: 3px #dce1e1 solid; padding: 5px;">
<tr>
	<td style="color: red; font-weight: bold;">
		�� ������Ÿ����� ���ؿ� ���� SMS���ſ��ο� ���ŵ��Ǹ� ���� ���� ȸ���鿡�Դ� ���� ���� SMS�� �߼��� �� �����ϴ�.<br />
		<span style="margin-left: 15px;">�ݵ�� SMS���ſ��� ���¸� Ȯ�� �� SMS�� �߼��� �ּ���.</span>
		<br /><br />
		�� ������Ÿ����� ���ؿ� ����, 1���̻� ���� �̿� ����� ���� ȸ��(�޸�ȸ��) ���� ���� ���� SMS�� �߼��� �� �����ϴ�.<br />
		<span style="margin-left: 15px;">�˻��� ��� �� �޸�ȸ���� ���� �� ��� ���� �� SMS�� �߼��Ͻñ� �ٶ��ϴ�.</span>
	</td>
</tr>
</table>

<div id="MSG01">
<table cellpadding=1 cellspacing=0 border="0" class="small_ex">
<tr>
	<td><strong>[�׸� ����]</strong></td>
</tr>
<tr>
	<td><img src="../img/icon_list.gif" align="absmiddle" />�߼ۿ�û: �߼۴���� ���� �� �������⡯ ��ư�� Ŭ���Ͽ� �߼��� ��û�� �߼۴�� �Ǽ� �Դϴ�.</td>
</tr>
<tr>
	<td><img src="../img/icon_list.gif" align="absmiddle" />�߼ۿ�û����: ��û�� â�� �ݰų� PC�� ������ ���� �������� �߼ۿ�û�� �Ϸ���� ���� �Ǽ��Դϴ�.</td>
</tr>
<tr>
	<td><img src="../img/icon_list.gif" align="absmiddle" />������Ŵ�� : ���� �޽����� �߼��� �̰ų�, �߼ۿϷ� �� ������� �����ϱ� ���� ������� �Ǽ��Դϴ�.</td>
</tr>
<tr>
	<td><img src="../img/icon_list.gif" align="absmiddle" />�������: ����߼��� ��û�� ���¿��� ����߼��� ����� �Ǽ��Դϴ�.</td>
</tr>
<tr>
	<td><img src="../img/icon_list.gif" align="absmiddle" />�߼۰Ǽ�: ��û�� �߼۴�󿡰� ������ �߼��� �޽��� �Ǽ��̸�, �߼ۼ����� ���� ����� Ȯ���� �� �ֽ��ϴ�.</td>
</tr>
</table>
</div>

<script>
cssRound('MSG01');
table_design_load();
</script>