<?php
include "../../../lib/library.php";
include "../../../conf/config.php";
//include "../../../conf/pg.lgdacom.php";
require_once "../../../lib/load.class.php";

// �����̼� ������� ��� PG ���� ��ü
resetPaymentGateway();

	/*
	 * [����������û ������(STEP2-2)]
	 *
	 * LG���������� ���� �������� LGD_PAYKEY(����Key)�� ������ ���� ������û.(�Ķ���� ���޽� POST�� ����ϼ���)
	 */

	$configPath					= $_SERVER['DOCUMENT_ROOT'].$cfg['rootDir']."/conf/lgdacom_today";		//LG�����޿��� ������ ȯ������("/conf/lgdacom.conf") ��ġ ����.

	/*
	 *************************************************
	 * 1.�������� ��û - BEGIN
	 *  (��, ���� �ݾ�üũ�� ���Ͻô� ��� �ݾ�üũ �κ� �ּ��� ���� �Ͻø� �˴ϴ�.)
	 *************************************************
	 */
	$CST_PLATFORM				= $_POST['CST_PLATFORM'];
	$CST_MID					= $_POST['CST_MID'];
	$LGD_MID					= (("test" == $CST_PLATFORM)?"t":"").$CST_MID;
	$LGD_PAYKEY					= $_POST['LGD_PAYKEY'];
	$LGD_CUSTOM_PROCESSTIMEOUT	= $_POST['LGD_CUSTOM_PROCESSTIMEOUT'];

	require_once("./XPayClient.php");
	$xpay = &new XPayClient($configPath, $CST_PLATFORM);
	$xpay->Init_TX($LGD_MID);
	$amount_check = true;

	//���� �ݾ�üũ�� ���Ͻø� �ּ��� ������ �ּ���.
	/*
	$xpay->Set("LGD_TXNAME", "AmountCheck");
	$xpay->Set("LGD_PAYKEY", $LGD_PAYKEY);
	$xpay->Set("LGD_AMOUNT", $HTTP_POST_VARS["LGD_AMOUNT"]);
	if ($xpay->TX()) {
		if($xpay->Response_Code() != "0000" ) {
			$amount_check = false;
		}
	}else {
		$amount_check = false;
	}
	*/

	$xpay->Set("LGD_TXNAME", "PaymentByKey");
	$xpay->Set("LGD_PAYKEY", $LGD_PAYKEY);
	$xpay->Set("LGD_CUSTOM_PROCESSTIMEOUT", $LGD_CUSTOM_PROCESSTIMEOUT);
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

	// �����޿��� ���� value
	if ($amount_check) {
		if ($xpay->TX()) {
			if($xpay->Response("LGD_PAYTYPE",0)=='SC0010') $payTypeStr = "�ſ�ī��";
			if($xpay->Response("LGD_PAYTYPE",0)=='SC0030') $payTypeStr = "������ü";
			if($xpay->Response("LGD_PAYTYPE",0)=='SC0040') $payTypeStr = "�������";
			if($xpay->Response("LGD_PAYTYPE",0)=='SC0060') $payTypeStr = "�ڵ���";

			$tmp_log[] = "������ XPay ������û�� ���� ���";
			$tmp_log[] = "TX Response_code : ".$xpay->Response_Code();
			$tmp_log[] = "TX Response_msg : ".$xpay->Response_Msg();
			$tmp_log[] = "����ڵ� : ".$xpay->Response("LGD_RESPCODE",0)." (0000(����) �׿� ����)";
			$tmp_log[] = "������� : ".$xpay->Response("LGD_RESPMSG",0);
			$tmp_log[] = "�ؽ�����Ÿ : ".$xpay->Response("LGD_HASHDATA",0);
			$tmp_log[] = "�����ݾ� : ".$xpay->Response("LGD_AMOUNT",0);
			$tmp_log[] = "�������̵� : ".$xpay->Response("LGD_MID",0);
			$tmp_log[] = "�ֹ���ȣ : ".$xpay->Response("LGD_OID",0);
			$tmp_log[] = "������� : ".$payTypeStr;
			$tmp_log[] = "�����Ͻ� : ".$xpay->Response("LGD_PAYDATE",0);

			$card_nm	= $xpay->Response("LGD_FINANCENAME",0);

			/*
			$keys = $xpay->Response_Names();
			foreach($keys as $name) {
				echo $name . " = " . $xpay->Response($name, 0) . "<br>";
			}
			echo "<p>";
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
				/*$isDBOK = true; //DBó�� ���н� false�� ������ �ּ���.
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
			}
		}else {
			//����������û ��� ���� DBó��
			//echo "����������û ��� ���� DBó���Ͻñ� �ٶ��ϴ�.<br>";
		}
	}else{
		//�����ݾ� üũ ���� ȭ��ó��
		//echo "���� ������û �ݾ��� �����մϴ�. �ݾ��� Ȯ���Ͽ� �ֽʽÿ�.";
	}

	$settlelog = "{$ordno} (" . date('Y:m:d H:i:s') . ")\n-----------------------------------\n" . implode( "\n", $tmp_log ) . "\n-----------------------------------\n";

	### ���ں������� �߱�
	@session_start();
	if (session_is_registered('eggData') === true && $xpay->Response_Code() == "0000" ){
		if ($_SESSION[eggData][ordno] == $ordno && $_SESSION[eggData][resno1] != '' && $_SESSION[eggData][resno2] != '' && $_SESSION[eggData][agree] == 'Y'){
			include '../../../lib/egg.class.usafe.php';
			$eggData = $_SESSION[eggData];
			switch ($xpay->Response("LGD_PAYTYPE",0)){
				case "SC0010":
					$eggData[payInfo1] = $xpay->Response("LGD_FINANCENAME",0); # (*) ��������(ī���)
					$eggData[payInfo2] = $xpay->Response("LGD_FINANCEAUTHNUM",0); # (*) ��������(���ι�ȣ)
					break;
				case "SC0030":
					$eggData[payInfo1] = $xpay->Response("LGD_FINANCENAME",0); # (*) ��������(�����)
					$eggData[payInfo2] = $xpay->Response("LGD_TID",0); # (*) ��������(���ι�ȣ or �ŷ���ȣ)
					break;
				case "SC0040":
					$eggData[payInfo1] = $xpay->Response("LGD_FINANCENAME",0); # (*) ��������(�����)
					$eggData[payInfo2] = $xpay->Response("LGD_ACCOUNTNUM",0); # (*) ��������(���¹�ȣ)
					break;
			}
			$eggCls = new Egg( 'create', $eggData );
			//if ( $eggCls->isErr == true && $xpay->Response("LGD_PAYTYPE",0) == "SC0060" ){
				//$xpay->Response("LGD_RESPCODE",0) = '';
			//}
			//else if ( $eggCls->isErr == true && in_array($xpay->Response("LGD_PAYTYPE",0), array("SC0010","SC0030")) );
		}
		session_unregister('eggData');
	}

	### ������� ������ ��� üũ �ܰ� ����
	$res_cstock = true;
	if($cfg['stepStock'] == '1' && $xpay->Response("LGD_PAYTYPE",0) == "SC0040") $res_cstock = false;

	### item check stock
	include "../../../lib/cardCancel.class.php";
	$cancel = new cardCancel();
	if(!$cancel->chk_item_stock($ordno) && $res_cstock){
		$step = 51;
	}

	// DB ó��
	$oData = $db->fetch("select step, vAccount from ".GD_ORDER." where ordno='$ordno'");
	if($oData['step'] > 0 || $oData['vAccount'] != '' || !strcmp($xpay->Response_Code(),"S007")){		// �ߺ�����

		### �α� ����
		$db->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");
		go("../../order_end.php?ordno=$ordno&card_nm=$card_nm","parent");

	} else if( "0000" == $xpay->Response_Code() && $step != 51 ) {	// ��������

		$query = "
		select * from
			".GD_ORDER." a
			left join ".GD_LIST_BANK." b on a.bankAccount = b.sno
		where
			a.ordno='$ordno'
		";
		$data = $db->fetch($query);

		include "../../../lib/cart.class.php";

		$cart = new Cart($_COOKIE[gd_isDirect]);
		$cart->chkCoupon();
		$cart->delivery = $data[delivery];
		$cart->dc = $member[dc]."%";
		$cart->calcu();

		### �ֹ�Ȯ�θ���
		$data[cart] = $cart;
		$data[str_settlekind] = $r_settlekind[$data[settlekind]];
		//sendMailCase($data[email],0,$data);
		// �����̼� �ֹ� sms & ���� �׸��� ���� �߱�
		$todayshop_noti = &load_class('todayshop_noti', 'todayshop_noti');
		$orderinfo = $todayshop_noti->getorderinfo($ordno);
		$todayshop_noti->set($ordno,'order');
		$todayshop_noti->send();

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
		if ($data[m_no] && $data[emoney]){
			setEmoney($data[m_no],-$data[emoney],"��ǰ���Խ� ������ ���� ���",$ordno);
		}

		### SMS ���� ����
		$dataSms = $data;

		if ($xpay->Response("LGD_PAYTYPE",0) != "SC0040"){ // ������°� �ƴҰ��

			/*/
			sendMailCase($data['email'],1,$data);			### �Ա�Ȯ�θ���
			sendSmsCase('incash',$data['mobileOrder']);	### �Ա�Ȯ��SMS
			/*/

			// ��� �߱� ���� ���� �� ���� ���� (todayshop_noti Ŭ������ todayshop �� ��ӹ޾ұ� ������ ����� ����ص� ��)
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
			else {	
				// ������ �ƴ� �ǹ���ǰ�� ���, �Ǹŷ� ����
				$query = "
					select
					TG.tgsno from ".GD_ORDER_ITEM." AS O
					INNER JOIN ".GD_TODAYSHOP_GOODS." AS TG
					ON O.goodsno = TG.goodsno
					where O.ordno='$ordno'
				";
				$res = $db->query($query);
				while($tmp = $db->fetch($res)) {
		
					$query = "
						SELECT
		
							IFNULL(SUM(OI.ea), 0) AS cnt
		
						FROM ".GD_ORDER." AS O
						INNER JOIN ".GD_ORDER_ITEM." AS OI
							ON O.ordno=OI.ordno
						INNER JOIN ".GD_TODAYSHOP_GOODS_MERGED." AS TG
							ON OI.goodsno = TG.goodsno
		
						WHERE
								O.step > 0
							AND O.step2 < 40
							AND TG.tgsno='".$tmp['tgsno']."'
		
					";
		
					$_res = $db->query($query);
		
					while ($_tmp = $db->fetch($_res)) {
		
						$query = "
						UPDATE
							".GD_TODAYSHOP_GOODS_MERGED."		AS TGM
							INNER JOIN ".GD_TODAYSHOP_GOODS."	AS TG	ON TGM.goodsno = TG.goodsno
						SET
							TGM.buyercnt = ".$_tmp['cnt'].",
							TG.buyercnt = ".$_tmp['cnt']."
						WHERE
							TG.tgsno = ".$tmp['tgsno']."
						";
						$db->query($query);
		
					}
		
				}
			}				
			/*
			else
			{
				$todayshop_noti->set($ordno,'sale');
				$todayshop_noti->send();
			}
			*/
			// eof �����̼� ���� �߱�
			/**/

		}
		/*
		else { // ������� �ӽ� �׽�Ʈ.
			$todayshop_noti = &load_class('todayshop_noti', 'todayshop_noti');
			$orderinfo = $todayshop_noti->getorderinfo($ordno);

			$todayshop_noti->set($ordno,'order');
			$todayshop_noti->send();

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
		}
		*/
		/*
		else {
			sendSmsCase('order',$data[mobileOrder]);	### �ֹ�Ȯ��SMS
		}
		*/

		go("../../order_end.php?ordno=$ordno&card_nm=$card_nm","parent");
	}else{
		if ($step == '51') {
			$cancel->cancel_db_proc($ordno);
		} else {
			$db->query("update ".GD_ORDER." set step2='54', settlelog=concat(ifnull(settlelog,''),'".$settlelog."') where ordno='".$ordno."'");
			$db->query("update ".GD_ORDER_ITEM." set istep='54' where ordno='".$ordno."'");
		}
		go("../../order_fail.php?ordno=$ordno","parent");
	}
?>
