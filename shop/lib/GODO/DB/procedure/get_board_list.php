<?
class get_board_list extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);

		$builder = $this->db->builder()->select();

		$builder->from(GD_BOARD);

		$param['page_num'] = !$param['page_num'] ? 20 : $param['page_num'];
		$param['page'] = !$param['page'] ? 1 : $param['page'];

		return $this->db->utility()->getPaging($builder, $param['page_num'], $param['page']);

	}

}
?>