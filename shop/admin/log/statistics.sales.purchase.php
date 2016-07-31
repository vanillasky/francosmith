<?
$location = "통계관리 > 매출분석 > 사입처(공급처)별 매출통계";
include "../_header.php";
include '../../lib/ofc/php-ofc-library/open-flash-chart.php';

$where = array();


// 검색 조건
	$_GET['regdt'][0] = $sdate_s = ($_GET['regdt'][0]) ? $_GET['regdt'][0] : date('Ymd',strtotime('-7 day'));
	$_GET['regdt'][1] = $sdate_e = ($_GET['regdt'][1]) ? $_GET['regdt'][1] : date('Ymd');

	if (checkStatisticsDateRange($sdate_s, $sdate_e) > 365) {
		msg('조회기간 설정은 최대 1년을 넘지 못합니다. 기간 확인후 재설정 해주세요.',$_SERVER['PHP_SELF']);exit;
	}

	$sword = isset($_GET['sword']) ? $_GET['sword'] : '';
	if ($sword) {
		$where[] = $_GET['skey']." like '%$sword%'";
	}

	$brandnm = isset($_GET['brandnm']) ? $_GET['brandnm'] : '';
	if ($brandnm) {
		$where[] = " OI.brandnm = '$brandnm'";
	}
	$category = false;
	if ($_GET['cate']){
		$category = array_notnull($_GET['cate']);
		$category = $category[count($category)-1];
	}

	if (sizeof($_GET['settlekind']) < 1 || $_GET['settlekind']['all']) {
		$_GET['settlekind'] = array();
		$_GET['settlekind']['all'] = 1;
	}
	elseif (sizeof($_GET['settlekind']) === 6) {
		$_GET['settlekind'] = array();
		$_GET['settlekind']['all'] = 1;
	}
	else {
		$_tmp = array();
		foreach($_GET['settlekind'] as $k => $v) {
			if (!$v || $k == 'all') continue;

			$_tmp[] = " O.settlekind = '".$k."'";
		}

		if (!empty($_tmp)) $where[] = ' ('.implode(' OR ',$_tmp).') ';
	}

// sql
	if (empty($_GET['dtkind'])) $_GET['dtkind'] = $dtkind = 'cdt';
	else $dtkind = $_GET['dtkind'];

	$sub_query = "
	SELECT
		PCH.comnm, PCH.pchsno,
		O.ordno,					/* 주문건수 */
		O.emoney,			/* 적립금 사용 금액 */
		(O.coupon) AS coupon_dc,	/* 쿠폰할인 금액 */
		(O.memberdc) AS member_dc,	/* 회원할인 금액 */
		(O.enuri) AS enuri_dc,	/* 에누리할인 금액 */
		(O.o_special_discount_amount) AS goods_dc,	/* 상품할인 금액 */
		SUM(OI.price * OI.ea) AS goodsprice,			/* 상품가격 */
		O.delivery,		/* 결제금액中 배송비 */
		SUM(OI.supply * OI.ea) AS sub_supply	/* 매입금액 */

	FROM ".GD_ORDER." AS O

	INNER JOIN ".GD_ORDER_ITEM." AS OI
	ON O.ordno = OI.ordno

	INNER JOIN ".GD_GOODS." AS G
	ON OI.goodsno = G.goodsno

	LEFT JOIN ".GD_PURCHASE_GOODS." AS PCHG
	ON G.goodsno = PCHG.goodsno

	LEFT JOIN ".GD_PURCHASE." AS PCH
	ON PCHG.pchsno = PCH.pchsno
	";

	if ($category){
		$sub_query .= "
		INNER JOIN ".GD_GOODS_LINK." AS LNK
		ON OI.goodsno=LNK.goodsno
		";

		// 상품분류 연결방식 전환 여부에 따른 처리
		$where[]	= getCategoryLinkQuery('LNK.category', $category, 'where');
	}

	$_param = array(
		$dtkind,
		Core::helper('Date')->min($sdate_s),
		Core::helper('Date')->max($sdate_e)
	);

	$where[] = vsprintf("O.%s between '%s' and '%s'", $_param);
	$where[] = "O.step2 < 40 AND O.step > 0 AND OI.istep < 40";
	//$where[] = "PCH.pchsno IS NOT NULL ";

	$sub_query .= ' WHERE '.implode(' AND ', $where);
	$sub_query .= ' GROUP BY O.ordno, PCH.pchsno ';

	$query = "
	SELECT
		IFNULL(SUB.comnm, '이름없음') AS comnm,
		IFNULL(SUB.pchsno, 0) AS pchsno,
		COUNT(SUB.ordno) AS cnt,
		SUM(SUB.emoney) AS tot_emoney,			/* 적립금 사용 금액 */
		SUM(SUB.coupon_dc) AS tot_coupon_dc,	/* 쿠폰할인 금액 */
		SUM(SUB.member_dc) AS tot_member_dc,	/* 회원할인 금액 */
		SUM(SUB.enuri_dc) AS tot_enuri_dc,	/* 에누리할인 금액 */
		SUM(SUB.goods_dc) AS tot_goods_dc,	/* 상품할인 금액 */
		SUM(SUB.goodsprice) AS tot_price,			/* 상품가격 */
		SUM(SUB.delivery) AS tot_delivery,		/* 결제금액中 배송비 */
		SUM(SUB.sub_supply) AS tot_supply	/* 매입금액 */

	FROM
	(
		".$sub_query."

	) AS SUB
	";
	$query .= ' GROUP BY SUB.pchsno ';
	$query .= ' ORDER BY SUB.comnm ';

// 쿼리
$rs = $db->query($query);
$rs_max = $db->count_($rs);
$total = $arRow = $chart = array();

while ($_row = $db->fetch($rs,1)) {

	$row['comnm']		= $_row['comnm'];
	$row['pchsno']	= $_row['pchsno'];
	$row['payment_cnt']	= $_row['cnt'];
	$row['tot_emoney']	= $_row['tot_emoney'];
	$row['tot_coupon_dc']		= $_row['tot_coupon_dc'];
	$row['tot_member_dc']		= $_row['tot_member_dc'];
	$row['tot_enuri_dc']		= $_row['tot_enuri_dc'];
	$row['tot_goods_dc']		= $_row['tot_goods_dc'];
	$row['tot_price']	= $_row['tot_price'];
	$row['tot_delivery']		= $_row['tot_delivery'];
	$row['tot_supply']	= $_row['tot_supply'];

	$row['tot_sales']	= $row['tot_price'] - $row['tot_dc'] - $row['tot_emoney'];
	$row['tot_earn']	= $row['tot_sales'] - $row['tot_supply'];

	$total = get_total($total, $row);

	$arRow[] = $row;

	// 차트 데이터
	$chart['data'][1][] = (int)$row['tot_sales'];
	$chart['data'][2][] = (int)$row['tot_supply'];
	$chart['data'][3][] = (int)$row['tot_earn'];

	$_m = max( array($row['tot_sales'],$row['tot_supply'],$row['tot_earn']));
	$chart['y_max'] = ($chart['y_max'] > $_m) ? (int)$chart['y_max'] : $_m;

	$chart['x_label'][] = iconv('EUC-KR','UTF-8',$row['comnm']);

}
$db->free($rs);
//  그래프

if (!empty($chart)) {

	$chart['color'][1] = '#A6A6A6';
	$chart['color'][2] = '#92D050';
	$chart['color'][3] = '#FF0000';

	$chart['Key'][1] = '매출금액';
	$chart['Key'][2] = '매입금액';
	$chart['Key'][3] = '판매이익';

	$ofc = new open_flash_chart();

	foreach($chart['data'] as $k => $data) {
		${'data'} = $data;
		${'bar'.$k} = new bar();
		${'bar'.$k}->colour( $chart['color'][$k] );
		${'bar'.$k}->set_values( $data );
		${'bar'.$k}->key( iconv('euc-kr','utf-8',$chart['Key'][$k]) , 12);

		$ofc->add_element( ${'bar'.$k} );
	}

	$tmp = pow(10,strlen($chart['y_max']) - 1);
	$chart['y_max'] =  ceil($chart['y_max'] / $tmp) * $tmp;

	$y = new y_axis();
	$y->set_range(0, $chart['y_max']);
	$y->set_colours( '#595D63', '#DEDEDE');

	$x = new x_axis();
	$x->set_colours( '#595D63', '#DEDEDE');

	$x_labels = new x_axis_labels();
	$x_labels->set_colour( '#595D63' );
	$x_labels->set_steps( ceil($rs_max / 11) );
	$x_labels->set_labels( $chart['x_label'] );
	$x_labels->set_size( 12 );

	$x->set_labels( $x_labels );

	$ofc->set_x_axis( $x );
	$ofc->set_y_axis( $y );
	$ofc->set_bg_colour( '#FFFFFF' );

	$chart_data_1 = $ofc->toPrettyString();
}
else {
	$chart_data_1 = 'false';
}
?>
<script type="text/javascript" src="common.js"></script>
<script type="text/javascript">

var chart_data_1 = <?=$chart_data_1?>;

function open_flash_chart_data() {
		if (chart_data_1 === false) {
			$('_chart').setStyle({'display':'none'});
			chart_data_1 = {};
		}

		return Object.toJSON(chart_data_1);
}

function fnDisplayChart(type, name) {

	var tab = $('el-chart-tab').childElements()[1];

	// 탭 텍스트 제어
	if (type === false) {
		$('chart-tab-button-overview').src = "../img/teb_graph.gif";
		tab.setStyle({display:'none'});
		var ofc = findSWF("my_chart");
		ofc.load( Object.toJSON(chart_data_1) );
	}
	else {
		$('chart-tab-button-overview').src = "../img/teb_graph_off.gif";
		tab.setStyle({display:'block'});
		tab.update(name);
	}

	var param = '?';
	var el, pair;


	$A( $('frmStatistics').serialize().split('&') ).each(function(el){

		el = decodeURIComponent(el);
		pair = el.split('=');
		param += '&'+ pair[0] +'='+ pair[1];

	});

	if (type) param += '&pchno='+type;

	var ajax = new Ajax.Request( "./indb.statistics.sales.purchase.chart.data.php",
	{
		method: "post",
		parameters: param,
		onComplete: function (response) {

			var json = response.responseText.evalJSON(true);
			var ofc = findSWF("my_chart");
			ofc.load( Object.toJSON(json) );
			$('_chart_loading').setStyle({display : 'none'});
		},
		onCreate : function(){
			$('_chart_loading').setStyle({display : 'block'});
		}
	});

}

function findSWF(movieName) {
  if (navigator.appName.indexOf("Microsoft")!= -1) {
    return window[movieName];
  } else {
    return document[movieName];
  }
}

function fnToggleChart() {
	var el = $('_chart').toggle();
	$('chart-toggle-button').src = el.getStyle('display') == 'none' ? '../img/btn_up.gif' : '../img/btn_down.gif';
}
</script>

<script type="text/javascript">
function fnToggleSettleKind() {
	var i=0;
	var _check = true;

	$$('input[name^=settlekind]').each(function(chk){

		if (i === 0) {
			_check = (chk.checked == true) ? false : true;
		}
		chk.checked = _check;
		i++;
	});
}

function fnDetailStatistic(d) {

	var param = '?';
	var el, pair;
	$A( $('frmStatistics').serialize().split('&') ).each(function(el){
		el = decodeURIComponent(el);
		pair = el.split('=');
		param += '&'+ pair[0] +'='+ pair[1];

	});
	param += '&pchno='+d;

	popupLayer('./statistics.sales.purchase.detail.php'+param,750,450);

}
</script>

<div class="title title_top">사입처(공급처)별 매출통계 <span>사입처(공급처)별 매출통계를 조회/분석 할 수 있습니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=data&no=26')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<script type="text/javascript">
function fnDownloadStatistics() {
	if (confirm('검색된 통계 내역을 다운로드 하시겠습니까?')) {
		var f = document.frmExcelQuery;
		if (f.query.value != '') f.submit();
	}
}
</script>

<form name="frmStatistics" id="frmStatistics" method=get>

	<table class=tb>
	<col class=cellC><col class=cellL><col class=cellC><col class=cellL>
	<tr>
		<td>기간설정</td>
		<td colspan="3">

			<select name="dtkind">
				<option value="orddt" <?=$_GET['dtkind'] == 'orddt' ? 'selected' : ''?>>주문일</option>
				<option value="cdt" <?=$_GET['dtkind'] == 'cdt' ? 'selected' : ''?>>입금일</option>
				<option value="ddt" <?=$_GET['dtkind'] == 'ddt' ? 'selected' : ''?>>배송일</option>
				<option value="confirmdt" <?=$_GET['dtkind'] == 'confirmdt' ? 'selected' : ''?>>배송완료일</option>
			</select>

			<input type="text" name="regdt[]" onclick="calendar(event)" size="12" class="line" value="<?=$_GET['regdt'][0]?>" /> -
			<input type="text" name="regdt[]" onclick="calendar(event)" size="12" class="line" value="<?=$_GET['regdt'][1]?>"/>

			<a href="javascript:setDate('regdt[]',<?=date("Ymd",G_CONST_NOW)?>,<?=date("Ymd",G_CONST_NOW)?>)"><img src="../img/sicon_today.gif" align="absmiddle"/></a>
			<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-7 day",G_CONST_NOW))?>,<?=date("Ymd",G_CONST_NOW)?>)"><img src="../img/sicon_week.gif" align="absmiddle"/></a>
			<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-15 day",G_CONST_NOW))?>,<?=date("Ymd",G_CONST_NOW)?>)"><img src="../img/sicon_twoweek.gif" align="absmiddle"/></a>
			<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-1 month",G_CONST_NOW))?>,<?=date("Ymd",G_CONST_NOW)?>)"><img src="../img/sicon_month.gif" align="absmiddle"/></a>
			<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-2 month",G_CONST_NOW))?>,<?=date("Ymd",G_CONST_NOW)?>)"><img src="../img/sicon_twomonth.gif" align="absmiddle"/></a>
		</td>
	</tr>
	<tr>
		<td>분류설정</td>
		<td colspan="3">
			<script>new categoryBox('cate[]',4,'<?=$category?>');</script>
		</td>
	</tr>
	<tr>
		<td>상품</td>
		<td>
			<select name=skey>
			<option value="OI.goodsnm" <?=$_GET['skey'] == 'OI.goodsnm' ? 'selected' : ''?>>상품명
			<option value="OI.goodsno" <?=$_GET['skey'] == 'OI.goodsno' ? 'selected' : ''?>>고유번호
			<option value="G.goodscd" <?=$_GET['skey'] == 'G.goodscd' ? 'selected' : ''?>>상품코드
			</select>
			<input type=text name="sword" value="<?=$_GET[sword]?>" class="line" style="height:22px">
		</td>
		<td>브랜드</td>
		<td>
			<select name=brandnm>
			<option value="">-- 브랜드 선택 --
			<?
			$bRes = $db->query("select * from ".GD_GOODS_BRAND." order by sort");
			while ($tmp=$db->fetch($bRes)){
			?>
			<option value="<?=$tmp[brandnm]?>" <?=$_GET['brandnm'] == $tmp[brandnm] ? 'selected' : ''?> ><?=$tmp[brandnm]?>
			<? } ?>
			</select>	</td>
	</tr>
	<tr>
		<td>결제수단</td>
		<td colspan="3" class="noline">
			<label><input type="checkbox" name="settlekind[all]" value=1 onClick="nsGodoFormHelper.magic_check(this);" <?=$_GET['settlekind']['all'] ? 'checked' : ''?>>전체</label>
			<label><input type="checkbox" name="settlekind[a]" value=1 onClick="nsGodoFormHelper.magic_check(this);" <?=$_GET['settlekind']['a'] ? 'checked' : ''?>>무통장</label>
			<label><input type="checkbox" name="settlekind[c]" value=1 onClick="nsGodoFormHelper.magic_check(this);" <?=$_GET['settlekind']['c'] ? 'checked' : ''?>>신용카드</label>
			<label><input type="checkbox" name="settlekind[o]" value=1 onClick="nsGodoFormHelper.magic_check(this);" <?=$_GET['settlekind']['o'] ? 'checked' : ''?>>계좌이체</label>
			<label><input type="checkbox" name="settlekind[v]" value=1 onClick="nsGodoFormHelper.magic_check(this);" <?=$_GET['settlekind']['v'] ? 'checked' : ''?>>가상계좌</label>
			<label><input type="checkbox" name="settlekind[h]" value=1 onClick="nsGodoFormHelper.magic_check(this);" <?=$_GET['settlekind']['h'] ? 'checked' : ''?>>핸드폰</label>
			<label><input type="checkbox" name="settlekind[d]" value=1 onClick="nsGodoFormHelper.magic_check(this);" <?=$_GET['settlekind']['d'] ? 'checked' : ''?>>전액할인</label>
		</td>
	</tr>
	</table>

	<div class=button_top><input type=image src="../img/btn_search_s.gif"></div>

</form>

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="height:26px;background:url(../img/teb_bg.gif) repeat-x top left;">
<tr>
	<td id="el-chart-tab">
		<div style="float:left;"><a href="javascript:void(0);" onClick="fnDisplayChart(false)"><img src="../img/teb_graph.gif" id="chart-tab-button-overview"></a></div>
		<div style="display:none;float:left;background:#fff;height:26px;border-top:1px solid #DBDBDB;border-right:1px solid #DBDBDB;line-height:26px;padding:0 10px 0 10px;font-weight:bold;color:#167BBB;">아싸바리</div>
	</td>
	<td align="right"><a href="javascript:void(0);" onClick="fnToggleChart();"><img src="../img/btn_down.gif" id="chart-toggle-button"></a></td>
</tr>
</table>


<!-- 그래프 -->
<div id="_chart" style="margin-top:20px;">
<div id="_chart_loading" style="display:none;position:absolute;background:#fff;width:100%;height:250px;text-align:center;"><img src="../img/loading.gif" style="vertical-align:middle;"><img src="../img/blank.gif" width="1" height="250" style="vertical-align:middle;"></div>
<script type="text/javascript">
var param = {
	'id':'my_chart',
	'width':'100%',
	'height':'250'
};
flash_chart(param);
</script>
</div>
<!-- 그래프 -->

<div style="padding-top:15px"></div>

<table width=100% cellpadding=0 cellspacing=0 class="statistics-list">
<tr><td class=rnd colspan=12></td></tr>
<tr class=rndbg>
	<th>그래프</th>
	<th>사입처</th>
	<th>건수</th>
	<th>적립금적용</th>
	<th>회원할인</th>
	<th>쿠폰할인</th>
	<th>상품할인</th>
	<th>에누리</th>
	<th>주문금액</th>
	<!--th>결제금액 (배송비포함)</th-->
	<th>매출금액</th>
	<th>매입금액</th>
	<th>판매이익</th>
</tr>
<tr><td class=rnd colspan=12></td></tr>

<?
for ($i=0,$m=sizeof($arRow);$i<$m;$i++) {
	$row = $arRow[$i];
?>
<tr height=25>
	<td class="cell1"><a href="javascript:void(0);" onClick="fnDisplayChart('<?=$row['pchsno']?>','<?=$row['comnm']?>');"><img src="../img/btn_graph.gif"></td>
	<td class="cell1"><a href="javascript:void(0);" onClick="fnDetailStatistic('<?=$row['pchsno']?>');"><?=$row['comnm']?></a></td>
	<td class="numeric ar"><?=number_format($row['payment_cnt'])?></td>
	<td class="cell1 numeric highlight  ar"><?=number_format($row['tot_emoney'])?></td>
	<td class="numeric highlight  ar"><?=number_format($row['tot_member_dc'])?></td>
	<td class="numeric highlight  ar"><?=number_format($row['tot_coupon_dc'])?></td>
	<td class="numeric highlight  ar"><?=number_format($row['tot_goods_dc'])?></td>
	<td class="numeric highlight  ar"><?=number_format($row['tot_enuri_dc'])?></td>
	<td class="cell1 numeric highlight  ar"><?=number_format($row['tot_price'])?></td>
	<td class="cell1 numeric highlight  ar"><?=number_format($row['tot_sales'])?></td>
	<td class="numeric highlight  ar"><?=number_format($row['tot_supply'])?></td>
	<td class="cell1 numeric highlight  ar"><?=number_format($row['tot_earn'])?></td>
</tr>
<tr><td colspan=12 class=rndline></td></tr>
<? } ?>
<tr><td colspan=12 bgcolor=A3A3A3></td></tr>
<tfoot>
<tr>
	<th colspan="2">합계</th>
	<td class="numeric ar"><?=number_format($total['payment_cnt'])?></td>
	<td class="numeric highlight  ar"><?=number_format($total['tot_emoney'])?></td>
	<td class="numeric highlight  ar"><?=number_format($total['tot_member_dc'])?></td>
	<td class="numeric highlight  ar"><?=number_format($total['tot_coupon_dc'])?></td>
	<td class="numeric highlight  ar"><?=number_format($total['tot_goods_dc'])?></td>
	<td class="numeric highlight  ar"><?=number_format($total['tot_enuri_dc'])?></td>
	<td class="numeric highlight  ar"><?=number_format($total['tot_price'])?></td>
	<td class="numeric highlight  ar"><?=number_format($total['tot_sales'])?></td>
	<td class="numeric highlight  ar"><?=number_format($total['tot_supply'])?></td>
	<td class="numeric highlight  ar"><?=number_format($total['tot_earn'])?></td>
</tr>
</tfoot>
<tr><td colspan=12 class=rndline></td></tr>
</table>

<table width="100%" style="margin-top:10px;">
<tr>
	<td width="" align="right"><a href="javascript:void(0);" onClick="fnDownloadStatistics()"><img src="../img/btn_download_s.gif"></a></td>
</tr>
</table>

<p />
<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">주문일, 입금일, 배송일, 배송완료일 기준별로 기간설정이 가능합니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">사입처(공급처)명을 클릭하면 해당 사입처(공급처)의 상품별 매출이력을 확인 하실 수 있습니다</td></tr>
<tr><td height="8"></td></tr>
<tr><td><span class="def1">&nbsp;&nbsp;<b>통계 리스트</span></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">적립금 적용 : 주문 결제시에 사용된 적립금 내역</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">할인 : 주문 결제시에 적용된 회원할인, 쿠폰할인 등이 포함된 할인금액 내역</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">주문금액 : 적립금, 할인, 배송비가 적용되지 않은 상품의 주문금액 내역</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">매출금액 : 상품 주문금액에서 적립금, 할인, 배송비가 제외된 총 매출금액 내역</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">매입금액 : 상품의 매입금액(상품등록시 매입금액을 정확히 입력하여야 판매이익을 확인할 수 있습니다.)</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">판매이익 : 매출금액에서 매입금액을 제외한 금액 내역</td></tr>
<tr><td height="8"></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">판매이익은  카드수수료, 배송료, VAT 등 기타 비용들이 포함되지 않은 전체매출에서 상품매입 금액을 제외한 통계 금액으로, 순매출(순이익)금액과는 차이가 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">시스템 과부화를 고려하여 일별 매출통계는 최대 1년 단위로 나누어 검색하시고, 엑셀로 파일로 다운로드 하여 활용하시기를 권장 드립니다.</td></tr>
<tr><td height="8"></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">위 통계 데이터는 주문취소 금액과 메인쇼핑몰(e나무)외 다른 판매채널의 주문관련 금액이 제외된 통계자료 입니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<script>table_design_load();</script>

<form name="frmExcelQuery" id="frmExcelQuery" method="post" action="indb.excel.statistics.sales.purchase.php" target="ifrmHidden">
<input type="hidden" name="query" value="<?=base64_encode($query)?>">
</form>

<? include "../_footer.php"; ?>
