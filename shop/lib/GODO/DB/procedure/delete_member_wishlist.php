<?
class delete_member_wishlist extends GODO_DB_procedure {

	function execute() {

		$sno = @func_get_arg(0);
		$m_no = @func_get_arg(1);

		$builder = $this->db->builder()->delete();
		$builder->from(GD_MEMBER_WISHLIST);

		if ($sno)
			$builder->where('sno = ?', $sno);

		if ($m_no)
			$builder->where('m_no = ?', $m_no);

		return
			  $builder->has('where')
			? $builder->query()
			: false;

	}

}
?>