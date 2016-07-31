<?php
include '../_header.popup.php';
@include '../../lib/page.class.php';
include '../../lib/sms.class.php';
include '../../lib/smsAPI.class.php';
include '../../lib/sms_sendlist.class.php';
$sms = new sms();
$smsAPI = new smsAPI();
$sms_sendlist = new sms_sendlist();

if(!$_GET['sms_logNo']){
	msg('�ùٸ� ������ �ƴմϴ�.', 'close');
	exit;
}
$db_table = GD_SMS_SENDLIST . ' AS a LEFT JOIN ' . GD_MEMBER . ' AS b ON a.sms_memNo=b.m_no';
$headInfo = array();
$nowDateTime = date("Y-m-d H:i:s");
if(!$_GET['page'])		$_GET['page'] = 1;
if(!$_GET['page_num'])	$_GET['page_num'] = 10;

$selected['page_num'][$_GET['page_num']] = " selected='selected'";
$selected['sms_status'][$_GET['sms_status']] = " selected='selected'";

$where[] = "a.sms_logNo = '" . $_GET['sms_logNo'] . "'";
$defaultWhere = "sms_logNo = '" . $_GET['sms_logNo'] . "'";

//�˻�-�̸�
if ($_GET['sms_name']) {
	$where[] = "a.sms_name like '%" . $_GET['sms_name'] . "%'";
}

//�˻�-���Ź�ȣ
if ($_GET['sms_phoneNumber']) {
	$where[] = "a.sms_phoneNumber like '%" . $_GET['sms_phoneNumber'] . "%'";
}

//���
if ($_GET['sms_status']) {
	if($_GET['sms_status'] == 'acceptN'){
		$where[] = "a.sms_status = 'r' and a.sms_send_status='n'";
	}
	else {
		$where[] = "a.sms_status = '".$_GET['sms_status']."' and a.sms_send_status='y'";
	}
}

$pg = new Page($_GET['page'], $_GET['page_num']);
$pg->field = " a.*, b.dormant_regDate ";
$pg->setQuery($db_table ,$where ,"a.sms_no desc");
$pg->exec();

$result = $db->query($pg->query);

list ($total) = $db->fetch("SELECT COUNT(*) FROM " . GD_SMS_SENDLIST . " WHERE " . $defaultWhere);

//sms_log ����
$smsLog = $db->fetch("SELECT * FROM " . GD_SMS_LOG . " WHERE sno='" . $_GET['sms_logNo'] . "' LIMIT 1");

//�߼ۻ���
$headInfo['status'] = $sms->getLogStatus($smsLog['status']);

//�߼۴��
list($headInfo['smsSendReadyCnt']) = $db->fetch("SELECT COUNT(*) FROM " . GD_SMS_SENDLIST . " WHERE sms_send_status = 'y' and sms_status = 'r'  and " . $defaultWhere);

//�������Ʈ
list($headInfo['smsUsePoint']) = $db->fetch("SELECT COUNT(*) FROM " . GD_SMS_SENDLIST . " WHERE sms_send_status = 'y' and (sms_status = 'y' || sms_status = 'r') and " . $defaultWhere);

//Ÿ��
if($smsLog['sms_type']=='lms'){
	$headInfo['smsUsePoint'] = $headInfo['smsUsePoint'] * 3;
	$smsLog['cnt'] = floor($smsLog['cnt']/3);
	$headInfo['type'] = 'LMS';
}
else {
	$headInfo['type'] = 'SMS';
}

//�߼ۿ�û ����
list($headInfo['smsRequestFailCnt']) = $db->fetch("SELECT COUNT(*) FROM " . GD_SMS_SENDLIST . " WHERE sms_send_status = 'n' and " . $defaultWhere);

//�������
list($headInfo['smsCancelCnt']) = $db->fetch("SELECT COUNT(*) FROM " . GD_SMS_SENDLIST . " WHERE sms_send_status = 'y' and sms_status = 'c' and " . $defaultWhere);

$reserveApiTimecheck = $smsAPI->reserveApiTimecheck($smsLog['reservedt']);
?>
<style type="text/css">
.reserveList-smsLayout						{ width: 146px; vertical-align: top;}
.reserveList-contents						{ vertical-align: top;}
.reserveList-contents .guideFont			{ color: #627dce; font-weight: bold; font-size: 11px; }
.button_top									{ margin-top: 10px; text-align: center; }
.reserveList-smsInfo-title					{ margin: 13px 0px 3px 0px;}
#reserveList-smsInfo th						{ color: #333333; background: #f6f6f6; font: 9pt tahoma; text-align: center; padding-top: 7px;}
#reserveList-smsInfo td						{ color: #333333;font: 9pt tahoma; text-align: center; padding-top: 7px;}
#reserveList-smsInfo2 th					{ color: #333333; background: #f6f6f6; font: 9pt tahoma; text-align: center; padding-top: 7px;}
#reserveList-smsInfo2 td					{ color: #333333;font: 9pt tahoma; text-align: center; padding-top: 7px;}
#reserveList-search							{ margin-top: 10px; }
#reserveList								{ word-break: break-all; }
#reserveList th								{ color: #333333; background: #f6f6f6; font: 9pt tahoma; text-align: center; }
#reserveList tr td							{ color:#262626; font-family:Tahoma,Dotum; font-size:11px; height: 25px; text-align: center;}
.reserveList-total							{ padding: 20px 0px 5px 0px; }
.reserveList-TextAlignL						{ text-align: left; }
.reserveList-TextAlignR						{ text-align: right; }
.reserveList-btn							{ margin-top: 10px; }
.reserveList-btn td							{ text-align: right; }
.reserveList-btn2							{ margin: 20px 0px 30px 0px; }
.reserveList-btn2 .reserveList-btn-td2		{ padding-left: 10px; }
.reserveList-btn2 .reserveList-btn-td2 img	{ padding-left: 10px; border: 0px; cursor: pointer; }
.imgLink									{ border: 0px; cursor: pointer; }
.verticalMiddle								{ vertical-align: middle; }
.inputBorder0								{ border: 0px; }
.cancelAllBtn								{ text-align: right; width: 100%; margin-top: 5px; }
</style>
<script type="text/javascript" src="../godo.loading.indicator.js"></script>
<script language="JavaScript" type="text/JavaScript">
var apiTimecheck = '<?php echo $reserveApiTimecheck; ?>';
var cancelAllCnt = '<?php echo $headInfo[smsSendReadyCnt]; ?>';

function tryModifyApi(sms_no)
{
	var El_PhoneNumber = document.getElementById("sms_phoneNumber_" + sms_no);

	if(apiTimecheck != true){
		alert("�߼ۿ���ð� 1�ð� ������ ��ȣ������ �����մϴ�.");
		return false;
	}

	if(El_PhoneNumber.value == ''){
		alert('����� ���Ź�ȣ�� �Է��ϼ���.');
		return false;
	}

	if(confirm("���Ź�ȣ�� ����˴ϴ�.\n��, ȸ�������� ����� ��ȣ�� ������� �ʽ��ϴ�.")){
		ajaxApi('smsModifyApi', sms_no, El_PhoneNumber.value);
	}
	return false;
}

function tryCancelApi(sms_no, sms_phoneNumber)
{
	if(apiTimecheck != true){
		alert("�߼ۿ���ð� 1�ð� ������ ������Ұ� �����մϴ�.");
		return false;
	}

	if(confirm("������ ����Ͻðڽ��ϱ�?")){
		ajaxApi('smsCancelApi', sms_no, sms_phoneNumber);
	}
}

function tryCancelAllApi(sms_no, sms_phoneNumber)
{
	if(cancelAllCnt < 1){
		alert("��Ұ����� ������� �����ϴ�.");
		return false;
	}

	if(apiTimecheck != true){
		alert("�߼ۿ���ð� 1�ð� ������ ������Ұ� �����մϴ�.");
		return false;
	}

	if(confirm("��ü���� ��Ҹ� �Ͻðڽ��ϱ�?")){
		ajaxApi('smsCancelApiAll', '', '');
	}
}

function smsApiErrorAlert(mode)
{
	switch(mode){
		case 'smsModifyApi':
			var msg = '��ȣ������ �����Ͽ����ϴ�';
		break;

		case 'smsCancelApi':
			var msg = '������Ҹ� �����Ͽ����ϴ�';
		break;

		case 'smsCancelApiAll':
			var msg = '��ü������Ҹ� �����Ͽ����ϴ�';
		break;
	}

	alert(msg + "\n��� �� �ٽ� �õ����ּ���.");
}

function ajaxApi(mode, sms_no, sms_phoneNumber){
	var logNo = '<?php echo $smsLog[sno]; ?>';
	nsGodoLoadingIndicator.init({});

	var ajax = new Ajax.Request("./smsApiUpdate.php",
	{
		method: "post",
		parameters: "mode=" + mode + "&sms_logNo=" + logNo + "&sms_no=" + sms_no + "&sms_phoneNumber=" + sms_phoneNumber,
		onComplete: function (req)
		{
			var data = new Array();
			data = req.responseText.split("|");

			if(data[0] == 'success') {
				alert(data[1]);
				window.location.reload();
			}
			else {
				if(data[1] != undefined){
					alert(data[1]);
				}
				else {
					smsApiErrorAlert(mode);
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
			smsApiErrorAlert(mode);
			nsGodoLoadingIndicator.hide();
		}
	});
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

function chgPhoneNumber(type, idNo)
{
	var phoneText = document.getElementById('phoneNumberText_' + idNo);
	var phoneInput = document.getElementById('phoneNumberInput_' + idNo);

	if(type=='input'){
		phoneInput.style.display = 'block';
		phoneText.style.display = 'none';
	}
	else{
		phoneInput.style.display = 'none';
		phoneText.style.display = 'block';
	}
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

	var f = document.reserveListForm;
	f.target = 'smswin';
	f.type.value = target_type;
	f.msgReload.value = msgReload;
	f.reLogSno.value = reLogSno;
	f.action = './popup.sms.php';
	f.submit();
}
</script>

<div class="title title_top">
	<font face="����" color="black"><strong>SMS</strong></font> �������� ��<span>����߼� ���� ���� �� �߼ۿ�����Ҹ� �� �� �ֽ��ϴ�. ��, �߼ۿ������ �� ���Ź�ȣ ������ �޽��� �߼� 1�ð� ���� �����մϴ�.</span>
</div>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
	<td class="reserveList-smsLayout"><?php include './popup.sms.layout.php'; ?></td>
	<td class="reserveList-contents">
		<!-- �������� -->
		<div class="reserveList-smsInfo-title">��&nbsp;��������</div>
		<table width="100%" id="reserveList-smsInfo" class="tb">
		<colgroup>
			<col width="25%" />
			<col width="25%" />
			<col width="25%" />
			<col width="25%" />
		</colgroup>
		<tr>
			<th>�߼ۿ���ð�</th>
			<th>Ÿ��</th>
			<th>�߼ۻ���</th>
			<th>�������Ʈ</th>
		</tr>
		<tr>
			<td><?php echo $smsLog['reservedt']; ?></td>
			<td><?php echo $headInfo['type']; ?></td>
			<td><?php echo $headInfo['status']; ?></td>
			<td><?php echo number_format($headInfo['smsUsePoint']); ?></td>
		</tr>
		</table>

		<!-- ������ -->
		<div class="reserveList-smsInfo-title">��&nbsp;������</div>
		<table width="100%" id="reserveList-smsInfo2" class="tb">
		<colgroup>
			<col width="25%" />
			<col width="25%" />
			<col width="25%" />
			<col width="25%" />
		</colgroup>
		<tr>
			<th>�߼ۿ�û</th>
			<th>�߼ۿ�û����</th>
			<th>�������</th>
			<th>�߼۴��</th>
		</tr>
		<tr>
			<td><?php echo number_format($smsLog['cnt']); ?></td>
			<td><?php echo number_format($headInfo['smsRequestFailCnt']); ?></td>
			<td><?php echo number_format($headInfo['smsCancelCnt']); ?></td>
			<td><?php echo number_format($headInfo['smsSendReadyCnt']); ?></td>
		</tr>
		</table>

		<form name="reserveList-SearchForm" id="reserveList-SearchForm" method="get" onsubmit="return formCheck(this);">
		<input type="hidden" name="sms_logNo" value="<?php echo $smsLog['sno']; ?>" />
		<table width="100%" id="reserveList-search" class="tb">
		<colgroup>
			<col class="cellC" />
			<col class="cellL" />
		</colgroup>
		<tr>
			<td>���</td>
			<td>
				<select name="sms_status">
					<option value="" <?php echo $selected['sms_status']['']; ?>>��ü</option>
					<option value="r" <?php echo $selected['sms_status']['r']; ?>>�߼۴��</option>
					<option value="acceptN" <?php echo $selected['sms_status']['acceptN']; ?>>�߼ۿ�û����</option>
					<option value="c" <?php echo $selected['sms_status']['c']; ?>>�������</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>�̸�</td>
			<td><input type="text" class="line" name="sms_name" value="<?php echo $_GET['sms_name']; ?>" /></td>
		</tr>
		<tr>
			<td>���Ź�ȣ</td>
			<td><input type="text" class="line" name="sms_phoneNumber" value="<?php echo $_GET['sms_phoneNumber']; ?>" />&nbsp;<span class="guideFont">ex) 010-1111-1111</span></td>
		</tr>
		</table>
		<div class="button_top"><input type="image" src="../img/btn_search2.gif" class="inputBorder0" /></div>

		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="reserveList-total">
		<tr>
			<td class="reserveList-TextAlignL">�� <?php echo $total; ?>��, �˻� <?php echo $pg->recode['total']; ?>�� / <?php echo $pg->page['total']; ?> of <?php echo $pg->page['now']; ?> Pages</td>
			<td class="reserveList-TextAlignR">
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

		<form name="reserveListForm" method="POST">
		<input type="hidden" name="type" value="" />
		<input type="hidden" name="query" value="<?php echo substr($pg->query, 0, strpos($pg->query, "limit")); ?>" />
		<input type="hidden" name="reLogSno" value="" />
		<input type="hidden" name="msgReload" value="" />
		<table width="100%" cellpadding="0" cellspacing="0" border="0" id="reserveList" class="tb">
		<colgroup>
			<col width="7%" />
			<col width="8%" />
			<col width="15%" />
			<col width="40%" />
			<col width="15%" />
			<col width="17%" />
		</colgroup>
		<tr>
			<th><a href="javascript:;" onclick="chkBox(document.getElementsByName('chk[]'),'rev');" />����</a></th>
			<th>��ȣ</th>
			<th>�̸�</th>
			<th>���Ź�ȣ</th>
			<th>���</th>
			<th>����߼����</th>
		</tr>
		<?php
		while ($data = $db->fetch($result, 1)){
			$reserveCancelBtn = $reserveMsg = ' - ';
			$modifyBtn = $modifyApplyBtn = '';

			$reserveMsg = $sms_sendlist->getSendListStatus($data['sms_send_status'], $data['sms_status'], $data['sms_mode'], $smsLog['reservedt']);

			$dormantCheckBoxDisabled = '';
			if($data['sms_memNo'] > 0 && $data['dormant_regDate'] != '0000-00-00 00:00:00'){
				$data['sms_name'] = '�޸�ȸ��';
				$data['sms_phoneNumber'] = ' - ';
				$dormantCheckBoxDisabled = 'disabled';
			}

			if($data['sms_status'] == 'r' && $data['sms_send_status'] == 'y' && $reserveApiTimecheck == true){

				//�������
				$reserveCancelBtn = "<img src='../img/btn_reserve_cancel.gif' class='imgLink' onclick=\"javascript:tryCancelApi('".$data['sms_no']."', '".$data['sms_phoneNumber']."');\" />";

				//����
				if($data['dormant_regDate'] == '0000-00-00 00:00:00'){
					$modifyBtn = "<span style='height: 25px;'><img src='../img/btn_small_modify.gif' onclick=\"javascript:chgPhoneNumber('input', '".$data['sms_no']."');\" class='imgLink verticalMiddle' /></span>";
				}

				//����
				$modifyApplyBtn = "
				<div id='phoneNumberInput_".$data['sms_no']."' style='display: none;'>
					<input type='text' name='sms_phoneNumber[]' id='sms_phoneNumber_".$data['sms_no']."' value='".$data['sms_phoneNumber']."' class='line' maxlength='15' />
					<img src='../img/btn_small_apply.gif' onclick=\"javascript:tryModifyApi('".$data['sms_no']."');\" class='imgLink' style='vertical-align: bottom;' /><img src='../img/btn_small_cancel.gif' onclick=\"javascript:chgPhoneNumber('text', '".$data['sms_no']."');\" class='imgLink' style='vertical-align: bottom;' />
				</div>
				";
			}
		?>
		<tr>
			<td><input type="checkbox" name="chk[]" value="<?php echo $data['sms_no']; ?>" class="inputBorder0" <?php echo $dormantCheckBoxDisabled; ?> /></td>
			<td><?php echo $pg->idx--; ?></td>
			<td><?php echo $data['sms_name']; ?></td>
			<td>
				<div id="phoneNumberText_<?php echo $data['sms_no']; ?>">
					<span style="line-height: 25px; height: 25px;"><?php echo $data['sms_phoneNumber']; ?></span>&nbsp;
					<?php echo $modifyBtn; ?>
				</div>
				<?php echo $modifyApplyBtn; ?>
			</td>
			<td><?php echo $reserveMsg; ?></td>
			<td><?php echo $reserveCancelBtn; ?></td>
		</tr>
		<?php } ?>
		</table>
		</form>

		<div class="cancelAllBtn"><img src="../img/btn_sms_all_cancel.gif" class='imgLink' onclick="javascript:tryCancelAllApi();" /></div>
		<div class="pageNavi" align="center"><font class="ver8"><?php echo $pg->page['navi']; ?></div>
	</td>
</tr>
</table>

<table class="reserveList-btn2" width="100%">
<tr>
	<td class="noline" width="57%" align="right">
		<select name="target_type" id="target_type">
			<option value="8">������ ��󿡰� SMS ������</option>
			<option value="9">�˻��� ��󿡰� SMS ������</option>
		</select>
	</td>
	<td width="43%" class="reserveList-btn-td2"><a href="javascript:void(0);" onClick="javascript:sendSMS('y', '<?php echo $smsLog['sno']; ?>');"><img src="../img/btn_today_email_sm.gif" border="0" /></td>
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
	<td><img src="../img/icon_list.gif" align="absmiddle" />�������: ����߼��� ��û�� ���¿��� ����߼��� ����� �Ǽ��Դϴ�.</td>
</tr>
<tr>
	<td><img src="../img/icon_list.gif" align="absmiddle" />�߼۴��: ��û�� �߼۴�󿡰� ����ð��� �Ǹ� ������ �߼۵� ���Ǽ��Դϴ�.</td>
</tr>
</table>
</div>

<script>
cssRound('MSG01');
table_design_load();
</script>