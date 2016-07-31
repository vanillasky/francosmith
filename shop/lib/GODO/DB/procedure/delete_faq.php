<?
class delete_faq extends GODO_DB_procedure {

	function execute() {

		$sno = @func_get_arg(0);

		$this->db->delete(GD_FAQ)->where('sno = ?', $sno)->query();

	}

}
?>