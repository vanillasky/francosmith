<?

$location = "통계관리 > 카테고리분석";
include "../_header.php";

if(!$_GET['category']) $where[] = "length(category) = '3'";
else{
	$len = strlen($_GET['category'])+3;
	if($len > 12)$len=12;
	$where[] = "length(category) = '".$len."'";
	$where[] = "category like '".$_GET['category']."%'";
}

$year = ($_GET[year]) ? $_GET[year] : date("Y");
$month = ($_GET[month]) ? sprintf("%02d",$_GET[month]) : date("m");

$stype = ($_GET[stype]) ? $_GET[stype] : 'm';
$sdate_s = ($_GET[regdt][0]) ? $_GET[regdt][0] : date('Ymd',strtotime('-7 day'));
$sdate_e = ($_GET[regdt][1]) ? $_GET[regdt][1] : date('Ymd');

if (checkStatisticsDateRange($sdate_s, $sdate_e) > 365) {
	msg('조회기간 설정은 최대 1년을 넘지 못합니다. 기간 확인후 재설정 해주세요.',$_SERVER['PHP_SELF']);exit;
}

$selected[year][$year] = "selected";
$selected[month][$month] = "selected";

$date = $year."-".sprintf("%02d",$month);
$last = date("t",strtotime($date."-01"));

$query = "
select category,catnm,length(category) len from
	".GD_CATEGORY."
where ".@implode(' and ',$where)."
order by category,len
";
$res = $db->query($query);
while ($data=$db->fetch($res)) {
	$query = "select sum(b.ea) ea,count(DISTINCT o.ordno) cnt, sum(b.price * b.ea) as sales from ".GD_GOODS_LINK." l
	left join ".GD_GOODS." a on l.goodsno=a.goodsno
	left join ".GD_ORDER_ITEM." b on a.goodsno=b.goodsno
	left join ".GD_ORDER." AS o on b.ordno = o.ordno
	where ".getCategoryLinkQuery('l.category', $data['category'], 'where')." and b.istep < 40 and b.istep > 0";

	if ($stype == 'm') {
		$query .= " and DATE_FORMAT(o.cdt,'%Y-%m') = '$date' ";
	}
	else if ($sdate_s & $sdate_e){
		$query .= " and (DATE_FORMAT(o.cdt, '%Y%m%d') >= '".$sdate_s."' and DATE_FORMAT(o.cdt,'%Y%m%d') <= '".($sdate_e)."')";
	}

	$tmp = $db->fetch($query);
	$tmp[catnm] = $data[catnm];
	$tmp[category] = $data[category];
	$tmp[gubun] = ($data[len]/3) . "차 카테고리";
	$arr[] = $tmp;
}
?>
<div class="title title_top">카테고리분석 <span>쇼핑몰에 등록된 카테고리별 매출을 확인 및 분석 할 수 있습니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=data&no=17')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<form method=get>
<input type="hidden" name="category" value="<?=$_GET['category']?>" />
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>기간설정</td>
	<td>
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
</table>

<div class=button_top><input type=image src="../img/btn_search_s.gif"></div>
</form>
<p>
<?
if($_GET['category']){
$query = "
select * from
	".GD_CATEGORY."
where
	category in (left('".$_GET['category']."',3),left('".$_GET['category']."',6),left('".$_GET['category']."',9),'".$_GET['category']."')
order by category
";
$res = $db->query($query);
while ($data=$db->fetch($res)) $pos[] = "<a class='ver8' href='popu.category.php?category=$data[category]'>$data[catnm]</a>";
$ret = " > ".@implode(" > ",$pos);
}
?>
<div>&nbsp;<a class='ver8' href='popu.category.php'><b>1차 카테고리보기</b></a><?=$ret?> <font class=extext>(카테고리명을 클릭하면 하위 카테고리를 볼 수 있습니다)</font></div>
<div style='font:0;height:5'></div>
<table width="100%" cellpadding="0" cellspacing="0">
<col width="150">
<col width="">
<col width="150">
<col width="150">
<col width="150">
<tr><td class="rnd" colspan="10"></td></tr>
<tr class="rndbg">
	<th><font class="small"><b>구분</th>
	<th bgcolor="63544B"><font class="small"><b>카테고리명</th>
	<th><font class="small"><b>구매자수</b></th>
	<th bgcolor="63544B"><font class="small"><b>구매수량</b></th>
	<th><font class="small"><b>매출액</b></th>
</tr>
<tr><td class=rnd colspan="10"></td></tr>
<?
if($arr)foreach ($arr as $k=>$v){
$v['ea']+=0;
$url = $_SERVER[PHP_SELF]."?";
$rtmp['category'] = $v['category'];
$_GET = array_merge($_GET,$rtmp);
$tot['cnt'] += $v['cnt'];
$tot['ea'] += $v['ea'];
$tot['sales'] += $v['sales'];
foreach($_GET as $k1 => $v1) {
	if ($k1 == 'regdt') {
		$url .= "&regdt[]=" . $v1[0];
		$url .= "&regdt[]=" . $v1[1];
	}
	else $url .= "&" . $k1 . "=" . $v1;
}
if($len == 9)$url = '#';
?>
<tr height=25>
	<td align=center bgcolor="#F7F7F7"><font class="ver8" color="444444"><?=$v['gubun']?></font></td>
	<td style="padding-left:10px"><a href='<?=$url?>'><?=$v['catnm']?> (<?=$v['category']?>)</a></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class="ver8" color="EC4E00"><b><?=number_format($v['cnt'])?><b></font></td>
	<td style="text-align:right;padding-right:10px"><font class="ver8" color="EC4E00"><b><?=number_format($v['ea'])?><b></font></td>
	<td style="text-align:right;padding-right:10px"><font class="ver8" color="EC4E00"><b><?=number_format($v['sales'])?><b></font></td>
</tr>
<tr><td colspan="10" class="rndline"></td></tr>
<? } ?>
<tr><td colspan="10" bgcolor="A3A3A3"></td></tr>
<tr height=25 bgcolor="#C5C5C5">
	<td align=center bgcolor="#EDEDED">합계</td>
	<td align=center bgcolor="white"><font class=ver8 color=6C6C6C>-</font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#EDEDED" ><font class="ver8" color="6C6C6C"><b><?=number_format($tot['cnt'])?></b></font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="white"><font class="ver8" color="6C6C6C"><b><?=number_format($tot['ea'])?></b></font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="white"><font class="ver8" color="6C6C6C"><b><?=number_format($tot['sales'])?></b></font></td>
</tr>
<tr><td colspan="10" class="rndline"></td></tr>
</table>
<p />


<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle">위 카테고리 분석 데이터는 입금확인일(결제완료일) 기준이며, 주문취소금액을 제외한 통계자료입니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">시스템 과부화를 고려하여 상품이 많은 경우 검색기간은 최대 1년 단위로 나누어 검색하시기를 권장 드립니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>




<script>table_design_load();</script>

<? include "../_footer.php"; ?>