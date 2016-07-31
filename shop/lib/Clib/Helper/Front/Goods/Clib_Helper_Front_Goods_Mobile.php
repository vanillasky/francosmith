<?php
/**
 * Clib_Helper_Front_Goods_Mobile
 * @author extacy @ godosoft development team.
 */
class Clib_Helper_Front_Goods_Mobile extends Clib_Helper_Front_Goods_Abstract
{
	protected function setGoodsCollectionAttributes($collection, $listYn)
	{
		global $cfg_soldout,$displayCfg;

		// 네이버 마일리지
		$naverNcash = Core::loader('naverNcash');
		if ($naverNcash->canUseMobile() === false)
			$naverNcash->useyn = "N";
		if ($naverNcash->useyn == 'Y' && $naverNcash->baseAccumRate)
			$naverMileageDisplay = true;
		else
			$naverMileageDisplay = false;

		if (method_exists($category, 'getConfig')) {
			$lstcfg = $category->getConfig();
		}
		else {
			global $lstcfg;
		}

		if (!$category instanceof Clib_Model_Category_Category) {

			$category_collection = Clib_Application::getCollectionClass('category');
			$category_collection->addExpressionFilter("level <> 0");
			$category_collection->load();
		}

		$goodsDiscountModel = Clib_Application::getModelClass('goods_discount');

		foreach ($collection as $item) {

			// 리스트인 경우
			if ($listYn) {
				unset($item['longdesc']);
				unset($item['memo']);
			}

			// 성인 전용 상품일때 이미지 교체
			if ($item['use_only_adult'] && ! Clib_Application::session()->canAccessAdult()) {
				if($GLOBALS['cfgMobileShop']['mobileShopRootDir'] == "/m2"){
					$skin_folder = "/skin_mobileV2";
				} else {
					$skin_folder = "/skin_mobile";
				}
				$item['img_i'] = 'http://' . $_SERVER['HTTP_HOST'] . $GLOBALS['cfg']['rootDir'] . "/data" . $skin_folder . "/" . $GLOBALS['cfg']['tplSkinMobile'] . '/common/img/19.gif';
				$item['img_s'] = 'http://' . $_SERVER['HTTP_HOST'] . $GLOBALS['cfg']['rootDir'] . "/data" . $skin_folder . "/" . $GLOBALS['cfg']['tplSkinMobile'] . '/common/img/19.gif';
				$item['img_m'] = 'http://' . $_SERVER['HTTP_HOST'] . $GLOBALS['cfg']['rootDir'] . "/data" . $skin_folder . "/" . $GLOBALS['cfg']['tplSkinMobile'] . '/common/img/19.gif';
				$item['img_l'] = 'http://' . $_SERVER['HTTP_HOST'] . $GLOBALS['cfg']['rootDir'] . "/data" . $skin_folder . "/" . $GLOBALS['cfg']['tplSkinMobile'] . '/common/img/19.gif';
				$item['img_mobile'] = 'http://' . $_SERVER['HTTP_HOST'] . $GLOBALS['cfg']['rootDir'] . "/data" . $skin_folder . "/" . $GLOBALS['cfg']['tplSkinMobile'] . '/common/img/19.gif';
				$item['img_w'] = 'http://' . $_SERVER['HTTP_HOST'] . $GLOBALS['cfg']['rootDir'] . "/data" . $skin_folder . "/" . $GLOBALS['cfg']['tplSkinMobile'] . '/common/img/19.gif';
				$item['img_x'] = 'http://' . $_SERVER['HTTP_HOST'] . $GLOBALS['cfg']['rootDir'] . "/data" . $skin_folder . "/" . $GLOBALS['cfg']['tplSkinMobile'] . '/common/img/19.gif';
				$item['img_y'] = 'http://' . $_SERVER['HTTP_HOST'] . $GLOBALS['cfg']['rootDir'] . "/data" . $skin_folder . "/" . $GLOBALS['cfg']['tplSkinMobile'] . '/common/img/19.gif';
				$item['img_z'] = 'http://' . $_SERVER['HTTP_HOST'] . $GLOBALS['cfg']['rootDir'] . "/data" . $skin_folder . "/" . $GLOBALS['cfg']['tplSkinMobile'] . '/common/img/19.gif';
			}
			
			if (is_file('../../shop/data/goods/' . $item['img_i'])) { // 메인이미지(200)
				$item['img_html_i'] = goodsimgMobile($item['img_i'], 200);
				$item['goodsImage_i'] = goodsimgMobile($item['img_i'], 200);
			}
			else if (is_file('../../shop/data/goods/' . $item['img_mobile'])) {
				$item['img_html_i'] = goodsimgMobile($item['img_mobile'], 200);
				$item['goodsImage_i'] = goodsimgMobile($item['img_mobile'], 200);
			}
			else {
				$img_l_arr = explode("|", $item['img_l']);
				$item['img_html_i'] = goodsimgMobile($img_l_arr[0], 200);
				$item['goodsImage_i'] = goodsimgMobile($img_l_arr[0], 200);
			}

			if (is_file('../../shop/data/goods/' . $item['img_m'])) { // 상세이미지(300)
				$item['img_html_m'] = goodsimgMobile($item['img_m'], 300);
				$item['goodsImage_m'] = goodsimgMobile($item['img_m'], 300);
			}
			else if (is_file('../../shop/data/goods/' . $item['img_mobile'])) {
				$item['img_html_m'] = goodsimgMobile($item['img_mobile'], 300);
				$item['goodsImage_m'] = goodsimgMobile($item['img_mobile'], 300);
			}
			else {
				$img_l_arr = explode("|", $item['img_l']);
				$item['img_html_m'] = goodsimgMobile($img_l_arr[0], 300);
				$item['goodsImage_m'] = goodsimgMobile($img_l_arr[0], 300);
			}

			if ($naverMileageDisplay) {
				$exceptionYN = $naverNcash->exception_goods(array( array('goodsno' => $item['goodsno'])));
				if ($exceptionYN == 'N') {
					$item['NaverMileageAccum'] = true;
				}
			}

			$item['price'] = $item->getPrice();
			$item['consumer'] = $item->getGoodsConsumer();
			$item['reserve'] = $item->getReserve();

			// 즉석할인쿠폰 유효성 검사 (pc or mobile)
			list($item['coupon'], $item['coupon_emoney']) = getCouponInfoMobile($item['goodsno'], $item['price']);

			// 쿠폰할인 체크
			if($item['coupon'] > 0 || $item['coupon_emoney'] > 0){
				$item['coupon_discount'] = true;
			}

			$tmp_category = null;

			if (!$category instanceof Clib_Model_Category_Category) {
				$goods_link_collection = $item->getCategory();

				foreach($category_collection as $category_model) {
					foreach($goods_link_collection as $goods_link) {

						if($category_model->getData('category') == $goods_link->getData('category')) {
							$category = $category_model;
							break;
						}
					}

					if($category instanceof Clib_Model_Category_Category) {
						break;
					}
				}

				$tmp_category = $category;
				unset($category);
			}
			else {
				$tmp_category = $category;
			}

			if ($tmp_category instanceof Clib_Model_Category_Category) {
				$item['auth_step'] = $tmp_category->getAuthStep();
				$item['level'] = $tmp_category->getLevel();
			}

			// 상품할인 가격 표시
			if ($displayCfg['displayType'] === 'discount') {
				$goodsDiscount = '';
				if ($item['use_goods_discount'] === '1') {
					$goodsDiscount = $goodsDiscountModel->getDiscountAmountSearch($item);
				}
				if ($goodsDiscount) {
					$item['oriPrice'] = $item['price'];
					$item['goodsDiscountPrice'] = $item['price'] - $goodsDiscount;
				}
				else {
					$item['oriPrice'] = '0';
					$item['goodsDiscountPrice'] = $item['price'];
				}
			}
			
			// 가격, 상품명, 기타정보(쿠폰, 아이콘, 적립금 등) 출력 여부
			$item['is_open_price'] = (($item['level'] == 0 || Clib_Application::session()->getMemberLevel() >= $item['level']) || $item['auth_step'][2] == 'Y') ? true : false;
			$item['is_open_extra'] = ($item['level'] == 0 || Clib_Application::session()->getMemberLevel() >= $item['level']) ? true : false;
			$item['is_open_name'] = (($item['level'] == 0 || Clib_Application::session()->getMemberLevel() >= $item['level']) || $item['auth_step'][1] == 'Y') ? true : false;


			// 각 출력 여부에 따른 처리
			if ($item['is_open_extra'] === false) {
				$item['icon'] = '';
				$item['coupon'] = '';
			}
			else {
				if ($item['runout'] && $cfg_soldout['icon'] == '0')
					$item['icon'] = '';
				if ($item['runout'] && $cfg_soldout['coupon'] == '0')
					$item['coupon'] = '';
			}

			// 품절상품 제어
			if ($item['runout'] || $item['usestock'] && $item['totstock'] < 1) {

				if ($cfg_soldout['mobile_display'] == 'overlay') {
					$item['css_selector'] = 'el-goods-soldout-image';
				}
		
				if ($cfg_soldout['mobile_display'] == 'none') {
					$item['css_selector'] = "";
				}

				if ($cfg_soldout['goodsnm'] == '0')
					$item['goodsnm'] = '';
			}

			if ($item['is_open_name'] === false || ($item['runout'] && $cfg_soldout['goodsnm'] == '0')) {
				$item['goodsnm'] = '';
			}

			if ($item['is_open_price'] === false) {
				$item['price'] = '';
			}

			if ($item['speach_description_useyn'] === 'y' && strlen($item['speach_description']) > 0) {
				$item['tts_url'] = Core::loader('TextToSpeach')->getURL($item['speach_description']);
			}
			else {
				$item['tts_url'] = '';
			}

			// 상품할인
			if($item['use_goods_discount']){
				$item['special_discount'] = $goodsDiscountModel->getDiscountUnit($item, Clib_Application::session()->getMemberLevel());
			}

			//카테고리 이미지
			if ($item['use_mobile_img'] === '1') {
				$item['img_html'] = goodsimgMobile($item['img_x'], $GLOBALS['cfg']['img_x'], 'class="'.$item['css_selector'].'"');
				$item['goodsImage'] = goodsimgMobile($item['img_x'], $GLOBALS['cfg']['img_x']);
			} else if ($item['use_mobile_img'] === '0') {
				$imgArr = explode('|', $item[$item['img_pc_x']]);
				$item['img_html'] = goodsimgMobile($imgArr[0], $GLOBALS['cfg']['img_x'], 'class="'.$item['css_selector'].'"');
				$item['goodsImage'] = goodsimgMobile($imgArr[0], $GLOBALS['cfg']['img_x']);
			} else {
				if (is_file('../../shop/data/goods/' . $item['img_s'])) {
					$item['img_html'] = goodsimgMobile($item['img_s'], 100, 'class="'.$item['css_selector'].'"');
					$item['goodsImage'] = goodsimgMobile($item['img_s'], 100);
				}
				else if (is_file('../../shop/data/goods/' . $item['img_mobile'])) {
					$item['img_html'] = goodsimgMobile($item['img_mobile'], 100, 'class="'.$item['css_selector'].'"');
					$item['goodsImage'] = goodsimgMobile($item['img_mobile'], 100);
				}
				else {
					$img_l_arr = explode("|", $item['img_l']);
					$item['img_html'] = goodsimgMobile($img_l_arr[0], 100, 'class="'.$item['css_selector'].'"');
					$item['goodsImage'] = goodsimgMobile($img_l_arr[0], 100);
				}
			}
		}

		return $collection;
	}

}
