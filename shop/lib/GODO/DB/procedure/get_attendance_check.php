<?
class get_attendance_check extends GODO_DB_procedure {

	function execute() {

		$check_no = @func_get_arg(0);

		$builder = $this->db->builder()->select();
		$builder->from(array('AC'=>GD_ATTENDANCE_CHECK), array('check_no','member_no'));
		$builder->join(array('MB'=>GD_MEMBER), 'AC.member_no = MB.m_no', 'mobile');

		if (is_array($check_no))
			$builder->where('AC.check_no IN (?)', array($check_no));
		else
			$builder->where('AC.check_no = ?', $check_no);

		return $this->db->utility()->getAll($builder);

	}

}
?>