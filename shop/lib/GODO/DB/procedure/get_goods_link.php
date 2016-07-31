<?
class get_goods_link extends GODO_DB_procedure {

	function execute() {

		$goodsno = @func_get_arg(0);
		$category = @func_get_arg(1);

		$builder = $this->db->builder()->select();
		$builder->from(GD_GOODS_LINK);

		$builder->where('goodsno = ?', $goodsno);

		if ($category)
			$builder->where('category = ?', $category);

		return $this->db->utility()->getAll($builder);

	}

}
?>
