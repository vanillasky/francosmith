<?php
@require "../lib.php";
include "../../conf/config.php";

set_time_limit(0);

if ($_POST['sms_type'] == 'lms'){
	$sms = & load_class('lms','lms');
	$msgName = 'lms_msg';
} else {
	$sms = & load_class('Sms','Sms');
	$msgName = 'sms_msg';
}
$sms_sendlist = $sms->loadSendlist();

// �߼� ���
if ($_POST['type'] == 'ts_query') {	// �˻� ��� ��ü
	$query = get_magic_quotes_gpc() ? stripslashes($_POST['query']) : $_POST['query'];
	$type = 14;
}
else {								// ����
	$query = "
	SELECT
		SC.*, MB.name
	from ".GD_TODAYSHOP_SUBSCRIBE." AS SC	/* php4 */
	LEFT JOIN ".GD_MEMBER." AS MB
	ON SC.m_id = MB.m_id
	WHERE SC.sno IN (".(implode(',',$_POST['chk'])).")
	";
	$type = 15;
}

$to_tran = $r_smsType[$type];


// �߼� ���� ���� üũ
list($total) = $db->fetch( "select count(*) ".substr($query,strpos($query, 'from'),strlen($query)) );

// SMS �߼� ���� �Ǽ�
if ($total > $sms->smsPt){
	msg("SMS �߼ۿ����� ".number_format($total)."���� �ܿ��ݼ��� ".number_format($sms->smsPt)."�Ǻ��� �����ϴ�");
	exit;
}

// �߼� �޽��� ���ø�
$msg_body = get_magic_quotes_gpc() ? stripslashes($_POST[$msgName]) : $_POST[$msgName];


// ���� �߼�
if ($_POST['reserve'] == 1) {
	$time = strtotime($_POST['reserve_date'].sprintf('%02d',$_POST['reserve_hour']).sprintf('%02d',$_POST['reserve_minute']).'00');

	if ($time <= time()) {
		msg("����߼� �Ͻø� ���� ���ķ� ������ �ּ���.");
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



// �߼�, graph ���

$idx = $pre_perc = $ici_perc = 0;
$num = array();
$msg = parseCode($msg_body);

$sms->log($msg,$to_tran,$type,$total,$reserve);
$sendlistKey				= 0;
$sendlistInfo				= array();
$sms_sendlist->sms_logNo	= $sms->smsLogInsertId; //sms log insert id
$sms_sendlist->sms_mode		= $sms_sendlist->getSms_mode($_POST['reserve']); //�߼�����(����, ���)

$rs = $db->query($query);
while ($row = $db->fetch($rs,1)) {
	$sendlistInfo[$sendlistKey]['phone']		= $row['phone'];
	$sendlistKey++;
}
$sms_sendlist->setListInsert($sendlistInfo);

//SMS�߼�
foreach($sendlistInfo as $data){
	

	$sms->send($msg,$data['phone'],$_POST['callback'],$reserve, $reserve_etc,$send_type);

	// graph ���;
	$ici_perc = floor(++$idx / $total * 100);
	if ($pre_perc!=$ici_perc){
		echo "<script>parent.document.getElementById('sms_bar').style.width = '".($ici_perc)."%';</script>";
		flush();
		$pre_perc = $ici_perc;
	}

}

$sms->update();
$num = $sms->countNum;

// ó�� ���
$msg = "SMS �߼ۿ�û �Ǽ� : ".number_format(array_sum($num))."�� \\n ------------------- \\n �߼ۿ�û : ".number_format($num[success])." / �߼ۿ�û���� : ".number_format($num[fail]);
msg($msg);
echo "<script>parent.document.getElementById('span_sms').innerHTML = '".number_format($sms->smsPt)."'; parent.document.getElementById('sms_bar').style.width = 0;</script>";
?>