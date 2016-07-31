<?php
if (isset($_GET['crno']) === false)
{
	include dirname(__FILE__).'/../../../lib/library.php';
}
include dirname(__FILE__).'/../../../conf/config.php';
include dirname(__FILE__).'/../../../conf/config.pay.php';
include dirname(__FILE__).'/../../../conf/pg.lgdacom.php';

if (class_exists('validation') && method_exists('validation', 'xssCleanArray')) {
	$_POST = validation::xssCleanArray($_POST, array(
		validation::DEFAULT_KEY	=> 'text'
	));
}

$configPath						= $_SERVER['DOCUMENT_ROOT'].$cfg['rootDir']."/conf/lgdacom";		//LG�����޿��� ������ ȯ������("/conf/lgdacom.conf") ��ġ ����.

if(!$pg['serviceType']) $pg['serviceType'] = "service";
$CST_PLATFORM			  		= $pg['serviceType'];							//LG������ ���� ���� ����(test:�׽�Ʈ, service:����)
$CST_MID						= $pg['id'];									//�������̵�(LG���������� ���� �߱޹����� �������̵� �Է��ϼ���)
$LGD_MID						= (("test" == $CST_PLATFORM)?"t":"").$CST_MID;	//�������̵�(�ڵ�����),�׽�Ʈ ���̵�� 't'�� �ݵ�� �����ϰ� �Է��ϼ���.
$LGD_CUSTOM_MERTNAME			= $cfg['compName'];								//������
$LGD_CUSTOM_CEONAME 			= $cfg['ceoName'];								//���� ��ǥ�ڸ�
$LGD_CUSTOM_BUSINESSNUM 		= str_replace("-","",$cfg['compSerial']);		//����ڵ�Ϲ�ȣ
$LGD_CUSTOM_MERTPHONE 			= $cfg['compPhone'];							//���� ��ȭ��ȣ

if ($_POST['method'] == 'auth' && isset($_GET['crno']) === false)
{
	$ordno						= $_POST['ordno'];
	$method						= 'auth';

	$data = $db -> fetch("select * from gd_order where ordno='".$ordno."' limit 1");

	// �߱޻���üũ(�����ý��۰��)
	if ($data['cashreceipt'] != '' && file_exists(dirname(__FILE__).'/../../../lib/cashreceipt.class.php') === false) {
		msg('���ݿ����� �����û����!! \\n['.$ordno.'] �ֹ��� �̹� ����Ǿ����ϴ�.');
		exit;
	}

	### ���ݿ�������û���� �߰�
	@include dirname(__FILE__).'/../../../lib/cashreceipt.class.php';
	$cashreceipt = new cashreceipt();
	$multitax = $cashreceipt->getCashReceiptCalCulate($ordno);
	if (class_exists('cashreceipt'))
	{
		// �߱޻���üũ
		list($crno) = $db->fetch("select crno from gd_cashreceipt where ordno='{$ordno}' and status='ACK' order by crno desc limit 1");
		if ($crno != '') {
			msg('���ݿ����� �����û����!! \\n['.$ordno.'] �ֹ��� �̹� ����Ǿ����ϴ�.');
			exit;
		}

		## ��ǰ��
		list($icnt) = $db->fetch("select count(*) from gd_order_item where istep < 40 and ordno='{$ordno}'");
		list($goodsnm) = $db->fetch("select goodsnm from gd_order_item where istep < 40 and ordno='{$ordno}' order by sno");

		$cutLen = 30;
		if ($icnt > 1){
			$cntStr = ' �� '.($icnt-1).'��';
			$cutLen -= strlen($cntStr) + 2;
		}
		$goodsnm = strcut($goodsnm,$cutLen) . $cntStr;

		$indata = array();
		$indata['ordno'] = $_POST['ordno'];
		$indata['goodsnm'] = $goodsnm;
		$indata['buyername'] = $data['nameOrder'];
		$indata['useopt'] = ($_POST['usertype'] == '1' ? '0' : '1');
		$indata['certno'] = $_POST['ssn'];
		$indata['amount'] = $multitax['caseReceiptAmount'];
		$indata['supply'] = $multitax['supply'];
		$indata['taxfree'] = $multitax['taxfree'];
		$indata['surtax'] = $multitax['vat'];

		$crno = $cashreceipt->putReceipt($indata);
	}

	//$LGD_TID					= $HTTP_POST_VARS["LGD_TID"];			 		//LG���������� ���� �������� �ŷ���ȣ(LGD_TID)
	$LGD_METHOD   				= "AUTH";										//�޼ҵ�('AUTH':����, 'CANCEL' ���)
	$LGD_OID					= $_POST['ordno'];								//�ֹ���ȣ(�������� ����ũ�� �ֹ���ȣ�� �Է��ϼ���)
	$LGD_PAYTYPE				= "SC0100";										//�������� �ڵ� (SC0030:������ü, SC0040:�������, SC0100:�������Ա� �ܵ�)
	$LGD_AMOUNT	 				= $multitax['caseReceiptAmount'];				//�ݾ�("," �� ������ �ݾ��� �Է��ϼ���)
	$LGD_TAXFREEAMOUNT			= $multitax['taxfree'];							//�鼼�ݾ�
	$LGD_CASHCARDNUM			= $_POST['ssn'];								//�߱޹�ȣ(�ֹε�Ϲ�ȣ,���ݿ�����ī���ȣ,�޴�����ȣ ���)
	$LGD_CASHRECEIPTUSE	 		= $_POST['usertype'];							//���ݿ������߱޿뵵('1':�ҵ����, '2':��������)
	$LGD_PRODUCTINFO			= $goodsnm;										//��ǰ��
}
else if ($crdata['method'] == 'auth')
{
	//$LGD_TID					= $_POST["LGD_TID"];			 				//LG���������� ���� �������� �ŷ���ȣ(LGD_TID)
	$LGD_METHOD   				= "AUTH";										//�޼ҵ�('AUTH':����, 'CANCEL' ���)
	$LGD_OID					= $crdata['ordno'];								//�ֹ���ȣ(�������� ����ũ�� �ֹ���ȣ�� �Է��ϼ���)
	$LGD_PAYTYPE				= "SC0100";										//�������� �ڵ� (SC0030:������ü, SC0040:�������, SC0100:�������Ա� �ܵ�)
	$LGD_AMOUNT					= $crdata['amount'];							//�ݾ�("," �� ������ �ݾ��� �Է��ϼ���)
	$LGD_TAXFREEAMOUNT			= $crdata['taxfree'];							//�鼼�ݾ�
	$LGD_CASHCARDNUM			= $crdata['certno'];		   					//�߱޹�ȣ(�ֹε�Ϲ�ȣ,���ݿ�����ī���ȣ,�޴�����ȣ ���)
	$LGD_CASHRECEIPTUSE			= ($crdata['useopt'] == '0' ? '1' : '2');		//���ݿ������߱޿뵵('1':�ҵ����, '2':��������)
	$LGD_PRODUCTINFO			= $crdata['goodsnm'];							//��ǰ��
	$ordno						= $crdata['ordno'];
	$method						= 'auth';
	$crno						= $_GET['crno'];
}
else if ($crdata['method'] == 'cancel')
{
	$LGD_TID					= $crdata['tid'];				 				//LG���������� ���� �������� �ŷ���ȣ(LGD_TID)
	$LGD_METHOD   				= "CANCEL";										//�޼ҵ�('AUTH':����, 'CANCEL' ���)
	$LGD_OID					= $crdata['ordno'];								//�ֹ���ȣ(�������� ����ũ�� �ֹ���ȣ�� �Է��ϼ���)
	$LGD_PAYTYPE				= "SC0100";										//�������� �ڵ� (SC0030:������ü, SC0040:�������, SC0100:�������Ա� �ܵ�)
	$ordno						= $crdata['ordno'];
	$method						= 'cancel';
}

	require_once(dirname(__FILE__)."/XPayClient.php");
	$xpay = &new XPayClient($configPath, $CST_PLATFORM);
	$xpay->Init_TX($LGD_MID);
	$xpay->Set("LGD_TXNAME", "CashReceipt");
	$xpay->Set("LGD_METHOD", $LGD_METHOD);
	$xpay->Set("LGD_PAYTYPE", $LGD_PAYTYPE);

	if ($LGD_METHOD = "AUTH"){					// ���ݿ����� �߱� ��û
		$xpay->Set("LGD_OID", $LGD_OID);
		$xpay->Set("LGD_AMOUNT", $LGD_AMOUNT);
		$xpay->Set("LGD_CASHCARDNUM", $LGD_CASHCARDNUM);
		$xpay->Set("LGD_CUSTOM_MERTNAME", $LGD_CUSTOM_MERTNAME);
		$xpay->Set("LGD_CUSTOM_CEONAME", $LGD_CUSTOM_CEONAME);
		$xpay->Set("LGD_CUSTOM_BUSINESSNUM", $LGD_CUSTOM_BUSINESSNUM);
		$xpay->Set("LGD_CUSTOM_MERTPHONE", $LGD_CUSTOM_MERTPHONE);
		$xpay->Set("LGD_CASHRECEIPTUSE", $LGD_CASHRECEIPTUSE);
		$xpay->Set("LGD_SEQNO", "001");
		$xpay->Set("LGD_TAXFREEAMOUNT", $LGD_TAXFREEAMOUNT);

		if ($LGD_PAYTYPE = "SC0100"){			//�������Ա� �ܵ��� �߱޿�û
			$xpay->Set("LGD_PRODUCTINFO", $LGD_PRODUCTINFO);
		}else{									// ������� ������ü,������� ���ݿ����� �߱޿�û
			$xpay->Set("LGD_TID", $LGD_TID);
		}
	}else {										// ���ݿ����� ��� ��û
		$xpay->Set("LGD_TID", $LGD_TID);
		$xpay->Set("LGD_SEQNO", "001");
	}

	/*
	 * 1. ���ݿ����� �߱�/��� ��û ���ó��
	 *
	 * ��� ���� �Ķ���ʹ� �����޴����� �����Ͻñ� �ٶ��ϴ�.
	 */
	$xpay->TX();

	if($method == 'auth')
	{
		if( "0000" == $xpay->Response_Code() )
		{
			$settlelog = $ordno.' ('.date('Y:m:d H:i:s').')'."\n";
			$settlelog .= '-----------------------------------'."\n";
			$settlelog .= '������ ���ݿ����� �߱޿� ���� ���'."\n";
			$settlelog .= '����ڵ� : '.$xpay->Response("LGD_RESPCODE",0)."\n";
			$settlelog .= '������� : '.$xpay->Response("LGD_RESPMSG",0)."\n";
			$settlelog .= '�ֹ���ȣ : '.$xpay->Response("LGD_OID",0)."\n";
			$settlelog .= '�ŷ���ȣ : '.$xpay->Response("LGD_TID",0)."\n";
			$settlelog .= '-----------------------------------'."\n";

			if (empty($crno) === true)
			{
				$db->query("update gd_order set cashreceipt='".$xpay->Response("LGD_CASHRECEIPTNUM",0)."',settlelog=concat(if(settlelog is null,'',settlelog),'\n{$settlelog}') where ordno='{$ordno}'");
			}
			else {
				# ���ݿ�������û���� ����
				$db->query("update gd_cashreceipt set pg='lgdacom',tid='".$xpay->Response("LGD_TID",0)."',cashreceipt='".$xpay->Response("LGD_CASHRECEIPTNUM",0)."',receiptnumber='".$xpay->Response("LGD_CASHRECEIPTNUM",0)."',moddt=now(),status='ACK',errmsg='',receiptlog=concat(if(receiptlog is null,'',receiptlog),'\n{$settlelog}') where crno='{$crno}'");
				$db->query("update gd_order set cashreceipt='".$xpay->Response("LGD_CASHRECEIPTNUM",0)."' where ordno='{$ordno}'");
			}

			if (isset($_GET['crno']) === false)
			{
				msg('���ݿ������� ����߱޵Ǿ����ϴ�');
				echo '<script>parent.location.reload();</script>';
			}
		}else{
			$settlelog = $ordno.' ('.date('Y:m:d H:i:s').')'."\n";
			$settlelog .= '-----------------------------------'."\n";
			$settlelog .= '������ ���ݿ����� �߱� ����'."\n";
			$settlelog .= '����ڵ� : '.$xpay->Response_Code()."\n";
			$settlelog .= '������� : '.$xpay->Response_Msg()."\n";
			$settlelog .= '-----------------------------------'."\n";

			if (empty($crno) === true)
			{
				$db->query("update gd_order set settlelog=concat(if(settlelog is null,'',settlelog),'\n{$settlelog}') where ordno='{$ordno}'");
			}
			else {
				# ���ݿ�������û���� ����
				$db->query("update gd_cashreceipt set pg='lgdacom',errmsg='".$xpay->Response("LGD_RESPCODE",0).":".$xpay->Response("LGD_RESPMSG",0)."',moddt=now(),receiptlog=concat(if(receiptlog is null,'',receiptlog),'\n{$settlelog}') where crno='{$crno}'");
			}

			if (isset($_GET['crno']) === false)
			{
				msg($xpay->Response("LGD_RESPMSG",0));
				exit;
			}
		}
	}

	if($method == 'cancel')
	{
		if( "0000" == $xpay->Response_Code() )
		{
			$settlelog = $ordno.' ('.date('Y:m:d H:i:s').')'."\n";
			$settlelog .= '-----------------------------------'."\n";
			$settlelog .= '������ ���ݿ����� ��ҿ� ���� ���'."\n";
			$settlelog .= '����ڵ� : '.$xpay->Response("LGD_RESPCODE",0)."\n";
			$settlelog .= '������� : '.$xpay->Response("LGD_RESPMSG",0)."\n";
			$settlelog .= '�ֹ���ȣ : '.$xpay->Response("LGD_OID",0)."\n";
			$settlelog .= '�ŷ���ȣ : '.$xpay->Response("LGD_TID",0)."\n";
			$settlelog .= '-----------------------------------'."\n";

			$db->query("update gd_cashreceipt set moddt=now(),status='CCR',errmsg='',receiptlog=concat(if(receiptlog is null,'',receiptlog),'{$settlelog}') where crno='{$_GET['crno']}'");
		}
		else {
			$settlelog = $ordno.' ('.date('Y:m:d H:i:s').')'."\n";
			$settlelog .= '-----------------------------------'."\n";
			$settlelog .= '������ ���ݿ����� ��� ����'."\n";
			$settlelog .= '����ڵ� : '.$xpay->Response_Code()."\n";
			$settlelog .= '������� : '.$xpay->Response_Msg()."\n";
			$settlelog .= '-----------------------------------'."\n";

			$db->query("update gd_cashreceipt set errmsg='".$xpay->Response("LGD_RESPCODE",0).":".$xpay->Response("LGD_RESPMSG",0)."',moddt=now(),receiptlog=concat(if(receiptlog is null,'',receiptlog),'\n{$settlelog}') where crno='{$_GET['crno']}'");
		}
	}
?>
