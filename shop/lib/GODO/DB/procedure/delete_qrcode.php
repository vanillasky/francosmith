<?
class delete_qrcode extends GODO_DB_procedure {

	function execute() {

		$qr_type = @func_get_arg(0);
		$contsNo = @func_get_arg(1);

		$builder = $this->db->builder()->delete();
		$builder->from(GD_QRCODE);

		if ($qr_type)
			$builder->where('qr_type = ?', $qr_type);

		if ($contsNo)
			$builder->where('contsNo = ?', $contsNo);

		return
			  $builder->has('where')
			? $builder->query()
			: false;
	}

}
?>
