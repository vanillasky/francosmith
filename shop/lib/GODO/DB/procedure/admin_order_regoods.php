<?
class admin_order_regoods extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);

		// 체크아웃 주문 연동
		$checkoutapi = Core::config('checkoutapi');
		$checkout = (!($checkoutapi['cryptkey'] && $checkoutapi['integrateOrder']=='y')) ? false : $this->db->builder()->select();

		$enamoo = $this->db->builder()->select();
		$enamoo
		->from(array('CNSL'=>GD_ORDER_CANCEL),array('sno','code','canceldt' => 'regdt' ,'nameCancel' => 'name'))
		->join(array('ITM' => GD_ORDER_ITEM),'CNSL.ordno = ITM.ordno AND CNSL.sno = ITM.cancel',null)
		->join(array('ORD' => GD_ORDER),'ORD.ordno = ITM.ordno',array('ordno','nameOrder','orddt','settlekind','pg','ipay_payno','ipay_cartno','ncash_tx_id'))
		->leftjoin(array('MB' => GD_MEMBER),'ORD.m_no = MB.m_no',array('m_id','m_no'))
		->where('ITM.istep > ?', 40)
		->where('ITM.cyn = ?', 'y')
		->where('ITM.dyn = ?', 'y');

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
			->group('PO.OrderID, PO.ProductOrderStatus, PO.ClaimStatus')
			->where('PO.ClaimType > ?' , '')
			->where('PO.ClaimStatus IN (?,?)' , array('RETURN_REQUEST','EXCHANGE_REQUEST'));

			$checkout->columns(array(
				'_order_type' => $this->db->quote('checkout'),
				'sno' => null,
				'canceldt' => $this->db->expression('if(R.ClaimRequestDate>"",R.ClaimRequestDate, if(E.ClaimRequestDate>"",E.ClaimRequestDate, ""))'),
				'nameCancel' => null,
				'code' => $this->db->expression('if(R.ClaimRequestDate>"",R.ReturnReason, if(E.ClaimRequestDate>"",E.ExchangeReason, ""))'),
				'ordno' => 'O.OrderID',
				'orddt' => 'O.OrderDate',
				'nameOrder' => 'O.OrdererName',
				'settlekind' => 'O.PaymentMeans',
				'pg' => null,
				'ncash_tx_id' => null,
				'MB.m_no',
				'MB.m_id',
				'PO.PlaceOrderStatus',
				'PO.ProductOrderStatus',
				'PO.ClaimType',
				'PO.ClaimStatus',
				'ProductOrderIDList' => $this->db->expression('GROUP_CONCAT(PO.ProductOrderID SEPARATOR ",")'),
			));

			$enamoo->reset('column')->columns(array(
				'_order_type' => $this->db->quote('godo'),
				'CNSL.sno',
				'canceldt' => 'CNSL.regdt',
				'nameCancel' => 'CNSL.name',
				'CNSL.code',
				'ORD.ordno',
				'ORD.orddt',
				'ORD.nameOrder',
				'ORD.settlekind',
				'ORD.pg',
				'ORD.ncash_tx_id',
				'MB.m_no',
				'MB.m_id',
				'PlaceOrderStatus' => null,
				'ProductOrderStatus' => null,
				'ClaimType' => null,
				'ClaimStatus' => null,
				'ProductOrderIDList' => null,
			));

		}

		if($param['regdt_start']) {

			if(!$param['regdt_end']) $param['regdt_end'] = date('Ymd', G_CONST_NOW);

			$enamoo->where('CNSL.regdt BETWEEN ? AND ?', array(Core::helper('Date')->min($param['regdt_start']),Core::helper('Date')->max($param['regdt_end'])));
		}

		if($param['cancelkey'] && $param['cancelkey'] != 'all') {
			$enamoo->where('CNSL.code = ?', $param['cancelkey']);
		}

		if($param['sword'] && $param['skey']) {

			switch($param['skey']) {
				case 'all':
					$subWhere = array();
					$subWhere[] = $enamoo->parse('ORD.ordno = ?', $param['sword']);
					$subWhere[] = $enamoo->parse('ORD.nameOrder like ?', $enamoo->wildcard($param['sword']));
					$subWhere[] = $enamoo->parse('ITM.goodsnm like ?', $enamoo->wildcard($param['sword']));
					$subWhere[] = $enamoo->parse('CNSL.name like ?', $enamoo->wildcard($param['sword']));

					$enamoo->where( '('.implode(' OR ',$subWhere).')' );
					break;
				case 'ordno': $enamoo->where('ORD.ordno = ?', $param['sword']); break;
				case 'nameOrder': $enamoo->where('ORD.nameOrder like ?', $enamoo->wildcard($param['sword'])); break;
				case 'goodsnm': $enamoo->where('ITM.goodsnm like ?', $enamoo->wildcard($param['sword'])); break;
				case 'name':  $enamoo->where('CNSL.name like ?', $enamoo->wildcard($param['sword'])); break;
			}
		}

		if (!$param['page_size']) $param['page_size'] = 20;
		if (!$param['page']) $param['page'] = 1;

		if (!$checkout) {
			$result = $this->db->utility()->getPaging($enamoo, $param['page_size'], $param['page']);
		}
		else {

			$union = $this->db->builder()->union($enamoo, $checkout);
			$union->order('canceldt desc');
			unset($enamoo, $checkout);

			$result = $this->db->utility()->getPaging($union, $param['page_size'], $param['page']);

		}

		return $result;
	}

}
?>