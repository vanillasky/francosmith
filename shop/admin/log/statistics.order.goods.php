<?
$location = "통계관리 > 주문분석 > 상품별 주문통계";
include "../_header.php";
include "../../lib/page.class.php";


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

	$_param = array(
		$dtkind,
		Core::helper('Date')->min($sdate_s),
		Core::helper('Date')->max($sdate_e)
	);

	$where[] = vsprintf("O.%s between '%s' and '%s'", $_param);

	$query .= ' WHERE '.implode(' AND ', $where);
	$query .= ' GROUP BY OI.goodsno ';

if (!$_GET[page_num]) $_GET[page_num] = 10;
if (!$_GET[page]) $_GET[page] = 1;

$pg = new Page($_GET[page],$_GET[page_num]);

$pg->field = "
	OI.goodsno, OI.goodsnm, OI.price, G.img_s,

	COUNT( IF(O.step2 >= 40 AND O.step2 <= 49,1,null) )				  AS `cnt_step_cancel`,
	COUNT( IF(O.step2 < 40 AND O.step = '0',1,null) ) AS `cnt_step_0`,
	COUNT( IF(O.step2 < 40 AND O.step = '1',1,null) ) AS `cnt_step_1`,
	COUNT( IF(O.step2 < 40 AND O.step = '2',1,null) ) AS `cnt_step_2`,
	COUNT( IF(O.step2 < 40 AND O.step = '3',1,null) ) AS `cnt_step_3`,
	COUNT( IF(O.step2 < 40 AND O.step = '4',1,null) ) AS `cnt_step_4`
";

$db_table = "
".GD_ORDER." AS O
	INNER JOIN ".GD_ORDER_ITEM." AS OI
	ON O.ordno = OI.ordno
	LEFT JOIN ".GD_GOODS." AS G
	ON OI.goodsno = G.goodsno
";

if ($category){
	$db_table .= "
	LEFT JOIN ".GD_GOODS_LINK." AS LNK
	ON OI.goodsno=LNK.goodsno
	";

	// 상품분류 연결방식 전환 여부에 따른 처리
	$where[]	= getCategoryLinkQuery('LNK.category', $category, 'where');
}

$pg->cntQuery = 'SELECT COUNT( DISTINCT OI.goodsno) FROM '.$db_table.' WHERE '.implode(' AND ', $where);
$pg->setQuery($db_table,$where,'','GROUP BY OI.goodsno');
$pg->exec();
$rs = $db->query($pg->query);
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
	param += '&goodsno='+d;

	popupLayer('./statistics.order.goods.detail.php'+param,750,550);

}
</script>

<div class="title title_top">상품별 주문통계 <span>상품별 주문통계를 조회/분석, 상품 옵션별 주문 현황을 확인할 수 있습니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=data&no=30')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

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

	<div style="padding-top:15px"></div>

	<table width="100%" cellpadding=0 cellspacing=0 border=0>
	<tr>
		<td align="right">
		<img src="../img/sname_output.gif" align=absmiddle>
		<select name=page_num onchange="this.form.submit()">
		<?
		$r_pagenum = array(10,20,40,60,100);
		foreach ($r_pagenum as $v){
		?>
		<option value="<?=$v?>" <?=$_GET[page_num] == $v ? 'selected' : ''?>><?=$v?>개 출력
		<? } ?>
		</select>
		</td>
	</tr>
	</table>

</form>

<div style="padding-top:15px"></div>

<table width=100% cellpadding=0 cellspacing=0 class="statistics-list">
<tr><td class=rnd colspan=12></td></tr>
<tr class=rndbg>
	<th>번호</th>
	<th></th>
	<th></th>
	<th>상품명</th>
	<th>가격</th>

	<th>총주문건</th>
	<th>주문접수</th>
	<th>입금확인</th>
	<th>배송준비</th>
	<th>배송중</th>
	<th>배송완료</th>
	<th>주문취소</th>
</tr>
<tr><td class=rnd colspan=12></td></tr>

<?
for ($i=0,$m=sizeof($arRow);$i<$m;$i++) {
	$row = $arRow[$i];
?>
<tr height=25>
	<td><?=$pg->idx--?></td>
	<td class="goods-image"><a href="javascript:void(0);" onClick="fnDetailStatistic('<?=$row['goodsno']?>');"><?=goodsimg($row['img_s'],40,'',1)?></a></td>
	<td width="10"></td>
	<td class="goods-name al"><?=($row['goodsnm'])?></td>
	<td class="numeric highlight "><?=number_format($row['price'])?></td>

	<td class="cell1 numeric highlight "><?=number_format($row['cnt_step_0'] + $row['cnt_step_1'] + $row['cnt_step_2'] + $row['cnt_step_3'] + $row['cnt_step_4'] + $row['cnt_step_cancel'])?></td>

	<td><?=number_format($row['cnt_step_0'])?></td>
	<td><?=number_format($row['cnt_step_1'])?></td>
	<td><?=number_format($row['cnt_step_2'])?></td>
	<td><?=number_format($row['cnt_step_3'])?></td>
	<td><?=number_format($row['cnt_step_4'])?></td>
	<td><?=number_format($row['cnt_step_cancel'])?></td>
</tr>
<tr><td colspan=12 class=rndline></td></tr>
<? } ?>
<tr><td colspan=12 bgcolor=A3A3A3></td></tr>
<tfoot>
<tr>
	<th colspan="5">합계</th>
	<td class="numeric highlight "><?=number_format($total['cnt_step_0'] + $total['cnt_step_1'] + $total['cnt_step_2'] + $total['cnt_step_3'] + $total['cnt_step_4'] + $total['cnt_step_cancel'])?></td>
	<td><?=number_format($total['cnt_step_0'])?></td>
	<td><?=number_format($total['cnt_step_1'])?></td>
	<td><?=number_format($total['cnt_step_2'])?></td>
	<td><?=number_format($total['cnt_step_3'])?></td>
	<td><?=number_format($total['cnt_step_4'])?></td>
	<td><?=number_format($total['cnt_step_cancel'])?></td>
</tr>
</tfoot>
<tr><td colspan=12 class=rndline></td></tr>
</table>

<table width="100%" style="margin-top:10px;">
<tr>
	<td width="20%" align="left">&nbsp;</td>
	<td width="60%" align="center">
	<?=$pg->page[navi]?>
	</td>
	<td width="20%" align="right"><a href="javascript:void(0);" onClick="fnDownloadStatistics()"><img src="../img/btn_download_s.gif"></a></td>
</tr>
</table>

<p />
<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">상품을 클릭하면 해당 상품의 옵션별 주문통계를 확인 하실 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">시스템 과부화를 고려하여 일별 매출통계는 최대 1년 단위로 나누어 검색하시고, 엑셀로 파일로 다운로드 하여 활용하시기를 권장 드립니다.</td></tr>
<tR><td><img src="../img/icon_list.gif" align="absmiddle">* 위 통계 데이터는 메인쇼핑몰(e나무)외 다른 판매채널(체크아웃, 쇼플, 옥션iPay)의 주문관련 금액이 제외된 통계자료 입니다.</td></tr>
</table>


</div>
<script>cssRound('MSG01')</script>

<script>table_design_load();</script>

<form name="frmExcelQuery" id="frmExcelQuery" method="post" action="indb.excel.statistics.order.goods.php" target="ifrmHidden">
<input type="hidden" name="query" value="<?=base64_encode($pg->query)?>">
</form>

<? include "../_footer.php"; ?>
