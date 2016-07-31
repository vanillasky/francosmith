<?php

/* INIcancel.php
 *
 * �̹� ���ε� ������ ����Ѵ�.
 * ������� ��ü , �������Ա��� �� ����� ���� ��� �Ұ���.
 *  [���������ü�� �������� ��ȸ������ (https://iniweb.inicis.com)�� ���� ��� ȯ�� �����ϸ�, �������Ա��� ��� ����� �����ϴ�.]
 *
 * Date : 2006/04
 * Author : ts@inicis.com
 * Project : INIpay V4.11 for Unix
 *
 * http://www.inicis.com
 * Copyright (C) 2006 Inicis, Co. All rights reserved.
 */


/**************************
 * 1. ���̺귯�� ��Ŭ��� *
 **************************/
include dirname(__FILE__).'/../../../../conf/pg.inicis.php';
require(dirname(__FILE__).'/INIpay41Lib.php');
$ordno = $crdata['ordno'];


/***************************************
 * 2. INIpay41 Ŭ������ �ν��Ͻ� ���� *
 ***************************************/
$inipay = new INIpay41;


/*********************
 * 3. ��� ���� ���� *
 *********************/
$inipayhome = substr(dirname(__FILE__),0,-7);
$inipay->m_inipayHome = $inipayhome; // �̴����� Ȩ���͸�
$inipay->m_type = 'cancel'; // ����
$inipay->m_pgId = 'INIpayRECP'; // ����
$inipay->m_subPgIp = '203.238.3.10'; // ����
$inipay->m_keyPw = '1111'; // Ű�н�����(�������̵� ���� ����)
$inipay->m_debug = 'true'; // �α׸��('true'�� �����ϸ� �󼼷αװ� ������.)
$inipay->m_mid = $pg['id']; // �������̵�
$inipay->m_tid = $crdata['tid']; // ����� �ŷ��� �ŷ����̵�
$inipay->m_cancelMsg = ''; // ��һ���
$inipay->m_uip = getenv('REMOTE_ADDR'); // ����


/****************
 * 4. ��� ��û *
 ****************/
$inipay->startAction();


/****************************************************************
 * 5. ��� ���                                           	*
 *                                                        	*
 * ����ڵ� : $inipay->m_resultCode ('00'�̸� ��� ����)  	*
 * ������� : $inipay->m_resultMsg (��Ұ���� ���� ����) 	*
 * ��ҳ�¥ : $inipay->m_pgCancelDate (YYYYMMDD)          	*
 * ��ҽð� : $inipay->m_pgCancelTime (HHMMSS)            	*
 * ���ݿ����� ��� ���ι�ȣ : $inipay->m_rcash_cancel_noappl    *
 * (���ݿ����� �߱� ��ҽÿ��� ���ϵ�)                          *
 ****************************************************************/
if( !strcmp($inipay->m_resultCode,'00') )
{
	$settlelog = $ordno.' ('.date('Y:m:d H:i:s').')'."\n";
	$settlelog .= '-----------------------------------'."\n";
	$settlelog .= '���ݿ����� ��� ����'."\n";
	$settlelog .= '����ڵ� : '.$inipay->m_resultCode."\n";
	$settlelog .= '������� : '.$inipay->m_resultMsg."\n";
	$settlelog .= '����Ͻ� : '.$inipay->m_pgCancelDate.' '.$inipay->m_pgCancelTime."\n";
	$settlelog .= '��� ���ι�ȣ : '.$inipay->m_rcash_cancel_noappl."\n";
	$settlelog .= '-----------------------------------'."\n";
	echo nl2br($settlelog);

	$db->query("update gd_cashreceipt set moddt=now(),status='CCR',errmsg='',receiptlog=concat(if(receiptlog is null,'',receiptlog),'{$settlelog}') where crno='{$_GET['crno']}'");
	echo '<script>if(parent.opener == null) window.onload = function (){ parent.parent.location.reload(); }</script>';
}
else {
	$settlelog = $ordno.' ('.date('Y:m:d H:i:s').')'."\n";
	$settlelog .= '-----------------------------------'."\n";
	$settlelog .= '���ݿ����� ��� ����'."\n";
	$settlelog .= '����ڵ� : '.$inipay->m_resultCode."\n";
	$settlelog .= '������� : '.$inipay->m_resultMsg."\n";
	$settlelog .= '-----------------------------------'."\n";
	echo nl2br($settlelog);

	$db->query("update gd_cashreceipt set errmsg='{$inipay->m_resultCode}:{$inipay->m_resultMsg}',moddt=now(),receiptlog=concat(if(receiptlog is null,'',receiptlog),'\n{$settlelog}') where crno='{$_GET['crno']}'");
	echo '<script>if(parent.opener == null) window.onload = function (){ parent.parent.location.reload(); }</script>';
}
?>