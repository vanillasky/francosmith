<?
class delete_goods_related extends GODO_DB_procedure {

	function execute() {

		$goodsno = @func_get_arg(0);
		$r_goodsno = @func_get_arg(1);
		$r_type = @func_get_arg(2);

		$builder = $this->db->builder()->delete();
		$builder->from(GD_GOODS_RELATED);

		if ($goodsno)
			$builder->where('goodsno = ?', $goodsno);

		if ($r_goodsno)
			$builder->where('r_goodsno = ?', $r_goodsno);

		if ($r_type)
			$builder->where('r_type = ?', $r_type);

		return
			  $builder->has('where')
			? $builder->query()
			: false;
	}

}
?>
