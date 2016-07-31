<?
class update_goods_related extends GODO_DB_procedure {

	function execute() {

		$param = 	@func_get_arg(0);
		$goodsno = @func_get_arg(1);
		$r_goodsno = @func_get_arg(2);

		$builder = $this->db->builder()->update();
		$builder->from(GD_GOODS_RELATED);
		$builder->set($param);

		if ($goodsno)
			$builder->where('goodsno = ?', $goodsno);

		if ($r_goodsno)
			$builder->where('r_goodsno = ?', $r_goodsno);

		return
			  $builder->has('where')
			? $builder->query()
			: false;
	}

}
?>
