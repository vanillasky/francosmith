<?
class delete_member_qna extends GODO_DB_procedure {

	function execute() {

		$sno = @func_get_arg(0);

		$builder = $this->db->builder()->delete();
		$builder->from(GD_MEMBER_QNA)->where('sno = ?', $sno);

		return $builder->query();

	}

}
?>