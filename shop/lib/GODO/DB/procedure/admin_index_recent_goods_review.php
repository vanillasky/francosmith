<?
class admin_index_recent_goods_review extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);

		$builder = $this->db->builder()->select();

		$builder
		->from(GD_GOODS_REVIEW)
		->limit($param['limit'])
		->order('sno desc');

		return $this->db->utility()->getAll($builder);

	}

}
?>