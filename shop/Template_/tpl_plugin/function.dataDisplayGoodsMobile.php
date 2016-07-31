<?php

/* Return Display Goods Data Function */

function dataDisplayGoodsMobile( $mode, $img='img_mobile', $limit=0, $re=0 ){

	global $db, $cfg;
	include dirname(__FILE__) . "/../../conf/config.pay.php";

	//상품할인
	$goodsDiscountModel = Clib_Application::getModelClass('goods_discount');

	$goods = array();

	if($re){
		$query = "
		select
			*,b.img_mobile, b.img_s, b.img_i, b.img_m, b.img_l, b.use_only_adult, b.use_goods_discount
		from
			".GD_GOODS_DISPLAY." a
			left join ".GD_GOODS." b on a.goodsno=b.goodsno
			left join ".GD_GOODS_OPTION." c on a.goodsno=c.goodsno and link and go_is_deleted <> '1' and go_is_display = '1'
			left join ".GD_GOODS_BRAND." d on b.brandno=d.sno
		where
			a.mode = '$mode'
			and b.open_mobile
			{$where}
		order by a.sort
		";
	}
	else{
		$query = "
		select
			*,b.img_mobile, b.img_s, b.img_i, b.img_m, b.img_l, b.use_only_adult, b.use_goods_discount
		from
			".GD_GOODS_DISPLAY_MOBILE." a
			left join ".GD_GOODS." b on a.goodsno=b.goodsno
			left join ".GD_GOODS_OPTION." c on a.goodsno=c.goodsno and link and go_is_deleted <> '1' and go_is_display = '1'
			left join ".GD_GOODS_BRAND." d on b.brandno=d.sno
		where
			a.mode = '$mode'
			and b.open_mobile
			{$where}
		order by a.sort
		";
	}
	if ( $limit > 0 ) $query .= " limit " . $limit;
	$res = $db->query($query);
	while ( $data = $db->fetch( $res, 1 ) ){

		if(!$data['img_mobile']) $data['img_mobile'] = $data['img_s'];
		if(!$data['img_mobile']) $data['img_mobile'] = $data['img_i'];
		if(!$data['img_mobile']) $data['img_mobile'] = $data['img_m'];
		if(!$data['img_mobile']) $data['img_mobile'] = $data['img_l'];
		
		// 성인 전용 상품일때 이미지 교체
		if ($data['use_only_adult'] && ! Clib_Application::session()->canAccessAdult()) {
			$data['img_mobile'] = 'http://' . $_SERVER['HTTP_HOST'] . $GLOBALS['cfg']['rootDir'] . "/data/skin/" . $GLOBALS['cfg']['tplSkin'] . '/img/common/19.gif';
		}
		### 실재고에 따른 자동 품절 처리
		$data['stock'] = $data['totstock'];
		if ($data[usestock] && $data[stock]==0) $data[runout] = 1;

		### 쿠폰
		list($data['coupon'],$data['coupon_emoney']) = getCouponInfoMobile($data['goodsno'],$data['price']);

		// 쿠폰 이미지 경로
		if($data['coupon'] > 0 || $data['coupon_emoney'] > 0){
			$data['coupon_discount'] = true;
		}

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
		
		// 상품할인
		if($data['use_goods_discount']){						
			$data['special_discount'] = $goodsDiscountModel->getDiscountUnit($data, Clib_Application::session()->getMemberLevel());
		}

		$goods[] = $data;
	}

	if(count($goods) || $re){
		return $goods;
	}
	else{
		return dataDisplayGoodsMobile( $mode, $img, $limit, 1);
	}
}
?>