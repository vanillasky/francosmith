<?
$location = "주문관리 > 주문취소 관리 > 취소 리스트";
include "../_header.php";
@include "../../conf/config.pay.php";
include "../../lib/page.class.php";
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
	$today = mktime(0,0,0,date('m',$now), date('d',$now), date('Y',$now));

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
	$_GET['mode']			= !empty($_GET['mode']) ? $_GET['mode'] : '';					// 뷰 형식
	$_GET['channel']		= !empty($_GET['channel']) ? $_GET['channel'] : array();			// 판매채널
	$_GET['ord_status']		= !empty($_GET['ord_status']) ? $_GET['ord_status'] : array();	// 처리상태
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
	$_GET['pay_method_p']	= !empty($_GET['pay_method_p']) ? $_GET['pay_method_p'] : '';	// 적립금(포인트)
	$_GET['flg_cashbag']			= !empty($_GET['flg_cashbag']) ? $_GET['flg_cashbag'] : '';					// ok캐시백 적립

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
					$_tmp[] = " o.pg = '$k' ";
				}
				else {
					$_tmp[] = " o.channel = '$k' ";
				}
			}
			if (sizeof($_tmp) > 0) $arWhere[] = '('.implode(' OR ',$_tmp).')';
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
		// 취소 관련 고정
		if ($_GET['ord_status'] == 10) {
			$arWhere[] = "((o.ord_status= '".$_GET['ord_status']."') OR (oi.cs = 'y' AND o.ord_status = 0))";
		}
		elseif ($_GET['ord_status'] == 11) {
			$arWhere[] = "((o.ord_status= '".$_GET['ord_status']."') OR (oi.cs = 'f' AND o.ord_status = 0))";
		}
		else {
			$arWhere[] = "(o.ord_status= '10' OR o.ord_status= '11' OR (oi.cs <> 'n' AND o.ord_status = 0))";
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

					// 처리담당자 추가
					$integrate_cfg['skey'][] = array(
						'field'=>'a.name',
						'condition'=>'like',
						'pattern'=>'/.{4,}/',
					);

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
				case 'ord_phone': $arWhere[] = "o.ord_phone like '%{$es_sword}%'"; break;
				case 'rcv_phone': $arWhere[] = "o.rcv_phone like '%{$es_sword}%'"; break;
				case 'rcv_address': $arWhere[] = "o.rcv_address like '%{$es_sword}%'"; break;
				case 'dlv_no': $arWhere[] = "o.dlv_no like '%{$es_sword}%'"; break;
				case 'name': $arWhere[] = "a.name like '%{$es_sword}%'"; break;
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

			//$arWhere[] = $db->_query_print('o.ord_date between [s] and [s]',$tmp_start,$tmp_end);

			switch($_GET['dtkind']) {
				case 'ord_date': $arWhere[] = $db->_query_print('o.ord_date between [s] and [s]',$tmp_start,$tmp_end); break;
				case 'cs_regdt': $arWhere[] = $db->_query_print('o.cs_regdt between [s] and [s]',$tmp_start,$tmp_end); break;
				//case 'dlv_date': $arWhere[] = $db->_query_print('o.dlv_date between [s] and [s]',$tmp_start,$tmp_end); break;
				//case 'fin_date': $arWhere[] = $db->_query_print('o.fin_date between [s] and [s]',$tmp_start,$tmp_end); break;
			}
		}

	#6. 상품검색
		$join_GD_PURCHASE = '';
		if($_GET['sgword'] && $_GET['sgkey']) {
			$es_sgword = $db->_escape($_GET['sgword']);
			switch($_GET['sgkey']) {
				case 'goodsnm': $arWhere[] = "oi.goodsnm like '%{$es_sgword}%'"; break;
				//case 'brandnm': $arWhere[] = "EXISTS (SELECT ordno FROM ".GD_ORDER_ITEM." AS _oi WHERE _oi.brandnm like '%{$es_sgword}%' AND _oi.ordno = o.ordno) "; break;
				case 'brandnm': $arWhere[] = "EXISTS (SELECT ordno FROM ".GD_ORDER_ITEM." AS _oi WHERE _oi.brandnm like '%{$es_sgword}%') "; break;
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

// 쿼리 실행
	$db_table = "".GD_INTEGRATE_ORDER." AS o
			INNER JOIN ".GD_INTEGRATE_ORDER_ITEM." as oi
			ON o.ordno = oi.ordno and o.channel = oi.channel
			left join ".GD_MEMBER." AS m
			ON o.m_no=m.m_no
			";
	$db_table .= $join_GD_PURCHASE;
	$orderby = $_GET['sort'];

	$pg = new Page($_GET['page'],$_GET['page_num']);
	$pg->cntQuery = "SELECT count(DISTINCT o.ordno) FROM ".$db_table." WHERE ".implode(' AND ',$arWhere);
	$pg->vars['page']= $_paging_query;
	$pg->field = "
			o.*,
			oi.cs,
			m.m_id,
			m.m_no,
			m.dormant_regDate,

			oi.goodsnm,
			oi.goodsno,
			COUNT(oi.channel) AS goodscnt
	";
	$pg->setQuery($db_table,$arWhere,$orderby,"group by o.ordno");
	$pg->exec();
	$res = $db->query($pg->query);
?>
<script type="text/javascript" src="./integrate_order_common.js"></script>

<div class="title title_top" style="position:relative;padding-bottom:15px">취소 리스트 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=order&no=30')"><img src="../img/btn_q.gif" border=0 hspace=2 align=absmiddle></a></div>

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

			<select>
				<option>주문취소</option>
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
				<option value="name"	<?=($_GET['skey'] == 'name') ? 'selected' : ''?>	>처리담당자</option>
			</select>

			<input type="text" name="sword" value="<?=htmlspecialchars($_GET['sword'])?>" class="line" />

		</td>
	</tr>
	<tr>
		<td><span class="small1">처리일자</span></td>
		<td colspan="3">

			<select name="dtkind">
				<option value="ord_date"		<?=($_GET['dtkind'] == 'ord_date' ? 'selected' : '')?>		>주문일</option>
				<option value="cs_regdt"		<?=($_GET['dtkind'] == 'cs_regdt' ? 'selected' : '')?>		>주문취소일</option>
				<!--option value="pay_date"			<?=($_GET['dtkind'] == 'pay_date' ? 'selected' : '')?>			>입금일</option>
				<option value="dlv_date"			<?=($_GET['dtkind'] == 'dlv_date' ? 'selected' : '')?>			>배송일</option>
				<option value="fin_date"	<?=($_GET['dtkind'] == 'fin_date' ? 'selected' : '')?>	>배송완료일</option-->
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

			<a href="javascript:setDate('regdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align="absmiddle"/></a>
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
		<a href="?<?=$_paging_query?>&ord_status="><img src="../img/btn_list_order_list<?=empty($_GET['ord_status']) ? '_on' : '' ?>.gif"></a>
		<a href="?<?=$_paging_query?>&ord_status=10"><img src="../img/btn_int_order_list_cancel_req<?=($_GET['ord_status'] == 10) ? '_on' : ''?>.gif"></a>
		<a href="?<?=$_paging_query?>&ord_status=11"><img src="../img/btn_int_order_list_cancel_fin<?=($_GET['ord_status'] == 11) ? '_on' : ''?>.gif"></a>
		</td>

		<td align="right">
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
		</td>
	</tr>
	</table>
</form>

<form name=frmList method=post action="indb.php">
<input type="hidden" name="mode" value="integrate_multi_action">
<input type="hidden" name="ord_status" value=""><!-- 변경코자하는 상태값 -->

	<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<!--col width="25"--><col width="35"><col width="100"><col width="100"><col width="80"><col width="160"><col><col width="50"><col width="90"><col width="50"><col width="60"><col width="70">
	<tr><td class="rnd" colspan="20"></td></tr>
	<tr class="rndbg">
		<!--th><a href="javascript:void(0)" onClick="chkBoxAll()" class=white>선택</a></th-->
		<th>번호</th>
		<th>주문일시</th>
		<th>취소일시/완료일시</th>
		<th>사유</th>
		<th colspan="2">주문번호 (주문상품)</th>
		<th>홍보채널</th>
		<th>주문자</th>
		<th>결제수단</th>
		<th>주문액</th>
		<th>처리상태</th>
	</tr>
	<tr><td class="rnd" colspan="20"></td></tr>
	<?
	$idx_grp = 0;
	$idx = $pg->idx; $pr = 1;
	while ($data=$db->fetch($res,1)){

		if ($_GET[sgword])
			list($goodsnm) = $db->fetch("select goodsnm, if({$_GET[sgkey]} LIKE '%{$_GET[sgword]}%', 0, 1) as resort from ".GD_ORDER_ITEM." where ordno='$data[ordno]' order by resort, sno");
		else
			list($goodsnm) = $db->fetch("select goodsnm from ".GD_ORDER_ITEM." where ordno='$data[ordno]' order by sno");

		if($data['goodscnt']>1) $goodsnm = $data['goodsnm'].' 외'.($data['goodscnt']-1).'건';
		else $goodsnm = $data['goodsnm'];

		// 강조색, 선택 버튼 비활성화
		if ($data['ord_status'] >= 10 OR ($data['channel'] != 'enamoo' AND $data['ord_status'] > 2)) {
			$disabled = 'disabled';
			$bgcolor = '#F0F4FF';
		}
		else {
			$disabled = '';
			$bgcolor = '#ffffff';
		}
	?>
		<tr height=25 bgcolor="<?=$bgcolor?>" bg="<?=$bgcolor?>" align=center>
			<td><font class=ver8 color=616161><?=$pr*$idx--?></font></td>
			<td><font class=ver81 color=616161><?=substr($data[ord_date],0,-3)?></font></td>
			<td><font class=ver81 color=616161><?=substr($data[cs_regdt],0,-3)?></font></td>
			<td><font class=ver81 color=616161><?=integrate_order::getCSStatus($data[cs_reason_type],$data[channel])?></font></td>
			<td align=left>
			<a href="view.php?ordno=<?=$data[ordno]?>"><font class=ver81 color=<?=$data['flg_inflow'] == 'sugi' ? 'ED6C0A' : '0074BA'?>><b><?=$data[ordno]?><?=$data['flg_inflow'] == 'sugi' ? '<span class="small1">(수기)</span>' : ''?></b></font></a>
			<a href="javascript:popup('popup.order.php?ordno=<?=$data[ordno]?>',800,600)"><img src="../img/btn_newwindow.gif" border=0 align=absmiddle></a>
			</td>
			<td align=left>
			<div>
				<?=($data['channel'] != 'enamoo') ? '<img src="../img/icon_int_order_'.$data['channel'].'.gif" align="absmiddle">' : ''?>
				<? if (!empty($data[old_ordno])){	?><a href="javascript:popup('popup.order.php?ordno=<?=$data[ordno]?>',800,600)"><img src="../img/icon_twice_order.gif"></a><? } ?>
				<? if ($data['flg_escrow']=="y"){	?><a href="javascript:popup('popup.order.php?ordno=<?=$data[ordno]?>',800,600)"><img src="../img/btn_escrow.gif"></a><? } ?>
				<? if ($data[flg_egg]=="y"){		?><a href="javascript:popup('popup.order.php?ordno=<?=$data[ordno]?>',800,600)"><img src="../img/icon_guar_order.gif"></a><? } ?>
				<? if (!empty($data[flg_cashreceipt])){	?><img src="../img/icon_cash_receipt.gif"><? } ?>
				<? if ($data[flg_cashbag]=="Y"){		?><a href="javascript:popup('popup.order.php?ordno=<?=$data[ordno]?>',800,600)"><img src="../img/icon_okcashbag.gif" align=absmiddle></a><? } ?>
				<font class=small1 color=444444><?=$goodsnm?></font>
			</div>
			</td>
			<td><? if ($data['flg_inflow']!="" && $data['flg_inflow']!="sugi"){ ?><a href="javascript:popup('popup.order.php?ordno=<?=$data['ordno']?>',800,600)"><img src="../img/inflow_<?=$data['flg_inflow']?>.gif" align="absmiddle" alt="<?=$integrate_cfg['inflows'][$data['flg_inflow']]?>" /></a><? } ?></td>
			<td>
				<?php if($data[m_id]){ ?>
					<?php if($data['dormant_regDate'] == '0000-00-00 00:00:00'){ ?>
						<span id="navig" name="navig" m_id="<?=$data['m_id']?>" m_no="<?=$data['m_no']?>"><span class="small1" style="color:#0074BA;"><strong><?php echo $data['ord_name']; ?></strong> (<?php echo $data[m_id]; ?>)</span></span>
					<?php } else { ?>
						<span class="small1" style="color:#0074BA;"><strong><?php echo $data['ord_name']; ?></strong> (<?php echo $data[m_id]; ?> / 휴면회원)</span>
					<?php } ?>
				<?php } else { ?>
					<span class="small1" style="color:#0074BA;font-weight:bold;"><?=$data[ord_name]?></span>
				<?php } ?>
			</td>

			<td class=small4><?=settleIcon($data['pg']);?> <?=isset($integrate_cfg['pay_method'][$data['pay_method']]) ? $integrate_cfg['pay_method'][$data['pay_method']] : '-'?></td>
			<td class=ver81><b><?=number_format($data['pay_amount'])?></b></td>
			<td class=small4 width=60><?=integrate_order::getOrderStatus($data['ord_status'],$data['cs'])?></td>
		</tr>
		<tr><td colspan=20 bgcolor=E4E4E4></td></tr>
	<?
		}
		$cnt = $pr * ($idx+1);
	?>
	<tr><td height="4"></td></tr>
	</table>

	<div class=pageNavi align=center><font class=ver8><?=$pg->page[navi]?></font></div>

</form>

<p>
<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">취소접수 및 취소완료 처리된 주문건에 대한 리스트입니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">검색매뉴 조건에 맞게 취소접수/완료 상태의 주문서를 조회할 수 있습니다.</td></tr>
<tr><td>&nbsp;</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">전체보기 : 조회된 주문 취소관련 리스트 전체를 확인할 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">취소접수 보기 : 조회된 주문취소 리스트중 취소접수건만 확인할 수있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">취소완료 보기 : 조회된 주문취소 리스트중 취소완료건만 확인할 수 있습니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<script>window.onload = function(){ UNM.inner();};</script>
<? @include dirname(__FILE__) . "/../interpark/_order_list.php"; // 인터파크_인클루드 ?>

<? include "../_footer.php"; ?>
