<?
class get_list_bank extends GODO_DB_procedure {

	function execute() {

		$sno = @func_get_arg(0);

		$builder = $this->db->builder()->select();
		$builder->from(GD_LIST_BANK);
		$builder->where('useyn = ?', 'y');

		if ($sno) {
			$builder->where('sno = ?', $sno);
			$result = $this->db->utility()->getOne($builder);
			$result = $result->current();
		}
		else {
			$result = $this->db->utility()->getAll($builder);
		}

		return $result;

	}

}
?>