<?
class delete_list_bank extends GODO_DB_procedure {

	function execute() {

		// 삭제하지 않고, useyn 컬럼만 업데이트.

		$sno = @func_get_arg(0);

		$builder = $this->db->builder()->update();
		$builder->from(GD_LIST_BANK);
		$builder->set(array('useyn' => 'n'))->where('sno = ?', $sno);

		return $builder->query();

	}

}
?>