<?

$location = "통계관리 > 매출통계";
include "../_header.php";

$r_yoil = array("일","월","화","수","목","금","토");

$year = ($_POST[year]) ? $_POST[year] : date("Y");
$month = ($_POST[month]) ? sprintf("%02d",$_POST[month]) : date("m");

$selected[year][$year] = "selected";
$selected[month][$month] = "selected";

$date = $year."-".sprintf("%02d",$month);
$last = date("t",strtotime($date."-01"));

$query = "
select * from
	".GD_ORDER."
where
	orddt like '$date%'
	and step2 < 40
";
$res = $db->query($query);
while ($data=$db->fetch($res)){
	$day = str_replace("-","",substr($data[orddt],0,10));
	$cnt[o][$day]++;
	$sum[o][$day] += $data[prn_settleprice];
}

$query = "
select * from
	".GD_ORDER."
where
	cdt like '$date%'
	and step > 0
	and step2 < 40
";
$res = $db->query($query);
while ($data=$db->fetch($res)){
	list ($supply) = $db->fetch("select sum(supply*ea) from ".GD_ORDER_ITEM." where ordno=$data[ordno] and istep<40");
	$day = str_replace("-","",substr($data[cdt],0,10));
	$cnt[c][$day]++;
	$sum[c][$day] += $data[prn_settleprice];
	$suppsum[c][$day] += $supply;
	$interest[c][$day] += $data[prn_settleprice] - $supply;
}

$query = "
select * from
	".GD_ORDER."
where
	ddt like '$date%'
	and step > 0
	and step2 < 40
";
$res = $db->query($query);
while ($data=$db->fetch($res)){
	$day = str_replace("-","",substr($data[ddt],0,10));
	$cnt[d][$day]++;
	$sum[d][$day] += $data[prn_settleprice];
}

?>

<div class="title title_top">매출통계 <span>월별 주문, 입금, 배송별 매출을 조회합니다</span></div>

<form method=post>

<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>기간설정</td>
	<td>
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
	<input type=image src="../img/btn_search_s.gif" style="border:0" align=absmiddle hspace=10>
	</td>
</tr>
</table>

</form>

<table width=100% cellpadding=0 cellspacing=0>
<tr><td height=30 align=right style="padding-right:15"><font class=main>※</font> 아래 자료는 <font color=0074BA>입금확인일(결제완료일)</font> 기준이며, <font color=0074BA>주문취소금액을 제한</font> 통계자료입니다.</td></tr>
</table>

<table width=100% cellpadding=0 cellspacing=0>
<tr><td class=rnd colspan=10></td></tr>
<tr class=rndbg>
	<th width="16%"><font class=small><b>일별통계</th>
	<th width="8%" bgcolor=63544B><font class=small><b>주문건수</th>
	<th width="12%"><font class=small><b>주문금액</th>
	<th width="8%" bgcolor=63544B><font class=small><b>결제건수</th>
	<th width="12%"><font class=small><b>결제금액</th>
	<th width="8%" bgcolor=63544B><font class=small><b>배송건수</th>
	<th width="12%"><font class=small><b>배송중/배송완료금액</th>
	<th width="12%" bgcolor=63544B><font class=small><b>매입금액</th>
	<th width="12%"><font class=small><b>순매출액</th>
</tr>
<tr><td class=rnd colspan=10></td></tr>
<? for ($i=1;$i<=$last;$i++){
	$day = str_replace("-","",$date).sprintf("%02d",$i);
	$yoil = date("w",strtotime($day));
?>
<tr height=25>
	<td align=center bgcolor="#F7F7F7"><font class=ver8 color=444444><?=toDate($day,"-")?> (<?=$r_yoil[$yoil]?>)</td>
	<td style="text-align:right;padding-right:10px"><font class=ver8 color=6C6C6C><b><?=number_format($cnt[o][$day])?></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class=ver8 color=EC4E00><b><?=number_format($sum[o][$day])?></td>
	<td style="text-align:right;padding-right:10px"><font class=ver8 color=6C6C6C><b><?=number_format($cnt[c][$day])?></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class=ver8 color=EC4E00><b><?=number_format($sum[c][$day])?></td>
	<td style="text-align:right;padding-right:10px"><font class=ver8 color=6C6C6C><b><?=number_format($cnt[d][$day])?></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class=ver8 color=EC4E00><b><?=number_format($sum[d][$day])?></td>
	<td style="text-align:right;padding-right:10px"><font class=ver8 color=EC4E00><b><?=number_format($suppsum[c][$day])?></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class=ver8 color=EC4E00><b><?=number_format($interest[c][$day])?></td>
</tr>
<tr><td colspan=10 class=rndline></td></tr>
<? } ?>

<tr><td colspan=10 bgcolor=A3A3A3></td></tr>
<tr height=25 bgcolor="#C5C5C5">
	<td align=center bgcolor="#EDEDED">합계</td>
	<td style="text-align:right;padding-right:10px" bgcolor=white><font class=ver8 color=6C6C6C><b><?=number_format(@array_sum($cnt[o]))?></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class=ver8 color=1259C3><b><?=number_format(@array_sum($sum[o]))?></td>
	<td style="text-align:right;padding-right:10px" bgcolor=white><font class=ver8 color=6C6C6C><b><?=number_format(@array_sum($cnt[c]))?></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class=ver8 color=1259C3><b><?=number_format(@array_sum($sum[c]))?></td>
	<td style="text-align:right;padding-right:10px" bgcolor=white><font class=ver8 color=6C6C6C><b><?=number_format(@array_sum($cnt[d]))?></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class=ver8 color=1259C3><b><?=number_format(@array_sum($sum[d]))?></td>
	<td style="text-align:right;padding-right:10px" bgcolor=white><font class=ver8 color=1259C3><b><?=number_format(@array_sum($suppsum[c]))?></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class=ver8 color=1259C3><b><?=number_format(@array_sum($interest[c]))?></td>
</tr>
<tr><td colspan=10 class=rndline></td></tr>
</table>


<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_tip>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">위 매출통계자료는 <font color=0074BA>입금확인일(결제완료일) 기준</font>이며, <font color=0074BA>주문취소금액을 제한</font> 통계자료입니다.</td></tr>
<tR><td><img src="../img/icon_list.gif" align="absmiddle">순매출액은 <font color=0074BA>결제금액 - 매입금액</font>로 계산됩니다.</td></tr>
<tR><td><img src="../img/icon_list.gif" align="absmiddle">상품등록시 <font color=0074BA>매입가를 정확히 입력</font>하여야만 순매출액을 확인할 수 있습니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01','#F7F7F7')</script>



<script>table_design_load();</script>

<? include "../_footer.php"; ?>