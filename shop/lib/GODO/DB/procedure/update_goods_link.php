<?
class update_goods_link extends GODO_DB_procedure {

	function execute() {

		$param = 	@func_get_arg(0);
		$goodsno = @func_get_arg(1);
		$category = @func_get_arg(2);

		$builder = $this->db->builder()->update();
		$builder->from(GD_GOODS_LINK);
		$builder->set($param);

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
