<?php
    /*
     * [���� �κ���� ��û ������]
     *
     * LG�ڷ������� ���� �������� �ŷ���ȣ(LGD_TID)�� ������ ��� ��û�� �մϴ�.(�Ķ���� ���޽� POST�� ����ϼ���)
     * (���ν� LG�ڷ������� ���� �������� PAYKEY�� ȥ������ ������.)
     */

	$CST_PLATFORM	= $data['service'];								//LG������ ���� ���� ����(test:�׽�Ʈ, service:����)
	$CST_MID					= $data['mid'];									//�������̵�(LG���������� ���� �߱޹����� �������̵� �Է��ϼ���)
	$LGD_MID					= (("test" == $CST_PLATFORM)?"t":"").$CST_MID;	//�������̵�(�ڵ�����)
	$LGD_TID					= $data['tid'];									//LG���������� ���� �������� �ŷ���ȣ(LGD_TID)\
	$LGD_CANCELAMOUNT			= $data['price'];								//����� �ݾ�
	$LGD_CANCELTAXFREEAMOUNT	= $data['taxfree'];								//����� �鼼�ݾ�
	$LGD_CANCELREASON			= "������ ���";

 	$configPath					= $data['shopdir']."/conf/lgdacom";				//LG�����޿��� ������ ȯ������("/conf/lgdacom.conf") ��ġ ����.

	require_once(dirname(__FILE__)."/XPayClient.php");
	$xpay = &new XPayClient($configPath, $CST_PLATFORM);
	$xpay->Init_TX($LGD_MID);
	
	$xpay->Set("LGD_TXNAME", "PartialCancel");
	$xpay->Set("LGD_TID", $LGD_TID);
	$xpay->Set("LGD_CANCELAMOUNT", $LGD_CANCELAMOUNT);
    $xpay->Set("LGD_CANCELTAXFREEAMOUNT", $LGD_CANCELTAXFREEAMOUNT);
    $xpay->Set("LGD_CANCELREASON", $LGD_CANCELREASON);
    $xpay->Set("LGD_RFACCOUNTNUM", $LGD_RFACCOUNTNUM);
    $xpay->Set("LGD_RFBANKCODE", $LGD_RFBANKCODE);
    $xpay->Set("LGD_RFCUSTOMERNAME", $LGD_RFCUSTOMERNAME);
    $xpay->Set("LGD_RFPHONE", $LGD_RFPHONE);
    /*
     * 1. ���� �κ���� ��û ���ó��
     *
     */
	$xpay->TX();

	if( "0000" == $xpay->Response_Code() ){
		//1)������Ұ�� ȭ��ó��(����,���� ��� ó���� �Ͻñ� �ٶ��ϴ�.)
		$settlelog = '������ XPay ī�� ��� ���'."\n";
		$settlelog .= '����ڵ� : '.$xpay->Response_Code()."\n";
		$settlelog .= '������� : '.$xpay->Response_Msg()."\n";
		$keys = $xpay->Response_Names();
            foreach($keys as $name) {
                $settlelog .=  $name . " = " . $xpay->Response($name, 0) . "<br>";
			}
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
