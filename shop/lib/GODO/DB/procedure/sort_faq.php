<?
class sort_faq extends GODO_DB_procedure {

	function execute() {

		$sort = @func_get_arg(0);
		$sno = @func_get_arg(1);
		$itemcd = @func_get_arg(2);

		if ($sort) {

			$builder = $this->db->builder()->update();
			$builder->from(GD_FAQ)->set(array(
				'sort' => $sort
			));
			$builder->where('itemcd = ?', $itemcd);
			$builder->where('sno = ?', $sno);
			$builder->query();

		}

		{ // 순서 재정렬

			$i = 0;
			$res = $this->db->query("SELECT sno FROM ".GD_FAQ." WHERE itemcd='" . $itemcd  . "' ORDER BY sort ASC, regdt DESC");

			while ($param=$this->db->fetch($res)){
				$this->db->query("UPDATE ".GD_FAQ." SET sort='" . ( ++$i ) . "' WHERE itemcd='" . $itemcd  . "' AND sno='" . $param['sno'] . "'");
			}
		}

	}

}
?>