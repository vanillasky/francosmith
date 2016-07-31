<?php
$location = 'SMS설정 > SMS 발송내역';
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

//예약발송건 발송중 상태 업데이트
$sms_sendlist->updateReserveSendingAll();

//검색 - 구분
if ($_GET['reserve'] == 'now') { 
	$where[] = " (reservedt = '0000-00-00 00:00:00' or reservedt = '') ";
} 
else if ($_GET['reserve'] == 'reserve') {
	$where[] = " (reservedt != '0000-00-00 00:00:00' and reservedt != '')";
} 
else {
	$_GET['reserve'] = '';
}

//검색 - 발송(예약) 기간
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

//검색 - 발송상태
if($_GET['status']){
	$where[] = "status='" . $_GET['status'] . "'";
}

/*
* 검색 - 발송실패 여부
* send_fail_check : y - 발송실패건있음, n - 발송실패건없음
* accept_fail_check : y - 접수실패건있음, n - 접수실패건없음
*/
if ($_GET['sms_status'] == 'y') {
	$where[] = "accept_fail_check='y'";
}
else if($_GET['sms_status'] == 'n') {
	$where[] = "send_fail_check = 'y'";
}

//검색 - 수신번호 
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
			alert('수신번호는 8자 이상이어야 합니다.');
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
.sendList-Bg					{ background-color: #4F4F4F; font:8pt 돋움; height:30px; color:#ffffff; }
.sendList-line					{ background-color: #DCD8D6; height: 1px; }
.sendList-TextAlignL			{ text-align: left; }
.sendList-TextAlignR			{ text-align: right; }
.sendList .ColorTr td			{ color: red; }
.sendList .sendListSubTh 		{ font:8pt 돋움; height:30px; color:#ffffff; }
.sendList .sendListSubTd 		{ color:#262626; font-family:Tahoma,Dotum; font-size:11px; text-align: center; }
.imgLink						{ border: 0px; cursor: pointer; }
</style>

<div class="title title_top">
	<font face="굴림" color="black"><strong>SMS</strong></font> 발송내역<span>전송된 SMS 발송내역을 관리합니다</span> 
	<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=member&no=18')"><img src="../img/btn_q.gif" align="absmiddle" hspace="2" /></a>
</div>

<table border="4" width="730" class="sendList-guide" cellspacing="0" cellpadding="0">
<tr>
	<td>
		<div class="guide-title">※ SMS 발송내역 확인 안내</div>
		<div class="g9">① 발송완료된 건수만 포인트차감되며, 발송실패된 건수는 하루에 한번 새벽 1시경에 정산되어 사용된 포인트가 반환됩니다.</div>
		<div class="g9">② 성공, 실패 내역 및 발송상태는 예약정보 혹은 발송결과 버튼을 클릭하면 조회하실 수 있습니다.</div>
		<div class="g9 divPaddingL">[발송요청실패 혹은 발송실패건이 있으면 리스트에 붉은색으로 표시됩니다.]</div>
		<div class="g9">③ 보다 정확한 SMS 발송내역 데이터는 고도몰에 로그인 하신 후, 마이고도에서 다운로드가 가능합니다.</div> 
		<div class="g9 divPaddingL">메뉴 : 고도몰 로그인 > 마이고도 > 나의 쇼핑몰 > [상세정보/관리] 클릭 > SMS 발송 내역에서 다운로드</div>
		<div class="g9 divPaddingL divLink"><a href="http://www.godo.co.kr/mygodo/index.html" target="_blank">[마이고도 바로가기 > ]</a></div>
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
	<td>구분</td>
	<td class="noline">
		<label><input type="radio" name="reserve" value="" <?php echo $checked['reserve']['']; ?> onclick="javascript:checkReserve();" />전체</label>
		<label><input type="radio" name="reserve" value="now" <?php echo $checked['reserve']['now']; ?> onclick="javascript:checkReserve();" />즉시발송</label>
		<label><input type="radio" name="reserve" value="reserve" <?php echo $checked['reserve']['reserve']; ?> onclick="javascript:checkReserve();" />예약발송</label>
	</td>
</tr>
<tr>
	<td>발송(예약)기간</td>
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
	<td>발송상태</td>
	<td class="noline">
		<label><input type="radio" name="status" value="" <?php echo $checked['status']['']; ?> />전체</label>
		<label><input type="radio" name="status" value="4" <?php echo $checked['status']['4']; ?> />발송완료</label>
		<label><input type="radio" name="status" value="3" <?php echo $checked['status']['3']; ?> />결과수신대기&nbsp;<img src="../img/icons/icon_qmark.gif" style="vertical-align:bottom; cursor:pointer; border: 0px;" class="godo-tooltip" tooltip="결과수신대기는 현재 메시지를 발송중 이거나, 발송완료 후 결과값을 수신하기 위해<br />대기중인 상태입니다. 발송결과를 클릭하면 결과값을 수신할 수 있습니다."> </label>
		<label><input type="radio" name="status" value="1" <?php echo $checked['status']['1']; ?> />발송대기</label>
		<label><input type="radio" name="status" value="2" <?php echo $checked['status']['2']; ?> />예약취소</label>
	</td>
</tr>
<tr>
	<td>발송실패건 조회</td>
	<td class="noline">
		<label><input type="radio" name="sms_status" value="" <?php echo $checked['sms_status']['']; ?> />전체</label>
		<label><input type="radio" name="sms_status" value="y" <?php echo $checked['sms_status']['y']; ?> />발송요청실패</label>
		<label><input type="radio" name="sms_status" value="n" <?php echo $checked['sms_status']['n']; ?> />발송실패</label>
	</td>
</tr>
<tr>
	<td>수신번호</td>
	<td><input type="text" name="sms_phoneNumber" value="<?=$_GET['sms_phoneNumber']?>" class="line" />&nbsp;<span class="guideFont">ex) 010-1111-1111</span></td>
</tr>
</table>
<div class="button_top"><input type="image" src="../img/btn_search2.gif" /></div>


<table width="100%" cellpadding="0" cellspacing="0" border="0" class="sendList-total">
<tr>
	<td class="sendList-TextAlignL">총 <?php echo $total; ?>개, 검색 <?php echo $pg->recode['total']; ?>개 / <?php echo $pg->page['total']; ?> of <?php echo $pg->page['now']; ?> Pages</td>
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
	<th>번호</th>
	<th>구분</th>
	<th>발송시간/<br />발송예약시간</th>
	<th>제목+메시지</th>
	<th>발송형태</th>
	<th>발송요청</th>
	<th>발송성공</th>
	<th>
		<table cellpadding="0" cellspacing="0" width="100%" class="sendListSubTh">
		<colgroup>
			<col width="50%" />
			<col width="50%" />
		</colgroup>
		<tr>
			<th colspan="2">실패</th>
		</tr>
		<tr>
			<th>요청실패</th>
			<th>발송실패</th>
		</tr>
		</table>
	</th>
	<th>발송상태</th>
	<th>자세히</th>
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
		$zoomButton = "<img src='../img/btn_sendlist_result_info.gif' class='imgLink' onclick=\"javascript:alert('[sms]패치 이후 발송건들만 발송결과를 확인할 수 있습니다.');\" />";
	}
	else {
		$zoomButton = "<img src='../img/btn_sendlist_result_info.gif' class='imgLink' onclick=\"javascript:popup('./popup.sms.sendList.php?sms_logNo=" . $data['sno'] . "&apiPermission=y', '800', '750')\" />";
	}

	if($data['reservedt'] != '0000-00-00 00:00:00' && $data['reservedt'] != '') {
		$sendTime = $data['reservedt'];
		$reserveType = '예약발송';

		if($data['status'] == '1' || $data['status'] == '2') $zoomButton = "<img src='../img/btn_sendlist_reserve_info.gif' class='imgLink' onclick=\"javascript:popup('./popup.sms.reserveList.php?sms_logNo=" . $data['sno'] . "', '800', '700')\" />";		
	}
	else {
		$sendTime = $data['regdt'];
		$reserveType = '즉시발송';
	}

	//발송성공
	list($smsSendSuccessCnt) = $db->fetch(" SELECT COUNT(*) FROM " . GD_SMS_SENDLIST . " WHERE sms_logNo='" . $data['sno'] . "' and sms_status='y' and sms_send_status='y' ");

	//발송실패
	list($smsSendFailCnt) = $db->fetch(" SELECT COUNT(*) FROM " . GD_SMS_SENDLIST . " WHERE sms_logNo='" . $data['sno'] . "' and sms_status='n' and sms_send_status='y' ");

	//접수실패
	list($smsAcceptFailCnt) = $db->fetch(" SELECT COUNT(*) FROM " . GD_SMS_SENDLIST . " WHERE sms_logNo='" . $data['sno'] . "' and sms_send_status='n' ");

	//접수실패 sms log update
	if($smsAcceptFailCnt > 0 && $data['accept_fail_check'] != 'y'){
		$sms_sendlist->updateSmsLogAcceptFail($data['sno']);
		$data['accept_fail_check'] = 'y';
	}
	//발송실패 sms log update
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
	<td>발송성공 건수만 포인트가 차감됩니다.</td>
</tr>
<tr>
	<td>발송결과를 클릭하면 실제 발송된 내역을 확인할 수 있습니다.</td>
</tr>
<tr>
	<td style="padding-top: 10px;"><strong>[발송내역 항목 설명]</strong></td>
</tr>
<tr>
	<td><img src="../img/icon_list.gif" align="absmiddle" />발송요청: 발송대상을 선택 후 ‘보내기’ 버튼을 클릭하여 발송을 요청한 발송대상 건수 입니다.</td>
</tr>
<tr>
	<td><img src="../img/icon_list.gif" align="absmiddle" />발송성공: 요청된 발송대상에게 실제로 발송에 성공한 건수 입니다.</td>
</tr>
<tr>
	<td><img src="../img/icon_list.gif" align="absmiddle" />발송요청실패: 요청중 창을 닫거나 PC가 꺼지는 등의 원인으로 발송요청이 완료되지 않은 건수입니다.</td>
</tr>
<tr>
	<td><img src="../img/icon_list.gif" align="absmiddle" />발송실패: 요청된 발송대상에게 실제로 발송을 하였으나, 잘못된 전화번호 등의 원인으로 발송에 실패한 건수입니다.</td>
</tr>
<tr>
	<td style="padding-left: 50px;">※ 발송실패 건수는 하루에 한번 새벽 1시경에 정산되어 포인트가 재충전됩니다.</td>
</tr>
<tr>
	<td><img src="../img/icon_list.gif" align="absmiddle" />고도몰의 발송 시스템상 오류가 아닌 통신사 스팸정책 등 기타 사유에 의한 문자발송 실패에 대해 고도몰은 책임이 없으며, 각 통신사에 사유확인은 본인만이 가능합니다.</td>
</tr>
<tr>
	<td><img src="../img/icon_list.gif" align="absmiddle" />발신번호가 사전 등록되지 않으면 SMS가 발송되지 않습니다. <a href="http://www.godo.co.kr/news/notice_view.php?board_idx=1247&page=2
" target="_blank"><font color=white><u>발신번호 사전등록제 안내</u></font></a></td>
</tr>
</table>
</div>
<script>cssRound('MSG01');</script>

<?php include "../_footer.php"; ?>