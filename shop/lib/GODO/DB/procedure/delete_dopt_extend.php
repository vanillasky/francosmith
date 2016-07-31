<?
class delete_dopt_extend extends GODO_DB_procedure {

	protected function execute() {

		$sno = @func_get_arg(0);

		$builder = $this->db->builder()->delete();
		$builder->from(GD_DOPT_EXTEND);
		$builder->where('sno = ?', $sno);

		return $builder->query();

	}
}

?>