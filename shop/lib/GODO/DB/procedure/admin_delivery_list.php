<?
class admin_delivery_list extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);

		$builder = $this->db->builder()->select();

		$builder->from(
			GD_LIST_DELIVERY,
			array('deliveryno','deliverycomp','useyn')
			);

		if (isset($param['useyn']))
			$builder->where('useyn = ?', $param['useyn']);

		$builder->order('deliverycomp asc');

		return $this->db->utility()->getAll($builder);

	}

}
?>