<?php
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

 	$configPath					= $data['shopdir']."/conf/lgdacom_today";				//LG�����޿��� ������ ȯ������("/conf/lgdacom.conf") ��ġ ����.

	require_once(dirname(__FILE__)."/XPayClient.php");
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
		$settlelog = '������ XPay ī�� ��� ���'."\n";
		$settlelog .= '����ڵ� : '.$xpay->Response_Code()."\n";
		$settlelog .= '������� : '.$xpay->Response_Msg()."\n";
		$cardCancelResult	= true;
	}else {
		//2)API ��û ���� ȭ��ó��
		$settlelog = $data['oid'].' ('.date('Y:m:d H:i:s').')'."\n";
		$settlelog .= '-----------------------------------'."\n";
		$settlelog .= '������ XPay ī�� ��� ����'."\n";
		$settlelog .= '����ڵ� : '.$xpay->Response_Code()."\n";
		$settlelog .= '������� : '.$xpay->Response_Msg()."\n";
		$settlelog .= '-----------------------------------'."\n";
		$cardCancelResult	= false;
	}
?>
