<?php
/**
 * �̴Ͻý� PG ����ũ�� ��� ��� ������
 * ���� ���ϸ� INIescrow_delivery.html
 * �̴Ͻý� PG ���� : INIpay V5.0 - ������ (V 0.1.1 - 20120302)
 */

include "../../../lib/library.php";
include "../../../conf/config.php";
include "../../../conf/pg.$cfg[settlePg].php";
include "../../../conf/pg.escrow.php";

$ordno = $_GET['ordno'];

$query = "
SELECT
	a.settleprice,a.delivery,a.nameReceiver,a.phoneReceiver,a.mobileReceiver,a.zipcode,a.address,a.escrowno,
	a.deliveryno,a.deliverycode,a.delivery,a.ddt
FROM
	".GD_ORDER." a
WHERE
	a.ordno = '$ordno'
";
$data = $db->fetch($query);

// ����� ��ȣ, �ù�� üũ
if (empty($data['deliveryno']) || empty($data['deliverycode'])) {
	msg('����� ��ȣ�� ���õ� �ù�簡 �����ϴ�. �ٽ� Ȯ�� �ٶ��ϴ�.');
	exit;
}

// ��ۺ� ���޹�� ����
if ($data['delivery'] > 0) {
	$dlvChargeVal	= 'BH';
} else {
	$dlvChargeVal	= 'SH';
}

// ��۵�� Ȯ���Ͻ�
if (strlen($data['ddt'] > 9)) {
	$dlvInvoiceDay	= $data['ddt'];
} else {
	$dlvInvoiceDay	= date('Y-m-d H:i:s');
}

// ������ ��ȭ��ȣ
if (empty($data['mobileReceiver']) === false) {
	$recvTel	= $data['mobileReceiver'];
} else {
	$recvTel	= $data['phoneReceiver'];
}

// �ù�� �ڵ� �� �ù�� �� ����
$compcode			= array();
$compcode['15']		= array('code'	=> 'cjgls', 'name' =>'CJ GLS');
$compcode['13']		= array('code'	=> 'hyundai', 'name' =>'�����ù�');
$compcode['12']		= array('code'	=> 'hanjin', 'name' =>'�����ù�');
$compcode['4']		= array('code'	=> 'korex', 'name' =>'�������');
$compcode['1']		= array('code'	=> 'kgbls', 'name' =>'KGB�ù�');
$compcode['5']		= array('code'	=> 'kgb', 'name' =>'�����ù�');
$compcode['9']		= array('code'	=> 'EPOST', 'name' =>'��ü���ù�');
$compcode['100']	= array('code'	=> 'EPOST', 'name' =>'��ü���ù�');
$compcode['6']		= array('code'	=> 'hth', 'name' =>'�ＺHTH');
$compcode['14']		= array('code'	=> '', 'name' =>'�ѹ̸��ù�');
$compcode['7']		= array('code'	=> 'ajutb', 'name' =>'�����ù�');
$compcode['8']		= array('code'	=> 'yellow', 'name' =>'���ο�ĸ');
$compcode['22']		= array('code'	=> '', 'name' =>'�Ͼ��ù�');
$compcode['11']		= array('code'	=> 'tranet', 'name' =>'Ʈ���');
$compcode['2']		= array('code'	=> 'ktlogistics', 'name' =>'KT������');
$compcode['18']		= array('code'	=> 'registpost', 'name' =>'������');
$compcode['20']		= array('code'	=> 'Hanaro', 'name' =>'�ϳ����ù�');
$compcode['17']		= array('code'	=> 'Sagawa', 'name' =>'�簡���ͽ�������');
$compcode['16']		= array('code'	=> 'sedex', 'name' =>'SEDEX');
$compcode['21']		= array('code'	=> 'dongbu', 'name' =>'�����ù�');
$compcode['9999']	= array('code'	=> '9999', 'name' =>'��Ÿ�ù�');

if (in_array($data['deliveryno'], array_keys($compcode))) {
	$dlvExArr	= $compcode[$data['deliveryno']];
} else {
	$dlvExArr	= $compcode['9999'];
}
?>
<html>
<head>
<title>�̴Ͻý� ��ü ����ũ��(INIescrow)</title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<meta http-equiv="Cache-Control" content="no-cache" />
<meta http-equiv="Expires" content="0" />
<meta http-equiv="Pragma" content="no-cache" />

<script language="Javascript">
function f_check(){
	if(document.ini.tid.value == ""){
		alert("�ŷ���ȣ�� �������ϴ�.")
		return;
	}
	else if(document.ini.mid.value == ""){
		alert("���� ���̵� �������ϴ�.")
		return;
	}
	else if(document.ini.EscrowType.value == ""){
		alert("����ũ�� �۾��� �����Ͻʽÿ�.")
		return;
	}
	else if(document.ini.invoice.value == ""){
		alert("������ȣ�� �������ϴ�.")
		return;
	}
	else if(document.ini.oid.value == ""){
		alert("�ֹ���ȣ�� �������ϴ�.")
		return;
	}
	document.ini.submit();
}
</script>
</head>

<body>
<form name="ini" method="post" action="./INIescrow_delivery.php">
<input type="hidden" name="ordno"			value="<?php echo $ordno;?>" />								<!-- �ֹ� ��ȣ - PG ó���ʹ� ���� ����� ���� �ɼ��� -->
<input type="hidden" name="mid"				value="<?php echo $escrow['id'];?>" />						<!-- * ����ũ�� ���̵� -->
<input type="hidden" name="tid"				value="<?php echo $data['escrowno'];?>" />					<!-- * ��ǰ���� �ŷ���ȣ(TID) -->
<input type="hidden" name="oid"				value="<?php echo $ordno;?>" />								<!-- * ��ǰ���� �ֹ���ȣ(OID) -->
<input type="hidden" name="EscrowType"		value="I" />												<!-- * ����ũ�� ������� (���:I, ����:U) -->

<input type="hidden" name="dlv_name"		value="������" />											<!-- * ��۵���� -->
<input type="hidden" name="dlv_exCode"		value="<?php echo $dlvExArr['code'];?>" />					<!-- * �ù���ڵ� -->
<input type="hidden" name="dlv_exName"		value="<?php echo $dlvExArr['name'];?>" />					<!-- * �ù��� -->
<input type="hidden" name="dlv_charge"		value="<?php echo $dlvChargeVal;?>" />						<!-- * ��ۺ� �������� (SH : �Ǹ��ںδ�, BH : �����ںδ�) -->
<input type="hidden" name="dlv_invoiceday"	value="<?php echo $dlvInvoiceDay;?>">						<!-- * ��۵�� Ȯ���Ͻ� -->
<input type="hidden" name="invoice"			value="<?php echo $data['deliverycode'];?>" />				<!-- * ������ȣ -->

<input type="hidden" name="sendName"		value="<?php echo $cfg['adminName'];?>" />					<!-- * �۽��� �̸� -->
<input type="hidden" name="sendPost"		value="<?php echo $cfg['zipcode'];?>" />					<!-- * �۽��� �����ȣ -->
<input type="hidden" name="sendAddr1"		value="<?php echo ($cfg['road_address'] ? $cfg['road_address'] : $cfg['address']);?>" />	<!-- * �۽��� �ּ�1 -->
<input type="hidden" name="sendAddr2"		value="" />													<!-- �۽��� �ּ�2 -->
<input type="hidden" name="sendTel"			value="<?php echo $cfg['compPhone'];?>" />					<!-- * �۽��� ��ȭ��ȣ -->

<input type="hidden" name="recvName"		value="<?php echo $data['nameReceiver'];?>" />				<!-- * ������ �̸� -->
<input type="hidden" name="recvPost"		value="<?php echo str_replace('-', '', $data['zipcode']);?>" />		<!-- * ������ �����ȣ -->
<input type="hidden" name="recvAddr"		value="<?php echo $data['address'];?>" />					<!-- * ������ �ּ� -->
<input type="hidden" name="recvTel"			value="<?php echo $recvTel;?>" />							<!-- * ������ ��ȭ��ȣ -->

<input type="hidden" name="goodsCode"		value="" />													<!-- ��ǰ�ڵ�(����) -->
<input type="hidden" name="goods"			value="" />													<!-- ��ǰ��(����) -->
<input type="hidden" name="goodCnt"			value="" />													<!-- ��ǰ����(����) -->
<input type="hidden" name="price"			value="<?php echo $data['settleprice'];?>" />				<!-- * ��ǰ����(�ʼ�) -->
<input type="hidden" name="reserved1"		value="" />													<!-- ��ǰ��ǰ�ɼ�1(����) -->
<input type="hidden" name="reserved2"		value="" />													<!-- ��ǰ��ǰ�ɼ�2(����) -->
<input type="hidden" name="reserved3"		value="" />													<!-- ��ǰ��ǰ�ɼ�3(����) -->
</form>
<script>f_check();</script>
</body>
</html>