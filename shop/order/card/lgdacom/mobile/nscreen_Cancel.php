<?php
	include dirname(__FILE__)."/../../../../conf/config.php";
	include dirname(__FILE__)."/../../../../conf/config.mobileShop.php";

	$page_type = $_GET['page_type'];

	if($page_type=='mobile') {
		$order_page = $cfgMobileShop['mobileShopRootDir'].'/ord/order.php';
	}
	else {
		$order_page = $cfg['rootDir'].'/order/order.php';
	}

	if($_GET['isAsync']=='Y'){
		$orderUrl=$order_page;
		echo "<script>alert('����ڰ� ISP(����/BC) ī������� �ߴ��Ͽ����ϴ�.');location.href='$orderUrl'</script>";
		exit;
	}
	/*
	 * [������� ��û ������]
	 *
	 * LG���������� ���� �������� �ŷ���ȣ(LGD_TID)�� ������ ��� ��û�� �մϴ�.(�Ķ���� ���޽� POST�� ����ϼ���)
	 * (���ν� LG���������� ���� �������� PAYKEY�� ȥ������ ������.)
	 */
	$CST_PLATFORM				= $data['service'];								//LG������ ���� ���� ����(test:�׽�Ʈ, service:����)
	$CST_MID					= $data['mid'];									//�������̵�(LG���������� ���� �߱޹����� �������̵� �Է��ϼ���)
	$LGD_MID					= (("test" == $CST_PLATFORM)?"t":"").$CST_MID;	//�������̵�(�ڵ�����)
	$LGD_TID					= $data['tid'];									//LG���������� ���� �������� �ŷ���ȣ(LGD_TID)

 	$configPath					=$_SERVER['DOCUMENT_ROOT'].$cfg['rootDir']."/conf/lgdacom";				//LG�����޿��� ������ ȯ������("/conf/lgdacom.conf") ��ġ ����.

	require_once(dirname(__FILE__)."/nscreen_XPayClient.php");
	$xpay = &new XPayClient($configPath, $CST_PLATFORM);
	$xpay->Init_TX($LGD_MID);

	$xpay->Set("LGD_TXNAME", "Cancel");
	$xpay->Set("LGD_TID", $LGD_TID);

	/*
	 * 1. ������� ��û ���ó��
	 *
	 * ��Ұ�� ���� �Ķ���ʹ� �����޴����� �����Ͻñ� �ٶ��ϴ�.
	 */
	$xpay->TX();

	if( "0000" == $xpay->Response_Code() ){
		//1)������Ұ�� ȭ��ó��(����,���� ��� ó���� �Ͻñ� �ٶ��ϴ�.)
		$settlelog = 'LGU+ SmartXPay ī�� ��� ���'."\n";
		$settlelog .= '����ڵ� : '.$xpay->Response_Code()."\n";
		$settlelog .= '������� : '.$xpay->Response_Msg()."\n";
		$cardCancelResult	= true;
	}else {
		//2)API ��û ���� ȭ��ó��
		$settlelog = $data['oid'].' ('.date('Y:m:d H:i:s').')'."\n";
		$settlelog .= '-----------------------------------'."\n";
		$settlelog .= 'LGU+ SmartXPay ī�� ��� ����'."\n";
		$settlelog .= '����ڵ� : '.$xpay->Response_Code()."\n";
		$settlelog .= '������� : '.$xpay->Response_Msg()."\n";
		$settlelog .= '-----------------------------------'."\n";
		$cardCancelResult	= false;
	}
?>
