<?
class get_list_delivery_list extends GODO_DB_procedure {

	function execute() {

		$builder = $this->db->builder()->select();
		$builder->from(GD_LIST_DELIVERY);

		$builder->where('useyn = ?', 'y');

		$builder->order('deliverycomp');

		return $this->db->utility()->getAll($builder);

	}

}
?>