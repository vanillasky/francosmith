<?
class delete_banner extends GODO_DB_procedure {

	function execute() {

		$sno = @func_get_arg(0);
		$tplSkin = @func_get_arg(1);

		$builder = $this->db->builder()->delete();
		$builder->from(GD_BANNER);

		if ($sno)
			$builder->where('sno = ?', $sno);

		if ($tplSkin)
			$builder->where('tplSkin = ?', $tplSkin);

		return
			  $builder->has('where')
			? $builder->query()
			: false;

	}

}
?>