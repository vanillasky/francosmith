<?php
$location = 'SMS���� > SMS �߼۳���';
include '../_header.php';
include_once('../../lib/page.class.php');
include_once('../../lib/sms.class.php');
include_once('../../lib/sms_sendlist.class.php');
$sms = new sms();
$sms_sendlist = new sms_sendlist();

$colspan = 10;
if(!$_GET['page'])		$_GET['page'] = 1;
if(!$_GET['page_num'])	$_GET['page_num'] = 10;

$selected['page_num'][$_GET['page_num']] = " selected='selected'";
$checked['reserve'][$_GET['reserve']] = " checked='checked'";
$checked['status'][$_GET['status']] = " checked='checked'";
$checked['sms_status'][$_GET['sms_status']] = " checked='checked'";

//����߼۰� �߼��� ���� ������Ʈ
$sms_sendlist->updateReserveSendingAll();

//�˻� - ����
if ($_GET['reserve'] == 'now') { 
	$where[] = " (reservedt = '0000-00-00 00:00:00' or reservedt = '') ";
} 
else if ($_GET['reserve'] == 'reserve') {
	$where[] = " (reservedt != '0000-00-00 00:00:00' and reservedt != '')";
} 
else {
	$_GET['reserve'] = '';
}

//�˻� - �߼�(����) �Ⱓ
if ($_GET['regdt'][0] && $_GET['regdt'][1]){
	switch($_GET['reserve']){
		case 'now':
			$where[] = "regdt between date_format(" . $_GET['regdt'][0] . ",'%Y-%m-%d 00:00:00') and date_format(" . $_GET['regdt'][1] . ",'%Y-%m-%d 23:59:59')";
		break;

		case 'reserve':
			$where[] = "reservedt between date_format(" . $_GET['regdt'][0] . ",'%Y-%m-%d 00:00:00') and date_format(" . $_GET['regdt'][1] . ",'%Y-%m-%d 23:59:59')";
		break;

		default: case '':
			$where[] = "((regdt between date_format(" . $_GET['regdt'][0] . ",'%Y-%m-%d 00:00:00') and date_format(" . $_GET['regdt'][1] . ",'%Y-%m-%d 23:59:59')) or (reservedt between date_format(" . $_GET['regdt'][0] . ",'%Y-%m-%d 00:00:00') and date_format(" . $_GET['regdt'][1] . ",'%Y-%m-%d 23:59:59')))";
		break;
	}
}

//�˻� - �߼ۻ���
if($_GET['status']){
	$where[] = "status='" . $_GET['status'] . "'";
}

/*
* �˻� - �߼۽��� ����
* send_fail_check : y - �߼۽��а�����, n - �߼۽��аǾ���
* accept_fail_check : y - �������а�����, n - �������аǾ���
*/
if ($_GET['sms_status'] == 'y') {
	$where[] = "accept_fail_check='y'";
}
else if($_GET['sms_status'] == 'n') {
	$where[] = "send_fail_check = 'y'";
}

//�˻� - ���Ź�ȣ 
if ($_GET['sms_phoneNumber']) {
	$res = $db->query("SELECT sms_logNo FROM " . GD_SMS_SENDLIST . " WHERE sms_phoneNumber like '%" . $_GET['sms_phoneNumber'] . "%'");
	while($row = $db->fetch($res)){
		$_logNoArr[] = $row['sms_logNo'];
	}
	$sms_logNoArr = array_unique($_logNoArr);
	$where[] = "sno in ('" . implode("','", $sms_logNoArr) . "')";
}

$pg = new Page($_GET['page'], $_GET['page_num']);
$pg->field = " * ";
$pg->setQuery(GD_SMS_LOG ,$where , 'sno desc');
$pg->exec();

$result = $db->query($pg->query);

list ($total) = $db->fetch("SELECT COUNT(*) FROM " . GD_SMS_LOG);
?>
<script type="text/javascript" src="../godo_ui.js"></script>
<script language="JavaScript" type="text/JavaScript">
function checkReserve()
{
	var form = document.sendList_searchForm;
	if(form.reserve[1].checked == true) {
		form.status[3].disabled = true;
		form.status[4].disabled = true;
		if(form.status[3].checked == true || form.status[4].checked == true) form.status[0].checked = true;
	}
	else {
		form.status[3].disabled = false;
		form.status[4].disabled = false;
	}
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
</script>
<style type="text/css">
.sendList-guide					{ border-collapse: collapse; margin-bottom: 10px; border-color: #dce1e1; }
.sendList-guide td				{ padding:7px 0px 10px 10px; }
.sendList-guide td a			{ letter-spacing: -1px; color: #627dce; font-weight: bold; text-decoration: underline; }
.sendList-guide td div			{ padding-top: 5px; }
.sendList-guide td .divPaddingL { padding-left: 16px; }
.sendList-guide td .guide-title { font-weight: bold; }
.guideFont						{ color: #627dce; font-weight: bold; font-size: 11px; }
.sendList-total					{ padding: 20px 0px 5px 0px; }
.sendList						{ word-break: break-all;}
.sendList .sendList-Height		{ height:30px; }
.sendList .sendList-contentsTr	{ height:30px; text-align: center; }
.sendList .sendList-contents	{ text-align: left; padding-left: 10px; }
.sendList tr td					{ color:#262626; font-family:Tahoma,Dotum; font-size:11px; }
.sendList-Bg					{ background-color: #4F4F4F; font:8pt ����; height:30px; color:#ffffff; }
.sendList-line					{ background-color: #DCD8D6; height: 1px; }
.sendList-TextAlignL			{ text-align: left; }
.sendList-TextAlignR			{ text-align: right; }
.sendList .ColorTr td			{ color: red; }
.sendList .sendListSubTh 		{ font:8pt ����; height:30px; color:#ffffff; }
.sendList .sendListSubTd 		{ color:#262626; font-family:Tahoma,Dotum; font-size:11px; text-align: center; }
.imgLink						{ border: 0px; cursor: pointer; }
</style>

<div class="title title_top">
	<font face="����" color="black"><strong>SMS</strong></font> �߼۳���<span>���۵� SMS �߼۳����� �����մϴ�</span> 
	<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=member&no=18')"><img src="../img/btn_q.gif" align="absmiddle" hspace="2" /></a>
</div>

<table border="4" width="730" class="sendList-guide" cellspacing="0" cellpadding="0">
<tr>
	<td>
		<div class="guide-title">�� SMS �߼۳��� Ȯ�� �ȳ�</div>
		<div class="g9">�� �߼ۿϷ�� �Ǽ��� ����Ʈ�����Ǹ�, �߼۽��е� �Ǽ��� �Ϸ翡 �ѹ� ���� 1�ð濡 ����Ǿ� ���� ����Ʈ�� ��ȯ�˴ϴ�.</div>
		<div class="g9">�� ����, ���� ���� �� �߼ۻ��´� �������� Ȥ�� �߼۰�� ��ư�� Ŭ���ϸ� ��ȸ�Ͻ� �� �ֽ��ϴ�.</div>
		<div class="g9 divPaddingL">[�߼ۿ�û���� Ȥ�� �߼۽��а��� ������ ����Ʈ�� ���������� ǥ�õ˴ϴ�.]</div>
		<div class="g9">�� ���� ��Ȯ�� SMS �߼۳��� �����ʹ� ������ �α��� �Ͻ� ��, ���̰����� �ٿ�ε尡 �����մϴ�.</div> 
		<div class="g9 divPaddingL">�޴� : ���� �α��� > ���̰� > ���� ���θ� > [������/����] Ŭ�� > SMS �߼� �������� �ٿ�ε�</div>
		<div class="g9 divPaddingL divLink"><a href="http://www.godo.co.kr/mygodo/index.html" target="_blank">[���̰� �ٷΰ��� > ]</a></div>
	</td>
</tr>
</table>

<form name="sendList_searchForm" method="get" onsubmit="return formCheck(this);">
<input type="hidden" name="search" value="yes" />

<table class="tb">
<colgroup>
	<col class="cellC" />
	<col class="cellL" />
</colgroup>
<tr>
	<td>����</td>
	<td class="noline">
		<label><input type="radio" name="reserve" value="" <?php echo $checked['reserve']['']; ?> onclick="javascript:checkReserve();" />��ü</label>
		<label><input type="radio" name="reserve" value="now" <?php echo $checked['reserve']['now']; ?> onclick="javascript:checkReserve();" />��ù߼�</label>
		<label><input type="radio" name="reserve" value="reserve" <?php echo $checked['reserve']['reserve']; ?> onclick="javascript:checkReserve();" />����߼�</label>
	</td>
</tr>
<tr>
	<td>�߼�(����)�Ⱓ</td>
	<td>
		<input type="text" name="regdt[]" value="<?=$_GET['regdt'][0]?>" size="8" maxlength="8" onkeydown="onlynumber();" class="cline" /> -
		<input type="text" name="regdt[]" value="<?=$_GET['regdt'][1]?>" size="8" maxlength="8" onkeydown="onlynumber();" class="cline" />
		<a href="javascript:setDate('regdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align="absmiddle" /></a>
		<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align="absmiddle" /></a>
		<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align="absmiddle" /></a>
		<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align="absmiddle" /></a>
		<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align="absmiddle" /></a>
		<a href="javascript:setDate('regdt[]')"><img src="../img/sicon_all.gif" align="absmiddle" /></a>
</tr>
<tr>
	<td>�߼ۻ���</td>
	<td class="noline">
		<label><input type="radio" name="status" value="" <?php echo $checked['status']['']; ?> />��ü</label>
		<label><input type="radio" name="status" value="4" <?php echo $checked['status']['4']; ?> />�߼ۿϷ�</label>
		<label><input type="radio" name="status" value="3" <?php echo $checked['status']['3']; ?> />������Ŵ��&nbsp;<img src="../img/icons/icon_qmark.gif" style="vertical-align:bottom; cursor:pointer; border: 0px;" class="godo-tooltip" tooltip="������Ŵ��� ���� �޽����� �߼��� �̰ų�, �߼ۿϷ� �� ������� �����ϱ� ����<br />������� �����Դϴ�. �߼۰���� Ŭ���ϸ� ������� ������ �� �ֽ��ϴ�."> </label>
		<label><input type="radio" name="status" value="1" <?php echo $checked['status']['1']; ?> />�߼۴��</label>
		<label><input type="radio" name="status" value="2" <?php echo $checked['status']['2']; ?> />�������</label>
	</td>
</tr>
<tr>
	<td>�߼۽��а� ��ȸ</td>
	<td class="noline">
		<label><input type="radio" name="sms_status" value="" <?php echo $checked['sms_status']['']; ?> />��ü</label>
		<label><input type="radio" name="sms_status" value="y" <?php echo $checked['sms_status']['y']; ?> />�߼ۿ�û����</label>
		<label><input type="radio" name="sms_status" value="n" <?php echo $checked['sms_status']['n']; ?> />�߼۽���</label>
	</td>
</tr>
<tr>
	<td>���Ź�ȣ</td>
	<td><input type="text" name="sms_phoneNumber" value="<?=$_GET['sms_phoneNumber']?>" class="line" />&nbsp;<span class="guideFont">ex) 010-1111-1111</span></td>
</tr>
</table>
<div class="button_top"><input type="image" src="../img/btn_search2.gif" /></div>


<table width="100%" cellpadding="0" cellspacing="0" border="0" class="sendList-total">
<tr>
	<td class="sendList-TextAlignL">�� <?php echo $total; ?>��, �˻� <?php echo $pg->recode['total']; ?>�� / <?php echo $pg->page['total']; ?> of <?php echo $pg->page['now']; ?> Pages</td>
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

<table width="100%" cellpadding="0" cellspacing="0" border="0" class="sendList">
<colgroup>
	<col width="4%" />
	<col width="8%" />
	<col width="11%" />
	<col width="*" />
	<col width="5%" />
	<col width="5%" />
	<col width="5%" />
	<col width="10%" />
	<col width="8%" />
	<col width="7%" />
</colgroup>
<tr class="sendList-Bg sendList-Height">
	<th>��ȣ</th>
	<th>����</th>
	<th>�߼۽ð�/<br />�߼ۿ���ð�</th>
	<th>����+�޽���</th>
	<th>�߼�����</th>
	<th>�߼ۿ�û</th>
	<th>�߼ۼ���</th>
	<th>
		<table cellpadding="0" cellspacing="0" width="100%" class="sendListSubTh">
		<colgroup>
			<col width="50%" />
			<col width="50%" />
		</colgroup>
		<tr>
			<th colspan="2">����</th>
		</tr>
		<tr>
			<th>��û����</th>
			<th>�߼۽���</th>
		</tr>
		</table>
	</th>
	<th>�߼ۻ���</th>
	<th>�ڼ���</th>
</tr>
<tr><td colspan="<?php echo $colspan; ?>" class="sendList-line"></td></tr>
<?php 
while ($data = $db->fetch($result, 1)){ 
	$send_successCnt	= $send_failCnt = $status = ' - ';
	$style				= '';

	if($data['sms_type'] == 'lms'){
		$msg = '<div style="margin: 5px 0px 7px 0px;">' . $data['subject'] . '</div>' . $data['msg'];
		$smsType = 'LMS';
		$data['cnt'] = $data['cnt']/3;
	} 
	else {
		$msg = $data['msg'];
		$smsType = 'SMS';	
	}

	if(!$data['status']){
		$zoomButton = "<img src='../img/btn_sendlist_result_info.gif' class='imgLink' onclick=\"javascript:alert('[sms]��ġ ���� �߼۰ǵ鸸 �߼۰���� Ȯ���� �� �ֽ��ϴ�.');\" />";
	}
	else {
		$zoomButton = "<img src='../img/btn_sendlist_result_info.gif' class='imgLink' onclick=\"javascript:popup('./popup.sms.sendList.php?sms_logNo=" . $data['sno'] . "&apiPermission=y', '800', '750')\" />";
	}

	if($data['reservedt'] != '0000-00-00 00:00:00' && $data['reservedt'] != '') {
		$sendTime = $data['reservedt'];
		$reserveType = '����߼�';

		if($data['status'] == '1' || $data['status'] == '2') $zoomButton = "<img src='../img/btn_sendlist_reserve_info.gif' class='imgLink' onclick=\"javascript:popup('./popup.sms.reserveList.php?sms_logNo=" . $data['sno'] . "', '800', '700')\" />";		
	}
	else {
		$sendTime = $data['regdt'];
		$reserveType = '��ù߼�';
	}

	//�߼ۼ���
	list($smsSendSuccessCnt) = $db->fetch(" SELECT COUNT(*) FROM " . GD_SMS_SENDLIST . " WHERE sms_logNo='" . $data['sno'] . "' and sms_status='y' and sms_send_status='y' ");

	//�߼۽���
	list($smsSendFailCnt) = $db->fetch(" SELECT COUNT(*) FROM " . GD_SMS_SENDLIST . " WHERE sms_logNo='" . $data['sno'] . "' and sms_status='n' and sms_send_status='y' ");

	//��������
	list($smsAcceptFailCnt) = $db->fetch(" SELECT COUNT(*) FROM " . GD_SMS_SENDLIST . " WHERE sms_logNo='" . $data['sno'] . "' and sms_send_status='n' ");

	//�������� sms log update
	if($smsAcceptFailCnt > 0 && $data['accept_fail_check'] != 'y'){
		$sms_sendlist->updateSmsLogAcceptFail($data['sno']);
		$data['accept_fail_check'] = 'y';
	}
	//�߼۽��� sms log update
	if($smsSendFailCnt > 0 && $data['send_fail_check'] != 'y'){
		$sms_sendlist->updateSmsLogSendFail($data['sno']);
		$data['send_fail_check'] = 'y';
	}

	if($data['status']) $status = $sms->getLogStatus($data['status']);
	
	if($data['accept_fail_check'] == 'y' || $data['send_fail_check'] == 'y') $style = ' ColorTr';
?>
<tr class="sendList-contentsTr<?php echo $style; ?>">
	<td><?php echo $pg->idx--; ?></td>
	<td><?php echo $reserveType; ?></td>
	<td><?php echo $sendTime; ?></td>
	<td class="sendList-contents"><?php echo $msg; ?></td>
	<td><?php echo $smsType; ?></td>
	<td><?php echo number_format($data['cnt']); ?></td>
	<td><?php echo number_format($smsSendSuccessCnt); ?></td>
	<td>
		<table cellpadding="0" cellspacing="0" width="100%" class="sendListSubTd">
		<colgroup>
			<col width="50%" />
			<col width="50%" />
		</colgroup>
		<tr>
			<td><?php echo number_format($smsAcceptFailCnt); ?></td>
			<td><?php echo number_format($smsSendFailCnt); ?></td>
		</tr>
		</table>
	</td>
	<td><?php echo $status; ?></td>
	<td><?php echo $zoomButton; ?></td>
</tr>
<tr><td colspan="<?php echo $colspan; ?>" class="sendList-line"></td></tr>
<?php } ?>
</table>

<div class="pageNavi" align="center"><font class="ver8"><?php echo $pg->page['navi']; ?></div>

<div id="MSG01">
<table cellpadding=1 cellspacing=0 border="0" class="small_ex">
<tr>
	<td>�߼ۼ��� �Ǽ��� ����Ʈ�� �����˴ϴ�.</td>
</tr>
<tr>
	<td>�߼۰���� Ŭ���ϸ� ���� �߼۵� ������ Ȯ���� �� �ֽ��ϴ�.</td>
</tr>
<tr>
	<td style="padding-top: 10px;"><strong>[�߼۳��� �׸� ����]</strong></td>
</tr>
<tr>
	<td><img src="../img/icon_list.gif" align="absmiddle" />�߼ۿ�û: �߼۴���� ���� �� �������⡯ ��ư�� Ŭ���Ͽ� �߼��� ��û�� �߼۴�� �Ǽ� �Դϴ�.</td>
</tr>
<tr>
	<td><img src="../img/icon_list.gif" align="absmiddle" />�߼ۼ���: ��û�� �߼۴�󿡰� ������ �߼ۿ� ������ �Ǽ� �Դϴ�.</td>
</tr>
<tr>
	<td><img src="../img/icon_list.gif" align="absmiddle" />�߼ۿ�û����: ��û�� â�� �ݰų� PC�� ������ ���� �������� �߼ۿ�û�� �Ϸ���� ���� �Ǽ��Դϴ�.</td>
</tr>
<tr>
	<td><img src="../img/icon_list.gif" align="absmiddle" />�߼۽���: ��û�� �߼۴�󿡰� ������ �߼��� �Ͽ�����, �߸��� ��ȭ��ȣ ���� �������� �߼ۿ� ������ �Ǽ��Դϴ�.</td>
</tr>
<tr>
	<td style="padding-left: 50px;">�� �߼۽��� �Ǽ��� �Ϸ翡 �ѹ� ���� 1�ð濡 ����Ǿ� ����Ʈ�� �������˴ϴ�.</td>
</tr>
<tr>
	<td><img src="../img/icon_list.gif" align="absmiddle" />������ �߼� �ý��ۻ� ������ �ƴ� ��Ż� ������å �� ��Ÿ ������ ���� ���ڹ߼� ���п� ���� ������ å���� ������, �� ��Ż翡 ����Ȯ���� ���θ��� �����մϴ�.</td>
</tr>
<tr>
	<td><img src="../img/icon_list.gif" align="absmiddle" />�߽Ź�ȣ�� ���� ��ϵ��� ������ SMS�� �߼۵��� �ʽ��ϴ�. <a href="http://www.godo.co.kr/news/notice_view.php?board_idx=1247&page=2
" target="_blank"><font color=white><u>�߽Ź�ȣ ��������� �ȳ�</u></font></a></td>
</tr>
</table>
</div>
<script>cssRound('MSG01');</script>

<?php include "../_footer.php"; ?>