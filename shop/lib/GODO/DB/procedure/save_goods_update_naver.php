<?
class save_goods_update_naver extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);

		$builder = $this->db->builder()->insert();
		$builder->into(GD_GOODS_UPDATE_NAVER);
		$builder->set($param);

		return $builder->query();

	}

}
?>