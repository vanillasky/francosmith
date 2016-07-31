<?
class get_dopt_extend_list extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);

		$builder = $this->db->builder()->select();
		$builder->from(GD_DOPT_EXTEND);

		$builder->order('sno');

		$result = $this->db->utility()->getPaging($builder, $param['page_num'], $param['page']);

		return $result;

	}

}
?>