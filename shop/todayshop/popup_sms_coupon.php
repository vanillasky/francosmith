<?
include "../_header.php";



// ���� �ް�
	$ordno = isset($_REQUEST['ordno']) ? $_REQUEST['ordno'] : '';

	$action = isset($_POST['action']) ? $_POST['action'] : '';
	$message = isset($_POST['message']) ? $_POST['message'] : '';

	$rcvphone0 = isset($_POST['rcvphone0']) ? $_POST['rcvphone0'] : '';
	$rcvphone1 = isset($_POST['rcvphone1']) ? $_POST['rcvphone1'] : '';
	$rcvphone2 = isset($_POST['rcvphone2']) ? $_POST['rcvphone2'] : '';




// �ֹ���ȣ�� �̿�, ���� ������ ������
$query = "
	SELECT
		A.ordno, A.m_no, A.nameOrder, A.nameReceiver,A.mobileReceiver,

		C.goodsnm,

		D.cp_sno,
		D.cp_num,
		D.cp_publish,
		D.cp_sms_cnt,
		D.cp_ea

	FROM ".GD_ORDER." AS A

	INNER JOIN ".GD_ORDER_ITEM." AS B
	ON A.ordno = B.ordno

	INNER JOIN ".GD_GOODS." AS C
	ON B.goodsno = C.goodsno

	INNER JOIN ".GD_TODAYSHOP_ORDER_COUPON." AS D
	ON A.ordno = D.ordno

	WHERE A.ordno = '$ordno'
";
$data = $db->fetch($query,1);

// ������ ������ �������� �ƴѰ���.
if (!$data) msg("�ش� �ֹ��� �������� �ʽ��ϴ�",'close');


### ���� üũ
if ($sess[m_no]){
	if ($data[m_no]!=$sess[m_no]) msg("���ٱ����� �����ϴ�",'close');
} else {
	if ($data[nameOrder]!=$_COOKIE[guest_nameOrder] || $data[m_no]) msg("���ٱ����� �����ϴ�",'close');
}





// ���ø� ������
$smsMsg = "$data[goodsnm] $data[cp_num] ($data[cp_ea]��)";

$tpl->assign('cp_sms_cnt',$data['cp_sms_cnt']);
$tpl->assign('nameReceiver',$data['nameReceiver']);
$tpl->assign('mobileReceiver',$data['mobileReceiver']);
$tpl->assign('smsMsg',$smsMsg);
$tpl->assign('ordno',$ordno);
$tpl->print_('tpl');
?>