<?PHP
require_once dirname(__FILE__) . "/../../../setGoods/class/_common.php";
require_once dirname(__FILE__) . "/../../../lib/library.php";
@include dirname(__FILE__) . "/../../../../shop/conf/coupon.php";

class EnamuDAO{
	var $db;
	var $cfg;
	var $session;
	var $opt;
	var $optkey;
	var $setval;


	function EnamuDAO() {
		//DB연결
		$this->db = Core::loader('db');

		$config = Core::loader('config');
		$this->cfg = $config->load('config','config',SHOPROOT.'/conf/config.php');

		$this->setval = $this->getConfig('set');
	}

	### 이차원배열까지만 지원 (차후 다차원모두 지원)
	### 세션정보를 가지고온다.
	function getSession($name){

		if(array_key_exists($name,$_SESSION) == true){
				return $_SESSION[$name];

		}

		reset($_SESSION);
		while (list($key) = each($_SESSION)) {
			if(array_key_exists($name,$_SESSION[$key]) == true){
				return $_SESSION[$key][$name];
			}
		}

	}

	function adminAuth(){

		return ($this->getSession('level') > 79) ? true : false;

	}


	### 상품 정보 로드
	function goodsFetch($goodsno){
		$query = "select a.*,b.category, c.level, c.level_auth, c.auth_step";
		$query .= " from ".GD_GOODS." a";
		$query .= "	left join ".GD_GOODS_LINK." b on a.goodsno=b.goodsno";
		$query .= "	join ".GD_CATEGORY." c on b.category = c.category";
		$query .= " where a.goodsno='".$goodsno."'";
		$query .= " limit 1";

		$data = $this->db->fetch($query,1);

		return $data;
	}

	### 기존 이나무 필수옵션 계산법을 그대로 적용 차후 이나무 goodsView.php파일 수정시 수정 필수
	function goodsOption($goodsno,$data,$m_no){
		$this->optkey = "";
		$this->opt = "";
		$sess = $this->getSession('sess');

		if(!$this->setval['emoney']['cut'])$this->setval['emoney']['cut']=0;
		$this->setval['emoney']['base'] = pow(10,$this->setval['emoney']['cut']);

		### 회원정보 가져오기
		if ($sess){
			$query = "select * from ".GD_MEMBER." a left join ".GD_MEMBER_GRP." b on a.level=b.level where m_no='".$m_no."'";
			$member = $this->db->fetch($query,1);
		}else{
			### 기본 할인율
			@require_once $_SERVER[DOCUMENT_ROOT] . "/shop/conf/fieldset.php";
			$member = $this->db->fetch("select * from ".GD_MEMBER_GRP." where level='".$joinset[grp]."' limit 1");
		}

		### 회원할인 제외상품 체크
		$mdc_exc = chk_memberdc_exc($member,$goodsno);

		### 상품 옵션
		$res = $this->goodsOptionSql($goodsno);

		while ($tmp=$this->db->fetch($res,1)){

			$tmp = array_map("htmlspecialchars",$tmp);

			if ($tmp[stock] && !$isSelected){
				$isSelected = 1;
				$tmp[selected] = "selected";
				$preSelIndex = $idx++;
			}

			### 옵션별 회원 할인가 및 쿠폰 할인가 계산
			$realprice = $tmp[realprice] = $tmp[memberdc] = $tmp[coupon] = $tmp[coupon_emoney] = $tmp[couponprice] = 0;

			$group_profit = load_class('group_profit', 'group_profit',SHOPROOT.'lib/group_profit.class.php');
			$group_profit->getGroupProfit();
			if( $group_profit->dc_type == 'goods' ){
				if( $tmp[price] >= $group_profit->dc_std_amt ){
					if(!$mdc_exc) $tmp[memberdc] = getDcprice($tmp[price],$member[dc]."%");
				}
			}

			$tmp[realprice] = $tmp[price] - $tmp[memberdc];
			$tmp_coupon = getCouponInfo($goodsno,$tmp['price'],'v');

			if($cfgCoupon[use_yn] == '1'){
				if($tmp_coupon)foreach($tmp_coupon as $v){
					$tp = $v[price];
					if(substr($v[price],-1) == '%') $tp = getDcprice($tmp[price],$v[price]);

					if($cfgCoupon['double']==1){
						if(!$v[ability]){
							$tmp[coupon] += $tp;
						}else {
							$tmp[coupon_emoney] += $tp;
						}
					}else{
						if(!$v[ability] && $tmp[coupon] < $tp) $tmp[coupon] = $tp;
						else if($v[ability] && $tmp[coupon_emoney] < $tp) $tmp[coupon_emoney] = $tp;
					}
				}
			}
			if($tmp[coupon] && $tmp[memberdc] && $cfgCoupon[range] != '2') $realprice = $tmp[realprice];
			else $realprice = $tmp[price];
			$tmp[couponprice] = $realprice - $tmp[coupon];
			if($tmp[coupon] && $tmp[memberdc] && $cfgCoupon[range] == '2') $tmp[realprice] = $tmp[memberdc] = 0;
			if($tmp[coupon] && $tmp[memberdc] && $cfgCoupon[range] == '1') $tmp[couponprice] = $tmp[coupon] = 0;
			if (!$this->optkey){
				$this->optkey = $tmp[opt1];
				$data[a_coupon] = $tmp_coupon;
			}

			if(!$data['use_emoney']){

				if($this->setval['emoney']['useyn'] == 'n') $tmp['reserve'] = 0;
				else {
					if( !$this->setval['emoney']['chk_goods_emoney'] ){
						$tmp['reserve']	= 0;
						if( $this->setval['emoney']['goods_emoney'] ) $tmp['reserve'] = getDcprice($tmp['price'],$this->setval['emoney']['goods_emoney'].'%');
					}else{
						$tmp['reserve']	= $this->setval['emoney']['goods_emoney'];
						if(!$tmp['reserve']) $tmp['reserve'] =0;
					}
				}
			}


			if($tmp['opt1img'])$opt1img[$tmp['opt1']] = $tmp['opt1img'];
			if($tmp['opt1icon'])$opticon[0][$tmp['opt1']] = $tmp['opt1icon'];
			if($tmp['opt2icon'])$opticon[1][$tmp['opt2']] = $tmp['opt2icon'];
			$lopt[0][$tmp['opt1']] = 1;
			$lopt[1][$tmp['opt2']] = 1;
			$this->opt[$tmp[opt1]][] = $tmp;
			$data[stock] += $tmp[stock];
		}

		$data[coupon_img_path] = "/shop/data/skin/".$this->cfg['tplSkin']."/img/common/";

		### 실재고에 따른 자동 품절 처리
		if ($data[usestock] && $data[stock]==0) $data[runout] = 1;

		$data[coupon] = $data[coupon_emoney] = 0;
		$data[price] = &$this->opt[$this->optkey][0][price];
		$data[consumer]	= &$this->opt[$this->optkey][0][consumer];
		$data[reserve] = &$this->opt[$this->optkey][0][reserve];
		$data[coupon] = &$this->opt[$this->optkey][0][coupon];
		$data[couponprice] = &$this->opt[$this->optkey][0][couponprice];
		$data[coupon_emoney] = &$this->opt[$this->optkey][0][coupon_emoney];
		$data[memberdc]	= &$this->opt[$this->optkey][0][memberdc];
		$data[realprice] = &$this->opt[$this->optkey][0][realprice];

		return $data;
	}


	function point_chk($goodsno){
		list ($point) = $this->db->fetch("select round(avg(point)) from ".GD_GOODS_REVIEW." where goodsno='".$goodsno."' and sno=parent");
		$data[chk_point] = $point;
		$data[point] = ($point) ? $point : 5;

		return $data;
	}

	### 네이버 마일리지
	function naverNcash($data,$goodsno){

		$data['naverNcash']="";

		$naverNcash = &load_class('naverNcash','naverNcash',SHOPROOT.'lib/naverNcash.class.php');
		if(!$naverNcash->realyn())$naverNcash->useyn = "N";
		$item[0]['goodsno'] = $goodsno;
		$exceptionYN = $naverNcash->exception_goods($item); // 예외상품 체크
		if($naverNcash->useyn == 'Y' && $exceptionYN == 'N' && $naverNcash->baseAccumRate){
			$data['naverNcash'] = "Y";
			$data['N_ba'] = preg_replace('/\.0$/', '', $naverNcash->get_base_accum_rate());
			$data['N_aa'] = preg_replace('/\.0$/', '', $naverNcash->get_add_accum_rate());
		}else if($naverNcash->useyn == 'Y' && $exceptionYN == 'Y' && $naverNcash->baseAccumRate){
			$data['naverNcash'] = "Y";
			$data['exception'] = "적립 및 사용 제한 상품";
		}else{
			$data['naverNcash'] = $data['N_ba'] = $data['N_aa'] = "";
		}

		return $data;
	}

	### 추가옵션
	function addoptnm($optnm,$goodsno){

		$r_addoptnm = explode("|",$optnm);

		for ($i=0;$i<count($r_addoptnm);$i++){
			list ($addoptnm[],$addoptreq[]) = explode("^",$r_addoptnm[$i]);
		}

		$query = "select * from ".GD_GOODS_ADD." where goodsno='".$goodsno."' order by step,sno";
		$res = $this->db->query($query);

		while ($tmp=$this->db->fetch($res,1)){
			$addopt[$addoptnm[$tmp[step]]][] = $tmp;
		}


		$option_val = array('addopt'=>$addopt,'addoptreq'=>$addoptreq);
		return $option_val;
	}



	### 상품 브랜드 정보
	function goodsBrand($brandno){
		$query = "select brandnm from ".GD_GOODS_BRAND." where sno='".$brandno."'";
		$ret = $this->fetch($query);

		return $ret;
	}

	function EditGoodsList($val,$sp,$st,$pg='0',$listNum='0'){

		// 상품분류 연결방식 전환 여부에 따른 처리
		$whereArr	= getCategoryLinkQuery('c.category', $val);

		$query  = "SELECT ".$whereArr['distinct']." a.goodsno as goodsno, a.goodsnm as goodsnm, a.img_s as img_s,a.img_l as img_l, a.open as open, a.regdt as regdt, b.price as price, b.reserve as reserve, a.use_emoney as use_emoney ";
		$query .= "FROM ".GD_GOODS." a ";
		$query .= "LEFT JOIN ".GD_GOODS_OPTION." b ON a.goodsno = b.goodsno ";
		$query .= "AND link and go_is_deleted <> '1' and go_is_display = '1' ";
		$query .= "LEFT JOIN ".GD_GOODS_LINK." c ON a.goodsno = c.goodsno ";
		$query .= "WHERE a.todaygoods ='n' ";

		if($val != '')$query .= "AND" . $whereArr['where'];

		if($st) $query .= "AND a.".$sp." LIKE'%".$st."%' ";

		$query .= "ORDER BY a.goodsno desc";

		if($listNum > 0) $query .= " limit ".(($pg-1) * $listNum).",".$listNum;

		$ret = $this->fetch($query);


		return $ret;
	}

	function EditGoodsListTotal($val,$sp,$st){

		// 상품분류 연결방식 전환 여부에 따른 처리
		$whereArr	= getCategoryLinkQuery('c.category', $val);

		$query  = "SELECT ".$whereArr['distinct']." a.goodsno as goodsno, a.goodsnm as goodsnm, a.img_s as img_s,a.img_l as img_l, a.open as open, a.regdt as regdt, b.price as price, b.reserve as reserve, a.use_emoney as use_emoney ";
		$query .= "FROM ".GD_GOODS." a ";
		$query .= "LEFT JOIN ".GD_GOODS_OPTION." b ON a.goodsno = b.goodsno ";
		$query .= "AND link and go_is_deleted <> '1' and go_is_display = '1' ";
		$query .= "LEFT JOIN ".GD_GOODS_LINK." c ON a.goodsno = c.goodsno ";
		$query .= "WHERE a.todaygoods ='n' ";

		if($val != '')$query .= "AND" . $whereArr['where'];

		if($st) $query .= "AND a.".$sp." LIKE'%".$st."%' ";

		$query .= "ORDER BY a.goodsno desc";

		$ret = $this->fetch($query);
		$rowCount = count($ret);

		return $rowCount;
	}



	function new_EditGoodsList($val,$sp,$st){

		$pram[sword] = trim($st);
		$pram[skey] = $sp;
		$pram[sort] = "goodsno desc";
		$pram[cate] = $sval;
		$pram[page_num] = 51;
		$pram[page] = 1;
		$objs =$this->db->procedure('admin_goods_list',$pram);

		return $objs;
	}

	function fetch($query){

		$res = $this->db->query($query);
		$retrunArray = array();
		while ($data=$this->db->fetch($res)){
			array_push($retrunArray, $data);
		}

		return $retrunArray;
	}

	### config 파일을 로드한다.
	function getConfig($name){
		if($name == 'soldout'){
			$cfg_soldout = "";

			if (is_file(SHOPROOT . "/conf/config.soldout.php"))
				include SHOPROOT . "/conf/config.soldout.php";

			return $cfg_soldout;

		}else if($name == 'set'){
		### 적립금 설정 로드
			$set ="";

			if (is_file(SHOPROOT . "/conf/config.pay.php"))
				include SHOPROOT . "/conf/config.pay.php";

			return $set;
		}

	}

	### 상품 옵션
	function goodsOptionSql($goodsno){
		$query = "select * from ".GD_GOODS_OPTION." where goodsno='".$goodsno."' and go_is_deleted <> '1' and go_is_display = '1'  order by sno asc";
		$res = $this->db->query($query);
		return $res;
	}

	### 상품당 가격
	function goodsOptionPrice($goodsno){

		$res = $this->goodsOptionSql($goodsno);

		while ($tmp=$this->db->fetch($res)){
			$tmp = array_map("htmlspecialchars",$tmp);
			$opt1[] = $tmp[opt1];
			$opt2[] = $tmp[opt2];
			$opt[$tmp[opt1]][$tmp[opt2]] = $tmp;

			### 총재고량 계산
			$stock += $tmp[stock];

			### 옵션이미지
			$opt1img[$tmp['opt1']] = $tmp['opt1img'];
			$opt1icon[$tmp['opt1']] = $tmp['opt1icon'];
			$opt2icon[$tmp['opt2']] = $tmp['opt2icon'];
		}
		if ($opt1) $opt1 = array_unique($opt1);
		if ($opt2) $opt2 = array_unique($opt2);
		if (!$opt){
			$opt1 = array('');
			$opt2 = array('');
		}

		### 기본 가격 할당

		$Goption[price]	  = $opt[$opt1[0]][$opt2[0]][price];
		$Goption[consumer] = $opt[$opt1[0]][$opt2[0]][consumer];
		$Goption[supply]	  = $opt[$opt1[0]][$opt2[0]][supply];
		$Goption[reserve]  = $opt[$opt1[0]][$opt2[0]][reserve];


		return $Goption;
	}

	### 상품 N개의 가격을 알아온다 (판매가격)
	function realgoodsOptionPrice($objs){
		$ret = "";
		foreach ( $objs as $obj) {
			$price = $this->goodsOptionPrice($obj->get('goods_idx'));
			$ret[$obj->get('goods_idx')] = $price[price];
			$ret[total] += $price[price];
		}

		return $ret;
	}

	### 상품의 품절상태를 가지고온다.
	### 파라미터
	### $goodsInfo -> 상품 정보를 넘겨받는다. 없다면 상품번호로 조회를 한다.
	### $goodsno -> 상품정보값이 없을경우 조회를 위해서 받는다
	function Runout($goodsInfo = "",$goodsno=""){
		$ret = array();
		if(!is_array($goodsInfo)){
			$goodsInfo =  $this->goodsFetch($goodsno);
		}

		$runout = $goodsInfo['runout'];
		$open = $goodsInfo['open'];
		if($open == '1'){


			if($runout == '0'){
				if($goodsInfo['usestock'] == 'o' && $goodsInfo['totstock'] < 1){
					$runout = '1';
				}
			}
		}else{
			$runout = '1';
		}

		$ret[runout] = $runout == '1'? 'N':'Y';
		$ret[open] = $open == '1'? 'Y':'N';

		return $ret;
	}


	function strCut($string,$length,$dep = "..." ){

	   $text=strip_tags($string);
	   $flag=0;
	   $count=0;

		for($i=0;$i<$length;$i++){
			if(Ord($text[$i])>=0x65 && Ord($text[$i])<=0x91){
				$count++;
			}
		}
		$length -= $count;


	   if(strlen($text)>$length){
		 for($i=0;$i<$length-3;$i++){
			if(Ord($text[$i])>=0x80){
				$flag ? $flag=0: $flag=1;
			}else
				$flag=0;
		 }
		  if($flag) $text = substr($text,0,$length-4).$dep;
		  else
		  $text = substr($text,0,$length-3).$dep;
	   }
	   return $text;
	}

	### 상품의 존재 유무
	function goodsConfirm($imgno,$T_img_cnt){
		$ret = 'Y';
		if(count($imgno) != $T_img_cnt){
			return $ret = 'N';
		}

		for($i=0;$i < $T_img_cnt;$i++){
			$goodsInfo = $this->goodsFetch($imgno[$i]);
			if($goodsInfo['goodsno'] < 1){
				return $ret = 'N';
			}
		}

		return $ret;
	}

	// 성인 상품 확인
	function goodsUseAdult($goodsno){
		$query = " select use_only_adult from ".GD_GOODS." where goodsno='".$goodsno."' limit 1";
		list($use_only_adult) = $this->db->fetch($query);

		return $use_only_adult;
	}

	function canAccessAdult(){
		if($this->adminAuth() || $this->getSession('adult')){
			return true;
		}
		return false;
	}

} //class End
?>

