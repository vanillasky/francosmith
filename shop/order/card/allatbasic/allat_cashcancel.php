<?

// �þܰ��� �Լ� Include
include dirname(__FILE__).'/../../../conf/pg.allatbasic.php';
include_once dirname(__FILE__).'/allatutil.php';

$ordno = $crdata['ordno'];

// Set Value
// -------------------------------------------------------------------
$at_cross_key		= $pg['crosskey'];
$at_shop_id         = $pg['id'];			// ShopId��(�ִ� 20Byte)
$at_cash_bill_no    = $crdata['tid'];		// ���ݿ������Ϸù�ȣ(�ִ� 10Byte)
$at_supply_amt      = $crdata['supply'];	// ��Ұ��ް���(�ִ� 10Byte)
$at_vat_amt         = $crdata['surtax'];	// ���VAT�ݾ�(�ִ� 10Byte)
$at_reg_business_no = '';					// ����һ���ڹ�ȣ(�ִ� 10Byte):���� ID�� �ٸ����
$at_opt_pin         = "NOUSE";
$at_opt_mod         = "APP";

// set Enc Data
// -------------------------------------------------------------------
$at_enc_data		= setValue($at_enc_data,"allat_shop_id",$at_shop_id);
$at_enc_data		= setValue($at_enc_data,"allat_cash_bill_no",$at_cash_bill_no);
$at_enc_data		= setValue($at_enc_data,"allat_supply_amt",$at_supply_amt);
$at_enc_data		= setValue($at_enc_data,"allat_vat_amt",$at_vat_amt);
$at_enc_data		= setValue($at_enc_data,"allat_reg_business_no",$at_reg_business_no);
$at_enc_data		= setValue($at_enc_data,"allat_opt_pin",$at_opt_pin);
$at_enc_data		= setValue($at_enc_data,"allat_opt_mod",$at_opt_mod);

// Set Request Data
//---------------------------------------------------------------------
$at_data   = "allat_shop_id=".$at_shop_id.
             "&allat_enc_data=".$at_enc_data.
             "&allat_cross_key=".$at_cross_key;

// �þ� ���� ������ ��� : SendApproval->����Լ�, $at_txt->�����
//----------------------------------------------------------------
$at_txt = CashCanReq($at_data,'SSL');

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
	$CANCEL_YMDHMS = getValue('cancel_ymdhms',$at_txt);
	$PART_CANCEL_FLAG = getValue('part_cancel_flag',$at_txt);
	$REMAIN_AMT = getValue('remain_amt',$at_txt);

	$settlelog = $ordno.' ('.date('Y:m:d H:i:s').')'."\n";
	$settlelog .= '-----------------------------------'."\n";
	$settlelog .= '���ݿ����� ��� ����'."\n";
	$settlelog .= '����ڵ� : '.$REPLYCD."\n";
	$settlelog .= '������� : '.$REPLYMSG."\n";
	$settlelog .= '����Ͻ� : '.$CANCEL_YMDHMS."\n";
	$settlelog .= '��ҿ��� : '.$PART_CANCEL_FLAG."\n";
	$settlelog .= '�ܾ� : '.$REMAIN_AMT."\n";
	$settlelog .= '-----------------------------------'."\n";

	$db->query("update gd_cashreceipt set moddt=now(),status='CCR',errmsg='',receiptlog=concat(if(receiptlog is null,'',receiptlog),'{$settlelog}') where crno='{$_GET['crno']}'");
}
else {
	$settlelog = $ordno.' ('.date('Y:m:d H:i:s').')'."\n";
	$settlelog .= '-----------------------------------'."\n";
	$settlelog .= '���ݿ����� ��� ����'."\n";
	$settlelog .= '����ڵ� : '.$REPLYCD."\n";
	$settlelog .= '������� : '.$REPLYMSG."\n";
	$settlelog .= '-----------------------------------'."\n";

	$db->query("update gd_cashreceipt set errmsg='{$REPLYCD}:{$REPLYMSG}',moddt=now(),receiptlog=concat(if(receiptlog is null,'',receiptlog),'\n{$settlelog}') where crno='{$_GET['crno']}'");
}

?>