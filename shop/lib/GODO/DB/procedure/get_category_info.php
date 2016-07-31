<?
class get_category_info extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);	// array or string

		$builder = $this->db->builder()->select();

		$builder->from(GD_CATEGORY);

		if (is_array($param)) {

			if (isset($param['category']))
				$builder->where("category like ? ", $this->db->wildcard($param['category'],1));

			if (isset($param['depth']))
				$builder->where("depth = ?", $param['depth']);

			if (isset($param['hidden']))
				$builder->where("hidden = ?", $param['hidden']);

			$builder->order('sort');

			return $this->db->utility()->getAll($builder);

		}
		else {
			$builder->where("category = ? ", $param);

			return $builder->fetch();

		}

	}

}
?>