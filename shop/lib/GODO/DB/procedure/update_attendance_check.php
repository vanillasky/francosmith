<?
class update_attendance_check extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);
		$check_no = @func_get_arg(1);

		$builder = $this->db->builder()->update();
		$builder->from(GD_ATTENDANCE_CHECK);
		$builder->set($param);
		$builder->where('check_no = ?', $check_no);

		return $builder->query();

	}
}

?>