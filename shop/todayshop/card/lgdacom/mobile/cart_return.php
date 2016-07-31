<?php

include "../../../../lib/library.php";
include "../../../../conf/config.mobileShop.php";
include "../../../../conf/config.php";
include "../../../../conf/pg_mobile.lgdacom.php";


    /*
     * [����������û ������(STEP2-2)]
     *
     * LG�ڷ������� ���� �������� LGD_PAYKEY(����Key)�� ������ ���� ������û.(�Ķ���� ���޽� POST�� ����ϼ���)
     */

	$configPath = $_SERVER['DOCUMENT_ROOT'].$cfg['rootDir']."/conf/lgdacom_mobile"; //LG�ڷ��޿��� ������ ȯ������("/conf/lgdacom.conf,/conf/mall.conf") ��ġ ����. 

    /*
     *************************************************
     * 1.�������� ��û - BEGIN
     *  (��, ���� �ݾ�üũ�� ���Ͻô� ��� �ݾ�üũ �κ� �ּ��� ���� �Ͻø� �˴ϴ�.)
     *************************************************
     */
//	$CST_PLATFORM               = $HTTP_POST_VARS["CST_PLATFORM"];
//	$CST_MID                    = $HTTP_POST_VARS["CST_MID"];
	$CST_PLATFORM               = $pg_mobile['serviceType'];
    $CST_MID                    = $pg_mobile['id'];

    $LGD_MID                    = (("test" == $CST_PLATFORM)?"t":"").$CST_MID;
    $LGD_PAYKEY                 = $HTTP_POST_VARS["LGD_PAYKEY"];

   require_once("./XPayClient.php");
    $xpay = &new XPayClient($configPath, $CST_PLATFORM);
    $xpay->Init_TX($LGD_MID);
    
    $xpay->Set("LGD_TXNAME", "PaymentByKey");
    $xpay->Set("LGD_PAYKEY", $LGD_PAYKEY);
    
    //�ݾ��� üũ�Ͻñ� ���ϴ� ��� �Ʒ� �ּ��� Ǯ� �̿��Ͻʽÿ�.
	//$DB_AMOUNT = "DB�� ���ǿ��� ������ �ݾ�"; //�ݵ�� �������� �Ұ����� ��(DB�� ����)���� �ݾ��� �������ʽÿ�.
	//$xpay->Set("LGD_AMOUNTCHECKYN", "Y");
	//$xpay->Set("LGD_AMOUNT", $DB_AMOUNT);
	    
    /*
     *************************************************
     * 1.�������� ��û(�������� ������) - END
     *************************************************
     */

    /*
     * 2. �������� ��û ���ó��
     *
     * ���� ������û ��� ���� �Ķ���ʹ� �����޴����� �����Ͻñ� �ٶ��ϴ�.
     */

	$ordno	= $_POST['LGD_OID'];

    if ($xpay->TX()) {

		if($xpay->Response("LGD_PAYTYPE",0)=='SC0010') $payTypeStr = "�ſ�ī��";
		if($xpay->Response("LGD_PAYTYPE",0)=='SC0030') $payTypeStr = "������ü";
		if($xpay->Response("LGD_PAYTYPE",0)=='SC0040') $payTypeStr = "�������";
		if($xpay->Response("LGD_PAYTYPE",0)=='SC0060') $payTypeStr = "�ڵ���";

		$tmp_log[] = "LGU+ SmartXPay ������û�� ���� ���";
		$tmp_log[] = "TX Response_code : ".$xpay->Response_Code();
		$tmp_log[] = "TX Response_msg : ".$xpay->Response_Msg();
		$tmp_log[] = "����ڵ� : ".$xpay->Response("LGD_RESPCODE",0)." (0000(����) �׿� ����)";
		$tmp_log[] = "������� : ".$xpay->Response("LGD_RESPMSG",0);
		$tmp_log[] = "�ؽ�����Ÿ : ".$xpay->Response("LGD_HASHDATA",0);
		$tmp_log[] = "�����ݾ� : ".$xpay->Response("LGD_AMOUNT",0);
		$tmp_log[] = "�������̵� : ".$xpay->Response("LGD_MID",0);
		$tmp_log[] = "�ŷ���ȣ : ".$xpay->Response("LGD_TID",0);
		$tmp_log[] = "�ֹ���ȣ : ".$xpay->Response("LGD_OID",0);
		$tmp_log[] = "������� : ".$payTypeStr;
		$tmp_log[] = "�����Ͻ� : ".$xpay->Response("LGD_PAYDATE",0);

		$card_nm	= $xpay->Response("LGD_FINANCENAME",0);
        
		/*           
        $keys = $xpay->Response_Names();
        foreach($keys as $name) {
            echo $name . " = " . $xpay->Response($name, 0) . "<br>";
        }
		*/
           
        if( "0000" == $xpay->Response_Code() ) {
			$tmp_log[] = "�ŷ���ȣ : ".$xpay->Response("LGD_TID",0);
			$tmp_log[] = "����ũ�� ���� ���� : ".$xpay->Response("LGD_ESCROWYN",0);
			$tmp_log[] = "��������ڵ� : ".$xpay->Response("LGD_FINANCECODE",0);
			$tmp_log[] = "��������� : ".$xpay->Response("LGD_FINANCENAME",0);

			switch ($xpay->Response("LGD_PAYTYPE",0)){
				case "SC0010":	// �ſ�ī��
					$tmp_log[] = "����������ι�ȣ : ".$xpay->Response("LGD_FINANCEAUTHNUM",0);
					$tmp_log[] = "�ſ�ī���ȣ : ".$xpay->Response("LGD_CARDNUM",0)." (�Ϲ� �������� *ó����)";
					$tmp_log[] = "�ſ�ī���Һΰ��� : ".$xpay->Response("LGD_CARDINSTALLMONTH",0);
					$tmp_log[] = "�ſ�ī�幫���ڿ��� : ".$xpay->Response("LGD_CARDNOINTYN",0)." (1:������, 0:�Ϲ�)";
					break;
				case "SC0030":	// ������ü
					$tmp_log[] = "���ݿ��������ι�ȣ : ".$xpay->Response("LGD_CASHRECEIPTNUM",0);
					$tmp_log[] = "���ݿ����������߱������� : ".$xpay->Response("LGD_CASHRECEIPTSELFYN",0)." Y: �����߱�";
					$tmp_log[] = "���ݿ��������� : ".$xpay->Response("LGD_CASHRECEIPTKIND",0)." 0:�ҵ����, 1:��������";
					$tmp_log[] = "���¼������̸� : ".$xpay->Response("LGD_ACCOUNTOWNER",0);
					break;
				case "SC0040":	// �������
					$tmp_log[] = "���ݿ��������ι�ȣ : ".$xpay->Response("LGD_CASHRECEIPTNUM",0);
					$tmp_log[] = "���ݿ����������߱������� : ".$xpay->Response("LGD_CASHRECEIPTSELFYN",0)." Y: �����߱�";
					$tmp_log[] = "���ݿ��������� : ".$xpay->Response("LGD_CASHRECEIPTKIND",0)." 0:�ҵ����, 1:��������";
					$tmp_log[] = "������¹߱޹�ȣ : ".$xpay->Response("LGD_ACCOUNTNUM",0);
					$tmp_log[] = "��������Ա��ڸ� : ".$xpay->Response("LGD_PAYER",0);
					$tmp_log[] = "�Աݴ����ݾ� : ".$xpay->Response("LGD_CASTAMOUNT",0);
					$tmp_log[] = "���Աݱݾ� : ".$xpay->Response("LGD_CASCAMOUNT",0);
					$tmp_log[] = "�ŷ����� : ".$xpay->Response("LGD_CASFLAG",0)." (R:�Ҵ�,I:�Ա�,C:���)";
					$tmp_log[] = "��������Ϸù�ȣ : ".$xpay->Response("LGD_CASSEQNO",0);
					break;
				case "SC0060":	// �ڵ���
					break;
			}
         	//����������û ��� ���� DBó��
           	//echo "����������û ��� ���� DBó���Ͻñ� �ٶ��ϴ�.<br>";

            //����������û ��� ���� DBó�� ���н� Rollback ó��
			/*	$isDBOK = true; //DBó�� ���н� false�� ������ �ּ���.
          	if( !$isDBOK ) {
           		echo "<p>";
           		$xpay->Rollback("���� DBó�� ���з� ���Ͽ� Rollback ó�� [TID:" . $xpay->Response("LGD_TID",0) . ",MID:" . $xpay->Response("LGD_MID",0) . ",OID:" . $xpay->Response("LGD_OID",0) . "]");            		            		
            		
                echo "TX Rollback Response_code = " . $xpay->Response_Code() . "<br>";
                echo "TX Rollback Response_msg = " . $xpay->Response_Msg() . "<p>";
            		
                if( "0000" == $xpay->Response_Code() ) {
                  	echo "�ڵ���Ұ� ���������� �Ϸ� �Ǿ����ϴ�.<br>";
                }else{
          			echo "�ڵ���Ұ� ���������� ó������ �ʾҽ��ϴ�.<br>";
                }
          	}  
			*/
        }else{
          	//����������û ��� ���� DBó��
			//echo "����������û ��� ���� DBó���Ͻñ� �ٶ��ϴ�.<br>";
			exit;
        }
    }else {
        //2)API ��û���� ȭ��ó��
       
		echo "������û�� �����Ͽ����ϴ�.  <br>";
        echo "TX Response_code = " . $xpay->Response_Code() . "<br>";
        echo "TX Response_msg = " . $xpay->Response_Msg() . "<p>";
            
        //����������û ��� ���� DBó��
        echo "����������û ��� ���� DBó���Ͻñ� �ٶ��ϴ�.<br>";
		exit;
    }

	$settlelog = "{$ordno} (" . date('Y:m:d H:i:s') . ")\n-----------------------------------\n" . implode( "\n", $tmp_log ) . "\n-----------------------------------\n";

	// DB ó��
	$oData = $db->fetch("select step, vAccount from ".GD_ORDER." where ordno='$ordno'");
	if($oData['step'] > 0 || $oData['vAccount'] != '' || !strcmp($xpay->Response_Code(),"S007")){		// �ߺ�����

		### �α� ����
		$db->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");
		go("../../order_end.php?ordno=$ordno&card_nm=$card_nm","parent");

	} else if( "0000" == $xpay->Response_Code() ) {

		$query = "
		select * from
			".GD_ORDER." a
			left join ".GD_LIST_BANK." b on a.bankAccount = b.sno
		where
			a.ordno='$ordno'
		";
		$data = $db->fetch($query);

		include "../../../../lib/cart.class.php";

		$cart = new Cart($_COOKIE[gd_isDirect]);
		$cart->chkCoupon();
		$cart->delivery = $data[delivery];
		$cart->dc = $member[dc]."%";
		$cart->calcu();

		### �ֹ�Ȯ�θ���
		$data[cart] = $cart;
		$data[str_settlekind] = $r_settlekind[$data[settlekind]];
		sendMailCase($data[email],0,$data);

		### ����ũ�� ���� Ȯ��
		if($xpay->Response("LGD_ESCROWYN",0) == 'Y'){
			$escrowyn = "y";
			$escrowno = $xpay->Response("LGD_TID",0);
		}else{
			$escrowyn = "n";
			$escrowno = "";
		}

		### ���� ���� ����
		$step = 1;
		$qrc1 = "cyn='y', cdt=now(), cardtno='".$xpay->Response("LGD_TID",0)."',";
		$qrc2 = "cyn='y',";

		### ������� ������ �������� ����
		if ($xpay->Response("LGD_PAYTYPE",0) == 'SC0040'){
			$vAccount = $xpay->Response("LGD_FINANCENAME",0)." ".$xpay->Response("LGD_ACCOUNTNUM",0)." ".$xpay->Response("LGD_PAYER",0);
			$step = 0; $qrc1 = $qrc2 = "";
		}

		### ���ݿ����� ����
		if ($xpay->Response("LGD_CASHRECEIPTNUM",0)){
			$qrc1 .= "cashreceipt='".$xpay->Response("LGD_CASHRECEIPTNUM",0)."',";
		}

		### �ǵ���Ÿ ����
		$db->query("
		update ".GD_ORDER." set $qrc1
			step		= '$step',
			step2		= '',
			escrowyn	= '$escrowyn',
			escrowno	= '$escrowno',
			vAccount	= '$vAccount',
			settlelog	= concat(ifnull(settlelog,''),'$settlelog')
		where ordno='$ordno'"
		);
		$db->query("update ".GD_ORDER_ITEM." set $qrc2 istep='$step' where ordno='$ordno'");

		### �ֹ��α� ����
		orderLog($ordno,$r_step2[$data[step2]]." > ".$r_step[$step]);

		### ��� ó��
		setStock($ordno);

		### ��ǰ���Խ� ������ ���
		if ($sess[m_no] && $data[emoney]){
			setEmoney($sess[m_no],-$data[emoney],"��ǰ���Խ� ������ ���� ���",$ordno);
		}

		### SMS ���� ����
		$dataSms = $data;

		if ($xpay->Response("LGD_PAYTYPE",0) != "SC0040"){
			sendMailCase($data[email],1,$data);			### �Ա�Ȯ�θ���
			sendSmsCase('incash',$data[mobileOrder]);	### �Ա�Ȯ��SMS
		} else {
			sendSmsCase('order',$data[mobileOrder]);	### �ֹ�Ȯ��SMS
		}

		go($cfgMobileShop['mobileShopRootDir']."/ord/order_end.php?ordno=$ordno&card_nm=$card_nm","parent");
	}else{
		$db->query("update ".GD_ORDER." set step2=54, settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno' and step2=50");
		$db->query("update ".GD_ORDER_ITEM." set istep=54 where ordno='$ordno' and istep=50");
		go($cfgMobileShop['mobileShopRootDir']."/ord/order_fail.php?ordno=$ordno","parent");
	}
?>
