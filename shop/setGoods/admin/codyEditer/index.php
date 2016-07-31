<?
require_once "../../class/_common.php";
require_once "../../class/enamu/EnamuDAO.class.php";
require_once "../../class/framework/Request.class.php";
require_once "../../class/template/TemplateDAO.class.php";
require_once "../../class/lib/Loader.class.php";

class codyEditer {
	var $req;
	var $tpl;
	var $TPdao;
	var $load;

	function codyEditer(){
		$this->req = new Request('GET');
		$this->Edao = new EnamuDAO();

		### 어드민 로그인 체크
		if(!$this->Edao->adminAuth()){
			L_mata::mata('../../../admin/');
		}

		$this->TPdao = new TemplateDAO;
		$this->cfg = loadConfig('cfg','config.php','../../../conf/');	
		$this->load = new Loader();
	}

	function index(){
		if($this->req->get('fn') == "E"){
			$this->Editer();
		}else if($this->req->get('fn') == "T"){
			$this->Template();
		}else{
			$this->Editer();
		}
	}

	function Editer(){
		
		$TPobjs = $this->TPdao->getList('0', '0', 'TP_idx', "TP_group = '2'"); 
				
		$request_data = array('TPobjs'=>$TPobjs);

		$this->load->View('./html/editer_form.php',$request_data);
		
	}

	function Template(){
		$Tval = $this->req->get('val');

		$objs = $this->TPdao->getList('0', '0', 'TP_idx', "TP_group = '".$Tval."'"); 
				
		$request_data = array('objs'=>$objs);

		$this->load->View('./html/Template_form.php',$request_data);		
	}
}

$ce = new codyEditer();

$ce->index();
?>