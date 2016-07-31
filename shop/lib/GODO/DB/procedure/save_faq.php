<?
class save_faq extends GODO_DB_procedure {

	function execute() {

		$param = @func_get_arg(0);

		$builder = $this->db->builder()->insert();
		$builder->into(GD_FAQ);
		$builder->set($param);

		$builder->query();
		$sno = $this->db->lastID();

		// 순서 재정렬
		$i = 0;
		$res = $this->db->query("SELECT sno FROM ".GD_FAQ." WHERE itemcd='" . $param['itemcd'] . "' ORDER BY sort ASC, regdt DESC");

		while ($param=$this->db->fetch($res)){
			$this->db->query("UPDATE ".GD_FAQ." SET sort='" . ( ++$i ) . "' WHERE itemcd='" . $param['itemcd'] . "' AND sno='" . $sno . "'");
		}

		return $sno;

	}

}
?>