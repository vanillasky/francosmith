<?
class get_diarycontent_by_month extends GODO_DB_procedure {

	function execute() {

		$diary_date = @func_get_arg(0);

		$builder = $this->db->builder()->select();
		$builder->from(GD_DIARYCONTENT,null);
		$builder->columns(array(
			'diary_date' => $this->db->expression('right( diary_date, 2 )'),
			'diary_title',
			'diary_content',

		));
		$builder->where('diary_date like ?', $this->db->wildcard($diary_date,1));
		$builder->order('diary_date');

		return $this->db->utility()->getAll($builder);

	}

}
?>