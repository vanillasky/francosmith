<?
class update_list_delivery_useyn extends GODO_DB_procedure {

	function execute() {

		$deliverynos = @func_get_arg(0);

		$builder = $this->db->builder()->update();
		$builder->from(GD_LIST_DELIVERY);
		$builder->set(array(
			'useyn' => 'n'
		));
		$builder->query();

		$builder->reset()->update();
		$builder->from(GD_LIST_DELIVERY);
		$builder->set(array(
			'useyn' => 'y'
		));
		$builder->where('deliveryno IN (?)', array($deliverynos));

		return $builder->query();

	}

}
?>