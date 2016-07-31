<?
class admin_order_taxapp extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);

		$builder = $this->db->builder()->select();

		$builder
		->from(
			array('TAX'=>GD_TAX)
			,array('*','tax_step'=>'step')
		)
		->join(
			 array('ORD' => GD_ORDER)
			,'TAX.ordno = ORD.ordno'
			,array('step','step2','nameOrder','prn_settleprice','cashreceipt')
		)
		->leftjoin(
			 array('MB' => GD_MEMBER)
			,'TAX.m_no = MB.m_no'
			,array('m_id','m_no','m_name'=>'name')
		);

		$builder->where('TAX.step = 0');

		if ($param[skey] && $param['sword']){

			$sordno = array();

			if ( $param[skey]== 'all' || $param[skey]== 'm_name' ) {
				$_builder = $this->db->builder()->select();
				$_builder
				->from(
					array('TAX'=>GD_TAX)
					,array('ordno')
				)
				->join(
					 array('ORD' => GD_ORDER)
					,'TAX.ordno = ORD.ordno'
					,null
				)
				->where('ORD.nameOrder like ?', $this->db->wildcard($param['sword']));

				$_res = $this->db->query($_builder->toString());
				$sordno = $_res->fetchAll();
			}

			if ( $param[skey]== 'all' ) {

				$builder->where(
					"(CONCAT( TAX.company, TAX.name, ifnull(ORD.name, ''), ifnull(m_id, ''), ordno ) LIKE ?" . (count($sordno) ? " OR FIND_IN_SET(ordno, '" . implode(",", $sordno) . "')" : "") . ")", $this->db->wildcard($param['sword'])
				);

			}
			else if ( $param[skey]== 'm_id' ) $builder->where('MB.m_id LIKE ?', $this->db->wildcard($param['sword']));
			else if ( $param[skey]== 'm_name' ) $builder->where('MB.m_id LIKE ?'.(count($sordno) ? " or find_in_set(ordno, '" . implode(",", $sordno) . "')" : ""), $this->db->wildcard($param['sword']));
			else $builder->where(sprintf('TAX.%s LIKE ?', $param['skey']), $this->db->wildcard($param['sword']));
		}

		if ( $param[sbusino] <> '' ) $builder->where('TAX.busino = ?',$param[sbusino]);	# 분류검색

		if ($param[sregdt][0] && $param[sregdt][1]) {

			$tmp_start = Core::helper('Date')->min($param['sregdt'][0]);
			$tmp_end = Core::helper('Date')->max($param['sregdt'][1]);

			$builder->where('TAX.issuedate BETWEEN ? AND ?',array($tmp_start,$tmp_end));
		}

		return $this->db->utility()->getPaging($builder, 20, $param['page']);
	}

}
?>