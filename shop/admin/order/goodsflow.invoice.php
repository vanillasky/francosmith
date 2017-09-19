<?php
$location = "택배연동 서비스 > 굿스플로 송장번호 발급";
include "../_header.php";
include "../../lib/page.class.php";
include "../../conf/config.pay.php";

$gf = Core::loader('goodsflow_v2');

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

// get 파라미터 처리 및 기본값 적용
	unset($_GET['x'],$_GET['y']);

	$_GET['sort']			= !empty($_GET['sort']) ? $_GET['sort'] : 'o.orddt desc';		// 정렬
	$_GET['mode']			= !empty($_GET['mode']) ? $_GET['mode'] : 'ordno';					// 뷰 형식
	$_GET['ord_status']		= !empty($_GET['ord_status']) ? $_GET['ord_status'] : -1;	// 처리상태
	$_GET['settlekind']		= !empty($_GET['settlekind']) ? $_GET['settlekind'] : '';		// 결제수단
	$_GET['ord_type']		= !empty($_GET['ord_type']) ? $_GET['ord_type'] : '';		// 접수유형
	$_GET['skey']			= !empty($_GET['skey']) ? $_GET['skey'] : '';					// 주문검색 조건
	$_GET['sword']			= !empty($_GET['sword']) ? trim($_GET['sword']) : '';					// 주문검색 키워드
	$_GET['dtkind']			= !empty($_GET['dtkind']) ? $_GET['dtkind'] : 'orddt';				// 날짜 조건
	$_GET['regdt']			= !empty($_GET['regdt']) ? $_GET['regdt'] : array(date('Ymd',strtotime('-'.(int)$cfg['orderPeriod'].' day',$now)), date('Ymd',$now));					// 날짜
	$_GET['regdt_range']	= !empty($_GET['regdt']) ? $_GET['regdt'] : '';					// 날짜 기간 ( regdt[0] 부터 며칠 )
	$_GET['regdt_time']		= !empty($_GET['regdt_time']) ? $_GET['regdt_time'] : array(-1,-1);		// 시간
	$_GET['sgkey']			= !empty($_GET['sgkey']) ? $_GET['sgkey'] : '';					// 상품검색 조건
	$_GET['sgword']			= !empty($_GET['sgword']) ? trim($_GET['sgword']) : '';				// 상품검색 키워드

	$_GET['eggyn']			= !empty($_GET['eggyn']) ? $_GET['eggyn'] : '';					// 전자보증보험

	$_GET['escrowyn']		= !empty($_GET['escrowyn']) ? $_GET['escrowyn'] : '';			// 에스크로
	$_GET['cashreceipt']	= !empty($_GET['cashreceipt']) ? $_GET['cashreceipt'] : '';		// 현금영수증
	$_GET['flg_coupon']		= !empty($_GET['flg_coupon']) ? $_GET['flg_coupon'] : '';			// 쿠폰사용
	$_GET['about_coupon_flag']	= !empty($_GET['about_coupon_flag']) ? $_GET['about_coupon_flag'] : '';		// 어바웃쿠폰
	$_GET['pay_method_p']	= !empty($_GET['pay_method_p']) ? $_GET['pay_method_p'] : '';	// 적립금(포인트)
	$_GET['cbyn']			= !empty($_GET['cbyn']) ? $_GET['cbyn'] : '';					// ok캐시백 적립

	$_GET['chk_inflow']		= !empty($_GET['chk_inflow']) ? $_GET['chk_inflow'] : array();	// 홍보채널 (유입경로)
	$_GET['page']			= !empty($_GET['page']) ? $_GET['page'] : 1;						// 페이지
	$_GET['page_num']		= !empty($_GET['page_num']) ? $_GET['page_num'] : ($cfg['orderPageNum'] ? $cfg['orderPageNum'] : 20);	// 페이지당 레코드수

// 검색절 만듦

	#0. 초기화
		$arWhere   = array();
		$arWhere[] = " (GF.status = '' OR  GF.status IS NULL) ";

	#1. 판매 채널

	#2. 주문 상태
		$arWhere[] = " o.step2 < 40 ";

		if ($_GET['ord_status'] > -1) {
			$arWhere[] = " o.step = ".(int)$_GET['ord_status'];
		}
		else {
			$arWhere[] = " (o.step = 1 OR  o.step = 2) ";
		}

	#3. 결제 수단
		if($_GET['settlekind']) {
			$arWhere[] = $db->_query_print('o.settlekind= [s]',$_GET['settlekind']);
		}

	#4. 통합 검색
		if($_GET['sword'] && $_GET['skey']) {
			$es_sword = $db->_escape($_GET['sword']);
			switch($_GET['skey']) {
				case 'all':
					$_where = array();

					// 이나무의 데이터를 가져오므로, 필드를 매핑해줌.
					$_skey_map = array(
						'o.ord_name' => 'o.nameOrder',
						'o.rcv_name' => 'o.nameReceiver',
						'o.pay_bank_name' => 'o.bankSender',
						'm.m_id' => 'm.m_id',
						'o.ord_phone' => 'o.phoneOrder',
						'o.rcv_phone' => 'o.phoneReceiver',
						'o.rcv_address' => 'o.address',
						'o.dlv_no' => 'o.deliverycode',
					);

					foreach($integrate_cfg['skey'] as $cond) {
						if (preg_match($cond['pattern'],$es_sword)) {

							$_condition = isset($_skey_map[$cond['field']]) ? $_skey_map[$cond['field']] : $cond['field'];

							if ($cond['condition'] == 'like') $_condition .= ' like \'%'.$es_sword.'%\'';
							else if ($cond['condition'] == 'equal') $_condition .= ' = \''.$es_sword.'\'';
							else continue;

							$_where[] = $_condition;
						}
					}

					if (sizeof($_where) > 0) $arWhere[] = "(".implode(' OR ',$_where).")";
					break;
				case 'ordno': $arWhere[] = "o.ordno = '{$es_sword}'"; break;
				case 'nameOrder': $arWhere[] = "o.nameOrder like '%{$es_sword}%'"; break;
				case 'nameReceiver': $arWhere[] = "o.nameReceiver like '%{$es_sword}%'"; break;
				case 'bankSender': $arWhere[] = "o.bankSender like '%{$es_sword}%'"; break;
				case 'm_id': $arWhere[] = "m.m_id = '{$es_sword}'"; break;
				case 'phoneOrder': $arWhere[] = "o.phoneOrder like '%{$es_sword}%'"; break;
				case 'phoneReceiver': $arWhere[] = "o.phoneReceiver like '%{$es_sword}%'"; break;
				case 'address': $arWhere[] = "o.address like '%{$es_sword}%'"; break;
				case 'deliverycode': $arWhere[] = "o.deliverycode like '%{$es_sword}%'"; break;
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

			//$arWhere[] = $db->_query_print('o.orddt between [s] and [s]',$tmp_start,$tmp_end);

			switch($_GET['dtkind']) {
				case 'orddt': $arWhere[] = $db->_query_print('o.orddt between [s] and [s]',$tmp_start,$tmp_end); break;
				case 'cs_regdt': $arWhere[] = $db->_query_print('a.regdt between [s] and [s]',$tmp_start,$tmp_end); break;
				//case 'ddt': $arWhere[] = $db->_query_print('o.ddt between [s] and [s]',$tmp_start,$tmp_end); break;
				//case 'confirmdt': $arWhere[] = $db->_query_print('o.confirmdt between [s] and [s]',$tmp_start,$tmp_end); break;
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
		if($_GET['eggyn']) {
			$arWhere[] = $db->_query_print('o.eggyn = [s]',$_GET['eggyn']);
		}

	#8. 결제시 적용
		$tmp_arWhere = array();

		if($_GET['escrowyn']) {
			$tmp_arWhere[] = $db->_query_print('o.escrowyn = [s]',$_GET['escrowyn']);
		}

		if($_GET['cashreceipt']) {
			$tmp_arWhere[] = 'o.cashreceipt != ""';
		}
		if($_GET['flg_coupon']) {
			$tmp_arWhere[] = 'co.ordno is not null';
			$join_GD_COUPON_ORDER='left join '.GD_COUPON_ORDER.' as co on o.ordno=co.ordno';
		}
		else {
			$join_GD_COUPON_ORDER='';
		}

		if($_GET['about_coupon_flag']=='1') {
			$tmp_arWhere[] = 'o.about_coupon_flag = "Y"';
		}

		if($_GET['pay_method_p']=='1') {
			$tmp_arWhere[] = 'o.settlekind= "p"';
		}

		if($_GET['cbyn']=='Y') {
			$tmp_arWhere[] = 'o.cbyn = "Y"';
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
			$arWhere[] = 'o.inflow in ('.implode(',',$es_inflow).')';
		}

	#10. 접수유형 (별도 필드가 없고, inflow 값이 sugi 인 레코드)
		if($_GET['ord_type'] == 'offline') {
			$arWhere[] = 'o.inflow = \'sugi\'';
		}
		else if ($_GET['ord_type'] == 'online') {
			$arWhere[] = 'o.inflow <> \'sugi\'';
		}

	#xx. 페이징 query 생성
		$_paging_query = http_build_query($_GET);	// php5 전용함수. but! lib.func.php 안에 php4용 있음.

// 쿼리 실행
	$db_table = "".GD_ORDER." AS o
			INNER JOIN ".GD_ORDER_ITEM." as oi
			ON o.ordno = oi.ordno

			LEFT JOIN ".GD_MEMBER." AS m
			ON o.m_no=m.m_no

			LEFT JOIN ".GD_GOODSFLOW_ORDER_MAP." as OD
			ON o.ordno = OD.ordno AND oi.sno = OD.item_sno

			LEFT JOIN ".GD_GOODSFLOW." as GF
			ON OD.goodsflow_sno = GF.sno
			";
	$db_table .= $join_GD_COUPON_ORDER;
	$db_table .= $join_GD_PURCHASE;
	$orderby = $_GET['sort'];
	$pg = new Page($_GET['page'],$_GET['page_num']);
	$pg->vars['page']= $_paging_query;

//$_GET['mode']

	if ($_GET['mode'] === 'goods') {

		// 상품별로 송장번호를 입력
		$pg->cntQuery = "SELECT COUNT(DISTINCT o.ordno) FROM ".$db_table." WHERE ".implode(' AND ',$arWhere);

		$pg->field = "
			o.ordno
		";
		$pg->setQuery($db_table,$arWhere,$orderby,"group by o.ordno");
		$pg->exec();
		$res = $db->query($pg->query);

		$ordnos = array();

		while ($row = $db->fetch($res,1)) {
			$ordnos[] = $row['ordno'];
		}

		if (sizeof($ordnos) > 0) {

			$query = "
			SELECT
				o.*,

				m.m_id,
				m.m_no,

				oi.goodsnm,
				oi.goodsno,
				oi.sno,
				oi.istep

			FROM
				".$db_table."

			WHERE
				o.ordno IN (".implode(',',$ordnos).")
				AND (GF.status = '' OR  GF.status IS NULL)

			ORDER BY
				".$orderby."
			";
			$res = $db->query($query);
		}
		else {
			$res = null;
		}

		list($total) = $db->fetch("SELECT COUNT(o.ordno) FROM ".$db_table." WHERE ".implode(' AND ',$arWhere));
		$query = 'SELECT CONCAT(o.ordno,\'_\',oi.sno) as ordno FROM '.$db_table.' WHERE '.implode(' AND ', $arWhere);

	}
	else {
		// 한개의 송장번호만 입력 (기본)
		$pg->cntQuery = "SELECT count(DISTINCT o.ordno) FROM ".$db_table." WHERE ".implode(' AND ',$arWhere);

		$pg->field = "
				o.*,

				m.m_id,
				m.m_no,

				oi.goodsnm,
				oi.goodsno,
				COUNT(oi.sno) AS goods_cnt
		";
		$pg->setQuery($db_table,$arWhere,$orderby,"group by o.ordno");
		$pg->exec();
		$res = $db->query($pg->query);

		$total = $pg->recode['total'];
		$query = 'SELECT o.ordno FROM '.$db_table.' WHERE '.implode(' AND ', $arWhere).' GROUP BY o.ordno';
	}

// 리스트 타입
	if ($_GET['mode'] === 'goods')
		define(_LIST_FILE_, './goodsflow.invoice.inc.casebygoods.php');
	else
		define(_LIST_FILE_, './goodsflow.invoice.inc.casebyorder.php');

?>

<style media="screen">
.el-goodsflow-descript {border:1px solid #ccc;padding:10px;}
.el-goodsflow-descript h4 {font-size:12px;margin:0;padding:0;}
.el-goodsflow-descript dl {margin:10px 0 5px 4px;}
.el-goodsflow-descript dl dt,
.el-goodsflow-descript dl dd {font-size:11px;height:18px;margin:0;padding:0;font-family:Dotum,Gulim;color:#333;letter-spacing:-1px;}
.el-goodsflow-descript dl dt {font-weight:bold;color:#666;}
.el-goodsflow-descript dl dd {margin:-18px 0 0 130px;color:#777;}
.el-goodsflow-descript p {font-size:12px;margin:0;}
.el-goodsflow-descript hr  {color:#ddd;background-color: #f00;height:1px;}
</style>
<script language='javascript'>
/**
* 라인색상 활성화
*/
function iciSelect(obj) {
	var row = obj.parentNode.parentNode;
	row.style.background = (obj.checked) ? "#F9FFA1" : row.getAttribute('bg');
	if($('tr_'+obj.value)) {
		$('tr_'+obj.value).style.background=row.style.background;
	}
}

/**
* 전체선택
*/
var chkBoxAll_flag=true;
function chkBoxAll() {
	$$(".chk_ordno").each(function(item){
		if(item.disabled==true) return;
		item.checked=chkBoxAll_flag;
		iciSelect(item);
	});
	chkBoxAll_flag=!chkBoxAll_flag;
}

function fnGoodsflowInvoice(m) {
	var f = document.fmList;

	f.mode.value = m;

	switch (m) {

		case 'partial':	// 상품별 부분 배송 (동일 주문내 체크된 상품은 하나의 송장번호로 발급)

			try
			{
				var _ordno = '';
				$$('input[name="target[ordno][]"]:checked').each(function(el){

					if (_ordno == '') {
						_ordno = el.value.split('_').shift();
					}
					else {
						if (_ordno != el.value.split('_').shift()) {
							throw false;
						}
					}

				});
			}
			catch (e) {
				alert('부분배송 송장 번호 발급은 같은 주문건에 대해서만 가능합니다.');
				return false;
			}
			break;

		case 'package' :	// 주문별 합포장 배송 (배송지가 같은 체크된 주문은 하나의 송장번호로 발급)
			try
			{
				var _address = '';
				$$('input[name="target[ordno][]"]:checked').each(function(el){

					if (_address == '') {
						_address = el.readAttribute('address');
					}
					else {
						if (_address != el.readAttribute('address')) {
							throw false;
						}
					}

				});
			}
			catch (e) {
				alert('배송지가 다른 주문건이 포함되어 있습니다. 합포장은 배송지가 같은 주문건만 가능합니다.');
				return false;
			}
			break;

		case 'casebyorder':	// 주문별 배송
		case 'casebygoods':	// 상품별 배송
			break;

		default:
			return false;
			break;

	}

	if (f.target_type.value == 'choice') {
		var chk_size = $$('input[name="target[ordno][]"]:checked').size();

		if (chk_size < 1)
		{
			alert('선택된 송장번호 발급 요청 주문건이 없습니다.');
		}
		else {
			if (confirm('선택된 주문 ' + chk_size + '건을 굿스플로와 연동합니다.')) {
				popup_return('about:blank','GODO_GF_WIN',800,650,0,0,1);
				f.target = 'GODO_GF_WIN';
				f.submit();
			}
		}
	}
	else {
		if (confirm('검색된 주문 <?=$total?>건을 굿스플로와 연동합니다.')) {
			popup_return('about:blank','GODO_GF_WIN',800,650,0,0,1);
			f.target = 'GODO_GF_WIN';
			f.submit();
		}
	}

	return false;

}
</script>

<div class="title title_top">굿스플로 송장번호 발급<span>배송을 위해 송장번호를 받아야 하는 주문건 리스트에서 굿스플로 송장발급을 요청합니다.</span>
<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=order&no=36')"><img src="../img/btn_q.gif" border=0 hspace=2 align=absmiddle></a>
</div>

<form name="frmSearch" id="frmSearch" method="get" action="">
	<input type="hidden" name="mode" value="<?=$_GET['mode']?>"/>	<!-- 주문일 or 주문처리흐름 -->

	<table class="tb">
	<col class="cellC"><col class="cellL" style="width:250px">
	<col class="cellC"><col class="cellL">
	<tr>
		<td><span class="small1">주문검색</span></td>
		<td colspan="3">

			<select name="ord_status">
				<option value="-1"> = 주문상태 = </option>
				<option value="1" <?=$_GET['ord_status'] == 1 ? 'selected' : ''?>>입금확인</option>
				<option value="2" <?=$_GET['ord_status'] == 2 ? 'selected' : ''?>>배송준비중</option>
			</select>

			<select name="settlekind">
				<option value=""> = 결제수단 = </option>
				<? foreach ($integrate_cfg['pay_method'] as $k=>$v) { ?>
				<? if ($k == 'p') continue; ?>
				<option value="<?=$k?>" <?=$_GET['settlekind'] == $k ? 'selected' : ''?>><?=$v?></option>
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
				<option value="nameOrder"		<?=($_GET['skey'] == 'nameOrder') ? 'selected' : ''?>		>주문자명</option>
				<option value="m_id"			<?=($_GET['skey'] == 'm_id') ? 'selected' : ''?>			>주문자ID</option>
				<option value="phoneOrder"			<?=($_GET['skey'] == 'phoneOrder') ? 'selected' : ''?>			>주문자연락처</option>
				<option value="bankSender"		<?=($_GET['skey'] == 'bankSender') ? 'selected' : ''?>	>입금자명</option>
				<option value="nameReceiver"	<?=($_GET['skey'] == 'nameReceiver') ? 'selected' : ''?>	>수령자명</option>
				<option value="phoneReceiver"	<?=($_GET['skey'] == 'phoneReceiver') ? 'selected' : ''?>	>수령자연락처</option>
				<option value="address"	<?=($_GET['skey'] == 'address') ? 'selected' : ''?>	>배송지주소</option>
				<option value="deliverycode"	<?=($_GET['skey'] == 'deliverycode') ? 'selected' : ''?>	>송장번호</option>
			</select>

			<input type="text" name="sword" value="<?=htmlspecialchars($_GET['sword'])?>" class="line" />

		</td>
	</tr>
	<tr>
		<td><span class="small1">처리일자</span></td>
		<td colspan="3">

			<select name="dtkind">
				<option value="orddt"		<?=($_GET['dtkind'] == 'orddt' ? 'selected' : '')?>		>주문일</option>
				<!--option value="cs_regdt"			<?=($_GET['dtkind'] == 'cs_regdt' ? 'selected' : '')?>			>환불요청일</option-->
				<!--option value="ddt"			<?=($_GET['dtkind'] == 'ddt' ? 'selected' : '')?>			>배송일</option>
				<option value="confirmdt"	<?=($_GET['dtkind'] == 'confirmdt' ? 'selected' : '')?>	>배송완료일</option-->
			</select>

			<input type="text" name="regdt[]" value="<?=$_GET['regdt'][0]?>" onclick="calendar(event)" size="12" class="line"/>

			<select name="regdt_time[]">
			<option value="-1">---</option>
			<? for ($i=0;$i<24;$i++) {?>
			<option value="<?=$i?>" <?=($_GET['regdt_time'][0] == $i ? 'selected' : '')?>><?=sprintf('%02d',$i)?>시</option>
			<? } ?>
			</select>
			-
			<input type="text" name="regdt[]" value="<?=$_GET['regdt'][1]?>" onclick="calendar(event)" size="12" class="line"/>
			<select name="regdt_time[]">
			<option value="-1">---</option>
			<? for ($i=0;$i<24;$i++) {?>
			<option value="<?=$i?>" <?=($_GET['regdt_time'][1] == $i ? 'selected' : '')?>><?=sprintf('%02d',$i)?>시</option>
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
			<select name="eggyn">
				<option value=""	<?=($_GET['eggyn'] == '') ? 'selected' : ''?>	>전체</option>
				<option value="n"	<?=($_GET['eggyn'] == 'n') ? 'selected' : ''?>>미발급</option>
				<option value="f"	<?=($_GET['eggyn'] == 'f') ? 'selected' : ''?>>발급실패</option>
				<option value="y"	<?=($_GET['eggyn'] == 'y') ? 'selected' : ''?>>발급완료</option>
			</select>
		</td>
	</tr>
	<tr class="blindable">
		<td><span class="small1">결제시적용</span></td>
		<td colspan="3" class="noline">
			<input type="checkbox" name="escrowyn" value="y" <?=frmChecked('y',$_GET['escrowyn'])?>>에스크로 <img src="../img/btn_escrow.gif" align="absmiddle"/></input>
			<input type="checkbox" name="cashreceipt" value="1" <?=frmChecked('1',$_GET['cashreceipt'])?>>현금영수증 <img src="../img/icon_cash_receipt.gif"/></input>
			<input type="checkbox" name="flg_coupon" value="1" <?=frmChecked('1',$_GET['flg_coupon'])?>>쿠폰사용</input>
			<input type="checkbox" name="about_coupon_flag" value="1" <?=frmChecked('1',$_GET['about_coupon_flag'])?>>어바웃쿠폰</input>
			<input type="checkbox" name="pay_method_p" value="1" <?=frmChecked('1',$_GET['pay_method_p'])?>>적립금(포인트)</input>
			<input type="checkbox" name="cbyn" value="Y" <?=frmChecked('Y',$_GET['cbyn'])?>><img src="../img/icon_okcashbag.gif" align="absmiddle"/>OK캐시백적립</input>
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
		<a href="?<?=$_paging_query?>&mode=ordno"><img src="../img/btn_int_order_list_by_ordno<?=$_GET['mode'] == 'ordno' ? '_on' : ''?>.gif"></a>
		<a href="?<?=$_paging_query?>&mode=goods"><img src="../img/btn_int_order_list_by_goods<?=$_GET['mode'] == 'goods' ? '_on' : ''?>.gif"></a>		</td>

		<td align="right">
		<select name="sort" onchange="this.form.submit();">
			<option value="a.regdt desc" <?=$_GET['sort'] == 'a.regdt desc' ? 'selected' : '' ?>> 취소일순↑</option>
			<option value="a.regdt asc" <?=$_GET['sort'] == 'a.regdt asc' ? 'selected' : '' ?>>취소일순↓</option>

			<option value="o.orddt desc" <?=$_GET['sort'] == 'o.orddt desc' ? 'selected' : '' ?>>주문일순↑</option>
			<option value="o.orddt asc" <?=$_GET['sort'] == 'o.orddt asc' ? 'selected' : '' ?>>주문일순↓</option>

			<option value="o.cdt desc" <?=$_GET['sort'] == 'o.cdt desc' ? 'selected' : '' ?>>입금일순↑</option>
			<option value="o.cdt asc" <?=$_GET['sort'] == 'o.cdt asc' ? 'selected' : '' ?>>입금일순↓</option>

			<option value="o.settleprice desc" <?=$_GET['sort'] == 'o.settleprice desc' ? 'selected' : '' ?>>결제액순↑</option>
			<option value="o.settleprice asc" <?=$_GET['sort'] == 'o.settleprice asc' ? 'selected' : '' ?>>결제액순↓</option>
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

<br>

<form name="fmList" method="post" action="indb.goodsflow.php" target="_blank">
<input type="hidden" name="process" value="invoice">
<input type="hidden" name="mode" value="">
<input type="hidden" name="query" value="<?=base64_encode($query)?>">

<?
include (_LIST_FILE_);
?>
</form>
<br>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><font class=def1 color=ffffff><strong>배송을 위해 송장번호를 받을 수 있는 입금확인/배송준비중 상태의 주문건 리스트입니다.</strong></td></tr>
<tr><td><font class=def1 color=ffffff><strong>이 리스트에서 굿스플로와 연동할 주문건을 선택하여 송장번호 발급 요청할 수 있습니다.</strong></td></tr>
<tr><td height=8></td></tr>
<tr><td><font class=def1 color=ffffff><strong>1.송장번호 발급</strong></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">주문건 선택 후 '굿스플로 송장번호 발급'을 클릭하시면 굿스플로 송장번호 출력 팝업 화면이 열립니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">굿스플로 서비스 화면에서 송장출력정보를 입력하시면 생성된 송장번호가 이나무 관리자와 연동됩니다.</td></tr>
<tr><td height=8></td></tr>
<tr><td><font class=def1 color=ffffff><strong>2.합포장</strong></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">주문자와 배송지 주소가 같은 상품은 합포장하여 하나의 송장번호를 발급 받을 수 있습니다.</td></tr>
<tr><td height=8></td></tr>
<tr><td><font class=def1 color=ffffff><strong>3.부분 배송</strong></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">한 주문건에 일부 상품만 배송해야 하는 경우 상품별 보기리스트에서 부분배송으로 송장번호를 발급 받을 수 있습니다.</td></tr>
<tr><td height=8></td></tr>
<tr><td><font class=def1 color=ffffff><strong>4.송장번호 연동 정보 확인</strong></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">발급 요청한 송장번호는 각 주문관련 메뉴(배송준비중 리스트, 주문 상세)와 택배연동서비스>굿스플로 배송대기 리스트에서 확인할 수 있습니다.</td></tr>
</table>
</div>

<script>cssRound('MSG01')</script>

<script>window.onload = function(){ UNM.inner();};</script>

<? include "../_footer.php"; ?>
