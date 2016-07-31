<?
class update_goods_add extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);
		$sno = @func_get_arg(1);
		$goodsno = @func_get_arg(2);

		$builder = $this->db->builder()->update();
		$builder->from(GD_GOODS_ADD);
		$builder->set($param);

		if ($goodsno)
			$builder->where('goodsno = ?', $goodsno);

		if ($sno)
			$builder->where('sno = ?', $sno);

		return
			  $builder->has('where')
			? $builder->query()
			: false;

	}
}

?>