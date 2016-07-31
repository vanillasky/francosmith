<?
class get_env extends GODO_DB_procedure {

	function execute() {

		$category = @func_get_arg(0);
		$name = @func_get_arg(1);

		$builder = $this->db->builder()->select();
		$builder->from(GD_ENV, 'value');
		if ($category)
			$builder->where('category = ?', $category);

		if ($name)
			$builder->where('name = ?', $name);

		return $builder->fetch();

	}

}
?>