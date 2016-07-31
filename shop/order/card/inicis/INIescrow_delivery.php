<?php
/**
 * �̴Ͻý� PG ����ũ�� ��� ��� ó�� ������
 * ���� ���ϸ� INIescrow_delivery.php
 * �̴Ͻý� PG ���� : INIpay V5.0 - ������ (V 0.1.1 - 20120302)
 */

//--- �⺻ ����
include "../../../lib/library.php";

//--- ���̺귯�� ��Ŭ���
require_once dirname(__FILE__).'/libs/INILib.php';

//--- INIpay50 Ŭ������ �ν��Ͻ� ����
$iniescrow	= new INIpay50;

//--- ���� ���� ����
$iniescrow->SetField('inipayhome', dirname(__FILE__));		// �̴����� Ȩ���͸�
$iniescrow->SetField('type', 'escrow');						// ���� (���� ���� �Ұ�)
$iniescrow->SetField('tid', $tid);							// �ŷ����̵�
$iniescrow->SetField('mid', $mid);							// �������̵�
$iniescrow->SetField('admin', '1111');						// Ű�н�����(�������̵� ���� ����)

$iniescrow->SetField('escrowtype', 'dlv');					// ���� (���� ���� �Ұ�)
$iniescrow->SetField('dlv_ip', getenv('REMOTE_ADDR'));		// IP
$iniescrow->SetField('debug','true');						// �α׸��('true'�� �����ϸ� ���� �αװ� ������)

$iniescrow->SetField('oid', $oid);							// �ֹ���ȣ
$iniescrow->SetField('soid', '1');							// ����
$iniescrow->SetField('dlv_date', $dlv_date);				// ��۵�� ����
$iniescrow->SetField('dlv_time', $dlv_time);				// ��۵�� �ð�
$iniescrow->SetField('dlv_report', $EscrowType);			// ����ũ�� Ÿ��
$iniescrow->SetField('dlv_invoice', $invoice);				// ����� ��ȣ
$iniescrow->SetField('dlv_name', $dlv_name);				// ��۵����

$iniescrow->SetField('dlv_excode', $dlv_exCode);			// �ù���ڵ�
$iniescrow->SetField('dlv_exname', $dlv_exName);			// �ù���
$iniescrow->SetField('dlv_charge', $dlv_charge);			// ��ۺ� ���޹��

$iniescrow->SetField('dlv_invoiceday', $dlv_invoiceday);	// ��۵�� Ȯ���Ͻ�
$iniescrow->SetField('dlv_sendname', $sendName);			// �۽��� �̸�
$iniescrow->SetField('dlv_sendpost', $sendPost);			// �۽��� �����ȣ
$iniescrow->SetField('dlv_sendaddr1', $sendAddr1);			// �۽��� �ּ�1
$iniescrow->SetField('dlv_sendaddr2', $sendAddr2);			// �۽��� �ּ�2
$iniescrow->SetField('dlv_sendtel', $sendTel);				// �۽��� ��ȭ��ȣ

$iniescrow->SetField('dlv_recvname', $recvName);			// ������ �̸�
$iniescrow->SetField('dlv_recvpost', $recvPost);			// ������ �����ȣ
$iniescrow->SetField('dlv_recvaddr', $recvAddr);			// ������ �ּ�
$iniescrow->SetField('dlv_recvtel', $recvTel);				// ������ ��ȭ��ȣ

$iniescrow->SetField('dlv_goodscode', $goodsCode);			// ��ǰ�ڵ�
$iniescrow->SetField('dlv_goods', $goods);					// ��ǰ��
$iniescrow->SetField('dlv_goodscnt', $goodCnt);				// ��ǰ����
$iniescrow->SetField('price', $price);						// ��ǰ����
$iniescrow->SetField('dlv_reserved1', $reserved1);			// ��ǰ��ǰ�ɼ�1
$iniescrow->SetField('dlv_reserved2', $reserved2);			// ��ǰ��ǰ�ɼ�2
$iniescrow->SetField('dlv_reserved3', $reserved3);			// ��ǰ��ǰ�ɼ�3

$iniescrow->SetField('pgn', $pgn);

//--- ��� ��� ��û
$iniescrow->startAction();

//--- ��� ��� ���
$resultCode	= $iniescrow->GetResult('ResultCode');			// ����ڵ� ('00'�̸� ���� ����)
$resultMsg	= $iniescrow->GetResult('ResultMsg');			// ������� (���Ұ���� ���� ����)
$dlv_date	= $iniescrow->GetResult('DLV_Date');
$dlv_time	= $iniescrow->GetResult('DLV_Time');

//--- �ֹ���ȣ ó��
$ordno		= $_POST['ordno'];

//--- �α� ����
$settlelog	= '';
$settlelog	.= '===================================================='.chr(10);
$settlelog	.= '�ֹ���ȣ : '.$ordno.chr(10);
$settlelog	.= '�ŷ���ȣ : '.$iniescrow->GetResult('TID').chr(10);
$settlelog	.= '����ڵ� : '.$iniescrow->GetResult('ResultCode').chr(10);
$settlelog	.= '������� : '.$iniescrow->GetResult('ResultMsg').chr(10);
$settlelog	.= 'ó����¥ : '.$iniescrow->GetResult('DLV_Date').chr(10);
$settlelog	.= 'ó���ð� : '.$iniescrow->GetResult('DLV_Time').chr(10);
$settlelog	.= 'ó����IP : '.$_SERVER['REMOTE_ADDR'].chr(10);

//--- ���ο��ο� ���� ó�� ����
if($iniescrow->GetResult('ResultCode') == "00"){

	// PG ���
	$getPgResult		= true;
	$settlelog	= '===================================================='.chr(10).'����ũ�� ��۵�� : ó���Ϸ�ð�('.date('Y-m-d H:i:s').')'.chr(10).$settlelog.'===================================================='.chr(10);
} else {
	$settlelog	= '===================================================='.chr(10).'����ũ�� ��۵�� : ����Ȯ�νð�('.date('Y-m-d H:i:s').')'.chr(10).$settlelog.'===================================================='.chr(10);

	// PG ���
	$getPgResult		= false;
}

//--- ������ ��� ó��
if( $getPgResult === true ){
	// �ǵ���Ÿ ����
	$db->query("
	UPDATE ".GD_ORDER." SET
		escrowconfirm	= 1,
		settlelog		= concat(ifnull(settlelog,''),'$settlelog')
	WHERE ordno='$ordno'"
	);
} else {
	// �ǵ���Ÿ ����
	$db->query("
	UPDATE ".GD_ORDER." SET
		settlelog		= concat(ifnull(settlelog,''),'$settlelog')
	WHERE ordno='$ordno'"
	);
}

msg($resultMsg);
exit;
?>