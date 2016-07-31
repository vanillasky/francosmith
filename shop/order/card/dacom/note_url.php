<?php
//�� �������� �������� ���ʽÿ�. ������ html�±׳� �ڹٽ�ũ��Ʈ�� ���� ��� ������ ������ �� �����ϴ�.
//���� dbó���� write.php���� �����ϴ� �Լ� write_success(),write_failure(),write_hasherr()���� ���� ��ƾ�� �߰��Ͻø� �˴ϴ�.
//���� �� �Լ����� ���� ������ ���� log����� �˴ϴ�. ������������ ����� �����η� �°� �����Ͽ� �ּ���

//hash����Ÿ���� �´� �� Ȯ�� �ϴ� ��ƾ�� �����޿��� ���� ����Ÿ�� �´��� Ȯ���ϴ� ���̹Ƿ� �� ����ϼž� �մϴ�
//�������� ���� ���ӿ��� �ұ��ϰ� ��Ƽ �������� ������ ��Ʈ�� ���� ������ ���� hash ���� ������ �߻��� ���� �ֽ��ϴ�.
//�׷��Ƿ� hash �����ǿ� ���ؼ��� ���� �߻��� ������ �ľ��Ͽ� ��� ���� �� ��ó�� �ּž� �մϴ�.

//���������� ó���� ��쿡�� �����޿��� ������ ���� ���� ���� ��������� �ߺ��ؼ� ���� �� �����Ƿ� ������ ó���� ����Ǿ�� �մϴ�.
//�� �������� ������������ ���ο� ���� 'OK' �Ǵ� 'FAIL' �̶�� ���ڸ� ǥ���ϵ��� �Ǿ����ϴ�.
//PHP������ �ǵ����̸� error_reporting() �Լ��� �̿��Ͽ� ���� �Ŀ��� �ܼ��� ���޼����� ����� ���� �ʵ��� ���ֽʽÿ�.

	// �������� function page
	include("./note_write.php");



	// �����޿��� ���� value
	$respcode="";		// �����ڵ�: 0000(����) �׿� ����
	$respmsg="";		// ����޼���
	$hashdata="";		// �ؽ���
	$transaction="";	// �������� �ο��� �ŷ���ȣ
	$mid="";			// �������̵�
	$oid="";			// �ֹ���ȣ
	$amount="";			// �ŷ��ݾ�
	$currency="";		// ��ȭ�ڵ�('410':��ȭ, '840':�޷�)
	$paytype="";		// ���������ڵ�
	$msgtype="";		// �ŷ������� ���� �������� ������ �ڵ�
	$paydate="";		// �ŷ��Ͻ�(�����Ͻ�/��ü�Ͻ�)
	$buyer="";			// �����ڸ�
	$productinfo="";	// ��ǰ����
	$buyerssn="";		// �������ֹε�Ϲ�ȣ
	$buyerid="";		// ������ID
	$buyeraddress="";	// �������ּ�
	$buyerphone="";		// ��������ȭ��ȣ
	$buyeremail="";		// �������̸����ּ�
	$receiver="";		// �����θ�
	$receiverphone="";	// ��������ȭ��ȣ
	$deliveryinfo="";	// �������
	$producttype="";	// ��ǰ����
	$productcode="";	// ��ǰ�ڵ�
	$financecode="";	// ��������ڵ�(ī������/�����ڵ�)
	$financename="";	// ��������̸�(ī���̸�/�����̸�)
	$useescrow="";		// ���� ����ũ�� ���� ���� - Y:����, N:������

	$authnumber="";		// ���ι�ȣ(�ſ�ī��)
	$cardnumber="";		// ī���ȣ(�ſ�ī��)
	$cardexp="";		// ��ȿ�Ⱓ(�ſ�ī��)
	$cardperiod="";		// �Һΰ�����(�ſ�ī��)
	$nointerestflag="";	// �������Һο���(�ſ�ī��) - '1'�̸� �������Һ� '0'�̸� �Ϲ��Һ�
	$transamount="";	// ȯ������ݾ�(�ſ�ī��)
	$exchangerate="";	// ȯ��(�ſ�ī��)

	$pid="";			// ������/�޴��������� �ֹε�Ϲ�ȣ(������ü/�޴���)
	$accountowner="";	// ���¼������̸�(������ü)
	$accountnumber="";	// ���¹�ȣ(������ü, �������Ա�)

	$telno="";			// �޴�����ȣ(�޴���)

	$payer="";			// �Ա���(�������Ա�)
	$cflag="";			// �������Ա� �÷���(�������Ա�) - 'R':�����Ҵ�, 'I':�Ա�, 'C':�Ա����
	$tamount="";		// �Ա��Ѿ�(�������Ա�)
	$camount="";		// ���Աݾ�(�������Ա�)
	$bankdate="";		// �ԱݶǴ�����Ͻ�(�������Ա�)
	$seqno="";			// �Աݼ���(�������Ա�)
	$receiptnumber="";	// ���ݿ����� ���ι�ȣ


	$resp = false;		// ������� ��������

	$respcode = get_param("respcode");
	$respmsg = get_param("respmsg");
	$hashdata = get_param("hashdata");
	$transaction = get_param("transaction");
	$mid = get_param("mid");
	$oid = get_param("oid");
	$amount = get_param("amount");
	$currency = get_param("currency");
	$paytype = get_param("paytype");
	$msgtype = get_param("msgtype");
	$paydate = get_param("paydate");
	$buyer = get_param("buyer");
	$productinfo = get_param("productinfo");
	$buyerssn = get_param("buyerssn");
	$buyerid = get_param("buyerid");
	$buyeraddress = get_param("buyeraddress");
	$buyerphone = get_param("buyerphone");
	$buyeremail = get_param("buyeremail");
	$receiver = get_param("receiver");
	$receiverphone = get_param("receiverphone");
	$deliveryinfo = get_param("deliveryinfo");
	$producttype = get_param("producttype");
	$productcode = get_param("productcode");
	$financecode = get_param("financecode");
	$financename = get_param("financename");
	$useescrow = get_param("useescrow");
	$authnumber = get_param("authnumber");
	$cardnumber = get_param("cardnumber");
	$cardexp = get_param("cardexp");
	$cardperiod = get_param("cardperiod");
	$nointerestflag = get_param("nointerestflag");
	$transamount = get_param("transamount");
	$exchangerate = get_param("exchangerate");
	$pid = get_param("pid");
	$accountnumber = get_param("accountnumber");
	$accountowner = get_param("accountowner");
	$telno = get_param("telno");
	$payer = get_param("payer");
	$cflag = get_param("cflag");
	$tamount = get_param("tamount");
	$camount = get_param("camount");
	$bankdate = get_param("bankdate");
	$seqno= get_param("seqno");
	$receiptnumber= get_param("receiptnumber");


	$mertkey = $pg['mertkey']; //�����޿��� �߱��� ����Ű�� ������ �ֽñ� �ٶ��ϴ�.

	$hashdata2 = md5($transaction.$mid.$oid.$paydate.$mertkey);

	$value = array( "msgtype"		=> $msgtype,
					"transaction"	=> $transaction,
					"mid"			=> $mid,
					"oid"			=> $oid,
					"amount"		=> $amount,
					"currency"		=> $currency,
					"paytype"		=> $paytype,
					"paydate"		=> $paydate,
					"buyer"			=> $buyer,
					"productinfo"	=> $productinfo,
					"respcode"		=> $respcode,
					"respmsg"		=> $respmsg,
					"buyerssn"		=> $buyerssn,
					"buyerid"		=> $buyerid,
					"buyeraddress"	=> $buyeraddress,
					"buyerphone"	=> $buyerphone,
					"buyeremail"	=> $buyeremail,
					"receiver"		=> $receiver,
					"receiverphone"	=> $receiverphone,
					"deliveryinfo"	=> $deliveryinfo,
					"producttype"	=> $producttype,
					"productcode"	=> $productcode,
					"financecode"	=> $financecode,
					"financename"	=> $financename,
					"useescrow"		=> $useescrow,
					"authnumber"	=> $authnumber,
					"cardnumber"	=> $cardnumber,
					"cardexp"		=> $cardexp,
					"cardperiod"	=> $cardperiod,
					"nointerestflag"=> $nointerestflag,
					"transamount"	=> $transamount,
					"exchangerate"	=> $exchangerate,
					"pid"			=> $pid,
					"accountnumber"	=> $accountnumber,
					"accountowner"	=> $accountowner,
					"telno"			=> $telno,
					"payer"			=> $payer,
					"cflag"			=> $cflag,
					"tamount"		=> $tamount,
					"camount"		=> $camount,
					"bankdate"		=> $bankdate,
					"hashdata"		=> $hashdata,
					"hashdata2"		=> $hashdata2,
					"seqno"			=> $seqno,
					"receiptnumber"	=> $receiptnumber);

	### ���ں������� �߱�
	$eggs = get_param("eggs");
	if ($value[paytype] == 'SC0040' && $value[cflag] != 'R');
	else if (isset($eggs[o]) === true && $respcode == "0000" && $hashdata2 == $hashdata){
		if ($eggs[o] == $value[oid] && $eggs[r1] != '' && $eggs[r2] != '' && $eggs[a] == 'Y'){
			include '../../../lib/egg.class.usafe.php';
			$eggData = array('ordno' => $eggs[o], 'issue' => $eggs[i], 'resno1' => $eggs[r1], 'resno2' => $eggs[r2], 'agree' => $eggs[a]);
			switch ($value[paytype]){
				case "SC0010":
					$eggData[payInfo1] = $value[financename]; # (*) ��������(ī���)
					$eggData[payInfo2] = $value[authnumber]; # (*) ��������(���ι�ȣ)
					break;
				case "SC0030":
					$eggData[payInfo1] = $value[financename]; # (*) ��������(�����)
					$eggData[payInfo2] = $value[transaction]; # (*) ��������(���ι�ȣ or �ŷ���ȣ)
					break;
				case "SC0040":
					$eggData[payInfo1] = $value[financename]; # (*) ��������(�����)
					$eggData[payInfo2] = $value[accountnumber]; # (*) ��������(���¹�ȣ)
					break;
			}
			$eggCls = new Egg( 'create', $eggData );
			if ( $eggCls->isErr == true && $value[paytype] == "SC0040" ){
				$respcode = '';
			}
			else if ( $eggCls->isErr == true && in_array($value[paytype], array("SC0010","SC0030")) );
		}
	}

	if ($hashdata2 == $hashdata) { //�ؽ��� ������ �����ϸ�
		$ordno = $value['oid'];

		### ������� ������ ��� üũ �ܰ� ����
		$res_cstock = true;
		if($value['paytype'] == 'SC0040' && $cfg['stepStock'] == '0' && $value['cflag'] != 'R') $res_cstock = false;
		if($value['paytype'] == 'SC0040' && $cfg['stepStock'] == '1' && $value['cflag'] != 'I') $res_cstock = false;

		### item check stock
		include "../../../lib/cardCancel.class.php";
		$cancel = new cardCancel();
		if(!$cancel->chk_item_stock($ordno) && $res_cstock){
			$respcode = "OUTOFSTOCK";
			$cancel->cancel_db_proc($ordno,$transaction);
		}else{
			$oData = $db->fetch("select step, vAccount from ".GD_ORDER." where ordno='$ordno'");
			if($oData['step'] > 0 || ($oData['vAccount'] != '' && $value['cflag'] != 'I') || $respcode == 'S007'){ //������ �ߺ������ϸ�
				$resp = write_overlap($value);
			}else if($respcode == "0000"){ //������ �����̸�
				### Ncash �ŷ� Ȯ�� API
				include "../../../lib/naverNcash.class.php";
				$naverNcash = new naverNcash();
				$naverNcash->deal_done($ordno);
				$resp = write_success($value);
			}else { //������ �����̸�
				$resp = write_failure($value);
			}
		}
	} else { //�ؽ��� ������ �����̸�
		write_hasherr($value);
	}

	if($respcode == "OUTOFSTOCK"){
		echo "ROLLBACK";
	}else if($resp){ //��������� �����̸�
		echo "OK";
	}else{ //��������� �����̸�
		echo "FAIL";
	}
?>
