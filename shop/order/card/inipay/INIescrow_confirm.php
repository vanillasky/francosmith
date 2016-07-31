<?php
/**
 * �̴Ͻý� PG ����ũ�� �� ���� Ȯ��(Ȯ�� �Ǵ� ����) ������
 * ���� ���ϸ� INIescrow_confirm.php
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

$iniescrow->SetField('escrowtype', 'confirm');				// ���� (���� ���� �Ұ�)
$iniescrow->SetField('dlv_ip', getenv('REMOTE_ADDR'));		// IP
$iniescrow->SetField('debug','true');						// �α׸��('true'�� �����ϸ� ���� �αװ� ������)

$iniescrow->SetField('encrypted', $encrypted);				// ����
$iniescrow->SetField('sessionkey', $sessionkey);			// ����

//--- ���� Ȯ�� ��û
$iniescrow->startAction();

//--- ���� Ȯ�� ���
$tid			= $iniescrow->GetResult('tid');				// �ŷ���ȣ
$resultCode		= $iniescrow->GetResult('ResultCode');		// ����ڵ� ('00'�̸� ���� ����)
$resultMsg		= $iniescrow->GetResult('ResultMsg');		// ������� (���Ұ���� ���� ����)
$resultDate		= $iniescrow->GetResult('CNF_Date');		// ó�� ��¥
$resultTime		= $iniescrow->GetResult('CNF_Time');		// ó�� �ð�

// ������ ���
if ($iniescrow->GetResult('CNF_Date') == '') {
	$resultDate	= $iniescrow->GetResult('DNY_Date');		// ó�� ��¥
	$resultTime	= $iniescrow->GetResult('DNY_Time');		// ó�� �ð�
	$confirmFl	= 'reject';
	$resultMsg	= '����ũ�� ������ ��û�Ǿ����ϴ�.';
} else {
	$confirmFl	= 'accept';
	$resultMsg	= '����ũ�� ����Ȯ���� �Ϸ� �Ǿ����ϴ�.';
}

//--- �ֹ���ȣ ó��
$ordno		= $_POST['ordno'];

//--- �α� ����
$settlelog	= '';
$settlelog	.= '===================================================='.chr(10);
$settlelog	.= '�ֹ���ȣ : '.$ordno.chr(10);
$settlelog	.= '����ڵ� : '.$iniescrow->GetResult('ResultCode').chr(10);
$settlelog	.= '������� : '.$iniescrow->GetResult('ResultMsg').chr(10);
if ($confirmFl == 'accept') {
	$settlelog	.= '����Ȯ����¥ : '.$resultDate.chr(10);
	$settlelog	.= '����Ȯ���ð� : '.$resultTime.chr(10);
} else {
	$settlelog	.= '���Ű�����¥ : '.$resultDate.chr(10);
	$settlelog	.= '���Ű����ð� : '.$resultTime.chr(10);
}
$settlelog	.= 'IP : '.$_SERVER['REMOTE_ADDR'].chr(10);

//--- ����� ���� ó�� ����
if($iniescrow->GetResult('ResultCode') == "00"){

	// PG ���
	$getPgResult		= true;

	$settlelog	= '===================================================='.chr(10).'����ũ�� ���� Ȯ�� : ó���Ϸ�ð�('.date('Y-m-d H:i:s').')'.chr(10).$settlelog.'===================================================='.chr(10);
} else {
	$settlelog	= '===================================================='.chr(10).'����ũ�� ���� Ȯ�� : ����Ȯ�νð�('.date('Y-m-d H:i:s').')'.chr(10).$settlelog.'===================================================='.chr(10);

	// PG ���
	$getPgResult		= true;
	$resultMsg			= '����ũ�� ����Ȯ�ο� ������ �ֽ��ϴ�.';
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
