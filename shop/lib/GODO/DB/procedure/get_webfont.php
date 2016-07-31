<?
class get_webfont extends GODO_DB_procedure {

	function execute() {

		$font_no = @func_get_arg(0);
		$font_code = @func_get_arg(1);

		$builder = $this->db->builder()->select();

		$builder->from(GD_WEBFONT);

		if ($font_no)
			$builder->where('font_no = ?', $font_no);

		if ($font_code)
			$builder->where('font_code = ?', $font_code);

		return $this->db->utility()->getAll($builder);

	}

}
?>