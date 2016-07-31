<?
class get_code extends GODO_DB_procedure {

	function execute() {

		$groupcd = @func_get_arg(0);

		$builder = $this->db->builder()->select();
		$builder->from(GD_CODE);
		$builder->where('groupcd = ?', $groupcd);

		$builder->order('sort');

		return $this->db->utility()->getAll($builder);

	}
}

?>