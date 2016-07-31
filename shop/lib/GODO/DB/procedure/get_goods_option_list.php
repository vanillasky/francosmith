<?
class get_goods_option_list extends GODO_DB_procedure {

	function execute() {

		$goodsno = @func_get_arg(0);

		$builder = $this->db->builder()->select();

		$builder->from(GD_GOODS_OPTION);
		$builder->where('goodsno = ?', $goodsno);

		$builder->order('optno');

		return $this->db->utility()->getAll($builder);

	}
}

?>