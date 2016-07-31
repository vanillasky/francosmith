<?
class update_code extends GODO_DB_procedure {

	protected function execute() {

		$sort = @func_get_arg(0);
		$sno = @func_get_arg(1);
		$groupcd = @func_get_arg(2);

		if ($sort) {

			$builder = $this->db->builder()->update();
			$builder->set(array('sort'=>$sort));
			$builder->from(GD_CODE);
			$builder->where('sno = ?', $sno);
			$builder->where('groupcd = ?', $groupcd);
			$builder->query();

		}

		{ // 순서 재정렬

			$i = 0;
			$res = $this->db->query("SELECT sno FROM ".GD_CODE." WHERE groupcd='" . $groupcd  . "' ORDER BY sort ASC, itemcd ASC");

			while ($param=$this->db->fetch($res)){
				$this->db->query("UPDATE ".GD_CODE." SET sort='" . ( ++$i ) . "' WHERE groupcd='" . $groupcd  . "' AND sno='" . $param['sno'] . "'");
			}
		}

	}
}

?>