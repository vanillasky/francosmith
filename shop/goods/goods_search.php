<?

include "../_header.php";
include "../lib/page.class.php";
include "../conf/config.pay.php";
@include "../conf/design.search.php";

if (class_exists('validation') && method_exists('validation', 'xssCleanArray')) {
	$_GET = validation::xssCleanArray($_GET, array(
		validation::DEFAULT_KEY	=> 'text',
		'sword'=>'html',
	));
}

$goodsDiscountModel = Clib_Application::getModelClass('goods_discount');

if (is_file(dirname(__FILE__) . "/../conf/config.soldout.php"))
	include dirname(__FILE__) . "/../conf/config.soldout.php";

@include dirname(__FILE__) . "/../conf/config.display.php";

if( !$_GET['edit'] && $_GET['sword'] && ($_GET['hid_pr_text'] == $_GET['sword'])){
	echo "<script>location.replace('".$_GET['hid_link_url']."')</script>";
	exit;
}

for($i=0; $i<count($cfg_search); $i++){
	foreach($cfg_search[$i] as $key=>$val){
		if( strstr($val, ',') ) $val = explode(',', $val);
		$s_type[$key] = $val;

		switch($key){
			case 'keyword':
				if( !is_array($val) ) {
					if( $val ) $s_type[$key] =  '<a href="javascript: add_param_submit(\'sword\', \''.$val.'\');">'.$val.'</a>';
					continue;
				}
				foreach($val as $k=>$v){
					$s_type[$key][$k] = '<a href="javascript: add_param_submit(\'sword\', \''.$v.'\');">'.$v.'</a>';
				}
				$s_type['keyword'] = implode(', ', $s_type['keyword']);
				break;
			case 'detail_type':
			case 'detail_add_type':
				if(!is_array($val)) $s_type[$key] = array($val);
				else $s_type[$key] = $val;
				if (in_array('color',$s_type[$key])) {
					// ../lib/smartSearch.class.php 내, getColor 메서드 참고
					$colorList = array();
					$query = "SELECT itemnm FROM ".GD_CODE." WHERE groupcd = 'colorList' ORDER BY sort";
					$result = $db->query($query);
					while($data = $db->fetch($result)) $colorList[] = $data['itemnm'];
				}
				break;
			default:
				$s_type[$key] = $val;
				break;
		}
	}
}
if(count($s_type['pr_text']) > 1) {
	$randcnt = rand(0, count($s_type['pr_text'])-1);
	$s_type['pr_text'] = $s_type['pr_text'][$randcnt];
	$s_type['link_url'] = $s_type['link_url'][$randcnt];
}

if(!$_GET['disp_type']) $_GET['disp_type'] = $s_type['disp_type'];
$s_type['disp_type'] = ($s_type['disp_type']? 'Y' : 'N');

if($_GET[skey2])$_GET[skey] = $_GET[skey2];
if($_GET[sword2])$_GET[sword] = $_GET[sword2];
if($_GET['searched2'])$_GET[searched] = $_GET[searched2];
if($_GET['log2'])$_GET[log] = $_GET[log2];
if($_GET['hid_sword']) $hid_checked = 'checked="chedked"';
if($_GET['detail_add_type']){
	foreach($_GET['detail_add_type'] as $val){
		$add_checked[$val] = 'checked="checked"';
	}
}

### 리스트 템플릿 환경 변수
$lstcfg[cols]	= 4;
$lstcfg[size]	= 80;
$lstcfg[tpl]	= "tpl_01";
$lstcfg[page_num] = array(20,40,60,100);

### 변수할당
if (!$_GET[page_num]) $_GET[page_num] = $lstcfg[page_num][0];
$selected[page_num][$_GET[page_num]] = "selected";
if (!$_GET[sort]) $_GET[sort] = "a.sort";
$selected[skey][$_GET[skey]] = "selected";

// 검색체크 여부
$searchCheck	= false;

if( $_GET['searched'] == 'Y' ){

	// 품절 상품 제외
	if ($cfg_soldout['exclude_search']) {
		$where[] = " !( b.runout = 1 OR (b.usestock = 'o' AND b.usestock IS NOT NULL AND b.totstock < 1) ) ";
	}
	// 제외시키지 않는 다면, 맨 뒤로 보낼지를 결정
	else if ($cfg_soldout['back_search']) {
		$_GET[sort] = "`soldout` ASC, ".$_GET[sort];
		$_add_field = ",IF (b.runout = 1 , 1, IF (b.usestock = 'o' AND b.totstock = 0, 1, 0)) as `soldout`";
	}

	$query = "select category, level, level_auth, auth_step from gd_category where level <> 0";
	$res = $db->_select($query);
	if(is_array($res)){
		for($i=0; $i<count($res); $i++){
			if($res[$i]['level_auth'] == '1' || $res[$i]['level_auth'] == '2') {
				$notCategory['level'][] = $res[$i]['level'];
				$notCategory['category'][] = $res[$i]['category'];
			}
		}
	}

	### 상품 리스트
	$pg = new Page($_GET[page],$_GET[page_num]);
	$pg->vars[page]= getVars('page,log,x,y');
	$pg->field = "
	b.*, c.price,c.reserve,c.consumer, d.brandnm,
	e.level, e.level_auth, e.auth_step $_add_field ";

	$db_table = "
	".GD_CATEGORY." AS e
	STRAIGHT_JOIN ".GD_GOODS_LINK." as a on a.category = e.category
	STRAIGHT_JOIN ".GD_GOODS." AS b ON ( a.goodsno = b.goodsno and b.open = 1)
	STRAIGHT_JOIN ".GD_GOODS_OPTION." AS c ON ( a.goodsno = c.goodsno AND c.link = 1 and go_is_deleted <> '1' and go_is_display = '1' )
	LEFT JOIN ".GD_GOODS_BRAND." AS d ON ( b.brandno = d.sno )
	";

	// 현재 진행중인 이벤트가 있다면 sql 확대.
	list($_tmp) = $db->fetch("SELECT COUNT(sno) FROM ".GD_EVENT." WHERE sdate <= '".date('Ymd')."' AND edate >= '".date('Ymd')."'");
	if ($_tmp > 0 || in_array('event',(array)$_GET['detail_add_type'])) {
		$pg->field .= ",h.sdate";

		$db_table .= "
					LEFT JOIN gd_goods_display AS g ON ( a.goodsno = g.goodsno AND g.mode LIKE 'e%' )
					LEFT JOIN gd_event AS h ON (g.mode = CONCAT('e',h.sno) AND h.sdate <= '".date('Ymd')."' AND h.edate >= '".date('Ymd')."')
		";
	}

	$level = $sess['level'] ? $sess['level'] : 0;
	if ($ici_admin === false) $where[] = "a.hidden=0";
	$where[] = "open";
	if ($tpl->var_['']['connInterpark']) $where[] = "b.inpk_prdno!=''";

	if ($_GET[cate]){
		$category = array_notnull($_GET[cate]);
		$category = $category[count($category)-1];

		if ($category){
			$where[] = "e.category like '$category%'";

			// 카테고리 검색이 있는 경우
			$searchCheck	= true;
		}
	}

	if($notCategory){
		for($i=0; $i<count($notCategory['level']); $i++){
			if(!$sess['level']){//비회원일경우 제한 카테고리만 제외
				$where[] = "e.category not like '".$notCategory['category'][$i]."'";
			}
			/* 리스트에서는 하위접근까지 막을수 있지만 검색에서는 접근가능
			else if($sess['level'] < $notCategory['level'][$i]){ //하위카테고리까지 제외
				$where[] = "e.category not like '".$notCategory['category'][$i]."%'";
			}*/
		}
	}

	if($_GET['detail_add_type']){
		foreach($_GET['detail_add_type'] as $key=>$val){
			switch($val){
				case 'free_deliveryfee':
					$where[] = "b.delivery_type='1'";  break;
				case 'dc':
					$ability[] = "0"; break;
				case 'save':
					$ability[] = "1"; break;
				case 'new':
					$where[] = "b.regdt BETWEEN '".date("Y-m-d H:i:s", strtotime($today." -30 day"))."' AND '".date("Y-m-d H:i:s", time())."' "; break;
				case 'event':
					$where[] = "h.sdate IS NOT NULL "; break;
			}
		}

		if($ability){
			$query = " SELECT goodstype, couponcd ";
			$query .= "  FROM gd_coupon ";
			$query .= " WHERE coupontype = '1' ";//  --회원직접다운로드인 쿠폰만
			$query .= "   AND ability IN (".implode(', ', $ability).") ";
			$query .= "   AND (CASE priodtype WHEN '0' THEN sdate WHEN '1' THEN regdt END) <= '".date('Y-m-d H:i:s',time())."' AND edate >= '".date('Y-m-d H:i:s',time())."' ";
			$ret = $db->_select($query);

			for($i=0, $il=count($ret); $i<$il; $i++){
				if( $ret[$i]['goodstype'] != '0' ) $tmp_cd[] = $ret[$i]['couponcd']; //전체상품 사용가능 쿠폰제외
			}

			if($tmp_cd){
				$query = " SELECT cpn.ability, tmp.couponcd, tmp.goodsno ";
				$query .= " FROM ( ";
				$query .= "		   SELECT c.couponcd, a.goodsno ";
				$query .= "			 FROM gd_goods AS a JOIN gd_goods_link AS b ON (a.goodsno = b.goodsno) ";
				$query .= "								JOIN gd_coupon_category AS c ON (b.category = c.category AND c.couponcd IN [v] ) ";
				$query .= "		    UNION ALL ";
				$query .= "			SELECT couponcd, goodsno ";
				$query .= "			  FROM gd_coupon_goodsno ";
				$query .= "			 WHERE couponcd IN [v]	";
				$query .= "		  ) AS tmp JOIN gd_coupon AS cpn ON (tmp.couponcd = cpn.couponcd)";
				$query .= " ORDER BY cpn.ability ";
				$query = $db->_query_print($query, $tmp_cd, $tmp_cd);
				$ret = $db->_select($query);

				$cnt = 0;
				for($i=0, $il=count($ret); $i<$il; $i++){
					if( $i>0 && $ret[$i]['ability'] != $ret[$i-1]['ability']) $cnt++;
					$tmp_goods[$cnt][] =  $ret[$i]['goodsno'];
				}

				if( count($tmp_goods) > 1 ){//할인, 적립쿠폰 동시검색일때
					for($i=0, $il=count($tmp_goods[0]); $i<$il; $i++){
						for($j=0, $jl=count($tmp_goods[1]); $j<$jl; $j++){
							if($tmp_goods[0][$i] == $tmp_goods[1][$j]) $search_goods[] = $tmp_goods[1][$j];
						}
					}
				}
				else $search_goods = $tmp_goods[0];

				$where[] = $db->_query_print("b.goodsno IN [v]", $search_goods);
			}

			// 조건 선택 검색이 있는 경우
			$searchCheck	= true;
		}
	}

	if (is_numeric($_GET[price][0]) == true || is_numeric($_GET[price][1]) == true || (is_numeric($_GET[price][0]) == true && is_numeric($_GET[price][1]) == true)) {
		if ($_GET[price][0] != '' && $_GET[price][1] != '') $where[] = "c.price between {$_GET[price][0]} and {$_GET[price][1]}";
		else if ($_GET[price][0] != '' && $_GET[price][1] == '') $where[] = "c.price >= {$_GET[price][0]}";
		else if ($_GET[price][0] == '' && $_GET[price][1] != '') $where[] = "c.price <= {$_GET[price][1]}";

		// 가격 검색이 있는 경우
		$searchCheck	= true;
	}

	if ($_GET[skey] && $_GET[sword]){
		switch ($_GET[skey]){
			case "all": $key = "concat( b.keyword, b.goodsnm, b.goodscd, b.maker, if(d.brandnm is null,'',d.brandnm) )"; break;
			case "brand": $key = "d.brandnm"; break;
			default: $key = $_GET[skey];
		}

		$tmp_sword = $_GET['sword'].' '.$_GET['hid_sword'];
		$r_word = array_notnull(array_unique(explode(" ",$tmp_sword)));
		$tmp = array();
		for ($i=0;$i<count($r_word);$i++){
			$tmp[] = "$key like '%$r_word[$i]%'";
			if (strlen($r_word[$i])>2) $log_word[] = $r_word[$i];
		}
		if (is_array($tmp)) $where[] = "(".implode(" and ",$tmp).")";

		// 검색 항목 및 검색어, 결과내 재검색이 있는 경우
		$searchCheck	= true;
	}

	// 색상 검색
	if ($_GET['ssColor'] && !empty($colorList)) {

		$colors = explode('#',$_GET['ssColor']);
		$tmp = array();

		foreach ($colors as $color) {
			if ($color = trim($color)) $tmp[] = "b.color like '%".$db->_escape($color)."%'";
		}

		if (sizeof($tmp) > 0) $where[] = "(".implode(" OR ", $tmp).")";

		// 색상 검색이 있는 경우
		$searchCheck	= true;
	}

	// 검색 조건이 있는 경우에만 쿼리 진행
	if ($searchCheck === true) {

		$pg->cntQuery = "select count(distinct b.goodsno) from ".$db_table." where " . implode(" and ", $where);
		$pg->setQuery($db_table,$where,$_GET[sort],' group by b.goodsno');
		$pg->exec();

		$res = $db->query($pg->query);
		while ($data=$db->fetch($res)){

			$data['stock'] = $data['totstock'];

			### 실재고에 따른 자동 품절 처리
			if ($data[usestock] && $data[stock]<=0) $data[runout] = 1;

			### 적립금 정책적용
			if(!$data['use_emoney']){
				if( !$set['emoney']['chk_goods_emoney'] ){
					if( $set['emoney']['goods_emoney'] ) $data['reserve'] = getDcprice($data['price'],$set['emoney']['goods_emoney'].'%');
				}else{
					$data['reserve']	= $set['emoney']['goods_emoney'];
				}
			}

			### 즉석할인쿠폰 유효성 검사
			list($data[coupon],$data[coupon_emoney]) = getCouponInfo($data[goodsno],$data[price]);
			$data[reserve] += $data[coupon_emoney];

			### 어바웃쿠폰 금액
			if($about_coupon->use){
				$data['coupon']  += (int) getDcPrice($data['price'], $about_coupon->sale);
			}

			### 아이콘
			$data[icon] = setIcon($data[icon],$data[regdt]);

			### 카테고리
			if ($category == '')
				list($data['category']) = $db->fetch("select ".getCategoryLinkQuery('category', null, 'max')." from ".GD_GOODS_LINK." where hidden=0 and goodsno='{$data[goodsno]}' limit 1");
			else
				$data['category'] = $category;
				$cauth_step = explode(':', $data['auth_step']);
				$data['auth_step'] = array();
				$data['auth_step'][0] = (in_array('1', $cauth_step) ? 'Y' : 'N' ) ;
				$data['auth_step'][1] = (in_array('2', $cauth_step) ? 'Y' : 'N' ) ;
				$data['auth_step'][2] = (in_array('3', $cauth_step) ? 'Y' : 'N' ) ;

			### 상품할인
			if ($data['use_goods_discount']) $data['special_discount_amount'] = $goodsDiscountModel->getDiscountAmountSearch($data, Clib_Application::session()->getMemberLevel());

			// 상품할인 가격 표시
			if ($displayCfg['displayType'] === 'discount') {
				if ($data['special_discount_amount']) {
					$data['oriPrice'] = $data['price'];
					$data['goodsDiscountPrice'] = $data['price'] - $data['special_discount_amount'];
				}
				else {
					$data['oriPrice'] = '0';
					$data['goodsDiscountPrice'] = $data['price'];
				}
			}
			
			// 출력 제어
			$loop[] = setGoodsOuputVar($data);
		}

		### 인기검색어 로그저장
		if ($_GET[log] && $log_word){ foreach ($log_word as $v){
			list ($chk) = $db->fetch("select count(*) from ".GD_SEARCH." where regdate=left(now()+0,8) and word='$v'");
			$query = ($chk) ? "update ".GD_SEARCH." set cnt=cnt+1 where regdate=left(now()+0,8) and word='$v'" : "insert into ".GD_SEARCH." set word='$v', regdate=left(now()+0,8)";
			$db->query($query);
		}}

		### ace 카운터
		$Acecounter->goods_search($_GET[sword]);
		if($Acecounter->scripts){
			$systemHeadTagEnd .= $Acecounter->scripts;
			$tpl->assign('systemHeadTagEnd',$systemHeadTagEnd);
		}

	}
}


$tpl->assign(array(
			pg		=> $pg,
			loop	=> $loop,
			lstcfg	=> $lstcfg,
			page_type=>'search',
			));
$tpl->print_('tpl');
//$db->viewLog();

?>