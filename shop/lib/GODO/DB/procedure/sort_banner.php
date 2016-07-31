<?
class sort_banner extends GODO_DB_procedure {

	function execute() {

		$sort = @func_get_arg(0);
		$sno = @func_get_arg(1);
		$loccd = @func_get_arg(2);
		$tplSkin = @func_get_arg(3);

		if ($sort) {

			$builder = $this->db->builder()->update();
			$builder->from(GD_BANNER)->set(array(
				'sort' => $sort
			));
			$builder->where('sno = ?', $sno);
			$builder->where('loccd = ?', $loccd);
			$builder->query();

		}

		{ // 순서 재정렬

			$i = 0;
			$res = $this->db->query("SELECT sno FROM ".GD_BANNER." WHERE tplSkin = '".$tplSkin."' and loccd='" . $loccd  . "' ORDER BY sort ASC, regdt DESC");

			while ($param=$this->db->fetch($res)){
				$this->db->query("UPDATE ".GD_BANNER." SET sort='" . ( ++$i ) . "' WHERE loccd='" . $loccd  . "' AND sno='" . $param['sno'] . "'");
			}
		}

	}

}
?>