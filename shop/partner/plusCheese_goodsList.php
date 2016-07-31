<?
	include "../lib/library.php";
	@include "../conf/config.plusCheeseCfg.php"; //플러스치즈
	require "../lib/plusCheese.class.php";
	@include "../conf/config.pay.php";

	$plusCheese = new plusCheese($godo['sno']);
	if(strtoupper($plusCheeseCfg['use']) != "Y" || strtoupper($plusCheese->getStatusCond()) != "Y"){
		exit;
	}
	$plusCheeseKey = $plusCheese->getRelayKey();

	//날짜
	$startDt = $_POST['updateDate'];
	if(empty($startDt)){
		$startDt = $_GET['updateDate'];
	}
	if($startDt == ""){
		$startDt = "1970-01-01";
	}else{
		$startDt = $plusCheese->dateFormatFromGET($startDt);
	}
	
	$delivery['deliverynm']		= $set['delivery']['deliverynm']; //배송명
	$delivery['free']			= $set['delivery']['free']; //무료배송금액
	$delivery['deliveryType']	= $set['delivery']['deliveryType']; //배송비 선후불여부
	$delivery['default']		= $set['delivery']['default']; //배송비
	
	if(!empty($_SERVER['HTTPS'])){
		$site_url = "https://";
	}else{
		$site_url = "http://";
	}
	$site_url .= $config->_loaded['config']['shopUrl'];
$xml .= "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
$xml .="<searchProductInfo>\n";
	$queryGoodsList = "SELECT * FROM ".GD_GOODS." g, ".GD_GOODS_OPTION." go WHERE g.open=1 AND go.link=1 AND go.goodsno = g.goodsno AND g.updatedt >= '".$startDt." 00:00:00'";
	$resGoosList = $db->query($queryGoodsList);
	while($rowGoodsList = $db->fetch($resGoosList)){
		//카테고리정보 가져오기
		$queryGoodsList = "SELECT * FROM ".GD_GOODS_LINK." gl, ".GD_CATEGORY." c WHERE goodsno='".$rowGoodsList['goodsno']."' AND gl.category = c.category ORDER BY gl.sort DESC LIMIT 1";
		$rowGoosList = $db->fetch($queryGoodsList);
		
		$categoryCd = $rowGoosList['category']; //카테고리코드
		$categoryNm = $rowGoosList['catnm']; //카테고리명

		//배송비 계산
		if($rowGoodsList['price'] >= $delivery['free']){
			$deliveryPrice = 0;
		}else{
			$deliveryPrice = $delivery['default'];
		}
		
		//상품이미지 한개씩만
		$img300 = explode("|", $rowGoodsList['img_m']);
		$img100 = explode("|", $rowGoodsList['img_m']);
		$img70 = explode("|", $rowGoodsList['img_s']);
		
		$img300[0] = $plusCheese->toUTF8($img300[0]);
		$img100[0] = $plusCheese->toUTF8($img100[0]);
		$img70[0] = $plusCheese->toUTF8($img70[0]);
		
$xml .="	<productInfo>\n";
$xml .="		<entID>godo1</entID>\n";
$xml .="		<prdCode>".$rowGoodsList['goodsno']."</prdCode>\n";
$xml .="		<contentsClass>"."COCL000001"."</contentsClass>\n";
$xml .="		<prdNo>".$rowGoodsList['goodsno']."</prdNo>\n";
$xml .="		<prdCategory><![CDATA[".$plusCheese->toUTF8(htmlspecialchars(strip_tags(currPosition($categoryCd))))."]]></prdCategory>\n";
$xml .="		<prdSearchWord>".$plusCheese->toUTF8($rowGoodsList['keyword'])."</prdSearchWord>\n";
$xml .="		<prdName><![CDATA[".$plusCheese->toUTF8($rowGoodsList['goodsnm'])."]]></prdName>\n";
$xml .="		<marketPrice>".$rowGoodsList['consumer']."</marketPrice>\n";
$xml .="		<price>".$rowGoodsList['price']."</price>\n";
$pc_com = $plusCheese->data['pc_commission'];
$xml .="		<partnerCommission>".$pc_com."</partnerCommission>\n";
$xml .="		<deliveryChargeAdjustAmount>".($rowGoodsList['price']+$deliveryPrice."")."</deliveryChargeAdjustAmount>\n";
$xml .="		<deliveryCharge>".$deliveryPrice."</deliveryCharge>\n";
$xml .="		<prdLinkURL>".$site_url."/shop/goods/goods_view.php?goodsno=".$rowGoodsList['goodsno']."</prdLinkURL>\n";
$xml .="		<prdImage>\n";
$xml .="			<prdImageURL>";
if(!empty($img300[0])){
$xml .=				$site_url."/shop/data/goods/".$img300[0];
}
$xml .="</prdImageURL>\n";
$xml .="			<prdMImageURL>";
if(!empty($img100[0])){
$xml .=				$site_url."/shop/data/goods/".$img100[0];
}
$xml .="</prdMImageURL>\n";
$xml .="			<prdSImageURL>";
if(!empty($img70[0])){
$xml .= 			$site_url."/shop/data/goods/".$img70[0];
}
$xml .="</prdSImageURL>\n";
$xml .="		</prdImage>\n";
$xml .="		<regDate>".$plusCheese->dateFormatToXML($rowGoodsList['regdt'])."</regDate>\n";
$xml .="		<updateDate>".$plusCheese->dateFormatToXML($rowGoodsList['updatedt'])."</updateDate>\n";
$xml .="	</productInfo>\n";
	}
$xml .="</searchProductInfo>";

echo $plusCheese->encrypt($xml);
?>