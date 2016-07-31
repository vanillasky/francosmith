<?
class save_member_qna extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);
		$param['_order'] = $this->db->procedure('get_document_order_sequence',GD_MEMBER_QNA, $param['notice'], $param['parent']);

		$builder = $this->db->builder()->insert();
		$builder->into(GD_MEMBER_QNA);
		$builder->set($param);

		if ($builder->query()) {

			if (!isset($param['parent'])) {
				$_parent = $this->db->lastID();
				$this->db->builder()->update()->from(GD_MEMBER_QNA)->set(array(
					'parent' => $_parent
				))->where('sno = ?', $_parent)->query();
			}

			return true;
		}
		else return false;

	}

}
?>