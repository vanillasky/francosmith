<?php

/* Return Display Goods Data Function */

function dataDisplayGoods( $mode, $img='img_s', $limit=0 ){

	global $db, $cfg ,$lstcfg, $cfg_step;
	include dirname(__FILE__) . "/../../conf/config.pay.php";

	if (is_file(dirname(__FILE__) . "/../../conf/config.soldout.php"))
		include dirname(__FILE__) . "/../../conf/config.soldout.php";
	
	@include dirname(__FILE__) . "/../../conf/config.display.php";
	
	$goods = array();

	$mainAutoSort = Core::loader('mainAutoSort');
	
	if ($GLOBALS['tpl']->var_['']['connInterpark']) $where .= "and b.inpk_prdno!=''";
	if (isset($GLOBALS['tpl']->var_['']['id'])) $GLOBALS['tpl']->var_['']['id'] = '';

	if (!$cfg_step[$mode]['sort_type'] || $cfg_step[$mode]['sort_type'] == '1') {
		$orderby = 'order by a.sort';
	} else {
		$sortNum = $mainAutoSort->use_table.".sort".$cfg_step[$mode]['sort_type']."_".$cfg_step[$mode]['select_date'];
		$orderby = 'order by '.$sortNum;
	}

	/* 메인 페이지 상품 진열인 경우 스킨별 처리 */
	if ( strlen($mode) >= 1 && strlen($mode) <= 10 ){
		if (!$cfg_step[$mode]['sort_type'] || $cfg_step[$mode]['sort_type'] == '1') {
			if( $cfg['shopMainGoodsConf'] == "E" ){
				$where .= " and tplSkin = '".$cfg['tplSkin']."'";
			}else{
				$where .= " and (tplSkin = '' OR tplSkin IS NULL)";
			}
		}

		// 품절 상품 제외
		if ($cfg_soldout['exclude_main']) {
			if (!$cfg_step[$mode]['sort_type'] || $cfg_step[$mode]['sort_type'] == '1') {
				$where .= " AND !( b.runout = 1 OR (b.usestock = 'o' AND b.usestock IS NOT NULL AND b.totstock < 1) ) ";
			} else {
				$where .= " AND !( ".GD_GOODS.".runout = 1 OR (".GD_GOODS.".usestock = 'o' AND ".GD_GOODS.".usestock IS NOT NULL AND ".GD_GOODS.".totstock < 1) ) ";
			}
		}
		// 제외시키지 않는 다면, 맨 뒤로 보낼지를 결정
		else if ($cfg_soldout['back_main']) {
			if (!$cfg_step[$mode]['sort_type'] || $cfg_step[$mode]['sort_type'] == '1') {
				$orderby = "order by `soldout` ASC, a.sort";
				$_add_field = ",IF (b.runout = 1 , 1, IF (b.usestock = 'o' AND b.totstock = 0, 1, 0)) as `soldout`";
			} else {
				$orderby = 'order by `soldout` ASC, '.$sortNum;
				$_add_field = ",IF (".GD_GOODS.".runout = 1 , 1, IF (".GD_GOODS.".usestock = 'o' AND ".GD_GOODS.".totstock = 0, 1, 0)) as `soldout`";
			}
		}
	}

	if (!$cfg_step[$mode]['sort_type'] || $cfg_step[$mode]['sort_type'] == '1') {
		$query = "
		select
			*,b.$img img_s
			$_add_field
		from
			".GD_GOODS_DISPLAY." a
			left join ".GD_GOODS." b on a.goodsno=b.goodsno
			left join ".GD_GOODS_OPTION." c on a.goodsno=c.goodsno and link and go_is_deleted <> '1' and go_is_display = '1'
			left join ".GD_GOODS_BRAND." d on b.brandno=d.sno
		where
			a.mode = '$mode'
			and b.open
			{$where}
		{$orderby}
		";
		if ( $limit > 0 ) $query .= " limit " . $limit;
	} else {
		list($add_table, $add_where, $add_order) = $mainAutoSort->getSortTerms($cfg_step[$mode]['categoods'], $cfg_step[$mode]['price'], $cfg_step[$mode]['stock_type'], $cfg_step[$mode]['stock_amount'], $cfg_step[$mode]['regdt'], $sortNum);

		$query = "
		SELECT 
			*,".GD_GOODS.".$img img_s
			$_add_field
		FROM
			".$mainAutoSort->use_table."
			{$add_table}
			left join ".GD_GOODS_BRAND." ON ".GD_GOODS.".brandno=".GD_GOODS_BRAND.".sno
		WHERE
			".GD_GOODS.".open
			AND link
			{$where}
			{$add_where}
		GROUP BY ".$mainAutoSort->use_table.".goodsno {$orderby}
		";
		if ($limit > 0) {
			if ($limit > $mainAutoSort->sort_limit) $query .= " limit " . $mainAutoSort->sort_limit;
			else $query .= " limit " . $limit;
		} else {
			$query .= " limit " . $mainAutoSort->sort_limit;
		}
	}

	//DB Cache 사용 141030
	$dbCache = Core::loader('dbcache')->setLocation('display');

	if (!$goods = $dbCache->getCache($query)) {
		$res = $db->query($query);
		while ( $data = $db->fetch( $res, 1 ) ){

			### 실재고에 따른 자동 품절 처리
			$data['stock'] = $data['totstock'];
			if ($data[usestock] && $data[stock]<=0) $data[runout] = 1;

			### 쿠폰
			list($data['coupon'],$data['coupon_emoney']) = getCouponInfo($data['goodsno'],$data['price']);

			### 적립금 셋팅
			if(!$data['use_emoney']){
				if( !$set['emoney']['chk_goods_emoney'] ){
					if( $set['emoney']['goods_emoney'] ) $tmp['reserve'] = getDcprice($data['price'],$set['emoney']['goods_emoney'].'%');
				}else{
					$tmp['reserve'] = $set['emoney']['goods_emoney'];
				}
				$data['reserve'] = $tmp['reserve'];
			}

			$data['reserve'] += $data['coupon_emoney'];

			### 아이콘
			$data[icon] = setIcon($data[icon],$data[regdt]);
			
			$data['shortdesc'] = ($lstcfg[rtpl] == "tpl_10") ? htmlspecialchars($data['shortdesc']) : $data['shortdesc'];	// 짤은설명 툴팁 수정
			
			// 상품할인 가격 표시
			if ($displayCfg['displayType'] === 'discount' && $cfg_step[$mode]['tpl'] != 'tpl_04' && $cfg_step[$mode]['tpl'] != 'tpl_05') {
				$discountModel = '';
				$goodsDiscount = '';
				if ($data['use_goods_discount'] === '1') {
					$discountModel = Clib_Application::getModelClass('Goods_Discount');
					$goodsDiscount = $discountModel->getDiscountAmountSearch($data);
				}
				if ($goodsDiscount) {
					$data['oriPrice'] = $data['price'];
					$data['goodsDiscountPrice'] = $data['price'] - $goodsDiscount;
				}
				else {
					$data['oriPrice'] = '0';
					$data['goodsDiscountPrice'] = $data['price'];
				}
			}
			
			// 출력 제어
			$goods[] = setGoodsOuputVar($data);
		}
		if ($dbCache) { $dbCache->setCache($query, $goods); }
	}

	return $goods;
}
?>