<?
class admin_order_list extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);

		// 체크아웃 주문 연동
		$checkoutapi = Core::config('checkoutapi');
		$checkout = (!($checkoutapi['cryptkey'] && $checkoutapi['integrateOrder']=='y')) ? false : $this->db->builder()->select();

		$enamoo = $this->db->builder()->select();
		$enamoo
		->from(		array('ORD' => GD_ORDER),										array('ordno','nameOrder','step','step2','orddt','oldordno','settlekind','escrowyn','eggyn','cashreceipt','cbyn','prn_settleprice','settleprice','bankAccount','bankSender','inflow','pCheeseOrdNo','nameReceiver','dyn','deliverycode'))
		->join(		array('ITM' => GD_ORDER_ITEM)		,'ORD.ordno = ITM.ordno',	array('goodsnm'))
		->leftjoin(	array('MB' => GD_MEMBER)			,'ORD.m_no = MB.m_no',		array('m_id','m_no'))
		->group('ORD.ordno');

		if ($checkout) {

			$checkout
			->from(		array('O' => GD_NAVERCHECKOUT_ORDERINFO),														null)
			->join(		array('PO' => GD_NAVERCHECKOUT_PRODUCTORDERINFO)	,'PO.OrderID = O.OrderID',					null)
			->leftjoin(	array('MB' => GD_MEMBER)							,'PO.MallMemberID = MB.m_id',				null)
			->leftjoin(	array('D' => GD_NAVERCHECKOUT_DELIVERYINFO)			,'PO.ProductOrderID = D.ProductOrderID',	null)
			->leftjoin(	array('C' => GD_NAVERCHECKOUT_CANCELINFO)			,'PO.ProductOrderID = C.ProductOrderID',	null)
			->leftjoin(	array('R' => GD_NAVERCHECKOUT_RETURNINFO)			,'PO.ProductOrderID = R.ProductOrderID',	null)
			->leftjoin(	array('E' => GD_NAVERCHECKOUT_EXCHANGEINFO)			,'PO.ProductOrderID = E.ProductOrderID',	null)
			->leftjoin(	array('DH' => GD_NAVERCHECKOUT_DECISIONHOLDBACKINFO),'PO.ProductOrderID = DH.ProductOrderID',	null)
			->group('PO.OrderID, PO.ProductOrderStatus, PO.ClaimStatus');

			$checkout->columns(array(
				'_order_type' => $this->db->quote('checkout'),
				'ordno' => 'O.OrderID',
				'nameOrder' => 'O.OrdererName',
				'nameReceiver' => 'PO.ShippingAddressName',
				'settlekind' => 'O.PaymentMeans',
				'step' => 'PO.ProductOrderStatus',
				'step2' => null,
				'orddt' => 'O.OrderDate',
				'dyn' => $this->db->quote('n'),
				'escrowyn' => null,
				'eggyn' => null,
				'inflow' => null,
				'deliverycode' => null,
				'cashreceipt' => null,
				'cbyn' => null,
				'oldordno' => null,
				'prn_settleprice' => $this->db->expression('SUM(PO.Quantity * PO.UnitPrice - ProductDiscountAmount)'),
				'MB.m_id',
				'MB.m_no',
				'goodsnm' => 'PO.ProductName',
				'PO.PlaceOrderStatus',
				'PO.ProductOrderStatus',
				'PO.ClaimType',
				'PO.ClaimStatus',
				'ProductOrderIDList' => $this->db->expression('GROUP_CONCAT(PO.ProductOrderID SEPARATOR ",")'),

				'count_item' => $this->db->expression('COUNT(PO.ProductOrderID)'),
				'count_dv_item' => null,
			));

			$enamoo->reset('column')->columns(array(
				'_order_type' => $this->db->quote('godo'),
				'ORD.ordno',
				'ORD.nameOrder',
				'ORD.nameReceiver',
				'ORD.settlekind',
				'ORD.step',
				'ORD.step2',
				'ORD.orddt',
				'ORD.dyn',
				'ORD.escrowyn',
				'ORD.eggyn',
				'ORD.inflow',
				'ORD.deliverycode',
				'ORD.cashreceipt',
				'ORD.cbyn',
				'ORD.oldordno',
				'ORD.prn_settleprice',
				'MB.m_id',
				'MB.m_no',
				'ITM.goodsnm',
				'PlaceOrderStatus' => null,
				'ProductOrderStatus' => null,
				'ClaimType' => null,
				'ClaimStatus' => null,
				'ProductOrderIDList' => null,
				//count_item	// 하단에서 추가
				//count_dv_item	// 하단에서 추가

			));

		}

		// where

		// 접수유형
		if($param['sugi']) {
			if($param['sugi'] == "Y") $enamoo->where("ORD.inflow = ?", 'sugi');
			elseif($param['sugi'] == "N") {
				$enamoo->where("ORD.inflow != ? OR ORD.inflow IS null", 'sugi');
			}
		}

		if($param['regdt_start']) {
			if(!$param['regdt_end']) $param['regdt_end'] = date('Ymd', G_CONST_NOW);

			if ($param['regdt_time_start'] !== -1 && $param['regdt_time_end'] !== -1) {
				$param['regdt_start'] .= sprintf('%02d',$param['regdt_time_start']);
				$param['regdt_end'] .= sprintf('%02d',$param['regdt_time_end']);
			}

			$tmp_start = Core::helper('Date')->min($param['regdt_start']);
			$tmp_end = Core::helper('Date')->max($param['regdt_end']);

			switch($param['dtkind']) {
				case 'orddt': $enamoo->where("ORD.orddt BETWEEN ? AND ? ", array($tmp_start, $tmp_end)); break;
				case 'cdt': $enamoo->where("ORD.cdt BETWEEN ? AND ? ", array($tmp_start, $tmp_end)); break;
				case 'ddt': $enamoo->where("ORD.ddt BETWEEN ? AND ? ", array($tmp_start, $tmp_end)); break;
				case 'confirmdt': $enamoo->where("ORD.confirmdt BETWEEN ? AND ? ", array($tmp_start, $tmp_end)); break;
			}

			if ($checkout) {
				switch($param['dtkind']) {
					case 'orddt': $checkout->where("O.OrderDate BETWEEN ? AND ? ", array($tmp_start, $tmp_end)); break;
					case 'cdt': $checkout->where("O.PaymentDate BETWEEN ? AND ? ", array($tmp_start, $tmp_end)); break;
					case 'ddt': $checkout->where("D.SendDate BETWEEN ? AND ? ", array($tmp_start, $tmp_end)); break;
					case 'confirmdt': $checkout->where("D.DeliveredDate BETWEEN ? AND ? ", array($tmp_start, $tmp_end)); break;
				}
			}

		}

		if($param['settlekind']) {
			$enamoo->where("ORD.settlekind = ?", $param['settlekind']);

			if ($checkout) {
				$tmpMap = array('a'=>'무통장입금','c'=>'신용카드','o'=>'계좌이체');
				if(array_key_exists($param['settlekind'],$tmpMap)) {
					$checkout->where("O.PaymentMeans = ?", $tmpMap[$param['settlekind']]);
				}
				else {
					$checkout_isUnableCondition=true;
				}
				unset($tmpMap);
			}
		}

		if(count($param['step']) || count($param['step2'])) {

			$subWhere = array();
			$co_subWhere = array();

			if(count($param['step'])) {
				$subWhere[] = $enamoo->parse("ORD.step IN (?) AND ORD.step2 = ?", array($param['step'],0));

				if ($checkout) {

					foreach ($param['step'] as $_step) {
						switch ((int)$_step) {
							case 0:	// 주문접수 (입금대기)
								$co_subWhere[] = $checkout->parse('PO.ProductOrderStatus = ? AND PO.ClaimStatus = ?', array('PAYMENT_WAITING',''));
								break;
							case 1:	// 입금확인 (발주확인전)
								$co_subWhere[] = $checkout->parse('PO.ProductOrderStatus = ? AND PO.PlaceOrderStatus = ? AND PO.ClaimStatus = ?', array('PAYED','NOT_YET',''));
								break;
							case 2:	// 배송준비중(발주확인)
								$co_subWhere[] = $checkout->parse('PO.ProductOrderStatus = ? AND PO.PlaceOrderStatus = ? AND PO.ClaimType = ?', array('PAYED','OK',''));
								break;
							case 3:	// 배송중
								$co_subWhere[] = $checkout->parse('PO.ProductOrderStatus = ? AND PO.ClaimStatus = ?', array('DELIVERING',''));
								break;
							case 4:	// 배송완료, 구매확정
								$co_subWhere[] = $checkout->parse('PO.ClaimStatus = ? AND (PO.ProductOrderStatus = ? OR PO.ProductOrderStatus = ?)', array('','DELIVERED','PURCHASE_DECIDED'));
								break;
						}
					}
				}
			}

			if(count($param['step2'])) {
				foreach($param['step2'] as $v) {
					switch($v) {
						case 1:
							$subWhere[] = $enamoo->parse('ORD.step = 0 AND ORD.step2 BETWEEN ? AND ?', array(1,49));
						break;

						case 2:
							$subWhere[] = $enamoo->parse('(ORD.step IN (?) AND ORD.step2 != ?) OR (ORD.cyn = ? AND ORD.step2 = ? AND ORD.dyn != ?)', array(array(1,2), 0, 'r', 44, 'e'));
						break;

						case 3:
							$subWhere[] = $enamoo->parse('ORD.step IN (?) AND ORD.step2 != ?', array(array(3,4),0));
						break;

						case 60 :
							$subWhere[] = $enamoo->parse('ITM.dyn = ? AND ITM.cyn = ?', array('e','e'));
							$isOrderItemSearch=true;
						break; //교환완료

						case 61 :
							$subWhere[] = $enamoo->parse('ORD.oldordno > 0');
						break; //재주문

						default :
							$subWhere[] = $enamoo->parse('ORD.step2 = ?',$v);
					}
				}

				if ($checkout) {
					foreach ($param['step2'] as $_step) {
						switch ((int)$_step) {
							case 1:	// 취소 (취소요청, 취소처리중, 취소완료)
								$co_subWhere[] = $checkout->parse('PO.ClaimType = ', 'CANCEL');
								break;
							case 2:
								// 환불 단계 없음

								break;
							case 3:	// 반품 (반품요청, 반품수거중, 반품수거완료, 반품완료)
								$co_subWhere[] = $checkout->parse('PO.ClaimType = ', 'RETURN');
								break;
							case 60:// 교환 (교환요청, 교환수거중, 교환수거완료, 교환재배송중, 교환완료)
								$co_subWhere[] = $checkout->parse('PO.ClaimType = ', 'EXCHANGE');
								break;
						}
					}
				}
			}

			if(count($subWhere)) {
				$enamoo->where( '('.implode(' OR ',$subWhere).')' );
			}

			if ($checkout) {
				if(sizeof($co_subWhere) > 0) {
					$checkout->where( '('.implode(' OR ',$co_subWhere).')' );
				}
				else {
					$checkout_isUnableCondition=true;
				}
			}
		}

		if($param['sword'] && $param['skey']) {

			switch($param['skey']) {
				case 'all':
					$subWhere = array();
					$subWhere[] = $enamoo->parse('ORD.ordno = ?', $param['sword']);
					$subWhere[] = $enamoo->parse('ORD.nameOrder like ?', $enamoo->wildcard($param['sword']));
					$subWhere[] = $enamoo->parse('ORD.nameReceiver like ?', $enamoo->wildcard($param['sword']));
					$subWhere[] = $enamoo->parse('ORD.bankSender like ?', $enamoo->wildcard($param['sword']));
					$subWhere[] = $enamoo->parse('MB.m_id = ?', $param['sword']);
					$enamoo->where( '('.implode(' OR ',$subWhere).')' );
					break;
				case 'ordno': $enamoo->where('ORD.ordno = ?', $param['sword']); break;
				case 'nameOrder': $enamoo->where('ORD.nameOrder like ?', $enamoo->wildcard($param['sword'])); break;
				case 'nameReceiver': $enamoo->where('ORD.nameReceiver like ?', $enamoo->wildcard($param['sword'])); break;
				case 'bankSender': $enamoo->where('ORD.bankSender like ?', $enamoo->wildcard($param['sword'])); break;
				case 'm_id': $enamoo->where('MB.m_id = ?', $param['sword']); break;
			}

			if ($checkout) {

				switch($param['skey']) {
					case 'all':
						$subWhere = array();
						$subWhere[] = $checkout->parse('O.OrderID = ?', $param['sword']);
						$subWhere[] = $checkout->parse('O.OrdererName like ?', $checkout->wildcard($param['sword']));
						$subWhere[] = $checkout->parse('PO.ShippingAddressName like ?', $checkout->wildcard($param['sword']));
						$subWhere[] = $checkout->parse('O.OrdererID = ?', $param['sword']);

						$checkout->where( '('.implode(' OR ',$subWhere).')' );
						break;
					case 'ordno': $checkout->where('O.OrderID = ?', $param['sword']); break;
					case 'nameOrder': $checkout->where('O.OrdererName like ?', $checkout->wildcard($param['sword'])); break;
					case 'nameReceiver': $checkout->where('PO.ShippingAddressName like ?', $checkout->wildcard($param['sword'])); break;
					case 'm_id': $checkout->where('O.OrdererID = ?', $param['sword']); break;
				}

			}

		}

		if($param['sgword'] && $param['sgkey']) {
			$isOrderItemSearch=true;

			switch($param['sgkey']) {
				case 'goodsnm': $enamoo->where('ITM.goodsnm like ?', $enamoo->wildcard($param['sgword'])); break;
				case 'brandnm': $enamoo->where('ITM.brandnm like ?', $enamoo->wildcard($param['sgword'])); break;
				case 'maker': $enamoo->where('ITM.maker like ?', $enamoo->wildcard($param['sgword'])); break;
			}

			if ($checkout) {

				switch($param['sgkey']) {
					case 'goodsnm':
						$checkout->where('PO.ProductName like ?', $enamoo->wildcard($param['sgword']));
						break;
					default:
						$checkout_isUnableCondition=true;
				}
			}
		}

		if($checkout && $checkout_isUnableCondition) {
			$checkout->reset('where')->where('0');
		}

		if(count($param['chk_inflow'])) {
			$es_inflow = array();
			foreach($param['chk_inflow'] as $v) {
				if($v == 'naver_price') {
					$es_inflow[] = 'naver_elec';
					$es_inflow[] = 'naver_bea';
					$es_inflow[] = 'naver_milk';
				}
				else if($v == 'plus_cheese'){
					$enamoo->where('ORD.pCheeseOrdNo <> ','');
				}
				else {
					$es_inflow[] = $v;
				}
			}
			if(!empty($es_inflow)){
				$enamoo->where('ORD.inflow IN (?)', array($es_inflow));
			}
		}
		if($param['cbyn']=='Y') {
			$enamoo->where('ORD.cbyn = ?','Y');
		}
		if($param['aboutcoupon']=='1') {
			$enamoo->where('ORD.about_coupon_flag = ?','Y');
		}
		if($param['escrowyn']) {
			$enamoo->where('ORD.escrowyn = ?',$param['escrowyn']);
		}
		if($param['eggyn']) {
			$enamoo->where('ORD.eggyn = ?',$param['eggyn']);
		}
		if($param['mobilepay']) {
			$enamoo->where('ORD.mobilepay = ?',$param['mobilepay']);
		}

		if ($param['todaygoods']) {

			$enamoo->where('GOODS.todaygoods = ?','y');

			$enamoo->join(
				 array('GOODS' => GD_GOODS)
				,'GOODS.goodsno = ITM.goodsno'
				,null
			);

		}

		if($param['cashreceipt']) {
			$enamoo->where('ORD.cashreceipt != '.'');
		}

		if($param['couponyn']) {

			$enamoo->join(
				 array('CUPN' => GD_COUPON_ORDER)
				,'CUPN.ordno = ORD.ordno'
				,null
			);

			$enamoo->where('CUPN.ordno IS NOT NULL');
		}

		// gd_order_item 에서 검색조건이 발생하는 경우 상품갯수와 상품송장체크는 별도로 처리
		if($isOrderItemSearch) {
			$enamoo->columns(array('count_item' => $this->db->expression('(select count(sno) from '.GD_ORDER_ITEM.' as s_oi where s_oi.ordno=ORD.ordno)')));
			$enamoo->columns(array('count_dv_item' => $this->db->expression('(select count(sno) from '.GD_ORDER_ITEM.' as s_oi where s_oi.ordno=ORD.ordno and s_oi.dvcode!="" and s_oi.dvno!="")')));
		}
		else {
			$enamoo->columns(array('count_item' => $this->db->expression('count(ITM.ordno)')));
			$enamoo->columns(array('count_dv_item' => $this->db->expression('sum(ITM.dvcode != "" and ITM.dvno != "")')));
		}

		if (!$checkout) {

			if($param['mode']=='group') {
				$result = $this->db->utility()->getAll($enamoo);
			}
			else {
				if(!$param['orderPageNum']) $param['orderPageNum'] = 15;
				if(!$param['page']) $param['page'] = 1;

				$enamoo->order('ORD.ordno DESC');

				$result = $this->db->utility()->getPaging($enamoo, $param['orderPageNum'], $param['page']);
			}

		}
		else {

			$union = $this->db->builder()->union($enamoo, $checkout);
			unset($enamoo, $checkout);

			// 체크아웃 주문 연동
			if($param['mode']=='group') {
				$result = $this->db->utility()->getAll($union);
			}
			else {

				if(!$param['orderPageNum']) $param['orderPageNum'] = 15;
				if(!$param['page']) $param['page'] = 1;

				$union->order('orddt DESC');
				$result = $this->db->utility()->getPaging($union, $param['orderPageNum'], $param['page']);
			}

		}

		return $result;
	}

}
?>