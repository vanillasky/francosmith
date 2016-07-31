<?
class get_list_delivery extends GODO_DB_procedure {

	function execute() {

		$deliveryno = @func_get_arg(0);

		$builder = $this->db->builder()->select();
		$builder->from(GD_LIST_DELIVERY);

		$builder->where('deliveryno = ?', $deliveryno);

		return $builder->fetch();

	}

}
?>