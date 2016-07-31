<?
class update_member_info extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);
		$m_no = @func_get_arg(1);
		$m_id = @func_get_arg(2);

		$builder = $this->db->builder()->update();
		$builder->from(GD_MEMBER);
		$builder->set($param);

		if ($m_no) {
			$builder->where('m_no = ?', $m_no);
		}

		if ($m_id) {
			$builder->where('m_id = ?', $m_id);
		}

		if ($builder->has('where') && $builder->query()) {

			if (! $m_no) // $m_no 가 없을때 $m_id 는 반드시 넘어옴
				list($m_no) = $this->db->select(GD_MEMBER, 'm_no')->where('m_id = ?', $m_id)->limit(1)->fetch();

			if (! $m_id) // $m_id 가 없을때 $m_no 는 반드시 넘어옴
				list($m_id) = $this->db->select(GD_MEMBER, 'm_id')->where('m_no = ?', $m_no)->limit(1)->fetch();

			$param['m_id'] = $m_id;

			// 색인 추출 및 갱신
			$sc = $this->db->indexer();
			$sc->generate(GD_MEMBER, $m_no, $param, array('m_id','name','nickname','email','phone','mobile','recommid','company'));

			return true;
		}
		else return false;

	}

}
?>