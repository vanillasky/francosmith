<?
class save_env extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);

		$builder = $this->db->builder()->insert();
		$builder->into(GD_ENV);
		$builder->set($param);

		return $builder->query();

	}

}
?>