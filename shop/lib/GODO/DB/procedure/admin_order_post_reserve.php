<?
class admin_order_post_reserve extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);

		$builder = $this->db->builder()->select();

		$builder
		->from(
			array('ITM'=>GD_ORDER_ITEM)
			,array('dvcode','ordno','goodsnm')
		)
		->leftjoin(
			 array('PST' => GD_GODOPOST_RESERVED)
			,'PST.deliverycode = ITM.dvcode'
			,null
		)
		->leftjoin(
			 array('ORD' => GD_ORDER)
			,'ORD.ordno = ITM.ordno'
			,array('nameOrder','step','step2','orddt','settleprice')
		)
		->columns( array('goods_cnt' => $this->db->expression('COUNT(ITM.sno)')))
		->group('ITM.dvcode')
		;

		$builder->where('ITM.dvno = 100');
		$builder->where('PST.deliverycode IS NULL');

		$param['page_size'] = isset($param['page_size']) ? $param['page_size'] : 10;

		return $this->db->utility()->getPaging($builder, $param['page_size'], $param['page']);
	}
}
?>