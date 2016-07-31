<?
class admin_index_recent_goods extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);

		$builder = $this->db->builder()->select();

		$builder
		->from(GD_GOODS)
		->limit($param['limit'])
		->order('regdt desc');

		return $this->db->utility()->getAll($builder);

	}

}
?>