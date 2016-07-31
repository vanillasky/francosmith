<?
require_once "../_header.php";

require_once "./class/framework/Request.class.php";
require_once "./class/enamu/EnamuDAO.class.php";
require_once "./class/cody/CodyDAO.class.php";
require_once "./class/image/ImageDAO.class.php";
require_once "./class/lib/Mata.class.php";
require_once "../lib/sns.class.php";

class codyContent {
	var $auth;
	var $req;
	var $tpl2;
	var $dao;
	var $Idao;
	var $snsCfg2;
	
	function codyContent(){
		$this->req = new Request('GET');
		$this->Edao = new EnamuDAO();
		$this->dao = new CodyDAO();
		$this->Idao = new ImageDAO();
		$this->setGConfig = loadConfig('setGoodsConfig','setGoodsConfig.php');	
	}

	function main($tpl){
		if($this->req->get('fn') == 'recody'){
			$this->relationCody($tpl);
		}else if($this->req->get('fn') == 'like'){
			$this->like_cnt();
		}else{
			$this->Content($tpl);
		}
	}

	function Content($tpl){
		$idx = $this->req->getInt('idx');
	
		//코디정보
		$objs = $this->dao->find("idx = '".$idx."'");
		$obj = $this->dao->jarArrayConverter($objs);

		$Iobjs = $this->Idao->getList(0, 0, 'tem_index asc', " cody_idx = '".$idx."'"); 
		$Iobj = $this->Idao->jarArrayConverter($Iobjs,'L');			
		
		### 품절상품이 포함되어있다면 보여주지 않는다.
		$state = 'Y';		
		### 코디상품의 상태값 Y
		if($objs->get(state) == 'Y'){
			### 코디 진열 상태값이 N이면 한다.
			if($this->setGConfig[goods_display] == 'N'){
				### 코디 등록상품의 상태를 확인한다.					
				foreach ( $Iobjs as $Iobj2) {
					### 상품정보에서 상태정보를 가져온다.
					$runout = $this->Edao->Runout("",$Iobj2->get(goods_idx));			
					if($runout[runout] == "N") $state = $runout[runout];
				}
			}
		
		} else {
			### 코디자체가 표시안함이면 상태를 등록한다.
			$state = $objs->get(state);
		}			



		$price =  $this->Edao->realgoodsOptionPrice($Iobjs);	
		
			
		#### 코디 설정
		
			
		//$cfg = loadConfig('cfg','config.php','/shop/conf/');

		$sns = new SNS();
		
		$goodsurl = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?idx='.$idx;
		$args = array('shopnm'=>$this->Edao->cfg['shopName'], 'goodsnm'=>$objs->get('cody_name'), 'goodsurl'=>$goodsurl, 'img'=>"http://".$_SERVER['HTTP_HOST']."/shop/setGoods/data/Tnail/300/300_".$objs->get('thumnail_name')); 
		$snsRes = $sns->get_post_btn($args);
		$hIncludeCustomHeader .= $snsRes['meta']; // 페이스북에 사용될 meta tag
		$tpl->assign('hIncludeCustomHeader', $hIncludeCustomHeader); 
		$snsBtn = $snsRes['btn'];
		
	
		### SNS
		$snsBtn = str_replace("../data/skin/season3/img/sns/icon_twitter.png","../data/skin/".$this->Edao->cfg['tplSkin']."/setGoods/img/front/icon_twitter.gif",$snsBtn);	
		$snsBtn = str_replace("../data/skin/season3/img/sns/icon_facebook.png","../data/skin/".$this->Edao->cfg['tplSkin']."/setGoods/img/front/icon_facebook.gif",$snsBtn);
		$snsBtn = str_replace("../data/skin/season3/img/sns/icon_me2day.png","../data/skin/".$this->Edao->cfg['tplSkin']."/setGoods/img/front/icon_me2day.gif",$snsBtn);
		$snsBtn = str_replace("../data/skin/season3/img/sns/btn_c.png","../data/skin/".$this->Edao->cfg['tplSkin']."/setGoods/img/front/icon_cyworld.gif",$snsBtn);

		
		$tpl->define(array(
			'h_include_item'=>'setGoods/common/h_include_item.htm',
			'content'=>'setGoods/content.htm'
		)); 
		
		
		$tpl->assign(array(
								'idx'=>$idx,
								'obj'=>$obj,
								'Iobj'=>$Iobj,
								'price'=>$price,
								'setGConfig'=>$this->setGConfig,
								'snsBtn'=>$snsBtn,
								'state'=>$state
								)
			              );

		$tpl->print_('content'); 

	}

	function relationCody($tpl){
		$idx = $this->req->get('idx');
		$sql = $this->dao->relation_cody($this->setGConfig[means],$idx);
		$objs = $this->dao->getCustemList($sql);
		$obj = $this->dao->jarArrayConverter($objs,'L');

		$tpl->define('relationCody','setGoods/main/relationCody.htm'); 

		$tpl->assign('obj',$obj);

		$tpl->print_('relationCody'); 

	}

	function like_cnt(){
		$idx = $this->req->getInt('idx');
		
		$sql = $this->dao->like_sql($idx);
		$this->dao->getCustemList($sql);

		return $idx;
	}

}
$ce = new codyContent();

$ce->main($tpl);
?>