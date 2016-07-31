<?
class update_env extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);
		$category = @func_get_arg(1);
		$name = @func_get_arg(2);

		$builder = $this->db->builder()->update();
		$builder->from(GD_ENV);
		$builder->set($param);

		if ($category)
			$builder->where('category = ?', $category);

		if ($name)
			$builder->where('name = ?', $name);

		return
			  $builder->has('where')
			? $builder->query()
			: false;
	}

}
?>