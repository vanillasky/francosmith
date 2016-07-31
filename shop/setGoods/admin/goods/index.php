<?PHP
require_once "../../class/framework/Request.class.php";
require_once "../../class/enamu/EnamuDAO.class.php";
require_once "../../class/lib/Loader.class.php";
require_once "../../class/cody/CodyDAO.class.php";
require_once "../../class/image/ImageDAO.class.php";
require_once "../../class/lib/Mata.class.php";


class setAdmin {
	var $load;
	var $dao;
	var $req;
	var $Edao;
	var $Idao;
	var $http_referer;

	 function setAdmin(){
		$this->req = new Request('GET');
		$this->Edao = new EnamuDAO();
		$pg_re = $this->req->getInt('pg');

			
		### ���� �α��� üũ
		if(!$this->Edao->adminAuth()){
			L_mata::mata('../../../admin/');
		}
		
		$this->load = new Loader();
		$this->dao = new CodyDAO();
		$this->Idao = new ImageDAO();
		
	}
	
	 function main(){
		 if($this->req->get('fn') == 'A'){
			$this->setConfig();			
		 }else{
			$this->setList();
		 }
	}

	function setList(){		

		$page_num = $this->req->get('page_num');
		$sort = $this->req->get('sort');

		$pg = $this->req->getInt('pg',1);
		$listNum = $this->req->getInt('LN',10);

		if(!$page_num) {
			$listNum = 10;
		} else {
			$listNum = $page_num;
		}		
		$pagingSize = 10;

		if(!$sort) {
			$orderby = 'idx desc';
		} else {
			$orderby = $sort.' desc, idx desc';
		}

		$where = " idx > 0 ";
		$options=array('page_num'=>$listNum,'sort'=>$orderby,);
		
		// $this->dao->isDebug = true;
		$total = $this->dao->getTotal($where);
		$objs = $this->dao->getList($pg, $listNum, $orderby, $where);

		foreach ( $objs as $obj) {
			$total_goods_price=0;
			$setCost = $obj->get('setCost'); //�ʱ��� �ڵ�Total ����

			### ��ǰ ���¸� ������Ʈ �ѽð��� date�� �����Ѵ�.		
			$datetime = date('YmdHis', strtotime($obj->get(goodsStateRegdate)));
			
			### ���� �ð��� �ѽð������� �����ؼ� ���Ѵ�.
			if($datetime < date('YmdHis',strtotime("-1 hours"))){
				
				### �ڵ� �̹��� �������� ��ǰ ��ȣ�� �����´�.
				$Iobjs = $this->Idao->getList(0, 0, 'tem_index asc', " cody_idx = '".$obj->get(idx)."'"); 	
				foreach ( $Iobjs as $Iobj) {
					// ��ǰ�������� ��ǰ ������ �����´�.
					$goods_price = $this->Edao->goodsOptionPrice($Iobj->get(goods_idx));
					$total_goods_price += $goods_price['price']; //���� �ڵ��ǰ�� ��ü ����
				}

				if($setCost != $total_goods_price) {
					$t_price = $total_goods_price;
				} else {
					$t_price = $setCost;
				}
				
				### ��Ʈ ��ǰ ���� ������Ʈ
				$objset = new Cody();
				$objset->set('setCost', $t_price);
				$objset->set('goodsStateRegdate', 'now()');
				$this->dao->setObject($objset);
				$this->dao->modify(array('setCost','goodsStateRegdate'),"idx = '".$obj->get(idx)."'");

			}
		}

		$objs = $this->dao->getList($pg, $listNum, $orderby, $where);
		###�����ǹ�ȣ
		$pos = $this->dao->postNo($total,$pg,$listNum);

		$paging = $this->dao->getPaging($pg, $pagingSize, $total, $listNum, $options);

		$request_data = array(
								'objs'=>$objs,
								'pos'=>$pos,
								'paging'=>$paging
							 );

		$this->load->View('./html/list.php',$request_data);

	}

	function setConfig(){
		$setGoodsConfig = loadConfig('setGoodsConfig','setGoodsConfig.php');		
		
		$request_data = array('setGoodsConfig'=>$setGoodsConfig,'tplSkin'=>$this->Edao->cfg['tplSkin']);
		
		$this->load->View('./html/config.php',$request_data);
	}

}

$set = new setAdmin();
$set->main();

?>