<?
class get_faq_list extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);

		$builder = $this->db->builder()->select();
		$builder->from(GD_FAQ);

		if ($param[skey] && $param[sword]){
			if ( $param[skey]== 'all' ){
				$builder->where('concat( question, descant, answer ) like ?', $this->db->wildcard($param['sword']));
			}
			else {
				$_clause = sprintf('%s like ?');
				$builder->where($_clause, $this->db->wildcard($param['sword']));
			}
		}

		# 분류검색
		if ( $param[sitemcd] <> '' && $param[sitemcd] <> 'all' )
			$builder->where('itemcd = ?', $param[sitemcd]);

		# 베스트여부
		if ( $param[sbest] <> '' )
			$builder->where('best = ?', $param[sbest]);

		if ($param[sregdt][0] && $param[sregdt][1]) {
			$builder->where('regdt between ? and ?' , array(
				Core::helper('Date')->min($param[sregdt][0]),
				Core::helper('Date')->max($param[sregdt][1])
			));
		}

		$param['page_num'] = !$param['page_num'] ? 20 : $param['page_num'];
		$param['page'] = !$param['page'] ? 1 : $param['page'];

		return $this->db->utility()->getPaging($builder, $param['page_num'], $param['page']);

	}

}
?>