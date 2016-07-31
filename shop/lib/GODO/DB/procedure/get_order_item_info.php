<?
class get_order_item_info extends GODO_DB_procedure {

	function execute() {

		$ordno = @func_get_arg(0);
		$ordno = @func_get_arg(1);

		$builder = $this->db->builder()->update();
		$builder->from(GD_ORDER_ITEM)->set($param);
		$builder->where('ordno = ?', $ordno);

		return $this->db->utility()->getAll($builder);

	}

}
?>