<?
class admin_event_paper_print extends GODO_DB_procedure {

	function execute() {

		$sno = @func_get_arg(0);

		$builder = $this->db->builder();
		$builder->select();

		$builder->from(GD_OFFLINE_PAPER);

		$builder->where('coupon_sno = ?', $sno);

		$builder->order('sno');

		return $this->db->utility()->getAll($builder);

	}

}
?>