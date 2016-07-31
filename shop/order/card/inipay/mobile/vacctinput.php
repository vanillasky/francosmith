<?php
/**
 * �̴Ͻý� PG ������� �Ա� ó�� ������
 * ���� ���ϸ� mx_rnoti.php
 * �̴Ͻý� PG ���� : INIpayMobile Web (V 2.4 - 20110725)
 * WEB ����� ��� �̹� P_NEXT_URL ���� ä�� ����� ���� �Ͽ����Ƿ�, �̴Ͻý����� �����ϴ� ������� ä�� ��� ������ ���� �Ͻñ� �ٶ��ϴ�.
 */

//--- �⺻ ����
include "../../../../lib/library.php";
include "../../../../conf/config.php";

function cancel_inipay($ordno,$cardtno,$settlelog,$claimReason) {
	include SHOPROOT."/conf/pg.inipay.php";
	global $db;
	require_once(SHOPROOT.'/order/card/inipay/libs/INILib.php');

	//--- INIpay50 Ŭ������ �ν��Ͻ� ����
	$inipay	= new INIpay50;

	//--- ��� ���� ����
	$inipay->SetField('inipayhome',	SHOPROOT.'/order/card/inipay');	// �̴����� Ȩ���͸�
	$inipay->SetField('type', 'cancel');											// ���� (���� ���� �Ұ�)
	$inipay->SetField('debug', 'true');												// �α׸��('true'�� �����ϸ� �󼼷αװ� ������.)
	$inipay->SetField('mid', $pg['id']);										// �������̵�
	$inipay->SetField('admin', '1111');												// ���Ī ���Ű Ű�н�����
	$inipay->SetField('tid', $cardtno);										// ����� �ŷ��� �ŷ����̵�
	$inipay->SetField('cancelmsg', $claimReason);											// ��һ���
	$inipay->startAction();

	$query = "update ".GD_ORDER." set settlelog	= concat(IFNULL(settlelog,''),'$settlelog') where ordno=".$ordno;
	$db->query($query);
}

@extract($_POST);

// KB ����ϰ����� P_TYPE �� ISP�� �ƴ� CARD�� ���޵Ǿ� ����ó�� (�Ʒ� ó���ϴ� ��İ� ����)
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

//--- INIpay ���
$INIpayHome = realpath(dirname(__FILE__).'/../');      // �̴����� Ȩ���͸�

//--- PG IP
$PGIP = $_SERVER['REMOTE_ADDR'];

//--- PG���� ���´��� IP�� üũ
//if($PGIP == "211.219.96.165" || $PGIP == "118.129.210.25")
{
	// �α� ���� (�̴Ͻý� �α׷� ���Ϸ� ���� �̴Ͻý��� ��� ���� ����)
	$logfile		= fopen( $INIpayHome . '/log/INI_mx_rnoti_'.date('Ymd').'.log', 'a+' );
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


	// �̴Ͻý� NOTI �������� ���� Value
	$P_TID;				// �ŷ���ȣ
	$P_MID;				// �������̵�
	$P_AUTH_DT;			// ��������
	$P_STATUS;			// �ŷ����� (00:����, 01:����)
	$P_TYPE;			// ���Ҽ���
	$P_OID;				// �����ֹ���ȣ
	$P_FN_CD1;			// �������ڵ�1
	$P_FN_CD2;			// �������ڵ�2
	$P_FN_NM;			// ������� (�����, ī����, ������)
	$P_AMT;				// �ŷ��ݾ�
	$P_UNAME;			// ����������
	$P_RMESG1;			// ����ڵ�
	$P_RMESG2;			// ����޽���
	$P_NOTI;			// ��Ƽ�޽���(�������� �ø� �޽���)
	$P_AUTH_NO;			// ���ι�ȣ

	$P_TID			= $_REQUEST[P_TID];
	$P_MID			= $_REQUEST[P_MID];
	$P_AUTH_DT		= $_REQUEST[P_AUTH_DT];
	$P_STATUS		= $_REQUEST[P_STATUS];
	$P_TYPE			= $_REQUEST[P_TYPE];
	$P_OID			= $_REQUEST[P_OID];
	$P_FN_CD1		= $_REQUEST[P_FN_CD1];
	$P_FN_CD2		= $_REQUEST[P_FN_CD2];
	$P_FN_NM		= $_REQUEST[P_FN_NM];
	$P_AMT			= $_REQUEST[P_AMT];
	$P_UNAME		= $_REQUEST[P_UNAME];
	$P_RMESG1		= $_REQUEST[P_RMESG1];
	$P_RMESG2		= $_REQUEST[P_RMESG2];
	$P_NOTI			= $_REQUEST[P_NOTI];
	$P_AUTH_NO		= $_REQUEST[P_AUTH_NO];

	//WEB ����� ��� ������� ä�� ��� ���� ó��
	//(APP ����� ��� �ش� ������ ���� �Ǵ� �ּ� ó�� �Ͻñ� �ٶ��ϴ�.)
	if($P_TYPE == "VBANK")	//���������� ��������̸�
	{
		if($P_STATUS != "02") //�Ա��뺸 "02" �� �ƴϸ�(������� ä�� : 00 �Ǵ� 01 ���)
		{
			echo "OK";
			exit;
		}
	}

	if($P_TYPE == "ISP") //���������� ISP�϶�
	{
		// PG���� ������ üũ �� ��ȿ�� üũ
		if (forge_order_check($P_OID,$P_AMT) === false) {
			$claimReason = $P_RMESG1."->�ڵ� �������(��ǰ�ݾװ� �����ݾ��� ��ġ���� ����.)";
			$settlelog	= '';
			$settlelog	.= '===================================================='.chr(10);
			$settlelog	.= 'PG�� : �̴Ͻý� - INIpay Mobile'.chr(10);
			$settlelog	.= '�ֹ���ȣ : '.$P_OID.chr(10);
			$settlelog	.= '�ŷ���ȣ : '.$P_TID.chr(10);
			$settlelog	.= '����ڵ� : '.$P_STATUS.chr(10);
			$settlelog	.= '������� : '.$claimReason.chr(10);
			$settlelog	.= '���ҹ�� : '.$P_TYPE.chr(10);
			$settlelog	.= '���αݾ� : '.$P_AMT.chr(10);
			$settlelog	.= '�������� : '.$P_AUTH_DT.chr(10);
			$settlelog	.= '���ι�ȣ : '.$P_AUTH_NO.chr(10);
			$settlelog	.= ' --------------------------------------------------'.chr(10);
			cancel_inipay($P_OID,$P_TID,$settlelog,$claimReason);
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

	$ordno = $P_OID;
	if (!$ordno) exit;

	//--- ���� ���
	$pgPayMethod	= array(
			'CARD'			=> '�ſ�ī��',
			'ISP'			=> '�ſ�ī��',
			'BANK'			=> '�ǽð�������ü',
			'MOBILE'		=> '�ڵ���',
			'VBANK'			=> '�������Ա�(�������)',
	);

	//--- �α� ����
	$settlelog	= '';
	$settlelog	.= '===================================================='.chr(10);
	$settlelog	.= 'PG�� : �̴Ͻý� - INIpay Mobile'.chr(10);
	$settlelog	.= '�ֹ���ȣ : '.$ordno.chr(10);
	$settlelog	.= '�ŷ���ȣ : '.$P_TID.chr(10);
	$settlelog	.= '����ڵ� : '.$P_STATUS.chr(10);
	$settlelog	.= '������� : '.$P_RMESG1.' '.$P_RMESG2.chr(10);
	$settlelog	.= '���ҹ�� : '.$P_TYPE.' - '.$pgPayMethod[$P_TYPE].chr(10);
	$settlelog	.= '���αݾ� : '.$P_AMT.chr(10);
	$settlelog	.= '�������� : '.$P_AUTH_DT.chr(10);
	$settlelog	.= '���ι�ȣ : '.$P_AUTH_NO.chr(10);
	$settlelog	.= ' --------------------------------------------------'.chr(10);

	//--- ���ο��� / ���� ����� ���� ó�� ����
	switch ($P_TYPE){
		case "ISP":
			$settlelog	.= 'ī���� : '.$P_FN_NM.chr(10);
			break;

		case 'BANK':
			$settlelog	.= '����� : '.$P_FN_NM.chr(10);
		break;

		case "VBANK":
			$settlelog	.= '�Ա�Ȯ�� : PG���ڵ��Ա�Ȯ��'.chr(10);
			$settlelog	.= '������ : '.$P_UNAME.chr(10);
		break;
	}

	$settlelog	= '===================================================='.chr(10).'�����ڵ�Ȯ�� : ����Ȯ�νð�('.date('Y-m-d H:i:s').')'.chr(10).$settlelog.'===================================================='.chr(10);

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
		include dirname(__FILE__)."/../../../../lib/naverNcash.class.php";
		$naverNcash = new naverNcash();
		$naverNcash->deal_done($ordno);
	}

	/***********************************************************************************
	' ������ ���� �����ͺ��̽��� ��� ���������� ���� �����ÿ��� "OK"�� �̴Ͻý��� ���нô� "FAIL" ��
	' �����ϼž��մϴ�. �Ʒ� ���ǿ� �����ͺ��̽� ������ �޴� FLAG ������ ��������
	' (����) OK�� �������� �����ø� �̴Ͻý� ���� ������ "OK"�� �����Ҷ����� ��� �������� �õ��մϴ�
	' ��Ÿ �ٸ� ������ echo "" �� ���� �����ñ� �ٶ��ϴ�
	'***********************************************************************************/

	// if(�����ͺ��̽� ��� ���� ���� ���Ǻ��� = true)
	echo "OK"; //����� ������ ������
	// else
	//	 echo "FAIL";
}
?>