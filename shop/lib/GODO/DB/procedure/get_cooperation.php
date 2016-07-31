<?
class get_cooperation extends GODO_DB_procedure {

	function execute() {

		$sno = @func_get_arg(0);

		$builder = $this->db->builder()->select();
		$builder->from(GD_COOPERATION)->where('sno = ?', $sno);

		return $builder->fetch(1);

	}

}
?>