<?
class get_contextmenu_list extends GODO_DB_procedure {

	function execute() {

		$m_no = @func_get_arg(0);

		$builder = $this->db->builder()->select();

		$builder->from(GD_CONTEXTMENU, array('name','url','target'));
		$builder->where('m_no = ?', $m_no);

		$result = $this->db->utility()->getAll($builder);

		return $result;
	}

}
?>