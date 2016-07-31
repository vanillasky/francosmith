<?

$location = "통계관리 > 상품 판매순위 분석 ";
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

$_GET[page_num] = $_GET[page_num] ? $_GET[page_num] : 20;
$selected[page_num][$_GET[page_num]] = "selected";
$selected[year][$year] = "selected";
$selected[month][$month] = "selected";
$selected[brandno][$_GET[brandno]] = "selected";

$date = $year."-".sprintf("%02d",$month);
$last = date("t",strtotime($date."-01"));
$page = $_GET['page'];

$pg = new Page($page,$_GET[page_num]);
$pg->vars[page] = getVars('page',1);

if(!$page)$page=1;

if ($_GET[brandno]) $where[] = "brandno='$_GET[brandno]'";
if ($_GET[cate]){
	$category = array_notnull($_GET[cate]);
	$category = $category[count($category)-1];
}

$where[] = "o.istep < 40";
$where[] = "o.istep > 0";
if ($stype == 'm') {
	$where[] = " DATE_FORMAT(o2.cdt,'%Y-%m') = '$date' ";
}
else if ($sdate_s & $sdate_e){
	$where[] = " (DATE_FORMAT(o2.cdt, '%Y%m%d') >= '".$sdate_s."' and DATE_FORMAT(o2.cdt,'%Y%m%d') <= '".($sdate_e)."')";
}

if ($srunout == '1') $where[] = "(g.runout = 1 OR (g.usestock = 'o' AND g.usestock IS NOT NULL AND g.totstock < 1))";
elseif ($srunout == '-1') $where[] = "g.runout <> 1 AND (g.usestock <> 'o' OR g.usestock IS NULL OR g.totstock > 0)";

$query = "
select o.goodsnm,o.goodsno,count(o.sno) cnt,sum(o.ea) as ea,sum(o.price * ea) as price, g.img_s, g.runout, g.icon from
".GD_ORDER_ITEM." as o FORCE INDEX (ix_goodsno)
left join ".GD_GOODS." as g
ON o.goodsno = g.goodsno
left join ".GD_ORDER." as o2 on o.ordno = o2.ordno
";

if ($category){
	$query .= " left join ".GD_GOODS_LINK." c on g.goodsno=c.goodsno ";

	// 상품분류 연결방식 전환 여부에 따른 처리
	$where[]	= getCategoryLinkQuery('c.category', $category, 'where');
}
$query .= ' where '.implode(' and ',$where);
$query .= " group by o.goodsno";
if (!$_GET[sort]) $query .= " order by goodsno ";
else {
	$query .= " order by ".$_GET[sort];
}
$res = $db->query($query);

while ($data=$db->fetch($res)){
	//list($data['goodsnm']) = $db->fetch("select goodsnm from ".GD_ORDER_ITEM." where goodsno='".$data['goodsno']."' limit 1");
	$arr[] = $data;
	$total++;
	$tot['cnt'] += $data['cnt'];
	$tot['ea'] += $data['ea'];
	$tot['price'] += $data['price'];
}

$tmp = explode('/',$_SERVER[PHP_SELF]);
$section = $tmp[count($tmp)-2];
$link = $tmp[count($tmp)-1];
?>
<script type="text/javascript">
function fnDownloadStatistics() {
	if (confirm('검색된 통계 내역을 다운로드 하시겠습니까?')) {
		var f = document.frmList;
		f.method = 'post'; f.action = './indb.excel.popu.goods.php'; f.target = 'ifrmHidden';
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
<div class="title title_top">상품 판매순위 분석  <span>쇼핑몰에 등록된 상품의 매출 순위를 일자별, 분류별 등으로 조회할 수 있습니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=data&no=18')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
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
		<td valign=bottom>
		<img src="../img/sname_person.gif"><a href="javascript:sort('cnt desc')"><img name=sort_cnt_desc src="../img/list_up_off.gif"></a><a href="javascript:sort('cnt')"><img name=sort_cnt src="../img/list_down_off.gif"></a><img src="../img/sname_dot.gif"><img src="../img/sname_amount.gif"><a href="javascript:sort('ea desc')"><img name=sort_ea_desc src="../img/list_up_off.gif"></a><a href="javascript:sort('ea')"><img name=sort_ea src="../img/list_down_off.gif"></a><img src="../img/sname_dot.gif"><img src="../img/sname_sales.gif"><a href="javascript:sort('price desc')"><img name=sort_price_desc src="../img/list_up_off.gif"></a><a href="javascript:sort('price')"><img name=sort_price src="../img/list_down_off.gif"></a>
		</td>
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
<col width="150">
<col width="150">
<col width="150">
<tr><td class="rnd" colspan="10"></td></tr>
<tr class="rndbg">
	<th><font class="small"><b>번호</th>
	<th></th>
	<th width=10></th>
	<th bgcolor="63544B"><font class="small"><b>상품명</b></font></th>
	<th><font class="small"><b>구매자수</b></font></th>
	<th bgcolor="63544B"><font class="small"><b>구매수량</b></font></th>
	<th><font class="small"><b>매출액</b></font></th>
</tr>
<tr><td class=rnd colspan="10"></td></tr>
<?
$tot_page = ceil($total / $_GET[page_num]) + 1;
$pg->recode[total] =  $total;
$pg->exec();
if($arr){
foreach($arr as $v){
$start = ($page - 1) * $_GET[page_num];
$end = $page * $_GET[page_num];
if(!$pi)$pi=$start;
$v1 = $arr[$pi];
$pi++;
if( $pi > $start && $pi <= $end && $pi <= $total ){
?>

<tr height=25>
	<td align=center bgcolor="#F7F7F7"><font class="ver8" color="444444"><?=$pi?></font></td>
	<td style="border:1px #e9e9e9 solid;"><a href="../../goods/goods_view.php?goodsno=<?=$v1[goodsno]?>" target=_blank><?=goodsimg($v1[img_s],40,'',1)?></a></td>
	<td></td>
	<td>
	<a href="javascript:void(0);" onClick="<?=getPermission('goods') ? 'popup(\'../goods/popup.register.php?mode=modify&goodsno='.$v1[goodsno].'\',850,600)' : 'alert(\'접근권한이 없습니다. 관리자 권한설정을 확인하여 주세요.\')' ?>;"><font color=303030><?=$v1[goodsnm]?></font></a>
	<? if ($icon){ ?><div style="padding-top:3px"><?=$icon?></div><? } ?>
	<? if ($v1[runout]){ ?><div style="padding-top:3px"><img src="../../data/skin/<?=$cfg[tplSkin]?>/img/icon/good_icon_soldout.gif"></div><? } ?>
	</td>
	<td style="text-align:right;padding-right:10px" bgcolor="#F7F7F7"><font class="ver8" color="6C6C6C"><b><?=$v1['cnt']?><b></font></td>
	<td style="text-align:right;padding-right:10px"><font class="ver8" color="EC4E00"><b><?=number_format($v1['ea'])?><b></font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class="ver8" color="EC4E00"><b><?=number_format($v1['price'])?><b></font></td>
</tr>
<tr><td colspan="10" class="rndline"></td></tr>
<?
}}}
?>
<tr><td colspan="10" bgcolor="A3A3A3"></td></tr>
<tr height=25 bgcolor="#C5C5C5">
	<td align=center bgcolor="#EDEDED">합계</td>
	<td align=center bgcolor="white" colspan="3">&nbsp;</td>
	<td style="text-align:right;padding-right:10px" bgcolor="#EDEDED"><font class="ver8" color="6C6C6C"><b><?=number_format($tot['cnt'])?></b></font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="white"><font class="ver8" color="6C6C6C"><b><?=number_format($tot['ea'])?></b></font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#EDEDED"><font class="ver8" color="6C6C6C"><b><?=number_format($tot['price'])?></b></font></td>
</tr>
<tr><td colspan="10" class="rndline"></td></tr>
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
<tr><td><img src="../img/icon_list.gif" align="absmiddle">위 판매순위 상품분석 데이터는 입금확인일(결제완료일) 기준이며, 주문취소금액을 제한 통계자료입니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">시스템 과부화를 고려하여 상품이 많은 경우 검색기간은 최대 1년 단위로 나누어 검색하시고, 엑셀로 파일로 다운로드 하여 활용하시기를 권장 드립니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01','#F7F7F7')</script>
<script>table_design_load();</script>

<? include "../_footer.php"; ?>
