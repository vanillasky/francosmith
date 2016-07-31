<?php
class orderDeliveryItem {
	var $total_delivery_price;
	var $ordno;

	function orderDeliveryItem($ordno = null)
	{
		$this->ordno = $ordno;
		$this->db = $GLOBALS['db'];
	}

	function extra_delivery($ordno, $extra_price, $prn_extra_price)
	{
		$areaDeliveryData						= array();
		$areaDeliveryData['ordno']				= $ordno;
		$areaDeliveryData['delivery_price']		= $extra_price;
		$areaDeliveryData['prn_delivery_price']	= $prn_extra_price;
		$areaDeliveryData['delivery_type']		= 100;

		$area = &load_class('areaDelivery','areaDelivery');
		$extra_fee = $area->getPay();

		if($extra_fee > 0) $areaDeliveryData['conditional_price']		= $extra_fee;

		$query = $this->db->_query_print('INSERT INTO '.GD_ORDER_ITEM_DELIVERY.' SET [cv]', $areaDeliveryData);
		$res = $this->db->_query($query);

		$db_idx = $this->db->_last_insert_id();
		$this->delivery_data_log('a', $areaDeliveryData['prn_delivery_price'], $db_idx);
		return $db_idx;
	}

	/*
	*	주문상품별 배송비 내역 분류
		$data = shop/lib/lib.func.php	getDeliveryMode()
		set_delivery_data($data)
			Array
			(
				[price] => 총 배송비
				[extra_price] => 0
				[free] => 기본배송 무료조건값
				[default_price] => 기본배송 배송비금액
				[order_delivery_item] => Array
					(
						[배송타입코드] => Array
							(
								[상품번호] => 배송비금액
							)
						[배송타입코드] => Array //무료배송인 경우
							(
								[상품번호][옵션코드] => 배송비금액
							)
					)
			)
	*/
	function set_delivery_data($data)
	{
		$set_data = $data['order_delivery_item'];
		$this->total_delivery_price = $data['price'];
		if(is_array($set_data) === false || empty($set_data)) return false;

		//지역별 추가 배송비
		if($data['extra_price'] > 0){
			$area_idx = $this->extra_delivery($this->ordno, $data['extra_price'], $data['extra_price']);
			$rtn['area_idx'] = $area_idx;
		}

		foreach($set_data as $delivery_type => $delivery_data) {
			if($delivery_type == 0) {
				foreach($delivery_data as $goods_no => $basic_price) {
					if(!isset($db_idx)) {
					$ins_data['ordno'] = $this->ordno;
					$ins_data['delivery_price'] = $basic_price;
					$ins_data['prn_delivery_price'] = $ins_data['delivery_price'];
					$ins_data['delivery_type'] = $delivery_type;
					$ins_data['conditional_price'] = $data['free'];

					$query = $this->db->_query_print('INSERT INTO '.GD_ORDER_ITEM_DELIVERY.' SET [cv]', $ins_data);
					$res = $this->db->_query($query);
					$db_idx = $this->db->_last_insert_id();

					$this->delivery_data_log('a', $ins_data['prn_delivery_price'], $db_idx);
					}

					$rtn[$goods_no] = $db_idx;
				}
			}
			else {
				foreach($delivery_data as $goods_no => $delivery_price) {
					if($delivery_type == 1) {
						if(is_array($delivery_price)) {
							foreach($delivery_price as $optno => $free_price) {
								$ins_data['ordno'] = $this->ordno;
								$ins_data['delivery_price'] = $free_price;
								$ins_data['prn_delivery_price'] = $ins_data['delivery_price'];
								$ins_data['delivery_type'] = $delivery_type;

								$goods_query = $this->db->_query_print('SELECT goods_delivery FROM '.GD_GOODS.' WHERE goodsno=[s]', $goods_no);
								$goods_res = $this->db->fetch($goods_query);

								$ins_data['conditional_price'] = $goods_res['goods_delivery'];

								$query = $this->db->_query_print('INSERT INTO '.GD_ORDER_ITEM_DELIVERY.' SET [cv]', $ins_data);

								$res = $this->db->_query($query);
								$db_idx = $this->db->_last_insert_id();

								$this->delivery_data_log('a', $ins_data['prn_delivery_price'], $db_idx);

								$rtn[$goods_no][$optno] = $db_idx;
								unset($ins_data, $db_idx);
							}
						}
						else {
							$ins_data['ordno'] = $this->ordno;
							$ins_data['delivery_price'] = $delivery_price;
							$ins_data['prn_delivery_price'] = $ins_data['delivery_price'];
							$ins_data['delivery_type'] = $delivery_type;

							$goods_query = $this->db->_query_print('SELECT goods_delivery FROM '.GD_GOODS.' WHERE goodsno=[s]', $goods_no);
							$goods_res = $this->db->fetch($goods_query);

							$ins_data['conditional_price'] = $goods_res['goods_delivery'];

							$query = $this->db->_query_print('INSERT INTO '.GD_ORDER_ITEM_DELIVERY.' SET [cv]', $ins_data);

							$res = $this->db->_query($query);
							$db_idx = $this->db->_last_insert_id();

							$this->delivery_data_log('a', $ins_data['prn_delivery_price'], $db_idx);

							$rtn[$goods_no] = $db_idx;
							unset($ins_data, $db_idx);
						}
					}
					else {
						$ins_data['ordno'] = $this->ordno;
						$ins_data['delivery_price'] = $delivery_price;
						$ins_data['prn_delivery_price'] = $ins_data['delivery_price'];
						$ins_data['delivery_type'] = $delivery_type;

						$goods_query = $this->db->_query_print('SELECT goods_delivery FROM '.GD_GOODS.' WHERE goodsno=[s]', $goods_no);
						$goods_res = $this->db->fetch($goods_query);

						$ins_data['conditional_price'] = $goods_res['goods_delivery'];

						$query = $this->db->_query_print('INSERT INTO '.GD_ORDER_ITEM_DELIVERY.' SET [cv]', $ins_data);

						$res = $this->db->_query($query);
						$db_idx = $this->db->_last_insert_id();

						$this->delivery_data_log('a', $ins_data['prn_delivery_price'], $db_idx);

						$rtn[$goods_no] = $db_idx;
					}
				}
			}
			unset($ins_data, $db_idx);
		}

		return $rtn;
	}

	function delivery_data_log($log_type, $delivery_price, $db_idx)
	{
		$log_data['oi_delivery_idx'] = $db_idx;
		$log_data['delivery_price'] = $delivery_price;
		$log_data['log_type'] = $log_type;
		$query = $this->db->_query_print('INSERT INTO '.GD_ORDER_ITEM_DELIVERY_LOG.' SET [cv], change_date=now()', $log_data);
		$this->db->_query($query);
	}

	/* 취소로 배송비 환불내역 저장 */
	function update_delivery_data($arr_data)
	{
		if(!empty($arr_data)) {
			foreach($arr_data as $sno => $price) {
				$check = $this->db->fetch('SELECT * FROM '.GD_ORDER_ITEM_DELIVERY.' WHERE oi_delivery_idx='.$sno, true);
				if($check['prn_delivery_price'] >= $price) {
					$query = $this->db->_query_print('UPDATE '.GD_ORDER_ITEM_DELIVERY.' SET prn_delivery_price = (prn_delivery_price - [i]) WHERE oi_delivery_idx=[i]', $price, $sno);
					$this->db->_query($query);

					$this->delivery_data_log('m', $price, $sno);
				}
				else {
					$this->delivery_data_log('e', $price, $sno);
				}
			}
		}
	}

	function getDeliveryConf()
	{
		if(empty($this->delivery_set)) {
			$query = $this->db->_query_print('SELECT add_extra_fee_duplicate_free, add_extra_fee_duplicate_fixEach, add_extra_fee_duplicate_each, freeDelivery, goodsDelivery from '.GD_ORDER.' WHERE ordno=[i]', $this->ordno);
			$this->delivery_set = $this->db->fetch($query);
		}
	}

	/*
	 * 기본배송비가 전체 취소되는지 체크
	 * 취소에 해당되는 고정배송비 상품이 모두 취소되는지 체크
	*/
	function checkAllCancel($sno, $item, $delivery_type)
	{
		//배송타입 전체 배송비 조회
		if(($delivery_type == '0') || ($delivery_type == '4' && $this->delivery_set['add_extra_fee_duplicate_fixEach'] == '0') || ($delivery_type == '1')) {
			$delivery_query = $this->db->_query_print('SELECT * FROM '.GD_ORDER_ITEM_DELIVERY.' WHERE ordno=[i] AND delivery_type=[i]', $item['ordno'], $delivery_type);//기본배송비
		}
		else $delivery_query = $this->db->_query_print('SELECT * FROM '.GD_ORDER_ITEM_DELIVERY.' WHERE ordno=[i] AND delivery_type=[i] AND oi_delivery_idx=[i]', $item['ordno'], $delivery_type, $item['oi_delivery_idx']);//고정배송비
		$delivery_result = $this->db->_select($delivery_query, true);

		$rtn_bool = false;// true=배송비계산, false=배송비계산 안함

		foreach($delivery_result as $dres) {
			// 배송idx 에 포함되는 item조회
			$item_query = $this->db->_query_print('SELECT * FROM '.GD_ORDER_ITEM.' WHERE ordno=[i] AND oi_delivery_idx=[i] AND (cancel=0 OR NOT cancel=[i])', $item['ordno'], $dres['oi_delivery_idx'], $sno);
			$item_result = $this->db->_select($item_query, true);

			if(!empty($item_result)) {
				foreach($item_result as $ires) {
					// 취소상태가 아닌 주문이 있는 경우 false
					if($ires['cancel'] == 0) {
						return false;
						break;
					}
					else {
						$cancel_query = $this->db->_query_print('SELECT * FROM '.GD_ORDER_CANCEL.' WHERE sno=[i] AND pgcancel=[s]', $ires['cancel'], 'n');
						$cancel_result = $this->db->fetch($cancel_query, true);
						if(!empty($cancel_result)) return false;//취소상태 이지만 아직 pg취소가 아닌 경우 false
						else {
							$rtn_bool = true;
						}
					}
				}
			}
			else $rtn_bool = true;
		}

		if($rtn_bool === true) {
			$rtn_bool = $this->DeliveryAllCancelCheck($item['sno'], $item['ordno'], $item['oi_delivery_idx']);
		}

		return $rtn_bool;
	}

	/*
	 * 기본배송/고정비송이 부분취소로 각각 취소완료된 상태에서
	 * 취소정보 조회(주문상세페이지)시 중복계산을 방지함
	*/
	function DeliveryAllCancelCheck($sno, $ordno, $oi_delivery_idx)
	{
		$rtn_bool = true;

		$item_query = $this->db->_query_print('SELECT i.sno, i.cancel, i.istep FROM '.GD_ORDER_ITEM.' i LEFT JOIN '.GD_ORDER_CANCEL.' c ON i.cancel=c.sno WHERE i.ordno=[i] AND i.oi_delivery_idx=[i] ORDER BY ccdt DESC', $ordno, $oi_delivery_idx);
		$item_result = $this->db->_select($item_query, true);
		$item_sno = 0;

		foreach($item_result as $item_data) {

			if($item_data['istep'] === '44') {
				if($item_sno < 1) {
					$item_sno = $item_data['sno'];
					$cancel_complet = true;
				}
			}
			else {
				$cancel_complet = false;
				break;
			}
		}

		// 마지막 item 번호와 다른 경우 false
		if($sno != $item_sno && $cancel_complet === true) $rtn_bool = false;
		return $rtn_bool;
	}

	function allItemCancel($sno, $item)
	{
		$item_bool = false;

		$item_query = $this->db->_query_print('SELECT * FROM '.GD_ORDER_ITEM.' WHERE ordno=[i] AND goodsno=[i] AND opt1=[s] AND opt2=[s] AND NOT cancel=[i]', $item['ordno'], $item['goodsno'], $item['opt1'], $item['opt2'], $sno);
		$item_result = $this->db->_select($item_query, true);

		if(empty($item_result)) return true;

		foreach($item_result as $ires) {
			if($ires['cancel'] == 0) {
				return false;
			}
			else {
				$cancel_query = $this->db->_query_print('SELECT * FROM '.GD_ORDER_CANCEL.' WHERE sno=[i] AND pgcancel=[s]', $ires['cancel'], 'n');
				$cancel_result = $this->db->fetch($cancel_query, true);
				if(!empty($cancel_result)) return false;//취소상태 이지만 아직 pg취소가 아닌 경우 false
				else $item_bool = true;
			}
		}

		return $item_bool;
	}

	/* 고정배송비 - 상품별 전체 취소되었는지 체크 */
	function allFixDeliveryCancel($cancel_sno, $item, $goodsno)
	{
		$fix_bool = false;

		$fix_query = $this->db->_query_print('SELECT * FROM '.GD_ORDER_ITEM.' WHERE ordno=[i] AND goodsno=[i] AND NOT cancel=[i]', $item['ordno'], $goodsno, $cancel_sno);
		$fix_result = $this->db->_select($fix_query, true);
		if(empty($fix_result)) return true;

		foreach($fix_result as $fres) {
			if($fres['cancel'] == 0) {
				return false;
			}
			else {
				$cancel_query = $this->db->_query_print('SELECT * FROM '.GD_ORDER_CANCEL.' WHERE sno=[i] AND pgcancel=[s]', $fres['cancel'], 'n');
				$cancel_result = $this->db->fetch($cancel_query, true);
				if(!empty($cancel_result)) return false;//취소상태 이지만 아직 pg취소가 아닌 경우 false
				else {
					$fix_bool = true;
				}
			}
		}

		/*
		 * 취소완료된 주문 정보 조회시 고정배송비가 중복계산되는 경우가 있어 예외처리함.
		*/
		if($fix_bool === true) {
			$fix_bool = $this->DeliveryAllCancelCheck($item['sno'], $item['ordno'], $item['oi_delivery_idx']);
		}
		return $fix_bool;
	}

	/* 지역별 배송비 조회 */
	function area_delivery_search($area_idx)
	{
		$area_query = $this->db->_query_print('SELECT * FROM '.GD_ORDER_ITEM_DELIVERY.' WHERE oi_delivery_idx=[i] AND delivery_type=100', $area_idx);
		return $this->db->fetch($area_query, true);
	}

	function cancel_delivery($sno)
	{
		// 취소처리건 주문조회
		$order_query = "select * from ".GD_ORDER_ITEM." where cancel='".$sno."' AND (istep=41 OR istep=42)";
		$order_res = $this->db->_select($order_query);

		return $this->get_cancel_delivery_data($sno, $order_res);
	}

	/* 주문건 취소시(취소접수) 취소금액 및 관련 배송비(지역별 배송비 포함) 데이터 생성
	 * return Array
	 * total_cancel_price			int		총 취소금액
	 * cancel_delivery_price		int		취소 배송비
	 * total_cancel_delivery_price	int		총 취소 배송비(취소 배송비 + 지역별 배송비)
	 * item							Array
	 *		sno						int		item번호
	 *		oi_delivery_idx			int		배송item 번호(지역별 배송비인 경우 dv_주문번호)
	 *		productAmt				int		취소금액
	 *		productUnitPrice		int		취고단가금액
	 *		ea						int		수량
	 *		delivery				bool	변수가 선언되어 있고 1인 경우 배송비item
	 * delivery						Array
	 *		[배송비 item 번호]	=>	취소금액
	 * msg							Array
	*/
	function get_cancel_delivery($ordno)
	{
		$return_data['total_cancel_goods_price'] = 0;
		$return_data['total_cancel_delivery_price'] = 0;

		//페이코 취소처리된 취소번호 조회
		$order_cancel_query = $this->db->_query_print("SELECT sno FROM ".GD_ORDER_CANCEL." WHERE ordno=[i] AND pgcancel IN ([s],[s])", $ordno, 'y', 'r');
		$cancel_res = $this->db->query($order_cancel_query, true);

		//배송비 계산시 기본배송비 상품을 부분취소하는 경우 배송비가 중복계산되는 경우가 있어 예외처리함
		$basic_query = $this->db->_query_print("SELECT oi_delivery_idx FROM ".GD_ORDER_ITEM_DELIVERY." WHERE ordno=[i] and delivery_type=[i]", $ordno, '0');
		list($basic_sno) = $this->db->fetch($basic_query);
		$basic_delivery_price = 0;//취소된 배송비 금액에 기본배송비가 포함여부
		$basic_delivery = false;//체크하는 취소건에 기본배송비 포함여부

		while($data = $this->db->fetch($cancel_res)) {
			$item_query = $this->db->_query_print("SELECT * FROM ".GD_ORDER_ITEM." WHERE cancel=[i]", $data['sno']);
			$arr_item_data = $this->db->_select($item_query);
			$arr_cancel_delivery = $this->get_cancel_delivery_data($data['sno'], $arr_item_data);

			foreach($arr_cancel_delivery['delivery'] as $delivery_idx => $delivery_price) {
				if($basic_sno == $delivery_idx) {
					if($basic_delivery !== true) {
						$basic_delivery_price += $delivery_price;
						$basic_delivery = true;
					}
				}
			}
			$return_data['total_cancel_goods_price'] += $arr_cancel_delivery['total_cancel_price'];//취소된 상품 금액
			$return_data['total_cancel_delivery_price'] += $arr_cancel_delivery['total_cancel_delivery_price'];//취소된 배송비 금액
		}

		$return_data['total_cancel_delivery_price'] -= $basic_delivery_price;

		return $return_data;
	}

	function get_cancel_delivery_data($sno, $order_res)
	{
		// 배송비 관련 설정값 로드
		$this->getDeliveryConf();

		$total_cancel_price = 0;
		$total_cancel_delivery_price = 0;
		$cancel_goods_price = 0;
		$total_cancel_area_delivery = 0;
		$sum_area_idxs = array();
		$view_data = Array();

		$fix_area_bool = Array();//고정배송비의 지역별 배송비 중복차감 방지용
		$basic_area_bool = false;//기본배송비의 지역별 배송비 중복차감 방지용
		$basic_bool = false;

		foreach($order_res as $item) {
			$cancel_delivery_price = 0;

			// 일부 수량만 취소하는 경우 주문상품번호가 달라져 원 주문상품번호를 조회함.
			$sno_query = "select sno from ".GD_ORDER_ITEM." WHERE addopt='".$item['addopt']."' AND opt1='".$item['opt1']."' AND opt2='".$item['opt2']."' AND goodsno='".$item['goodsno']."' AND oi_delivery_idx='".$item['oi_delivery_idx']."' AND cancel < 1 AND ordno=".$item['ordno']." order by sno desc";
			$sno_res = $this->db->fetch($sno_query);


			if(!empty($sno_res['sno']) && ($item['sno']) != $sno_res['sno']) {
				$tmp_item['sno'] = $sno_res['sno'];//주문상품번호
			}
			else $tmp_item['sno'] = $item['sno'];//주문상품번호

			$tmp_item['productAmt'] = ($item['price'] - $item['memberdc'] - $item['coupon'] - $item['oi_special_discount_amount']) * $item['ea'];//총 금액
			$tmp_item['productUnitPrice'] = $item['price'];//상품단가
			$tmp_item['ea'] = $item['ea'];//수량

			$items[] = $tmp_item;
			unset($tmp_item);

			$total_cancel_price += ($item['price'] - $item['memberdc'] - $item['coupon'] - $item['oi_special_discount_amount']) * $item['ea'];//총 취소금액

			$cancel_goods_price += $item['price'] * $item['ea'];//취소상품금액

			$area_delivery = 0;//지역별 배송비 금액

			$delivery_query = "select delivery_price, prn_delivery_price, delivery_type, conditional_price from ".GD_ORDER_ITEM_DELIVERY." WHERE oi_delivery_idx='".$item['oi_delivery_idx']."'";
			$delivery_res = $this->db->fetch($delivery_query);


			// 취소 item 배송비 및 지역별 배송비 계산
/*
				 * 0 : 기본 배송
				 * 1 : 무료배송
				 * 2 : 상품별 배송비 (더이상 사용하지 않음)
				 * 3 : 착불배송비
				 * 4 : 고정 배송비
				 * 5 : 수량별 배송비
*/

			switch($delivery_res['delivery_type']) {
				case '0' : //기본배송비
					$view_data[0]['cnt'] += 1;

					if($this->checkAllCancel($sno, $item, $delivery_res['delivery_type']) === true) {
						//지역별 배송비 조회
						$area_result = $this->area_delivery_search($item['oi_area_idx']);

						if(!empty($area_result) && $basic_area_bool === false) {

							$area_delivery = $area_result['conditional_price'];
							$total_cancel_area_delivery += $area_delivery;
							$deliverys[$item['oi_area_idx']] += $area_delivery;//배송비 테이블 차감용

							$tmp_area_item['oi_delivery_idx'] = 'dv_'.$item['ordno'];
							$tmp_area_item['productAmt'] = $area_delivery;
							$tmp_area_item['productUnitPrice'] = $area_delivery;
							$tmp_area_item['delivery'] = true;

							if($area_result['prn_delivery_price'] == $tmp_area_item['productAmt']) $tmp_area_item['ea'] = '1';

							$arr_area_item[] = $tmp_area_item;
							unset($tmp_area_item);
							$basic_area_bool = true;

							$view_data[0]['area_delivery_price'] = $area_delivery;
						}

						if($basic_bool !== true) {
							$cancel_delivery_price = $delivery_res['delivery_price'];
							$total_cancel_delivery_price += $cancel_delivery_price;

							$tmp_delivery_item['oi_delivery_idx'] = $item['oi_delivery_idx'];//delivery_idx
							$tmp_delivery_item['productAmt'] = $cancel_delivery_price;//배송비
							$tmp_delivery_item['productUnitPrice'] = $cancel_delivery_price;//배송비
							$tmp_delivery_item['delivery'] = true;

							if($delivery_res['prn_delivery_price'] == $tmp_delivery_item['productAmt']) $tmp_delivery_item['ea'] = '1';

							$deliverys[$tmp_delivery_item['oi_delivery_idx']] += $cancel_delivery_price;//배송비 테이블 차감용

							$items[] = $tmp_delivery_item;
							unset($tmp_delivery_item);

							$basic_bool = true;
							$view_data[0]['delivery_price'] = $cancel_delivery_price;
						}
					}
					else if(count($view_data[0]) < 2) $view_data[0]['delivery_price'] = 0;

					//배송비취소금액
					break;

				case '1' : //무료배송비
					//지역별 배송비
					if(!isset($this->delivery_free_type)) $this->delivery_free_type = false;
					$area_result = $this->area_delivery_search($item['oi_area_idx']);
					$view_data[1]['cnt'] += 1;

					if($this->delivery_set['add_extra_fee_duplicate_free'] == '1' && $this->allItemCancel($sno, $item) === true) {
						if(!empty($area_result)) {
							$area_delivery = $area_result['conditional_price'];
							$total_cancel_area_delivery += $area_delivery;
							$deliverys[$item['oi_area_idx']] += $area_delivery;//배송비 테이블 차감용

							$tmp_area_item['oi_delivery_idx'] = 'dv_'.$item['ordno'];
							$tmp_area_item['productAmt'] = $area_delivery;
							$tmp_area_item['productUnitPrice'] = $area_delivery;
							$tmp_area_item['delivery'] = true;

							if($area_result['prn_delivery_price'] == $tmp_area_item['productAmt']) $tmp_area_item['ea'] = '1';

							$arr_area_item[] = $tmp_area_item;
							unset($tmp_area_item);

							$view_data[1]['area_delivery_price'] += $area_delivery;
						}
					}
					else if($this->delivery_set['add_extra_fee_duplicate_free'] != '1' && $this->delivery_free_type === false && $this->checkAllCancel($sno, $item, $delivery_res['delivery_type']) === true) {
						$this->delivery_free_type = true;
						if(!empty($area_result)) {
							$area_delivery = $area_result['conditional_price'];
							$total_cancel_area_delivery += $area_delivery;
							$deliverys[$item['oi_area_idx']] += $area_delivery;//배송비 테이블 차감용

							$tmp_area_item['oi_delivery_idx'] = 'dv_'.$item['ordno'];
							$tmp_area_item['productAmt'] = $area_delivery;
							$tmp_area_item['productUnitPrice'] = $area_delivery;
							$tmp_area_item['delivery'] = true;

							if($area_result['prn_delivery_price'] == $tmp_area_item['productAmt']) $tmp_area_item['ea'] = '1';

							$arr_area_item[] = $tmp_area_item;
							unset($tmp_area_item);

							$view_data[1]['area_delivery_price'] = $area_delivery;
						}
					}
					break;

				case '3' : //착불배송비
					//지역별 배송비
					//배송비취소금액
					$view_data[3][$item['goodsno']]['cnt'] += 1;
					$view_data[3][$item['goodsno']]['area_delivery_price'] = 0;
					$view_data[3][$item['goodsno']]['delivery_price'] = 0;
					break;

				case '4' : //고정배송비
					$view_data[4][$item['goodsno']]['cnt'] += 1;

					//지역별 배송비
					//모든 고정배송비가 취소/부분취소 되는 경우 지역별 배송비 환불
					if($this->checkAllCancel($sno, $item, $delivery_res['delivery_type']) === true) {
						$area_result = $this->area_delivery_search($item['oi_area_idx']);

						if($this->delivery_set['add_extra_fee_duplicate_fixEach'] == '1') {
							// 고정배송비 상품별 지역별 배송비 각각 부과
							if(!empty($area_result) && $fix_area_bool[$item['goodsno']] !== true) {

								$area_delivery = $area_result['conditional_price'];
								$total_cancel_area_delivery += $area_delivery;
								$deliverys[$item['oi_area_idx']] += $area_delivery;//배송비 테이블 차감용

								$tmp_area_item['oi_delivery_idx'] = 'dv_'.$item['ordno'];
								$tmp_area_item['productAmt'] = $area_delivery;
								$tmp_area_item['productUnitPrice'] = $area_delivery;
								$tmp_area_item['delivery'] = true;

								if($area_result['prn_delivery_price'] == $tmp_area_item['productAmt']) $tmp_area_item['ea'] = '1';

								$arr_area_item[] = $tmp_area_item;
								unset($tmp_area_item);
								$fix_area_bool[$item['goodsno']] = true;

								$view_data[4][$item['goodsno']]['area_delivery_price'] += $area_delivery;
							}
						}
						else {
							// 고정배송비 상품에 지역별 배송비 1회 부과
							if(!empty($area_result) && $fix_area_bool[0] !== true) {

								$area_delivery = $area_result['conditional_price'];
								$total_cancel_area_delivery += $area_delivery;
								$deliverys[$item['oi_area_idx']] += $area_delivery;//배송비 테이블 차감용

								$tmp_area_item['oi_delivery_idx'] = 'dv_'.$item['ordno'];
								$tmp_area_item['productAmt'] = $area_delivery;
								$tmp_area_item['productUnitPrice'] = $area_delivery;
								$tmp_area_item['delivery'] = true;

								if($area_result['prn_delivery_price'] == $tmp_area_item['productAmt']) $tmp_area_item['ea'] = '1';

								$arr_area_item[] = $tmp_area_item;
								unset($tmp_area_item);
								$fix_area_bool[0] = true;

								$view_data[4][$item['goodsno']]['area_delivery_price'] = $area_delivery;
							}
						}
					}

					//고정배송비 취소
					if($this->allFixDeliveryCancel($sno, $item, $item['goodsno']) === true) {

						if($deliverys[$item['oi_delivery_idx']] < 1) {
							if($delivery_res['delivery_price'] < 1) {//최초 고정배송비가 0원인 경우 0원으로 반환
								$view_data[4][$item['goodsno']]['delivery_price'] = '0';
								break;
							}

							$cancel_delivery_price = $delivery_res['conditional_price'];
							$total_cancel_delivery_price += $cancel_delivery_price;

							$tmp_delivery_item['oi_delivery_idx'] = $item['oi_delivery_idx'];//delivery_idx
							$tmp_delivery_item['productAmt'] = $cancel_delivery_price;//배송비
							$tmp_delivery_item['productUnitPrice'] = $cancel_delivery_price;//배송비
							$tmp_delivery_item['delivery'] = true;

							if($delivery_res['prn_delivery_price'] == $tmp_delivery_item['productAmt']) $tmp_delivery_item['ea'] = '1';

							$deliverys[$tmp_delivery_item['oi_delivery_idx']] += $cancel_delivery_price;//배송비 테이블 차감용

							$items[] = $tmp_delivery_item;
							unset($tmp_delivery_item);

							$view_data[4][$item['goodsno']]['delivery_price'] = $cancel_delivery_price;
						}
					}

					if(count($view_data[4][$item['goodsno']]) < 2) $view_data[4][$item['goodsno']]['area_delivery_price'] = 0;

					break;

				case '5' : //수량별배송비
					//지역별 배송비
					$area_result = $this->area_delivery_search($item['oi_area_idx']);

					if(!empty($area_result)) {
						$area_delivery = $item['ea'] * $area_result['conditional_price'];
						$total_cancel_area_delivery += $area_delivery;
						$deliverys[$item['oi_area_idx']] += $area_delivery;//배송비 테이블 차감용

						$tmp_area_item['oi_delivery_idx'] = 'dv_'.$item['ordno'];
						$tmp_area_item['productAmt'] = $area_delivery;
						$tmp_area_item['productUnitPrice'] = $area_delivery;
						$tmp_area_item['delivery'] = true;

						if($area_result['prn_delivery_price'] == $tmp_area_item['productAmt']) $tmp_area_item['ea'] = '1';

						$arr_area_item[] = $tmp_area_item;
						unset($tmp_area_item);

						$view_data[5][$item['optno']]['area_delivery_price'] = $area_delivery;
					}

					//수량별 배송비 취소
					if($delivery_res['delivery_price'] < 1) {
						$view_data[5][$item['optno']]['delivery_price'] = '0';
						break;
					}

					$cancel_delivery_price = $delivery_res['conditional_price'] * $item['ea'];
					$total_cancel_delivery_price += $cancel_delivery_price;

					$tmp_delivery_item['oi_delivery_idx'] = $item['oi_delivery_idx'];//delivery_idx
					$tmp_delivery_item['productAmt'] = $cancel_delivery_price;//배송비
					$tmp_delivery_item['productUnitPrice'] = $cancel_delivery_price;//배송비
					$tmp_delivery_item['delivery'] = true;

					if($delivery_res['prn_delivery_price'] == $tmp_delivery_item['productAmt']) $tmp_delivery_item['ea'] = '1';

					$deliverys[$tmp_delivery_item['oi_delivery_idx']] += $cancel_delivery_price;//배송비 테이블 차감용

					$items[] = $tmp_delivery_item;
					unset($tmp_delivery_item);

					$view_data[5][$item['optno']]['delivery_price'] = $cancel_delivery_price;

					break;
			}
		}

		if(!empty($arr_area_item)) {
			$tmp_area = Array();
			foreach($arr_area_item as $area_item) {

				$tmp_area['oi_delivery_idx'] = $area_item['oi_delivery_idx'];//dv_1426211557647
				$tmp_area['productAmt'] += $area_item['productAmt'];//배송비
				$tmp_area['productUnitPrice'] += $area_item['productUnitPrice'];//배송비
				$tmp_area['delivery'] = true;

				if(isset($area_item['ea'])) $tmp_area['ea'] = '1';
			}
			if(!empty($tmp_area)) $items[] = $tmp_area;
		}

		/*
		 * 주문에 기본배송비가 있는 경우 취소시 무료조건에서 배송비 부과로 변경되는지 확인
		*/
		$basic_check_query = $this->db->_query_print('SELECT d.delivery_price, d.prn_delivery_price, d.conditional_price, count(*) as cnt FROM '.GD_ORDER_ITEM.' i LEFT JOIN '.GD_ORDER_ITEM_DELIVERY.' d ON i.oi_delivery_idx=d.oi_delivery_idx WHERE i.cancel=0 AND d.ordno=[i] AND d.delivery_type=0 ', $order_res[0]['ordno']);
		$basic_res = $this->db->fetch($basic_check_query, true);

		if(($basic_res['cnt'] > 0) && ($basic_res['prn_delivery_price'] == 0) && ($basic_res['conditional_price'] > $basic_res['price'])) {
			$ord_price_query = $this->db->_query_print('SELECT sum(price * ea) as price FROM '.GD_ORDER_ITEM.' WHERE cancel=0 AND ordno=[i]', $order_res[0]['ordno']);
			$ord_price = $this->db->fetch($ord_price_query);

			$rtn['msg'][] = '- 주문취소로 상품 주문금액('.number_format($ord_price['price']).'원)이 기본배송 무료조건('.number_format($basic_res['conditional_price']).'원)에 미달됩니다.<br>확인하셔서 환불 수수료를 설정해 주시기 바랍니다.';
		}

		$rtn['total_cancel_price'] = $total_cancel_price;
		$rtn['cancel_delivery_price'] = $total_cancel_delivery_price;
		$rtn['total_cancel_delivery_price'] = $total_cancel_delivery_price + $total_cancel_area_delivery;
		$rtn['item'] = $items;
		$rtn['delivery'] = $deliverys;//배송비 테이블 차감용

		if($total_cancel_area_delivery > 0) $rtn['msg'][] = '배송비에 지역별 배송비 ['.number_format($total_cancel_area_delivery).'원] 이 포함되어 있습니다.';

		$rtn['coupon'] = $this->getCoupon();//주문의 쿠폰사용내역 조회
		$rtn['emoney'] = $this->getEmoney();//주문의 적립금 조회
		$rtn['view'] = $view_data;

		return $rtn;
	}

	function getEmoney()
	{
		$amount = 0;
		$order_query = $this->db->_query_print("SELECT emoney from ".GD_ORDER." WHERE ordno=[i]", $this->ordno);
		list($amount) = $this->db->fetch($order_query);

		return $amount;
	}

	function getCoupon()
	{
		$amount['f'] = 0;
		$amount['m'] = 0;

		$order_query = $this->db->_query_print("SELECT coupon from ".GD_ORDER." WHERE ordno=[i]", $this->ordno);
		list($amount['m']) = $this->db->fetch($order_query);

		$item_query = $this->db->_query_print("SELECT coupon * ea as coupon from ".GD_ORDER_ITEM." WHERE ordno=[i]", $this->ordno);
		$item_data = $this->db->_select($item_query);
		
		foreach($item_data as $data) {
			$amount['f'] += $data['coupon'];
		}
		return $amount;
	}

	// 마지막 취소건인지 확인
	function checkLastCancel($sno)
	{
		$query = $this->db->_query_print("select istep, cancel from ".GD_ORDER_ITEM." WHERE ordno=[s]", $this->ordno);
		$order_data = $this->db->_select($query);

		foreach($order_data as $item) {
			if($item['istep'] == '44') $bool = true;
			else if($item['istep'] >= '40' && $item['istep'] <= '43') {
				if($sno != $item['cancel']) {
					$cancel_query = $this->db->_query_print("select pgcancel from ".GD_ORDER_CANCEL." WHERE sno=[s]", $item['cancel']);
					$cancel_data = $this->db->fetch($cancel_query);
					if($cancel_data['pgcancel'] == 'n') {
						return false;
					}
					else $bool = true;
				}
				else {
					$bool = true;
				}
			}
			else {
				return false;
			}
		}

		return $bool;
	}

	function getDeliveryType($oi_delivery_idx)
	{
		$query = $this->db->_query_print("SELECT delivery_type FROM ".GD_ORDER_ITEM_DELIVERY." WHERE oi_delivery_idx=[i]", $oi_delivery_idx);
		list($delivery_type) = $this->db->fetch($query);
		if($delivery_type === '0') return 'basic';
		else if($delivery_type === '4') return 'fix';
		else return 'etc';
	}

	function getCancelCompletDelivery($oi_delivery_idx)
	{
		switch($this->getDeliveryType($oi_delivery_idx)) {
			case 'basic' ://기본배송비
			case 'fix' ://고정배송비
				$query = $this->db->_query_print("SELECT istep FROM ".GD_ORDER_ITEM." WHERE oi_delivery_idx=[i]", $oi_delivery_idx);
				$res = $this->db->query($query);
				while($data = $this->db->fetch($res)) {
					if($data['istep'] !== '44') return false;
				}
			break;
		}
		return true;
	}

	/**
	 * 취소접수시점의 배송비
	 *
	 * @return int 배송비
	 */
	public function getCancelingDeliveryFee()
	{
		$return_data['total_cancel_goods_price'] = 0;
		$return_data['total_cancel_delivery_price'] = 0;

		//페이코 취소처리된 취소번호 조회
		$order_cancel_query = $this->db->_query_print("SELECT sno FROM ".GD_ORDER_CANCEL." WHERE ordno=[i]", $this->ordno);
		$cancel_res = $this->db->query($order_cancel_query, true);
		$arr_delivery_price = Array();

		while($data = $this->db->fetch($cancel_res)) {

			$item_query = $this->db->_query_print("SELECT * FROM ".GD_ORDER_ITEM." WHERE cancel=[i] AND istep>40 AND istep<44 ", $data['sno']);
			$arr_item_data = $this->db->_select($item_query);

			if(!empty($arr_item_data)) {
				$arr_cancel_delivery = $this->get_cancel_delivery_data($data['sno'], $arr_item_data);

				if(!empty($arr_cancel_delivery['delivery'])) {

					//배송타입별 계산
					foreach($arr_cancel_delivery['delivery'] as $oi_delivery_idx => $delivery_price) {
						switch($this->getDeliveryType($oi_delivery_idx)) {
							case 'basic' ://기본배송비
							case 'fix' ://고정배송비
								$arr_delivery_price[$oi_delivery_idx] = $delivery_price;
								break;
							default :
								$arr_delivery_price[$oi_delivery_idx] += $delivery_price;
								break;
						}
					}
				}

				$return_data['total_cancel_goods_price'] += $arr_cancel_delivery['total_cancel_price'];//취소된 상품 금액
				$return_data['total_cancel_delivery_price'] += $arr_cancel_delivery['total_cancel_delivery_price'];//취소된 배송비 금액
			}
		}

		if(isset($arr_delivery_price)) $return_data['total_cancel_delivery_price'] = array_sum($arr_delivery_price);

		return $return_data;
	}

	/**
	 * 취소완료시점의 배송비
	 *
	 * @return int 배송비
	 */
	public function getCancelCompletedDeliveryFee()
	{
		$return_data['total_cancel_goods_price'] = 0;
		$return_data['total_cancel_delivery_price'] = 0;

		//페이코 취소처리된 취소번호 조회
		$order_cancel_query = $this->db->_query_print("SELECT sno FROM ".GD_ORDER_CANCEL." WHERE ordno=[i]", $this->ordno);
		$cancel_res = $this->db->query($order_cancel_query, true);

		while($data = $this->db->fetch($cancel_res)) {
			$item_query = $this->db->_query_print("SELECT * FROM ".GD_ORDER_ITEM." WHERE cancel=[i] AND istep=44 ", $data['sno']);
			$arr_item_data = $this->db->_select($item_query);

			if(!empty($arr_item_data)) {
				$arr_cancel_delivery = $this->get_cancel_delivery_data($data['sno'], $arr_item_data);

				if(!empty($arr_cancel_delivery['delivery'])) {
					$arr_delivery_price = Array();


					$return_data['total_cancel_goods_price'] += $arr_cancel_delivery['total_cancel_price'];//취소된 상품 금액

					//취소완료 상태의 배송비만 계산
					foreach($arr_cancel_delivery['delivery'] as $oi_delivery_idx => $delivery_price) {
						if($this->getCancelCompletDelivery($oi_delivery_idx) === true) {
							$return_data['total_cancel_delivery_price'] += $delivery_price;//취소된 배송비 금액
						}
					}
				}
			}
		}

		return $return_data;
	}

	/**
	  * 취소SNO 기준의 환원 배송비
	  *
	  * @return int 배송비
	  */
	public function getCancelCompletedDeliverFeeWithSno($sno, $view = false) {
		$item_query = $this->db->_query_print("SELECT * FROM ".GD_ORDER_ITEM." WHERE cancel=[i] AND istep=44 ", $sno);
		$arr_item_data = $this->db->_select($item_query);
		if(!empty($arr_item_data)) {
			$arr_cancel_delivery = $this->get_cancel_delivery_data($sno, $arr_item_data);
			if(!empty($arr_cancel_delivery['delivery'])) {
				$arr_delivery_price = Array();

				$return_data['total_cancel_goods_price'] += $arr_cancel_delivery['total_cancel_price'];//취소된 상품 금액
				//취소완료 상태의 배송비만 계산
				foreach($arr_cancel_delivery['delivery'] as $oi_delivery_idx => $delivery_price) {
					if($this->getCancelCompletDelivery($oi_delivery_idx) === true) {
						$return_data['total_cancel_delivery_price'] += $delivery_price;//취소된 배송비 금액
					}
				}
			}
			if($view === true) {
				$return_data['view'] = $arr_cancel_delivery['view'];
			}
		}
		return $return_data;
	}
}