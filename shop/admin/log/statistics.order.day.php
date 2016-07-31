<?
$location = "통계관리 > 주문분석 > 일별 주문통계";
include "../_header.php";

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

	if ((int)$_GET['amount'][0] > 0) $where[] = " O.prn_settleprice >= ".(int)$_GET['amount'][0];
	if ((int)$_GET['amount'][1] > 0) $where[] = " O.prn_settleprice <= ".(int)$_GET['amount'][1];

// sql
	if (empty($_GET['dtkind'])) $_GET['dtkind'] = $dtkind = 'cdt';
	else $dtkind = $_GET['dtkind'];

	$sub_query = "
	SELECT

		DATE_FORMAT(O.$dtkind,'%Y-%m-%d') AS `date`,
		O.ordno,O.step2,O.step,	O.prn_settleprice

	FROM ".GD_ORDER." AS O
	INNER JOIN ".GD_ORDER_ITEM." AS OI
	ON O.ordno = OI.ordno
	LEFT JOIN ".GD_GOODS." AS G
	ON OI.goodsno = G.goodsno
	";

	if ($category){
		$sub_query .= "
		LEFT JOIN ".GD_GOODS_LINK." AS LNK
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

	$sub_query .= ' WHERE '.implode(' AND ', $where);
	$sub_query .= ' GROUP BY O.ordno ';



	$query = "
	SELECT
		SUB.`date`,

		COUNT( IF(SUB.step2 >= 40 AND SUB.step2 <= 49,1,null) )				  AS `cnt_step_cancel`,
		COUNT( IF(SUB.step2 < 40 AND SUB.step = '0',1,null) ) AS `cnt_step_0`,
		COUNT( IF(SUB.step2 < 40 AND SUB.step = '1',1,null) ) AS `cnt_step_1`,
		COUNT( IF(SUB.step2 < 40 AND SUB.step = '2',1,null) ) AS `cnt_step_2`,
		COUNT( IF(SUB.step2 < 40 AND SUB.step = '3',1,null) ) AS `cnt_step_3`,
		COUNT( IF(SUB.step2 < 40 AND SUB.step = '4',1,null) ) AS `cnt_step_4`,

		SUM( IF(SUB.step2 >= 40 AND SUB.step2 <= 49, SUB.prn_settleprice ,0) )				 AS `amount_step_cancel`,
		SUM( IF(SUB.step2 < 40 AND SUB.step = '0',SUB.prn_settleprice,0) ) AS `amount_step_0`,
		SUM( IF(SUB.step2 < 40 AND SUB.step = '1',SUB.prn_settleprice,0) ) AS `amount_step_1`,
		SUM( IF(SUB.step2 < 40 AND SUB.step = '2',SUB.prn_settleprice,0) ) AS `amount_step_2`,
		SUM( IF(SUB.step2 < 40 AND SUB.step = '3',SUB.prn_settleprice,0) ) AS `amount_step_3`,
		SUM( IF(SUB.step2 < 40 AND SUB.step = '4',SUB.prn_settleprice,0) ) AS `amount_step_4`

	FROM
	(
		".$sub_query."

	) AS SUB
	";
	$query .= ' GROUP BY `date` ';
	$query .= ' ORDER BY `date` ';

// 쿼리
$rs = $db->query($query);
$rs_max = $db->count_($rs);
$total = $arRow = array();

while ($row = $db->fetch($rs,1)) {
	$total = get_total($total, $row);
	$arRow[] = $row;
}
$db->free($rs);
?>

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
	param += '&date='+d;

	popupLayer('./statistics.order.day.detail.php'+param,750,450);

}
</script>

<div class="title title_top">일별 주문통계 <span>일별 주문통계를 조회/분석 할 수 있습니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=data&no=28')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

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
		<td>결제금액</td>
		<td colspan="3">
			<input type="text" name="amount[]" size="12" class="line" value="<?=$_GET['amount'][0]?>" />원 -
			<input type="text" name="amount[]" size="12" class="line" value="<?=$_GET['amount'][1]?>"/>원
		</td>
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

<div style="padding-top:15px"></div>

<table width=100% cellpadding=0 cellspacing=0 class="statistics-list">
<tr><td class=rnd colspan=15></td></tr>
<tr class=rndbg>
	<th>날짜</th>
	<th colspan="2">총주문건</th>
	<th colspan="2">주문접수</th>
	<th colspan="2">입금확인</th>
	<th colspan="2">배송준비</th>
	<th colspan="2">배송중</th>
	<th colspan="2">배송완료</th>
	<th colspan="2">주문취소</th>

</tr>
<tr><td class=rnd colspan=15></td></tr>
<tr height=25 align="center">
	<th class="">일별</th>
	<th class="cell1 highlight">건수</th>
	<th class="cell1 highlight">금액</th>
	<th>건수</th>
	<th>금액</th>
	<th class="cell1">건수</th>
	<th class="cell1">금액</th>
	<th>건수</th>
	<th>금액</th>
	<th class="cell1">건수</th>
	<th class="cell1">금액</th>
	<th>건수</th>
	<th>금액</th>
	<th class="cell1">건수</th>
	<th class="cell1">금액</th>
</tr>
<tr><td class=rnd colspan=15></td></tr>

<?
for ($i=0,$m=sizeof($arRow);$i<$m;$i++) {
	$row = $arRow[$i];
?>
<tr height=25>
	<td class=""><a href="javascript:void(0);" onClick="fnDetailStatistic('<?=$row['date']?>');"><?=$row['date']?></a></td>
	<td class="cell1 numeric highlight  ar"><?=number_format($row['cnt_step_0'] + $row['cnt_step_1'] + $row['cnt_step_2'] + $row['cnt_step_3'] + $row['cnt_step_4'] + $row['cnt_step_cancel'])?></td>
	<td class="cell1 numeric highlight  ar"><?=number_format($row['amount_step_0'] + $row['amount_step_1'] + $row['amount_step_2'] + $row['amount_step_3'] + $row['amount_step_4'] + $row['amount_step_cancel'])?></td>

	<td class="numeric ar"><?=number_format($row['cnt_step_0'])?></td>
	<td class="numeric ar"><?=number_format($row['amount_step_0'])?></td>
	<td class="cell1 numeric ar"><?=number_format($row['cnt_step_1'])?></td>
	<td class="cell1 numeric ar"><?=number_format($row['amount_step_1'])?></td>
	<td class="numeric ar"><?=number_format($row['cnt_step_2'])?></td>
	<td class="numeric ar"><?=number_format($row['amount_step_2'])?></td>
	<td class="cell1 numeric ar"><?=number_format($row['cnt_step_3'])?></td>
	<td class="cell1 numeric ar"><?=number_format($row['amount_step_3'])?></td>
	<td class="numeric ar"><?=number_format($row['cnt_step_4'])?></td>
	<td class="numeric ar"><?=number_format($row['amount_step_4'])?></td>
	<td class="cell1 numeric ar"><?=number_format($row['cnt_step_cancel'])?></td>
	<td class="cell1 numeric ar"><?=number_format($row['amount_step_cancel'])?></td>
</tr>
<tr><td colspan=15 class=rndline></td></tr>
<? } ?>
<tr><td colspan=15 bgcolor=A3A3A3></td></tr>
<tfoot>
<tr>
	<th>합계</th>
	<td class="numeric highlight  ar"><?=number_format($total['cnt_step_0'] + $total['cnt_step_1'] + $total['cnt_step_2'] + $total['cnt_step_3'] + $total['cnt_step_4'] + $total['cnt_step_cancel'])?></td>
	<td class="numeric highlight  ar"><?=number_format($total['amount_step_0'] + $total['amount_step_1'] + $total['amount_step_2'] + $total['amount_step_3'] + $total['amount_step_4'] + $total['amount_step_cancel'])?></td>

	<td class="numeric ar"><?=number_format($total['cnt_step_0'])?></td>
	<td class="numeric ar"><?=number_format($total['amount_step_0'])?></td>
	<td class="numeric ar"><?=number_format($total['cnt_step_1'])?></td>
	<td class="numeric ar"><?=number_format($total['amount_step_1'])?></td>
	<td class="numeric ar"><?=number_format($total['cnt_step_2'])?></td>
	<td class="numeric ar"><?=number_format($total['amount_step_2'])?></td>
	<td class="numeric ar"><?=number_format($total['cnt_step_3'])?></td>
	<td class="numeric ar"><?=number_format($total['amount_step_3'])?></td>
	<td class="numeric ar"><?=number_format($total['cnt_step_4'])?></td>
	<td class="numeric ar"><?=number_format($total['amount_step_4'])?></td>
	<td class="numeric ar"><?=number_format($total['cnt_step_cancel'])?></td>
	<td class="numeric ar"><?=number_format($total['amount_step_cancel'])?></td>
</tr>
</tfoot>
<tr><td colspan=10 class=rndline></td></tr>
</table>

<table width="100%" style="margin-top:10px;">
<tr>

	<td width="" align="right"><a href="javascript:void(0);" onClick="fnDownloadStatistics()"><img src="../img/btn_download_s.gif"></a></td>
</tr>
</table>

<p />

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">날짜를 클릭하면 해당 일자의 시간대별 주문통계를 확인 하실 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">시스템 과부화를 고려하여 일별 매출통계는 최대 1년 단위로 나누어 검색하시고, 엑셀로 파일로 다운로드 하여 활용하시기를 권장 드립니다.</td></tr>
<tr><td height="8"></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">위 통계 데이터는 메인쇼핑몰(e나무)외 다른 판매채널의 주문관련 금액이 제외된 통계자료 입니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<script>table_design_load();</script>

<form name="frmExcelQuery" id="frmExcelQuery" method="post" action="indb.excel.statistics.order.day.php" target="ifrmHidden">
<input type="hidden" name="query" value="<?=base64_encode($query)?>">
</form>

<? include "../_footer.php"; ?>
