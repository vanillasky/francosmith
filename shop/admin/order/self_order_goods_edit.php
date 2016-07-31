<?
	include "../_header.popup.php";
	include "../../conf/config.pay.php";
	include "../../lib/cart.class.php";
	require "../../lib/load.class.php";

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
?>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<script src="../common.js"></script>
<link rel="styleSheet" href="../style.css">
</head>

<script type="text/javascript">
var price = new Array();
var reserve = new Array();
var consumer = new Array();
var memberdc = new Array();
var realprice = new Array();
var couponprice = new Array();
var coupon = new Array();
var cemoney = new Array();
var opt1img = new Array();
var opt2icon = new Array();
var opt2kind = "{optkind[1]}";
var oldborder = "";

<?
	// 옵션별 가격정보
	$i = 0;
	if(is_array($opt)) foreach($opt as $oList_k => $oList_v) {
		foreach($oList_v as $oField_k => $oField_v) {
			$tmpKey = $oField_v['opt1'].(($oField_v['opt2']) ? '|'.$oField_v['opt2'] : '');
			if($i == 0) echo "var fkey = \"$tmpKey\";";
?>
price['<?=$tmpKey?>'] = <?=$oField_v['price']?>;
reserve['<?=$tmpKey?>'] = <?=$oField_v['reserve']?>;
consumer['<?=$tmpKey?>'] = <?=$oField_v['consumer']?>;
memberdc['<?=$tmpKey?>'] = <?=$oField_v['memberdc']?>;
realprice['<?=$tmpKey?>'] = <?=$oField_v['realprice']?>;
coupon['<?=$tmpKey?>'] = <?=$oField_v['coupon']?>;
couponprice['<?=$tmpKey?>'] = <?=$oField_v['couponprice']?>;
cemoney['<?=$tmpKey?>'] = <?=$oField_v['coupon_emoney']?>;
<?
			$i++;
		}
	}

	// 옵션별 이미지
	if(is_array($opt1img)) foreach($opt1img as $k => $v) echo "opt1img['$k'] = \"$v\"\n";

	// 옵션2의 아이콘
	if(is_array($opticon[1])) foreach($opticon[1] as $k => $v) echo "opt2icon['$k'] = \"$v\"\n";
?>

/* 필수 옵션 분리형 스크립트 start */
var opt = new Array();
opt[0] = new Array("('1차옵션을 먼저 선택해주세요','')");
<?
	$i = 1;
	if(is_array($opt)) foreach($opt as $k => $v) {
		$tmpOpt = "opt['$i'] = new Array(\"('== 옵션선택 ==','')\",";
		foreach($v as $k2 => $v2) {
			$tmpOpt .= "\"('".$v2['opt2'];
			if($v2['price'] != $item['price']) $tmpOpt .= "(".number_format($item['price'])."원)";
			if($item['usestock'] && !$v2['stock']) $tmpOpt .= " [품절]";
			$tmpOpt .= "','".$v2['opt2']."','";
			if($item['usestock'] && !$v2['stock']) $tmpOpt .= "soldout";
			$tmpOpt .= "')\"";

			if(isset($v[$k2 + 1])) $tmpOpt .= ",";
		}
		$tmpOpt .= ");";
		echo $tmpOpt."\n";
		$i++;
	}
?>

function checkForm(f) {
	opt = document.getElementsByName('opt[]');
	for(i = 0; i < opt.length; i++) {
		if(!opt[i].options[opt[i].selectedIndex].value) {
			alert("옵션을 선택해 주세요.");
			opt[i].focus();
			return false;
		}
	}

	addopt = document.getElementsByName('addopt[]');
	for(i = 0; i < addopt.length; i++) {
		if(!addopt[i].options[addopt[i].selectedIndex].value) {
			alert("옵션을 선택해 주세요.");
			addopt[i].focus();
			return false;
		}
	}

	if(f.ea.value * 1 < 1) {
		alert("수량을 입력해 주세요.");
		f.ea.focus();
		return false;
	}

	var res = chkForm(f);

	// 입력옵션 필드값 설정
	if (res)
	{
		var addopt_inputable = document.getElementsByName('addopt_inputable[]');
		for (var i=0,m=addopt_inputable.length;i<m ;i++ ) {

			if (typeof addopt_inputable[i] == 'object') {
				var v = addopt_inputable[i].value.trim();
				if (v) {
					var tmp = addopt_inputable[i].getAttribute("option-value").split('^');
					tmp[2] = v;
					v = tmp.join('^');
				}
				else {
					v = '';
				}
				document.getElementsByName('_addopt_inputable[]')[i].value = v;
			}
		}
	}

	return res;
}

function increStock(stockIptID) {
	$(stockIptID).value = ($(stockIptID).value) * 1 + 1;
}

function decreStock(stockIptID) {
	if(($(stockIptID).value * 1) > 1) $(stockIptID).value = ($(stockIptID).value) * 1 - 1;
}

</script>

<body style="margin:10px">
<b style="font-family:dotum;">■ 옵션 및 수량 수정</b>
<form name="editForm" method="post" action="../order/indb.self_order.php" onsubmit="return checkForm(this);">
<input type="hidden" name="mode" value="editOption">
<input type="hidden" name="goodsno" value="<?=$goodsno?>">
<input type="hidden" name="idx" value="<?=$_GET['idx']?>">

<div style="width:100%;height:410px;border:1px solid #D0D0D0;padding:10px;overflow-y:auto;">

<table border="0">
<tr>
	<td>
		<a href="../../goods/goods_view.php?goodsno=<?=$goodsno?>"><?=goodsimg($goods['img_s'], 80, 'id=\'objImg\'', 3)?></a>
	</td>
	<td><?=$goods['goodsnm']?></td>
</tr>
</table>
<div style="border:1px #9D9D9D dotted; margin:15px 0px; overflow:hidden; height:1px;"></div>


<table border="0" cellpadding="2" cellspacing="0" class="top">

<!-- 추가 옵션 -->
	<? $tmpi = 0; if(is_array($addopt_inputable)) foreach($addopt_inputable as $k => $v) { ?>
	<tr>
		<td align="right"><?=$k?> : </td>
		<td>
			<input type="hidden" name="_addopt_inputable[]" value="">
			<input type="text" name="addopt_inputable[]" label="<?=$k?>" option-value="<?=$v['sno']?>^<?=$k?>^<?=$v['opt']?>^<?=$v['addprice']?>" value="<?=$v['value']?>" <? if ($addopt_inputable_req[$tmpi]) { ?>required fld_esssential<? } ?> maxlength="<?=$v['opt']?>">
		</td>
	</tr>
	<tr><td height="6"></td></tr>
	<? $tmpi++; } ?>

<!-- 여기선 무조건 일체형 옵션으로.. -->
	<? if($opt && ($typeOption == "single" || $typeOption == "double")) { ?>
	<tr><td height="6"></td></tr>
	<tr>
		<td align="right"><?=str_replace("|", "/", $goods['optnm'])?> : </td>
		<td>
			<div>
			<select name="opt[]">
			<option value="">== 옵션선택 ==</option>
		<? foreach($opt as $k => $v) { ?>
			<? foreach($v as $k2 => $v2) { $tempOption = $v2['opt1'].(($v2['opt2']) ? "|".$v2['opt2'] : ""); ?>
			<option value="<?=$tempOption?>" <? if($item['usestock'] && !$v2['stock']) { ?>disabled class="disabled"<? } ?> <?if($v2['optno'] == $item['optno']) echo "selected";?>><?=str_replace("|", "/", $tempOption)?>
			<? if($v2['price'] != $item['price']) { echo "(".number_format($v2['price'])."원)"; } ?>
			<? if($item['usestock'] && !$v2['stock']) { ?> [품절]<? } ?>
			</option>
			<? } ?>
		<? } ?>
			</select></div>
		</td>
	</tr>
	<tr><td height="6"></td></tr>
	<? } ?>

<!-- 추가 옵션 -->
	<? $tmpi = 0; if(is_array($addopt)) foreach($addopt as $k => $v) { ?>
	<tr>
		<td align="right"><?=$k?> : </td>
		<td>
			<select name="addopt[]">
			<option value="">==<?=$k?> 선택==</option>
		<? if(!$addoptreq[$tmpi]) { ?>
			<option value="-1" <?if(!$item['addopt'][$tmpi]['sno']) echo "selected";?>>선택안함</option>
		<? } ?>
		<? foreach($v as $k2 => $v2) { ?>
			<option value="<?=$v2['sno']?>^<?=$k?>^<?=$v2['opt']?>^<?=$v2['addprice']?>" <?if(in_array($v2['sno'], $item[addopt_sno])) echo "selected";?>><?=$v2['opt']?>
				<? if($v2['addprice']) { echo "(".number_format($v2['addprice'])."원 추가)"; } ?>
			</option>
		<? } ?>
			</select>
		</td>
	</tr>
	<tr><td height="6"></td></tr>
	<? $tmpi++; } ?>

<!-- 수량 -->
	<tr><td height="6"></td></tr>
	<tr>
		<td align="right">수량 : </td>
		<td>
			<table cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td rowspan="2">
					<input type="text" name="ea" id="ea" size="2" value="<?=$item['ea']?>" step="<?=$item['sales_unit'] ? $item['sales_unit'] : '1' ?>" min="<?=$item['min_ea'] ? $item['min_ea'] : '1' ?>" max="<?=$item['max_ea'] ? $item['max_ea'] : '0' ?>" onblur="chg_cart_ea(this, 'set');" onkeydown="onlynumber()" style="border:1px solid #D3D3D3;width:30px;text-align:right;height:20px">

					</td>
					<td><img src="../img/btn_plus.gif" onClick="chg_cart_ea(editForm.ea,'up')" style="cursor:pointer;" /></td>
				</tr>
				<tr>
					<td><img src="../img/btn_minus.gif" onClick="chg_cart_ea(editForm.ea,'dn')" style="cursor:pointer;" /></td>
				</tr>
			</table>
		</td>
	</tr>
</table>

</div>

<div style="text-align:center;padding-top:13px;" class="noline"><input type="image" src="../img/btn_modify.gif" /><a href="javascript:void(0);" onClick="self.close();"><img src="../img/btn_cancel.gif" style="margin-left:5px" /></a></div>

</form>
</body>
</html>
