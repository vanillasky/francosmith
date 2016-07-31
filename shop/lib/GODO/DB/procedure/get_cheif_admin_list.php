<?
class get_cheif_admin_list extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);

		$builder = $this->db->builder()->select();
		$builder->from(GD_MEMBER, array('m_no','m_id','name'));

		$builder->where('m_id != ?' , 'godomall');
		$builder->where('level = 100');
		$builder->order('m_id');

		return $this->db->utility()->getAll($builder);

	}

}
?>