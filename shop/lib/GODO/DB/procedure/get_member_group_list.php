<?
class get_member_group_list extends GODO_DB_procedure {

	function execute() {

		$level = @func_get_arg(0);

		$builder = $this->db->builder()->select();
		$builder->from(GD_MEMBER_GRP);
		$builder->order('level');

		return $this->db->utility()->getAll($builder);
	}
}

?>