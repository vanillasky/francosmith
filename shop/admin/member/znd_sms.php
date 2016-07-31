<?
require_once "../../lib/sms.class.php";

ignore_user_abort(false);
set_time_limit(0);
ini_set("memory_limit", -1);

$sms = new Sms(true);
register_shutdown_function(array($sms, 'registerSmsShutdownLog'));
if(!$sms_sendlist) $sms_sendlist = $sms->loadSendlist();

$idx = $pre_perc = $ici_perc = 0;

# SMS 내용 처리
$msg = parseCode($_POST['msg']);

# SMS 분할 발송 설정
$msg = gd_str_split($msg,90);
$msg_size = $_POST['msg_split'] ? sizeof($msg) : 1;

# 예약 발송
if ($_POST['reserve'] == 1) {
	$time = strtotime($_POST['reserve_date']) + (int)$_POST['reserve_hour'] * 3600 + (int)$_POST['reserve_minute'] * 60;

	if(strtotime("+10 minute", time()) > $time){
		msg("예약은 현재시간 10분 후에 가능합니다.");
		exit;
	}

	if ($time <= time()) {
		msg("예약발송 일시를 현재 이후로 설정해 주세요.");
		exit;
	}

	$overLapCnt = $sms_sendlist->checkOverlapReserve('sms', $time, $msg, '');
	if($overLapCnt > 0){
		msg("중복된 내용, 일시의 예약발송건이 존재합니다.");
		exit;
	}

	$reserve = date('Y-m-d H:i:s', $time);
	$reserve_etc = date('Ymd', $time);
	$send_type = 'res_send';
}
else {
	$reserve = '';
	$reserve_etc = '';
	$send_type = 'send';
}

$sms->log($_POST['msg'], $to_tran, $_POST['type'], $total, $reserve);
$sendlistKey				= 0;
$sendlistInfo				= array();
$sms_sendlist->sms_logNo	= $sms->smsLogInsertId; //sms log insert id
$sms_sendlist->sms_mode		= $sms_sendlist->getSms_mode($_POST['reserve']); //발송종류(예약, 즉시)

# 개별 발송
if ($_POST['type']==1) {

	foreach ($div as $v){
		$sendlistInfo[$sendlistKey]['phone']		= $v;
		list($sendlistInfo[$sendlistKey]['sms_name'], $sendlistInfo[$sendlistKey]['sms_memNo']) = $sms_sendlist->getMember($v); //이름, 회원번호
		$sendlistKey++;
	}

# SMS 회원 주소록 검색 / 선택회원
} else if ($_POST['type']==2 || $_POST['type']==3 || $_POST['type']=="query" || $_POST['type']=="select") {

	while ($v = $db->fetch($res)){
		$sendlistInfo[$sendlistKey]['phone']		= $v['mobile'];
		$sendlistInfo[$sendlistKey]['sms_name']		= $v['name'];
		$sendlistInfo[$sendlistKey]['sms_memNo']	= $v['m_no'];
		$sendlistKey++;
	}

# SMS 일반 주소록 검색 / 선택회원
} else if ($_POST['type']==4 || $_POST['type']==5) {

	while ($v = $db->fetch($res)){
		$sendlistInfo[$sendlistKey]['phone']		= $v['sms_mobile'];
		$sendlistInfo[$sendlistKey]['sms_name']		= $v['sms_name'];
		$sendlistKey++;
	}

# SMS 회원 주소록 전체
} else if ($_POST['type']==6) {

	while ($v = $db->fetch($res)){
		$sendlistInfo[$sendlistKey]['phone']		= $v['mobile'];
		$sendlistInfo[$sendlistKey]['sms_name']		= $v['name'];
		$sendlistInfo[$sendlistKey]['sms_memNo']	= $v['m_no'];
		$sendlistKey++;
	}

# SMS 일반 주소록 전체
} else if ($_POST['type']==7) {

	while ($v = $db->fetch($res)){
		$sendlistInfo[$sendlistKey]['phone']		= $v['sms_mobile'];
		$sendlistInfo[$sendlistKey]['sms_name']		= $v['sms_name'];
		$sendlistKey++;
	}

//SMS 발송결과 재전송 - 선택, 검색
} else if ($_POST['type'] == 8 || $_POST['type'] == 9) {

	while ($v = $db->fetch($res)){
		$sendlistInfo[$sendlistKey]['phone']		= $v['sms_phoneNumber'];
		$sendlistInfo[$sendlistKey]['sms_name']		= $v['sms_name'];
		$sendlistKey++;
	}
}
//SMS SENDLIST insert
$sms_sendlist->setListInsert($sendlistInfo);

//SMS발송
$num = array();
foreach($sendlistInfo as $data){
	$sms->send($msg[0], $data['phone'], $_POST['callback'], $reserve, $reserve_etc, $send_type);

	$ici_perc = floor(++$idx / $total * 100);
	if ($pre_perc!=$ici_perc){
		echo "<script>if((typeof parent.smsLoadingCount == 'function' || typeof parent.smsLoadingCount == 'object') && typeof parent.smsLoadingCount != null){ parent.smsLoadingCount('".$ici_perc."'); }</script>";
		echo "<script>parent.document.getElementById('sms_bar').style.width = '".($ici_perc)."%';</script>";
		ob_flush();
		flush();
		$pre_perc = $ici_perc;
	}
}
$sms->update_ok_eNamoo = true;
$sms->update();
$num = $sms->countNum;
unset($sms->update_ok_eNamoo);
?>