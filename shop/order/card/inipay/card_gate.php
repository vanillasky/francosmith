<?php
/**
 * �̴Ͻý� PG ��� ������
 * ���� ���ϸ� INIsecurepaystart.php
 * �̴Ͻý� PG ���� : INIpay V5.0 - ������ (V 0.1.1 - 20120302)
 */

include "../conf/pg.inipay.php";
@include "../conf/pg.escrow.php";

//--- ����ũ�� ������ pgId ����
if ($_POST['escrow'] == "Y") {
	$pg['id']	= $escrow['id'];
}

//--- ���̺귯�� ��Ŭ���
require_once dirname(__FILE__).'/libs/INILib.php';

//--- INIpay50 Ŭ������ �ν��Ͻ� ����
$inipay = new INIpay50;

//--- ������ ����
if ($pg['zerofee'] == 'yes') {
 $quotabase  = $pg['quota'].'('.$pg['zerofee_period'].')';
} else {
 $quotabase  = $pg['quota'];
}

//--- ��ȣȭ ���/�� ����
$inipay->SetField('inipayhome', dirname(__FILE__));		// �̴����� Ȩ���͸�
$inipay->SetField('type', 'chkfake');					// ���� (���� ���� �Ұ�)
$inipay->SetField('debug', 'true');						// �α׸��('true'�� �����ϸ� �󼼷αװ� ������.)
$inipay->SetField('enctype', 'asym');					// asym:���Ī, symm:��Ī(���� asym���� ����)
$inipay->SetField('admin', '1111');						// Ű�н�����(Ű�߱޽� ����, ���������� �н������ �������)
$inipay->SetField('checkopt', 'false');					// base64��:false, base64����:true(���� false�� ����)
$inipay->SetField('mid', $pg['id']);					// �������̵�
$inipay->SetField('price', $_POST['settleprice']);		// ����
$inipay->SetField('nointerest', $pg['zerofee']);		// �����ڿ���(no:�Ϲ�, yes:������)
$inipay->SetField('quotabase', $quotabase);			// �ҺαⰣ

// --- ��ȣȭ ���/���� ��ȣȭ��
$inipay->startAction();

//--- ��ȣȭ ���
if ($inipay->GetResult('ResultCode') != '00'){
	msg($inipay->GetResult('ResultMsg'));
	exit();
}

//--- �������� ����
$_SESSION['INI_MID']		= $pg['id'];						// ����ID
$_SESSION['INI_ADMIN']		= '1111';							// Ű�н�����(Ű�߱޽� ����, ���������� �н������ �������)
$_SESSION['INI_PRICE']		= $_POST['settleprice'];			// ����
$_SESSION['INI_RN']			= $inipay->GetResult('rn');			// ���� (���� ���� �Ұ�)
$_SESSION['INI_ENCTYPE']	= $inipay->GetResult('enctype');	// ���� (���� ���� �Ұ�)

//--- ���� ���� ����
$tmpSettleCode	= array(
	'c'		=> 'onlycard',
	'o'		=> 'onlydbank',
	'v'		=> 'onlyvbank',
	'h'		=> 'onlyhpp',
	'y'		=> 'onlyypay',
);
$settlekindCode	= $tmpSettleCode[$_POST['settlekind']];

//--- ��ǰ�� ����
if(!preg_match('/mypage/',$_SERVER['SCRIPT_NAME'])){
	$item = $cart -> item;
}
foreach($item as $v){
	$i++;
	if($i == 1) $ordnm = str_replace("`", "'", $v[goodsnm]);
}
if($i > 1)$ordnm .= " ��".($i-1)."��";
$ordnm	= pg_text_replace(strip_tags($ordnm));

//--- �̴Ͻý� �ڵ� assign
$tpl->assign('INIConfEncfield',$inipay->GetResult("encfield"));
$tpl->assign('INIConfCertid',$inipay->GetResult("certid"));
?>