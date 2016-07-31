<?php

//*******************************************************************************
// FILE NAME : vacctinput.php
// DATE : 2010.08
// �̴Ͻý� ������� �Աݳ��� ó��demon���� �Ѿ���� �Ķ���͸� control �ϴ� �κ� �Դϴ�.
//*******************************************************************************

//**********************************************************************************
//�̴Ͻý��� �����ϴ� ���������ü�� ����� �����Ͽ� DB ó�� �ϴ� �κ� �Դϴ�.
//�ʿ��� �Ķ���Ϳ� ���� DB �۾��� �����Ͻʽÿ�.
//**********************************************************************************

@extract($_POST);

//**********************************************************************************
//  �̺κп� �α����� ��θ� �������ּ���.

$INIpayHome = realpath(dirname(__FILE__).'/../');      // �̴����� Ȩ���͸�

//**********************************************************************************


$TEMP_IP = $_SERVER['REMOTE_ADDR'];
$PG_IP  = substr($TEMP_IP,0, 10);

//if( $PG_IP == "203.238.37" || $PG_IP == "210.98.138" )  //PG���� ���´��� IP�� üũ
{
        $logfile = fopen( $INIpayHome . "/log/result.log", "a+" );

        fwrite( $logfile,"************************************************\r\n");
        fwrite( $logfile,"ID_MERCHANT : ".$id_merchant."\r\n");
        fwrite( $logfile,"P_TID : ".$P_TID."\r\n");
        fwrite( $logfile,"P_TYPE : ".$P_TYPE."\r\n");
		fwrite( $logfile,"P_STATUS : ".$P_STATUS."\r\n");
		fwrite( $logfile,"P_AUTH_DT : ".$P_AUTH_DT."\r\n");
		fwrite( $logfile,"P_OID : ".$P_OID."\r\n");
		fwrite( $logfile,"P_FN_CD1 : ".$P_FN_CD1."\r\n");
		fwrite( $logfile,"P_FN_CD2 : ".$P_FN_CD2."\r\n");
		fwrite( $logfile,"P_FN_NM : ".$P_FN_NM."\r\n");
		fwrite( $logfile,"P_AMT : ".$P_AMT."\r\n");
        fwrite( $logfile,"P_RMESG1 : ".$P_RMESG1."\r\n");
        fwrite( $logfile,"P_UNAME : ".$P_UNAME."\r\n");
        fwrite( $logfile,"************************************************\r\n");

        fclose( $logfile );

include "../../../../lib/library.php";
include "../../../../conf/config.php";

//�񵿱� �ֹ� �� �������
function cancel_inicis($ordno,$cardtno,$settlelog,$claimReason) {
	include SHOPROOT."/conf/pg.inicis.php";
	global $db;
	require_once(SHOPROOT.'/order/card/inicis/sample/INIpay41Lib.php');
	$inipay = new INIpay41;
	$inipay->m_inipayHome = SHOPROOT.'/order/card/inicis/'; // �̴����� Ȩ���͸�
	$inipay->m_type = 'cancel'; // ����
	$inipay->m_pgId = 'INIpayRECP'; // ����
	$inipay->m_subPgIp = '203.238.3.10'; // ����
	$inipay->m_keyPw = '1111'; // Ű�н�����(�������̵� ���� ����)
	$inipay->m_debug = 'true'; // �α׸��('true'�� �����ϸ� �󼼷αװ� ������.)
	$inipay->m_mid = $pg['id']; // �������̵�
	$inipay->m_tid = $cardtno; // ����� �ŷ��� �ŷ����̵�
	$inipay->m_cancelMsg = $claimReason; // ��һ���
	$inipay->m_uip = getenv('REMOTE_ADDR'); // ����
	$inipay->startAction();

	$query = "update ".GD_ORDER." set settlelog	= concat(IFNULL(settlelog,''),'$settlelog') where ordno=".$ordno;
	$db->query($query);
}

// KB ����ϰ����� P_TYPE �� ISP�� �ƴ� CARD�� ���޵Ǿ� ����ó�� (ó���ϴ� ������ �Ʒ��� ����)
if($P_TYPE == 'CARD') {
	include dirname(__FILE__).'/vacctinput_card.php';
	exit;
}

// ISP, ������ü�� ��� card_return.php�� ��ġ�� �ʱ⶧���� ���̹� ���ϸ��� ���� ���� API ȣ��
if ($P_TYPE == 'ISP' || $P_TYPE == 'BANK') {
	include dirname(__FILE__).'/../../../../lib/naverNcash.class.php';
	$naverNcash = new naverNcash(true);
	if ($naverNcash->useyn == 'Y') {
		$ncashResult = $naverNcash->payment_approval($P_OID, true);
		if ($ncashResult === false) {
			exit("OK");
		}
	}
}

$ordno = $P_OID;
if (!$ordno) exit;

if($P_TYPE == "VBANK") //���������� ��������̸�
{
	if($P_STATUS != "02") //�Ա��뺸 "02" �� �ƴϸ�(������� ä�� : 00 �Ǵ� 01 ���)
	{
		echo "OK";
		exit();
	}
}

if($P_TYPE == "ISP") //���������� ISP�϶�
{
	// PG���� ������ üũ �� ��ȿ�� üũ
	if (forge_order_check($P_OID,$P_AMT) === false) {
		$claimReason = $P_RMESG1."->�ڵ� �������(��ǰ�ݾװ� �����ݾ��� ��ġ���� ����.)";
		$settlelog = "
		----------------------------------------
		������ȣ : ".$P_TID."
		������� : ".$P_TYPE."
		����ڵ� : ".$P_STATUS."
		���νð� : ".$P_AUTH_DT."
		�ֹ���ȣ : ".$P_OID."
		������� : ".$P_FN_NM."
		�ŷ��ݾ� : ".$P_AMT."
		������� : ".$claimReason."
		----------------------------------------
		";
		cancel_inicis($ordno,$P_TID,$settlelog,$claimReason);
		exit('OK');
	}

	if($P_STATUS != "00") //���� "00" �� �ƴϸ�
	{
		// ISP, ������ü ���� �� ���̹� ���ϸ��� ���� ���� ��� API ȣ��
		if ($P_TYPE == 'ISP' || $P_TYPE == 'BANK') {
			if ($naverNcash->useyn == 'Y') $naverNcash->payment_approval_cancel($P_OID);
		}
		echo "OK";
		exit();
	}
}

$settlelog = "
----------------------------------------
�Ա�Ȯ�� : PG���ڵ��Ա�Ȯ��
���νð� : ".$P_AUTH_DT."
����ڵ� : ".$P_STATUS."
Ȯ�νð� : ".date('Y:m:d H:i:s')."
�Աݱݾ� : ".$P_AMT."
----------------------------------------
";

if($P_TYPE == "ISP"){
$settlelog = "
----------------------------------------
������ȣ : ".$P_TID."
������� : ".$P_TYPE."
����ڵ� : ".$P_STATUS."
���νð� : ".$P_AUTH_DT."
�ֹ���ȣ : ".$P_OID."
������� : ".$P_FN_NM."
�ŷ��ݾ� : ".$P_AMT."
�ŷ���� : ".$P_RMESG1."
----------------------------------------
";
}
	### item check stock
	include "../../../../lib/cardCancel.class.php";
	$cancel = new cardCancel();
	if(!$cancel->chk_item_stock($ordno) && $cfg['stepStock'] == '1'){
		$cancel -> cancel_db_proc($ordno,$P_TID);
	}else{
		$query = "
		select * from
			".GD_ORDER." a
			left join ".GD_LIST_BANK." b on a.bankAccount = b.sno
		where
			a.ordno='$ordno'
		";
		$data = $db->fetch($query);

		### ���� ���� ����
		$step = 1;

		### �ǵ���Ÿ ����
		$db->query("
		update ".GD_ORDER." set cyn='y', cdt=now(),
			step		= '1',
			step2		= '',
			cardtno		= '$P_TID',
			settlelog	= concat(IFNULL(settlelog,''),'$settlelog')
		where ordno='$ordno'"
		);

		$db->query("update ".GD_ORDER_ITEM." set cyn='y', istep='1' where ordno='$ordno'");

		### �ֹ��α� ����
		orderLog($ordno,$r_step[$data[step]]." > ".$r_step[$step]);

		### ��� ó��
		setStock($ordno);

		// ��ǰ���Խ� ������ ���
		if ($data['m_no'] && $data['emoney'] && $P_TYPE == 'ISP') {
			setEmoney($data['m_no'], -$data['emoney'], '��ǰ���Խ� ������ ���� ���', $ordno);
		}

		### �Ա�Ȯ�θ���
		sendMailCase($data[email],1,$data);

		### �Ա�Ȯ��SMS
		$dataSms = $data;
		sendSmsCase('incash',$data[mobileOrder]);

		// ������� �Ա��뺸 �϶� ���̹� ���ϸ��� �ŷ� Ȯ�� API ȣ��
		if ($P_TYPE == 'VBANK') {
			include dirname()."/../../../../lib/naverNcash.class.php";
			$naverNcash = new naverNcash();
			$naverNcash->deal_done($ordno);
		}
	}

//************************************************************************************

        //������ ���� �����ͺ��̽��� ��� ���������� ���� �����ÿ��� "OK"�� �̴Ͻý���
        //�����ϼž��մϴ�. �Ʒ� ���ǿ� �����ͺ��̽� ������ �޴� FLAG ������ ��������
        //(����) OK�� �������� �����ø� �̴Ͻý� ���� ������ "OK"�� �����Ҷ����� ��� �������� �õ��մϴ�
        //��Ÿ �ٸ� ������ PRINT( echo )�� ���� �����ñ� �ٶ��ϴ�

//      if (�����ͺ��̽� ��� ���� ���� ���Ǻ��� = true)
//      {

                echo "OK";                        // ����� ������������

//      }

//*************************************************************************************

}
?>