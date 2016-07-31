<?

if (isset($_GET['crno']) === false)
{
	include dirname(__FILE__).'/../../../lib/library.php';
	include dirname(__FILE__).'/../../../conf/config.pay.php';

	$ordno = $_POST['ordno'];

	### �ݾ� ����Ÿ ��ȿ�� üũ
	$data = $db->fetch("select * from gd_order where ordno='$ordno'",1);
	if ($set['receipt']['compType'] == '1'){ // �鼼/���̻����
		$data['supply'] = $data['prn_settleprice'];
		$data['vat'] = 0;
	}
	else { // ���������
		$data['supply'] = round($data['prn_settleprice'] / 1.1);
		$data['vat'] = $data['prn_settleprice'] - $data['supply'];
	}
	if ($data['supply'] != $_POST['allat_supply_amt'] || $data['vat'] != $_POST['allat_vat_amt']) msg('�ݾ��� ��ġ���� �ʽ��ϴ�',-1);

	// �߱޻���üũ(�����ý��۰��)
	if ($data['cashreceipt'] != '' && file_exists(dirname(__FILE__).'/../../../lib/cashreceipt.class.php') === false) {
		msg('���ݿ����� �����û����!! \\n['.$ordno.'] �ֹ��� �̹� ����Ǿ����ϴ�.');
		exit;
	}

	### ���ݿ�������û���� �߰�
	@include dirname(__FILE__).'/../../../lib/cashreceipt.class.php';
	if (class_exists('cashreceipt'))
	{
		// �߱޻���üũ
		list($crno) = $db->fetch("select crno from gd_cashreceipt where ordno='{$ordno}' and status='ACK' order by crno desc limit 1");
		if ($crno != '') {
			msg('���ݿ����� �����û����!! \\n['.$ordno.'] �ֹ��� �̹� ����Ǿ����ϴ�.');
			exit;
		}

		$indata = array();
		$indata['ordno'] = $_POST['ordno'];
		$indata['goodsnm'] = $_POST['allat_product_nm'];
		$indata['buyername'] = $_POST['allat_shop_member_id'];
		$indata['useopt'] = $_POST['useopt'];
		$indata['certno'] = $_POST['allat_cert_no'];
		$indata['amount'] = $_POST['allat_supply_amt'] + $_POST['allat_vat_amt'];
		$indata['supply'] = $_POST['allat_supply_amt'];
		$indata['surtax'] = $_POST['allat_vat_amt'];

		$cashreceipt = new cashreceipt();
		$crno = $cashreceipt->putReceipt($indata);
	}
}
else {
	$ordno = $crdata['ordno'];
	$data['supply'] = $crdata['supply'];
	$data['vat'] = $crdata['surtax'];
	$crno = $_GET['crno'];
}
//include dirname(__FILE__).'/../../../conf/pg.allat.php';
// �����̼� ������� ��� PG ���� ��ü
resetPaymentGateway();
include dirname(__FILE__).'/allatutil.php';


$at_shop_id		= $pg['id'];
$at_cross_key	= $pg['crosskey'];
$at_supply_amt	= $data['supply']; //�ݾ��� �ٽ� ����ؼ� ������ ��(��ŷ����)
$at_vat_amt		= $data['vat'];

// ��û ������ ����
//----------------------
$at_data   = 'allat_shop_id='.$at_shop_id .
			 '&allat_supply_amt='.$at_supply_amt.
			 '&allat_vat_amt='.$at_vat_amt.
			 '&allat_enc_data='.$_POST['allat_enc_data'].
			 '&allat_cross_key='.$at_cross_key;

// �þ� ���� ������ ��� : CashAppReq->����Լ�, $at_txt->�����
//----------------------------------------------------------------
$at_txt = CashAppReq($at_data,$pg['ssl']);    //���� �ʿ� https(SSL),http(NOSSL)

// ���� ��� �� Ȯ��
//------------------
$REPLYCD   =getValue('reply_cd',$at_txt);
$REPLYMSG  =getValue('reply_msg',$at_txt);

// ����� ó��
//--------------------------------------------------------------------------
// ��� ���� '0000'�̸� ������. ��, allat_test_yn=Y �ϰ�� '0001'�� ������.
// ���� ����   : allat_test_yn=N �� ��� reply_cd=0000 �̸� ����
// �׽�Ʈ ���� : allat_test_yn=Y �� ��� reply_cd=0001 �̸� ����
//--------------------------------------------------------------------------
if( !strcmp($REPLYCD,'0000') )
{
	$APPROVAL_NO = trim(getValue('approval_no',$at_txt));
	$CASH_BILL_NO = trim(getValue('cash_bill_no',$at_txt));

	$settlelog = $ordno.' ('.date('Y:m:d H:i:s').')'."\n";
	$settlelog .= '----------------------------------'."\n";
	$settlelog .= '���ݿ����� �߱� ����'."\n";
	$settlelog .= '����ڵ� : '.$REPLYCD."\n";
	$settlelog .= '������� : '.$REPLYMSG."\n";
	$settlelog .= '���ι�ȣ : '.$APPROVAL_NO."\n";
	$settlelog .= '�Ϸù�ȣ : '.$CASH_BILL_NO."\n";
	$settlelog .= '-----------------------------------'."\n";
	echo nl2br($settlelog);

	if (empty($crno) === true)
	{
		$db->query("update gd_order set cashreceipt='{$CASH_BILL_NO}',settlelog=concat(if(settlelog is null,'',settlelog),'\n{$settlelog}') where ordno='{$ordno}'");
	}
	else {
		# ���ݿ�������û���� ����
		$db->query("update gd_cashreceipt set pg='allat',cashreceipt='{$CASH_BILL_NO}',receiptnumber='{$APPROVAL_NO}',tid='{$CASH_BILL_NO}',moddt=now(),status='ACK',errmsg='',receiptlog=concat(if(receiptlog is null,'',receiptlog),'\n{$settlelog}') where crno='{$crno}'");
		$db->query("update gd_order set cashreceipt='{$CASH_BILL_NO}' where ordno='{$ordno}'");
	}

	if (isset($_GET['crno']) === false)
	{
		msg('���ݿ������� ����߱޵Ǿ����ϴ�');
		echo '<script>parent.location.reload();</script>';
	}
	else {
		echo '<script>if(parent.opener == null) window.onload = function (){ parent.parent.location.reload(); }</script>';
	}
}
else {
	$settlelog = $ordno.' ('.date('Y:m:d H:i:s').')'."\n";
	$settlelog .= '-----------------------------------'."\n";
	$settlelog .= '���ݿ����� �߱� ����'."\n";
	$settlelog .= '����ڵ� : '.$REPLYCD."\n";
	$settlelog .= '������� : '.$REPLYMSG."\n";
	$settlelog .= '-----------------------------------'."\n";
	echo nl2br($settlelog);

	if (empty($crno) === true)
	{
		$db->query("update gd_order set settlelog=concat(if(settlelog is null,'',settlelog),'\n$settlelog') where ordno='$ordno'");
	}
	else {
		# ���ݿ�������û���� ����
		$db->query("update gd_cashreceipt set pg='allat',errmsg='{$REPLYCD}:{$REPLYMSG}',moddt=now(),receiptlog=concat(if(receiptlog is null,'',receiptlog),'\n{$settlelog}') where crno='{$crno}'");
	}

	if (isset($_GET['crno']) === false)
	{
		msg("$REPLYMSG");
		exit;
	}
	else {
		echo '<script>if(parent.opener == null) window.onload = function (){ parent.parent.location.reload(); }</script>';
	}
}

?>