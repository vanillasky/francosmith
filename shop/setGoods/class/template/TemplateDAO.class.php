<?php
/**
 * Created on 2012-09-03
 *
 * Filename	: TemplateDAO.class.php
 * Comment 	: CLASS
 * Function	: 
 * History	: sf2000 by v1.0 최소작성
 * 
 **/
?>
<?PHP
require_once dirname(__FILE__) . "/../../../lib/library.php";
require_once dirname(__FILE__) . "/../../../setGoods/class/framework/BaseDAO.class.php";
require_once dirname(__FILE__) . "/../../../setGoods/class/template/Template.class.php";

class TemplateDAO extends BaseDAO {
	var $base;
	function TemplateDAO() {
		parent::BaseDAO();
		$this->base = new BaseObject();
		$this->tablename = GD_SET_TEMPLATE;		
		$this->setObject(new Template());
		$this->initFields();
	}

	//쿼리 디버그를 보여준다.
	function debug($str) {
		if ( $this->isDebug ) {
			echo "Debug/TemplateDAO: " . $str ."\n";
		}
	}

}
?>