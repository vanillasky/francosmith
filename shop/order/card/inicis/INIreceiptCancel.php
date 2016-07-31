<?php
/**
 * �̴Ͻý� PG ���ݿ����� ��� ��� ó�� ������
 * ���� ���ϸ� INIcancel.php
 * �̴Ͻý� PG ���� : INIpay V5.0 (V 0.1.1 - 20120302)
 */

//--- PG ����
include dirname(__FILE__).'/../../../conf/pg.inipay.php';

//--- ���̺귯�� ��Ŭ���
require_once dirname(__FILE__).'/libs/INILib.php';

//--- �ֹ���ȣ ó��
$ordno	= $crdata['ordno'];

//--- INIpay50 Ŭ������ �ν��Ͻ� ����
$inipay	= new INIpay50;

//--- ��� ���� ����
$inipay->SetField('inipayhome',		dirname(__FILE__));		// �̴����� Ȩ���͸�
$inipay->SetField('type',			'cancel');				// ���� (���� ���� �Ұ�)
$inipay->SetField('debug',			'true');				// �α׸��('true'�� �����ϸ� �󼼷αװ� ������.)
$inipay->SetField('mid',			$pg['id']);				// �������̵�
$inipay->SetField('admin',			'1111');				// ���Ī ���Ű Ű�н�����
$inipay->SetField('tid',			$crdata['tid']);		// ����� �ŷ��� �ŷ����̵�
$inipay->SetField('cancelmsg',		'���������');			// ��һ���

//--- ��� ��û
$inipay->startAction();
/********************************************************************
* ��� ���															*
*																	*
* ����ڵ� : $inipay->getResult('ResultCode') ("00"�̸� ��� ����)	*
* ������� : $inipay->getResult('ResultMsg') (��Ұ���� ���� ����)	*
* ��ҳ�¥ : $inipay->getResult('CancelDate') (YYYYMMDD)			*
* ��ҽð� : $inipay->getResult('CancelTime') (HHMMSS)				*
* ���ݿ����� ��� ���ι�ȣ : $inipay->getResult('CSHR_CancelNum')	*
* (���ݿ����� �߱� ��ҽÿ��� ���ϵ�)								*
********************************************************************/

//--- �α� ����
$settlelog	= '';
$settlelog	.= '===================================================='.chr(10);
$settlelog	.= '�ֹ���ȣ : '.$ordno.chr(10);
$settlelog	.= '�ŷ���ȣ : '.$crdata['tid'].chr(10);
$settlelog	.= '����ڵ� : '.$inipay->GetResult('ResultCode').chr(10);
$settlelog	.= '������� : '.$inipay->GetResult('ResultMsg').chr(10);
$settlelog	.= '��ҳ�¥ : '.$inipay->GetResult('CancelDate').chr(10);
$settlelog	.= '��ҽð� : '.$inipay->GetResult('CancelTime').chr(10);

//--- ���ο��� / ���� ����� ���� ó�� ����
if($inipay->GetResult('ResultCode') == "00"){
	// PG ���
	$getPgResult	= true;

	$settlelog	.= '���ݿ����� ��� ���ι�ȣ : '.$inipay->GetResult('CSHR_CancelNum').chr(10);
	$settlelog	= '===================================================='.chr(10).'���ݿ����� ��� ���� : ��ҿϷ�ð�('.date('Y-m-d H:i:s').')'.chr(10).$settlelog.'===================================================='.chr(10);
} else {
	// PG ���
	$getPgResult	= false;

	$settlelog	= '===================================================='.chr(10).'���ݿ����� ��� ���� : ��ҿ����ð�('.date('Y-m-d H:i:s').')'.chr(10).$settlelog.'===================================================='.chr(10);
}

//--- ��� ó��
if( $getPgResult === true )
{
	$db->query("UPDATE ".GD_CASHRECEIPT." SET moddt=now(),status='CCR',errmsg='',receiptlog=concat(if(receiptlog is null,'',receiptlog),'".$settlelog."') WHERE crno='".$_GET['crno']."'");
	echo '<script>if(parent.opener == null) window.onload = function (){ parent.parent.location.reload(); }</script>';
}
else
{
	$db->query("UPDATE ".GD_CASHRECEIPT." SET errmsg='".$inipay->GetResult('ResultCode').":".$inipay->GetResult('ResultMsg')."',moddt=now(),receiptlog=concat(if(receiptlog is null,'',receiptlog),'".$settlelog."') WHERE crno='".$_GET['crno']."'");
	echo '<script>if(parent.opener == null) window.onload = function (){ parent.parent.location.reload(); }</script>';
}
?>