<?
include "../_header.php";
include "../lib/cart.class.php";

$orderitem_mode = "cart";

$cart = new Cart;

// 수정할 장바구니 정보
$item = $cart->item[ $_GET['idx'] ];
$item[addopt_sno] = array();
$item[addopt_value] = array();
if (is_array($item[addopt])) foreach($item[addopt] as $addopt) {
	$item[addopt_sno][] = $addopt[sno];
	$item[addopt_value][] = $addopt[opt];
}
unset($addopt);
$goodsno = $item['goodsno'];

// 해당 장바구니의 상품 정보
$goods = Clib_Application::getModelClass('goods')->load($goodsno);

### 필수옵션 출력타입 (일체형[single]/분리형[double])
$typeOption = $goods[opttype];

### 필수옵션 (선택 가격)
$optnm = $goods[option_name] ? explode("|",$goods[option_name]) : explode("|",$goods[optnm]);
$optnm_size = sizeof($optnm);
$options = Clib_Application::getCollectionClass('goods_option');
$options->addFilter('go_is_display', 1);
$options->addFilter('goodsno', $goodsno);
$options->load();

$idx=0;
foreach($options as $option) {

	foreach($option as $k => $v) {
		$option[$k] = htmlspecialchars($v);
	}

	if ($option[stock] && !$isSelected){
		$isSelected = 1;
		$option[selected] = "selected";
		$preSelIndex = $idx++;
	}

	### 옵션별 회원 할인가 및 쿠폰 할인가 계산
	$realprice = $option[realprice] = $option[memberdc] = $option[coupon] = $option[coupon_emoney] = $option[couponprice] = 0;
	$group_profit = Core::loader('group_profit');
	$group_profit->getGroupProfit();
	if( $group_profit->dc_type == 'goods' && !$goods->getData('exclude_member_discount')){
		if( $option[price] >= $group_profit->dc_std_amt ){
			if(!$mdc_exc) $option[memberdc] = getDcprice($option[price],$member[dc]."%");
		}
	}
	$option[realprice] = $option[price] - $option[memberdc] - $goods[special_discount_amount];
	$tmp_coupon = getCouponInfo($goods[goodsno],$option['price'],'v');

	if($cfgCoupon[use_yn] == '1'){
		if($tmp_coupon)foreach($tmp_coupon as $v){
			$tp = $v[price];
			if(substr($v[price],-1) == '%') $tp = getDcprice($option[price],$v[price]);

			if($cfgCoupon['double']==1){
				if(!$v[ability]){
					$option[coupon] += $tp;
				}else {
					$option[coupon_emoney] += $tp;
				}
			}else{
				if(!$v[ability] && $option[coupon] < $tp) $option[coupon] = $tp;
				else if($v[ability] && $option[coupon_emoney] < $tp) $option[coupon_emoney] = $tp;
			}
		}
	}
	if($option[coupon] && $option[memberdc] && $cfgCoupon[range] != '2') $realprice = $option[realprice];
	else $realprice = $option[price];
	$option[couponprice] = $realprice - $option[coupon];
	if($option[coupon] && $option[memberdc] && $cfgCoupon[range] == '2') $option[realprice] = $option[memberdc] = 0;
	if($option[coupon] && $option[memberdc] && $cfgCoupon[range] == '1') $option[couponprice] = $option[coupon] = 0;
	if (!$optkey){
		$optkey = $option[opt1];
		$goods[a_coupon] = $tmp_coupon;
	}

	if(!$goods['use_emoney']){
		if($set['emoney']['useyn'] == 'n') $option['reserve'] = 0;
		else {
			if( !$set['emoney']['chk_goods_emoney'] ){
				$option['reserve']	= 0;
				if( $set['emoney']['goods_emoney'] ) $option['reserve'] = getDcprice($option['price'],$set['emoney']['goods_emoney'].'%');
			}else{
				$option['reserve']	= $set['emoney']['goods_emoney'];
				if(!$option['reserve']) $option['reserve'] =0;
			}
		}
	}

	if($option['opt1img'])$opt1img[$option['opt1']] = $option['opt1img'];


	if($option['opt1icon'])$opticon[0][$option['opt1']] = $option['opt1icon'];
	if($option['opt2icon'])$opticon[1][$option['opt2']] = $option['opt2icon'];
	if($option['optnicon'])$opticon[n][$option['optn']] = $option['optnicon'];

	$lopt[0][$option['opt1']] = 1;
	$lopt[1][$option['opt2']] = 1;
	$opt[$option[opt1]][] = $option;
	$goods[stock] += $option[stock];

}

for($i=0;$i<2;$i++){
	if(isset($opticon[$i])){
		if(count($lopt[$i]) == count($opticon[$i])) $_optkind[$i] = $goods['opt'.($i+1).'kind'];
		else $_optkind[$i] = "select";
	}else $_optkind[$i] = "select";
}
$goods['optkind'] = $_optkind;

$goods[optnm]	= implode('/', $optnm);
if ($opt[$optkey][0][opt1] == null && $opt[$optkey][0][opt2] == null) {
	unset($opt);
	unset($options);
}
if (!$optnm[1]) $typeOption = "single";

### 추가옵션
$r_addoptnm = explode("|",$goods[addoptnm]);
for ($i=0;$i<count($r_addoptnm);$i++) list ($addoptnm[],$_addoptreq[],$_addopttype[]) = explode("^",$r_addoptnm[$i]);
$query = "select * from ".GD_GOODS_ADD." where goodsno='$goodsno' order by type,step,sno";
$res = $db->query($query);
$_offset = 0;
while ($tmp=$db->fetch($res,1)) {
	if ($tmp['type'] == 'I') {
		// 입력된 값
		$_offset = (int) array_search('I', $_addopttype);

		if (($key = array_search($tmp[sno], $item[addopt_sno])) !== false) {
			$tmp['value'] = $item[addopt_value][$key];
		}

		$addopt_inputable[$addoptnm[$_offset + $tmp[step]]] = $tmp;
		$addopt_inputable_req = array_slice($_addoptreq, $_offset);
	}
	else {
		$addopt[$addoptnm[$tmp[step]]][] = $tmp;
		$addoptreq = $_offset > 0 ? array_slice($_addoptreq, 0, $_offset) : $_addoptreq;
	}
}


$tpl->assign($goods->getData());
$tpl->assign('option', $options);
$tpl->assign('item',$item);
### 템플릿 출력
$tpl->print_('tpl');
?>
