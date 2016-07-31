<?PHP
require_once dirname(__FILE__) . "/../../../lib/lib.func.php";
require_once dirname(__FILE__) . "/../../../setGoods/class/framework/BaseDAO.class.php";
require_once dirname(__FILE__) . "/../../../setGoods/class/comment/Comment.class.php";

class CommentDAO extends BaseDAO {
	var $base;
	var $tablename;
	function CommentDAO() {
		parent::BaseDAO();
		$this->base = new BaseObject();
		$this->tablename = GD_SET_COMMENT;		
		$this->setObject(new Comment());
		$this->initFields();
	}

	//쿼리 디버그를 보여준다.
	function debug($str) {
		if ( $this->isDebug ) {
			echo "Debug/CommentDAO: " . $str ."\n";
		}
	}
}
?>