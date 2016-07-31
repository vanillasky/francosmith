<?
class get_member_info extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);

		$builder = $this->db->builder()->select();

		$builder->from(array('MB'=>GD_MEMBER))->leftjoin(array('GRP'=>GD_MEMBER_GRP),'MB.level = GRP.level');

		if (is_array($param)) {

			foreach($param as $k => $v) {
				$_clause = sprintf('MB.%s = ?', $k);
				$builder->where($_clause, $v);
			}

		}
		else {
			$builder->where('MB.m_no = ?', (int)$param);
		}

		return $builder->fetch();

	}

}
?>