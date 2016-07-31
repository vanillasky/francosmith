<?
$location = "주문관리 > 주문통합관리 > 주문통합 리스트";
include "../_header.php";
include "../../lib/page.class.php";
@include "../../conf/config.pay.php";
@include "../../conf/phone.php";
include "../../lib/sAPI.class.php";

$sAPI = new sAPI();

$code_arr['grp_cd'] = 'mall_cd';
$selly_mall_cd = $sAPI->getcode($code_arr, 'hash');
unset($code_arr);

if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST); stripslashes_all($_GET); stripslashes_all($_COOKIE);
}

// 통함 주문 설정
	@include(dirname(__FILE__).'/_cfg.integrate.php');

// 기본 값
	$now = time();

	if (empty($_GET)) {
		$loc = $sess['m_id'].preg_replace('/[^a-zA-Z0-9]/','_',($_SERVER['PHP_SELF']));
		list($_data) = $db->fetch("SELECT `value` FROM gd_env WHERE `category` = 'form_helper' AND `name` = 'searchCondition'");
		$data = $_data ? unserialize($_data) : array();

		if (!empty($data[$loc])) {
			parse_str( iconv('utf-8','euc-kr',urldecode($data[$loc])),$_GET );
		}
		// 기간 조정
		if (!empty($_GET['regdt'][0]) && !empty($_GET['regdt'][1])) {
			$gap = abs( strtotime($_GET['regdt'][1]) - strtotime($_GET['regdt'][0]) );
			$_GET['regdt'][0] = date('Ymd',$now - $gap);
			$_GET['regdt'][1] = date('Ymd',$now);
		}
		unset($loc,$data,$_data);
	}

// 통합 데이터 수집
	$integrate_order = Core::loader('integrate_order');
	$integrate_order -> doSync();

// get 파라미터 처리 및 기본값 적용
	unset($_GET['x'],$_GET['y']);

	$_GET['sort']			= !empty($_GET['sort']) ? $_GET['sort'] : 'o.ord_date desc';		// 정렬
	$_GET['mode']			= !empty($_GET['mode']) ? $_GET['mode'] : 'group';					// 뷰 형식
	$_GET['channel']		= !empty($_GET['channel']) ? $_GET['channel'] : array();			// 판매채널

	$_GET['ord_status']		= isset($_GET['ord_status']) ? $_GET['ord_status'] : -1;	// 처리상태
	$_GET['pay_method']		= !empty($_GET['pay_method']) ? $_GET['pay_method'] : '';		// 결제수단
	$_GET['ord_type']		= !empty($_GET['ord_type']) ? $_GET['ord_type'] : '';		// 접수유형
	$_GET['skey']			= !empty($_GET['skey']) ? $_GET['skey'] : '';					// 주문검색 조건
	$_GET['sword']			= !empty($_GET['sword']) ? trim($_GET['sword']) : '';					// 주문검색 키워드
	$_GET['dtkind']			= !empty($_GET['dtkind']) ? $_GET['dtkind'] : 'ord_date';				// 날짜 조건
	$_GET['regdt']			= !empty($_GET['regdt']) ? $_GET['regdt'] : array(date('Ymd',strtotime('-'.(int)$cfg['orderPeriod'].' day',$now)), date('Ymd',$now));					// 날짜
	$_GET['regdt_range']	= !empty($_GET['regdt']) ? $_GET['regdt'] : '';					// 날짜 기간 ( regdt[0] 부터 며칠 )
	$_GET['regdt_time']		= !empty($_GET['regdt_time']) ? $_GET['regdt_time'] : array(-1,-1);		// 시간
	$_GET['sgkey']			= !empty($_GET['sgkey']) ? $_GET['sgkey'] : '';					// 상품검색 조건
	$_GET['sgword']			= !empty($_GET['sgword']) ? trim($_GET['sgword']) : '';				// 상품검색 키워드

	$_GET['flg_egg']			= !empty($_GET['flg_egg']) ? $_GET['flg_egg'] : '';					// 전자보증보험
	$_GET['flg_escrow']		= !empty($_GET['flg_escrow']) ? $_GET['flg_escrow'] : '';			// 에스크로
	$_GET['flg_cashreceipt']	= !empty($_GET['flg_cashreceipt']) ? $_GET['flg_cashreceipt'] : '';		// 현금영수증

	$_GET['flg_coupon']		= !empty($_GET['flg_coupon']) ? $_GET['flg_coupon'] : '';			// 쿠폰사용

	$_GET['flg_aboutcoupon']	= !empty($_GET['flg_aboutcoupon']) ? $_GET['flg_aboutcoupon'] : '';		// 어바웃쿠폰
	$_GET['flg_cashbag']			= !empty($_GET['flg_cashbag']) ? $_GET['flg_cashbag'] : '';					// ok캐시백 적립
	$_GET['pay_method_p']	= !empty($_GET['pay_method_p']) ? $_GET['pay_method_p'] : '';	// 적립금(포인트)
	$_GET['chk_inflow']		= !empty($_GET['chk_inflow']) ? $_GET['chk_inflow'] : array();	// 홍보채널 (유입경로)

	$_GET['page']			= !empty($_GET['page']) ? $_GET['page'] : 1;						// 페이지
	$_GET['page_num']		= !empty($_GET['page_num']) ? $_GET['page_num'] : ($cfg['orderPageNum'] ? $cfg['orderPageNum'] : 20);	// 페이지당 레코드수

// 검색절 만듦

	#0. 초기화
		$arWhere = array();

	#1. 판매 채널
		$_tmp = array();

		if (sizeof($_GET['channel']) < 1 || $_GET['channel']['all']) {
			$_GET['channel'] = array();
			$_GET['channel']['all'] = 1;
		}
		elseif (sizeof($_GET['channel']) === 6) {
			$_GET['channel'] = array();
			$_GET['channel']['all'] = 1;
		}
		else {

			if ($_GET['channel']['mobile'] || $_GET['channel']['todayshop']) $_GET['channel']['enamoo'] = 1;
			foreach($_GET['channel'] as $k=>$v) {
				if ($k == 'mobile') {
					$arWhere[] = 'o.flg_mobile = \'1\'';
				}
				else if ($k == 'todayshop') {
					$arWhere[] = 'exists(SELECT * FROM '.GD_ORDER_ITEM.' AS oi JOIN '.GD_GOODS.' AS g ON oi.goodsno=g.goodsno WHERE oi.ordno=o.ordno AND g.todaygoods=\'y\')';
				}
				else if($k == 'payco') {
					$arWhere[] = " o.pg = '$k' ";
				}
				else {
					$_tmp[] = " o.channel = '$k' ";
				}
			}
		}

	#1-1. 서브 판매 채널
		if(!$_GET['sub_channel']['all']) {
			if(sizeof($_GET['sub_channel']) < 1) {
				$arWhere[] = " o.channel != 'selly' ";
			}
			else {
				foreach($_GET['sub_channel'] as $s_k => $s_v) {
					$_tmp[] = " (o.channel = 'selly' AND o.sub_channel = '$s_k') ";
				}
			}
		}

		if (sizeof($_tmp) > 0) $arWhere[] = '('.implode(' OR ',$_tmp).')';
		unset($_tmp);

	#2. 주문 상태
		if ($_GET['ord_status'] == 91) {
			$arWhere[] = "(o.old_ordno > '')";
		}
		else if ($_GET['ord_status'] > -1) {
			$arWhere[] = $db->_query_print('o.ord_status= [s]',$_GET['ord_status']);
		}

	#3. 결제 수단
		if($_GET['pay_method']) {
			$arWhere[] = $db->_query_print('o.pay_method= [s]',$_GET['pay_method']);
		}

	#4. 통합 검색
		if($_GET['sword'] && $_GET['skey']) {
			$es_sword = $db->_escape($_GET['sword']);
			switch($_GET['skey']) {
				case 'all':
					$_where = array();

					foreach($integrate_cfg['skey'] as $cond) {
						if (preg_match($cond['pattern'],$es_sword)) {
							$_condition = $cond['field'];

							if ($cond['condition'] == 'like') $_condition .= ' like \'%'.$es_sword.'%\'';
							else if ($cond['condition'] == 'equal') $_condition .= ' = \''.$es_sword.'\'';
							else continue;

							$_where[] = $_condition;
						}
					}

					if (sizeof($_where) > 0) $arWhere[] = "(".implode(' OR ',$_where).")";
					break;
				case 'ordno': $arWhere[] = "o.ordno = '{$es_sword}'"; break;
				case 'ord_name': $arWhere[] = "o.ord_name like '%{$es_sword}%'"; break;
				case 'rcv_name': $arWhere[] = "o.rcv_name like '%{$es_sword}%'"; break;
				case 'pay_bank_name': $arWhere[] = "o.pay_bank_name like '%{$es_sword}%'"; break;
				case 'm_id': $arWhere[] = "m.m_id = '{$es_sword}'"; break;
				case 'ord_phone': $arWhere[] = "(o.ord_phone like '%{$es_sword}%' OR o.ord_mobile like '%{$es_sword}%')"; break;
				case 'rcv_phone': $arWhere[] = "(o.rcv_phone like '%{$es_sword}%' OR o.rcv_mobile like '%{$es_sword}%')"; break;
				case 'rcv_address': $arWhere[] = "o.rcv_address like '%{$es_sword}%'"; break;
				case 'dlv_no': $arWhere[] = "o.dlv_no like '%{$es_sword}%'"; break;
			}
		}

	#5. 처리일자
		if($_GET['regdt'][0]) {
			if(!$_GET['regdt'][1]) $_GET['regdt'][1] = date('Ymd',$now);

			$tmp_start = substr($_GET['regdt'][0],0,4).'-'.substr($_GET['regdt'][0],4,2).'-'.substr($_GET['regdt'][0],6,2);
			$tmp_end = substr($_GET['regdt'][1],0,4).'-'.substr($_GET['regdt'][1],4,2).'-'.substr($_GET['regdt'][1],6,2);

			if ((int)$_GET['regdt_time'][0] !== -1 && (int)$_GET['regdt_time'][1] !== -1) {
				$tmp_start .= ' '.sprintf('%02d',$_GET['regdt_time'][0]).':00:00';
				$tmp_end .= ' '.sprintf('%02d',$_GET['regdt_time'][1]).':59:59';
			}
			else {
				$tmp_start .= ' 00:00:00';
				$tmp_end .= ' 23:59:59';
			}
			switch($_GET['dtkind']) {
				case 'ord_date': $arWhere[] = $db->_query_print('o.ord_date between [s] and [s]',$tmp_start,$tmp_end); break;
				case 'pay_date': $arWhere[] = $db->_query_print('o.pay_date between [s] and [s]',$tmp_start,$tmp_end); break;
				case 'dlv_date': $arWhere[] = $db->_query_print('o.dlv_date between [s] and [s]',$tmp_start,$tmp_end); break;
				case 'fin_date': $arWhere[] = $db->_query_print('o.fin_date between [s] and [s]',$tmp_start,$tmp_end); break;
			}
		}

	#6. 상품검색
		$join_GD_PURCHASE = '';
		if($_GET['sgword'] && $_GET['sgkey']) {
			$es_sgword = $db->_escape($_GET['sgword']);
			switch($_GET['sgkey']) {
				case 'goodsnm': $arWhere[] = "oi.goodsnm like '%{$es_sgword}%'"; break;
				case 'brandnm': $arWhere[] = "EXISTS (SELECT ordno FROM ".GD_ORDER_ITEM." AS _oi WHERE _oi.brandnm like '%{$es_sgword}%' AND _oi.ordno = o.ordno) "; break;
				case 'maker': $arWhere[] = "EXISTS (SELECT ordno FROM ".GD_ORDER_ITEM." AS _oi WHERE _oi.maker like '%{$es_sgword}%' AND _oi.ordno = o.ordno) "; break;
				case 'goodsno': $arWhere[] = "oi.goodsno like '%{$es_sgword}%'"; break;
				case 'purchase': $arWhere[] = "pch.comnm like '%{$es_sgword}%'"; $join_GD_PURCHASE = 'INNER JOIN '.GD_PURCHASE_GOODS.' AS pchg ON pchg.goodsno = oi.goodsno INNER JOIN '.GD_PURCHASE.' AS pch ON pchg.pchsno = pch.pchsno'; break;
			}
		}

	#7. 전자보증보험
		if($_GET['flg_egg']) {
			$arWhere[] = $db->_query_print('o.flg_egg = [s]',$_GET['flg_egg']);
		}

	#8. 결제시 적용
		$tmp_arWhere = array();

		if($_GET['flg_escrow']) {
			$tmp_arWhere[] = $db->_query_print('o.flg_escrow = [s]',$_GET['flg_escrow']);
		}

		if($_GET['flg_cashreceipt']) {
			$tmp_arWhere[] = 'o.flg_cashreceipt != ""';
		}
		if($_GET['flg_coupon']) {
			$tmp_arWhere[] = 'co.ordno is not null';
			$join_GD_COUPON_ORDER='left join '.GD_COUPON_ORDER.' as co on o.ordno=co.ordno';
		}
		else {
			$join_GD_COUPON_ORDER='';
		}

		if($_GET['flg_aboutcoupon']=='1') {
			$tmp_arWhere[] = 'o.flg_aboutcoupon = "Y"';
		}

		if($_GET['pay_method_p']=='1') {
			$tmp_arWhere[] = 'o.pay_method= "p"';
		}

		if($_GET['flg_cashbag']=='Y') {
			$tmp_arWhere[] = 'o.flg_cashbag = "Y"';
		}

		if (sizeof($tmp_arWhere) > 0) {
			$arWhere[] = '('.implode(' OR ',$tmp_arWhere).')';
			unset($tmp_arWhere);
		}

	#9. 홍보채널
		if(count($_GET['chk_inflow'])) {
			$es_inflow = array();
			foreach($_GET['chk_inflow'] as $v) {
				if($v == 'naver_price') {
					$es_inflow[] = '"naver_elec"';
					$es_inflow[] = '"naver_bea"';
					$es_inflow[] = '"naver_milk"';
				}
				else {
					$es_inflow[] = '"'.$db->_escape($v).'"';
				}
			}
			$arWhere[] = 'o.flg_inflow in ('.implode(',',$es_inflow).')';
		}

	#10. 접수유형 (별도 필드가 없고, inflow 값이 sugi 인 레코드)
		if($_GET['ord_type'] == 'offline') {
			$arWhere[] = 'o.flg_inflow = \'sugi\'';
		}
		else if ($_GET['ord_type'] == 'online') {
			$arWhere[] = 'o.flg_inflow <> \'sugi\'';
		}

	#xx. 페이징 query 생성
		$_paging_query = http_build_query($_GET);	// php5 전용함수. but! lib.func.php 안에 php4용 있음.

	#XX. where 절 합침
		if(!empty($arWhere)) {
			$strWhere = 'where '.implode(' and ',$arWhere);
		}

// 쿼리 실행
$orderList=array();
$orderGroupNameMap=array();

	$query = '
		SELECT
			o.*,
			m.m_id,
			m.m_no,
			m.level,
			m.dormant_regDate as dormant_regDate,
			oi.goodsnm,
			oi.goodsno,
			COUNT(oi.channel) AS goodscnt
		FROM

			'.GD_INTEGRATE_ORDER.' as o
			LEFT JOIN '.GD_INTEGRATE_ORDER_ITEM.' as oi
			ON o.ordno = oi.ordno and o.channel = oi.channel
			LEFT JOIN '.GD_MEMBER.' as m
			ON o.m_no = m.m_no
			'.$join_GD_COUPON_ORDER.'
			'.$join_GD_PURCHASE.'

		'.$strWhere.'

		GROUP BY o.ordno
	';

	if($_GET['mode']=='group') {
		$result = $db->_select($query);

		// 그룹별로 주문서 할당
		foreach($result as $v) {
			$orderGroupKey = $v['ord_status'] > -1 ? $v['ord_status'] : 9998;
			$orderGroupNameMap[$orderGroupKey] = integrate_order::getOrderStatus($orderGroupKey);

			$orderList[$orderGroupKey][] = $v;
		}
		ksort($orderList);

		// 정렬
		foreach($orderList as $orderGroupKey=>$eachOrderGroup) {

			$sortAssistDyn=$sortAssistOrdno=array();
			foreach ($eachOrderGroup as $k => $v) {
				$sortAssistDyn[$k]  = $v['dyn'];
				$sortAssistOrdno[$k] = $v['ordno'];
			}
			array_multisort($sortAssistDyn,SORT_ASC,$sortAssistOrdno,SORT_DESC,$orderList[$orderGroupKey]);

			$i=0;
			foreach ($eachOrderGroup as $k => $v) {
				$orderList[$orderGroupKey][$k]['_rno'] = count($eachOrderGroup)-($i++);
			}
		}
	}
	else {
		if(!$cfg['orderPageNum']) $cfg['orderPageNum'] = 20;

		$query = $query.' order by '.$_GET['sort'];
		$result = $db->_select_page($_GET['page_num'],$_GET['page'],$query);

		$orderList[9999]=array();
		foreach($result['record'] as $v) {
			$orderList[9999][] = $v;
		}
		$pageNavi = $result['page'];
	}

	### 그룹명 가져오기
	$r_grp = array();
	$garr = member_grp();
	foreach( $garr as $v ) $r_grp[$v['level']] = $v['grpnm'];
?>

<script type="text/javascript" src="./integrate_order_common.js"></script>

<div class="title title_top" style="position:relative;padding-bottom:15px">주문통합 리스트 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=order&no=24')"><img src="../img/btn_q.gif" border="0" hspace="2" align="absmiddle"/></a></div>
<?

?>
<form name="frmSearch" id="frmSearch" method="get" action="">
	<input type="hidden" name="mode" value="<?=$_GET['mode']?>"/>	<!-- 주문일 or 주문처리흐름 -->

	<table class="tb">
	<col class="cellC"><col class="cellL" style="width:250px">
	<col class="cellC"><col class="cellL">
	<tr>
		<td><span class="small1">판매채널</span></td>
		<td colspan="3" class="noline">
			<label><input type="checkbox" name="channel[all]"		value="1"	onClick="nsGodoFormHelper.magic_check(this);" <?=($_GET['channel']['all'] ? 'checked' : '' )?>/>전체</label>
			<? foreach ($integrate_cfg['channels'] as $k => $v) {
				if($k == 'selly') continue;
				?>
			<label><input type="checkbox" name="channel[<?=$k?>]"	value="1"	onClick="nsGodoFormHelper.magic_check(this);" <?=($_GET['channel'][$k] ? 'checked' : '' )?>/><?=$v?> <img src="../img/icon_int_order_<?=$k?>.gif" align="absmiddle"/></label>
			<? } ?>
		</td>
	</tr>
	<tr>
		<td><span class="small1">셀리(마켓)</span></td>
		<td colspan="3" class="noline">
			<?  if(is_array($selly_mall_cd) && !empty($selly_mall_cd)) { ?>
			<label><input type="checkbox" name="sub_channel[all]"		value="1"	onClick="nsGodoFormHelper.magic_check(this);" <?=($_GET['sub_channel']['all'] ? 'checked' : '' )?>/>전체 <img src="../img/icon_int_order_selly.gif" align="absmiddle"/></label>
			<?
			foreach ($selly_mall_cd as $k => $v) {
				if($k == 'mall0005') continue;
				?>
			<label><input type="checkbox" name="sub_channel[<?=$k?>]"	value="1"	onClick="nsGodoFormHelper.magic_check(this);" <?=($_GET['sub_channel'][$k] ? 'checked' : '' )?>/><?=$v?> </label>
			<? } ?>
			<div><span class="extext">셀리(마켓) 주문내역은 e나무에 등록되어 있지 않은 상품의 주문정보도 함께 보여 질 수 있습니다.</span></div>
			<? } else { ?>
				<a href="../selly/setting.php"><span class="extext">셀리(마켓) 주문내역을 통합해서 보시려면 셀리 서비스 신청 후 상점인증 해주시기 바랍니다.</span></a>
			<? } ?>

		</td>
	</tr>
	<tr>
		<td><span class="small1">주문검색</span></td>
		<td colspan="3">
			<select name="ord_status">
				<option value="-1"> = 주문상태 = </option>
				<? foreach ($integrate_cfg['step'] as $k=>$v) { ?>
				<option value="<?=$k?>" <?=$_GET['ord_status'] == $k ? 'selected' : ''?>><?=$v?></option>
				<? } ?>
			</select>

			<select name="pay_method">
				<option value=""> = 결제수단 = </option>
				<? foreach ($integrate_cfg['pay_method'] as $k=>$v) { ?>
				<? if ($k == 'p') continue; ?>
				<option value="<?=$k?>" <?=$_GET['pay_method'] == $k ? 'selected' : ''?>><?=$v?></option>
				<? } ?>
			</select>

			<select name="ord_type">
				<option value=""> = 접수유형 = </option>
				<option value="online" <?=$_GET['ord_type'] == 'online' ? 'selected' : ''?>>온라인접수</option>
				<option value="offline" <?=$_GET['ord_type'] == 'offline' ? 'selected' : ''?>>수기접수</option>
			</select>

			<select name="skey">
				<option value="all"> = 통합검색 = </option>
				<option value="ordno"			<?=($_GET['skey'] == 'ordno') ? 'selected' : ''?>			>주문번호</option>
				<option value="ord_name"		<?=($_GET['skey'] == 'ord_name') ? 'selected' : ''?>		>주문자명</option>
				<option value="m_id"			<?=($_GET['skey'] == 'm_id') ? 'selected' : ''?>			>주문자ID</option>
				<option value="ord_phone"			<?=($_GET['skey'] == 'ord_phone') ? 'selected' : ''?>			>주문자연락처</option>
				<option value="pay_bank_name"		<?=($_GET['skey'] == 'pay_bank_name') ? 'selected' : ''?>	>입금자명</option>
				<option value="rcv_name"	<?=($_GET['skey'] == 'rcv_name') ? 'selected' : ''?>	>수령자명</option>
				<option value="rcv_phone"	<?=($_GET['skey'] == 'rcv_phone') ? 'selected' : ''?>	>수령자연락처</option>
				<option value="rcv_address"	<?=($_GET['skey'] == 'rcv_address') ? 'selected' : ''?>	>배송지주소</option>
				<option value="dlv_no"	<?=($_GET['skey'] == 'dlv_no') ? 'selected' : ''?>	>송장번호</option>
			</select>

			<input type="text" name="sword" value="<?=htmlspecialchars($_GET['sword'])?>" class="line" />

		</td>
	</tr>
	<tr>
		<td><span class="small1">처리일자</span></td>
		<td colspan="3">

			<select name="dtkind">
				<option value="ord_date"		<?=($_GET['dtkind'] == 'ord_date' ? 'selected' : '')?>		>주문일</option>
				<option value="pay_date"			<?=($_GET['dtkind'] == 'pay_date' ? 'selected' : '')?>			>입금일</option>
				<option value="dlv_date"			<?=($_GET['dtkind'] == 'dlv_date' ? 'selected' : '')?>			>배송일</option>
				<option value="fin_date"	<?=($_GET['dtkind'] == 'fin_date' ? 'selected' : '')?>	>배송완료일</option>
			</select>

			<input type="text" name="regdt[]" value="<?=$_GET['regdt'][0]?>" onclick="calendar(event)" size="12" class="line"/>

			<select name="regdt_time[]">
			<option value="-1">---</option>
			<? for ($i=0;$i<24;$i++) {?>
			<option value="<?=$i?>" <?=($_GET['regdt_time'][0] === $i ? 'selected' : '')?>><?=sprintf('%02d',$i)?>시</option>
			<? } ?>
			</select>
			-
			<input type="text" name="regdt[]" value="<?=$_GET['regdt'][1]?>" onclick="calendar(event)" size="12" class="line"/>
			<select name="regdt_time[]">
			<option value="-1">---</option>
			<? for ($i=0;$i<24;$i++) {?>
			<option value="<?=$i?>" <?=($_GET['regdt_time'][1] === $i ? 'selected' : '')?>><?=sprintf('%02d',$i)?>시</option>
			<? } ?>
			</select>

			<a href="javascript:setDate('regdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>);"><img src="../img/sicon_today.gif" align="absmiddle"/></a>
			<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align="absmiddle"/></a>
			<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align="absmiddle"/></a>
			<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align="absmiddle"/></a>
			<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align="absmiddle"/></a>
			<a href="javascript:setDate('regdt[]')"><img src="../img/sicon_all.gif" align="absmiddle"/></a>

		</td>
	</tr>
	<tr class="blindable">
		<td><span class="small1">상품검색</span></td>
		<td>
			<select name="sgkey">
				<option value="goodsnm"	<?=($_GET['sgkey'] == 'goodsnm') ? 'selected' : ''?>	>상품명</option>
				<option value="goodsno"	<?=($_GET['sgkey'] == 'goodsno') ? 'selected' : ''?>	>상품번호</option>
				<option value="brandnm"	<?=($_GET['sgkey'] == 'brandnm') ? 'selected' : ''?>	>브랜드</option>
				<option value="maker"	<?=($_GET['sgkey'] == 'maker') ? 'selected' : ''?>		>제조사</option>
				<option value="purchase"	<?=($_GET['sgkey'] == 'purchase') ? 'selected' : ''?>	>사입처(공급처)</option>
			</select>
			<input type=text name="sgword" value="<?=htmlspecialchars($_GET['sgword'])?>" class="line"/>
		</td>
		<td><span class="small1">전자보증보험</span> <a href="../basic/egg.intro.php"><img src="../img/btn_question.gif"/></a></td>
		<td class="noline">
			<select name="flg_egg">
				<option value=""	<?=($_GET['flg_egg'] == '') ? 'selected' : ''?>	>전체</option>
				<option value="n"	<?=($_GET['flg_egg'] == 'n') ? 'selected' : ''?>>미발급</option>
				<option value="f"	<?=($_GET['flg_egg'] == 'f') ? 'selected' : ''?>>발급실패</option>
				<option value="y"	<?=($_GET['flg_egg'] == 'y') ? 'selected' : ''?>>발급완료</option>
			</select>
		</td>
	</tr>
	<tr class="blindable">
		<td><span class="small1">결제시적용</span></td>
		<td colspan="3" class="noline">
			<input type="checkbox" name="flg_escrow" value="y" <?=frmChecked('y',$_GET['flg_escrow'])?>>에스크로 <img src="../img/btn_escrow.gif" align="absmiddle"/></input>
			<input type="checkbox" name="flg_cashreceipt" value="1" <?=frmChecked('1',$_GET['flg_cashreceipt'])?>>현금영수증 <img src="../img/icon_cash_receipt.gif"/></input>
			<input type="checkbox" name="flg_coupon" value="1" <?=frmChecked('1',$_GET['flg_coupon'])?>>쿠폰사용</input>
			<input type="checkbox" name="flg_aboutcoupon" value="1" <?=frmChecked('1',$_GET['flg_aboutcoupon'])?>>어바웃쿠폰</input>
			<input type="checkbox" name="pay_method_p" value="1" <?=frmChecked('1',$_GET['pay_method_p'])?>>적립금(포인트)</input>
			<input type="checkbox" name="flg_cashbag" value="Y" <?=frmChecked('Y',$_GET['flg_cashbag'])?>><img src="../img/icon_okcashbag.gif" align="absmiddle"/>OK캐시백적립</input>
		</td>
	</tr>
	<tr class="blindable">
		<td><span class="small1">홍보채널<br>(유입경로)</span></td>
		<td colspan="3" class="noline">
			<? foreach ($integrate_cfg['inflows'] as $k=>$v) { ?>
			<label class="small1"><input type="checkbox" name="chk_inflow[]" value="<?=$k?>" <?=(in_array($k,$_GET['chk_inflow']) ? 'checked' : '')?> /><img src="../img/inflow_<?=$k?>.gif" align="absmiddle"/> <?=$v?></label>
			<? } ?>
		</td>
	</tr>
	</table>

	<div class="button_top">
	<table width="100%">
	<tr>
		<td width="35%" align="left">&nbsp;</td>
		<td width="30%" align="center"><input type="image" src="../img/btn_search2.gif"/></td>
		<td width="35%" align="right">

		<a href="javascript:void(0);" onClick="nsGodoFormHelper.save();"><img src="../img/btn_search_form_save.gif"></a>
		<a href="javascript:void(0);" onClick="nsGodoFormHelper.reset();"><img src="../img/btn_search_form_reset.gif"></a>
		<a href="javascript:void(0);" onClick="nsGodoFormHelper.toggle();"><img src="../img/btn_search_form_toggle_open.gif" id="el-godo-form-helper-toggle-btn"></a>
		</td>
	</tr>
	</table>
	</div>

	<div style="padding-top:15px"></div>

	<table width="100%" cellpadding="0" cellspacing="0">
	<tr>
		<td align="left">

		<a href="?<?=$_paging_query?>&mode=date"><img src="../img/btn_orderdate<?=($_GET['mode'] == 'date') ? '_on' : '_off' ?>.gif"></a>
		<a href="?<?=$_paging_query?>&mode=group"><img src="../img/btn_orderprocess<?=($_GET['mode'] == 'group') ? '_on' : '_off'?>.gif"></a>

		</td>
		<td align="right">
		<? if ($_GET['mode']!="group") { ?>
		<select name="sort" onchange="this.form.submit();">
			<option value="o.ord_date desc" <?=$_GET['sort'] == 'o.ord_date desc' ? 'selected' : '' ?>>주문일순↑</option>
			<option value="o.ord_date asc" <?=$_GET['sort'] == 'o.ord_date asc' ? 'selected' : '' ?>>주문일순↓</option>

			<option value="o.pay_date desc" <?=$_GET['sort'] == 'o.pay_date desc' ? 'selected' : '' ?>>입금일순↑</option>
			<option value="o.pay_date asc" <?=$_GET['sort'] == 'o.pay_date asc' ? 'selected' : '' ?>>입금일순↓</option>

			<option value="o.pay_amount desc" <?=$_GET['sort'] == 'o.pay_amount desc' ? 'selected' : '' ?>>결제액순↑</option>
			<option value="o.pay_amount asc" <?=$_GET['sort'] == 'o.pay_amount asc' ? 'selected' : '' ?>>결제액순↓</option>
		</select>&nbsp;

		<select name="page_num" onchange="this.form.submit();">
			<?
			$r_pagenum = array(10,20,40,60,100);
			if ((int)$cfg['orderPageNum'] > 0 && !in_array((int)$cfg['orderPageNum'] ,$r_pagenum)) {
				$r_pagenum[] = (int)$cfg['orderPageNum'];
				sort($r_pagenum);
			}
			foreach ($r_pagenum as $v){
			?>
			<option value="<?=$v?>" <?=$_GET['page_num'] == $v ? 'selected' : ''?>><?=$v?>개 출력</option>
			<? } ?>
		</select>
		<? } ?>
		</td>
	</tr>
	</table>
</form>

<form name="frmList" method="post" action="indb.php" id="frmList">
	<input type="hidden" name="mode" value="integrate_multi_action"/>

	<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<col width="25"><col width="35"><col width="100"><col width="160"><col><col width="50"><col width="90"><col width="60"><col width="50"><col width="60"><col width="55">
	<tr><td class="rnd" colspan="20"></td></tr>
	<tr class="rndbg">
		<th><a href="javascript:void(0)" onClick="chkBoxAll()" class=white>선택</a></th>
		<th>번호</th>
		<th>주문일시</th>
		<th colspan="2">주문번호 (주문상품)</th>
		<th>홍보채널</th>
		<th>주문자</th>
		<th>받는분</th>
		<th>결제수단</th>
		<th>금액</th>
		<th>처리상태</th>
	</tr>
	<tr><td class="rnd" colspan="20"></td></tr>
	<?
	$totalPrnSettlePrice=0;
	foreach($orderList as $orderGroupKey => $eachOrderGroup) {
		$groupPrnSettlePrice=0;

		if($orderGroupKey!=9999) {
	?>
	<tr><td colspan="13" bgcolor="#E8E7E7" height="1"></td></tr>
	<tr align="center">
		<td colspan="13" bgcolor="#f7f7f7" height="30" style="padding-left:15px">
		<b><img src="../img/icon_process.gif" align="absmiddle"/>
			<?=$orderGroupNameMap[$orderGroupKey]?>
		</b>
		</td>
	</tr>
	<?
		}

		foreach($eachOrderGroup as $eachOrder) {

			if($eachOrder['goodscnt']>1) $goodsnm = $eachOrder['goodsnm'].' 외'.($eachOrder['goodscnt']-1).'건';
			else $goodsnm = $eachOrder['goodsnm'];

			$groupPrnSettlePrice+=$eachOrder['pay_amount'];

			// 강조색, 선택 버튼 비활성화
			if ($eachOrder['ord_status'] >= 10 OR ($eachOrder['channel'] != 'enamoo' AND $eachOrder['ord_status'] > 2)) {
				$disabled = 'disabled';
				$bgcolor = '#F0F4FF';
			}
			else {
				$disabled = '';
				$bgcolor = '#ffffff';
			}

			$ord_name = "<span class='small1' style='color:#0074BA'><strong>".$eachOrder['ord_name']."</strong><br />";
			if($eachOrder['m_id']){
				if($eachOrder['dormant_regDate'] == '0000-00-00 00:00:00'){
					$ord_name = "<span id='navig' name='navig' m_id='".$eachOrder['m_id']."' m_no='".$eachOrder['m_no']."'>".$ord_name."(".$eachOrder['m_id']." / ".$r_grp[$eachOrder['level']].")</span></span>";
				}
				else {
					$ord_name = $ord_name."(".$eachOrder['m_id']." / 휴면회원)</span>";
				}
			}
			else {
				$ord_name = $ord_name."(비회원)</span>";
			}
	?>
	<tr height="25" bgcolor="<?=$bgcolor?>" bg="<?=$bgcolor?>" align="center">
		<td class="noline">
		<input type="checkbox" name="chk[<?=$eachOrder['channel']?>][]" value="<?=$eachOrder['ordno']?>" class="chk_ordno_<?=$orderGroupKey?> chk_ordno" onclick="iciSelect(this)" <?=$disabled?>/>
		</td>
		<td><span class="ver8" style="color:#616161"><?=$eachOrder['_rno']?></span></td>
		<td><span class="ver81" style="color:#616161"><?=substr($eachOrder['ord_date'],0,-3)?></span></td>
		<td align="left">

		<a href="view.php?ordno=<?=$eachOrder['ordno']?>"><span class="ver81" style="color:#<?=$eachOrder['flg_inflow'] == 'sugi' ? 'ED6C0A' : '0074BA'?>"><b><?=$eachOrder['ordno']?><?=$eachOrder['flg_inflow'] == 'sugi' ? '<span class="small1">(수기)</span>' : ''?></b></span></a>
		<a href="javascript:popup('popup.order.php?ordno=<?=$eachOrder['ordno']?>',800,600)"><img src="../img/btn_newwindow.gif" border=0 align="absmiddle"/></a>

		</td>
		<td align="left">
			<div>
			<?=($eachOrder['channel'] != 'enamoo') ? '<img src="../img/icon_int_order_'.$eachOrder['channel'].'.gif" align="absmiddle">' : ''?>
			<? if (!empty($eachOrder['old_ordno'])){ ?><a href="javascript:popup('popup.order.php?ordno=<?=$eachOrder['ordno']?>',800,600)"><img src="../img/icon_twice_order.gif"/></a><? } ?>
			<? if ($eachOrder['flg_escrow']=="y"){ ?><a href="javascript:popup('popup.order.php?ordno=<?=$eachOrder['ordno']?>',800,600)"><img src="../img/btn_escrow.gif"/></a><? } ?>
			<? if ($eachOrder['flg_egg']=="y"){ ?><a href="javascript:popup('popup.order.php?ordno=<?=$eachOrder['ordno']?>',800,600)"><img src="../img/icon_guar_order.gif"/></a><? } ?>
			<? if ($eachOrder['flg_cashreceipt']!=""){ ?><img src="../img/icon_cash_receipt.gif"/><? } ?>
			<? if ($eachOrder['flg_cashbag']=="Y"){ ?><a href="javascript:popup('popup.order.php?ordno=<?=$eachOrder['ordno']?>',800,600)"><img src="../img/icon_okcashbag.gif" align="absmiddle"/></a><? } ?>

			<span class="small1" style="color:#444444"><?=$goodsnm?></span>
			</div>
		</td>
		<td><? if ($eachOrder['flg_inflow']!="" && $eachOrder['flg_inflow']!="sugi"){ ?><a href="javascript:popup('popup.order.php?ordno=<?=$eachOrder['ordno']?>',800,600)"><img src="../img/inflow_<?=$eachOrder['flg_inflow']?>.gif" align="absmiddle" alt="<?=$integrate_cfg['inflows'][$eachOrder['flg_inflow']]?>" /></a><? } ?></td>
		<td><?php echo $ord_name; ?></td>
		<td><span class="small1" style="color:#444444;"><?=$eachOrder['rcv_name']?></span></td>
		<td class="small4">
		<?=settleIcon($eachOrder['pg']);?> <?=isset($integrate_cfg['pay_method'][$eachOrder['pay_method']]) ? $integrate_cfg['pay_method'][$eachOrder['pay_method']] : '-'?>
		</td>
		<td class="ver81"><b><?=number_format($eachOrder['pay_amount'])?></b></td>
		<td class="small4" width="60">
		<? if($eachOrder['dlv_no'] || $eachOrder['count_dv_item']) { ?>
			<a href="javascript:void(0);" onClick="fnDeliveryTrace('<?=$eachOrder['channel']?>','<?=$eachOrder['dlv_company']?>','<?=$eachOrder['dlv_no']?>');" style="color:#0074BA"><?=integrate_order::getOrderStatus($eachOrder['ord_status'])?></a>
		<? } else { ?>
		<?=integrate_order::getOrderStatus($eachOrder['ord_status'])?>
		<? } ?>
		</td>
	</tr>
	<tr><td colspan="20" bgcolor="#E4E4E4"></td></tr>

	<?
	}
		$totalPrnSettlePrice+=$groupPrnSettlePrice;
	?>
	<tr>
		<td><a href="javascript:chkBoxGroup('<?=$orderGroupKey?>')"><img src="../img/btn_allchoice.gif" border="0"/></a></td>
		<td height="30" colspan="9" align="right" style="padding-right:8px">합계: <span class="ver9"><b><?=number_format($groupPrnSettlePrice)?></span>원</b></td>
		<td colspan="3"></td>
	</tr>
	<tr><td colspan="13" height="15"></td></tr>
	<?
	}
	?>
	<tr bgcolor="#f7f7f7" height="30">
		<td colspan="10" align="right" style="padding-right:8px">전체합계 : <span class="ver9"><b><?=number_format($totalPrnSettlePrice)?>원</b></span></td>
		<td colspan="3"></td>
	</tr>
	<tr><td height="4"></td></tr>
	<tr><td colspan="12" class="rndline"></td></tr>
	</table>

	<? if($_GET['mode']!='group') { ?>
		<div align="center" class="pageNavi ver8" style="font-weight:bold">
			<? if($pageNavi['prev']) { ?>
				<a href="?<?=getvalue_chg('page',$pageNavi['prev'])?>">◀ </a>
			<? } ?>
			<? foreach($pageNavi['page'] as $v) { ?>
				<? if($v==$pageNavi['nowpage']) { ?>
					<a href="?<?=getvalue_chg('page',$v)?>"><?=$v?></a>
				<? } else { ?>
					<a href="?<?=getvalue_chg('page',$v)?>">[<?=$v?>]</a>
				<? } ?>
			<? } ?>
			<? if($pageNavi['next']) { ?>
				<a href="?<?=getvalue_chg('page',$pageNavi['next'])?>">▶</a>
			<? } ?>
		</div>
	<? } ?>

	<div>
		선택한 주문건을
		<select name="ord_status" required label="수정사항">
		<option value="">- 주문상태 변경처리 -</option>
		<option value="0">주문접수 처리</option>
		<option value="1">입금확인 처리</option>
		<option value="2">배송준비중 처리</option>
		<option value="3">배송중 처리</option>
		<option value="4">배송완료 처리</option>
		</select> 합니다.
	</div>

	<div class="button">
	<a href="javascript:void(0);" onClick="fnSetOrder(document.frmList.ord_status.value)"><img src="../img/btn_modify.gif"/></a>
	<a href="javascript:void(0);" onClick="history.back()"><img src="../img/btn_cancel.gif"/></a>
	</div>

</form>

<form name="frmDnXls" method="post">
<input type="hidden" name="mode"/>
<input type="hidden" name="search" value="<? echo htmlspecialchars(serialize($_GET));?>"/>
</form>

<!-- 주문내역 프린트&다운로드 : Start -->
<table width="100%" border="0" cellpadding="10" cellspacing="0" style="border:1px #dddddd solid;">
<tr>
	<td width="50%" align="center" bgcolor="#f6f6f6" style="font:16pt tahoma;"><img src="../img/icon_down.gif" border="0" align="absmiddle"/><b>download</b></td>
	<td width="50%" align="center" bgcolor="#f6f6f6" style="font:16pt tahoma;border-left:1px #dddddd solid;"><img src="../img/icon_down.gif" border="0" align="absmiddle"/><b>print</b></td>
</tr>
<tr>
	<td align="center">
	<table border="0" cellpadding="4" cellpadding="0" border="0">
	<tr align="center">
	<td><a href="javascript:fnExcelDownload('order')"><img src="../img/btn_order_data_order.gif" border="0"/></a></td>
	<td><a href="javascript:fnExcelDownload('goods')"><img src="../img/btn_order_data_goods.gif" border="0"/></a></td>
	</tr>
	<tr align="center">
	<td><a href="javascript:popupLayer('../data/popup.orderxls.php?mode=orderXls',550,700)"><img src="../img/btn_order_data_order_ot.gif" border="0"/></a></td>
	<td><a href="javascript:popupLayer('../data/popup.orderxls.php?mode=orderGoodsXls',550,700)"><img src="../img/btn_order_data_goods_ot.gif" border="0"/></a></td>
	</tr>
	</table>
	</td>
	<td align="center" style="border-left:1px #dddddd solid;">
	<form method="get" name="frmPrint">
	<input type="hidden" name="ordnos"/>

	<table border="0" cellpadding="4" cellpadding="0" border="0">
	<tr align="center">
	<td><select NAME="type">
	<option value="report">주문내역서</option>
	<option value="reception">간이영수증</option>
	<option value="tax">세금계산서</option>
	<option value="particular">거래명세서</option>
	</select></td>
	</tr>
	<tr>
	<td align="center"><strong class=noline><label for="r1"><input class="no_line" type="radio" name="list_type" value="list" id="r1" onclick="openLayer('psrch','none')" checked>목록선택</input></label>&nbsp;&nbsp;&nbsp;<label for="r2"><input class="no_line" type="radio" name="list_type" value="term" id="r2" onclick="openLayer('psrch','block')">기간선택</input></label></strong></td>
	</tr>
	<tr>
	<td align="cemter"><div style="float:left; display:none;" id="psrch">
	<input type="text" name="regdt[]" onclick="calendar(event)" size="12" class="line"/> -
	<input type="text" name="regdt[]" onclick="calendar(event)" size="12" class="line"/>
	<select name="settlekind">
	<option value=""> - 결제방법 - </option>
	<? foreach ( $r_settlekind as $k => $v ) echo "<option value=\"{$k}\">{$v}</option>"; ?>
	</select>
	<select name="step">
	<option value=""> - 단계선택 - </option>
	<? foreach ( $r_step as $k => $v ) echo "<option value=\"step_{$k}\">{$v}</option>"; ?>
	<option value="step2_1">주문취소</option>
	<option value="step2_2">환불관련</option>
	<option value="step2_3">반품관련</option>
	<option value="step2_50">결제시도</option>
	<option value="step2_54">결제실패</option>
	</select>
	</div></td>
	</tr>
	<tr>
	<td align="center"><a href="javascript:fnOrderPrint('frmPrint', 'frmList');" style="padding-top:20px"><img src="../img/btn_print.gif" border="0" align="absmiddle"/></a></td>
	</tr>
	</table>
	</form>
	</td>
</tr>
</table>
<!-- 주문내역 프린트 : End -->

<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle"/>주문일 또는 주문처리흐름 방식으로 주문내역을 정렬하실 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"/>주문상태를 변경하시려면 주문건 선택 - 처리단계선택 후 수정버튼을 누르세요.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"/>주문상태변경을 통해 각 주문처리단계 (주문접수, 입금확인, 배송준비, 배송중, 배송완료) 로 빠르게  처리하실 수 있습니다.</td></tr>

<tr><td height="8"></td></tr>
<tr><td><span class="def1"><b>- 카드결제주문은 아래와 같은 경우가 발생할 수 있습니다. (필독하세요!) -</span></td></tr>

<tr><td><img src="../img/icon_list.gif" align="absmiddle"/>해당 PG사 관리자모드에는 승인이 되었으나, 주문리스트에서 주문상태가 '입금확인'이 아닌 '결제시도'로 되어 있는 경우가 발생될 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"/>이는 중간에 통신상의 문제로 리턴값을 제대로 받지 못해 주문상태가 변경이 되지 않은 것입니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"/>즉, 이와같이 승인이 되었지만 주문상태가 '결제시도'인 경우 해당주문건의 주문상세내역 페이지에서 "결제시도, 실패 복원" 처리를 하시면 주문처리상태가 "입금확인"으로 수정됩니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"/>그러나 정상적인 리턴값을 받아 주문처리상태가 변경된 건이기에 이에 대해서는 정확한 결제로그를 주문상세내역페이지에서 확인을 할 수 없습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"/>또한 고객이 카드결제로 주문을 1건 결제했는데 간혹 PG사 쪽에서는 2건이 승인(중복승인)되는 경우가 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"/>이 경우는 해당 PG사의 관리자모드로 가서 중복승인된 2건중에 1건을 승인취소 해주시면 됩니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"/>중복승인건을 체크해서 바로 승인취소처리하지 않으면 미수금이 발생되어 쌓이게 되고, 해당 PG사로부터 거래중지요청 등의 불이익을 받을 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"/>결제승인건의 주문상태와 중복승인건 처리는 세심하게 체크해야 하며 이에 대한 책임은 쇼핑몰 운영자에게 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"/>항상 카드결제건은 이곳 주문리스트와 PG사에서 제공하는 관리페이지의 결제승인건과 비교하면서 주의깊게 체크하여 처리하시기 바랍니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<?
include "_deliveryForm.php"; //송장일괄입력폼
?>

<script>window.onload = function(){ UNM.inner();};</script>
<? @include dirname(__FILE__) . "/../interpark/_order_list.php"; // 인터파크_인클루드 ?>

<? include "../_footer.php"; ?>
