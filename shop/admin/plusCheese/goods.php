<?
	include "../../lib/library.php";
	@include "../../conf/config.plusCheeseCfg.php"; //플러스치즈
	@include "../../conf/config.pay.php"; //플러스치즈
	require "../../lib/plusCheese.class.php";
	require "../../lib/goods.class.php";

	if(!empty($_SERVER['HTTPS'])){
		$site_url = "https://";
	}else{
		$site_url = "http://";
	}
	$site_url .= $_SERVER['HTTP_HOST'];

	$goods = new Goods();
	$goodsData = $goods->get_goods($_GET['prdCode']);
	$goodsCategoryData = $goods->get_goods_category($_GET['prdCode']);
	$goodsData = $goodsData[0];

	$plusCheese = new plusCheese($godo['sno']);
	$plusCheeseKey = $plusCheese->getRelayKey();

	echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";

	//가격정보
	$query = "select * from ".GD_GOODS_OPTION." where goodsno='".$_GET['prdCode']."' AND link=1";	
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
	$query = "select * from ".GD_GOODS_OPTION." where goodsno='".$_GET['prdCode']."'";
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
	$query = "SELECT * FROM ".GD_GOODS_REVIEW." WHERE goodsno='".$_GET['prdCode']."' ORDER BY regdt DESC LIMIT 5";
	$res = $db->query($query);
	while($row = $db->fetch($res)){
		$review['score'][]		= $row['point'];
		$review['name'][]		= $row['name'];
		$review['contents'][]	= $row['contents'];
		$review['regdt'][]		= str_replace("-", "", substr($row['regdt'], 0, 10));
	}
?>
<productInfo>
	<entID><?=$_GET['entID']?></entID>
	<contentsClass>COCL000001</contentsClass>
	<prdCode><?=iconv("CP949", "UTF-8", $goodsData['goodsno'])?></prdCode>
	<prdNo><?=iconv("CP949", "UTF-8", $goodsData['goodscd'])?></prdNo>
	<prdCategory><![CDATA[ <?=iconv("CP949", "UTF-8", strip_tags((currPosition("001001"))))?> ]]> </prdCategory>
	<prdSearchWord><?=iconv("CP949", "UTF-8", $goodsData['keyword'])?></prdSearchWord>
	<prdName><![CDATA[<?=iconv("CP949", "UTF-8", $goodsData['goodsnm'])?>]]></prdName>
	<etc>
		<title>제조사</title>
		<value><?=iconv("CP949", "UTF-8", $goodsData['maker'])?></value>
	</etc>
	<etc>
		<title>원산지</title>
		<value><?=iconv("CP949", "UTF-8", $goodsData['origin'])?></value>
	</etc>
	<etc>
		<title>브랜드</title>
		<value><?
			echo iconv("CP949", "UTF-8", $brand[$goodsData['brandno']]);
		?></value>
	</etc>
	<marketPrice><?=iconv("CP949", "UTF-8", $consumer)?></marketPrice>
	<price><?=iconv("CP949", "UTF-8", $price)?></price>
	<totalStock><?
		if($goodsData['runout'] == "1"){ //품절이라면
			echo "0";
		}else if($goodsData['usestock'] == "o"){ //재고를 사용하면
			echo $goodsData['totstock'];
		}else if($goodsData['usestock'] != "o"){ //무한정 판매라면
			echo "9999";
		}
		?></totalStock>
	<partnerCommission>0</partnerCommission>
	<deliveryPaymentType><?
		if($goodsData['goods_delivery'] == "0"){ //기본 배송이라면
			echo "C";
		}else if($goodsData['goods_delivery'] == "1" || $goodsDAta['goods_delivery'] == "2" || $goodsDAta['goods_delivery'] == "4" || $goodsDAta['goods_delivery'] == "5"){ //선불
			echo "C";
		}else if($goodsData['goods_delivery'] == "3"){ //착불
			echo "C";
		}
		?></deliveryPaymentType>
	<deliveryChargeAdjustAmount><?=$delivery['free']?></deliveryChargeAdjustAmount>
	<deliveryCharge><?=$delivery['free']?></deliveryCharge>
	<deliveryChargeInfo><![CDATA[]]></deliveryChargeInfo>
	<prdLinkURL><?=$site_url?>/shop/goods/goods_view.php?goodsno=<?=$_GET['prdCode']?></prdLinkURL>
	<prdImage>
		<?
		$tmpEach = explode("|", $goodsData['img_m']);
		foreach($tmpEach as $v){
			?><prdImageURL><?=iconv("CP949", "UTF-8", $site_url)?>/shop/data/goods/<?=iconv("CP949", "UTF-8", $v)?></prdImageURL>
<?
		}
		$tmpEach = explode("|", $goodsData['img_m']);
		?>
		<prdMImageURL><?=$site_url?>/shop/data/goods/<?=$tmpEach[0]?></prdImageURL>
		<?
			$tmpEach = explode("|", $goodsData['img_s']);
		?><prdSImageURL><?=$site_url?>/shop/data/goods/<?=$tmpEach[0]?></prdImageURL>
	</prdImage>
	<?
	for($i=0;$i<count($option['optno']);$i++){
	?><option>
		<code><?=iconv("CP949", "UTF-8", $option['optno'][$i])?></code>
		<category><?=iconv("CP949", "UTF-8", $option['optnm'][$i])?></category>
		<title><?=iconv("CP949", "UTF-8", $option['optvl'][$i])?></title>
		<price><?=iconv("CP949", "UTF-8", $option['optpr'][$i])?></price>
		<stock><?=iconv("CP949", "UTF-8", $option['stock'][$i])?></stock>
	</option>
	<?
	}
	?><detailHTML><![CDATA[<?=iconv("CP949", "UTF-8", $goodsData['longdesc'])?>]]></detailHTML>
	<?
	for($i=0;$i<count($review['score']);$i++){
	?><valuation>
		<score><?=iconv("CP949", "UTF-8", $review['score'][$i])?></score>
		<writer><?=iconv("CP949", "UTF-8", $review['name'][$i])?></writer>
		<description><![CDATA[<?=iconv("CP949", "UTF-8", $review['contents'][$i])?>]]></description>
		<regDate><?=iconv("CP949", "UTF-8", $review['regdt'][$i])?></regDate>
	</valuation><?
	}
	?>
	<regDate><?=iconv("CP949", "UTF-8", str_replace("-", "", substr($goodsData['regdt'], 0, 10)))?>_<?=str_replace(":", "", substr($goodsData['regdt'], 11, 8))?></regDate>
	<updateDate><?=iconv("CP949", "UTF-8", str_replace("-", "", substr($goodsData['updatedt'], 0, 10)))?>_<?=str_replace(":", "", substr($goodsData['updatedt'], 11, 8))?></updateDate>
</productInfo>
