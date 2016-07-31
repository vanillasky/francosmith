<?PHP
require_once dirname(__FILE__) . "/../../../lib/lib.func.php";
require_once dirname(__FILE__) . "/../../../setGoods/class/framework/BaseDAO.class.php";
require_once dirname(__FILE__) . "/../../../setGoods/class/cody/Cody.class.php";

class CodyDAO extends BaseDAO {
	var $base;
	var $tablename;
	function CodyDAO() {
		parent::BaseDAO();
		$this->base = new BaseObject();
		$this->tablename = GD_SET_CODY;		
		$this->setObject(new Cody());
		$this->initFields();
	}

	//쿼리 디버그를 보여준다.
	function debug($str) {
		if ( $this->isDebug ) {
			echo "Debug/CodyDAO: " . $str ."\n";
		}
	}

	//가상번호만들기
	function postNo($t = 0,$p = 0,$l = 0){
		return $t - ($p-1) * $l;
	}


	function relation_cody($means,$idx){

		if($means == '2'){
			
			$sql = "select * from (select * from ".$this->tablename." where idx != '".$idx."' and state='Y' order by regdate desc limit 10) a order by rand() limit 6";

		}else if($means == '3'){
			
			$sql = "select * from (select * from ".$this->tablename." where idx != '".$idx."' and state='Y' order by like_cnt desc limit 10) a order by rand() limit 6";

		}else{
			
			$sql = "select * from ".$this->tablename." where idx != '".$idx."' and state='Y' order by rand() limit 6";
		}

		return $sql;
		
	}

	function like_sql($idx){
		$sql = "update ".$this->tablename." set like_cnt = like_cnt+1 where idx = '".$idx."'";
		
		return $sql;
	}
}
?>