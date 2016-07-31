<?
class get_faq extends GODO_DB_procedure {

	function execute() {

		$sno = @func_get_arg(0);

		$builder = $this->db->builder()->select();
		$builder->from(GD_FAQ)->where('sno = ?', $sno);

		return $builder->fetch(1);

	}

}
?>