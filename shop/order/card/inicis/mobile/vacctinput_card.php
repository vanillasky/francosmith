<?php

// ISP, ������ü�� ��� card_return.php�� ��ġ�� �ʱ⶧���� ���̹� ���ϸ��� ���� ���� API ȣ��
if ($P_TYPE == 'CARD') {
	include dirname(__FILE__).'/../../../../lib/naverNcash.class.php';
	$naverNcash = new naverNcash(true);
	if ($naverNcash->useyn == 'Y') {
		$ncashResult = $naverNcash->payment_approval($P_OID, true);
		if ($ncashResult === false) {
			exit("OK");
		}
	}
}

$ordno = $P_OID;
if (!$ordno) exit;

if($P_TYPE == "CARD") //kb���� ��ī�� ������
{
	// PG���� ������ üũ �� ��ȿ�� üũ
	if (forge_order_check($P_OID,$P_AMT) === false) {
		$claimReason = $P_RMESG1."->�ڵ� �������(��ǰ�ݾװ� �����ݾ��� ��ġ���� ����.)";
		$settlelog = "
		----------------------------------------
		������ȣ : ".$P_TID."
		������� : ".$P_TYPE."
		����ڵ� : ".$P_STATUS."
		���νð� : ".$P_AUTH_DT."
		�ֹ���ȣ : ".$P_OID."
		������� : ".$P_FN_NM."
		�ŷ��ݾ� : ".$P_AMT."
		������� : ".$claimReason."
		----------------------------------------
		";
		cancel_inicis($ordno,$P_TID,$settlelog,$claimReason);
		exit('OK');
	}

	if($P_STATUS != "00") //���� "00" �� �ƴϸ�
	{
		// ISP, ������ü ���� �� ���̹� ���ϸ��� ���� ���� ��� API ȣ��
		if ($P_TYPE == 'CARD') {
			if ($naverNcash->useyn == 'Y') $naverNcash->payment_approval_cancel($P_OID);
		}

		// �ֹ��������� ���� ����
		$settlelog = "$ordno (".date('Y:m:d H:i:s').")
----------------------------------------
�ŷ���ȣ : ".$P_TID."
����ڵ� : ".$P_STATUS."
������� : ".$P_RMESG1."
���ҹ�� : ".$P_TYPE."
���αݾ� : ".$P_AMT."
----------------------------------------";

		$db->query("update ".GD_ORDER." set step2=54, settlelog=concat(ifnull(settlelog,''),'$settlelog'),cardtno='".$P_TID."' where ordno='$ordno'");
		$db->query("update ".GD_ORDER_ITEM." set istep=54 where ordno='$ordno'");

		echo "OK";
		exit();
	}
}

$settlelog = "
----------------------------------------
�Ա�Ȯ�� : PG���ڵ��Ա�Ȯ��
���νð� : ".$P_AUTH_DT."
����ڵ� : ".$P_STATUS."
Ȯ�νð� : ".date('Y:m:d H:i:s')."
�Աݱݾ� : ".$P_AMT."
----------------------------------------
";

if($P_TYPE == "CARD"){
$settlelog = "
----------------------------------------
������ȣ : ".$P_TID."
������� : ".$P_TYPE."
����ڵ� : ".$P_STATUS."
���νð� : ".$P_AUTH_DT."
�ֹ���ȣ : ".$P_OID."
������� : ".$P_FN_NM."
�ŷ��ݾ� : ".$P_AMT."
�ŷ���� : ".$P_RMESG1."
----------------------------------------
";
}
	### item check stock
	include "../../../../lib/cardCancel.class.php";
	$cancel = new cardCancel();
	if(!$cancel->chk_item_stock($ordno) && $cfg['stepStock'] == '1'){
		$cancel -> cancel_db_proc($ordno,$P_TID);
	}else{
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
		if ($data['m_no'] && $data['emoney'] && $P_TYPE == 'CARD') {
			setEmoney($data['m_no'], -$data['emoney'], '��ǰ���Խ� ������ ���� ���', $ordno);
		}

		### �Ա�Ȯ�θ���
		sendMailCase($data[email],1,$data);

		### �Ա�Ȯ��SMS
		$dataSms = $data;
		sendSmsCase('incash',$data[mobileOrder]);

	}

//************************************************************************************

        //������ ���� �����ͺ��̽��� ��� ���������� ���� �����ÿ��� "OK"�� �̴Ͻý���
        //�����ϼž��մϴ�. �Ʒ� ���ǿ� �����ͺ��̽� ������ �޴� FLAG ������ ��������
        //(����) OK�� �������� �����ø� �̴Ͻý� ���� ������ "OK"�� �����Ҷ����� ��� �������� �õ��մϴ�
        //��Ÿ �ٸ� ������ PRINT( echo )�� ���� �����ñ� �ٶ��ϴ�

//      if (�����ͺ��̽� ��� ���� ���� ���Ǻ��� = true)
//      {

                echo "OK";                        // ����� ������������

//      }

//*************************************************************************************

?>