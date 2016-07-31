<?php
/**
 * �̴Ͻý� PG ������� �Ա� ó�� ������
 * ���� ���ϸ� vacctinput.php
 * �̴Ͻý� PG ���� : INIpay V5.0 (V 0.1.1 - 20120302)
 */

//--- �⺻ ����
include "../../../lib/library.php";
include "../../../conf/config.php";

//--- ������� extract ��
@extract($_GET);
@extract($_POST);
@extract($_SERVER);

//--- INIpay ���
$INIpayHome	= dirname($_SERVER['SCRIPT_FILENAME']);      // �̴����� Ȩ���͸�

//--- �⺻ ����
$TEMP_IP	= getenv('REMOTE_ADDR');
$PG_IP		= substr($TEMP_IP, 0, 10);

//--- PG���� ���´��� IP�� üũ
if( $PG_IP == '203.238.37' || $PG_IP == '210.98.138' )
{
	$msg_id			= $msg_id;				//�޼��� Ÿ��
	$no_tid			= $no_tid;				//�ŷ���ȣ
	$no_oid			= $no_oid;				//���� �ֹ���ȣ
	$id_merchant	= $id_merchant;			//���� ���̵�
	$cd_bank		= $cd_bank;				//�ŷ� �߻� ��� �ڵ�
	$cd_deal		= $cd_deal;				//��� ��� �ڵ�
	$dt_trans		= $dt_trans;			//�ŷ� ����
	$tm_trans		= $tm_trans;			//�ŷ� �ð�
	$no_msgseq		= $no_msgseq;			//���� �Ϸ� ��ȣ
	$cd_joinorg		= $cd_joinorg;			//���� ��� �ڵ�

	$dt_transbase	= $dt_transbase;		//�ŷ� ���� ����
	$no_transeq		= $no_transeq;			//�ŷ� �Ϸ� ��ȣ
	$type_msg		= $type_msg;			//�ŷ� ���� �ڵ�
	$cl_close		= $cl_close;			//���� �����ڵ�
	$cl_kor			= $cl_kor;				//�ѱ� ���� �ڵ�
	$no_msgmanage	= $no_msgmanage;		//���� ���� ��ȣ
	$no_vacct		= $no_vacct;			//������¹�ȣ
	$amt_input		= $amt_input;			//�Աݱݾ�
	$amt_check		= $amt_check;			//�̰��� Ÿ���� �ݾ�
	$nm_inputbank	= $nm_inputbank;		//�Ա� ���������
	$nm_input		= $nm_input;			//�Ա� �Ƿ���
	$dt_inputstd	= $dt_inputstd;			//�Ա� ���� ����
	$dt_calculstd	= $dt_calculstd;		//���� ���� ����
	$flg_close		= $flg_close;			//���� ��ȭ

	// �������ä���� ���ݿ����� �ڵ��߱޽�û�ÿ��� ����
	$dt_cshr     	= $dt_cshr;				//���ݿ����� �߱�����
	$tm_cshr     	= $tm_cshr;				//���ݿ����� �߱޽ð�
	$no_cshr_appl	= $no_cshr_appl;		//���ݿ����� �߱޹�ȣ
	$no_cshr_tid 	= $no_cshr_tid;			//���ݿ����� �߱�TID

	$logfile		= fopen( $INIpayHome . '/log/INI_vbank_result_'.date('Ymd').'.log', 'a+' );

	// �α� ���� (�̴Ͻý� �α׷� ���Ϸ� ���� �̴Ͻý��� ��� ���� ����)
	$logInfo	 = '------------------------------------------------------------------------------'.chr(10);
	$logInfo	.= 'INFO	['.date('Y-m-d H:i:s').']	START Order log'.chr(10);
	foreach ($_POST as $key => $val) {
		$logInfo	.= 'DEBUG	['.date('Y-m-d H:i:s').']	'.$key.'	: '.$val.chr(10);
	}
	$logInfo	.= 'DEBUG	['.date('Y-m-d H:i:s').']	IP	: '.$_SERVER['REMOTE_ADDR'].chr(10);
	$logInfo	.= 'INFO	['.date('Y-m-d H:i:s').']	END Order log'.chr(10);
	$logInfo	.= '------------------------------------------------------------------------------'.chr(10).chr(10);
	fwrite( $logfile, $logInfo);
	fclose( $logfile );

	//--- �Ա� Ȯ�� ó��
	if (empty($no_oid) === false) {

		//--- �α� ����
		$settlelog	= '===================================================='.chr(10);
		$settlelog	.= '������� �Ա� �ڵ� Ȯ�� : ���� ('.date('Y-m-d H:i:s').')'.chr(10);
		$settlelog	.= '===================================================='.chr(10);
		$settlelog	.= '�ֹ���ȣ : '.$no_oid.chr(10);
		$settlelog	.= '�ŷ���ȣ : '.$no_tid.chr(10);
		$settlelog	.= '���� �Ϸ� ��ȣ : '.$no_msgseq.chr(10);
		$settlelog	.= '�Աݱݾ� : '.number_format($amt_input).chr(10);

		// ���ݿ����� ��� ����
		if (empty($no_cshr_appl) === false && empty($no_cshr_tid) === false) {
			$settlelog	.= '������� : ������� �ڵ��Ա� Ȯ�ο� ���� ó��'.chr(10);
			$settlelog	.= '���ݿ����� �߱޹�ȣ : '.$no_cshr_appl.chr(10);
			$settlelog	.= '���ݿ����� �߱�TID : '.$no_cshr_tid.chr(10);

			$qrc1	= "cashreceipt='".$no_cshr_tid."',";
		}
		$settlelog	.= '===================================================='.chr(10);

		// �ֹ� ��ȣ
		$ordno	= $no_oid;

		// �ֹ� ����
		$query = "
		SELECT * FROM
			".GD_ORDER." a
			LEFT JOIN ".GD_LIST_BANK." b on a.bankAccount = b.sno
		WHERE
			a.ordno='$ordno'
		";
		$data = $db->fetch($query);

		### �ǵ���Ÿ ����
		$db->query("
		UPDATE ".GD_ORDER." SET ".$qrc1." cyn='y', cdt=now(),
			step		= '1',
			step2		= '',
			cardtno		= '$no_tid',
			settlelog	= concat(settlelog,'$settlelog')
		WHERE ordno='$ordno'"
		);
		$db->query("update ".GD_ORDER_ITEM." set cyn='y', istep='1' where ordno='$ordno'");

		### �ֹ��α� ����
		orderLog($ordno,$r_step2[$data[step2]]." > ".$r_step[$step]);

		### ��� ó��
		setStock($ordno);

		### �Ա�Ȯ�θ���
		//sendMailCase($data[email],1,$data);

		### �Ա�Ȯ��SMS
		//$dataSms = $data;
		//sendSmsCase('incash',$data[mobileOrder]);

		// ��� �߱� ���� ���� �� ���� ���� (todayshop_noti Ŭ������ todayshop �� ��ӹ޾ұ� ������ ����� ����ص� ��)
		$todayshop_noti = &load_class('todayshop_noti', 'todayshop_noti');
		$orderinfo = $todayshop_noti->getorderinfo($ordno);
		if ($orderinfo['goodstype'] == 'coupon') { // ������ ���
			if ($orderinfo['processtype'] == 'i') { // ��� �߱� ������ �߱��ϰ� SMS/MAIL
				if (($cp_sno = $todayshop_noti->publishCoupon($ordno)) !== false) {
					$formatter = &load_class('stringFormatter', 'stringFormatter');
					if ($phone = $formatter->get($data['mobileReceiver'],'dial','-')) {
						$db->query("UPDATE ".GD_TODAYSHOP_ORDER_COUPON." SET cp_publish = 1 WHERE cp_sno = '$cp_sno'");	// �߱� ó��
						ctlStep($ordno,4,1);
					}
				}
			}
		}

	    //������ ���� �����ͺ��̽��� ��� ���������� ���� �����ÿ��� "OK"�� �̴Ͻý���
	    //�����ϼž��մϴ�. �Ʒ� ���ǿ� �����ͺ��̽� ������ �޴� FLAG ������ ��������
	    //(����) OK�� �������� �����ø� �̴Ͻý� ���� ������ "OK"�� �����Ҷ����� ��� �������� �õ��մϴ�
	    //��Ÿ �ٸ� ������ PRINT( echo )�� ���� �����ñ� �ٶ��ϴ�
		echo "OK";			// ����� ������������
	}
}
?>