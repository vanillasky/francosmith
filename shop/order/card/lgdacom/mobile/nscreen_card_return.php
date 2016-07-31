<?php
/**
 * LG ���÷��� PG ��� (nScreen ��, nScreen ������ ��� ������ PC�� ���� ������ �����)
 * ���� ���ϸ� payres.php , note_url.php
 * LG ���÷��� PG ���� : LG U+ ǥ�ذ���â 2.5 - SmartXPay(V1.2 - 20141212)
 * @author artherot @ godosoft development team.
 */

// �⺻ ���� ����
include "../../../../lib/library.php";
include "../../../../conf/config.mobileShop.php";
include "../../../../conf/config.php";
@include "../../../../conf/pg.lgdacom.php";

// ���� ����Ʈ ����
error_reporting(E_ALL ^ E_NOTICE);

// ������ Ÿ��
$page_type		= $_GET['page_type'];

// ������ Ÿ�Կ� ���� ���� ������ ó��
if($page_type == 'mobile') {
	$order_end_page		= $cfgMobileShop['mobileShopRootDir'].'/ord/order_end.php';
	$order_fail_page	= $cfgMobileShop['mobileShopRootDir'].'/ord/order_fail.php';
} else {
	$order_end_page		= $cfg['rootDir'].'/order/order_end.php';
	$order_fail_page	= $cfg['rootDir'].'/order/order_fail.php';
}

// �α� ����
if (function_exists('pg_data_log_write')) {
	$logPath	= '../../../../log/lgdacom/';
	pg_data_log_write($_POST, 'lguplus_nScreen', $logPath);
}

// ����, �񵿱� ��Ŀ� ���� ����
$isAsync		= $_GET['isAsync'];			// �����Ŀ��� :�񵿱�(ISP) , �׿� ����
$isSuccess		= false;					// ������������

// ���̹� ���ϸ��� üũ
$naverNcashClass	= $_SERVER['DOCUMENT_ROOT'].$cfg['rootDir'].'/lib/naverNcash.class.php';
$naverNcashCheck	= false;
if (is_file($naverNcashClass)) {
	$naverNcashCheck	= true;
}

// ���̹� ���ϸ��� Class�� �ִ� ��� API ����
if ($naverNcashCheck === true) {
	include $naverNcashClass;
	$naverNcash	= new naverNcash(true);
	if ($naverNcash->useyn == 'Y') {
		if ($_POST['LGD_PAYTYPE'] == "SC0040") {
			$ncashResult	= $naverNcash->payment_approval($_POST['LGD_OID'], false);
		} else {
			$ncashResult	= $naverNcash->payment_approval($_POST['LGD_OID'], true);
		}
		if ($ncashResult === false) {
			if ($isAsync == 'Y') {	//�񵿱��� ���(ISP����)
				echo "ROLLBACK";	//OK�� �ƴѰ�� ROLLBACKó�� ��. (���� �ڵ��������)
				exit();
			} else {
				msg('���̹� ���ϸ��� ��뿡 �����Ͽ����ϴ�.', $order_fail_page.'?ordno='.$_POST['LGD_OID']);
				exit();
			}
		}
	}
}

// PG���� ������ üũ �� ��ȿ�� üũ
if (function_exists('forge_order_check')) {
	if (forge_order_check($_POST['LGD_OID'], $_POST['LGD_AMOUNT']) === false) {
		if ($isAsync == 'Y') {	//�񵿱��� ���(ISP����)
			echo "ROLLBACK";	//OK�� �ƴѰ�� ROLLBACKó�� ��. (���� �ڵ��������)
			exit();
		} else {
			msg('�ֹ� ������ ���� ������ ���� �ʽ��ϴ�. �ٽ� ���� �ٶ��ϴ�.', $order_fail_page.'?ordno='.$_POST['LGD_OID']);
			exit();
		}
	}
}

/*
 *************************************************
 * 1. �񵿱� ����� �����ΰ�� (ISP����)
 *************************************************
 */
if ($isAsync == 'Y') {
	// LG ���÷������� ���� ������� POST�� ���� Ű�̸��� ������ ������ ó���� ��
	extract($_POST);

	// ����Ű
	$LGD_MERTKEY	= $pg['mertkey'];

	// MD5 �ؽ���ȣȭ
	$LGD_HASHDATA2	= md5($LGD_MID.$LGD_OID.$LGD_AMOUNT.$LGD_RESPCODE.$LGD_TIMESTAMP.$LGD_MERTKEY);

	/*
	 * ���� ó����� ���ϸ޼���
	 *
	 * OK   : ���� ó����� ����
	 * �׿� : ���� ó����� ����
	 *
	 * �� ���ǻ��� : ������ 'OK' �����̿��� �ٸ����ڿ��� ���ԵǸ� ����ó�� �ǿ��� �����Ͻñ� �ٶ��ϴ�.
	 */
	$resultMSG = "������� ���� DBó��(NOTE_URL) ������� �Է��� �ֽñ� �ٶ��ϴ�.";

	//�ؽ��� ������ �����ϸ�
	if ($LGD_HASHDATA2 == $LGD_HASHDATA) {
		//������ �����̸�
		if($LGD_RESPCODE == '0000'){
			$isSuccess	= true;
			$resultMSG	= 'OK';
		}
		//������ �����̸�
		else {
			$resultMSG	= $LGD_RESPMSG;
		}
	}
	//�ؽ��� ������ �����̸�
	else {
		$resultMSG		= "������� ���� DBó��(NOTE_URL) �ؽ��� ������ �����Ͽ����ϴ�.";
	}
}

/*
 *************************************************
 * 2. ���� ����� �����ΰ��
 *************************************************
 */
else {
	// �⺻�� ����
	$configPath			= $_SERVER['DOCUMENT_ROOT'].$cfg['rootDir'].'/conf/lgdacom';		// LG���÷������� ������ ȯ������("/conf/lgdacom.conf") ��ġ ����.

	// LG���÷��� ���̵� ó��
	if (empty($pg['serviceType'])) {
		$pg['serviceType']	= 'service';
	}
	if ($pg['serviceType'] == 'test') {
		$LGD_MID	= 't'.$pg['id'];
	} else {
		$LGD_MID	= $pg['id'];
	}

	$CST_PLATFORM		= $pg['serviceType'];
    $CST_MID			= $pg['id'];
    $LGD_PAYKEY			= $_POST['LGD_PAYKEY'];

    require_once("./nscreen_XPayClient.php");
    $xpay = &new XPayClient($configPath, $CST_PLATFORM);
    $xpay->Init_TX($LGD_MID);

    $xpay->Set('LGD_TXNAME', 'PaymentByKey');
    $xpay->Set('LGD_PAYKEY', $LGD_PAYKEY);

    // �ݾ��� üũ�Ͻñ� ���ϴ� ��� �Ʒ� �ּ��� Ǯ� �̿��Ͻʽÿ�.
	// $DB_AMOUNT = 'DB�� ���ǿ��� ������ �ݾ�'; //�ݵ�� �������� �Ұ����� ��(DB�� ����)���� �ݾ��� �������ʽÿ�.
	// $xpay->Set('LGD_AMOUNTCHECKYN', 'Y');
	// $xpay->Set('LGD_AMOUNT', $DB_AMOUNT);

	// �ֹ���ȣ
	$LGD_OID			= $_POST['LGD_OID'];

	// ������ ���� ó�� API ����
	if ($xpay->TX()) {
		// �����
		$Response_Code		= $xpay->Response_Code();
		$Response_Msg		= $xpay->Response_Msg();

		// LG ���÷������� ���� ������� ���� Ű�̸��� ������ ������ ó���� ��
		$tmp				= array();
		$tmp_ArrLGResult	= $xpay->Response_Names();
		foreach($tmp_ArrLGResult as $name) {
			$tmp[$name]		= $xpay->Response($name, 0);
		}
		extract($tmp);
		unset($tmp);
		unset($tmp_ArrLGResult);

		if( $xpay->Response_Code() == '0000') {
			$isSuccess		= true;
		} else {
			$resultMSG		= '��������';
		}
    } else {
		// API ��û���� ȭ��ó��
		$resultMSG			= '��������';
		$resultMSG			.= '������û�� �����Ͽ����ϴ�.  <br>';
		$resultMSG			.= 'TX Response_code = ' . $xpay->Response_Code() . '<br>';
		$resultMSG			.= 'TX Response_msg = ' . $xpay->Response_Msg() . '<p>';
    }
}

/*
 *************************************************
 * 3. ���� �α� ó��
 *************************************************
 */
$ordno		= $LGD_OID;					// �ֹ���ȣ

// ���� ����
if($LGD_PAYTYPE=='SC0010') $payTypeStr = "�ſ�ī��";
if($LGD_PAYTYPE=='SC0030') $payTypeStr = "������ü";
if($LGD_PAYTYPE=='SC0040') $payTypeStr = "�������";
if($LGD_PAYTYPE=='SC0060') $payTypeStr = "�ڵ���";

// ���� �α� ó��
$tmp_log	= array();
$tmp_log[]	= "LG U+ SmartXPay (ǥ�ذ���â 2.5) ������û�� ���� ���(nScreen)";
if($Response_Code)	$tmp_log[]	= "TX Response_code : ".$Response_Code;
if($Response_Msg)	$tmp_log[]	= "TX Response_msg : ".$Response_Msg;
$tmp_log[]	= "����ڵ� : ".$LGD_RESPCODE." (0000(����) �׿� ����)";
$tmp_log[]	= "������� : ".$LGD_RESPMSG."\n".$resultMSG;
$tmp_log[]	= "�ؽ�����Ÿ : ".$LGD_HASHDATA;
$tmp_log[]	= "�����ݾ� : ".$LGD_AMOUNT;
$tmp_log[]	= "�������̵� : ".$LGD_MID;
$tmp_log[]	= "�ŷ���ȣ : ".$LGD_TID;
$tmp_log[]	= "�ֹ���ȣ : ".$LGD_OID;
$tmp_log[]	= "������� : ".$payTypeStr;
$tmp_log[]	= "�����Ͻ� : ".$LGD_PAYDATE;
$tmp_log[]	= "�ŷ���ȣ : ".$LGD_TID;
$tmp_log[]	= "����ũ�� ���� ���� : ".$LGD_ESCROWYN;
$tmp_log[]	= "��������ڵ� : ".$LGD_FINANCECODE;
$tmp_log[]	= "��������� : ".$LGD_FINANCENAME;

switch ($LGD_PAYTYPE){
	case "SC0010":	// �ſ�ī��
		$tmp_log[]	= "����������ι�ȣ : ".$LGD_FINANCEAUTHNUM;
		$tmp_log[]	= "�ſ�ī���ȣ : ".$LGD_CARDNUM." (�Ϲ� �������� *ó����)";
		$tmp_log[]	= "�ſ�ī���Һΰ��� : ".$LGD_CARDINSTALLMONTH;
		$tmp_log[]	= "�ſ�ī�幫���ڿ��� : ".$LGD_CARDNOINTYN." (1:������, 0:�Ϲ�)";
		break;
	case "SC0030":	// ������ü
		if($LGD_CASHRECEIPTNUM)		$tmp_log[]	= "���ݿ��������ι�ȣ : ".$LGD_CASHRECEIPTNUM;
		if($LGD_CASHRECEIPTSELFYN)	$tmp_log[]	= "���ݿ����������߱������� : ".$LGD_CASHRECEIPTSELFYN." (Y: �����߱�)";
		if($LGD_CASHRECEIPTKIND)	$tmp_log[]	= "���ݿ��������� : ".$LGD_CASHRECEIPTKIND." (0:�ҵ����, 1:��������)";
		if($LGD_ACCOUNTOWNER)		$tmp_log[]	= "���¼������̸� : ".$LGD_ACCOUNTOWNER;
		break;
	case "SC0040":	// �������
		if($LGD_CASHRECEIPTNUM)		$tmp_log[]	= "���ݿ��������ι�ȣ : ".$LGD_CASHRECEIPTNUM;
		if($LGD_CASHRECEIPTSELFYN)	$tmp_log[]	= "���ݿ����������߱������� : ".$LGD_CASHRECEIPTSELFYN." (Y: �����߱�)";
		if($LGD_CASHRECEIPTKIND)	$tmp_log[]	= "���ݿ��������� : ".$LGD_CASHRECEIPTKIND." (0:�ҵ����, 1:��������)";
		if($LGD_ACCOUNTNUM)			$tmp_log[]	= "������¹߱޹�ȣ : ".$LGD_ACCOUNTNUM;
		if($LGD_PAYER)				$tmp_log[]	= "��������Ա��ڸ� : ".$LGD_PAYER;
		if($LGD_CASTAMOUNT)			$tmp_log[]	= "�Աݴ����ݾ� : ".$LGD_CASTAMOUNT;
		if($LGD_CASCAMOUNT)			$tmp_log[]	= "���Աݱݾ� : ".$LGD_CASCAMOUNT;
		if($LGD_CASFLAG)			$tmp_log[]	= "�ŷ����� : ".$LGD_CASFLAG." (R:�Ҵ�,I:�Ա�,C:���)";
		if($LGD_CASSEQNO)			$tmp_log[]	= "��������Ϸù�ȣ : ".$LGD_CASSEQNO;
		break;
	case "SC0060":	// �ڵ���
		break;
}

// ���� ���� �α� ����
$settlelog	= "{$ordno} (" . date('Y:m:d H:i:s') . ")\n----------------------------------------------------\n" . implode( "\n", $tmp_log ) . "\n----------------------------------------------------\n";
unset($tmp_log);

/*
 *************************************************
 * 4. DB ó��
 *************************************************
 */
// �ֹ� ����
$oData = $db->fetch("SELECT step, vAccount FROM ".GD_ORDER." WHERE ordno='".$ordno."'");

// �ߺ� ���� üũ
if($oData['step'] > 0 || $oData['vAccount'] != '' || !strcmp($LGD_RESPCODE,'S007')){
	// �α� ����
	$db->query("UPDATE ".GD_ORDER." SET settlelog=CONCAT(IFNULL(settlelog,''),'".$settlelog."') WHERE ordno='".$ordno."'");

	// DB ó���� �̵��� ������
	$goUrl	= $order_fail_page.'?ordno='.$ordno;
}

// ��������
else if($isSuccess === true) {
	// �ֹ� ������ ����
	$query	= "SELECT * FROM ".GD_ORDER." a LEFT JOIN ".GD_LIST_BANK." b ON a.bankAccount = b.sno WHERE a.ordno='".$ordno."'";
	$data	= $db->fetch($query);

	// ����ũ�� ���� Ȯ��
	if($LGD_ESCROWYN == 'Y'){
		$escrowyn	= 'y';
		$escrowno	= $LGD_TID;
	}else{
		$escrowyn	= 'n';
		$escrowno	= '';
	}

	// ���� ���� ����
	$step	= 1;
	$qrc1	= "cyn='y', cdt=now(), cardtno='".$LGD_TID."',";
	$qrc2	= "cyn='y',";

	// ������� ������ �������� ����
	if ($LGD_PAYTYPE == 'SC0040'){
		$vAccount	= $LGD_FINANCENAME.' '.$LGD_ACCOUNTNUM.' '.$LGD_PAYER;
		$step		= 0;
		$qrc1		= '';
		$qrc2		= '';
	}

	// ���ݿ����� ���� ����
	if ($LGD_CASHRECEIPTNUM){
		$qrc1 .= "cashreceipt='".$LGD_CASHRECEIPTNUM."',";
	}

	// �ֹ� ������ ������Ʈ
	$db->query("
	UPDATE ".GD_ORDER." SET ".$qrc1."
		step		= '".$step."',
		step2		= '',
		escrowyn	= '".$escrowyn."',
		escrowno	= '".$escrowno."',
		vAccount	= '".$vAccount."',
		settlelog	= CONCAT(IFNULL(settlelog,''),'".$settlelog."')
	WHERE ordno='".$ordno."'"
	);

	// �ֹ� ��ǰ ������ ������Ʈ
	$db->query("UPDATE ".GD_ORDER_ITEM." SET ".$qrc2." istep='".$step."' WHERE ordno='".$ordno."'");

	// �ֹ��α� ����
	orderLog($ordno,$r_step2[$data[step2]]." > ".$r_step[$step]);

	// ��� ó��
	setStock($ordno);

	// ��ǰ���Խ� ������ ���
	if ($data['m_no'] && $data['emoney']){
		setEmoney($data['m_no'],-$data['emoney'],"��ǰ���Խ� ������ ���� ���",$ordno);
	}

	// �ֹ�Ȯ�θ���
	if(function_exists('getMailOrderData')){
		sendMailCase($data['email'],0,getMailOrderData($ordno));
	}

	// SMS ���� ����
	$dataSms	= $data;

	// ��Ȳ�� SMS / Email ����
	if ($LGD_PAYTYPE != 'SC0040'){
		sendMailCase($data['email'],1,$data);		// �Ա�Ȯ�� ����
		sendSmsCase('incash',$data['mobileOrder']);	// �Ա�Ȯ�� SMS
	} else {
		sendSmsCase('order',$data['mobileOrder']);	// �ֹ�Ȯ�� SMS
	}

	// DB ó���� �̵��� ������
	$goUrl	= $order_end_page.'?ordno='.$ordno.'&card_nm='.$LGD_FINANCENAME;
}

// ��������
else {
	// ���̹� ���ϸ��� Class�� �ִ� ��� API ����
	if ($naverNcashCheck === true) {
		// ���̹� ���ϸ��� ���� ���� ��� API ȣ��
		if ($naverNcash->useyn == 'Y') $naverNcash->payment_approval_cancel($ordno);
	}

	// ���п� ���� �ֹ� ����Ÿ�� �α� ������Ʈ
	$db->query("UPDATE ".GD_ORDER." SET step2=54, settlelog=CONCAT(IFNULL(settlelog,''),'".$settlelog."') WHERE ordno='".$ordno."' AND step2=50");
	$db->query("UPDATE ".GD_ORDER_ITEM." SET istep=54 WHERE ordno='".$ordno."' AND istep=50");

	// DB ó���� �̵��� ������
	$goUrl	= $order_fail_page.'?ordno='.$ordno;
}

//�񵿱��� ���(ISP����)
if($isAsync == 'Y'){
	if ( $isSuccess === true && $resultMSG == "OK" ) {
		echo $resultMSG;
	} else {
		// OK�� �ƴѰ�� ROLLBACKó�� ��. �ַ��α״� ������ �����ڿ��� ����������ȸ>��ü�ŷ�������ȸ>���۽��г�����ȸ  ���� Ȯ�� ����
		// LGD_CUSTOM_ROLLBACK �� C�� ó���� �߱� ������ ROLLBACK �̶�� ó���� �ؾ߸� ���� �ش� ������ ROLLBACK ó����
		echo 'ROLLBACK';
	}
}

// ���� ��� ������ ��� ������ �̵�
else {
	//go($goUrl,'parent');
	go($goUrl);
}
?>