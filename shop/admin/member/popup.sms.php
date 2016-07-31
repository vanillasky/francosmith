<?
include "../_header.popup.php";
include '../../lib/sms_sendlist.class.php';
$sms_sendlist = new sms_sendlist();

$_POST[type] = isset($_POST[type]) ? $_POST[type] : 1;
$smsFailCnt = 0;

switch ($_POST[type]){
	case "1":				// 개별 발송

		$_POST[level] = '';
		$total = 1;

		# 회원인경우
		if ($_REQUEST['m_id']){
			if ($_REQUEST['m_id']) list ($phone) = $db->fetch("SELECT mobile FROM ".GD_MEMBER." WHERE m_id='".$_REQUEST['m_id']."' AND " . MEMBER_DEFAULT_WHERE);
			if ($_REQUEST['mobile']) $phone = $_REQUEST['mobile'];
		}
		# SMS 주소록인 경우
		if ($_REQUEST['sno']){
			list ($phone) = $db->fetch("SELECT sms_mobile FROM ".GD_SMS_ADDRESS." WHERE sno='".$_REQUEST['sno']."'");
		}

		# 직접입력
		if ($_REQUEST[mobile]) {

			$phone = $_REQUEST[mobile];
			$div = explode("\r\n",$phone);
			$div = array_notnull(array_unique($div));
			sort($div);
			$to_tran = implode(",",$div);
			$total = count($div);
		}

		if(is_array($div)){
			$phoneNumberArr = $div;
		}
		else if($phone){
			$phoneNumberArr = (array)$phone;
		}
		//전송실패번호 확인
		$failSnoArr = smsFailCheck('array', $phoneNumberArr);

		break;
	case "2":				// 주소록
		$to_tran= "SMS 회원 주소록 검색 목록";
		$query	= stripslashes($_POST['query']);
		$res	= $db->query($query);
		$total	= $db->count_($res);

		//전송실패번호 확인
		$resCnt = $db->query($query);
		while($row = $db->fetch($resCnt, 1)){
			$phoneNumberArr[] = $row['mobile'];
		}
		$failSnoArr = smsFailCheck('array', $phoneNumberArr);
		break;
	case "3":				// 주소록
		$to_tran= "SMS 회원 주소록 선택 목록";
		$where	= "m_no IN (".implode(",",$_POST['chk']).")";
		$query	= "SELECT count(m_no) FROM ".GD_MEMBER." WHERE ".$where;
		list($total) = $db->fetch($query);

		//전송실패번호 확인
		$_query = preg_replace('/count\(m_no\)/','mobile', $query);
		$resCnt = $db->query($_query);
		while($row = $db->fetch($resCnt, 1)){
			$phoneNumberArr[] = $row['mobile'];
		}
		$failSnoArr = smsFailCheck('array', $phoneNumberArr);
		break;
	case "4":				// 주소록
		$to_tran= "SMS 일반 주소록 검색 목록";
		$query	= stripslashes($_POST['query']);
		$res	= $db->query($query);
		$total	= $db->count_($res);

		//전송실패번호 확인
		$resCnt = $db->query($query);
		while($row = $db->fetch($resCnt, 1)){
			$phoneNumberArr[] = $row['sms_mobile'];
		}
		$failSnoArr = smsFailCheck('array', $phoneNumberArr);
		break;
	case "5":				// 주소록
		$to_tran= "SMS 일반 주소록 선택 목록";
		$where	= "sno IN (".implode(",",$_POST['chk']).")";
		$query	= "SELECT count(sno) FROM ".GD_SMS_ADDRESS." WHERE ".$where;
		list($total) = $db->fetch($query);

		//전송실패번호 확인
		$_query = preg_replace('/count\(sno\)/','sms_mobile', $query);
		$resCnt = $db->query($_query);
		while($row = $db->fetch($resCnt, 1)){
			$phoneNumberArr[] = $row['sms_mobile'];
		}
		$failSnoArr = smsFailCheck('array', $phoneNumberArr);
		break;
	case "6":				// 주소록
		$to_tran= "SMS 회원 주소록 전체";
		$where[] = "sms='y'";
		$where[] = "mobile!=''";
		$where[] = MEMBER_DEFAULT_WHERE;
		if ($where) $where = "where ".implode(" and ",$where);
		$query	= "select count(m_no) from ".GD_MEMBER." $where";
		list($total) = $db->fetch($query);

		//전송실패번호 확인
		$_query = preg_replace('/count\(m_no\)/','mobile', $query);
		$resCnt = $db->query($_query);
		while($row = $db->fetch($resCnt, 1)){
			$phoneNumberArr[] = $row['mobile'];
		}
		$failSnoArr = smsFailCheck('array', $phoneNumberArr);
		break;
	case "7":				// 주소록
		$to_tran= "SMS 일반 주소록 전체";
		$query	= "SELECT count(sno) FROM ".GD_SMS_ADDRESS;
		list($total) = $db->fetch($query);

		//전송실패번호 확인
		$_query = preg_replace('/count\(sno\)/','sms_mobile', $query);
		$resCnt = $db->query($_query);
		while($row = $db->fetch($resCnt, 1)){
			$phoneNumberArr[] = $row['sms_mobile'];
		}
		$failSnoArr = smsFailCheck('array', $phoneNumberArr);
		break;
	case "8": //SMS 발송결과 재전송 - 선택
		$to_tran= "SMS 발송결과 선택목록 재전송";
		$where	= "sms_no IN (" . implode(",", $_POST['chk']) . ")";
		$query	= "SELECT count(sms_no) FROM " . GD_SMS_SENDLIST . " WHERE " . $where;
		list($total) = $db->fetch($query);

		//전송실패번호 확인
		$_query	= "SELECT sms_phoneNumber FROM " . GD_SMS_SENDLIST . " WHERE " . $where;
		$resCnt = $db->query($_query);
		while($row = $db->fetch($resCnt, 1)){
			$phoneNumberArr[] = $row['sms_phoneNumber'];
		}
		$failSnoArr = smsFailCheck('array', $phoneNumberArr);
		break;
	case "9": //SMS 발송결과 재전송 - 검색
		$to_tran= "SMS 발송결과 검색목록 재전송";
		$query	= stripslashes($_POST['query']);
		$res	= $db->query($query);
		$total	= $db->count_($res);

		//전송실패번호 확인
		$_query = preg_replace('/\*/','sms_phoneNumber' ,$query);
		$_query = preg_replace('/order by[\D]+/i', '', $_query);
		$resCnt = $db->query($_query);
		while($row = $db->fetch($resCnt, 1)){
			$phoneNumberArr[] = $row['sms_phoneNumber'];
		}
		$failSnoArr = smsFailCheck('array', $phoneNumberArr);
		break;
}

//메시지 재설정
if($_POST['msgReload'] == 'y'){
	list($reSmsType, $reSubject, $reMsg) = $db->fetch("SELECT sms_type, subject, msg FROM " . GD_SMS_LOG . " WHERE sno = '" . $_POST['reLogSno'] . "' LIMIT 1");
}

//전송실패 갯수
$smsFailCnt = count($failSnoArr);

//sms faillist pk list
$smsFailSnoList = implode("|", $failSnoArr);

//실패사유
if($smsFailCnt == 1 && $total == 1){
	$smsFailCode = $sms_sendlist->getFailList_failCode($smsFailSnoList);
	$smsErrorCode = $sms_sendlist->errorCodeList();
	$errorType = $smsErrorCode[$smsFailCode];
}

//수신거부목록 확인 [회원 주소록 검색목록, 회원 주소록 선택 목록]
$smsReceiveRefuseCount = 0;
if(in_array($_POST[type], array('2', '3'))){
	$receiveRefuseQuery = '';
	$whereType = (preg_match('/where/i', $query)) ? ' AND ' : ' WHERE ';
	$receiveRefuseQuery = preg_replace('/(order)(\s|\w|\D|\d)+/i', '', $query) . $whereType . "sms = 'n'";

	if($_POST[type] == '2'){
		$res = $db->query($receiveRefuseQuery);
		$smsReceiveRefuseCount = $db->count_($res);
	}
	else {
		list($smsReceiveRefuseCount) = $db->fetch($receiveRefuseQuery);
	}
}

### 분류별 수량 체크
$query = "select category,count(*) cnt from ".GD_SMS_SAMPLE." group by category";
$res = $db->query($query);
while ($data=$db->fetch($res)) $cnt[$data[category]] = $data[cnt];
?>
<script type="text/javascript" src="../godo.loading.sms.js"></script>
<script type="text/javascript">
window.onload = function(){
	var smsFailCnt	= '<?php echo $smsFailCnt; ?>';
	var total		= '<?php echo $total; ?>';

	if(smsFailCnt > 0){
		if(total == 1){
			document.getElementById("smsFailListInfo1").style.display = 'block';
			document.getElementById("smsFailListInfo2").style.display = 'block';
			document.getElementById("includeFail1").disabled = true;
			document.getElementById("includeFail2").disabled = true;
		}
		else if(total > 1){
			document.getElementById("smsFailListInfo1").style.display = 'block';
			document.getElementById("smsFailListInfo3").style.display = 'block';
			document.getElementById("includeFail1").disabled = false;
			document.getElementById("includeFail2").disabled = false;
		}
	}

	// SMS 수신거부 개수
	if(document.getElementById('smsReceiveRefuseCount').value > 0){
		document.getElementById("smsReceiveRefuse").style.display = 'inline-block';
	}
}

function eventStop(event){
	if(event.preventDefault){
		event.preventDefault();
	}
	else {
		event.returnValue = false;
	}
}

function chkForm2(f)
{
	var nav = navigator.userAgent.toLowerCase();

	document.onkeydown = function(e){
		var event = e || window.event;
		if(event.keyCode == 116){
			if(nav.indexOf("chrome") != -1){
				return "SMS발송 중에 새로고침을 할 경우 일부 대상에게 발송되지 않을 수 있습니다.\n계속하시겠습니까?";
				eventStop(event);
			}
			else {
				if(!confirm("SMS발송 중에 새로고침을 할 경우 일부 대상에게 발송되지 않을 수 있습니다.\n계속하시겠습니까?")){
					eventStop(event);
				}
			}
		}
	};

	window.onbeforeunload = function(e){
		var event = e || window.event;
		if(nav.indexOf("chrome") != -1){
			return "SMS발송 중에 브라우저 창을 닫을 경우 일부 대상에게 발송되지 않을 수 있습니다.\n정말 닫으시겠습니까?";
			eventStop(event);
		}
		else {
			if(!confirm("SMS발송 중에 브라우저 창을 닫을 경우 일부 대상에게 발송되지 않을 수 있습니다.\n정말 닫으시겠습니까?")){
				eventStop(event);
			}
		}
	}

	//progress bar
	nsGodoLoadingSms.init({
		psObject : $$('iframe[name="ifrmHidden"]')[0]
	});
	nsGodoLoadingSms.show();
	smsLoadingCount(0);
	return true;
}

function smsLoadingCount(PerValue){
	nsGodoLoadingSms.gogosing('SMS 발송중<br /><span style="margin-left: 20px;">'+ PerValue +'%</span>');
}

function checkReceiveRefuseForm(form)
{
	var smsReceiveRefuseCount = document.getElementById('smsReceiveRefuseCount').value;
	if(smsReceiveRefuseCount > 0){
		openLayerPopupReceiveRefuse('smsPopup');
		return false;
	}
	else {
		return chkForm2(form);
	}
}
</script>

<div class="title title_top"><font face="굴림" color="black"><b>SMS</b></font> 보내기<span>SMS문자메세지를 이용하여 고객들을 감동시키세요</span></div>

<!-- SMS 실패번호 목록 -->
<form name="failListForm" id="failListForm" method="post">
<input type="hidden" name="smsFailSnoList" value="<?php echo $smsFailSnoList; ?>" />
</form>

<form method="post" name="popupSmsForm" id="popupSmsForm" action="indb.php" target="ifrmHidden" onsubmit="return checkReceiveRefuseForm(this);">
<input type="hidden" name="mode" value="send_sms">
<input type="hidden" name="type" value="<?=$_POST['type']?>">
<input type="hidden" name="level" value="<?=$_POST['level']?>">
<input type="hidden" name="group" value="<?=$_POST['group']?>">
<input type="hidden" name="query" value="<?=(get_magic_quotes_gpc()) ? stripslashes($_POST['query']) : $_POST['query'] ?>">
<input type="hidden" name="smsFailSnoList" value="<?php echo $smsFailSnoList; ?>" />
<input type="hidden" name="smsReceiveRefuseCount" id="smsReceiveRefuseCount" value="<?php echo $smsReceiveRefuseCount; ?>" />
<input type="hidden" name="receiveRefuseType" id="receiveRefuseType" value="" />
<input type="hidden" name="totalCount" id="totalCount" value="<?php echo $total; ?>" />
<? if (is_array($_POST['chk'])) foreach($_POST['chk'] as $chk) { ?>
<input type="hidden" name="chk[]" value="<?=$chk?>">
<? } ?>
<?
	$_smsReceiverChk	= "Y";
	include "./_smsForm.php";
?>

</form>