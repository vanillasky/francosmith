<?
class update_list_delivery extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);
		$deliveryno = @func_get_arg(1);

		$builder = $this->db->builder()->update();
		$builder->from(GD_LIST_DELIVERY);
		$builder->set($param);
		$builder->where('deliveryno = ?', $deliveryno);

		return $builder->query();

	}

}
?>