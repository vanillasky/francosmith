<?PHP
require_once "../../class/framework/Request.class.php";
require_once "../../class/enamu/EnamuDAO.class.php";
require_once "../../class/cody/CodyDAO.class.php";
require_once "../../class/lib/Loader.class.php";

class codyModify{
	var $req;
	var $tpl;
	var $dao;
	var $Edao;
	
	function codyModify(){
		$this->req = new Request('GET');
		$this->Edao = new EnamuDAO();
		$this->dao = new CodyDAO();
		$this->load = new Loader();		
		
	}

	function index(){
		if($this->req->get('fn') == 'M'){
			$this->modify();
		}
	}

	function modify(){
		$idx = $this->req->get('idx');
		$obj = $this->dao->find("idx = '".$idx."'");

		$stateY = "";
		$stateN = "";
		if($obj->get('state') == 'Y') $stateY = 'checked';
		else $stateN = 'checked';



		$request_data = array('codyhtml'=>$obj->get('CD_content'),
									'memo'=>$obj->get('memo'),
									'cody_name'=>$obj->get('cody_name'),
									'stateY'=>$stateY,
									'stateN'=>$stateN,
									'idx'=>$obj->get('idx'),
									'thumnail_name'=>$obj->get('thumnail_name')
							 );

		$this->load->View('./html/codyinfoModify_form.php',$request_data);

	}
}


 $mf = new codyModify();

 $mf->index();
 ?>