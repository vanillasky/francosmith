<?
class admin_order_repay extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);

		$builder = $this->db->builder()->select();
		$builder
		->from(
			array('CNSL'=>GD_ORDER_CANCEL)
			,array('canceldt' => 'regdt', 'nameCancel' => 'name','sno','bankcode','bankaccount','bankuser')
		)
		->join(
			 array('ITM' => GD_ORDER_ITEM)
			,'CNSL.ordno = ITM.ordno AND CNSL.sno = ITM.cancel'
			,null
		)
		->join(
			 array('ORD' => GD_ORDER)
			,'ORD.ordno = ITM.ordno'
			,array('ordno','nameOrder','orddt','step2','settlekind','settleprice','goodsprice','coupon', 'emoney', 'memberdc','enuri','eggFee','escrowyn','pgcancel','delivery','ncash_emoney')
		)
		->leftjoin(
			 array('MB' => GD_MEMBER)
			,'ORD.m_no = MB.m_no'
			,array('m_id','m_no')
		)
		->group('CNSL.sno')
		->order('CNSL.regdt DESC')
		->columns(array('cnt'=>$this->db->expression('COUNT(CNSL.sno)')))
		->columns(array('repay'=>$this->db->expression('SUM( (ITM.price - ITM.memberdc - ITM.coupon) * ITM.ea)')))
		;

		if($param['regdt_start']) {
			if(!$param['regdt_end']) $param['regdt_end'] = date('Ymd', G_CONST_NOW);

			$tmp_start = Core::helper('Date')->min($param['regdt_start']);
			$tmp_end = Core::helper('Date')->max($param['regdt_end']);

			switch($param['dtkind']) {
				case 'orddt': $builder->where('ORD.orddt BETWEEN ? AND ?', array($tmp_start,$tmp_end)); break;
				case 'cdt': $builder->where('CNSL.regdt BETWEEN ? AND ?', array($tmp_start,$tmp_end)); break;
			}

		}

		if($param['settlekind']) {
			$builder->where('ORD.settlekind = ?', $param['settlekind']);
		}

		if($param['bankcode'] && $param['bankcode'] != 'all') {
			$builder->where('CNSL.bankcode = ?', $param['bankcode']);
		}

		if($param['sword'] && $param['skey']) {

			switch($param['skey']) {
				case 'all':
					$subWhere = array();
					$subWhere[] = $builder->parse('ORD.ordno = ?', $param['sword']);
					$subWhere[] = $builder->parse('ORD.nameOrder like ?', $this->db->wildcard($param['sword']));
					$subWhere[] = $builder->parse('ITM.goodsnm like ?', $this->db->wildcard($param['sword']));
					$subWhere[] = $builder->parse('CNSL.name like ?', $this->db->wildcard($param['sword']));

					$builder->where( '('.implode(' OR ',$subWhere).')' );
					break;
				case 'ordno': $builder->where('ORD.ordno = ?', $param['sword']); break;
				case 'nameOrder': $builder->where('ORD.nameOrder like ?', $this->db->wildcard($param['sword'])); break;
				case 'goodsnm': $builder->where('ITM.goodsnm like ?', $this->db->wildcard($param['sword'])); break;
				case 'name': $builder->where('CNSL.name like ?', $this->db->wildcard($param['sword'])); break;
			}
		}

		$builder
			->where('ITM.istep > 40')
			->where('ITM.cyn = ?', 'y')
			->where('ITM.dyn IN (?)', array( array('n','r') ));

		if (!$param['page_size']) $param['page_size'] = 20;
		if (!$param['page']) $param['page'] = 1;

		$result = $this->db->utility()->getPaging($builder, $param['page_size'], $param['page']);

		return $result;

	}

}
?>