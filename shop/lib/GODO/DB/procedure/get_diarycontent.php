<?
class get_diarycontent extends GODO_DB_procedure {

	function execute() {

		$diary_date = @func_get_arg(0);

		$builder = $this->db->builder()->select();
		$builder->from(GD_DIARYCONTENT);
		$builder->where('diary_date = ?', $diary_date);

		return $builder->fetch();	// @юс╫ц : getOne, limit 1

	}

}
?>