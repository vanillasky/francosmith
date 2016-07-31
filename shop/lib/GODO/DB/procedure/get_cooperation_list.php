<?
class get_cooperation_list extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);

		$builder = $this->db->builder()->select();
		$builder->from(GD_COOPERATION);

		if ($param[skey] && $param[sword]){
			if ( $param[skey]== 'all' ){
				$where[] = "concat( name, title, content, reply ) like '%$param[sword]%'";
			}
			else {
				$_clause = sprintf('%s like ?');
				$builder->where($_clause, $this->db->wildcard($param['sword']));
			}
		}

		# 분야검색
		if ( $param[sitemcd] <> '' )
			$builder->where('itemcd = ?', $param[sitemcd]);

		# 답변 여부
		if ( $param[sreplyyn] == 'Y' )
			$builder->where('reply > ?', '');
		else if ( $param[sreplyyn] == 'N' )
			$builder->where('reply is null or reply = ?', '');

		# 답변메일여부
		if ( $param[smailyn] == 'Y' )
			$builder->where('maildt > ?', '');
		else if ( $param[smailyn] == 'N' )
			$builder->where('maildt is null or maildt = ?', '');

		if ($param[sregdt][0] && $param[sregdt][1]) {
			$builder->where('regdt between ? and ?' , array(
				Core::helper('Date')->min($param[sregdt][0]),
				Core::helper('Date')->max($param[sregdt][1])
			));
		}

		if ($param[sreplydt][0] && $param[sreplydt][1]) {
			$builder->where('replydt between ? and ?' , array(
				Core::helper('Date')->min($param[sreplydt][0]),
				Core::helper('Date')->max($param[sreplydt][1])
			));
		}

		if ($param[smaildt][0] && $param[smaildt][1]) {
			$builder->where('maildt between ? and ?' , array(
				Core::helper('Date')->min($param[smaildt][0]),
				Core::helper('Date')->max($param[smaildt][1])
			));
		}

		$param['page_num'] = !$param['page_num'] ? 20 : $param['page_num'];
		$param['page'] = !$param['page'] ? 1 : $param['page'];

		return $this->db->utility()->getPaging($builder, $param['page_num'], $param['page']);

	}

}
?>