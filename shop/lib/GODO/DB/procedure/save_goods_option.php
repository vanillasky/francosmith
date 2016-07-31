<?
class save_goods_option extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);

		$builder = $this->db->builder()->insert();
		$builder->into(GD_GOODS_OPTION);
		$builder->set($param);

		return $builder->query();

	}
}

?>