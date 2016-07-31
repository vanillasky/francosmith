<?
class get_attendance_info extends GODO_DB_procedure {

	function execute() {

		$attendance_no = @func_get_arg(0);

		$builder = $this->db->builder()->select();

		$builder
			->from(GD_ATTENDANCE)
			->where("attendance_no = ?", $attendance_no );

		return $builder->fetch(1);

	}

}
?>