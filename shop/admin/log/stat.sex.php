<?

$location = "통계관리 > 성별 주문분석";
include "../_header.php";

$r_yoil = array("일","월","화","수","목","금","토");

$year = ($_POST[year]) ? $_POST[year] : date("Y");
$month = ($_POST[month]) ? sprintf("%02d",$_POST[month]) : date("m");

$selected[year][$year] = "selected";
$selected[month][$month] = "selected";

$date = $year."-".sprintf("%02d",$month);
$last = date("t",strtotime($date."-01"));

$query = "
select substring(cdt,1,10) as odt,b.sex,sum(a.prn_settleprice) as price,count(*) as cnt, a.step from
	".GD_ORDER." a ,".GD_MEMBER." b
where
	a.m_no = b.m_no
	and a.orddt like '$date%'
	and b.sex != ''
	and a.step > 0
	and a.step2 = 0
	group by odt,b.sex,a.step";

$res = $db->query($query);
while ($data=$db->fetch($res)){
	$cnt[$data[sex]][$data[step]][$data[odt]] += $data[cnt];
	$sum[$data[sex]][$data[step]][$data[odt]] += $data[price];
}
?>

<div class="title title_top">성별 주문분석 <span>월 단위로 성별 주문 건수와 주문 금액을 조회 합니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=data&no=16')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

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
<tr><td height=30 align=right style="padding-right:15"><font class=extext>* 아래 자료는 <b>입금확인일(결제완료일)</b> 기준이며, <b>주문취소금액을 제한</b> 통계자료입니다.</td></tr>
</table>

<table width=100% cellpadding=0 cellspacing=0>
<tr><td class=rnd colspan=19></td></tr>
<tr class=rndbg>
	<th width="16%"><font class=small><b>일별통계</b></font></th>
	<th width="10%" colspan=2><font class=small><b>입금확인(남)</b></font></th>
	<th width="10%" colspan=2><font class=small><b>배송준비(남)</b></font></th>
	<th width="10%" colspan=2><font class=small><b>배송중(남)</b></font></th>
	<th width="10%" colspan=2><font class=small><b>배송완료(남)</b></font></th>
	<th width="10%" colspan=2><font class=small><b>입금확인(여)</b></font></th>
	<th width="10%" colspan=2><font class=small><b>배송준비(여)</b></font></th>
	<th width="10%" colspan=2><font class=small><b>배송중(여)</b></font></th>
	<th width="10%" colspan=2><font class=small><b>배송완료(여)</b></font></th>
</tr>
<tr><td class=rnd colspan=50></td></tr>
<tr height=25 class=small>
	<td align=center bgcolor="#F7F7F7"><b>일자</b></td>
	<td align=center><b>건</b></td>
	<td align=center bgcolor="#F7F7F7"><b>금액</b></td>
	<td align=center><b>건</b></td>
	<td align=center bgcolor="#F7F7F7"><b>금액</b></td>
	<td align=center><b>건</b></td>
	<td align=center bgcolor="#F7F7F7"><b>금액</b></td>
	<td align=center><b>건</b></td>
	<td align=center bgcolor="#F7F7F7"><b>금액</b></td>
	<td align=center><b>건</b></td>
	<td align=center bgcolor="#F7F7F7"><b>금액</b></td>
	<td align=center><b>건</b></td>
	<td align=center bgcolor="#F7F7F7"><b>금액</b></td>
	<td align=center><b>건</b></td>
	<td align=center bgcolor="#F7F7F7"><b>금액</b></td>
	<td align=center><b>건</b></td>
	<td align=center bgcolor="#F7F7F7"><b>금액</b></td>
</tr>
<tr><td class=rnd colspan=19></td></tr>
<? for ($i=1;$i<=$last;$i++){
	$day = $date.'-'.sprintf("%02d",$i);
	$yoil = date("w",strtotime($day));
?>

<tr height=25>
	<td align=center bgcolor="#F7F7F7"><font class=ver8 color=444444><?=$day?> (<?=$r_yoil[$yoil]?>)</td>
	<td style="text-align:right;padding-right:5px"><font class=ver8 color=6C6C6C><b><?=number_format($cnt['m']['1'][$day])?></td>
	<td style="text-align:right;padding-right:5px" bgcolor="#f7f7f7"><font class=ver8 color=EC4E00><b><?=number_format($sum['m'][1][$day])?></td>
	<td style="text-align:right;padding-right:5px"><font class=ver8 color=6C6C6C><b><?=number_format($cnt['m']['2'][$day])?></td>
	<td style="text-align:right;padding-right:5px" bgcolor="#f7f7f7"><font class=ver8 color=EC4E00><b><?=number_format($sum['m'][2][$day])?></td>
	<td style="text-align:right;padding-right:5px"><font class=ver8 color=6C6C6C><b><?=number_format($cnt['m']['3'][$day])?></td>
	<td style="text-align:right;padding-right:5px" bgcolor="#f7f7f7"><font class=ver8 color=EC4E00><b><?=number_format($sum['m'][3][$day])?></td>
	<td style="text-align:right;padding-right:5px"><font class=ver8 color=6C6C6C><b><?=number_format($cnt['m']['4'][$day])?></td>
	<td style="text-align:right;padding-right:5px" bgcolor="#f7f7f7"><font class=ver8 color=EC4E00><b><?=number_format($sum['m'][4][$day])?></td>
	<td style="text-align:right;padding-right:5px"><font class=ver8 color=6C6C6C><b><?=number_format($cnt['w']['1'][$day])?></td>
	<td style="text-align:right;padding-right:5px" bgcolor="#f7f7f7"><font class=ver8 color=EC4E00><b><?=number_format($sum['w'][1][$day])?></td>
	<td style="text-align:right;padding-right:5px"><font class=ver8 color=6C6C6C><b><?=number_format($cnt['w']['2'][$day])?></td>
	<td style="text-align:right;padding-right:5px" bgcolor="#f7f7f7"><font class=ver8 color=EC4E00><b><?=number_format($sum['w'][2][$day])?></td>
	<td style="text-align:right;padding-right:5px"><font class=ver8 color=6C6C6C><b><?=number_format($cnt['w']['3'][$day])?></td>
	<td style="text-align:right;padding-right:5px" bgcolor="#f7f7f7"><font class=ver8 color=EC4E00><b><?=number_format($sum['w'][3][$day])?></td>
	<td style="text-align:right;padding-right:5px"><font class=ver8 color=6C6C6C><b><?=number_format($cnt['w']['4'][$day])?></td>
	<td style="text-align:right;padding-right:5px" bgcolor="#f7f7f7"><font class=ver8 color=EC4E00><b><?=number_format($sum['w'][4][$day])?></td>
	
</tr>
<tr><td colspan=19 class=rndline></td></tr>
<? } ?>

<tr><td colspan=19 bgcolor=A3A3A3></td></tr>
<tr height=25 bgcolor="#C5C5C5">
	<td align=center bgcolor="#EDEDED">합계</td>
	<td style="text-align:right;padding-right:5px" bgcolor='white'><font class=ver8 color=6C6C6C><b><?=number_format(@array_sum($cnt['m'][1]))?></td>
	<td style="text-align:right;padding-right:5px" bgcolor="#f7f7f7"><font class=ver8 color=1259C3><b><?=number_format(@array_sum($sum['m'][1]))?></td>
	<td style="text-align:right;padding-right:5px" bgcolor='white'><font class=ver8 color=6C6C6C><b><?=number_format(@array_sum($cnt['m'][2]))?></td>
	<td style="text-align:right;padding-right:5px" bgcolor="#f7f7f7"><font class=ver8 color=1259C3><b><?=number_format(@array_sum($sum['m'][2]))?></td>
	<td style="text-align:right;padding-right:5px" bgcolor='white'><font class=ver8 color=6C6C6C><b><?=number_format(@array_sum($cnt['m'][3]))?></td>
	<td style="text-align:right;padding-right:5px" bgcolor="#f7f7f7"><font class=ver8 color=1259C3><b><?=number_format(@array_sum($sum['m'][3]))?></td>
	<td style="text-align:right;padding-right:5px" bgcolor='white'><font class=ver8 color=6C6C6C><b><?=number_format(@array_sum($cnt['m'][4]))?></td>
	<td style="text-align:right;padding-right:5px" bgcolor="#f7f7f7"><font class=ver8 color=1259C3><b><?=number_format(@array_sum($sum['m'][4]))?></td>
	<td style="text-align:right;padding-right:5px" bgcolor='white'><font class=ver8 color=6C6C6C><b><?=number_format(@array_sum($cnt['w'][1]))?></td>
	<td style="text-align:right;padding-right:5px" bgcolor="#f7f7f7"><font class=ver8 color=1259C3><b><?=number_format(@array_sum($sum['w'][1]))?></td>
	<td style="text-align:right;padding-right:5px" bgcolor='white'><font class=ver8 color=6C6C6C><b><?=number_format(@array_sum($cnt['w'][2]))?></td>
	<td style="text-align:right;padding-right:5px" bgcolor="#f7f7f7"><font class=ver8 color=1259C3><b><?=number_format(@array_sum($sum['w'][2]))?></td>
	<td style="text-align:right;padding-right:5px" bgcolor='white'><font class=ver8 color=6C6C6C><b><?=number_format(@array_sum($cnt['w'][3]))?></td>
	<td style="text-align:right;padding-right:5px" bgcolor="#f7f7f7"><font class=ver8 color=1259C3><b><?=number_format(@array_sum($sum['w'][3]))?></td>
	<td style="text-align:right;padding-right:5px" bgcolor='white'><font class=ver8 color=6C6C6C><b><?=number_format(@array_sum($cnt['w'][4]))?></td>
	<td style="text-align:right;padding-right:5px" bgcolor="#f7f7f7"><font class=ver8 color=1259C3><b><?=number_format(@array_sum($sum['w'][4]))?></td>
	
</tr>
<tr><td colspan=19 class=rndline></td></tr>
</table>

<div style="padding-top:15px"></div>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">위 성별 분석은 입금확인일(결제완료일) 기준이며, 주문취소금액을 제한 통계자료입니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>



<script>table_design_load();</script>

<? include "../_footer.php"; ?>
