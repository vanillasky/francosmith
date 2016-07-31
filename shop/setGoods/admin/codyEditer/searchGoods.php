<?
require_once "../../class/_common.php";
require_once "../../class/lib/Basic.lib.php";
require_once "../../class/framework/Request.class.php";
require_once "../../class/enamu/EnamuDAO.class.php";
require_once "../../class/lib/Loader.class.php";

class searchGoods {
	var $req;
	var $tpl;
	var $dao;

	function searchGoods(){
		$this->req = new Request('GET');
		$this->dao = new EnamuDAO;		
		$this->load = new Loader();
	}

	function index(){
		
		$this->Glist();
		
	}

	function Glist(){
		$sval = $this->req->get('svals');
		$sp = $this->req->get('sp');
		$st = iconv('UTF-8','EUC-kr',$this->req->get('st'));
		$pg = $this->req->getInt('pg',1);
		
		$listNum = 41;
		$pagingSize = 10;
		$orderby = 'idx desc';

		$options=array('sval'=>$sval,'sp'=>$sp,'st'=>$st);
		
		$objs = $this->dao->EditGoodsList($sval,$sp,$st,$pg,$listNum);		
		$total = $this->dao->EditGoodsListTotal($sval,$sp,$st);

		$paging = getScriptPaging($pg, $pagingSize, $total, $listNum);
		
		$request_data = array('objs'=>$objs,'paging'=>$paging,'total'=>$total,'dao'=>$this->dao);

		$this->load->View('./html/searchGoods_form.php',$request_data);		
	}	
	
}

$gl = new searchGoods();

$gl->index();
?>