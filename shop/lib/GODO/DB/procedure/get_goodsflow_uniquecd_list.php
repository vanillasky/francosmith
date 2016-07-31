<?
class get_goodsflow_uniquecd_list extends GODO_DB_procedure {

	function execute() {

		$UniqueCd = @func_get_arg(0);

		$builder = $this->db->builder()->select();
		$builder->from(array('GF'=>GD_GOODSFLOW), null);
		$builder->join(array('OD'=>GD_GOODSFLOW_ORDER_MAP), 'GF.sno = OD.goodsflow_sno','ordno');
		$builder->join(array('O'=>GD_ORDER), 'OD.ordno = O.ordno',null);
		$builder->where('GF.UniqueCd = ?', $UniqueCd);
		$builder->group('O.ordno');

		return $this->db->utility()->getAll($builder);

	}
}

?>