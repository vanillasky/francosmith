<?php
$location = '네이버체크아웃 4.0 > 배송준비중리스트';
include '../_header.php';
include "../../lib/page.class.php";

// 체크아웃 4.0 설정
	$checkout_message_schema = include "./_cfg.checkout.php";

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

// get 파라미터 처리 및 기본값 적용
	unset($_GET['x'],$_GET['y']);

	$_GET['mode']			= !empty($_GET['mode']) ? $_GET['mode'] : 'all';		// 리스트 타입
	$_GET['sort']			= !empty($_GET['sort']) ? $_GET['sort'] : 'O.OrderID DESC';		// 정렬
	$_GET['ProductOrderStatus']		= isset($_GET['ProductOrderStatus']) ? $_GET['ProductOrderStatus'] : -1;	// 처리상태
	$_GET['PaymentMeans']	= !empty($_GET['PaymentMeans']) ? $_GET['PaymentMeans'] : '';		// 결제수단
	$_GET['skey']			= !empty($_GET['skey']) ? $_GET['skey'] : '';					// 주문검색 조건
	$_GET['sword']			= !empty($_GET['sword']) ? trim($_GET['sword']) : '';					// 주문검색 키워드
	$_GET['dtkind']			= !empty($_GET['dtkind']) ? $_GET['dtkind'] : 'OrderDate';				// 날짜 조건
	$_GET['regdt']			= !empty($_GET['regdt']) ? $_GET['regdt'] : array(date('Ymd',strtotime('-'.(int)$cfg['orderPeriod'].' day',$now)), date('Ymd',$now));					// 날짜
	$_GET['regdt_time']		= !empty($_GET['regdt_time']) ? $_GET['regdt_time'] : array(-1,-1);		// 시간
	$_GET['sgkey']			= !empty($_GET['sgkey']) ? $_GET['sgkey'] : '';					// 상품검색 조건
	$_GET['sgword']			= !empty($_GET['sgword']) ? trim($_GET['sgword']) : '';				// 상품검색 키워드

	$_GET['page']			= !empty($_GET['page']) ? $_GET['page'] : 1;						// 페이지
	$_GET['page_num']		= !empty($_GET['page_num']) ? $_GET['page_num'] : ($cfg['orderPageNum'] ? $cfg['orderPageNum'] : 20);	// 페이지당 레코드수

// 검색절 만듦
	#0. 초기화
		$arWhere = array();

	#1. 주문상태
		$arWhere[] = '('.$checkout_message_schema['extra_productOrderStatusType']['배송준비중'].')';

	#2. 결제수단
		if($_GET['payMeansClassType']) {
			$arWhere[] = $db->_query_print('O.PaymentMeans= [s]',$_GET['payMeansClassType']);
		}

	#3. 통합검색
		if($_GET['sword'] && $_GET['skey']) {
			$es_sword = $db->_escape($_GET['sword']);
			switch($_GET['skey']) {
				case 'all':
					$_where = array();

					$_where[] = "O.OrderID = '{$es_sword}'";
					$_where[] = "PO.ProductOrderID = '{$es_sword}'";
					$_where[] = "PO.MallMemberID like '%{$es_sword}%'";
					$_where[] = "O.OrdererName like '%{$es_sword}%'";
					$_where[] = "(O.OrdererTel1 like '%{$es_sword}%' OR O.OrdererTel2 like '%{$es_sword}%')";

					$arWhere[] = "(".implode(' OR ',$_where).")";
					break;
				case 'OrderID': $arWhere[] = "O.OrderID = '{$es_sword}'"; break;
				case 'ProductOrderID': $arWhere[] = "PO.ProductOrderID = '%{$es_sword}%'"; break;
				case 'MallMemberID': $arWhere[] = "PO.MallMemberID like '%{$es_sword}%'"; break;
				case 'OrdererName': $arWhere[] = "O.OrdererName like '%{$es_sword}%'"; break;
				case 'OrdererTel': $arWhere[] = "(O.OrdererTel1 like '%{$es_sword}%' OR O.OrdererTel2 like '%{$es_sword}%')"; break;
			}
		}

	#4. 기간
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
				case 'OrderDate': $arWhere[] = $db->_query_print('O.OrderDate between [s] and [s]',$tmp_start,$tmp_end); break;
				case 'PaymentDate': $arWhere[] = $db->_query_print('O.PaymentDate between [s] and [s]',$tmp_start,$tmp_end); break;
				case 'SendDate': $arWhere[] = $db->_query_print('D.SendDate between [s] and [s]',$tmp_start,$tmp_end); break;
				case 'DeliveredDate': $arWhere[] = $db->_query_print('D.DeliveredDate between [s] and [s]',$tmp_start,$tmp_end); break;
			}
		}

	#5. 상품검색
		if($_GET['sgword'] && $_GET['sgkey']) {
			$es_sgword = $db->_escape($_GET['sgword']);
			switch($_GET['sgkey']) {
				case 'ProductName': $arWhere[] = "PO.ProductName like '%{$es_sgword}%'"; break;
			}
		}

	#6. 리스트 타입
		if ($_GET['mode'] == 'delayed') {
			$arWhere[] = "PO.DelayedDispatchReason > ''";
		}


	#xx. 페이징 query 생성
		$_paging_query = http_build_query($_GET);	// php5 전용함수. but! lib.func.php 안에 php4용 있음.


// 쿼리 실행
	$db_table = "".GD_NAVERCHECKOUT_ORDERINFO." AS O

		INNER JOIN ".GD_NAVERCHECKOUT_PRODUCTORDERINFO." AS PO
			ON PO.OrderID = O.OrderID

		LEFT JOIN ".GD_MEMBER." AS MB
			ON PO.MallMemberID=MB.m_id
			";

	$orderby = $_GET['sort'];

	$pg = new Page($_GET['page'],$_GET['page_num']);
	$pg->vars['page']= $_paging_query;

	$pg->cntQuery = "SELECT COUNT(DISTINCT O.OrderID) FROM ".$db_table.( sizeof($arWhere) > 0 ? ' WHERE '.implode(' AND ', $arWhere) : '');

	$pg->field = "
		O.*, PO.*,
		SUM(PO.TotalPaymentAmount) AS calculated_payAmount,
		SUM(PO.TotalProductAmount) AS calculated_ordAmount,
		COUNT(PO.ProductOrderID) AS OrderCount,
		GROUP_CONCAT(PO.ProductOrderID SEPARATOR ',') AS ProductOrderIDList
	";
	$pg->setQuery($db_table,$arWhere,$orderby,' GROUP BY PO.OrderID, PO.ProductOrderStatus');
	$pg->exec();
	$rs = $db->query($pg->query);

// 리스트 타입
	if ($_GET['mode'] === 'delayed') {
		define(_LIST_FILE_, './checkout.placed.inc.delayed.php');
		$delayedCount = $pg->recode['total'];
	}
	else {
		define(_LIST_FILE_, './checkout.placed.inc.all.php');
		list($delayedCount) = $db->fetch($pg->cntQuery." AND PO.DelayedDispatchReason > '' ");
	}

?>
<style>
select.small-selectbox {font-family:'돋움','돋움체','굴림','굴림체',dotum, dotumche;font-size:11px;letter-spacing:-1px;}
</style>

<script type="text/javascript" src="./checkout.js"></script>

<div class="title title_top">배송준비중 리스트 / 발송처리 <span>배송준비가 완료된 주문건들을 확인하고 발송처리를 하는 페이지 입니다.</span></div>

<form name="frmSearch" id="frmSearch" method="get" action="">

	<table class="tb">
	<col class="cellC"><col class="cellL">
	<tr>
		<td><span class="small1">주문검색</span></td>
		<td>

			<select name="payMeansClassType">
				<option value=""> = 결제수단 = </option>
				<? foreach ($checkout_message_schema['payMeansClassType'] as $k=>$v) { ?>
				<? if ($k == 'p') continue; ?>
				<option value="<?=$k?>" <?=$_GET['payMeansClassType'] == $k ? 'selected' : ''?>><?=$v?></option>
				<? } ?>
			</select>

			<select name="skey">
				<option value="all"> = 통합검색 = </option>
				<option value="OrderID"			<?=($_GET['skey'] == 'OrderID') ? 'selected' : ''?>			>주문번호</option>
				<option value="ProductOrderID"		<?=($_GET['skey'] == 'ProductOrderID') ? 'selected' : ''?>		>상품주문번호</option>
				<option value="MallMemberID"			<?=($_GET['skey'] == 'MallMemberID') ? 'selected' : ''?>			>주문자ID</option>
				<option value="OrdererName"			<?=($_GET['skey'] == 'OrdererName') ? 'selected' : ''?>			>주문자명</option>
				<option value="OrdererTel"		<?=($_GET['skey'] == 'OrdererTel') ? 'selected' : ''?>		>주문자연락처</option>	OrdererTel1	OrdererTel2
			</select>

			<input type="text" name="sword" value="<?=htmlspecialchars($_GET['sword'])?>" class="line" />

		</td>
	</tr>
	<tr>
		<td><span class="small1">처리일자</span></td>
		<td>
			<select name="dtkind">
				<option value="OrderDate"		<?=($_GET['dtkind'] == 'OrderDate' ? 'selected' : '')?>		>주문일</option>
				<option value="PaymentDate"			<?=($_GET['dtkind'] == 'PaymentDate' ? 'selected' : '')?>			>입금일</option>
				<option value="SendDate"			<?=($_GET['dtkind'] == 'SendDate' ? 'selected' : '')?>			>배송일</option>
				<option value="DeliveredDate"	<?=($_GET['dtkind'] == 'DeliveredDate' ? 'selected' : '')?>	>배송완료일</option>
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
	<tr>
		<td><span class="small1">상품검색</span></td>
		<td>
			<select name="sgkey">
				<option value="ProductName"	<?=($_GET['sgkey'] == 'ProductName') ? 'selected' : ''?>	>상품명</option>
			</select>
			<input type="text" name="sgword" value="<?=htmlspecialchars($_GET['sgword'])?>" class="line"/>
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

		</td>
	</tr>
	</table>
	</div>

</form>

<div style="margin:15px 0 5px 0;_border:1px solid #fff">

	<div class="fl">
		<button class="default-btn<?=($_GET['mode'] == 'all') ? '' : '-off' ?>" onClick="window.location.href='?<?=$_paging_query?>&mode=all';return false;">전체 리스트</button>
		<button class="default-btn<?=($_GET['mode'] == 'delayed') ? '' : '-off' ?>" onClick="window.location.href='?<?=$_paging_query?>&mode=delayed';return false;">발송지연 리스트 (<?=$delayedCount?>건)</button>
	</div>

	<div class="fr">

		<select name="DeliveryMethodCode" id="el-DeliveryMethodCode" align="absmiddle" onChange="fnChangedDeliveryMethodCode();">
		<option value="">배송방법</option>
		<? foreach ($checkout_message_schema['deliveryMethodType'] as $code => $name) { ?>
		<? if (strpos($code,'RETURN_') === 0) continue;?>
		<option value="<?=$code?>"><?=$name?></option>
		<? } ?>
		</select>

		<select name="DeliveryCompanyCode" id="el-DeliveryCompanyCode" align="absmiddle" disabled>
		<option value="">택배사</option>
		<? foreach ($checkout_message_schema['selectDeliveryCompanyType'] as $code => $name) { ?>
		<option value="<?=$code?>"><?=$name?></option>
		<? } ?>
		</select>

		<button class="default-btn" onClick="fnApplyDeliveryCodes();return false;">선택적용</button>

	</div>

	<div class="cb"></div>

</div>

<form name="frmNaverCheckout" method="post" target="processLayerForm">
	<input type="hidden" name="mode" value="">
	<?
	include (_LIST_FILE_);
	?>
</form>

<div style="margin:15px 0 5px 0;_border:1px solid #fff">

	<div class="fl">
		<button class="default-btn" onClick="fnShipProductOrder()">발송처리</button>
		<button class="default-btn" onClick="fnDelayProductOrder()">발송지연</button>
	</div>

	<div class="fr">

		<select name="DeliveryMethodCode" id="el-DeliveryMethodCode" align="absmiddle" onChange="fnChangedDeliveryMethodCode();">
		<option value="">배송방법</option>
		<? foreach ($checkout_message_schema['deliveryMethodType'] as $code => $name) { ?>
		<option value="<?=$code?>"><?=$name?></option>
		<? } ?>
		</select>

		<select name="DeliveryCompanyCode" id="el-DeliveryCompanyCode" align="absmiddle" disabled>
		<option value="">택배사</option>
		<? foreach ($checkout_message_schema['selectDeliveryCompanyType'] as $code => $name) { ?>
		<option value="<?=$code?>"><?=$name?></option>
		<? } ?>
		</select>

		<button class="default-btn" onClick="fnApplyDeliveryCodes();return false;">선택적용</button>

	</div>

	<div class="cb"></div>

</div>

<div class="pageNavi" align="center">
	<font class="ver8"><?=$pg->page[navi]?></font>
</div>

<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle"/>주문번호를 클릭하시면 해당 주문의 상세정보를 확인하실 수 있으며, 주문 상품별 부분처리가 가능합니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"/>배송준비가 완료된 주문내역을 확인하고, 해당 주문건의 상품을 발송여부를 처리 합니다.</td></tr>
<tr><td>&nbsp;</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"/>발송처리 : 배송일, 배송방법, 택배사, 송장번호 입력이 완료된 주문의 상품을 발송처리 합니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"/>발송지연 : 배송준비 완료가 되지 않았거나, 고객의 요청등 으로 인하여 배송준비 기간이 필요한 주문건에 대하여 발송 지연 처리 합니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"/>배송방법, 택배사 일괄등록 : 배송방법과 택배사 등록을 일괄적으로 처리할 수 있습니다. 일괄처리할 주문건을 선택하고 배송방법과 택배사 등록후 [선택적용] 버튼을 눌러주세요.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<? include '../_footer.php'; ?>