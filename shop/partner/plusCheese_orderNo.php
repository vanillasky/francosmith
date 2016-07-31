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

	$fp = fopen("../log/pcheese.txt", "w");
	fwrite($fp, "==POST==\n");
	foreach($_POST as $k => $v){
		fwrite($fp, $k." => ".$v."\n");
	}
	fwrite($fp, "==GET==\n");
	foreach($_GET as $k => $v){
		fwrite($fp, $k." => ".$v."\n");
	}
	chmod("../log/pcheese.txt", 0755);

	if(empty($_POST['XMLData'])){
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

	$insertData['nameOrder'] = $plusCheese->findKey($data, "ORDERNAME", true); //구매자명
	$insertData['nameOrder'] = $insertData['nameOrder'][0];

	$insertData['email'] = $plusCheese->findKey($data, "ORDEREMAIL", true); //구매자이메일
	$insertData['email'] = $insertData['email'][0];

	$insertData['phoneOrder'] = $plusCheese->findKey($data, "ORDERTEL", true); //구매자 전화
	$insertData['phoneOrder'] = $insertData['phoneOrder'][0];

	$insertData['mobileOrder'] = $plusCheese->findKey($data, "ORDERTEL", true); //구매자 전화
	$insertData['mobileOrder'] = $insertData['mobileOrder'][0];

	$insertData['nameReceiver'] = $plusCheese->findKey($data, "RECEIVERNAME", true); //수령자 이름
	$insertData['nameReceiver'] = $insertData['nameReceiver'][0];

	$insertData['phoneReceiver'] = $plusCheese->findKey($data, "RECEIVERTEL", true); //수령자 전화
	$insertData['phoneReceiver'] = $insertData['phoneReceiver'][0];

	$insertData['mobileReceiver'] = $plusCheese->findKey($data, "RECEIVERTEL", true); //수령자 전화
	$insertData['mobileReceiver'] = $insertData['mobileReceiver'][0];

	$insertData['zipcode'] = $plusCheese->findKey($data, "RECEIVERPOST", true); //수령자 우편번호
	$insertData['zipcode'] = $insertData['zipcode'][0];

	$insertData['address'] = $plusCheese->findKey($data, "RECEIVERADDR1", true); //수령자 주소1
	$insertData['address'] = $insertData['address'][0];

	$insertData['address_sub'] = $plusCheese->findKey($data, "RECEIVERADDR2", true); //수령자 주소2
	$insertData['address_sub'] = $insertData['address_sub'][0];

	$insertData['settlekind'] = $plusCheese->findKey($data, "PAYMENTMETHOD", true); //결제방식
	$insertData['settlekind'] = $insertData['settlekind'][0];

	$insertData['settleprice'] = $plusCheese->findKey($data, "PAYMENTAMOUNT", true); //결제금액
	$insertData['settleprice'] = $insertData['settleprice'][0];

	$insertData['prn_settleprice'] = $plusCheese->findKey($data, "PAYMENTAMOUNT", true); //결제금액
	$insertData['prn_settleprice'] = $insertData['prn_settleprice'][0];

	$insertData['goodsprice'] = $plusCheese->findKey($data, "PAYMENTAMOUNT", true); //결제금액
	$insertData['goodsprice'] = $insertData['goodsprice'][0];

	$insertData['delivery'] = $plusCheese->findKey($data, "DELIVERYCHARGE", true); //배송비
	$insertData['delivery'] = $insertData['delivery'][0];

	$insertData['deli_type'] = $plusCheese->findKey($data, "DELIVERYCOLLECTYN", true); //착불여부
	$insertData['deli_type'] = $insertData['deli_type'][0];

	$insertData['deli_msg'] = $plusCheese->findKey($data, "ORDERDEMAND", true); //요구사항
	$insertData['deli_msg'] = $insertData['deli_msg'][0];

	$insertData['deli_title'] = "플러스 치즈 배송"; //배송방법


	$insertData['goodsno'] = $plusCheese->findKey($data, "PRDCODE", true); //상품코드
	$insertData['goodsno'] = $insertData['goodsno'][0];

	$insertData['goodsnm'] = $plusCheese->findKey($data, "PRDNAME", true); //상품명
	$insertData['goodsnm'] = $insertData['goodsnm'][0];

	$insertData['opt1'] = $plusCheese->findKey($data, "OPTIONCODE", true); //옵션
	$insertData['opt1'] = $insertData['opt1'][0];
	$opt = $db->fetch("SELECT * FROM ".GD_GOODS_OPTION." WHERE optno='".$insertData['opt1']."'");
	$insertData['opt1'] = $opt['opt1'];
	$insertData['opt2'] = $opt['opt2'];

	$insertData['price'] = $plusCheese->findKey($data, "PAYMENTAMOUNT", true); //상품가격
	$insertData['price'] = $insertData['price'][0];

	$insertData['supply'] = $opt['supply']; //공급가

	$insertData['ea'] = $plusCheese->findKey($data, "ORDERCOUNT", true); //구매수량
	$insertData['ea'] = $insertData['ea'][0];

	$insertData['pCheeseOrdNo'] = $plusCheese->findKey($data, "TEMPORDERSEQ", true); //구매수량
	$insertData['pCheeseOrdNo'] = $insertData['pCheeseOrdNo'][0];


	$corder = $plusCheese->findKey($data, "TEMPORDERNO", true); //플러스치즈 주문번호
	$corder = $corder[0];

	### 제조사, 브랜드
	list($maker, $brandnm, $tax, $delivery_type, $goods_delivery, $usestock) = $db->fetch("select maker, brandnm, tax, delivery_type, goods_delivery, usestock from ".GD_GOODS." left join ".GD_GOODS_BRAND." on brandno=sno where goodsno='".$insertData['goodsno']."'");
	$maker = addslashes($maker);
	$brandnm = addslashes($brandnm);
	$item_deli_msgi = "";
	if($delivery_type == 3){
		$item_deli_msgi = "착불";
		if($goods_delivery) $item_deli_msgi .= " ".number_format($goods_delivery)." 원";
	}
	if($usestock == 'o') $stockable = "y";
	else $stockable = "n";

	$insertData['item_deli_msgi'] = $item_deli_msgi; //제조사

	$insertData['maker'] = $maker; //제조사

	$insertData['brandnm'] = $brandnm; //브랜드명

	$insertData['tax'] = $brandnm; //과세

	$insertData['tax'] = $brandnm; //과세

	$insertData['stockable'] = $stockable; //재고

	$ordno = getordno();

	### 주문정보 저장
	$err = "000";
	$query = "
	insert into ".GD_ORDER." set
		step			= 0,
		step2			= 0,
		ordno			= '".$ordno."',
		nameOrder		= '".$insertData['nameOrder']."',
		email			= '".$insertData['email']."',
		phoneOrder		= '".$insertData['phoneOrder']."',
		mobileOrder		= '".$insertData['mobileOrder']."',
		nameReceiver	= '".$insertData['nameReceiver']."',
		phoneReceiver	= '".$insertData['phoneReceiver']."',
		mobileReceiver	= '".$insertData['mobileReceiver']."',
		zipcode			= '".$insertData['zipcode']."',
		address			= '".$insertData['address']." ".$insertData['address_sub']."',
		settlekind		= '".$insertData['settlekind']."',
		settleprice		= '".$insertData['settleprice']."',
		prn_settleprice	= '".$insertData['prn_settleprice']."',
		goodsprice		= '".$insertData['goodsprice']."',
		deli_title		= '".$insertData['deli_title']."',
		delivery		= '".$insertData['delivery']."',
		deli_type		= '".$insertData['deli_type']."',
		deli_msg		= '".$insertData['deli_msg']."',
		orddt			= now(),
		pCheeseOrdNo	= '".$corder."'
	";
	if(!$db->query($query))$err="001";

	$query = "
	insert into ".GD_ORDER_ITEM." set $qrTmp2
		ordno			= '$ordno',
		goodsno			= '".$insertData['goodsno']."',
		goodsnm			= '".$insertData['goodsnm']."',
		opt1			= '".$insertData['opt1']."',
		opt2			= '".$insertData['opt2']."',
		addopt			= '',
		price			= '".$insertData['price']."',
		supply			= '".$insertData['supply']."',
		reserve			= '0',
		memberdc		= '0',
		ea				= '".$insertData['ea']."',
		maker			= '".$insertData['maker']."',
		brandnm			= '".$insertData['brandnm']."',
		tax				= '".$insertData['tax']."',
		deli_msg		= '".$insertData['item_deli_msgi']."',
		stockable		= '".$insertData['stockable']."',
		pCheeseOrdNo	= '".$insertData['pCheeseOrdNo']."'
	";
	if(!$db->query($query))$err="001";
$xmlPrint .= "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
$xmlPrint .= "<orderNoResponse>\n";
$xmlPrint .= "	<responseCode>000</responseCode>\n";
$xmlPrint .= "	<responseMsg>성공</responseMsg>\n";
$xmlPrint .= "	<tempOrderNo>".$corder."</tempOrderNo>\n";
$xmlPrint .= "	<orderNo>".$ordno."</orderNo>\n";
$xmlPrint .= "</orderNoResponse>\n";

echo $plusCheese->encrypt($xmlPrint);
?>