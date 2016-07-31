<?
class update_tax extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);
		$sno = @func_get_arg(1);

		$builder = $this->db->builder()->update();
		$builder->from(GD_TAX);
		$builder->set($param);
		$builder->where('sno = ?', $sno);

		return $builder->query();
	}

}
?>