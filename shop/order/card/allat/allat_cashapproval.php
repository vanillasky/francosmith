<?

if (isset($_GET['crno']) === false)
{
	include dirname(__FILE__).'/../../../lib/library.php';
	include dirname(__FILE__).'/../../../conf/config.pay.php';
	@include_once(dirname(__FILE__).'/../../../lib/cashreceipt.class.php');

	if (class_exists('validation') && method_exists('validation', 'xssCleanArray')) {
		$_POST = validation::xssCleanArray($_POST, array(
			validation::DEFAULT_KEY	=> 'text'
		));
	}

	$ordno = $_POST['ordno'];

	if(!is_object($cashreceipt) && class_exists('cashreceipt')) $cashreceipt = new cashreceipt();

	### �ݾ� ����Ÿ ��ȿ�� üũ
	$data = $cashreceipt->getCashReceiptCalCulate($ordno);
	if ($data['supply'] != $_POST['allat_supply_amt'] || $data['vat'] != $_POST['allat_vat_amt']) msg('�ݾ��� ��ġ���� �ʽ��ϴ�',-1);

	// �߱޻���üũ(�����ý��۰��)
	if ($data['cashreceipt'] != '' && file_exists(dirname(__FILE__).'/../../../lib/cashreceipt.class.php') === false) {
		msg('���ݿ����� �����û����!! \\n['.$ordno.'] �ֹ��� �̹� ����Ǿ����ϴ�.');
		exit;
	}

	### ���ݿ�������û���� �߰�
	if (is_object($cashreceipt))
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

		$crno = $cashreceipt->putReceipt($indata);
	}

	// �߰� ����
	$data['buyername']	= $_POST['allat_shop_member_id'];
	$data['certno']		= $_POST['allat_cert_no'];
	$data['goodsnm']	= $_POST['allat_product_nm'];
	$data['type']		= $_POST['allat_receipt_type'];
	$data['tno']		= $_POST['allat_seq_no'];
}
else {
	$ordno				= $crdata['ordno'];
	$data['supply']		= $crdata['supply'];
	$data['vat']		= $crdata['surtax'];
	$crno				= $_GET['crno'];

	// �߰� ����
	$data['buyername']	= $crdata['buyername'];
	$data['certno']		= $crdata['certno'];
	$data['goodsnm']	= $crdata['goodsnm'];
	$data['type']		= $type;
	$data['tno']		= $tno;
}
include dirname(__FILE__).'/../../../conf/pg.allat.php';
include_once dirname(__FILE__).'/allatutil.php';

// Set Value
// -------------------------------------------------------------------
$at_shop_id			= $pg['id'];
$at_cross_key		= $pg['crosskey'];
$at_supply_amt		= $data['supply'];			// ���ް���, �ݾ��� �ٽ� ����ؼ� ������ ��(��ŷ����)
$at_vat_amt			= $data['vat'];				// VAT�ݾ�
$at_apply_ymdhms    = date('YmdHis');			// �ŷ���û����(�ִ� 14Byte)
$at_shop_member_id  = $data['buyername'];		// ���θ��� ȸ��ID(�ִ� 20Byte)
$at_cert_no         = $data['certno'];			// ��������(�ִ� 13Byte) : �ڵ�����ȣ,�ֹι�ȣ,����ڹ�ȣ
$at_product_nm      = $data['goodsnm'];			// ��ǰ��(�ִ� 100Byte)
$at_receipt_type    = $data['type'];			// ���ݿ���������(�ִ� 6Byte):������ü(ABANK),������(NBANK)
$at_seq_no          = $data['tno'];				// �ŷ��Ϸù�ȣ(�ִ� 10Byte)
$at_reg_business_no	= '';						// ����һ���ڹ�ȣ(�ִ� 10Byte):���� ID�� �ٸ����
$at_buyer_ip		= $_SERVER['REMOTE_ADDR'];	// ��û�� IP
$at_test_yn			= 'N';						// TEST ����
$at_opt_pin         = 'NOUSE';
$at_opt_mod         = 'APP';

// set Enc Data
// -------------------------------------------------------------------
$at_enc_data	= setValue($at_enc_data,"allat_shop_id",$at_shop_id);
$at_enc_data	= setValue($at_enc_data,"allat_apply_ymdhms",$at_apply_ymdhms);
$at_enc_data	= setValue($at_enc_data,"allat_shop_member_id",$at_shop_member_id);
$at_enc_data	= setValue($at_enc_data,"allat_cert_no",$at_cert_no);
$at_enc_data	= setValue($at_enc_data,"allat_supply_amt",$at_supply_amt);
$at_enc_data	= setValue($at_enc_data,"allat_vat_amt",$at_vat_amt);
$at_enc_data	= setValue($at_enc_data,"allat_product_nm",$at_product_nm);
$at_enc_data	= setValue($at_enc_data,"allat_receipt_type",$at_receipt_type);
$at_enc_data	= setValue($at_enc_data,"allat_seq_no",$at_seq_no);
$at_enc_data	= setValue($at_enc_data,"allat_reg_business_no",$at_reg_business_no);
$at_enc_data	= setValue($at_enc_data,"allat_buyer_ip",$at_buyer_ip);
$at_enc_data	= setValue($at_enc_data,"allat_opt_pin",$at_opt_pin);
$at_enc_data	= setValue($at_enc_data,"allat_opt_mod",$at_opt_mod);

// ��û ������ ����
//----------------------
$at_data   = "allat_shop_id=".$at_shop_id.
             "&allat_enc_data=".$at_enc_data.
             "&allat_cross_key=".$at_cross_key;

// �þ� ���� ������ ��� : CashAppReq->����Լ�, $at_txt->�����
//----------------------------------------------------------------
$at_txt = CashAppReq($at_data,'SSL');    //���� �ʿ� https(SSL),http(NOSSL)

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
}
else {
	$settlelog = $ordno.' ('.date('Y:m:d H:i:s').')'."\n";
	$settlelog .= '-----------------------------------'."\n";
	$settlelog .= '���ݿ����� �߱� ����'."\n";
	$settlelog .= '����ڵ� : '.$REPLYCD."\n";
	$settlelog .= '������� : '.$REPLYMSG."\n";
	$settlelog .= '-----------------------------------'."\n";

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
}

?>