<?php
include "../../../lib/library.php";
include "../../../conf/config.php";
//include "../../../conf/pg.lgdacom.php";

// �����̼� ������� ��� PG ���� ��ü
resetPaymentGateway();

	/*
	 * [���� �������ó��(DB) ������]
	 *
	 * 1) ������ ������ ���� hashdata�� ������ �ݵ�� �����ϼž� �մϴ�.
	 *
	 */
	$LGD_RESPCODE			= $_POST['LGD_RESPCODE'];				// �����ڵ�: 0000(����) �׿� ����
	$LGD_RESPMSG			= $_POST['LGD_RESPMSG'];				// ����޼���
	$LGD_MID				= $_POST['LGD_MID'];					// �������̵�
	$LGD_OID				= $_POST['LGD_OID'];					// �ֹ���ȣ
	$LGD_AMOUNT				= $_POST['LGD_AMOUNT'];					// �ŷ��ݾ�
	$LGD_TID				= $_POST['LGD_TID'];					// �������� �ο��� �ŷ���ȣ
	$LGD_PAYTYPE			= $_POST['LGD_PAYTYPE'];				// ���������ڵ�
	$LGD_PAYDATE			= $_POST['LGD_PAYDATE'];				// �ŷ��Ͻ�(�����Ͻ�/��ü�Ͻ�)
	$LGD_HASHDATA			= $_POST['LGD_HASHDATA'];				// �ؽ���
	$LGD_FINANCECODE		= $_POST['LGD_FINANCECODE'];			// ��������ڵ�(�����ڵ�)
	$LGD_FINANCENAME		= $_POST['LGD_FINANCENAME'];			// ��������̸�(�����̸�)
	$LGD_ESCROWYN			= $_POST['LGD_ESCROWYN'];				// ����ũ�� ���뿩��
	$LGD_TIMESTAMP			= $_POST['LGD_TIMESTAMP'];				// Ÿ�ӽ�����
	$LGD_ACCOUNTNUM			= $_POST['LGD_ACCOUNTNUM'];				// ���¹�ȣ(�������Ա�)
	$LGD_CASTAMOUNT			= $_POST['LGD_CASTAMOUNT'];				// �Ա��Ѿ�(�������Ա�)
	$LGD_CASCAMOUNT			= $_POST['LGD_CASCAMOUNT'];				// ���Աݾ�(�������Ա�)
	$LGD_CASFLAG			= $_POST['LGD_CASFLAG'];				// �������Ա� �÷���(�������Ա�) - 'R':�����Ҵ�, 'I':�Ա�, 'C':�Ա����
	$LGD_CASSEQNO			= $_POST['LGD_CASSEQNO'];				// �Աݼ���(�������Ա�)
	$LGD_CASHRECEIPTNUM		= $_POST['LGD_CASHRECEIPTNUM'];			// ���ݿ����� ���ι�ȣ
	$LGD_CASHRECEIPTSELFYN	= $_POST['LGD_CASHRECEIPTSELFYN'];		// ���ݿ����������߱������� Y: �����߱��� ����, �׿� : ������
	$LGD_CASHRECEIPTKIND	= $_POST['LGD_CASHRECEIPTKIND'];		// ���ݿ����� ���� 0: �ҵ������ , 1: ����������

	/*
	 * ��������
	 */
	$LGD_BUYER				= $_POST['LGD_BUYER'];					// ������
	$LGD_PRODUCTINFO		= $_POST['LGD_PRODUCTINFO'];			// ��ǰ��
	$LGD_BUYERID			= $_POST['LGD_BUYERID'];				// ������ ID
	$LGD_BUYERADDRESS		= $_POST['LGD_BUYERADDRESS'];			// ������ �ּ�
	$LGD_BUYERPHONE			= $_POST['LGD_BUYERPHONE'];				// ������ ��ȭ��ȣ
	$LGD_BUYEREMAIL			= $_POST['LGD_BUYEREMAIL'];				// ������ �̸���
	$LGD_BUYERSSN			= $_POST['LGD_BUYERSSN'];				// ������ �ֹι�ȣ
	$LGD_PRODUCTCODE		= $_POST['LGD_PRODUCTCODE'];			// ��ǰ�ڵ�
	$LGD_RECEIVER			= $_POST['LGD_RECEIVER'];				// ������
	$LGD_RECEIVERPHONE		= $_POST['LGD_RECEIVERPHONE'];			// ������ ��ȭ��ȣ
	$LGD_DELIVERYINFO		= $_POST['LGD_DELIVERYINFO'];			// �����

	$LGD_MERTKEY = $pg['mertkey'];  //�����޿��� �߱��� ����Ű�� ������ �ֽñ� �ٶ��ϴ�.

	$LGD_HASHDATA2 = md5($LGD_MID.$LGD_OID.$LGD_AMOUNT.$LGD_RESPCODE.$LGD_TIMESTAMP.$LGD_MERTKEY);

	/*
	 * ���� ó����� ���ϸ޼���
	 *
	 * OK  : ���� ó����� ����
	 * �׿� : ���� ó����� ����
	 *
	 * �� ���ǻ��� : ������ 'OK' �����̿��� �ٸ����ڿ��� ���ԵǸ� ����ó�� �ǿ��� �����Ͻñ� �ٶ��ϴ�.
	 */
	$resultMSG = "������� ���� DBó��(LGD_CASNOTEURL) ������� �Է��� �ֽñ� �ٶ��ϴ�.";

	$tmp_log[] = "������ XPay �������Աݿ� ���� ���";
	$tmp_log[] = "����ڵ� : ".$LGD_RESPCODE." (0000(����) �׿� ����)";
	$tmp_log[] = "������� : ".$LGD_RESPMSG;
	$tmp_log[] = "�ؽ�����Ÿ : ".$LGD_HASHDATA." (������)";
	$tmp_log[] = "�ؽ�����Ÿ : ".$LGD_HASHDATA2." (����)";
	$tmp_log[] = "�����ݾ� : ".$LGD_AMOUNT;
	$tmp_log[] = "�������̵� : ".$LGD_MID;
	$tmp_log[] = "�ֹ���ȣ : ".$LGD_OID;
	$tmp_log[] = "�����Ͻ� : ".$LGD_PAYDATE;
	$tmp_log[] = "�ŷ���ȣ : ".$LGD_TID;
	$tmp_log[] = "����ũ�� ���� ���� : ".$LGD_ESCROWYN;
	$tmp_log[] = "��������ڵ� : ".$LGD_FINANCECODE;
	$tmp_log[] = "��������� : ".$LGD_FINANCENAME;
	$tmp_log[] = "���ݿ��������ι�ȣ : ".$LGD_CASHRECEIPTNUM;
	$tmp_log[] = "���ݿ����������߱������� : ".$LGD_CASHRECEIPTSELFYN." Y: �����߱�";
	$tmp_log[] = "���ݿ��������� : ".$LGD_CASHRECEIPTKIND." 0:�ҵ����, 1:��������";
	$tmp_log[] = "������¹߱޹�ȣ : ".$LGD_ACCOUNTNUM;
	$tmp_log[] = "��������Ա��ڸ� : ".$LGD_PAYER;
	$tmp_log[] = "�Աݴ����ݾ� : ".$LGD_CASTAMOUNT;
	$tmp_log[] = "���Աݱݾ� : ".$LGD_CASCAMOUNT;
	$tmp_log[] = "�ŷ����� : ".$LGD_CASFLAG." (R:�Ҵ�,I:�Ա�,C:���)";
	$tmp_log[] = "��������Ϸù�ȣ : ".$LGD_CASSEQNO;

	$ordno = $LGD_OID;

	$settlelog = "{$ordno} (" . date('Y:m:d H:i:s') . ")\n-----------------------------------\n" . implode( "\n", $tmp_log ) . "\n-----------------------------------\n";

	$resultCHK	= true;
	if ( $LGD_HASHDATA2 == $LGD_HASHDATA ) { //�ؽ��� ������ �����̸�
		### ������� ������ ��� üũ �ܰ� ����
		$res_cstock = true;
		if($cfg['stepStock'] == '0' && $LGD_CASFLAG != 'R') $res_cstock = false;
		if($cfg['stepStock'] == '1' && $LGD_CASFLAG != 'I') $res_cstock = false;

		### item check stock
		include "../../../lib/cardCancel.class.php";
		include "../../../lib/cardCancel_social.class.php";
		$cancel = new cardCancel_social();
		if(!$cancel->chk_item_stock($ordno) && $res_cstock){
			$resultMSG	= "���� ��� �������� ���";
			$resultCHK	= false;
			$cancel->cancel_db_proc($ordno,$LGD_TID);
		}else{
			$oData = $db->fetch("select step, vAccount from ".GD_ORDER." where ordno='$ordno'");
			if($oData['step'] > 0 || ($oData['vAccount'] != '' && $LGD_CASFLAG != 'I') || $LGD_RESPCODE == 'S007'){ //������ �ߺ������ϸ�

				$db->query("update ".GD_ORDER." set settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");

				$resultMSG = "OK";

			}else if($LGD_RESPCODE == "0000"){ //������ �����̸�

				if( "R" == $LGD_CASFLAG ) {
					/*
					 * ������ �Ҵ� ���� ��� ���� ó��(DB) �κ�
					 * ���� ��� ó���� �����̸� "OK"
					 */
					//if( ������ �Ҵ� ���� ����ó����� ���� ) $resultMSG = "OK";
					$resultMSG = "OK";
				}else if( "I" == $LGD_CASFLAG ) {
	 				/*
					 * ������ �Ա� ���� ��� ���� ó��(DB) �κ�
					 * ���� ��� ó���� �����̸� "OK"
					 */

					### ���� ���� ����
					$step = 1;
					$qrc1 = "cyn='y', cdt=now(), cardtno='".$LGD_TID."',";
					$qrc2 = "cyn='y',";

					$pre = $db->fetch("select step2, emoney, m_no from ".GD_ORDER." where ordno='$ordno'");
					$db->query("update ".GD_ORDER." set step='$step', step2='', $qrc1 settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");
					$db->query("update ".GD_ORDER_ITEM." set $qrc2 istep='$step' where ordno='$ordno'");

					### ��� ó��
					setStock($ordno);

					### ��ǰ���Խ� ������ ���
					if ($pre[m_no] && $pre[emoney]){
						setEmoney($pre[m_no],-$pre[emoney],"��ǰ���Խ� ������ ���� ���",$ordno);
					}
					$resultMSG = "OK";
				}else if( "C" == $LGD_CASFLAG ) {
	 				/*
					 * ������ �Ա���� ���� ��� ���� ó��(DB) �κ�
					 * ���� ��� ó���� �����̸� "OK"
					 */
					//if( ������ �Ա���� ���� ����ó����� ���� ) $resultMSG = "OK";
					$resultMSG = "OK";
					$resultCHK	= false;
				}
			}else { //������ �����̸�
				$resultMSG = "OK";
				$resultCHK	= false;
			}
		}
	} else { //�ؽ����� ������ �����̸�
		/*
		 * hashdata���� ���� �α׸� ó���Ͻñ� �ٶ��ϴ�.
		 */
		$resultMSG = "������� ���� DBó��(LGD_CASNOTEURL) �ؽ��� ������ �����Ͽ����ϴ�.";
		$resultCHK	= false;
	}

	if($resultCHK === false){
		//$db->query("update ".GD_ORDER." set step2=54, settlelog=concat(ifnull(settlelog,''),'$settlelog'),cardtno='".$LGD_TID."' where ordno='$ordno'");
		$db->query("update ".GD_ORDER." set step2=54, settlelog=concat(ifnull(settlelog,''),'$settlelog') where ordno='$ordno'");
		$db->query("update ".GD_ORDER_ITEM." set istep=54 where ordno='$ordno' and istep=50");
	}

	echo $resultMSG;
?>
