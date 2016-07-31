<?php

//*******************************************************************************
// FILE NAME : INIpayResult.php
// DATE : 2006.05
// �̴Ͻý� ������� �Աݳ��� ó��demon���� �Ѿ���� �Ķ���͸� control �ϴ� �κ� �Դϴ�.
//*******************************************************************************

//**********************************************************************************
//�̴Ͻý��� �����ϴ� ���������ü�� ����� �����Ͽ� DB ó�� �ϴ� �κ� �Դϴ�.
//�ʿ��� �Ķ���Ϳ� ���� DB �۾��� �����Ͻʽÿ�.
//**********************************************************************************

@extract($_GET);
@extract($_POST);
@extract($_SERVER);


//**********************************************************************************
//  �̺κп� �α����� ��θ� �������ּ���.

$INIpayHome = dirname($_SERVER['SCRIPT_FILENAME']);      // �̴����� Ȩ���͸�

//**********************************************************************************


$TEMP_IP = getenv("REMOTE_ADDR");
$PG_IP  = substr($TEMP_IP,0, 10);

if( $PG_IP == "203.238.37" || $PG_IP == "210.98.138" )  //PG���� ���´��� IP�� üũ
{
        $msg_id = $msg_id;                              //�޼��� Ÿ��
        $no_tid = $no_tid;                              //�ŷ���ȣ
        $no_oid = $no_oid;                              //���� �ֹ���ȣ
        $id_merchant = $id_merchant;    //���� ���̵�
        $cd_bank = $cd_bank;                    //�ŷ� �߻� ��� �ڵ�
        $cd_deal = $cd_deal;                    //��� ��� �ڵ�
        $dt_trans = $dt_trans;                  //�ŷ� ����
        $tm_trans = $tm_trans;                  //�ŷ� �ð�
        $no_msgseq = $no_msgseq;                //���� �Ϸ� ��ȣ
        $cd_joinorg = $cd_joinorg;              //���� ��� �ڵ�

        $dt_transbase = $dt_transbase;  //�ŷ� ���� ����
        $no_transeq = $no_transeq;              //�ŷ� �Ϸ� ��ȣ
        $cl_msg = $cl_msg;                              //���� ���� �ڵ�
        $cl_trans = $cl_trans;                  //�ŷ� �����ڵ�
        $cl_close = $cl_close;                  //���� �����ڵ�
        $cl_kor = $cl_kor;                              //�ѱ� ���� �ڵ�
        $no_msgmanage = $no_msgmanage;  //���� ���� ��ȣ
        $no_vacct = $no_vacct;                  //������¹�ȣ
        $amt_input = $amt_input;                //�Աݱݾ�
        $amt_check = $amt_check;                //�̰��� Ÿ���� �ݾ�
        $nm_inputbank = $nm_inputbank;  //�Ա� ���������
        $nm_input = $nm_input;                  //�Ա� �Ƿ���
        $dt_inputstd = $dt_inputstd;    //�Ա� ���� ����
        $dt_calculstd = $dt_calculstd;  //���� ���� ����
        $flg_close = $flg_close;                //���� ��ȭ

        $logfile = fopen( $INIpayHome . "/log/result.log", "a+" );


        fwrite( $logfile,"************************************************\r\n");
        fwrite( $logfile,"ID_MERCHANT : ".$id_merchant."\r\n");
        fwrite( $logfile,"NO_TID : ".$no_tid."\r\n");
        fwrite( $logfile,"NO_OID : ".$no_oid."\r\n");
        fwrite( $logfile,"NO_VACCT : ".$no_vacct."\r\n");
        fwrite( $logfile,"AMT_INPUT : ".$amt_input."\r\n");
        fwrite( $logfile,"NM_INPUTBANK : ".$nm_inputbank."\r\n");
        fwrite( $logfile,"NM_INPUT : ".$nm_input."\r\n");
        fwrite( $logfile,"************************************************\r\n");

        /*
        fwrite( $logfile,"��ü �����"."\r\n");
        fwrite( $logfile, $msg_id."\r\n");
        fwrite( $logfile, $no_tid."\r\n");
        fwrite( $logfile, $no_oid."\r\n");
        fwrite( $logfile, $id_merchant."\r\n");
        fwrite( $logfile, $cd_bank."\r\n");
        fwrite( $logfile, $dt_trans."\r\n");
        fwrite( $logfile, $tm_trans."\r\n");
        fwrite( $logfile, $no_msgseq."\r\n");
        fwrite( $logfile, $cl_trans."\r\n");
        fwrite( $logfile, $cl_close."\r\n");
        fwrite( $logfile, $cl_kor."\r\n");
        fwrite( $logfile, $no_msgmanage."\r\n");
        fwrite( $logfile, $no_vacct."\r\n");
        fwrite( $logfile, $amt_input."\r\n");
        fwrite( $logfile, $amt_check."\r\n");
        fwrite( $logfile, $nm_inputbank."\r\n");
        fwrite( $logfile, $nm_input."\r\n");
        fwrite( $logfile, $dt_inputstd."\r\n");
        fwrite( $logfile, $dt_calculstd."\r\n");
        fwrite( $logfile, $flg_close."\r\n");
        fwrite( $logfile, "\r\n");
        */

        fclose( $logfile );

include "../../../lib/library.php";
include "../../../conf/config.php";

$ordno = $no_oid;
if (!$ordno) exit;

$settlelog = "
----------------------------------------
�Ա�Ȯ�� : PG���ڵ��Ա�Ȯ��
Ȯ�νð� : ".date('Y:m:d H:i:s')."
�Աݱݾ� : $amt_input
----------------------------------------
";
	### item check stock
	include "../../../lib/cardCancel.class.php";
	$cancel = new cardCancel();
	if(!$cancel->chk_item_stock($ordno) && $cfg['stepStock'] == '1'){
		$cancel -> cancel_db_proc($ordno,$no_tid);
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
			cardtno		= '$no_tid',
			settlelog	= concat(settlelog,'$settlelog')
		where ordno='$ordno'"
		);
		$db->query("update ".GD_ORDER_ITEM." set cyn='y', istep='1' where ordno='$ordno'");

		### �ֹ��α� ����
		orderLog($ordno,$r_step[$data[step]]." > ".$r_step[$step]);

		### ��� ó��
		setStock($ordno);

		### �Ա�Ȯ�θ���
		sendMailCase($data[email],1,$data);

		### �Ա�Ȯ��SMS
		$dataSms = $data;
		sendSmsCase('incash',$data[mobileOrder]);

		### Ncash �ŷ� Ȯ�� API
		include "../../../lib/naverNcash.class.php";
		$naverNcash = new naverNcash();
		$naverNcash->deal_done($ordno);
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