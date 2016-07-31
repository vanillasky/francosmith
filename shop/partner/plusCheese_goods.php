<?
	include "../lib/library.php";
	@include "../conf/config.plusCheeseCfg.php"; //플러스치즈
	@include "../conf/config.pay.php"; //플러스치즈
	require "../lib/plusCheese.class.php";
	require "../lib/goods.class.php";

	if(!$_POST['prdCode'])	$_POST['prdCode'] = $_GET['prdCode'];
	if(!$_POST['entID'])	$_POST['entID'] = $_GET['entID'];

	if(!empty($_SERVER['HTTPS'])){
		$site_url = "https://";
	}else{
		$site_url = "http://";
	}

	$site_url .= $config->_loaded['config']['shopUrl'];

	$goods = new Goods();
	$goodsData = $goods->get_goods($_POST['prdCode']);
	$goodsCategoryData = $goods->get_goods_category($_POST['prdCode']);
	$goodsData = $goodsData[0];

	$plusCheese = new plusCheese($godo['sno']);
	if(strtoupper($plusCheeseCfg['use']) != "Y" || strtoupper($plusCheese->getStatusCond()) != "Y"){
		exit;
	}
	//플러스치즈 예외상품 여부 체크
	if($plusCheese->except_goods($_POST['prdCode']) || ($sess['level'] >= 80 && $plusCheeseCfg['test'] == "Y")){
		exit;
	}
	$plusCheeseKey = $plusCheese->getRelayKey();

	//가격정보
	$query = "select * from ".GD_GOODS_OPTION." where goodsno='".$_POST['prdCode']."' AND link=1";	
	$data = $db->fetch($query);
	$price = $data['price'];
	$consumer = $data['consumer'];
	$supply = $data['supply'];

	//브랜드 목록
	$query = "SELECT * FROM ".GD_GOODS_BRAND."";
	$res = $db->query($query);
	while($row = $db->fetch($res)){
		$brand[$row['sno']] = $row['brandnm'];
	}

	//배송비 계산
	//기본배송
	$delivery['deliverynm']		= $set['delivery']['deliverynm']; //배송명
	$delivery['free']			= $set['delivery']['free']; //무료배송금액
	$delivery['deliveryType']	= $set['delivery']['deliveryType']; //배송비 선후불여부
	$delivery['default']		= $set['delivery']['default']; //배송비

	//옵션목록
	$options = explode("|", $goodsData['optnm']);
	$query = "select * from ".GD_GOODS_OPTION." where goodsno='".$_POST['prdCode']."'";
	$res = $db->query($query);
	while($row = $db->fetch($res)){
		if(!empty($options[0])){
			$option['optno'][] = $row['optno']; //옵션 일련번호
			$option['optnm'][] = $options[0].":".$options[1]; //옵션명
			$option['optvl'][] = $row['opt1'].":".$row['opt2']; //옵션값
			$option['optpr'][] = $row['price']; //옵션 가격
			if($goodsData['runout'] == "1"){
				$option['stock'][] = 0;
			}else if($goodsData['usestock'] == "o"){ //재고를 사용하면
				$option['stock'][] = $row['stock']; //옵션 재고
			}else if($goodsData['usestock'] != "o"){ //무한정 판매라면
				$option['stock'][] = 9999;
			}
		}
	}

	//이용후기
	$query = "SELECT * FROM ".GD_GOODS_REVIEW." WHERE goodsno='".$_POST['prdCode']."' ORDER BY regdt DESC LIMIT 5";
	$res = $db->query($query);
	while($row = $db->fetch($res)){
		$review['score'][]		= $row['point'];
		$review['name'][]		= $row['name'];
		$review['contents'][]	= $row['contents'];
		$review['regdt'][]		= str_replace("-", "", substr($row['regdt'], 0, 10));
	}

$xmlPrint .= "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
$xmlPrint .= "<productInfo>\n";
$xmlPrint .= "	<entID>".$_POST['entID']."</entID>\n";
$xmlPrint .= "	<contentsClass>COCL000001</contentsClass>\n";
$xmlPrint .= "	<prdCode>".$plusCheese->toUTF8($goodsData['goodsno'])."</prdCode>\n";
$xmlPrint .= "	<prdNo>".$plusCheese->toUTF8($goodsData['goodscd'])."</prdNo>\n";
$xmlPrint .= "	<prdCategory><![CDATA[".$plusCheese->toUTF8(htmlspecialchars(strip_tags(currPosition($goodsCategoryData[0]))))."]]></prdCategory>\n";
$xmlPrint .= "	<prdSearchWord>".$plusCheese->toUTF8($goodsData['keyword'])."</prdSearchWord>\n";
$xmlPrint .= "	<prdName><![CDATA[".$plusCheese->toUTF8($goodsData['goodsnm'])."]]></prdName>\n";
$xmlPrint .= "	<etc>\n";
$xmlPrint .= "		<title>제조사</title>\n";
$xmlPrint .= "		<value>";
						$tmp = $plusCheese->toUTF8($goodsData['maker']);
						if(empty($tmp))	$xmlPrint .= "-";
						else			$xmlPrint .= $tmp;
$xmlPrint .= "</value>\n";
$xmlPrint .= "	</etc>\n";
$xmlPrint .= "	<etc>\n";
$xmlPrint .= "		<title>원산지</title>\n";
$xmlPrint .= "		<value>";
						$tmp = $plusCheese->toUTF8($goodsData['origin']);
						if(empty($tmp))	$xmlPrint .= "-";
						else			$xmlPrint .= $tmp;
$xmlPrint .= "</value>\n";
$xmlPrint .= "	</etc>\n";
$xmlPrint .= "	<etc>\n";
$xmlPrint .= "		<title>브랜드</title>\n";
$xmlPrint .= "		<value>";
						$tmp = $plusCheese->toUTF8($brand[$goodsData['brandno']]);
						if(empty($tmp))	$xmlPrint .= "-";
						else			$xmlPrint .= $tmp;
$xmlPrint .= "</value>\n";
$xmlPrint .= "	</etc>\n";
$xmlPrint .= "	<marketPrice>".$plusCheese->toUTF8($consumer)."</marketPrice>\n";
$xmlPrint .= "	<price>".$plusCheese->toUTF8($price)."</price>\n";
$xmlPrint .= "	<totalStock>";
						if($goodsData['runout'] == "1"){ //품절이라면
							$xmlPrint .= "0";
						}else if($goodsData['usestock'] == "o"){ //재고를 사용하면
							$xmlPrint .= $goodsData['totstock'];
						}else if($goodsData['usestock'] != "o"){ //무한정 판매라면
							$xmlPrint .= "9999";
		 		}
$xmlPrint .= "</totalStock>\n";
$pc_com = $plusCheese->data['pc_commission'];
$xmlPrint .= "	<partnerCommission>".$pc_com."</partnerCommission>\n";
$xmlPrint .= "	<deliveryPaymentType>";
				if($goodsData['goods_delivery'] == "0" || empty($goodsData['goods_delivery'])){ //기본 배송이라면
					if(strcmp($plusCheese->toUTF8($delivery['deliveryType']), "후불") == 0){
						$xmlPrint .= "C";
					}else{
						$xmlPrint .= "P";
					}
				}else if($goodsData['goods_delivery'] == "1" || $goodsDAta['goods_delivery'] == "2" || $goodsDAta['goods_delivery'] == "4" || $goodsDAta['goods_delivery'] == "5"){ //선불
					$xmlPrint .= "P";
				}else if($goodsData['goods_delivery'] == "3"){ //착불
					$xmlPrint .= "C";
				}
$xmlPrint .= "</deliveryPaymentType>\n";
$xmlPrint .= "	<deliveryChargeAdjustAmount>".$delivery['free']."</deliveryChargeAdjustAmount>\n";
$xmlPrint .= "	<deliveryCharge>".$delivery['default']."</deliveryCharge>\n";
$xmlPrint .= "	<deliveryChargeInfo><![CDATA[".number_format($delivery['free'])."원 이상 구매시 무료배송]]></deliveryChargeInfo>\n";
$xmlPrint .= "	<prdLinkURL>".$site_url."/shop/goods/goods_view.php?goodsno=".$_POST['prdCode']."</prdLinkURL>\n";
$xmlPrint .= "	<prdImage>\n";
				$tmpEach = explode("|", $goodsData['img_m']);
				foreach($tmpEach as $v){
$xmlPrint .= "		<prdImageURL>";
					if(!empty($v)){
$xmlPrint .= $plusCheese->toUTF8($site_url)."/shop/data/goods/".$plusCheese->toUTF8($v);
}
$xmlPrint .=			"</prdImageURL>\n";
					}
					$tmpEach = explode("|", $goodsData['img_m']);
$xmlPrint .= "		<prdMImageURL>";
					if(!empty($tmpEach[0])){
$xmlPrint .=			$site_url."/shop/data/goods/".$tmpEach[0];
					}
$xmlPrint .=			"</prdMImageURL>\n";
					$tmpEach = explode("|", $goodsData['img_s']);
$xmlPrint .= "		<prdSImageURL>";
					if(!empty($tmpEach[0])){
$xmlPrint .= 		$site_url."/shop/data/goods/".$tmpEach[0];
					}
$xmlPrint .=			"</prdSImageURL>\n";
$xmlPrint .= "	</prdImage>\n";
					for($i=0;$i<count($option['optno']);$i++){
$xmlPrint .= "	<option>\n";
$xmlPrint .= "		<code>".$plusCheese->toUTF8($option['optno'][$i])."</code>\n";
$xmlPrint .= "		<category>".$plusCheese->toUTF8($option['optnm'][$i])."</category>\n";
$xmlPrint .= "		<title>".$plusCheese->toUTF8($option['optvl'][$i])."</title>\n";
$xmlPrint .= "		<price>".$plusCheese->toUTF8($option['optpr'][$i] - $price)."</price>\n";
$xmlPrint .= "		<stock>".$plusCheese->toUTF8($option['stock'][$i])."</stock>\n";
$xmlPrint .= "	</option>\n";
					};
$xmlPrint .= "	<detailHTML><![CDATA[".$plusCheese->toUTF8(str_replace("/shop/lib/meditor/../../", "http://".$cfg['shopUrl']."/shop/", $goodsData['longdesc']))."]]></detailHTML>\n";
			for($i=0;$i<count($review['score']);$i++){
$xmlPrint .= "	<valuation>\n";
$xmlPrint .= "		<score>".$plusCheese->toUTF8($review['score'][$i])."</score>\n";
$xmlPrint .= "		<writer>".$plusCheese->toUTF8($review['name'][$i])."</writer>\n";
$xmlPrint .= "		<description><![CDATA[".$plusCheese->toUTF8($review['contents'][$i])."]]></description>\n";
$xmlPrint .= "		<regDate>".$plusCheese->toUTF8($review['regdt'][$i])."</regDate>\n";
$xmlPrint .= "	</valuation>\n";
			}
$xmlPrint .= "	<regDate>".$plusCheese->dateFormatToXML($goodsData['regdt'])."</regDate>\n";
$xmlPrint .= "	<updateDate>".$plusCheese->dateFormatToXML($goodsData['updatedt'])."</updateDate>\n";
$xmlPrint .= "</productInfo>\n";

echo $plusCheese->encrypt($xmlPrint);
?>
