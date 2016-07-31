<?
class delete_goods_review extends GODO_DB_procedure {

	function execute() {

		$sno = @func_get_arg(0);

		$builder = $this->db->builder()->delete();
		$builder->from(GD_GOODS_REVIEW)->where('sno = ?', $sno);

		return $builder->query();

	}

}
?>