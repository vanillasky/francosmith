<?
include "../_header.php";

### �����Ҵ�
$tgsno = $_GET['tgsno'];

### ȸ������ ��������
if ($sess){
	$query = "
	SELECT * FROM
		".GD_MEMBER." a
		LEFT JOIN ".GD_MEMBER_GRP." b ON a.level=b.level
	WHERE
		m_no='$sess[m_no]'
	";
	$member = $db->fetch($query,1);
}
else {
	msg('�α����� ȸ���� ��밡���մϴ�.');
	exit;
}

// TodayShop class
$todayShop = Core::loader('todayshop');
if ($todayshop->cfg['useSms'] == 'n') {
	msg('SMS ���񽺸� �������� �ʽ��ϴ�.', 'close');
	exit;
}

### ��ǰ ����Ÿ
if ($tgsno) { // ������ǰ ��������
	$data = $todayShop->getGoods($tgsno);
}
if (!is_array($data) || empty($data)) {
	msg('�߸��� ��ǰ��ȣ�Դϴ�.', 'close');
	exit;
}
if (!trim($data['sms'])) {
	msg('SMS ���񽺸� �������� �ʴ� ��ǰ�Դϴ�.', 'close');
	exit;
}

$sms = Core::loader('sms');
$sendMsg = $todayShop->makeSmsMsg($data['sms']);
if ($sms->smsPt < count($sendMsg)) {
	msg('SMS ���񽺸� ����� �� �����ϴ�.', 'close');
	exit;
}

### ���ø� ���
$tpl->assign('tgsno', $tgsno);
$tpl->assign('smsMsg', $data['sms']);
$tpl->print_('tpl');

unset($data, $sms, $sendMsg);
?>
