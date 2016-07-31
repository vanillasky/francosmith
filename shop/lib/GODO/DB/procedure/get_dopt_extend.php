<?
class get_dopt_extend extends GODO_DB_procedure {

	function execute() {

		$groupcd = @func_get_arg(0);

		$builder = $this->db->builder()->select();
		$builder->from(GD_DOPT_EXTEND);

		$builder->order('sno desc');

		return $this->db->utility()->getAll($builder);

	}
}

?>