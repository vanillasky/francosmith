<?

$location = "통계관리 > 상품 페이지뷰 분석 ";
include "../_header.php";
include "../../lib/page.class.php";


$year = ($_GET[year]) ? $_GET[year] : date("Y");
$month = ($_GET[month]) ? sprintf("%02d",$_GET[month]) : date("m");

$stype = ($_GET[stype]) ? $_GET[stype] : 'm';
$sdate_s = ($_GET[regdt][0]) ? $_GET[regdt][0] : date('Ymd',strtotime('-7 day'));
$sdate_e = ($_GET[regdt][1]) ? $_GET[regdt][1] : date('Ymd');

if (checkStatisticsDateRange($sdate_s, $sdate_e) > 365) {
	msg('조회기간 설정은 최대 1년을 넘지 못합니다. 기간 확인후 재설정 해주세요.',$_SERVER['PHP_SELF']);exit;
}

$srunout = ($_GET[srunout]) ? $_GET[srunout] : '';

$_GET[page] = $_GET[page] ? $_GET[page] : 1;
$_GET[page_num] = $_GET[page_num] ? $_GET[page_num] : 20;
$selected[page_num][$_GET[page_num]] = "selected";
$selected[year][$year] = "selected";
$selected[month][$month] = "selected";
$selected[brandno][$_GET[brandno]] = "selected";

$date = $year."-".sprintf("%02d",$month);
$last = date("t",strtotime($date."-01"));

if ($_GET[brandno]) $where[] = "brandno='$_GET[brandno]'";
if ($_GET[cate]){
	$category = array_notnull($_GET[cate]);
	$category = $category[count($category)-1];
}

$where[] = '1=1';

if ($stype == 'm') {
	$where[] = " PGV.date like '$date%' ";
}
else if ($sdate_s & $sdate_e){
	$where[] = " PGV.date >= '".(date('Y-m-d', strtotime($sdate_s)))."' AND PGV.date <= '".(date('Y-m-d',strtotime($sdate_e)))."'";
}

if ($srunout == '1') $where[] = "(G.runout = 1 OR (G.usestock = 'o' AND G.usestock IS NOT NULL AND G.totstock < 1))";
elseif ($srunout == '-1') $where[] = "G.runout <> 1 AND (G.usestock <> 'o' OR G.usestock IS NULL OR G.totstock > 0)";


$pg = new Page($_GET[page],$_GET[page_num]);

$pg->field = "
	DISTINCT PGV.goodsno, PGV.date,  SUM(PGV.cnt) AS `cnt`,
	G.goodsno, G.goodsnm, G.regdt, G.img_s, G.totstock, G.runout, G.usestock, G.icon,
	GO.price
";

$db_table = "
gd_goods_pageview AS PGV
INNER JOIN gd_goods AS G
ON PGV.goodsno = G.goodsno
INNER JOIN gd_goods_option AS GO
ON G.goodsno = GO.goodsno AND GO.link = 1 and go_is_deleted <> '1'
";

if ($category){
	$db_table .= " left join ".GD_GOODS_LINK." c on G.goodsno=c.goodsno ";

	// 상품분류 연결방식 전환 여부에 따른 처리
	$where[]	= getCategoryLinkQuery('c.category', $category, 'where');
}

$groupby = " GROUP BY PGV.goodsno ";
$orderby = " `cnt` DESC";
$pg->setQuery($db_table,$where,$orderby,$groupby);

$pg->exec();
$res = $db->query($pg->query);

// 총 카운트 수 (group by 하지 않은 쿼리 실행후 cnt 필드)
$tmp = $db->fetch("SELECT ".$pg->field.' FROM '.$db_table.' WHERE '.@implode(' AND ', $where));
$total_cnt = $tmp['cnt'];
?>
<script type="text/javascript">
function fnDownloadStatistics() {
	if (confirm('검색된 통계 내역을 다운로드 하시겠습니까?')) {
		var f = document.frmList;
		f.method = 'post'; f.action = './indb.excel.popu.pageview.php'; f.target = 'ifrmHidden';
		f.submit();
		f.action = ''; f.target = ''; f.method = '';
	}
}

function sort(sort)
{
	var fm = document.frmList;
	fm.sort.value = sort;
	fm.submit();
}
function sort_chk(sort)
{
	if (!sort) return;
	sort = sort.replace(" ","_");
	var obj = document.getElementsByName('sort_'+sort);
	if (obj.length){
		div = obj[0].src.split('list_');
		for (i=0;i<obj.length;i++){
			chg = (div[1]=="up_off.gif") ? "up_on.gif" : "down_on.gif";
			obj[i].src = div[0] + "list_" + chg;
		}
	}
}

window.onload = function(){ sort_chk('<?=$_GET[sort]?>'); }
</script>
<div class="title title_top">상품 페이지뷰 분석  <span>쇼핑몰에 등록된 상품중 가장 많이본 상품 순위를 일자별, 분류별 등으로 조회할 수 있습니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=data&no=31')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<form name=frmList method=get>
<input type=hidden name=sort value="<?=$_GET['sort']?>">
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
	<td>분류선택</td>
	<td colspan=3><script>new categoryBox('cate[]',4,'<?=$category?>');</script></td>
</tr>
	<td>품절여부</td>
	<td class="noline">
		<label><input type="radio" name="srunout" value="" <?=$srunout == '' ? 'checked' : ''?>>전체</label>
		<label><input type="radio" name="srunout" value="1" <?=$srunout == '1' ? 'checked' : ''?>>품절상품</label>
		<label><input type="radio" name="srunout" value="-1" <?=$srunout == '-1' ? 'checked' : ''?>>품절상품제외</label>
	</td>
	<td>브랜드</td>
	<td>
	<select name=brandno>
	<option value="">-- 브랜드 선택 --
	<?
	$bRes = $db->query("select * from ".GD_GOODS_BRAND." order by sort");
	while ($tmp=$db->fetch($bRes)){
	?>
	<option value="<?=$tmp[sno]?>" <?=$selected[brandno][$tmp[sno]]?>><?=$tmp[brandnm]?>
	<? } ?>
	</select>
	</td>
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
<col width="40">
<col width="10">
<col width="">
<col width="100">
<col width="100">
<col width="100">
<col width="100">
<col width="100">
<tr><td class="rnd" colspan="10"></td></tr>
<tr class="rndbg">
	<th>순위</th>
	<th></th>
	<th></th>
	<th>상품명</th>
	<th>등록일</th>
	<th>가격</th>
	<th>재고</th>
	<th>횟수</th>
	<th>비율</th>
</tr>
<tr><td class=rnd colspan="10"></td></tr>
<?

$rank = ($_GET['page'] - 1) * $_GET['page_num'];

while ($row = $db->fetch($res,1)) {
	$icon = setIcon($row[icon],$row[regdt],"../");
	if ($row[usestock] && $row[totstock] < 1) $row[runout] = 1;

	$row[rate] = round(($row[cnt] / $total_cnt) * 100 * 100) / 100;
?>
<tr height=25>
	<td align=center><font class="ver8" color="444444"><?=++$rank?></font></td>
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
	<td align=center><font class=ver81 color=444444><?=number_format($row[cnt])?></td>
	<td align=center><font class=ver81 color=444444><?=($row[rate])?>%</td>
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
<tr><td><img src="../img/icon_list.gif" align="absmiddle">시스템 과부화를 고려하여 상품이 많은 경우 검색기간은 최대 1년 단위로 나누어 검색하시고, 엑셀로 파일로 다운로드 하여 활용하시기를 권장 드립니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01','#F7F7F7')</script>





<script>table_design_load();</script>

<? include "../_footer.php"; ?>
