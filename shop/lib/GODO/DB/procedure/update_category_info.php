<?
class update_category_info extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);
		$category = @func_get_arg(1);

		$builder = $this->db->builder()->update();
		$builder->from(GD_CATEGORY);
		$builder->set($param);
		$builder->where('category = ?', $category);

		return $builder->query();

	}

}
?>