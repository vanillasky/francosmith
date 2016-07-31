<?
class update_webfont extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);
		$font_no = @func_get_arg(1);

		$builder = $this->db->builder()->update();
		$builder->from(GD_WEBFONT);
		$builder->set($param);
		$builder->where('font_no = ?', $font_no);

		return $builder->query();

	}
}

?>