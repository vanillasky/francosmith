<?
include "../_header.php";

### �����Ҵ�
$tgsno = $_POST['tgsno'];

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

### ��ǰ ����Ÿ
if ($tgsno) { // ������ǰ ��������
	$data = $todayShop->getGoods($tgsno);
}
if (!is_array($data) || empty($data)) {
	msg('�߸��� ��ǰ��ȣ�Դϴ�.');
	exit;
}
if (!trim($data['sms'])) {
	msg('SMS ���񽺸� �������� �ʴ� ��ǰ�Դϴ�.');
	exit;
}

$rcvphone = $_POST['rcvphone0'].'-'.$_POST['rcvphone1'].'-'.$_POST['rcvphone2'];
$callback = $_POST['callback0'].'-'.$_POST['callback1'].'-'.$_POST['callback2'];
$msg = $todayShop->sendSms($tgsno, $rcvphone, $callback);
msg($msg);
?>
