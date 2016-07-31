<?
class delete_goods_qna extends GODO_DB_procedure {

	function execute() {

		$sno = @func_get_arg(0);

		$_doc = $this->db->procedure('get_goods_qna', $sno);

		$builder = $this->db->builder()->delete();
		$builder->from(GD_GOODS_QNA);

		if ($_doc['parent'] == $sno)
			$builder->where('parent = ?', $sno);
		else
			$builder->where('sno = ?', $sno);

		return $builder->query();

	}

}
?>