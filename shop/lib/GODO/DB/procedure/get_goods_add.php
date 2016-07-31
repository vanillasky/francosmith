<?
class get_goods_add extends GODO_DB_procedure {

	function execute() {

		$goodsno = @func_get_arg(0);

		$builder = $this->db->builder()->select();

		$builder->from(GD_GOODS_ADD);
		$builder->where('goodsno = ?', $goodsno);

		$result = $this->db->utility()->getAll($builder);

		return $result;

	}
}

?>