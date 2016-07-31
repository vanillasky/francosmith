<?
class save_goods_review extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);
		$param['_order'] = $this->db->procedure('get_document_order_sequence',GD_GOODS_REVIEW, $param['notice'], $param['parent']);

		$builder = $this->db->builder()->insert();
		$builder->into(GD_GOODS_REVIEW);
		$builder->set($param);

		return $builder->query();

	}

}
?>