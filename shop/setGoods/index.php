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
			L_mata::redirect('http://'.$_SERVER[HTTP_HOST]); //코디상품 사용안함 일 경우, 홈으로
		}
	}

	function MainList($tpl){
		
		$pg = $this->req->getInt('pg', 1);
		$cody = $this->req->getInt('cody');	### 관련코디
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
		
		
		### 진열 설정
		switch ($this->setGConfig[listing]) {
			case 'R' : $rendum = '1';
				break;
			case 'L'   : $orderKey = 'like_cnt';
				break;
			default : $rendum = '';
				break;		
		}

		//설정값
		$listNum = 20;
		$orderby = $orderKey.' desc';
		if($ll) { $orderby = $orderbySub.", ".$orderKey.' desc '; $rendum = ''; }
		$where = " idx > 0 AND state = 'Y'";
		if($sh) $where .=" and memo like '%".$sh."%'";
		
		### 관련코디 
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
		

		
		### unset을 하면 카운트카 줄어든다 그래서 고정을 위해서
		$count = count($obj);

		### 상품중 품절이 있으면 표시않음 설정시 적용

			
		### 조회된 상품 리스트	
		for ($i=0;$i<$count;$i++) {

			$obj[$i][thumnail_image] = "../setGoods/data/Tnail/300/300_" . $obj[$i][thumnail_name];
			$obj[$i][url] = "content.php?idx=" . $obj[$i][idx];

			// 성인 상품 체크
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
			$setCost = $obj[$i]['setCost']; //초기의 코디Total 가격
			
			### 상품 상태를 업데이트 한시간을 date로 변경한다.		
			$datetime = date('YmdHis', strtotime($obj[$i][goodsStateRegdate]));
			
			### 현재 시간을 한시간전으로 세팅해서 비교한다.
			if($datetime < date('YmdHis',strtotime("-1 hours"))){
				
				### 코디 이미지 정보에서 상품 번호를 가져온다.
				$Iobjs = $Idao->getList(0, 0, 'tem_index asc', " cody_idx = '".$obj[$i][idx]."'"); 	
				$set_runout = "Y";
				$set_open = 'Y';
				foreach ( $Iobjs as $Iobj) {

					### 상품정보에서 상태정보를 가져온다.
					$runout = $this->Edao->Runout("",$Iobj->get(goods_idx));	
					
					if($this->setGConfig[goods_display] == 'N'){
						if($runout[runout] == "N") $set_runout = $runout[runout];
					}

					if($runout[open] == "N") $set_open = $runout[open];

					// 상품정보에서 상품 가격을 가져온다.
					$goods_price = $this->Edao->goodsOptionPrice($Iobj->get(goods_idx));
					$total_goods_price += $goods_price['price']; //코디상품의 전체 가격
				}

				if($setCost != $total_goods_price) {
					$t_price = $total_goods_price;
				} else {
					$t_price = $setCost;
				}
				
				
				### 세트 상품 상태 업데이트
				$objset = new Cody();
				$objset->set('setCost', $t_price);
				$objset->set('state', $set_open);
				$objset->set('goodsStateRegdate', 'now()');

				### 설정이 품절상품 뺄경우에만 한다.
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
					### 업데이트 한지 한시간이 안지났다면 					
					if($obj[$i][goodsState] == 'N') unset($obj[$i]);
				}
			}

		}


		
		
		### 뎃글 조회
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

