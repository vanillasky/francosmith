<?php
class Clib_Controller_Admin_Goods extends Clib_Controller_Admin_Abstract
{
	private $_requests = array();

	private function _getExtraInformation()
	{
		$param = array(
			'title' => Clib_Application::request()->get('extra_info_title'),
			'descript' => Clib_Application::request()->get('extra_info_desc'),

			// 인터파크
			'inerparkCode' => Clib_Application::request()->get('extra_info_inpk_code'),
			'interparkType' => Clib_Application::request()->get('extra_info_inpk_type'),
		);

		return Clib_Application::iapi('goods_extra_information')->generateJson($param);

	}

	private function _normalizeRequest()
	{

		$this->_requests = array();

		$post = Clib_Application::request()->gets('post');

		if ( ! empty($post['category'])) {

			foreach ($post['category'] as $k => $v) {

				$_sort = ! $post['sort'][$k] || $post['sortTop'] ? G_CONST_NOW : $post['sort'][$k];

				$this->_requests['goods_link'][] = array(
					'category' => $post['category'][$k],
					'sort' => $_sort,
				);
			}
		}

		// 추가정보
		if($post[use_extra_field] == 0){
			unset($post['ex']);
			unset($post['title']);
		}

		for ($i = 0; $i < 6; $i++) {
			$post['ex' . ($i + 1)] = $post['ex'][$i];
		}
		$post['ex_title'] = preg_replace("/^(\|)+$/i", "", @implode("|", $post['title']));

		// 사입처
		global $purchaseSet;
		$ar_tmpPchs = array();

		if($purchaseSet['usePurchase'] == "Y") {

			// 수정일때 사입처 목록만 생성하고,
			if ($post['mode'] == 'modify') {

				$goodsOptions = Clib_Application::getCollectionClass('goods_option');
				$goodsOptions->addFilter('goodsno', $post['goodsno']);
				$goodsOptions->load();

				foreach($goodsOptions as $option) {
					$key = $option['opt1']."|^|^".$option['opt2'];
					$ar_tmpPchs[$key] = $option['pchsno'];
				}

			}
		}


		// 옵션 및 재고
		if ($post['opt'] && $post['use_option']) {

			$post['option_value'] = array();

			// 다수
			$keys = array_keys($post['option_price']);

			for ($i = 0, $m = sizeof($keys); $i < $m; $i++) {

				$key = $keys[$i];

				$_option = array(
					'price' => $post['option_price'][$key],
					'stock' => $post['option_is_deleted'][$key] ? 0 : $post['option_stock'][$key],
					'go_is_display' => $post['option_is_display'][$key] ? 1 : 0,
					'go_is_deleted' => $post['option_is_deleted'][$key] ? 1 : 0,
					'sno' => $post['option_sno'][$key],
					'reserve' => $post['option_reserve'][$key],
					'supply' => $post['option_supply'][$key],
					'consumer' => $post['option_consumer'][$key],
				);

				// 옵션 명이 없는 경우 처리 하지 않는다
				$_b = 0;
				foreach ($post['opt'] as $idx => $opt) {
					if ($opt[$key]) {
						$_b++;
						$_option['opt' . ($idx + 1)] = $opt[$key];
						if (!$_option['go_is_deleted']) {
							$post['option_value'][$idx][] = $opt[$key];
						}
					}
				}
				if ($_b == 0)
					continue;

				// 사입처
				$key = $_option['opt1']."|^|^".$_option['opt2'];
				$_option['pchsno'] = ($ar_tmpPchs[$key]) ? $ar_tmpPchs[$key] : $post['pchsno'];

				$this->_requests['goods_option'][] = $_option;

			}

			if (!empty($post['optnm'])) {
				$post['option_name'] = $post['optnm'];
			}

			$post['option_value'][0] = implode(',',array_unique($post['option_value'][0]));
			$post['option_value'][1] = implode(',',array_unique($post['option_value'][1]));

		}
		else {
			// 단일
			$_option = array(
				'price' => $post['goods_price'],
				'stock' => $post['totstock'],
				'go_is_display' => 1,
				'go_is_deleted' => '0',
				'sno' => $post['sno'] ? $post['sno'] : array_shift($post['option_sno']),
				'reserve' => $post['goods_reserve'],
				'supply' => $post['goods_supply'],
				'consumer' => $post['goods_consumer'],
			);

			// 사입처
			$key = $post['opt'][0][0]."|^|^".$post['opt'][1][0];
			$_option['pchsno'] = ($ar_tmpPchs[$key]) ? $ar_tmpPchs[$key] : $post['pchsno'];

			$this->_requests['goods_option'] = array(0 => $_option);
		}

		// 옵션 이미지 타입
		foreach($post['option_image_type'] as $k => $v) {
			$this->_requests['goods']['opt'.($k+1).'kind'] = $v;
		}

		// 총 판매재고
		$post['totstock'] = gd_array_sum($this->_requests['goods_option'], 'stock');

		// 상품 추가 정보
		$this->_requests['goods']['extra_info'] = $this->_getExtraInformation();

		// 옵션명, 옵션값
		$this->_requests['goods']['option_value'] = @implode("|", array_notnull($post['option_value']));
		$this->_requests['goods']['optnm'] = $this->_requests['goods']['option_name'] = $this->_requests['goods']['option_value'] ? @implode("|", array_notnull($post['option_name'])) : '';

		// 상품별 배송비
		$this->_requests['goods']['delivery_type'] = $post['delivery_type'];
		if ((int)$this->_requests['goods']['delivery_type'] > 1) {
			$this->_requests['goods']['goods_delivery'] = $post['goods_delivery' . (int)$this->_requests['goods']['delivery_type']];
		}
		else {
			$this->_requests['goods']['goods_delivery'] = 0;
		}

		// 회원 혜택 제외
		$this->_requests['goods']['exclude_member_reserve'] = $post['exclude_member_reserve'];
		$this->_requests['goods']['exclude_member_discount'] = $post['exclude_member_discount'];

		// 품절로 표시
		$this->_requests['goods']['runout'] = $post['runout'];

		// 상품 재입고 알림
		$this->_requests['goods']['use_stocked_noti'] = $post['use_stocked_noti'];

		// 주문시 재고량 빠짐
		$this->_requests['goods']['usestock'] = $post['usestock'];

		// 상품명 (상세페이지 메타태그에 입력)
		$this->_requests['goods']['meta_title'] = $post['meta_title'];

		// 아이콘
		$this->_requests['goods']['icon'] = @array_sum($post['icon']);

		// 추가 옵션
		if ( ! $post['use_add_option'])
			$post['additional_option']['selectable'] = array();
		if ( ! $post['use_add_input_option'])
			$post['additional_option']['inputable'] = array();
		$tmp = array();

		foreach ($post['additional_option'] as $type => $block) {
			// name, require
			// sno, value, addprice_operator, addprice, addno
			$type = strtoupper($type[0]);
			$idx = 0;

			foreach ($block['name'] as $step => $name) {

				$keys = array_keys($block['value'][$step]);

				for ($i = 0, $m = sizeof($keys); $i < $m; $i++) {

					$_addoption = array(
						'sno' => $block['sno'][$step][$i],
						'goodsno' => $post['goodsno'],
						'step' => $idx,
						'opt' => $block['value'][$step][$i],
						'addprice' => abs($block['addprice'][$step][$i]) * ($block['addprice_operator'][$step][$i] == '-' ? - 1 : 1),
						'addno' => $block['addno'][$step][$i],
						'stats' => 1,
						'type' => $type,
					);

					$this->_requests['goods_additional_option'][] = $_addoption;

				}

				$tmp[] = $name . "^" . $block['require'][$idx] . "^" . $type;

				$idx++;

			}

		}

		$this->_requests['goods']['addoptnm'] = @implode("|", $tmp);

		// 기간 할인
		$this->_requests['goods_discount'] = array();

		if ($post['use_goods_discount']) {

			$cutting = Clib_Application::iapi('number')->getCuttingConfigString($post['goods_discount_by_term_use_cutting'], $post['goods_discount_by_term_cutting_unit'], $post['goods_discount_by_term_cutting_method']);

			$_discount = array(
				'gd_start_date' => 0,
				'gd_end_date' => 0,
				'gd_cutting' => $cutting,
				'gd_level' => array(),
				'gd_amount' => array(),
				'gd_unit' => array(),
			);

			if ($post['goods_discount_by_term_range_date'][0]) {
				$_discount['gd_start_date'] = Core::helper('date')->min($post['goods_discount_by_term_range_date'][0] . $post['goods_discount_by_term_range_hour'][0] . $post['goods_discount_by_term_range_min'][0], false);
			}

			if ($post['goods_discount_by_term_range_date'][1]) {
				$_discount['gd_end_date'] = Core::helper('date')->max($post['goods_discount_by_term_range_date'][1] . $post['goods_discount_by_term_range_hour'][1] . $post['goods_discount_by_term_range_min'][1], false);
			}

			// 회원 그룹 지정
			if ($post['goods_discount_by_term_for_specify_member_group'] === '1') {

				foreach ($post['goods_discount_by_term_target'] as $k => $v) {
					$_discount['gd_level'][] = $post['goods_discount_by_term_target'][$k];
					$_discount['gd_amount'][] = preg_replace('/[^0-9\.]/', '', $post['goods_discount_by_term_amount'][$k]);
					$_discount['gd_unit'][] = $post['goods_discount_by_term_amount_type'][$k];
				}

				$_discount['gd_level'] = implode(',', $_discount['gd_level']);
				$_discount['gd_amount'] = implode(',', $_discount['gd_amount']);
				$_discount['gd_unit'] = implode(',', $_discount['gd_unit']);

			}
			// 회원 및 비회원 전체
			else if ($post['goods_discount_by_term_for_specify_member_group'] === '2') {
				$_discount['gd_level'] = '0';
				$_discount['gd_amount'] = $post['goods_discount_by_term_amount_for_nonmember_all'];
				$_discount['gd_unit'] = $post['goods_discount_by_term_amount_type_for_nonmember_all'];
			}
			// 회원 전체
			else {
				$_discount['gd_level'] = '*';
				$_discount['gd_amount'] = $post['goods_discount_by_term_amount_for_all'];
				$_discount['gd_unit'] = $post['goods_discount_by_term_amount_type_for_all'];
			}

			$this->_requests['goods_discount'] = $_discount;

		}

		// 배송방법
		$post['delivery_method'] = @implode('|', array_notnull($post['delivery_method']));

		// 판매기간
		$this->_requests['goods']['sales_range_start'] = 0;
		$this->_requests['goods']['sales_range_end'] = 0;

		if ($post['sales_range_date'][0]) {
			$this->_requests['goods']['sales_range_start'] = Core::helper('date')->min($post['sales_range_date'][0] . $post['sales_range_hour'][0] . $post['sales_range_min'][0], false);
		}

		if ($post['sales_range_date'][1]) {
			$this->_requests['goods']['sales_range_end'] = Core::helper('date')->max($post['sales_range_date'][1] . $post['sales_range_hour'][1] . $post['sales_range_min'][1], false);
		}

		// qr 코드
		if ($post['qrcode'] == 'y') {
			$this->_requests['qrcode'] = true;
		}
		else {
			$this->_requests['qrcode'] = false;
		}

		// 관련상품
		if ($post['relation']) {
			$this->_requests['related_goods'] = gd_json_decode(strip_tags($post['relation']));
			$post['relation'] = 'new_type';
		}
		else {
			$this->_requests['related_goods'] = array();
		}

		// 모바일샵 상품 노출 설정 가져오기
		$cfgMobileShop = Clib_Application::getLoadConfig('config.mobileShop');

		if($cfgMobileShop['vtype_goods'] == 1) {
			$this->_requests['goods']['open_mobile'] = $post['open_mobile'];
		}
		else {
			$this->_requests['goods']['open_mobile'] = $post['open'];
		}

		// @todo : 필드 mapping 배열 정의
		// @todo : 구현 중이기 때문에, DB 테이블을 읽어 오도록 해두었음
		$goodsColumns = array();
		$rs = Clib_Application::database()->query("desc gd_goods");
		while ($row = Clib_Application::database()->fetch($rs)) {
			$goodsColumns[$row['Field']] = '';
		}

		$excludeColumns = array(
			'inpk_prdno',
			'inpk_dispno',
		);

		foreach ($post as $k => $v) {
			if (isset($this->_requests['goods'][$k]) || in_array($k, $excludeColumns))
				continue;
			$this->_requests['goods'][$k] = $v;
		}
		$this->_requests['goods'] = array_intersect_key($this->_requests['goods'], $goodsColumns);

		if ( ! Clib_Application::request()->get('returnUrl')) {
			Clib_Application::request()->set('returnUrl', $_SERVER['HTTP_REFERER']);
		}

	}

	private function _checkGoodsImage($type)
	{
		static $upload = null;

		if ($upload == null) {
			$upload = Core::loader('upload_file');
		}

		if (Clib_Application::request()->file($type)) {
			$file_array = array();
			$file_array = reverse_file_array(Clib_Application::request()->file($type));
			foreach ($file_array as $k => $v) {
				$upload->upload_file($file_array[$k], '', 'image');
				if ( ! $upload->file_extension_check())
					return false;
				if ( ! $upload->file_type_check())
					return false;
			}
		}
		return true;

	}

	private function _imageCopy($key, $images)
	{

		static $now = null;

		if (is_null($now)) {
			$now = time();
		}

		$cfg = Clib_Application::getConfig('config');

		$_dir = SHOPROOT . '/data/goods/';
		$_dirT = $_dir . 't/';

		$len = ($key == "img_i" || $key == "img_s" || $key == "img_mobile") ? 1 : count($images['img_l']);
		if ($key == "opt1img" || $key == "opticon_a" || $key == "opticon_b")
			$len = count($images[$key]);

		### 이전 이미지 파일 삭제
		if ($key != "img_l" && $key != "opt1img" && $key != "opticon_a" && $key != "opticon_b") {
			for ($i = 0; $i < count($images[$key]); $i++) {
				@unlink($_dir . $images[$key][$i]);
				@unlink($_dirT . $images[$key][$i]);
				$images[$key][$i] = "";
			}
		}
		$images[$key] = array_notnull($images[$key]);

		for ($i = 0; $i < $len; $i++) {
			if ($key != "opt1img" && $key != "opticon_a" && $key != "opticon_b")
				$src = $images['img_l'][$i];
			else {
				$src = $images[$key]['name'][$i];
				if ($key == "opt1img")
					$cfg[$key] = $cfg['img_l'];
				else
					$cfg[$key] = 40;
			}

			if ($src && ! preg_match('/^http(s)?:\/\//', $src)) {

				$_ext = array_pop(explode(".", $src));
				$_key = substr($key, - 1, 1) . $i;
				$_rnd = mt_rand(0, 999);

				while (is_file($_dir . $now . $_rnd . $_key . "." . $_ext)) {
					$now++;
					$_rnd = mt_rand(0, 999);
				}

				$images[$key][$i] = $now . $_rnd . $_key . "." . $_ext;
				thumbnail($_dir . $src, $_dir . $images[$key][$i], $cfg[$key]);
				if ($key != "img_i" && $key != "img_s" && $key != "opticon_a" && $key != "opticon_b" && $key != "img_mobile")
					copy($_dirT . $src, $_dirT . $images[$key][$i]);

			}
		}

		return $images;
	}
	
	private function _mobileImageCopy($key, $images)
	{

		static $now = null;

		if (is_null($now)) {
			$now = time();
		}

		$cfg = Clib_Application::getConfig('config');
		if (!$cfg[img_w]) $cfg[img_w] = 200;
		if (!$cfg[img_x]) $cfg[img_x] = 200;
		if (!$cfg[img_y]) $cfg[img_y] = 300;
		if (!$cfg[img_z]) $cfg[img_z] = 500;

		$_dir = SHOPROOT . '/data/goods/';
		$_dirT = $_dir . 't/';

		$len = ($key == "img_w" || $key == "img_x") ? 1 : count($images['img_z']);
		if ($key == "opt1img" || $key == "opticon_a" || $key == "opticon_b")
			$len = count($images[$key]);

		### 이전 이미지 파일 삭제
		if ($key != "img_z" && $key != "opt1img" && $key != "opticon_a" && $key != "opticon_b") {
			for ($i = 0; $i < count($images[$key]); $i++) {
				@unlink($_dir . $images[$key][$i]);
				@unlink($_dirT . $images[$key][$i]);
				$images[$key][$i] = "";
			}
		}
		$images[$key] = array_notnull($images[$key]);

		for ($i = 0; $i < $len; $i++) {
			if ($key != "opt1img" && $key != "opticon_a" && $key != "opticon_b")
				$src = $images['img_z'][$i];
			else {
				$src = $images[$key]['name'][$i];
				if ($key == "opt1img")
					$cfg[$key] = $cfg['img_z'];
				else
					$cfg[$key] = 40;
			}

			if ($src && ! preg_match('/^http(s)?:\/\//', $src)) {

				$_ext = array_pop(explode(".", $src));
				$_key = substr($key, - 1, 1) . $i;
				$_rnd = mt_rand(0, 999);

				while (is_file($_dir . $now . $_rnd . $_key . "." . $_ext)) {
					$now++;
					$_rnd = mt_rand(0, 999);
				}

				$images[$key][$i] = $now . $_rnd . $_key . "." . $_ext;
				thumbnail($_dir . $src, $_dir . $images[$key][$i], $cfg[$key]);
				if ($key != "opticon_a" && $key != "opticon_b")
					copy($_dirT . $src, $_dirT . $images[$key][$i]);

			}
		}

		return $images;
	}

	private function _imageUpload()
	{

		$images = array();

		$keys = array(
			'i',
			's',
			'm',
			'l',
			'mobile',
			'w',
			'x',
			'y',
			'z',
		);

		if (Clib_Application::request()->get('image_attach_method') == 'url') {

			// 기 업로드된 파일은 모두 지운다.
			/*
			 $_del = array();

			 array_push($_del, $data['img_i']);
			 array_push($_del, $data['img_s']);
			 array_push($_del, $data['img_mobile']);

			 $_del = array_merge($_del, explode("|",$data['img_m']));
			 $_del = array_merge($_del, explode("|",$data['img_l']));

			 $_dir	= "../../data/goods/";		// 이미지
			 $_dirT	= "../../data/goods/t/";	// 썸네일

			 foreach($_del as $k => $f) {
			 if ($f == '' || $f == '.' || $f == '..') continue;
			 @unlink($_dir.$f);
			 @unlink($_dirT.$f);
			 }
			 */

			foreach ($keys as $postfix) {
				$images['img_' . $postfix] = array_notnull(Clib_Application::request()->get('url_' . $postfix));
			}

		}
		else {

			// 파일체크
			foreach ($keys as $postfix) {
				if ( ! $this->_checkGoodsImage('img_' . $postfix))
					msg('상품이미지 파일이 올바르지 않습니다.', - 1);
			}

			// 이미지 업로드
			foreach ($keys as $postfix) {
				$tmp = $postfix == 'm' || $postfix == 'y' ? Clib_Application::request()->get('detailView') : null;
				$images['img_' . $postfix] = multiUpload('img_' . $postfix, $tmp);
			}

			// 썸네일 생성
			foreach ($keys as $postfix) {
				if ($postfix == 'l' || $postfix == 'z')
					continue;
				if (Clib_Application::request()->get('copy_' . $postfix)) {
					if (in_array($postfix, array('w','x','y'))) {
						$images = $this->_mobileImageCopy('img_' . $postfix, $images);
					} else {
						$images = $this->_imageCopy('img_' . $postfix, $images);
					}
				}
			}

		}

		// set to request;
		foreach ($keys as $postfix) {

			if (empty($images['img_' . $postfix])) {
				//continue;
			}

			if ($postfix == 'i' || $postfix == 's' || $postfix == 'w' || $postfix == 'x' || $postfix == 'mobile') {
				$this->_requests['goods']['img_' . $postfix] = @array_shift($images['img_' . $postfix]);
			}
			else {
				$this->_requests['goods']['img_' . $postfix] = @implode('|', $images['img_' . $postfix]);
			}

		}

	}

	private function _optionImageUpload()
	{

		$data = $this->_getRequest('goods');

		// 옵션 이미지 업로드 및 변수 설정
		$images = array();
		$images['option_image'] = multiUpload('option_image');

		foreach (explode('|', $data['option_name']) as $k => $v) {
			$_key = $k > 1 ? 'n' : $k + 1;

			if ($data['opt' . $_key . 'kind'] == 'color') {
				$images['opt' . $_key . 'icon'] = Clib_Application::request()->get('option_color_'. $k);
			}
			else {
				$images['opt' . $_key . 'icon'] = multiUpload('option_icon_' . $k);
			}
		}

		// 삭제 체크된 옵션 이미지 및, 재 업로드된 옵션 이미지 삭제
		$goodsOption = Clib_Application::getModelClass('goods')->load($data['goodsno'])->getOptions();

		$option_image = $goodsOption->getImages();
		$opt1icon = $goodsOption->getNthIcons(1);
		$opt2icon = $goodsOption->getNthIcons(2);

		$delete = array();
		foreach((array)Clib_Application::request()->get('del') as $imageKey => $v) {
			switch ($imageKey) {
				case 'option_icon_0':
					$imageKey = 'opt1icon';
					break;
				case 'option_icon_1':
					$imageKey = 'opt2icon';
					break;
				case 'option_image':
					break;
			}
			$delete[$imageKey] = $v;
		}

		foreach(array('option_image','opt1icon','opt2icon') as $imageKey) {

			$tmp = (array)array_keys($delete[$imageKey]);

			if (!empty($images[$imageKey])) {
				$tmp = array_merge($tmp, array_keys($images[$imageKey]));
			}

			foreach(${$imageKey} as $k => $v) {

				$kk = get_js_compatible_key($k);

				if (array_search($kk, $tmp) !== false) {

					// 파일 삭제 처리
					@unlink('../../data/goods/'.$v);
					@unlink('../../data/goods/t/'.$v);

					if (!isset($images[$imageKey][$kk])) {
						$images[$imageKey][$kk] = '';
					}

				}
			}

		}

		for ($i = 0, $m = sizeof($this->_requests['goods_option']); $i < $m; $i++) {
			// opt1, opt2
			$_option = &$this->_requests['goods_option'][$i];

			foreach (array(1,2,'n') as $k => $v) {

				// 옵션 아이콘 이미지
				$tmp = array_keys($images['opt' . $v . 'icon']);

				foreach ($tmp as $v2) {
					$_v2 = get_js_compatible_key($_option['opt' . $v]);
					if ($_v2 == $v2) {
						$_option['opt' . $v . 'icon'] = $images['opt' . $v . 'icon'][$v2];
					}
				}

				// 옵션 이미지
				if ($v === 1) {
					$tmp = array_keys($images['option_image']);

					foreach ($tmp as $v2) {
						$_v2 = get_js_compatible_key($_option['opt' . $v]);
						if ((string)$_v2 === (string)$v2) {
							$_option['opt1img'] = $images['option_image'][$v2];
							break(1);
						}
					}
				}
			}
		}
	}

	private function _initSave()
	{
		$GLOBALS['data'] = Clib_Application::getModelClass('goods')->load(Clib_Application::request()->get('goodsno'))->getData();

		// normalize requests;
		$this->_normalizeRequest();

		// image upload;
		$this->_imageUpload();

		// option image upload;
		$this->_optionImageUpload();

		return $this;
	}

	private function _getRequest($key)
	{
		return $this->_requests[$key];
	}

	private function _save()
	{

		// start transaction;
		Clib_Application::database()->begin();

		try {

		/* 상품 정보 저장 */

			// 네이버 지식쇼핑
			naver_goods_diff_check();
			
			// 다음 쇼핑하우
			daum_goods_diff_check();

			// create model instance;
			$goods = Clib_Application::getModelClass('goods');

			$data = $this->_getRequest('goods');

			// update or insert;
			if ($goodsno = Clib_Application::request()->get('goodsno')) {
				$goods->load($goodsno);
				$goods->setData('updatedt', Core::helper('date')->now());

				$ar_update=array(
					'goodsnm'=>$data[goodsnm],
					'brandno'=>$data[brandno],
					'origin'=>$data[origin],
					'maker'=>$data[maker],
					'launchdt'=>date("Y-m-d",strtotime($data[launchdt])),
					'delivery_type'=>$data[delivery_type],
					'goods_delivery'=>$data[goods_delivery],
					'use_emoney'=>$data[use_emoney],
					'price'=>$data[goods_price],
					'runout'=>$data[runout],
					'open'=>$data[open],
					'hidden'=>$data[hidden],
					'usestock'=>$data[usestock],
					'stock'=>$data[totstock],
					'reserve'=>$data[goods_reserve],
					'img_l'=> array_shift(explode('|',$data[img_l])),
					'naver_event'=>$data['naver_event'],
					'use_only_adult'=>$data['use_only_adult'],
					'model_name'=>$data['model_name'],
					'sales_range_start'=>$data['sales_range_start'],
					'sales_range_end'=>$data['sales_range_end'],
				);

				if (($category = $goods->categories->getIterator()->current()) instanceof Clib_Model_Goods_Link) {

					$link = $this->_getRequest('goods_link');

					if ($category->getCategory() != $link[0]['category']) {
						$ar_update['category'] = $link[0]['category'];
					}
				}

				naver_goods_diff($goods->getId(),$ar_update);
				$ar_update['discount'] = $this->_getRequest('goods_discount');	// 상품별 할인
				daum_goods_diff($goods->getId(),$ar_update);
			}
			else {
				$goods->createNew();

				naver_goods_diff($goods->getId(),array(),"I");
				daum_goods_diff($goods->getId(),$data,"I");
			}

			unset($data['goodsno']);
			$goods->addData($data);
			$goods->save();


		/* 관련 상품 */

			// create model instances;
			$related = Clib_Application::getModelClass('goods_related');
			$datas = $this->_getRequest('related_goods');

			$related->deleteAll($goods->getId());


			foreach ($datas as $sort => $data) {

				$related->resetData();
				$related->setData('goodsno', $goods->getId());
				$related->setData('sort', $sort);
				$related->setData('r_type', $data['r_type']);
				$related->setData('r_goodsno', $data['goodsno']);
				$related->setData('r_start', (!empty($data['r_start']) ? $data['r_start'] : null));
				$related->setData('r_end',  (!empty($data['r_end']) ? $data['r_end'] : null));
				$related->setData('regdt',  $data['r_regdt']);

				$related->save();

			}


		/* 선택 옵션 */

			// create model instances;
			$option = Clib_Application::getModelClass('goods_option');
			$datas = $this->_getRequest('goods_option');

			// 단일 상품인경우 기존 옵션은 모두 삭제처리 함
			if (! $goods['use_option']) {

				$datas = array_slice($datas, 0, 1);
				$datas[0]['opt1'] = '';
				$datas[0]['opt2'] = '';

				$goods->setData('optnm', '');
				$goods->setData('option_name', '');
				$goods->setData('option_value', '');

				foreach($goods->getOptions() as $_option) {
					if ($_option->getId() != $datas[0]['sno']) {
						$_option->delete();
					}
				}
			}

			// loop
			$keys = array_keys($datas);

			$link = 0;

			for ($i = 0, $m = sizeof($keys); $i < $m; $i++) {

				$data = $datas[$keys[$i]];

				// option
				$option->resetData();

				if ($data['sno']) {
					$option->load($data['sno']);
				}

				if (! $option->hasLoaded()) {
					$option->createNew($goods->getId());
					// 상품 ID 를 이용하여 빈 옵션을 집어 넣는다.
				}

				unset($data['sno']);
				$option->addData($data);

				if ($link === 0 && $data['go_is_display'] && ! $data['go_is_deleted']) {
					$option->setData('link', $link = 1);

					$goods->setData('goods_price', $option['price']);
					$goods->setData('goods_consumer', $option['consumer']);
					$goods->setData('goods_supply', $option['supply']);
					$goods->setData('goods_reserve', $option['reserve']);
				}
				else {
					$option->setData('link', 0);
				}
				$option->save();

			}

			if ($link === 0) {
				// 옵션 사용안함으로 상품 저장
				$goods->setData('use_option', 0);
			}

			// 상품 데이터가 변경 됐으면 저장.
			if ($goods->hasChanged()) {
				$goods->save();
			}

		/* 카테고리 */

			// 등록할 or 등록된 연결 정보를 제외하고 모두 삭제
			$datas = $this->_getRequest('goods_link');

			$goodsLink = Clib_Application::getModelClass('goods_link');
			$goodsLink->deleteExclude($goods->getId(), $datas);

			// load collection
			$categories = $goodsLink->getCollection();
			$categories->addFilter('goodsno', $goodsno);
			$categories->setOrder('category', 'DESC');
			$categories->load();

			// 정렬
			$goodsSort = Core::loader('GoodsSort');
			$goodsLinkSort = array();
			$shouldBeOptimized = array();
			$maxSortIncrease = array();
			$linkSortIncrease = array();

			foreach ($categories->toArray() as $data) {
				for ($length = 3; $length <= strlen($data['category']); $length += 3) {
					$goodsLinkSort[substr($data['category'], 0, $length)] = $data['sort'.($length/3)];
				}
			}
			// 모바일샵 상품 노출 설정 가져오기
			$cfgMobileShop = Clib_Application::getLoadConfig('config.mobileShop');

			foreach ($datas as $data) {

				$hidden = getCateHideCnt($data['category']) > 0 ? 1 : 0;
				if ($cfgMobileShop['vtype_category'] == 1) {
					$hidden_mobile = getCateHideCnt($data['category'], 'mobile') > 0 ? 1 : 0;
				}
				else {
					$hidden_mobile = $hidden;
				}

				$_goodsLink = clone $goodsLink;

				$_goodsLink->resetData();
				$_goodsLink->setData('goodsno', $goods->getId());
				$_goodsLink->setData('category', $data['category']);
				$_goodsLink->setData('hidden', $hidden);
				$_goodsLink->setData('hidden_mobile', $hidden_mobile);
				$_goodsLink->setData('sort', abs($data['sort']) * - 1);

				// 카테고리별 정렬 최대치 갱신 (수동 진열 설정 중인 카테고리만)
				foreach ($goodsSort->getManualSortInfoHierarchy($data['category']) as $categorySortSet) {
					if (strlen($data['category']) / 3 >= $categorySortSet['depth']) {
						if ($goodsLinkSort[$categorySortSet['category']]) {
							$_goodsLink->setData($categorySortSet['sort_field'], $goodsLinkSort[$categorySortSet['category']]);
						}
						else {
							if ($categorySortSet['manual_sort_on_link_goods_position'] === 'FIRST') {
								if (isset($linkSortIncrease[$categorySortSet['category']]) === false) {
									$goodsSort->increaseCategorySort($categorySortSet['category'], $categorySortSet['sort_field']);
									$linkSortIncrease[$categorySortSet['category']] = true;
								}
								$_goodsLink->setData($categorySortSet['sort_field'], 1);
							}
							else {
								$_goodsLink->setData($categorySortSet['sort_field'], ((int)$categorySortSet['sort_max'] + 1));
							}
							// 새로 연결된 카테고리들을 저장해두고 하단에서 새로 연결된 각 카테고리정보를 갱신한다
							$maxSortIncrease[$categorySortSet['category']] = true;
						}
					}
				}

				// 상단 고정 사용시 정렬값 변경
				if (Clib_Application::request()->get('sortTop')) {
					if (($sortField = $goodsSort->getSortField($data['category'])) !== 'sort') {
						$_goodsLink->setData($sortField, 0);
						// 상단 고정 상품이 있는 카테고리는 아래에서 재정렬한다.
						$shouldBeOptimized[] = $data['category'];
					}
				}

				if ($categories->mergeIntoExistItem($_goodsLink)) {
					// 합쳐짐
				}
				else {
					$categories->addItem($_goodsLink);
				}

			}

			// 새로 연결된 각 카테고리들의 sort#값의 최대치를 증가시킴
			foreach (array_keys($maxSortIncrease) as $category) $goodsSort->increaseSortMax($category);

			$categories->save();

			// 상단 고정 진열시, 동일 카테고리내 정렬 순서를 맞춤.
			// 카테고리 정보가 저장된 이후에 실행되야 함.
			foreach ($shouldBeOptimized as $category) {
				$goodsSort->optimizeManualSort($category);
			}

		/* 이벤트 진열 등록 */
			foreach($categories as $category) {
				if ($category->event->hasLoaded()) {

					$mode = $category->event->getDisplayId();

					$displays = Clib_Application::getCollectionClass('goods_display');
					$displays->addFilter('mode' , $mode);
					$displays->addFilter('goodsno', $goods->getId());

					if (!$displays->getTotalCount()) {

						$display = Clib_Application::getModelClass('goods_display');
						$display->setData(array(
							'goodsno' => $goods->getId(),
							'mode' => $mode,
							'sort' => $display->getMaxSortNum($mode) + 1,
						));

						$display->save();
					}
				}
			}


		/* 추가 옵션 (입력형, 선택형) */

			$datas = $this->_getRequest('goods_additional_option');

			$additionalOption = Clib_Application::getModelClass('goods_additional_option');
			$additionalOption->initStatus($goods->getId());

			foreach ($datas as $data) {

				$additionalOption->resetData();

				if ($data['sno']) {
					$additionalOption->load($data['sno']);
				}
				else {
					$additionalOption->createNew(array('goodsno' => $goods->getId()));
				}

				$data['sno'] = $additionalOption->getId();
				$data['addno'] = $data['addno'] ? $data['addno'] : $data['sno'];
				$data['goodsno'] = $goods->getId();
				$additionalOption->addData($data);
				$additionalOption->save();

			}

			$additionalOption->deleteUnnecessary($goods->getId());

		/* 상품별 할인 */

			$data = $this->_getRequest('goods_discount');

			$discount = Clib_Application::getModelClass('goods_discount');
			$discount->load($goods->getId());

			if ( ! empty($data)) {

				foreach ($data as $k => $v) {
					$discount->setData($k, $v);
				}

				if ( ! $discount->hasLoaded()) {
					$discount->setId($goods->getId());
				}

				$discount->save();

			}
			else {
				// delete;
				$discount->delete();

			}



		/* QR 코드 */

			$qr = Clib_Application::getModelClass('qrcode');
			$qr->deleteGoodsCode($goods->getId());

			if ($this->_getRequest('qrcode')) {
				$qr->resetData();

				$qr->setData('qr_type', 'goods');
				$qr->setData('contsNo', $goods->getId());
				$qr->setData('qr_string', '');
				$qr->setData('qr_name', 'event qr code');
				$qr->setData('qr_size', '');
				$qr->setData('useLogo', '');
				$qr->setData('regdt', Core::helper('date')->format(G_CONST_NOW));
				$qr->save();
			}



			// commit;
			Clib_Application::database()->commit();
		}
		catch (Clib_Exception $exception) {

			// rollback;
			Clib_Application::database()->rollback();

			throw $exception;
		}

		return $goods;

	}

	public function save()
	{

		if (Clib_Application::request()->getMethod() != 'POST') {
			throw new Clib_Exception('admin action is allow post method only.');
		}

		$post = Clib_Application::request()->gets('post');

		// validate;
		$form = Clib_Application::form('admin_goods_register');
		if ($form->validate($post) === true) {

			// success;
			$this->_initSave();
			$goods = $this->_save();
			$goodsno = $goods->getId();

			### 사입처 관련 - 등록시만 적용
			if($post['mode'] == "register" && $purchaseSet['usePurchase'] == "Y") {
				list($chkOneOption) = Clib_Application::database()->fetch("SELECT COUNT(sno) FROM ".GD_GOODS_OPTION." WHERE goodsno = '$goodsno' and go_is_deleted <> '1'");
				if($post['pchsno'] && $post['pchs_stock'] && $post['pchs_pchsdt'] && $chkOneOption == 1) {
					$post['pchs_pchsdt'] = substr($post['pchs_pchsdt'], 0, 4)."-".substr($post['pchs_pchsdt'], 4, 2)."-".substr($post['pchs_pchsdt'], 6, 2);

					Clib_Application::database()->query("UPDATE ".GD_GOODS_OPTION." SET stock = stock + ".$post['pchs_stock'].",pchsno = '".$post['pchsno']."' WHERE goodsno = '$goodsno'");
					list($CurTotStock) = Clib_Application::database()->fetch("SELECT SUM(stock) FROM ".GD_GOODS_OPTION." WHERE goodsno = '$goodsno' AND go_is_deleted <> '1'");
					Clib_Application::database()->query("UPDATE ".GD_GOODS." SET totstock = totstock + ".$post['pchs_stock']." WHERE goodsno = '$goodsno'");
					Clib_Application::database()->query("INSERT INTO ".GD_PURCHASE_GOODS." SET goodsno = '$goodsno', goodsnm = '".$goods['goodsnm']."', img_s = '".$goods['img_s']."', pchsno = '".$post['pchsno']."', p_stock = '".$post['pchs_stock']."', p_price = '".$post['supply']."', pchsdt = '".$post['pchs_pchsdt']."', regdt = NOW()");
					Clib_Application::database()->query("DELETE FROM ".GD_PURCHASE_SMSLOG." WHERE goodsno = '$goodsno'");
				}

				if($post['purchaseApplyOption'] == "1") {
					$sql = "SELECT * FROM ".GD_GOODS_OPTION." WHERE goodsno = '$goodsno' AND go_is_deleted <> '1'";
					$rs = Clib_Application::database()->query($sql);
					for($i = 0; $data = Clib_Application::database()->fetch($rs); $i++) {
						$sql_pchs = "INSERT INTO ".GD_PURCHASE_GOODS." SET goodsno = '$goodsno', goodsnm = '".$goods['goodsnm']."', img_s = '".$goods['img_s']."', opt1 = '".$data['opt1']."', opt2 = '".$data['opt2']."', pchsno = '".$post['pchsno']."', p_stock = '".$data['stock']."', p_price = '".$data['supply']."', pchsdt = '".$post['pchs_pchsdt']."', regdt = NOW()";
						Clib_Application::database()->query($sql_pchs);
					}
				}
				else if($post['purchaseApplyOption'] == "2") {
					msg("상품이 등록되었습니다.\\n입고 상품 등록 페이지로 이동합니다.");

					$returnUrl = "../goods/purchase_goods.php?skey=G.goodsno&sword=$goodsno&pchsDefType=comnm&pchsDefVal=미등록";
				}
			}

			// 인터파크로 상품 정보 전송 페이지 or 사입처 등록 페이지로 이동
			global $inpkCfg, $inpkOSCfg, $purchaseSet;
			if ($inpkCfg['use'] == 'Y' || $inpkOSCfg['use'] == 'Y') {
				// 전시코드는 상품API 등록전에만 수정가능
				if ($goods['inpk_prdno'] == '' && Clib_Application::request()->get('inpk_dispno')) {
					$goods->setData('inpk_dispno', Clib_Application::request()->get('inpk_dispno'));
					$goods->save();
				}

				// 전송 페이지로 리디렉션
				$returnUrl = '../interpark/transmit_action.php?goodsno[]=' . $goods->getId() . '&returnUrl=' . Clib_Application::request()->get('returnUrl');

				if (Clib_Application::request()->get('popup')) {
					$returnUrl .= '&popup=1';
				}

				go($returnUrl);
			}
			else if (Clib_Application::request()->get('mode') == "modify" && $purchaseSet['usePurchase'] == "Y") {

				$returnUrl = "./purchase_goods.php?skey=G.goodsno&sword=$goodsno";

				if (Clib_Application::request()->get('popup')) {
					echo "<script>opener.location.href=\"".$returnUrl."\";self.close();</script>";
				}

			}
			else if (Clib_Application::request()->get('popup')) {
				$returnUrl = '../goods/adm_popup_goods_form.php?mode=modify&popup=1&goodsno=' . $goods->getId();
			}
			else {
				$returnUrl = Clib_Application::request()->get('returnUrl');
				//$returnUrl = '../goods/adm_goods_form.php?mode=modify&goodsno=' . $goods->getId();
			}

			// 페이지캐시 초기화
			$templateCache = Core::loader('TemplateCache');
			$templateCache->clearCacheByClass('goods');

			echo '
			<script>
				parent.location.replace("'.$returnUrl.'");
			</script>
			';
		}
		else {
			throw new Clib_Exception('bad request');
		}

	}

	public function resetOption()
	{
		// normalize requests;
		$this->_normalizeRequest();

		// start transaction;
		Clib_Application::database()->begin();

		try {

			// create model instance;
			$goods = Clib_Application::getModelClass('goods')->load(Clib_Application::request()->get('goodsno'));

			if ( ! $goods->hasLoaded()) {
				throw new Clib_Exception('상품 정보가 없습니다.');
			}

			$data = $this->_getRequest('goods');

			$goods->setData('option_name', $data['option_name']);
			$goods->setData('optnm', $data['optnm']);
			$goods->setData('option_value', $data['option_value']);
			$goods->setData('use_option', $data['use_option']);
			$goods->setData('opttype', $data['opttype']);
			$goods->setData('totstock', $data['totstock']);

			$goods->save();
			$goods->delOptions();

			// create model instances;
			$option = Clib_Application::getModelClass('goods_option');
			$datas = $this->_getRequest('goods_option');

			// loop
			$keys = array_keys($datas);

			$link = 0;

			for ($i = 0, $m = sizeof($keys); $i < $m; $i++) {

				$data = $datas[$keys[$i]];

				// option
				$option->resetData();
				$option->createNew($goods->getId());
				// 상품 ID 를 이용하여 빈 옵션을 집어 넣는다.

				unset($data['sno']);
				$option->addData($data);

				if ($link === 0 && $data['go_is_display'] && ! $data['go_is_deleted']) {
					$option->setData('link', $link = 1);

					$goods->setData('goods_price', $option['price']);
					$goods->setData('goods_consumer', $option['consumer']);
					$goods->setData('goods_supply', $option['supply']);
					$goods->setData('goods_reserve', $option['reserve']);
				}
				else {
					$option->setData('link', 0);
				}

				$option->save();

			}

			if ($link === 0) {
				// 옵션 사용안함으로 상품 저장
				$goods->setData('use_option', 0);
			}

			// 상품 데이터가 변경 됐으면 저장.
			if ($goods->hasChanged()) {
				$goods->save();
			}

			// 상품의 총 재고, 판매가격, 정가, 매입가, 적립금 갱신
			//resetOption

			// commit;
			Clib_Application::database()->commit();

		}
		catch (Clib_Exception $exception) {

			// rollback;
			Clib_Application::database()->rollback();

			throw $exception;
		}

	}

}
