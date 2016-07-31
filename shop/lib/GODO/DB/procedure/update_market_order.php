<?
class update_market_order extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);
		$order_idx = @func_get_arg(1);

		$builder = $this->db->builder()->update();
		$builder->from(GD_MARKET_ORDER);
		$builder->set($param);

		$builder->where('order_idx = ?', $order_idx);

		return
			  $builder->has('where')
			? $builder->query()
			: false;
	}

}
?>