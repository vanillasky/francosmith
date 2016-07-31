<?PHP
require_once "../../class/framework/Request.class.php";
require_once "../../class/enamu/EnamuDAO.class.php";
require_once "../../class/cody/CodyDAO.class.php";
require_once "../../class/image/ImageDAO.class.php";
require_once "../../class/lib/file.class.php";
require_once "../../class/lib/Mata.class.php";


class setAdminIndb {
	var $req;
	var $dao;
	var $Edao;
	var $Idao;
	var $reqFile;

	 function setAdminIndb(){
		$this->req = new Request('POST');
		$this->reqFile = new Request('FILES');
		$this->dao = new CodyDAO();
		$this->Edao = new EnamuDAO();
		$this->Idao = new ImageDAO();

		### 어드민 로그인 체크
		if(!$this->Edao->adminAuth()){
			L_mata::mata('../../../admin/');
		}
	}
	
	 function main(){
		 if($this->req->get('fn') == 'C'){
			$this->setState();
		 }elseif($this->req->get('fn') == 'F'){
			$this->setConfig();
		 }elseif($this->req->get('fn') == 'D'){
			$this->delCody();
		 }
	}

	function setState(){
		$statetext = "";
		$idx = $this->req->getInt('gidx');

		$this->dao = new CodyDAO();
		$total = $this->dao->getTotal(" idx > 0 ");
	
		if($total > 0 ) { //코디상품이 0개 이상이면				

			$obj = $this->dao->find("idx = '".$idx."'");

			$arr_stateY = $this->req->get('state_Y');
			$arr_stateALL = $this->req->get('state_ALL');
			if ($arr_stateY) {
				$arr_stateN = array_diff($arr_stateALL,$arr_stateY);
			} else {
				$arr_stateN = $arr_stateALL;
			}

			if(is_array($arr_stateY)) {

				foreach ($arr_stateY as $k => $v) {	
					$obj = new Cody();
					$obj->set('state', "Y");
					$this->dao->setObject($obj);			
					$this->dao->modify(array('state'),"idx = '".$v."'");
				}
			}
			
			foreach ($arr_stateN as $k => $v) {	
				$obj = new Cody();
				$obj->set('state', "N");
				$this->dao->setObject($obj);			
				$this->dao->modify(array('state'),"idx = '".$v."'");
			}
		}

		L_mata::mata('./?fn=B', '진열 상태가 변경되었습니다.');
	}

	function setConfig(){
		$data = "";
		$state = $this->req->get('state');
		$listing = $this->req->get('listing');
		$goods_display = $this->req->get('goods_display');
		$means = $this->req->get('means');
		$memo = $this->req->get('memo');
		$memo_permission = $this->req->get('memo_permission');
		$setconnection = $this->req->get('setconnection');
		
		
		
		if($this->reqFile->getFile('setGoodsBanner')){
			$dir = "../../data/banner";
			$setGoodsBanner_name = $this->reqFile->getFile('setGoodsBanner');
			
			L_File::copyfileup($this->reqFile->getFile('setGoodsBanner','1'), $setGoodsBanner_name, $dir);
			
		}else{

			$setGoodsBanner_name = $this->req->get('setGoodsBanner_old');
		}

		if(!$listing){
			@include "../../data/config/setGoodsConfig.php";
			$listing = $setGoodsConfig['listing'];
			$goods_display = $setGoodsConfig['goods_display'];
			$means = $setGoodsConfig['means'];
			$memo = $setGoodsConfig['memo'];
			$memo_permission = $setGoodsConfig['memo_permission'];
			$setconnection = $setGoodsConfig['setconnection'];
			$setconnection = $setGoodsConfig['setconnection'];		
		}

		
		$data .="<?	\n";
		$data .="$"."setGoodsConfig = array(	\n";
		$data .="'state'=>'".$state."',	\n";
		$data .="'listing'=>'".$listing."',	\n";
		$data .="'goods_display'=>'".$goods_display."',	\n";
		$data .="'means'=>'".$means."',	\n";
		$data .="'memo'=>'".$memo."',	\n";
		$data .="'memo_permission'=>'".$memo_permission."',	\n";
		$data .="'setGoodsBanner'=>'".$setGoodsBanner_name."',	\n";
		$data .="'setconnection'=>'".$setconnection."'	\n";
		$data .=");	\n";
		$data .="?>";
		L_File::filewrite('../../data/config/setGoodsConfig.php', $data);
		L_mata::mata('./?fn=A', '저장되었습니다.');
	}

	function delCody(){
		$gidx = $this->req->getInt('gidx');

		$file_path = "../../data/";
		
		$obj = $this->dao->find("idx = '".$gidx."'");
		if($obj->get('thumnail_name')){
			//원본이미지
			if(is_file($file_path.'/org/'.$obj->get('thumnail_name'))) {
				unlink($file_path.'/org/'.$obj->get('thumnail_name'));
				unlink($file_path.'/Tnail/100/100_'.$obj->get('thumnail_name'));
				unlink($file_path.'/Tnail/200/200_'.$obj->get('thumnail_name'));
				unlink($file_path.'/Tnail/300/300_'.$obj->get('thumnail_name'));
			}

			//원본이미지
			if(is_file($file_path.'/piece/'.$gidx.'_1_droppable1-images_'.$obj->get('thumnail_name'))) {
				exec("rm -rf ".$file_path.'/piece/*'.$obj->get('thumnail_name'));
			}			
		}

		$cody = $this->dao->delete("idx = '".$gidx."'");
		$image = $this->Idao->delete("cody_idx = '".$gidx."'");


		return $cody;
	}

}

$set = new setAdminIndb();
$set->main();

?>