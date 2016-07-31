<?

$location = "통계관리 > 신규 회원분석";
include "../_header.php";

$r_yoil = array("일","월","화","수","목","금","토");

$year = ($_POST[year]) ? $_POST[year] : date("Y");
$month = ($_POST[month]) ? sprintf("%02d",$_POST[month]) : date("m");

$selected[year][$year] = "selected";
$selected[month][$month] = "selected";

$date = $year."-".sprintf("%02d",$month);
$last = date("t",strtotime($date."-01"));

$query = "
select substring(regdt,1,10) rdt,count(*) as cnt, sum(cnt_login) login, sum(cnt_sale) sale_cnt,sex from
	".GD_MEMBER."
where
	regdt  like '$date%'
	group by rdt,sex
";
$res = $db->query($query);
while ($data=$db->fetch($res)){
	$data['sex'] = empty($data['sex']) ? 'none' : $data['sex'];
	$cnt[$data['sex']][$data['rdt']] += $data['cnt'];
	$login[$data['sex']][$data['rdt']] += $data['login'];
	$sale_cnt[$data['sex']][$data['rdt']] += $data['sale_cnt'];
}
if($cnt)$tot = array_sum($cnt);
$extra = isset($cnt['none']) ? true : false;
?>

<div class="title title_top">신규 회원분석 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=data&no=20')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

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
<p/>
<?
// 컬럼수, 컬럼 사이즈
$cols = $extra ? 3 : 2;
$col_size =  $extra ? '7%' : '10%';
?>
<table width=100% cellpadding=0 cellspacing=0>
<tr><td class=rnd colspan=20></td></tr>
<tr class=rndbg>
	<th width="16%"><font class="small"><b>일별통계</th>
	<th colspan="<?=$cols?>" width='20%'><font class="small"><b>신규회원수</th>
	<th colspan="<?=$cols?>" width='20%'><font class="small"><b>비율</th>
	<th colspan="<?=$cols?>" width='20%'><font class="small"><b>로그인횟수</th>
	<th colspan="<?=$cols?>" width='20%'><font class="small"><b>구매횟수</th>
</tr>
<tr><td class=rnd colspan=20></td></tr>
<tr height=25>
	<td align=center bgcolor="#F7F7F7"><b>일자</b></td>
	<td align=center width='<?=$col_size?>'><b>남</b></td>
	<td align=center bgcolor="#F7F7F7" width='<?=$col_size?>'><b>여</b></td>
	<? if ($extra) { ?><td align=center bgcolor="#E7E7E7" width='<?=$col_size?>'><b>미입력</b></td><? } ?>
	<td align=center width='<?=$col_size?>'><b>남</b></td>
	<td align=center bgcolor="#F7F7F7" width='<?=$col_size?>'><b>여</b></td>
	<? if ($extra) { ?><td align=center bgcolor="#E7E7E7" width='<?=$col_size?>'><b>미입력</b></td><? } ?>
	<td align=center width='<?=$col_size?>'><b>남</b></td>
	<td align=center bgcolor="#F7F7F7" width='<?=$col_size?>'><b>여</b></td>
	<? if ($extra) { ?><td align=center bgcolor="#E7E7E7" width='<?=$col_size?>'><b>미입력</b></td><? } ?>
	<td align=center width='<?=$col_size?>'><b>남</b></td>
	<td align=center bgcolor="#F7F7F7" width='<?=$col_size?>'><b>여</b></td>
	<? if ($extra) { ?><td align=center bgcolor="#E7E7E7" width='10%'><b>미입력</b></td><? } ?>
</tr>
<tr><td class=rnd colspan=20></td></tr>
<? for ($i=1;$i<=$last;$i++){
	$day = $date.'-'.sprintf("%02d",$i);
	$yoil = date("w",strtotime($day));
?>
<tr height=25>
	<td align=center bgcolor="#F7F7F7"><font class=ver8 color=444444><?=$day?> (<?=$r_yoil[$yoil]?>)</td>
	<td style="text-align:right;padding-right:10px"><font class=ver8 color=6C6C6C><b><?=number_format($cnt['m'][$day])?></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class=ver8 color=EC4E00><b><?=number_format($cnt['w'][$day])?></td>
	<? if ($extra) { ?><td style="text-align:right;padding-right:10px" bgcolor="#E7E7E7"><font class=ver8 color=6C6C6C><b><?=number_format($cnt['none'][$day])?></td><? } ?>

	<?
		// 비율 계산
		$_tot = (int)$cnt['m'][$day] + (int)$cnt['w'][$day] + (int)$cnt['none'][$day];
		$_rate['m'] = $cnt['m'][$day] ? round((int)$cnt['m'][$day] / $_tot * 100) : '';
		$_rate['w'] = $cnt['w'][$day] ? round((int)$cnt['w'][$day] / $_tot * 100) : '';
		$_rate['none'] = $cnt['none'][$day] ? round((int)$cnt['none'][$day] / $_tot * 100) : '';
	?>
	<td style="text-align:right;padding-right:10px"><font class=ver8 color=6C6C6C><b><?=$_rate['m']?>%</td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class=ver8 color=EC4E00><b><?=$_rate['w']?>%</td>
	<? if ($extra) { ?><td style="text-align:right;padding-right:10px" bgcolor="#E7E7E7"><font class=ver8 color=6C6C6C><b><?=$_rate['none']?>%</td><? } ?>

	<td style="text-align:right;padding-right:10px"><font class=ver8 color=6C6C6C><b><?=number_format($login['m'][$day])?></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class=ver8 color=EC4E00><b><?=number_format($login['w'][$day])?></td>
	<? if ($extra) { ?><td style="text-align:right;padding-right:10px" bgcolor="#E7E7E7"><font class=ver8 color=6C6C6C><b><?=number_format($login['none'][$day])?></td><? } ?>

	<td style="text-align:right;padding-right:10px"><font class=ver8 color=6C6C6C><b><?=number_format($sale_cnt['m'][$day])?></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class=ver8 color=EC4E00><b><?=number_format($sale_cnt['w'][$day])?></td>
	<? if ($extra) { ?><td style="text-align:right;padding-right:10px" bgcolor="#E7E7E7"><font class=ver8 color=6C6C6C><b><?=number_format($sale_cnt['none'][$day])?></td><? } ?>
</tr>
<tr><td colspan=20 class=rndline></td></tr>
<? } ?>

<tr><td colspan=20 bgcolor=A3A3A3></td></tr>
<tr height=25 bgcolor="#C5C5C5">
	<?
		// 비율 계산 및 성별 합
		$_sum['m'] = @array_sum($cnt['m']);
		$_sum['w'] = @array_sum($cnt['w']);
		$_sum['none'] = @array_sum($cnt['none']);

		$_tot = array_sum($_sum);
		$_rate['m'] = $_sum['m'][$day] ? round($_sum['m'] / $_tot * 100) : '';
		$_rate['w'] = $_sum['w'][$day] ? round($_sum['w'] / $_tot * 100) : '';
		$_rate['none'] = $_sum['none'][$day] ? round($_sum['none'] / $_tot * 100) : '';
	?>
	<td align=center bgcolor="#EDEDED">합계</td>
	<td style="text-align:right;padding-right:10px" bgcolor=white><font class=ver8 color=6C6C6C><b><?=number_format($_sum['m'])?></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class=ver8 color=1259C3><b><?=number_format($_sum['w'])?></td>
	<? if ($extra) { ?><td style="text-align:right;padding-right:10px" bgcolor="#E7E7E7"><font class=ver8 color=6C6C6C><b><?=number_format($_sum['none'])?></td><? } ?>
	<td style="text-align:right;padding-right:10px" bgcolor=white><font class=ver8 color=6C6C6C><b><?=$_rate['m']?>%</td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class=ver8 color=1259C3><b><?=$_rate['w']?>%</td>
	<? if ($extra) { ?><td style="text-align:right;padding-right:10px" bgcolor="#E7E7E7"><font class=ver8 color=6C6C6C><b><?=$_rate['none']?>%</td><? } ?>
	<td style="text-align:right;padding-right:10px" bgcolor=white><font class=ver8 color=6C6C6C><b><?=number_format(@array_sum($login['m']))?></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class=ver8 color=1259C3><b><?=number_format(@array_sum($login['w']))?></td>
	<? if ($extra) { ?><td style="text-align:right;padding-right:10px" bgcolor="#E7E7E7"><font class=ver8 color=6C6C6C><b><?=number_format(@array_sum($login['none']))?></td><? } ?>
	<td style="text-align:right;padding-right:10px" bgcolor=white><font class=ver8 color=6C6C6C><b><?=number_format(@array_sum($sale_cnt['m']))?></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class=ver8 color=1259C3><b><?=number_format(@array_sum($sale_cnt['w']))?></td>
	<? if ($extra) { ?><td style="text-align:right;padding-right:10px" bgcolor="#E7E7E7"><font class=ver8 color=6C6C6C><b><?=number_format(@array_sum($sale_cnt['none']))?></td><? } ?>
</tr>
<tr><td colspan=20 class=rndline></td></tr>
</table>
<p/>
<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle">위 신규회원분석은 입금확인일(결제완료일) 기준이며, 주문취소금액을 제한 통계자료입니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01','#F7F7F7')</script>



<script>table_design_load();</script>

<? include "../_footer.php"; ?>