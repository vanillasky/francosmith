<?
class get_banner extends GODO_DB_procedure {

	function execute() {

		$tplSkin = @func_get_arg(0);
		$loccd = @func_get_arg(1);

		$builder = $this->db->builder()->select();

		$builder->from(GD_BANNER);

		if ($tplSkin)
			$builder->where('tplSkin = ?', $tplSkin);

		if ($loccd)
			$builder->where('loccd = ?', $loccd);

		return $this->db->utility()->getAll($builder);

	}

}
?>