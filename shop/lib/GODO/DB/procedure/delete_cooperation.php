<?
class delete_cooperation extends GODO_DB_procedure {

	function execute() {

		$sno = @func_get_arg(0);

		$builder = $this->db->builder()->delete();
		$builder->from(GD_COOPERATION)->where('sno = ?', $sno);

		return $builder->query();

	}

}
?>