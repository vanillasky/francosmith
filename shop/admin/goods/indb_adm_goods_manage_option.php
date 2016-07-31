<?
include "../lib.php";

include_once("../../conf/config.pay.php");

// 가격, 재고 수정 및 네이버 지식쇼핑 상품 엔진 업데이트
$snos = array_values($_POST['chk']);	// 체크된 옵션(품목) 만 수정함

naver_goods_diff_check();
daum_goods_diff_check();
$ar_goods_update = array();
$goodsnos = array();

$option = Clib_Application::getModelClass('goods_option');

for ($i=0,$m=sizeof($snos);$i<$m;$i++) {

	$sno = $snos[$i];
	$option->resetData()->load($sno);

	$option->setData('consumer', $_POST['consumer'][$sno]);
	$option->setData('price', $_POST['price'][$sno]);
	$option->setData('supply', $_POST['supply'][$sno]);
	$option->setData('reserve', $_POST['reserve'][$sno]);
	$option->setData('stock', $_POST['stock'][$sno]);
	
	daum_goods_diff($option['goodsno'],$option);	// 다음 요약 EP
	$option->save();

	// 네이버 지식쇼핑 상품 엔진 업데이트 위한 데이터 세팅
	$ar_goods_update[$option['goodsno']]['stock'][$sno] = $_POST['stock'][$sno];

	if($option['link']=='1')
	{
		list($ar_goods_update[$option['goodsno']]['price']) = $option['price'];
		$ar_goods_update[$option['goodsno']]['reserve'] = $option['reserve'];
	}

	$goodsnos[$option['goodsno']][$sno] = true;

}

// 일부 옵션만 수정되는 경우, 나머지 옵션 들도 처리
foreach($goodsnos as $goodsno => $snos) {

	$query = "select * from gd_goods_option where goodsno = $goodsno and go_is_deleted <> '1'";

	if ($snos) {
		$query .= " AND sno NOT IN (".implode(',', array_keys($snos)).")";
	}

	$rs = $db->query($query);

	while ($option = $db->fetch($rs,1)) {

		if($option['link']=='1')
		{
			$ar_goods_update[$option['goodsno']]['price'] = $option['price'];
			$ar_goods_update[$option['goodsno']]['reserve'] = $option['reserve'];
		}

		if (!isset($ar_goods_update[$option['goodsno']]['stock'][$option['sno']])) {
			$ar_goods_update[$option['goodsno']]['stock'][$option['sno']] = $option['stock'];
		}

	}

}

// 상품 총 재고량 갱신 및 수정일시 저장
foreach($ar_goods_update as $goodsno => $data) {
	$data['stock'] = array_sum($data['stock']);

	$def_goods_opt = $db->_select($db->_query_print(' SELECT price AS goods_price, consumer AS goods_consumer, supply AS goods_supply, reserve AS goods_reserve FROM '.GD_GOODS_OPTION.' WHERE goodsno=[i] AND link=1 AND go_is_deleted="0" AND go_is_display="1" ', $goodsno));
 	$def_goods_opt[0]['totstock'] = $data['stock'];
 	$db->_query($db->_query_print(' UPDATE '.GD_GOODS.' SET [cv], updatedt=NOW() WHERE goodsno=[i]', $def_goods_opt[0], $goodsno));

	naver_goods_diff($goodsno,$data);
	daum_goods_diff($goodsno,$data);
}

### 인터파크 전송
if ($inpkCfg['use'] == 'Y' || $inpkOSCfg['use'] == 'Y'){
	$element = array();
	$element['returnUrl'] = $_SERVER[HTTP_REFERER];
	foreach($goodsno as $k => $v) $element['goodsno['.$k.']'] = $v;
	goPost('../interpark/transmit_action.php', $element, 'parent');
}

// 페이지캐시 초기화
$templateCache = Core::loader('TemplateCache');
$templateCache->clearCacheByClass('goods');

msg('수정된 내용이 저장되었습니다.');
echo "<script type=\"text/javascript\">parent.location.href='{$_SERVER[HTTP_REFERER]}'; history.back();</script>";

