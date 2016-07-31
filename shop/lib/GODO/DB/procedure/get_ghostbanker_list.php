<?
class get_ghostbanker_list extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);

		$builder = $this->db->builder()->select();
		$builder->from(GD_GHOSTBANKER);

		$builder->where(' date >= ?', $param['expire']);

		if ($param['date'] != '')
			$builder->where('date = ?', $param['date']);

		if ($param['name'] != '')
			$builder->where('name like ?', $this->db->wildcard($param['name']));

		$builder->order('date asc, name asc');

		$result = $this->db->utility()->getPaging($builder, $param['page_num'], $param['page']);

		return $result;

	}

}
?>