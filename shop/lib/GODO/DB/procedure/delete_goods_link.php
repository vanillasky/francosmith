<?
class delete_goods_link extends GODO_DB_procedure {

	function execute() {

		$goodsno = @func_get_arg(0);
		$category = @func_get_arg(1);

		$builder = $this->db->builder()->delete();
		$builder->from(GD_GOODS_LINK);

		if ($goodsno)
			$builder->where('goodsno = ?', $goodsno);

		if ($category)
			$builder->where('category = ?', $category);

		return
			  $builder->has('where')
			? $builder->query()
			: false;
	}

}
?>
