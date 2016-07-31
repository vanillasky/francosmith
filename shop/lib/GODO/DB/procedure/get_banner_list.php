<?
class get_banner_list extends GODO_DB_procedure {

	function execute() {

		global $cfg;

		$param = @func_get_arg(0);

		$builder = $this->db->builder()->select();

		$builder->from(GD_BANNER, array('sno','loccd','img','regdt','sort'));

		$builder->where('tplSkin = ?', $cfg['tplSkinWork']);

		if ( $param['sloccd'] <> '' && $param['sloccd'] <> 'all' )
			$builder->where('loccd = ?', $param['sloccd']);

		$builder->order($param['sort']);

		$param['page_num'] = !$param['page_num'] ? 10 : $param['page_num'];
		$param['page'] = !$param['page'] ? 1 : $param['page'];

		return $this->db->utility()->getPaging($builder, $param['page_num'], $param['page']);

	}

}
?>