<?PHP
require_once dirname(__FILE__) . "/../../../lib/library.php";
require_once dirname(__FILE__) . "/../../../setGoods/class/framework/BaseDAO.class.php";
require_once dirname(__FILE__) . "/../../../setGoods/class/image/Image.class.php";

class ImageDAO extends BaseDAO {
	var $base;
	function ImageDAO() {
		parent::BaseDAO();
		$this->base = new BaseObject();
		$this->tablename = GD_SET_IMAGE;		
		$this->setObject(new image());
		$this->initFields();
	}

	//쿼리 디버그를 보여준다.
	function debug($str) {
		if ( $this->isDebug ) {
			echo "Debug/ImageDAO: " . $str ."\n";
		}
	}

}
?>