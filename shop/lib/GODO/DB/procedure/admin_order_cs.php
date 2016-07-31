<?
class admin_order_cs extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);

		// 체크아웃 주문 연동
		$checkoutapi = Core::config('checkoutapi');
		$checkout = (!($checkoutapi['cryptkey'] && $checkoutapi['integrateOrder']=='y')) ? false : $this->db->builder()->select();

		$enamoo = $this->db->builder()->select();
		$enamoo
		->from(array('OC' => GD_ORDER_CANCEL) ,array('code','canceldt' => 'regdt'))
		->join(array('ORD' => GD_ORDER),'OC.ordno = ORD.ordno',array('ordno','nameOrder','step','orddt','settlekind','inflow','pCheeseOrdNo','nameReceiver','dyn','deliverycode'))
		->join(array('ITM' => GD_ORDER_ITEM),'ORD.ordno = ITM.ordno',array('goodsnm','goodsno','istep','itemsno'=>'sno'))
		->leftjoin(array('MB' => GD_MEMBER),'ORD.m_no = MB.m_no',array('m_id','m_no'))
		->columns( array('count_goods' => $this->db->expression('COUNT(ITM.goodsno)'), 'sea' => $this->db->expression('SUM(ITM.ea)'),'pay' => 'SUM( (ITM.price - ITM.memberdc - ITM.coupon) * ITM.ea )' ) )
		->group('OC.sno')
		->order('OC.regdt DESC');

		$enamoo->where("ITM.istep BETWEEN ? AND ? ", array(40,49));

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
				'orddt' => 'O.OrderDate',
				'canceldt' => $this->db->expression('if(C.ClaimRequestDate > "",C.ClaimRequestDate,if(R.ClaimRequestDate>"",R.ClaimRequestDate,if(E.ClaimRequestDate>"",E.ClaimRequestDate,"")))'),
				'code' => $this->db->expression('if(C.ClaimRequestDate>"",C.CancelReason, if(R.ClaimRequestDate>"",R.ReturnReason, if(E.ClaimRequestDate>"",E.ExchangeReason, "")))'),
				'ordno' => 'O.OrderID',
				'nameOrder' => 'O.OrdererName',
				'settlekind' => 'O.PaymentMeans',
				'MB.m_id',
				'MB.m_no',
				'goodsnm' => 'PO.ProductName',
				'goodsno' => null,
				'count_goods' => $this->db->expression('count(PO.ProductOrderID)'),
				'sea' => $this->db->expression('sum(PO.Quantity)'),
				'pay' => $this->db->expression('SUM(PO.Quantity * PO.UnitPrice - ProductDiscountAmount)'),
				'step' => 'PO.ProductOrderStatus',
				'istep' => null,
				'itemsno' => null,
				'PO.PlaceOrderStatus',
				'PO.ProductOrderStatus',
				'PO.ClaimType',
				'PO.ClaimStatus',
				'ProductOrderIDList' => $this->db->expression('GROUP_CONCAT(PO.ProductOrderID SEPARATOR ",")'),
			));

			$enamoo->reset('order')->reset('column')->columns(array(
				'_order_type' => $this->db->quote('godo'),
				'ORD.orddt',
				'canceldt' => 'OC.regdt',
				'OC.code',
				'ORD.ordno',
				'ORD.nameOrder',
				'ORD.settlekind',
				'MB.m_id',
				'MB.m_no',
				'ITM.goodsnm',
				'ITM.goodsno',
				'count_goods' => $this->db->expression('count(ITM.goodsno)'),
				'sea' => $this->db->expression('sum(ITM.ea)'),
				'pay' => $this->db->expression('sum((ITM.price-ITM.memberdc-ITM.coupon)*ITM.ea)'),
				'ORD.step',
				'ITM.istep',
				'itemsno' => 'ITM.sno',
				'PlaceOrderStatus' => null,
				'ProductOrderStatus' => null,
				'ClaimType' => null,
				'ClaimStatus' => null,
				'ProductOrderIDList' => null,
			));
		}

		// 접수유형
		if($param['sugi']) {
			if($param['sugi'] == "Y") $enamoo->where("ORD.inflow = ?", 'sugi');
			elseif($param['sugi'] == "N") {
				$enamoo->where("ORD.inflow != ? OR ORD.inflow IS null", 'sugi');
			}
		}

		if($param['regdt_start']) {
			if(!$param['regdt_end']) $param['regdt_end'] = date('Ymd', G_CONST_NOW);

			$enamoo->where("OC.regdt BETWEEN ? AND ? ", array(Core::helper('Date')->min($param['regdt_start']), Core::helper('Date')->max($param['regdt_end'])));

			if ($checkout) {
				$checkout->where("O.OrderDate BETWEEN ? AND ? ", array(Core::helper('Date')->min($param['regdt_start']), Core::helper('Date')->max($param['regdt_end'])));
			}
		}

		if(count($param['type'])) {

			$subWhere = array();
			$co_subWhere = array();

			foreach($param['type'] as $v) {
				switch($v) {
					case '1':
						$subWhere[] = $enamoo->parse('ITM.cyn = ? AND ITM.dyn = ?', array('n','n'));
					break;

					case '2':
						$subWhere[] = $enamoo->parse('ITM.cyn = ?', 'y');
					break;
					case '3':
						$subWhere[] = $enamoo->parse('ITM.cyn = ?', 'r');
					break;
					case '4':
						$subWhere[] = $enamoo->parse('ITM.dyn = ?', 'y');
					break;
					case '5':
						$subWhere[] = $enamoo->parse('ITM.dyn = ? AND ITM.cyn = ?', array('r','y'));
					break;
					case '6':
						$subWhere[] = $enamoo->parse('ITM.dyn = ? AND ITM.cyn = ?', array('r','r'));
					break;
					case '7':
						$subWhere[] = $enamoo->parse('ITM.dyn = ? AND ITM.cyn = ?', array('e','e'));
					break;
				}
			}
			if(count($subWhere)) {
				$enamoo->where( '('.implode(' OR ',$subWhere).')' );
			}

			if ($checkout) {
				foreach($param['type'] as $v) {
					switch($v) {
						case '1':	// 취소완료
							$co_subWhere[] = $checkout->parse('PO.ProductOrderStatus = ? OR PO.ProductOrderStatus = ?', array('CANCELED','CANCELED_BY_NOPAYMENT'));
							// = $checkout_message_schema['extra_productOrderStatusType']['취소완료'];
							break;
						case '2':	// 환불접수

							break;
						case '3':	// 환불완료

							break;
						case '4':	// 반품접수
							$co_subWhere[] = $checkout->parse('PO.ProductOrderStatus IN (?,?) AND PO.ClaimType = ? AND PO.ClaimStatus = ? AND R.HoldbackStatus = ? AND R.HoldbackReason = ?', array('DELIVERING','DELIVERED','RETURN','RETURN_REQUEST','HOLDBACK','SELLER_CONFIRM_NEED'));

							// = $checkout_message_schema['extra_productOrderStatusType']['반품요청'];
							break;
						//case '5': $subWhere[] = '(ITM.dyn="r" and ITM.cyn="y")'; break;
						case '6':	// 반품완료
							$co_subWhere[] = $checkout->parse('PO.ProductOrderStatus = ?', 'RETURNED');
							// = $checkout_message_schema['extra_productOrderStatusType']['반품완료'];
							break;
						case '7':	// 교환완료
							$co_subWhere[] = $checkout->parse('PO.ProductOrderStatus = ?', 'EXCHANGED');

							// = $checkout_message_schema['extra_productOrderStatusType']['교환완료'];
							break;

					}
				}

				if(count($subWhere)) {
					$checkout->where( '('.implode(' OR ',$co_subWhere).')' );
				}
			}

		}
		else if ($checkout) {
			$checkout->where('PO.ClaimType > ?', '');
		}

		if($param['settlekind']) {
			$enamoo->where("ORD.settlekind = ?", $param['settlekind']);

			if ($checkout) {
				$tmpMap = array('a'=>'무통장입금','c'=>'신용카드','o'=>'계좌이체','v'=>'가상계좌');
				if(array_key_exists($param['settlekind'],$tmpMap)) {
					$checkout->where("O.PaymentMeans = ?", $tmpMap[$param['settlekind']]);
				}
				unset($tmpMap);
			}

		}

		if($param['sword'] && $param['skey']) {

			switch($param['skey']) {
				case 'all':
					$subWhere = array();
					$subWhere[] = $enamoo->parse('ORD.ordno = ?', $param['sword']);
					$subWhere[] = $enamoo->parse('ORD.nameOrder like ?', $this->db->wildcard($param['sword']));
					$subWhere[] = $enamoo->parse('ORD.nameReceiver like ?', $this->db->wildcard($param['sword']));
					$subWhere[] = $enamoo->parse('ORD.bankSender like ?', $this->db->wildcard($param['sword']));
					$subWhere[] = $enamoo->parse('MB.m_id = ?', $param['sword']);

					$enamoo->where( '('.implode(' OR ',$subWhere).')' );
					break;
				case 'ordno': $enamoo->where('ORD.ordno = ?', $param['sword']); break;
				case 'nameOrder': $enamoo->where('ORD.nameOrder like ?', $this->db->wildcard($param['sword'])); break;
				case 'nameReceiver': $enamoo->where('ORD.nameReceiver like ?', $this->db->wildcard($param['sword'])); break;
				case 'bankSender': $enamoo->where('ORD.bankSender like ?', $this->db->wildcard($param['sword'])); break;
				case 'm_id': $enamoo->where('MB.m_id = ?', $param['sword']); break;
			}

			if ($checkout) {

				switch($param['skey']) {
					case 'all':
						$subWhere = array();
						$subWhere[] = $checkout->parse('O.OrderID = ?', $param['sword']);
						$subWhere[] = $checkout->parse('O.OrdererName like ?', $this->db->wildcard($param['sword']));
						//$subWhere[] = $checkout->parse('PO.ShippingAddressName like ?', $this->db->wildcard($param['sword']));
						$subWhere[] = $checkout->parse('O.OrdererID = ?', $param['sword']);

						$checkout->where( '('.implode(' OR ',$subWhere).')' );
						break;
					case 'ordno': $checkout->where('O.OrderID = ?', $param['sword']); break;
					case 'nameOrder': $checkout->where('O.OrdererName like ?', $this->db->wildcard($param['sword'])); break;
					//case 'nameReceiver': $checkout->where('PO.ShippingAddressName like ?', $this->db->wildcard($param['sword'])); break;
					case 'm_id': $checkout->where('O.OrdererID = ?', $param['sword']); break;
				}

			}
		}

		$param['page_num'] = 20;
		if(!$param['page']) $param['page'] = 1;

		if (!$checkout) {
			$result = $this->db->utility()->getPaging($enamoo, $param['page_num'], $param['page']);
		}
		else {

			$union = $this->db->builder()->union($enamoo, $checkout);
			$union->order('orddt desc');

			unset($enamoo, $checkout);
			$result = $this->db->utility()->getPaging($union, $param['page_num'], $param['page']);

		}

		return $result;
	}

}
?>