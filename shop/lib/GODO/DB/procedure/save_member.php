<?
class save_member extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);

		$builder = $this->db->builder()->insert();
		$builder->into(GD_MEMBER);
		$builder->set($param);

		if ($builder->query()) {

			$m_no = $builder->lastID();

			// 색인 추출 및 갱신
			$sc = $this->db->indexer();
			$sc->generate(GD_MEMBER, $m_no, $param, array('m_id','name','nickname','email','phone','mobile','recommid','company'));

			return $m_no;
		}
		else return false;

	}

}
?>