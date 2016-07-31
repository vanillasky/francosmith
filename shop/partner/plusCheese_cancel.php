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

	$updateData['pCheeseOrdNo'] = $plusCheese->findKey($data, "TEMPORDERNO", true); //플러스치즈 주문번호
	$updateData['pCheeseOrdNo'] = $updateData['pCheeseOrdNo'][0];

	$updateData['pCheeseOrdSeq'] = $plusCheese->findKey($data, "TEMPORDERSEQ", true); //플러스치즈 주문일련번호
	$updateData['pCheeseOrdSeq'] = $updateData['pCheeseOrdSeq'][0];

	$updateData['ea'] = $plusCheese->findKey($data, "ORDERCANCELCOUNT", true); //주문취소 수량
	$updateData['ea'] = $updateData['ea'][0];

	$updateData['step'] = $plusCheese->findKey($data, "ORDERCONDITION", true); //결제금액
	$updateData['step'] = $updateData['step'][0];

	$updateData['updatedt'] = $plusCheese->findKey($data, "CHANGEDATE", true); //결제금액
	$updateData['updatedt'] = $updateData['updatedt'][0];

	//주문서 업데이트
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

	### 주문정보 저장
	$query = "
	UPDATE ".GD_ORDER." set
		step			= 1,
		step2			= 40,
		ordno			= '".$ordno."',
		settlekind		= '".$updateData['settlekind']."',
		cdt				= '".$updateData['cdt']."'
	WHERE
		pCheeseOrdNo	= '".$updateData['pCheeseOrdNo']."'
	";
	$db->query($query);

	$query = "
	UPDATE ".GD_ORDER_ITEM." set
		istep			= '40'
	WHERE
		ordno			= '$ordno'
	";
	$db->query($query);
$xmlPrint .= "<orderCondResponse>\n";
$xmlPrint .= "	<tempOrderNo>".$updateData['pCheeseOrdNo']."</tempOrderNo>\n";
$xmlPrint .= "	<tempOrderSeq>".$updateData['pCheeseOrdSeq']."</tempOrderSeq>\n";
$xmlPrint .= "	<responseCode>000</responseCode>\n";
$xmlPrint .= "	<responseMsg>성공</responseMsg>\n";
$xmlPrint .= "</orderCondResponse>\n";

echo $plusCheese->encrypt($xmlPrint);
?>
