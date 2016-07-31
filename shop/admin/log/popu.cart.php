<?

$location = "통계관리 > 장바구니 분석";
include "../_header.php";
include "../../lib/page.class.php";

### 공백 제거
$_GET[sword] = trim($_GET[sword]);

$year = ($_GET[year]) ? $_GET[year] : date("Y");
$month = ($_GET[month]) ? sprintf("%02d",$_GET[month]) : date("m");

$stype = ($_GET[stype]) ? $_GET[stype] : 'm';
$sdate_s = ($_GET[regdt][0]) ? $_GET[regdt][0] : date('Ymd',strtotime('-7 day'));
$sdate_e = ($_GET[regdt][1]) ? $_GET[regdt][1] : date('Ymd');

if (checkStatisticsDateRange($sdate_s, $sdate_e) > 365) {
	msg('조회기간 설정은 최대 1년을 넘지 못합니다. 기간 확인후 재설정 해주세요.',$_SERVER['PHP_SELF']);exit;
}

$srunout = ($_GET[srunout]) ? $_GET[srunout] : '';
$sbuy = ($_GET[sbuy]) ? $_GET[sbuy] : '';

$_GET[page_num] = $_GET[page_num] ? $_GET[page_num] : 20;
$_GET[page] = $_GET[page] ? $_GET[page] : 1;
$selected[page_num][$_GET[page_num]] = "selected";
$selected[skey][$_GET[skey]] = "selected";
$selected[year][$year] = "selected";
$selected[month][$month] = "selected";

$getString="year=$_GET[year]&month=$_GET[month]&stype=$_GET[stype]&regdt[0]=".$_GET[regdt][0]."&regdt[1]=".$_GET[regdt][1]."&skey=$_GET[skey]&sword=$_GET[sword]";

$date = $year."-".sprintf("%02d",$month);
$last = date("t",strtotime($date."-01"));

if ($srunout == '1') $where[] = "(G.runout = 1 OR (G.usestock = 'o' AND G.usestock IS NOT NULL AND G.totstock < 1))";
elseif ($srunout == '-1') $where[] = "(G.runout <> 1 AND (G.usestock <> 'o' OR G.usestock IS NULL OR G.totstock > 0))";

if ($sbuy != '') {
	$where[] = "CT.is_buy = '".($sbuy == '1' ? '1' : '0')."'";
}

if ($_GET[sword]) $where[] = "$_GET[skey] like '%$_GET[sword]%'";


if ($stype == 'm') {
	$where[] = " DATE_FORMAT(CT.regdt, '%Y-%m') = '$date' ";
}
else if ($sdate_s & $sdate_e){
	$where[] = " ( DATE_FORMAT(CT.regdt,'%Y%m%d') >= '".($sdate_s)."' and DATE_FORMAT(CT.regdt,'%Y%m%d') <= '".($sdate_e)."')";
}



$pg = new Page($_GET[page],$_GET[page_num]);

$pg->field = "
DISTINCT G.goodsno, G.goodsnm, G.img_s, G.totstock, G.regdt, G.icon, G.usestock, G.runout,
O.price,
COUNT( DISTINCT CT.uid) AS `cart_cnt`,
COUNT(  DISTINCT IF(CT.m_id != '',CT.m_id,null) ) AS `cart_mb`
";

$db_table = "
".GD_CART." AS CT
INNER JOIN ".GD_GOODS." AS G
ON CT.goodsno = G.goodsno
INNER JOIN ".GD_GOODS_OPTION." AS O
ON G.goodsno = O.goodsno AND O.link = 1 and go_is_deleted <> '1'
";
$groupby = " GROUP BY G.goodsno ";
$orderby = " `cart_cnt` DESC";
$pg->setQuery($db_table,$where,$orderby,$groupby);

$pg->exec();
$res = $db->query($pg->query);
?>
<script type="text/javascript">
function fnDownloadStatistics() {
	if (confirm('검색된 통계 내역을 다운로드 하시겠습니까?')) {
		var f = document.frmList;
		f.method = 'post'; f.action = './indb.excel.popu.cart.php'; f.target = 'ifrmHidden';
		f.submit();
		f.action = ''; f.target = ''; f.method = '';
	}
}
</script>
<div class="title title_top">장바구니 분석 <span>쇼핑몰 장바구니에 담긴 상품별 순위 확인 및 분석 할 수 있습니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=data&no=32')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<form name=frmList method=get>
<input type="hidden" name="category" value="<?=$_GET['category']?>" />
<table class=tb>
<col class=cellC><col class=cellL><col class=cellC><col class=cellL>

<tr>
	<td>기간설정</td>
	<td colspan="3">
	<div>
		<label class="noline"><input type="radio" name="stype" value="m" <?=$stype == 'm' ? 'checked' : ''?>>월별조회</label>

		<select name=year>
		<? for ($i=0;$i<3;$i++){ $y = date("Y") - $i; ?>
		<option value="<?=$y?>" <?=$selected[year][$y]?>><?=$y?>
		<? } ?>
		</select>년
		<select name=month>
		<?
		for ($i=1;$i<=12;$i++){
			$tmp = sprintf("%02d",$i);
		?>
		<option value="<?=$i?>" <?=$selected[month][$tmp]?>><?=$i?>
		<? } ?>
		</select>월
	</div>

	<div style="margin-top:5px;">
		<label class="noline"><input type="radio" name="stype" value="d" <?=$stype == 'd' ? 'checked' : ''?>>일별조회</label>

		<input type=text name=regdt[] value="<?=$sdate_s?>" onclick="calendar(event)" onkeydown="onlynumber()" class="cline"> -
		<input type=text name=regdt[] value="<?=$sdate_e?>" onclick="calendar(event)" onkeydown="onlynumber()" class="cline">
		<a href="javascript:setDate('regdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align=absmiddle></a>
		<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align=absmiddle></a>
		<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align=absmiddle></a>
		<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align=absmiddle></a>
		<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align=absmiddle></a>
	</div>

	</td>
</tr>
<tr>
	<td>상품검색</td>
	<td colspan=3>
	<select name=skey>
	<option value="G.goodsnm" <?=$selected[skey]['G.goodsnm']?>>상품명
	<option value="G.goodsno" <?=$selected[skey]['G.goodsno']?>>고유번호
	<option value="G.goodscd" <?=$selected[skey]['G.goodscd']?>>상품코드
	<option value="G.keyword" <?=$selected[skey]['G.keyword']?>>유사검색어
	</select>
	<input type=text name="sword" value="<?=$_GET[sword]?>" class="line" style="height:22px">
	</td>
</tr>
</tr>
	<td>품절여부</td>
	<td class="noline">
		<label><input type="radio" name="srunout" value="" <?=$srunout == '' ? 'checked' : ''?>>전체</label>
		<label><input type="radio" name="srunout" value="1" <?=$srunout == '1' ? 'checked' : ''?>>품절상품</label>
		<label><input type="radio" name="srunout" value="-1" <?=$srunout == '-1' ? 'checked' : ''?>>품절상품제외</label>
	</td>
	<td>구매여부</td>
	<td class="noline">
		<label><input type="radio" name="sbuy" value="" <?=$sbuy == '' ? 'checked' : ''?>>전체</label>
		<label><input type="radio" name="sbuy" value="1" <?=$sbuy == '1' ? 'checked' : ''?>>구매완료상품</label>
		<label><input type="radio" name="sbuy" value="-1" <?=$sbuy == '-1' ? 'checked' : ''?>>구매완료상품 제외</label>
	</td>
</tr>
</table>
<div class=button_top><input type=image src="../img/btn_search_s.gif"></div>
<div style="padding-top:15px"></div>

<table width=100% cellpadding=0 cellspacing=0>
<tr>

	<td align=right>

	<table cellpadding=0 cellspacing=0 border=0>
	<tr>
		<td style="padding-left:20px">
		<img src="../img/sname_output.gif" align=absmiddle>
		<select name=page_num onchange="this.form.submit()">
		<?
		$r_pagenum = array(10,20,40,60,100);
		foreach ($r_pagenum as $v){
		?>
		<option value="<?=$v?>" <?=$selected[page_num][$v]?>><?=$v?>개 출력
		<? } ?>
		</select>
		</td>
	</tr>
	</table>

	</td>
</tr>
</table>
</form>


<table width="100%" cellpadding="0" cellspacing="0">
<col width="60">
<col width="120">
<col width="40">
<col width="10">
<col width="">
<col width="100">
<col width="100">
<col width="80">
<tr><td class="rnd" colspan="10"></td></tr>
<tr class="rndbg">
	<th>순위</th>
	<th>고객수(회원/비회원)</th>
	<th></th>
	<th></th>
	<th>상품명</th>
	<th>등록일</th>
	<th>가격</th>
	<th>재고</th>
</tr>
<tr><td class=rnd colspan="10"></td></tr>
<?
$rank = ($_GET['page'] - 1) * $_GET['page_num'];

while ($row = $db->fetch($res,1)) {
	$icon = setIcon($row[icon],$row[regdt],"../");
	if ($row[usestock] && $row[totstock] < 1) $row[runout] = 1;
?>
<tr height=25>
	<td align=center><font class="ver8" color="444444"><?=++$rank?></font></td>
	<td align=center><font class=ver81 color=444444><a href="javascript:popup('popu.cart.detail.php?goodsno=<?=$row[goodsno]?>&<?=$getString?>',850,650)"><?=number_format($row[cart_cnt])?> (<?=number_format($row[cart_mb])?> / <?=number_format($row[cart_cnt] - $row[cart_mb])?>)</a></td>

	<td style="border:1px #e9e9e9 solid;"><a href="../../goods/goods_view.php?goodsno=<?=$row[goodsno]?>" target=_blank><?=goodsimg($row[img_s],40,'',1)?></a></td>
	<td></td>
	<td>
	<a href="javascript:void(0);" onClick="<?=getPermission('goods') ? 'popup(\'../goods/popup.register.php?mode=modify&goodsno='.$row[goodsno].'\',850,600)' : 'alert(\'접근권한이 없습니다. 관리자 권한설정을 확인하여 주세요.\')' ?>;"><font color=303030><?=$row[goodsnm]?></font></a>
	<? if ($icon){ ?><div style="padding-top:3px"><?=$icon?></div><? } ?>
	<? if ($row[runout]){ ?><div style="padding-top:3px"><img src="../../data/skin/<?=$cfg[tplSkin]?>/img/icon/good_icon_soldout.gif"></div><? } ?>
	</td>

	<td align=center><font class=ver81 color=444444><?=substr($row[regdt],0,10)?></td>
	<td align=center><font class=ver81 color=444444><?=number_format($row[price])?></td>
	<td align=center><font class=ver81 color=444444><?=number_format($row[totstock])?></td>

</tr>
<tr><td colspan="10" class="rndline"></td></tr>
<?
}
?>
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
<table cellpadding=1 cellspacing=0 border=0 class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle">고객의 장바구니에 담긴 상품 분석을 통하여 고객의 구매성향 파악할 수 있으며, 판매 마케팅을 위한 이벤트 기획시에 효과적으로 활용할 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">시스템 과부화를 고려하여 상품이 많은 경우 검색기간은 최대 1년 단위로 나누어 검색하시고, 엑셀로 파일로 다운로드 하여 활용하시기를 권장 드립니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01','#F7F7F7')</script>
<script>table_design_load();</script>

<? include "../_footer.php"; ?>