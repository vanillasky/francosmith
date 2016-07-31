<?php
/**
 * �̴Ͻý� PG ���ݿ����� ��� ó�� ������
 * ���� ���ϸ� INIreceipt.php
 * �̴Ͻý� PG ���� : INIpay V5.0 (V 0.1.1 - 20120302)
 * ���ݰ���(�ǽð� ���������ü, �������Ա�)�� ���� ���ݰ��� ������ ���� ��û�Ѵ�.
 */

//--- ���� ����Ÿ ó��
if (isset($_GET['crno']) === false)
{
	include dirname(__FILE__).'/../../../lib/library.php';
	include dirname(__FILE__).'/../../../conf/config.pay.php';
	extract($_POST);

	### �ݾ� ����Ÿ ��ȿ�� üũ
	$data = $db->fetch("SELECT * FROM ".GD_ORDER." WHERE ordno='".$ordno."'",1);
	if ($set['receipt']['compType'] == '1'){ // �鼼/���̻����
		$data['supply']	= $data['prn_settleprice'];
		$data['vat']	= 0;
	}
	else { // ���������
		$data['supply']	= round($data['prn_settleprice'] / 1.1);
		$data['vat']	= $data['prn_settleprice'] - $data['supply'];
	}
	if ($data['supply']!=$_POST['sup_price'] || $data['vat']!=$_POST['tax']) msg('�ݾ��� ��ġ���� �ʽ��ϴ�',-1);

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
		list($crno) = $db->fetch("SELECT crno FROM ".GD_CASHRECEIPT." WHERE ordno='".$ordno."' AND status='ACK' ORDER BY crno DESC LIMIT 1");
		if ($crno != '') {
			msg('���ݿ����� �����û����!! \\n['.$ordno.'] �ֹ��� �̹� ����Ǿ����ϴ�.');
			exit;
		}

		$indata = array();
		$indata['ordno']		= $_POST['ordno'];
		$indata['goodsnm']		= $_POST['goodname'];
		$indata['buyername']	= $_POST['buyername'];
		$indata['useopt']		= $_POST['useopt'];
		$indata['certno']		= $_POST['reg_num'];
		$indata['amount']		= $_POST['cr_price'];
		$indata['supply']		= $_POST['sup_price'];
		$indata['surtax']		= $_POST['tax'];

		$cashreceipt	= new cashreceipt();
		$crno	= $cashreceipt->putReceipt($indata);
	}
}
else {
	$ordno		= $crdata['ordno'];
	$goodname	= $crdata['goodsnm'];
	$cr_price	= $crdata['amount'];
	$sup_price	= $crdata['supply'];
	$tax		= $crdata['surtax'];
	$srvc_price	= 0;
	$buyername	= $crdata['buyername'];
	$buyeremail	= $crdata['buyeremail'];
	$buyertel	= $crdata['buyerphone'];
	$reg_num	= $crdata['certno'];
	$useopt		= $crdata['useopt'];
	$crno		= $_GET['crno'];
}

//--- PG ����
//include dirname(__FILE__).'/../../../conf/pg.inipay.php';

//--- �����̼� ������� ��� PG ���� ��ü
resetPaymentGateway();

//--- ���̺귯�� ��Ŭ���
require_once dirname(__FILE__).'/libs/INILib.php';

//--- INIpay50 Ŭ������ �ν��Ͻ� ����
$inipay	= new INIpay50;

//--- �߱� ���� ����
$inipay->SetField('inipayhome',		dirname(__FILE__));		// �̴����� Ȩ���͸�
$inipay->SetField('type',			'receipt');				// ����
$inipay->SetField('pgid',			'INIphpRECP');			// ����
$inipay->SetField('paymethod',		'CASH');				// ���� (��û�з�)
$inipay->SetField('currency',		'WON');					// ȭ����� (WON �Ǵ� CENT ���� : ��ȭ������ ���� ����� �ʿ��մϴ�.)
$inipay->SetField('admin',			'1111');				// Ű�н�����(�������̵� ���� ����)
$inipay->SetField('debug',			'true');				// �α׸��('true'�� �����ϸ� �󼼷αװ� ������.)
$inipay->SetField('mid',			$pg['id']);				// �������̵�
$inipay->SetField('goodname',		$goodname);				// ��ǰ��
$inipay->SetField('cr_price',		$cr_price);				// �� ���ݰ��� �ݾ�
$inipay->SetField('sup_price',		$sup_price);			// ���ް���
$inipay->SetField('tax',			$tax);					// �ΰ���
$inipay->SetField('srvc_price',		$srvc_price);			// �����
$inipay->SetField('buyername',		$buyername);			// ������ ����
$inipay->SetField('buyeremail',		$buyeremail);			// ������ �̸��� �ּ�
$inipay->SetField('buyertel',		$buyertel);				// ������ ��ȭ��ȣ
$inipay->SetField('reg_num',		$reg_num);				// ���ݰ����� �ֹε�Ϲ�ȣ
$inipay->SetField('useopt',			$useopt);				// ���ݿ����� ����뵵 ('0' - �Һ��� �ҵ������, '1' - ����� ����������)
$inipay->SetField('companynumber',	$companynumber);

//--- �߱� ��û
$inipay->startAction();
/********************************************************************************
 * �߱� ���																	*
 *																				*
 * ����ڵ� : $inipay->GetResult('ResultCode') ("00" �̸� ���� ����)			*
 * ���ι�ȣ : $inipay->GetResult('ApplNum') (���ݿ����� ���� ���ι�ȣ)			*
 * ���γ�¥ : $inipay->GetResult('ApplDate') (YYYYMMDD)							*
 * ���νð� : $inipay->GetResult('ApplTime') (HHMMSS)							*
 * �ŷ���ȣ : $inipay->GetResult('TID')											*
 * �����ݰ��� �ݾ� : $inipay->GetResult('CSHR_ApplPrice')						*
 * ���ް��� : $inipay->GetResult('CSHR_SupplyPrice')							*
 * �ΰ��� : $inipay->GetResult('CSHR_Tax')										*
 * ����� : $inipay->GetResult('CSHR_ServicePrice')								*
 * ��뱸�� : $inipay->GetResult('CSHR_Type')									*
 ********************************************************************************/

//--- ���� �뵵
$arrType	= array('0' => '�ҵ������', '1' => '����������');

//--- �α� ����
$settlelog	= '';
$settlelog	.= '===================================================='.chr(10);
$settlelog	.= '�ֹ���ȣ : '.$ordno.chr(10);
$settlelog	.= '�ŷ���ȣ : '.$inipay->GetResult('TID').chr(10);
$settlelog	.= '����ڵ� : '.$inipay->GetResult('ResultCode').chr(10);
$settlelog	.= '������� : '.$inipay->GetResult('ResultMsg').chr(10);
$settlelog	.= '���ι�ȣ : '.$inipay->GetResult('ApplNum').chr(10);
$settlelog	.= '���γ�¥ : '.$inipay->GetResult('ApplDate').chr(10);
$settlelog	.= '���νð� : '.$inipay->GetResult('ApplTime').chr(10);
$settlelog	.= '���αݾ� : '.$inipay->GetResult('CSHR_ApplPrice').chr(10);
$settlelog	.= '���ް��� : '.$inipay->GetResult('CSHR_SupplyPrice').chr(10);
$settlelog	.= '�ΰ��� : '.$inipay->GetResult('CSHR_Tax').chr(10);
$settlelog	.= '����� : '.$inipay->GetResult('CSHR_ServicePrice').chr(10);
$settlelog	.= '��뱸�� : '.$inipay->GetResult('CSHR_Type').' - '.$arrType[$inipay->GetResult('CSHR_Type')].chr(10);

//--- ���ο��� / ���� ����� ���� ó�� ����
if($inipay->GetResult('ResultCode') == "00"){
	// PG ���
	$getPgResult	= true;

	$settlelog	= '===================================================='.chr(10).'���ݿ����� : ����Ȯ�νð�('.date('Y-m-d H:i:s').')'.chr(10).$settlelog.'===================================================='.chr(10);
} else {
	// PG ���
	$getPgResult	= false;

	$settlelog	= '===================================================='.chr(10).'���ݿ����� : ����Ȯ�νð�('.date('Y-m-d H:i:s').')'.chr(10).$settlelog.'===================================================='.chr(10);
}

//--- ��� ó��
if( $getPgResult === true )
{
	if (empty($crno) === true)
	{
		$db->query("UPDATE ".GD_ORDER." SET cashreceipt='".$inipay->GetResult('TID')."',settlelog=concat(if(settlelog is null,'',settlelog),'".$settlelog."') WHERE ordno='".$ordno."'");
	}
	else {
		# ���ݿ�������û���� ����
		$db->query("UPDATE ".GD_CASHRECEIPT." SET pg='inipay',cashreceipt='".$inipay->GetResult('TID')."',receiptnumber='".$inipay->GetResult('ApplNum')."',tid='".$inipay->GetResult('TID')."',moddt=now(),status='ACK',errmsg='',receiptlog=concat(if(receiptlog is null,'',receiptlog),'".$settlelog."') where crno='".$crno."'");
		$db->query("UPDATE ".GD_ORDER." SET cashreceipt='".$inipay->GetResult('TID')."' where ordno='".$ordno."'");
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
else
{
	if (empty($crno) === true)
	{
		$db->query("UPDATE ".GD_ORDER." SET settlelog=concat(if(settlelog is null,'',settlelog),'".$settlelog."') WHERE ordno='".$ordno."'");
	}
	else {
		# ���ݿ�������û���� ����
		$db->query("UPDATE ".GD_CASHRECEIPT." SET pg='inipay',errmsg='".$inipay->GetResult('ResultCode').":".$inipay->GetResult('ResultMsg')."',moddt=now(),receiptlog=concat(if(receiptlog is null,'',receiptlog),'".$settlelog."') WHERE crno='".$crno."'");
	}

	if (isset($_GET['crno']) === false)
	{
		msg($inipay->GetResult('ResultMsg'));
		exit;
	}
	else {
		echo '<script>if(parent.opener == null) window.onload = function (){ parent.parent.location.reload(); }</script>';
	}
}
?>