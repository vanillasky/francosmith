<?
require_once "../../lib/lms.class.php";

ignore_user_abort(false);
set_time_limit(0);
ini_set("memory_limit", -1);

$lms = new Lms(true);
register_shutdown_function(array($lms, 'registerSmsShutdownLog'));
if(!$sms_sendlist) $sms_sendlist = $sms->loadSendlist();

$idx = $pre_perc = $ici_perc = 0;

# LMS ���� ó��
$_POST['subject'] = $_POST['lms_subject'];
$_POST['msg'] = $_POST['lms_msg'];
$subject = parseCode($_POST['subject']);
$msg = parseCode($_POST['msg']);

$msg_size = 3;

# ���� �߼�
if ($_POST['reserve'] == 1) {
	$time = strtotime($_POST['reserve_date']) + (int)$_POST['reserve_hour'] * 3600 + (int)$_POST['reserve_minute'] * 60;

	if(strtotime("+10 minute", time()) > $time){
		msg("������ ����ð� 10�� �Ŀ� �����մϴ�.");
		exit;
	}

	if ($time <= time()) {
		msg("����߼� �Ͻø� ���� ���ķ� ������ �ּ���.");
		exit;
	}

	$overLapCnt = $sms_sendlist->checkOverlapReserve('lms', $time, $msg, $subject);
	if($overLapCnt > 0){
		msg("�ߺ��� ����, �Ͻ��� ����߼۰��� �����մϴ�.");
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

$lms->log($_POST['msg'], $to_tran, $_POST['type'], $total, $reserve, $_POST['subject']);
$sendlistKey				= 0;
$sendlistInfo				= array();
$sms_sendlist->sms_logNo	= $lms->smsLogInsertId; //sms log insert id
$sms_sendlist->sms_mode		= $sms_sendlist->getSms_mode($_POST['reserve']); //�߼�����(����, ���)

# ���� �߼�
if ($_POST['type']==1) {

	foreach ($div as $v){
		$sendlistInfo[$sendlistKey]['phone']		= $v;
		list($sendlistInfo[$sendlistKey]['sms_name'], $sendlistInfo[$sendlistKey]['sms_memNo']) = $sms_sendlist->getMember($v); //�̸�, ȸ����ȣ
		$sendlistKey++;
	}

# SMS ȸ�� �ּҷ� �˻� / ����ȸ��
} else if ($_POST['type']==2 || $_POST['type']==3 || $_POST['type']=="query" || $_POST['type']=="select") {

	while ($v = $db->fetch($res)){
		$sendlistInfo[$sendlistKey]['phone']		= $v['mobile'];
		$sendlistInfo[$sendlistKey]['sms_name']		= $v['name'];
		$sendlistInfo[$sendlistKey]['sms_memNo']	= $v['m_no'];
		$sendlistKey++;
	}

# SMS �Ϲ� �ּҷ� �˻� / ����ȸ��
} else if ($_POST['type']==4 || $_POST['type']==5) {

	while ($v = $db->fetch($res)){
		$sendlistInfo[$sendlistKey]['phone']		= $v['sms_mobile'];
		$sendlistInfo[$sendlistKey]['sms_name']		= $v['sms_name'];
		$sendlistKey++;
	}

# SMS ȸ�� �ּҷ� ��ü
} else if ($_POST['type']==6) {

	while ($v = $db->fetch($res)){
		$sendlistInfo[$sendlistKey]['phone']		= $v['mobile'];
		$sendlistInfo[$sendlistKey]['sms_name']		= $v['name'];
		$sendlistInfo[$sendlistKey]['sms_memNo']	= $v['m_no'];
		$sendlistKey++;
	}

# SMS �Ϲ� �ּҷ� ��ü
} else if ($_POST['type']==7) {

	while ($v = $db->fetch($res)){
		$sendlistInfo[$sendlistKey]['phone']		= $v['sms_mobile'];
		$sendlistInfo[$sendlistKey]['sms_name']		= $v['sms_name'];
		$sendlistKey++;
	}

//SMS �߼۰�� ������ - ����, �˻�
}  else if ($_POST['type'] == 8 || $_POST['type'] == 9) {

	while ($v = $db->fetch($res)){
		$sendlistInfo[$sendlistKey]['phone']		= $v['sms_phoneNumber'];
		$sendlistInfo[$sendlistKey]['sms_name']		= $v['sms_name'];
		$sendlistKey++;
	}

}
//SMS SENDLIST insert
$sms_sendlist->setListInsert($sendlistInfo);

//SMS�߼�
$num = array();
$totalLms = floor($total/3);
foreach($sendlistInfo as $data){
	$lms->send($msg, $data['phone'], $_POST['callback'], $reserve, $reserve_etc, $send_type, $subject);

	$ici_perc = floor(++$idx / $totalLms * 100);
	if ($pre_perc!=$ici_perc){
		echo "<script>if((typeof parent.smsLoadingCount == 'function' || typeof parent.smsLoadingCount == 'object') && typeof parent.smsLoadingCount != null){ parent.smsLoadingCount('".$ici_perc."'); }</script>";
		echo "<script>parent.document.getElementById('sms_bar').style.width = '".($ici_perc)."%';</script>";
		ob_flush();
		flush();
		$pre_perc = $ici_perc;
	}
}

$lms->update_ok_eNamoo = true;
$lms->update();
$num = $lms->countNum;
unset($lms->update_ok_eNamoo);
?>