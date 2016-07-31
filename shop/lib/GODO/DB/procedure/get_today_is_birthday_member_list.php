<?
class get_today_is_birthday_member_list extends GODO_DB_procedure {

	function execute() {

		$builder = $this->db->builder()->select();
		$builder->from(GD_MEMBER, array('m_id','name','mobile','sms','calendar'));
		$builder->where('birth = ?' , date('md', G_CONST_NOW));

		$result = $this->db->utility()->getAll($builder);

		return $result;
	}

}
?>