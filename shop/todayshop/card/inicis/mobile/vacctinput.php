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
		fwrite( $logfile,"P_AUTH_DT : ".$P_AUTH_DT."\r\n");
		fwrite( $logfile,"P_OID : ".$P_OID."\r\n");
		fwrite( $logfile,"P_FN_CD1 : ".$P_FN_CD1."\r\n");
		fwrite( $logfile,"P_FN_CD2 : ".$P_FN_CD2."\r\n");
		fwrite( $logfile,"P_FN_NM : ".$P_FN_NM."\r\n");
		fwrite( $logfile,"P_AMT : ".$P_AMT."\r\n");
		fwrite( $logfile,"P_TYPE : ".$P_TYPE."\r\n");
        fwrite( $logfile,"P_RMESG1 : ".$P_RMESG1."\r\n");
        fwrite( $logfile,"P_UNAME : ".$P_UNAME."\r\n");
        fwrite( $logfile,"************************************************\r\n");

        fclose( $logfile );

include "../../../../lib/library.php";
include "../../../../conf/config.php";

$ordno = $P_OID;
if (!$ordno) exit;

$settlelog = "
----------------------------------------
�Ա�Ȯ�� : PG���ڵ��Ա�Ȯ��
���νð� : ".$P_AUTH_DT."
Ȯ�νð� : ".date('Y:m:d H:i:s')."
�Աݱݾ� : ".$P_AMT."
----------------------------------------
";
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

		### �ǵ���Ÿ ����
		$db->query("
		update ".GD_ORDER." set cyn='y', cdt=now(),
			step		= '1',
			step2		= '',
			cardtno		= '$P_TID',
			settlelog	= concat(settlelog,'$settlelog')
		where ordno='$ordno'"
		);
		$db->query("update ".GD_ORDER_ITEM." set cyn='y', istep='1' where ordno='$ordno'");

		### �ֹ��α� ����
		orderLog($ordno,$r_step2[$data[step2]]." > ".$r_step[$step]);

		### ��� ó��
		setStock($ordno);

		### �Ա�Ȯ�θ���
		sendMailCase($data[email],1,$data);

		### �Ա�Ȯ��SMS
		$dataSms = $data;
		sendSmsCase('incash',$data[mobileOrder]);
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