<?php
/**
 * �̴Ͻý� PG ����ũ�� ���� Ȯ�� ó�� ������
 * ���� ���ϸ� INIescrow_denyconfirm.php
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

$iniescrow->SetField('escrowtype', 'dcnf');					// ���� (���� ���� �Ұ�)
$iniescrow->SetField('dcnf_name', $dcnf_name);				// ���Ű��� Ȯ����
$iniescrow->SetField('debug','true');						// �α׸��('true'�� �����ϸ� ���� �αװ� ������)

//--- ���� Ȯ�� ��û
$iniescrow->startAction();

//--- ���� Ȯ�� ���
$tid		= $iniescrow->GetResult('tid');					// �ŷ���ȣ
$resultCode	= $iniescrow->GetResult('ResultCode');			// ����ڵ� ('00'�̸� ���� ����)
$resultMsg	= $iniescrow->GetResult('ResultMsg');			// ������� (���Ұ���� ���� ����)
$resultDate	= $iniescrow->GetResult('DCNF_Date');			// ó�� ��¥
$resultTime	= $iniescrow->GetResult('DCNF_Time');			// ó�� �ð�

//--- �ֹ���ȣ ó��
$ordno		= $_POST['ordno'];

//--- �α� ����
$settlelog	= '';
$settlelog	.= '===================================================='.chr(10);
$settlelog	.= '�ֹ���ȣ : '.$ordno.chr(10);
$settlelog	.= '�ŷ���ȣ : '.$iniescrow->GetResult('TID').chr(10);
$settlelog	.= '����ڵ� : '.$iniescrow->GetResult('ResultCode').chr(10);
$settlelog	.= '������� : '.$iniescrow->GetResult('ResultMsg').chr(10);
$settlelog	.= 'ó����¥ : '.$iniescrow->GetResult('DCNF_Date').chr(10);
$settlelog	.= 'ó���ð� : '.$iniescrow->GetResult('DCNF_Time').chr(10);
$settlelog	.= 'ó����IP : '.$_SERVER['REMOTE_ADDR'].chr(10);

//--- ���ο��ο� ���� ó�� ����
if($iniescrow->GetResult('ResultCode') == "00"){

	// PG ���
	$getPgResult		= true;
	$settlelog	= '===================================================='.chr(10).'����ũ�� ���� Ȯ�� : ó���Ϸ�ð�('.date('Y-m-d H:i:s').')'.chr(10).$settlelog.'===================================================='.chr(10);
} else {
	$settlelog	= '===================================================='.chr(10).'����ũ�� ���� Ȯ�� : ����Ȯ�νð�('.date('Y-m-d H:i:s').')'.chr(10).$settlelog.'===================================================='.chr(10);

	// PG ���
	$getPgResult		= false;
}

//--- ������ ��� ó��
if( $getPgResult === true ){
	// �ǵ���Ÿ ����
	$db->query("
	UPDATE ".GD_ORDER." SET
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

msg($resultMsg,'close');
exit;
?>