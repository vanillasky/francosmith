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
	msg('올바른 접속이 아닙니다.', 'close');
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

//검색 - 발송결과
switch($_GET['sms_status']){
	case 'y' : case 'n' : case 'r' : case 'c' :
		$where[] = "a.sms_status = '" . $_GET['sms_status'] . "' and a.sms_send_status = 'y'";
		if($_GET['sms_status'] == 'n' && $_GET['sms_failCode']) $where[] = "a.sms_failCode = '".$_GET['sms_failCode']."'";
	break;

	case 'acceptN' :
		$where[] = "a.sms_status = 'r'and a.sms_send_status = 'n'";
	break;
}

//검색 - 이름
if ($_GET['sms_name']) {
	$where[] = "a.sms_name like '%" . $_GET['sms_name'] . "%'";
}

//검색-수신번호
if ($_GET['sms_phoneNumber']) {
	$where[] = "a.sms_phoneNumber like '%" . $_GET['sms_phoneNumber'] . "%'";
}

$pg = new Page($_GET['page'], $_GET['page_num']);
$pg->field = " a.*, b.dormant_regDate ";
$pg->setQuery($db_table ,$where ,"a.sms_no desc");
$pg->exec();
$result = $db->query($pg->query);

list ($total) = $db->fetch("SELECT COUNT(*) FROM " . GD_SMS_SENDLIST . " WHERE " . $defaultWhere);

//sms_log 정보
$smsLog = $db->fetch("SELECT * FROM " . GD_SMS_LOG . " WHERE sno='" . $_GET['sms_logNo'] . "' LIMIT 1");

//발송시간
if($smsLog['reservedt'] != '0000-00-00 00:00:00' && $smsLog['reservedt'] != ''){
	$headInfo['sendTime'] = $smsLog['reservedt'];
}
else{
	$headInfo['sendTime'] = $smsLog['regdt'];
}

//발송상태
$headInfo['status'] = $sms->getLogStatus($smsLog['status']);

//발송건수 (실제접수건수)
list($headInfo['smsAcceptSuccessCnt']) = $db->fetch("SELECT COUNT(*) FROM " . GD_SMS_SENDLIST . " WHERE sms_send_status = 'y' and sms_status != 'c' and " . $defaultWhere);

//발송요청 실패
list($headInfo['smsAcceptFailCnt']) = $db->fetch("SELECT COUNT(*) FROM " . GD_SMS_SENDLIST . " WHERE sms_send_status = 'n' and " . $defaultWhere);

//예약취소
list($headInfo['smsCancelCnt']) = $db->fetch("SELECT COUNT(*) FROM " . GD_SMS_SENDLIST . " WHERE sms_send_status = 'y' and sms_status='c' and " . $defaultWhere);

//발송결과 - 성공
list ($headInfo['smsSendSuccessCnt']) = $db->fetch("SELECT COUNT(*) FROM " . GD_SMS_SENDLIST . " WHERE sms_send_status = 'y' and sms_status = 'y' and " . $defaultWhere);

//발송결과 - 실패
list ($headInfo['smsSendFailCnt']) = $db->fetch("SELECT COUNT(*) FROM " . GD_SMS_SENDLIST . " WHERE sms_send_status = 'y' and sms_status = 'n' and " . $defaultWhere);

//사용포인트
list ($headInfo['smsUsePoint']) = $db->fetch("SELECT COUNT(*) FROM " . GD_SMS_SENDLIST . " WHERE sms_send_status = 'y' and (sms_status = 'y' || sms_status = 'r') and " . $defaultWhere);

//발송결과 - 결과수신대기
list ($headInfo['smsSendReadyCnt']) = $db->fetch("SELECT COUNT(*) FROM " . GD_SMS_SENDLIST . " WHERE sms_send_status = 'y' and sms_status = 'r' and " . $defaultWhere);

$smsErrorCode = $sms_sendlist->errorCodeList();

//타입
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
	alert("정보를 불러올 수 없습니다.\n잠시 후 다시 발송결과 버튼을 클릭하여 조회해주세요.");
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
			alert('발송할 대상을 선택해 주세요.');
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
			alert('수신번호는 8자 이상이어야 합니다.');
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
	<font face="굴림" color="black"><strong>SMS</strong></font> 발송결과 상세<span>SMS 발송결과를 조회할 수 있습니다.</span>
</div>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
	<td class="sendList-smsLayout"><?php include './popup.sms.layout.php'; ?></td>
	<td class="sendList-contents">
		<form name="sendList_SearchForm" id="sendList_SearchForm" method="GET" onsubmit="return formCheck(this);">
		<input type="hidden" name="sms_logNo" value="<?php echo $_GET['sms_logNo']; ?>" />

		<!-- 발송정보 -->
		<div class="sendList-smsInfo-title">▼&nbsp;발송정보</div>
		<table width="100%" id="sendList-smsInfo" class="tb">
		<colgroup>
			<col width="25%" />
			<col width="25%" />
			<col width="25%" />
			<col width="25%" />
		</colgroup>
		<tr>
			<th>발송시간</th>
			<th>타입</th>
			<th>발송상태</th>
			<th>사용포인트</th>
		</tr>
		<tr>
			<td><?php echo $headInfo['sendTime']; ?></td>
			<td><?php echo $headInfo['type']; ?></td>
			<td><?php echo $headInfo['status']; ?></td>
			<td><?php echo number_format($headInfo['smsUsePoint']); ?></td>
		</tr>
		</table>
		<div class="sendList-smsInfo-warning">※ 발송실패 건수는 하루에 한번 새벽 1시경에 정산되어 포인트가 재충전됩니다. </div>

		<!-- 발송결과 -->
		<div class="sendList-smsInfo-title">▼&nbsp;발송결과</div>
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
			<th rowspan="2">발송요청</th>
			<th rowspan="2">발송요청실패</th>
			<th rowspan="2">예약취소</th>
			<th rowspan="2">발송건수</th>
			<th colspan="3">발송결과</th>
		</tr>
		<tr>
			<th>발송성공</th>
			<th>발송실패</th>
			<th>결과수신대기</th>
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
		<div class="sendList-smsInfo-warning">※ 발송요청실패, 발송실패건 등 발송결과 별로 검색 후 리스트 하단의 ‘SMS 작성’을 통해 메시지를 재발송 할 수 있습니다.</div>

		<table width="100%" id="sendList-search" class="tb">
		<colgroup>
			<col class="cellC" />
			<col class="cellL" />
			<col class="cellC" />
			<col class="cellL" />
		</colgroup>
		<tr>
			<td>결과</td>
			<td>
				<select name="sms_status" id="sms_status" onchange="javascript:chgSmsFailCode();">
					<option value=""> = 전체 =</option>
					<option value="y" <?php echo $selected['sms_status']['y']; ?>>발송성공</option>
					<option value="n" <?php echo $selected['sms_status']['n']; ?>>발송실패</option>
					<option value="r" <?php echo $selected['sms_status']['r']; ?>>결과수신대기</option>
					<option value="acceptN" <?php echo $selected['sms_status']['acceptN']; ?>>발송요청실패</option>
					<option value="c" <?php echo $selected['sms_status']['c']; ?>>예약취소</option>
				</select>
			</td>
			<td>실패사유</td>
			<td>
				<select name="sms_failCode" id="sms_failCode" disabled>
					<option value="" <?php echo $selected['sms_failCode']['']; ?>> - 전체 - </option>
					<?php foreach($smsErrorCode as $code => $value){ ?>
					<option value="<?php echo $code; ?>" <?php echo $selected['sms_failCode'][$code]; ?>><?php echo $value; ?></option>
					<? } ?>
				<select>
			</td>
		</tr>
		<tr>
			<td>이름</td>
			<td colspan="3"><input type="text" class="line" name="sms_name" value="<?php echo $_GET['sms_name']; ?>" /></td>
		</tr>
		<tr>
			<td>수신번호</td>
			<td colspan="3"><input type="text" class="line" name="sms_phoneNumber" value="<?php echo $_GET['sms_phoneNumber']; ?>" />&nbsp;<span class="guideFont">ex) 010-1111-1111</span></td>
		</tr>
		</table>
		<div class="button_top"><input type="image" src="../img/btn_search2.gif" class="inputBorder" /></div>

		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="sendList-total">
		<tr>
			<td class="sendList-TextAlignL">총 <?php echo number_format($total); ?>개, 검색 <?php echo number_format($pg->recode['total']); ?>개 / <?php echo number_format($pg->page['total']); ?> of <?php echo number_format($pg->page['now']); ?> Pages</td>
			<td class="sendList-TextAlignR">
				<select name="page_num" onchange="javascript:this.form.submit();">
					<option value="10" <?php echo $selected['page_num'][10]; ?>>10개 출력</option>
					<option value="20" <?php echo $selected['page_num'][20]; ?>>20개 출력</option>
					<option value="40" <?php echo $selected['page_num'][40]; ?>>40개 출력</option>
					<option value="60" <?php echo $selected['page_num'][60]; ?>>60개 출력</option>
					<option value="100" <?php echo $selected['page_num'][100]; ?>>100개 출력</option>
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
			<th><a href="javascript:;" onclick="chkBox(document.getElementsByName('chk[]'),'rev');" />선택</a></th>
			<th>번호</th>
			<th>핸드폰도착시간</th>
			<th>이름</th>
			<th>수신번호</th>
			<th>결과</th>
			<th>실패사유</th>
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
				$data['sms_name'] = '휴면회원';
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
			<option value="8">선택한 대상에게 SMS 보내기</option>
			<option value="9">검색한 대상에게 SMS 보내기</option>
		</select>
	</td>
	<td width="43%" class="sendList-btn-td2"><a href="javascript:void(0);" onClick="javascript:sendSMS('y', '<?php echo $smsLog['sno']; ?>');"><img src="../img/btn_today_email_sm.gif" border="0" /></td>
</tr>
</table>

<table cellpadding="0" cellspacing="2" width="100%" border="0" style="margin: 0px 0px 5px 0px; border: 3px #dce1e1 solid; padding: 5px;">
<tr>
	<td style="color: red; font-weight: bold;">
		※ 정보통신망법의 기준에 따라 SMS수신여부에 수신동의를 하지 않은 회원들에게는 광고성 정보 SMS를 발송할 수 없습니다.<br />
		<span style="margin-left: 15px;">반드시 SMS수신여부 상태를 확인 후 SMS를 발송해 주세요.</span>
		<br /><br />
		※ 정보통신망법의 기준에 따라, 1년이상 서비스 이용 기록이 없는 회원(휴면회원) 에게 광고성 정보 SMS를 발송할 수 없습니다.<br />
		<span style="margin-left: 15px;">검색한 대상 중 휴면회원이 포함 된 경우 제외 후 SMS를 발송하시기 바랍니다.</span>
	</td>
</tr>
</table>

<div id="MSG01">
<table cellpadding=1 cellspacing=0 border="0" class="small_ex">
<tr>
	<td><strong>[항목 설명]</strong></td>
</tr>
<tr>
	<td><img src="../img/icon_list.gif" align="absmiddle" />발송요청: 발송대상을 선택 후 ‘보내기’ 버튼을 클릭하여 발송을 요청한 발송대상 건수 입니다.</td>
</tr>
<tr>
	<td><img src="../img/icon_list.gif" align="absmiddle" />발송요청실패: 요청중 창을 닫거나 PC가 꺼지는 등의 원인으로 발송요청이 완료되지 않은 건수입니다.</td>
</tr>
<tr>
	<td><img src="../img/icon_list.gif" align="absmiddle" />결과수신대기 : 현재 메시지를 발송중 이거나, 발송완료 후 결과값을 수신하기 위해 대기중인 건수입니다.</td>
</tr>
<tr>
	<td><img src="../img/icon_list.gif" align="absmiddle" />예약취소: 예약발송을 요청한 상태에서 예약발송을 취소한 건수입니다.</td>
</tr>
<tr>
	<td><img src="../img/icon_list.gif" align="absmiddle" />발송건수: 요청된 발송대상에게 실제로 발송한 메시지 건수이며, 발송성공과 실패 결과를 확인할 수 있습니다.</td>
</tr>
</table>
</div>

<script>
cssRound('MSG01');
table_design_load();
</script>