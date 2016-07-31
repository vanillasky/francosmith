<?
class get_document_order_sequence extends GODO_DB_procedure {

	// gd_member_qna, gd_goods_review, gd_goods_qna 테이블의 게시물 정렬 키를 얻음
	function execute() {

		$table = @func_get_arg(0);
		$notice = @func_get_arg(1);
		$parent = @func_get_arg(2);

		if (!in_array($table,array(GD_MEMBER_QNA,GD_GOODS_REVIEW,GD_GOODS_QNA))) return false;

		$builder = $this->db->builder()->select();
		$builder->from($table, $this->db->func('min','__order'));

		// 공지
		if ($notice) {

			list($_order2) = $builder->fetch();
			$_order = floor($_order2 - 1);

		}
		// 일반
		else {

			$builder->where('notice <> ?', '1');

			// 답변글인 경우
			if ($parent) {
				list($_order1) = $this->db->builder()->select()->from($table, '_order')->where('sno = ?', $parent)->fetch();
				list($_order2) = $builder->where('_order > ?', $_order1)->fetch();

				$_order = (float)(($_order1 + $_order2) / 2);
			}
			else {
				list($_order2) = $builder->fetch();
				$_order = floor($_order2 - 1);
			}

			// 공지글 재정렬
			$this->db->update($table)->set(array('_order' => $this->db->expression('_order - 1')))->where('notice = ?', '1')->query();

		}

		return $_order;

	}

}
?>