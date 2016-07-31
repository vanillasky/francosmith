<?
	require "../lib/library.php";
	require "../lib/goods.class.php";
	require "../lib/parsexml.class.php";
	require "../lib/plusCheese.class.php";
	@include "../conf/config.plusCheeseCfg.php";

	$plusCheese = new plusCheese($godo['sno']);
	if(strtoupper($plusCheeseCfg['use']) != "Y" || strtoupper($plusCheese->getStatusCond()) != "Y"){
		exit;
	}
	
	if(!$_POST['XMLData']){
		$_POST['XMLData'] = $_GET['XMLData'];
	}
	$buffer = str_replace(" ", "+", ($_POST['XMLData']));

	$buffer = $plusCheese->decrypt($buffer);
	$xml = new XMLParser();
	$xml->parse($buffer);
	$data = $xml->parseOut();
	for($i=0;$i<count($data);$i++){
		foreach($data[$i] as $k => $v){
			if($k == "val"){
				$data[$i][$k] = $plusCheese->toEUCKR($v);
			}
		}
	}
	
	$updateData['pCheeseOrdNo'] = $plusCheese->findKey($data, "TEMPORDERNO", true); //�÷���ġ�� �ֹ���ȣ
	$updateData['pCheeseOrdNo'] = $updateData['pCheeseOrdNo'][0];
	
	$updateData['settleKind'] = $plusCheese->findKey($data, "PAYMENTTYPE", true); //�������
	$updateData['settleKind'] = $updateData['settleKind'][0];
	
	if($updateData['settleKind'] = "C_CARD"){			//�ſ�ī��
		$updateData['settleKind'] = "c";
	}else if($updateData['settleKind'] = "PHONE"){		//�޴���
		$updateData['settleKind'] = "h";
	}else if($updateData['settleKind'] = "C_TRANSFER"){	//�������Ա�
		$updateData['settleKind'] = "a";
	}else if($updateData['settleKind'] = "POINT"){		//����Ʈ
		$updateData['settleKind'] = "a";
	}else if($updateData['settleKind'] = "R_TRANSFER"){	//�ǽð� ������ü
		$updateData['settleKind'] = "o";
	}
	
	$updateData['settleprice'] = $plusCheese->findKey($data, "PAYMENTAMOUNT", true); //�����ݾ�
	$updateData['settleprice'] = $updateData['settleprice'][0];
	
	$updateData['prn_settleprice'] = $plusCheese->findKey($data, "PAYMENTAMOUNT", true); //�����ݾ�
	$updateData['prn_settleprice'] = $updateData['prn_settleprice'][0];
	
	$updateData['goodsprice'] = $plusCheese->findKey($data, "PAYMENTAMOUNT", true); //�����ݾ�
	$updateData['goodsprice'] = $updateData['goodsprice'][0];
	
	$updateData['cdt'] = $plusCheese->findKey($data, "PAYMENTDATE", true); //���� Ȯ�γ�¥
	$updateData['cdt'] = $updateData['mobileOrder'][0];

	//�ֹ��� ������Ʈ
	$query = "
	SELECT
		*
	FROM
		".GD_ORDER."
	WHERE
		pCheeseOrdNo	= '".$updateData['pCheeseOrdNo']."'
	";
	$order = $db->fetch($query);
	$ordno = $order['ordno'];
	
	### �ֹ����� ����
	$query = "
	UPDATE ".GD_ORDER." set
		step			= 1,
		step2			= 0,
		ordno			= '".$ordno."',
		settlekind		= '".$updateData['settlekind']."',
		cdt				= '".$updateData['cdt']."'
	WHERE
		pCheeseOrdNo	= '".$updateData['pCheeseOrdNo']."'
	";
	$db->query($query);

	$query = "
	UPDATE ".GD_ORDER_ITEM." set
		istep			= '1'
	WHERE
		ordno			= '$ordno'
	";
	$db->query($query);
$xml .= "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
$xml .= "<orderNoResponse>\n";
$xml .= "	<responseCode>000</responseCode>\n";
$xml .= "	<responseMsg>000</responseMsg>\n";
$xml .= "	<tempOrderNo>".$updateData['pCheeseOrdNo']."</tempOrderNo>\n";
$xml .= "	<orderNo>".$ordno."</orderNo>\n";
$xml .= "</orderNoResponse>\n";
?>
