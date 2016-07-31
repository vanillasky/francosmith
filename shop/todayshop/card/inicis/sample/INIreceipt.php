<?php

/* INIreceipt.php
 *
 * ���ݰ���(�ǽð� ���������ü, �������Ա�)�� ���� ���ݰ��� ������ ���� ��û�Ѵ�.
 *
 *
 *
 * Date : 2004/12
 * Author : izzylee@inicis.com
 * Project : INIpay V4.11 for Unix
 *
 * http://www.inicis.com
 * http://support.inicis.com
 * Copyright (C) 2002 Inicis, Co. All rights reserved.
 */


/**************************
 * 1. ���̺귯�� ��Ŭ��� *
 **************************/
if (isset($_GET['crno']) === false)
{
	include dirname(__FILE__).'/../../../../lib/library.php';
	include dirname(__FILE__).'/../../../../conf/config.pay.php';
	extract($_POST);

	### �ݾ� ����Ÿ ��ȿ�� üũ
	$data = $db->fetch("select * from gd_order where ordno='{$ordno}'",1);
	if ($set['receipt']['compType'] == '1'){ // �鼼/���̻����
		$data['supply'] = $data['prn_settleprice'];
		$data['vat'] = 0;
	}
	else { // ���������
		$data['supply'] = round($data['prn_settleprice'] / 1.1);
		$data['vat'] = $data['prn_settleprice'] - $data['supply'];
	}
	if ($data['supply']!=$_POST['sup_price'] || $data['vat']!=$_POST['tax']) msg('�ݾ��� ��ġ���� �ʽ��ϴ�',-1);

	// �߱޻���üũ(�����ý��۰��)
	if ($data['cashreceipt'] != '' && file_exists(dirname(__FILE__).'/../../../../lib/cashreceipt.class.php') === false) {
		msg('���ݿ����� �����û����!! \\n['.$ordno.'] �ֹ��� �̹� ����Ǿ����ϴ�.');
		exit;
	}

	### ���ݿ�������û���� �߰�
	@include dirname(__FILE__).'/../../../../lib/cashreceipt.class.php';
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
		$indata['goodsnm'] = $_POST['goodname'];
		$indata['buyername'] = $_POST['buyername'];
		$indata['useopt'] = $_POST['useopt'];
		$indata['certno'] = $_POST['reg_num'];
		$indata['amount'] = $_POST['cr_price'];
		$indata['supply'] = $_POST['sup_price'];
		$indata['surtax'] = $_POST['tax'];

		$cashreceipt = new cashreceipt();
		$crno = $cashreceipt->putReceipt($indata);
	}
}
else {
	$ordno = $crdata['ordno'];
	$goodname = $crdata['goodsnm'];
	$cr_price = $crdata['amount'];
	$sup_price = $crdata['supply'];
	$tax = $crdata['surtax'];
	$srvc_price = 0;
	$buyername = $crdata['buyername'];
	$buyeremail = $crdata['buyeremail'];
	$buyertel = $crdata['buyerphone'];
	$reg_num = $crdata['certno'];
	$useopt = $crdata['useopt'];
	$crno = $_GET['crno'];
}
include dirname(__FILE__).'/../../../../conf/pg.inicis.php';
require(dirname(__FILE__).'/INIpay41Lib.php');


/***************************************
 * 2. INIpay41 Ŭ������ �ν��Ͻ� ���� *
 ***************************************/
$inipay = new INIpay41;


/*********************
 * 3. �߱� ���� ���� *
 *********************/
$inipayhome = substr(dirname(__FILE__),0,-7);
$inipay->m_inipayHome = $inipayhome; // �̴����� Ȩ���͸�
$inipay->m_type = 'receipt'; // ����
$inipay->m_pgId = 'INIpayRECP'; // ����
$inipay->m_payMethod = 'CASH'; // ���� (��û�з�)
$inipay->m_subPgIp = '203.238.3.10'; // ����
$inipay->m_currency = 'WON'; // ȭ����� (����)
$inipay->m_keyPw = '1111'; // Ű�н�����(�������̵� ���� ����)
$inipay->m_debug = 'true'; // �α׸��('true'�� �����ϸ� �󼼷αװ� ������.)
$inipay->m_mid = $pg['id']; // �������̵�
$inipay->m_uip = getenv('REMOTE_ADDR'); // ����
$inipay->m_goodName = $goodname; // ��ǰ��
$inipay->m_cr_price = $cr_price; // �� ���ݰ��� �ݾ�
$inipay->m_sup_price = $sup_price; // ���ް���
$inipay->m_tax = $tax; // �ΰ���
$inipay->m_srvc_price = $srvc_price; // �����
$inipay->m_buyerName = $buyername; // ������ ����
$inipay->m_buyerEmail = $buyeremail; // ������ �̸��� �ּ�
$inipay->m_buyerTel = $buyertel; // ������ ��ȭ��ȣ
$inipay->m_reg_num = $reg_num; // ���ݰ����� �ֹε�Ϲ�ȣ
$inipay->m_useopt = $useopt; // ���ݿ����� ����뵵 ('0' - �Һ��� �ҵ������, '1' - ����� ����������)

/*----------------------------------------------------------------------------------------*
 * ����� ����ڵ�Ϲ�ȣ *                                                                *
 *----------------------------------------------------------------------------------------*
 * ���¸��ϰ� ���� ������� �����ϴ� ��� �ݵ�� ����� ����ڵ�Ϲ�ȣ�� �Է��ؾ��մϴ�.  *
 * ����� ����ڵ�Ϲ�ȣ�� �Է����� �ʰ� ���ݿ������� �߱��ϴ� ��� �������̵� �ش��ϴ� *
 * ���ݿ������� �߱޵Ǿ� ����� ����ڷ� ���ݿ������� �߱޵��� �ʽ��ϴ�.                  *
 * ��� ������ �ݵ�� �����ֽñ� �ٶ��, �� ������ ��Ű�� �ʾ� �߻��� ������ ���ؼ���     *
 * (��)�̴Ͻý��� å���� ������ �����Ͻñ� �ٶ��ϴ�.                                      *
 *                                                                                        *
 * ����� ���ݿ������� �߱��Ͻ÷��� �Ʒ� �ʵ� �ּ��� ���� �Ͻð� ����Ͻñ� �ٶ��ϴ�.     *
 *----------------------------------------------------------------------------------------*/

 //$inipay->m_companyNumber = '222333444';              // ����� ����� ��Ϲ�ȣ


/****************
 * 4. �߱� ��û *
 ****************/
$inipay->startAction();


/********************************************************************************
 * 5. �߱� ���                                                                 *
 *                                                                              *
 * ����ڵ� : $inipay->m_rcash_rslt ('0000' �̸� ���� ����)                     *
 * ������� : $inipay->m_resultMsg (�������� ���� ����)                       *
 * ���ι�ȣ : $inipay->m_rcash_noappl (���ݿ����� ���� ���ι�ȣ)                *
 * ���γ�¥ : $inipay->m_pgAuthDate (YYYYMMDD)                                  *
 * ���νð� : $inipay->m_pgAuthTime (HHMMSS)                                    *
 * �ŷ���ȣ : $inipay->m_tid                                                    *
 * �����ݰ��� �ݾ� : $inipay->rcr_price                                         *
 * ���ް��� : $inipay->m_rsup_price                                             *
 * �ΰ��� : $inipay->m_rtax                                                     *
 * ����� : $inipay->m_rsrvc_price                                              *
 * ��뱸�� : $inipay->m_ruseopt                                                *
 ********************************************************************************/
if( !strcmp($inipay->m_rcash_rslt,'0000') )
{
	$settlelog = $ordno.' ('.date('Y:m:d H:i:s').')'."\n";
	$settlelog .= '-----------------------------------'."\n";
	$settlelog .= '���ݿ����� �߱� ����'."\n";
	$settlelog .= '����ڵ� : '.$inipay->m_rcash_rslt."\n";
	$settlelog .= '������� : '.$inipay->m_resultMsg."\n";
	$settlelog .= '���ι�ȣ : '.$inipay->m_rcash_noappl."\n";
	$settlelog .= '�ŷ���ȣ : '.$inipay->m_tid."\n";
	$settlelog .= '�����ݾ� : '.$inipay->rcr_price."\n";
	$settlelog .= '-----------------------------------'."\n";
	echo nl2br($settlelog);

	if (empty($crno) === true)
	{
		$db->query("update gd_order set cashreceipt='{$inipay->m_tid}',settlelog=concat(if(settlelog is null,'',settlelog),'\n{$settlelog}') where ordno='{$ordno}'");
	}
	else {
		# ���ݿ�������û���� ����
		$db->query("update gd_cashreceipt set pg='inicis',cashreceipt='{$inipay->m_tid}',receiptnumber='{$inipay->m_rcash_noappl}',tid='{$inipay->m_tid}',moddt=now(),status='ACK',errmsg='',receiptlog=concat(if(receiptlog is null,'',receiptlog),'\n{$settlelog}') where crno='{$crno}'");
		$db->query("update gd_order set cashreceipt='{$inipay->m_tid}' where ordno='{$ordno}'");
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
	$settlelog .= '����ڵ� : '.$inipay->m_rcash_rslt."\n";
	$settlelog .= '������� : '.$inipay->m_resultMsg."\n";
	$settlelog .= '-----------------------------------'."\n";
	echo nl2br($settlelog);

	if (empty($crno) === true)
	{
		$db->query("update gd_order set settlelog=concat(if(settlelog is null,'',settlelog),'\n{$settlelog}') where ordno='{$ordno}'");
	}
	else {
		# ���ݿ�������û���� ����
		$db->query("update gd_cashreceipt set pg='inicis',errmsg='{$inipay->m_rcash_rslt}:{$inipay->m_resultMsg}',moddt=now(),receiptlog=concat(if(receiptlog is null,'',receiptlog),'\n{$settlelog}') where crno='{$crno}'");
	}

	if (isset($_GET['crno']) === false)
	{
		msg($inipay->m_resultMsg);
		exit;
	}
	else {
		echo '<script>if(parent.opener == null) window.onload = function (){ parent.parent.location.reload(); }</script>';
	}
}

?>