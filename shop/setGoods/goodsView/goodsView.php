<?
require_once "../class/framework/Request.class.php";
require_once "../class/enamu/EnamuDAO.class.php";
require_once "../../Template_/Template_.class.php";
require_once "../../lib/cart.class.php";
@include dirname(__FILE__) . "/../../lib/acecounter.class.php";

class goodsView {

	var $req;
	var $tpl;
	var $dao;
	var $imgpath;


	function goodsView(){
		$this->req = new Request('GET');
		$this->dao = new EnamuDAO();
		$this->tpl = new Template_;

		$this->tpl->template_dir = SKIN_DIR.$this->dao->cfg[tplSkin]."/setGoods/";
		$this->tpl->compile_dir	= TEMPLATE_DIR.$this->dao->cfg[tplSkin]."/setGoods/";
		$this->tpl->prefilter		= "adjustPath|include_file|capture_print|sitelinkConvert|systemHeadTag";
	}

	function main(){
		$this->view();
	}

	function view(){

		$gidxArray = $this->req->getInt('gidx');
		$gidx = explode(",",$gidxArray);
		$sess = $this->dao->getSession('sess');

		for($i=0;$i<count($gidx);$i++){

		    $goodsInfo = $this->dao->goodsFetch($gidx[$i]);

			### 상품 진열 여부 체크

			list( $goodsInfo[brand] ) = $this->dao->goodsBrand($goodsInfo['brandno']);
			$goodsInfo = $this->dao->goodsOption($gidx[$i],$goodsInfo,$sess[m_no]);

			### 추가스펙 세팅
			$goodsInfo[ex_title] = explode("|",$goodsInfo[ex_title]);
			foreach ($goodsInfo[ex_title] as $k=>$v) $goodsInfo[ex][$v] = $goodsInfo["ex".($k+1)];
			$goodsInfo[ex] = array_notnull($goodsInfo[ex]);
			$goodsInfo[r_img] = explode("|",$goodsInfo['img_m']);
			$goodsInfo[soldout] = $this->dao->getConfig('soldout');

			//인증레벨
			$cauth_step = explode(':', $goodsInfo['auth_step']);
			$goodsInfo['auth_step'] = array();
			$goodsInfo['auth_step'][0] = (in_array('1', $cauth_step) ? 'Y' : 'N' ) ;
			$goodsInfo['auth_step'][1] = (in_array('2', $cauth_step) ? 'Y' : 'N' ) ;
			$goodsInfo['auth_step'][2] = (in_array('3', $cauth_step) ? 'Y' : 'N' ) ;


			$optnm = explode("|",$goodsInfo[optnm]);
			$goodsInfo[optnm]	= str_replace("|","/",$goodsInfo[optnm]);

			$opt = $this->dao->opt;
			if ($opt[$this->dao->optkey][0][opt1] == null && $opt[$this->dao->optkey][0][opt2] == null) unset($opt);


			### 고객선호도
			$point = $this->dao->point_chk($gidx[$i]);
			$goodsInfo[chk_point] = $point[chk_point];
			$goodsInfo[point] = $point[point];

			### 아이콘
			$tplSkin_path = "/shop/data/";
			$goodsInfo[icon] = setIcon($goodsInfo[icon],$goodsInfo[regdt],$tplSkin_path);

			### 네이버 마일리지
			$goodsInfo = $this->dao->naverNcash($goodsInfo,$gidx[$i]);
			if($goodsInfo['naverNcash']=='Y') $goodsInfomap['naverNcash'] = 'Y';

			### 추가옵션
			$option_val = $this->dao->addoptnm($goodsInfo[addoptnm],$gidx[$i]);

			$goodsInfomap['goodsInfo'][$i] = $goodsInfo;
			$goodsInfomap['opt'][$i] = $opt;
			$goodsInfomap['optnm'][$i] = $optnm;
			$goodsInfomap['option_val'][$i] = $option_val;

			### ace 카운터
			if ($i == 0) {
				$Acecounter = new Acecounter();
				$Acecounter->get_common_script();
				$Acecounter->goods_view($goodsInfo['goodsno'],$goodsInfo['goodsnm'],$goodsInfo['price']);
				if($Acecounter->scripts){
					$acecounterHeader .= $Acecounter->scripts;
					$this->tpl->assign('acecounterHeader',$acecounterHeader);
				}
			}
		}

		// 네이버공통 유입스크립트
		@include dirname(__FILE__).'/../../lib/naverCommonInflowScript.class.php';
		$naverCommonInflowScript = new NaverCommonInflowScript();
		$goodsInfomap['naverCommonInflowScript'] = $naverCommonInflowScript->getCommonInflowScript();

		$this->tpl->define('view','goodsView/goodsView.htm'); 

		$this->tpl->assign($goodsInfomap);
		$this->tpl->assign('cartCfg',$cart = new Cart);
		$this->tpl->assign(array(
								'clevel'=> $goodsInfo['level'],
								'slevel'=> $sess['level']
		));


		$this->tpl->print_('view');

	}

}
$ce = new goodsView();

$ce->main();
?>