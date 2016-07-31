<?
class get_admin_member_list extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);

		$builder = $this->db->builder()->select();
		$builder->from(GD_MEMBER);

		if ($param['skey'] && $param['sword']){
			if ( $param['skey']== 'all' ){
				$builder->search(GD_MEMBER, 'm_no', array('m_id','name','email','phone','mobile'), $param['sword']);
			}
			else {
				$builder->search(GD_MEMBER, 'm_no', $param['skey'], $param['sword']);
			}
		}

		if(!$param['grpType']){
			$builder->where('level >= 80');
		}else{
			$builder->where('level < 80');
		}

		$builder->where('m_id != ?' , 'godomall');
		$builder->order($param['sort']);

		$param['page_num'] = !$param['page_num'] ? 20 : $param['page_num'];
		$param['page'] = !$param['page'] ? 1 : $param['page'];

		return $this->db->utility()->getPaging($builder, $param['page_num'], $param['page']);

	}

}
?>