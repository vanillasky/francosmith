<?
class get_goods_count extends GODO_DB_procedure {

	function execute() {

		$goodsno = @func_get_arg(0);

		$builder = $this->db->builder()->select();
		$builder->from(GD_GOODS, $this->db->expression('count(*)'));

		if ($goodsno)
			$builder->where('goodsno = ?', $goodsno);

		return $builder->fetch();

	}

}
?>