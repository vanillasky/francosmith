<?
require_once "../_header.php";

require_once "./class/framework/Request.class.php";
require_once "./class/enamu/EnamuDAO.class.php";
require_once "./class/cody/CodyDAO.class.php";
require_once "./class/image/ImageDAO.class.php";
require_once "./class/comment/CommentDAO.class.php";
require_once "./class/lib/Mata.class.php";

class codyList {
	var $auth;
	var $req;
	var $Edao;
	var $tpl2;
	var $dao;
	var $setGConfig;
	var $cfg;

	function codyList(){
		$this->req = new Request('GET');
		$this->Edao = new EnamuDAO();
		$this->setGConfig = loadConfig('setGoodsConfig','setGoodsConfig.php');		
	}

	function main($tpl){
		
		if($this->setGConfig[state] == 'Y'){
			if($this->req->getInt('pg') < 1){
				$this->MainList($tpl);
			}else{
				$this->appendList($tpl);
			}
		}else{
			L_mata::redirect('http://'.$_SERVER[HTTP_HOST]); //�ڵ��ǰ ������ �� ���, Ȩ����
		}
	}

	function MainList($tpl){
		
		$pg = $this->req->getInt('pg', 1);
		$cody = $this->req->getInt('cody');	### �����ڵ�
		$sh = $this->req->get('sh');
		$sp = $this->req->get('sp');
		$ll = $this->req->get('ll');

		$tpl->define(array(
			'h_include_item'=>'setGoods/common/h_include_item.htm',
			'main'=>'setGoods/index.htm'			
		)); 

		$tpl->assign(array(								
								'pg'=>$pg,
								'cody'=>$cody,
								'sh'=>$sh,
								'header'=>$header,
								'footer'=>$footer,
								'sp'=>$sp,
								'll'=>$ll								
								)
			              );	

		$tpl->print_('main'); 
	}

	function appendList($tpl){
		
		$dao = new CodyDAO();
		$Idao = new ImageDAO();
		$Cdao = new CommentDAO();
		
		$rendum = "";
		$orderKey = "idx";
		$pg = $this->req->getInt('pg',  1);
		$sh = $this->req->get('sh');
		$sp = $this->req->get('sp');
		$cody = $this->req->get('cody');
		$ll = $this->req->get('ll');
		
		if($ll == 'L') $orderbySub = "like_cnt desc";
		if($ll == 'D') $orderbySub = "regdate desc";
		
		
		### ���� ����
		switch ($this->setGConfig[listing]) {
			case 'R' : $rendum = '1';
				break;
			case 'L'   : $orderKey = 'like_cnt';
				break;
			default : $rendum = '';
				break;		
		}

		//������
		$listNum = 20;
		$orderby = $orderKey.' desc';
		if($ll) { $orderby = $orderbySub.", ".$orderKey.' desc '; $rendum = ''; }
		$where = " idx > 0 AND state = 'Y'";
		if($sh) $where .=" and memo like '%".$sh."%'";
		
		### �����ڵ� 
		if($cody > 0){
			$Iobjs = $Idao->getList(0, 0, 'idx', "goods_idx = '".$cody."'");
			
			foreach ( $Iobjs as $Iobj) {
				$cody_idx = $Iobj->get('cody_idx');
				$wtext = str_replace($cody_idx.",","",$wtext);
				$wtext .= $cody_idx.",";				
			}
			$wtext = substr($wtext,0,-1);
			if(strlen($wtext) > 0){
				$where .=" and idx in(".$wtext.")";
			}else{
				$where .=" and idx =''";
			}
		}
		
		$objs = $dao->getList($pg, $listNum, $orderby, $where); 
		$obj = $dao->jarArrayConverter($objs,'L',$rendum);
		

		
		### unset�� �ϸ� ī��Ʈī �پ��� �׷��� ������ ���ؼ�
		$count = count($obj);

		### ��ǰ�� ǰ���� ������ ǥ�þ��� ������ ����

			
		### ��ȸ�� ��ǰ ����Ʈ	
		for ($i=0;$i<$count;$i++) {

			$obj[$i][thumnail_image] = "../setGoods/data/Tnail/300/300_" . $obj[$i][thumnail_name];
			$obj[$i][url] = "content.php?idx=" . $obj[$i][idx];

			// ���� ��ǰ üũ
			$Aobjs = $Idao->getList(0, 0, 'tem_index asc', " cody_idx = '".$obj[$i][idx]."'"); 	
			foreach ( $Aobjs as $Aobj) {
				$use_only_adult = $this->Edao->goodsUseAdult($Aobj->get(goods_idx));
				if($use_only_adult && ! $this->Edao->canAccessAdult()){
					$obj[$i][thumnail_image] = $this->Edao->cfg[rootDir] . "/data/skin/" . $this->Edao->cfg[tplSkin] . '/img/common/19set.gif';
					$obj[$i][url] = '../main/intro_adult.php?returnUrl=' . urlencode("../../setGoods/".$obj[$i][url]);
					break;
				}
			}

			$total_goods_price=0;
			$setCost = $obj[$i]['setCost']; //�ʱ��� �ڵ�Total ����
			
			### ��ǰ ���¸� ������Ʈ �ѽð��� date�� �����Ѵ�.		
			$datetime = date('YmdHis', strtotime($obj[$i][goodsStateRegdate]));
			
			### ���� �ð��� �ѽð������� �����ؼ� ���Ѵ�.
			if($datetime < date('YmdHis',strtotime("-1 hours"))){
				
				### �ڵ� �̹��� �������� ��ǰ ��ȣ�� �����´�.
				$Iobjs = $Idao->getList(0, 0, 'tem_index asc', " cody_idx = '".$obj[$i][idx]."'"); 	
				$set_runout = "Y";
				$set_open = 'Y';
				foreach ( $Iobjs as $Iobj) {

					### ��ǰ�������� ���������� �����´�.
					$runout = $this->Edao->Runout("",$Iobj->get(goods_idx));	
					
					if($this->setGConfig[goods_display] == 'N'){
						if($runout[runout] == "N") $set_runout = $runout[runout];
					}

					if($runout[open] == "N") $set_open = $runout[open];

					// ��ǰ�������� ��ǰ ������ �����´�.
					$goods_price = $this->Edao->goodsOptionPrice($Iobj->get(goods_idx));
					$total_goods_price += $goods_price['price']; //�ڵ��ǰ�� ��ü ����
				}

				if($setCost != $total_goods_price) {
					$t_price = $total_goods_price;
				} else {
					$t_price = $setCost;
				}
				
				
				### ��Ʈ ��ǰ ���� ������Ʈ
				$objset = new Cody();
				$objset->set('setCost', $t_price);
				$objset->set('state', $set_open);
				$objset->set('goodsStateRegdate', 'now()');

				### ������ ǰ����ǰ ����쿡�� �Ѵ�.
				if($this->setGConfig[goods_display] == 'N') $objset->set('goodsState', $set_runout);

				$dao->setObject($objset);
				if($this->setGConfig[goods_display] == 'N'){
					$dao->modify(array('setCost','goodsState','state','goodsStateRegdate'),"idx = '".$obj[$i][idx]."'");
					if($set_runout == 'N') unset($obj[$i]);
				}else{
					$dao->modify(array('setCost','state','goodsStateRegdate'),"idx = '".$obj[$i][idx]."'");
				}
				
				if($set_open == 'N') unset($obj[$i]);
				

			}else{
				if($this->setGConfig[goods_display] == 'N'){
					### ������Ʈ ���� �ѽð��� �������ٸ� 					
					if($obj[$i][goodsState] == 'N') unset($obj[$i]);
				}
			}

		}


		
		
		### ���� ��ȸ
		for ($i=0;$i<$count;$i++) {
			if($obj[$i][idx] > 0){
				$Cobjs = $Cdao->getList(1, 5, "idx desc", "cody_idx = ".$obj[$i][idx]);			
				$k=0;
				foreach ( $Cobjs as $Cobj ) {
					$obj[$i][comment_nickname][$k] = $Cobj->get('nickname');
					$obj[$i][comment_memo][$k] = $Cobj->get('memo');

					$k++;
				}		
			}
		}
			
		
		$tpl->define('listing','setGoods/main/main_list_item.htm'); 

		$tpl->assign(array(
								'obj'=>$obj,
								'pg'=>$pg,								
								'sh'=>$sh,
								'count'=>$count,
								'img_dir'=>IMG_DIR.$this->Edao->cfg[tplSkin]."/setGoods/"								
						  )
			         );	
		
		$tpl->print_('listing'); 
	}

}

$ce = new codyList();

$ce->main($tpl);
?>

