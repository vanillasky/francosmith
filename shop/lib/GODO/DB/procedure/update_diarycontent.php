<?
class update_diarycontent extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);
		$diary_date = @func_get_arg(1);

		$builder = $this->db->builder()->update();
		$builder->from(GD_DIARYCONTENT);
		$builder->set($param);
		$builder->where('diary_date = ?', $diary_date);

		return $builder->query();

	}

}
?>