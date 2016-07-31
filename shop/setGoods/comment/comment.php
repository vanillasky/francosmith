<?
require_once "../class/framework/Request.class.php";
require_once "../class/enamu/EnamuDAO.class.php";
require_once "../class/cody/CodyDAO.class.php";
require_once "../class/comment/CommentDAO.class.php";
require_once "../../Template_/Template_.class.php";

class commentContent {
	var $auth;
	var $req;
	var $tpl;
	var $dao;
	var $cdao;
	var $objs;
	
	function commentContent(){

		$this->req = new Request('GET');
		$this->dao = new CommentDAO();
		$this->Edao = new EnamuDAO();
		$this->Cdao = new CodyDAO();
		$this->setGConfig = loadConfig('setGoodsConfig','setGoodsConfig.php');
		
		$this->tpl = new Template_;
		$this->tpl->template_dir = SKIN_DIR.$this->Edao->cfg[tplSkin]."/setGoods/";
		$this->tpl->compile_dir	= TEMPLATE_DIR.$this->Edao->cfg[tplSkin]."/setGoods/";
		$this->tpl->prefilter  = "adjustPath|include_file|capture_print|sitelinkConvert|systemHeadTag"; 
		
	}

	function main(){
		$this->Content();		
	}

	function Content(){
		$cody_idx = $this->req->getInt('idx');
		
		$objs = $this->dao->getList(0, 0, 'idx desc', " cody_idx = '".$cody_idx."'"); 
		$obj = $this->dao->jarArrayConverter($objs,'L');

		$this->tpl->assign(array('obj'=>$obj,
								 'cody_idx'=>$cody_idx,
								 'sess'=>$this->Edao->getSession('sess'),
								 'member'=>$this->Edao->getSession('member'),
								 'setGConfig'=>$this->setGConfig));

		$this->tpl->define('comment','comment/comment.htm'); 

		$this->tpl->print_('comment'); 

	}

}
$comment = new commentContent();

$comment->main();
?>