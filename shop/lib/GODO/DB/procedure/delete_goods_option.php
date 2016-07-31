<?
class delete_goods_option extends GODO_DB_procedure {

	function execute() {

		$goodsno = @func_get_arg(0);

		$builder = $this->db->builder()->delete();
		$builder->from(GD_GOODS_OPTION)->where('goodsno = ?', $goodsno);

		return $builder->query();

	}

}
?>