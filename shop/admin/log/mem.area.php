<?

$location = "통계관리 > 지역별 회원분석";
include "../_header.php";

$r_yoil = array("일","월","화","수","목","금","토");

$year = ($_POST[year]) ? $_POST[year] : date("Y");
$month = ($_POST['month']) ? sprintf("%02d",$_POST['month']) : date("m");

$selected['year'][$year] = "selected";
$selected['month'][$month] = "selected";

$date = $year."-".sprintf("%02d",$month);
$last = date("t",strtotime($date."-01"));

$query = "
select substring(regdt,1,10) rdt, sex,substring(zipcode,1,3) as zip,count(*) as cnt from
	".GD_MEMBER."
where
	regdt  like '$date%'
	group by rdt,sex,zip
";
$res = $db->query($query);
$y = date("Y",time());
while ($data=$db->fetch($res)){
	$data['sex'] = empty($data['sex']) ? 'none' : $data['sex'];
	if($data[zip] < 200) $tmp = '서울';
	else if($data[zip] < 358) $tmp = '충남';
	else if($data[zip] < 396) $tmp = '충북';
	else if($data[zip] < 488) $tmp = '경기';
	else if($data[zip] < 551) $tmp = '전남';
	else if($data[zip] < 600) $tmp = '전북';
	else if($data[zip] < 679) $tmp = '경남';
	else if($data[zip] < 698) $tmp = '제주';
	else if($data[zip] < 800) $tmp = '경북';

	$cnt[$tmp][$data[sex]][$data[rdt]] += $data[cnt];
	$tot += $data[cnt];

	if ($data['sex'] == 'none') {
		$extra = true;
	}
}
?>

<div class="title title_top">지역별 회원분석 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=data&no=22')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<form method="post">

<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>기간설정</td>
	<td>
	<select name="year">
	<? for ($i=0;$i<3;$i++){ $y = date("Y") - $i; ?>
	<option value="<?=$y?>" <?=$selected[year][$y]?>><?=$y?>
	<? } ?>
	</select>년
	<select name="month">
	<?
	for ($i=1;$i<=12;$i++){
		$tmp = sprintf("%02d",$i);
	?>
	<option value="<?=$i?>" <?=$selected[month][$tmp]?>><?=$i?>
	<? } ?>
	</select>월
	<input type="image" src="../img/btn_search_s.gif" style="border:0" align="absmiddle" hspace="10">
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
<table width="100%" cellpadding="0" cellspacing="0">
<tr><td class="rnd" colspan="30"></td></tr>
<tr class="rndbg">
	<th width="16%"><font class=small><b>일별통계</b></font></th>
	<th  colspan="<?=$cols?>"><font class=small><b>서울</b></font></th>
	<th  colspan="<?=$cols?>"><font class=small><b>경기</b></font></th>
	<th  colspan="<?=$cols?>"><font class=small><b>경남</b></font></th>
	<th  colspan="<?=$cols?>"><font class=small><b>경북</b></font></th>
	<th  colspan="<?=$cols?>"><font class=small><b>전남</b></font></th>
	<th  colspan="<?=$cols?>"><font class=small><b>전북</b></font></th>
	<th  colspan="<?=$cols?>"><font class=small><b>충남</b></font></th>
	<th  colspan="<?=$cols?>"><font class=small><b>충북</b></font></th>
	<th  colspan="<?=$cols?>"><font class=small><b>제주</b></font></th>
</tr>
<tr height=25>
	<td align="center" bgcolor="#F7F7F7"><b>일자</b></td>
	<td align="center"><b>남</b></td>
	<td align="center" bgcolor="#F7F7F7"><b>여</b></td>
	<? if ($extra) { ?><td align="center" bgcolor="#E7E7E7"><b>미입력</b></td><? } ?>
	<td align="center"><b>남</b></td>
	<td align="center" bgcolor="#F7F7F7"><b>여</b></td>
	<? if ($extra) { ?><td align="center" bgcolor="#E7E7E7"><b>미입력</b></td><? } ?>
	<td align="center"><b>남</b></td>
	<td align="center" bgcolor="#F7F7F7"><b>여</b></td>
	<? if ($extra) { ?><td align="center" bgcolor="#E7E7E7"><b>미입력</b></td><? } ?>
	<td align="center"><b>남</b></td>
	<td align="center" bgcolor="#F7F7F7"><b>여</b></td>
	<? if ($extra) { ?><td align="center" bgcolor="#E7E7E7"><b>미입력</b></td><? } ?>
	<td align="center"><b>남</b></td>
	<td align="center" bgcolor="#F7F7F7"><b>여</b></td>
	<? if ($extra) { ?><td align="center" bgcolor="#E7E7E7"><b>미입력</b></td><? } ?>
	<td align="center"><b>남</b></td>
	<td align="center" bgcolor="#F7F7F7"><b>여</b></td>
	<? if ($extra) { ?><td align="center" bgcolor="#E7E7E7"><b>미입력</b></td><? } ?>
	<td align="center"><b>남</b></td>
	<td align="center" bgcolor="#F7F7F7"><b>여</b></td>
	<? if ($extra) { ?><td align="center" bgcolor="#E7E7E7"><b>미입력</b></td><? } ?>
	<td align="center"><b>남</b></td>
	<td align="center" bgcolor="#F7F7F7"><b>여</b></td>
	<? if ($extra) { ?><td align="center" bgcolor="#E7E7E7"><b>미입력</b></td><? } ?>
	<td align="center"><b>남</b></td>
	<td align="center" bgcolor="#F7F7F7"><b>여</b></td>
	<? if ($extra) { ?><td align="center" bgcolor="#E7E7E7"><b>미입력</b></td><? } ?>
</tr>
<tr><td class=rnd colspan=30></td></tr>
<? for ($i=1;$i<=$last;$i++){
	$day = $date.'-'.sprintf("%02d",$i);
	$yoil = date("w",strtotime($day));
?>
<tr height=25>
	<td align="center" bgcolor="#F7F7F7"><font class="ver8" color="444444"><?=$day?> (<?=$r_yoil[$yoil]?>)</td>
	<td style="text-align:right;padding-right:10px"><font class="ver8" color="6C6C6C"><b><?=number_format($cnt['서울']['m'][$day])?></b></font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class="ver8" color=EC4E00><b><?=number_format($cnt['서울']['w'][$day])?></b></font></td>
	<? if ($extra) { ?><td style="text-align:right;padding-right:10px" bgcolor="#E7E7E7"><font class="ver8" color=6C6C6C><b><?=number_format($cnt['서울']['none'][$day])?></b></font></td><? } ?>
	<td style="text-align:right;padding-right:10px"><font class="ver8" color="6C6C6C"><b><?=number_format($cnt['경기']['m'][$day])?></b></font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class="ver8" color=EC4E00><b><?=number_format($cnt['경기']['w'][$day])?></b></font></td>
	<? if ($extra) { ?><td style="text-align:right;padding-right:10px" bgcolor="#E7E7E7"><font class="ver8" color=6C6C6C><b><?=number_format($cnt['경기']['none'][$day])?></b></font></td><? } ?>
	<td style="text-align:right;padding-right:10px"><font class="ver8" color="6C6C6C"><b><?=number_format($cnt['경남']['m'][$day])?></b></font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class="ver8" color=EC4E00><b><?=number_format($cnt['경남']['w'][$day])?></b></font></td>
	<? if ($extra) { ?><td style="text-align:right;padding-right:10px" bgcolor="#E7E7E7"><font class="ver8" color=6C6C6C><b><?=number_format($cnt['경남']['none'][$day])?></b></font></td><? } ?>
	<td style="text-align:right;padding-right:10px"><font class="ver8" color="6C6C6C"><b><?=number_format($cnt['경북']['m'][$day])?></b></font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class="ver8" color=EC4E00><b><?=number_format($cnt['경북']['w'][$day])?></b></font></td>
	<? if ($extra) { ?><td style="text-align:right;padding-right:10px" bgcolor="#E7E7E7"><font class="ver8" color=6C6C6C><b><?=number_format($cnt['경북']['none'][$day])?></b></font></td><? } ?>
	<td style="text-align:right;padding-right:10px"><font class="ver8" color="6C6C6C"><b><?=number_format($cnt['전남']['m'][$day])?></b></font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class="ver8" color=EC4E00><b><?=number_format($cnt['전남']['w'][$day])?></b></font></td>
	<? if ($extra) { ?><td style="text-align:right;padding-right:10px" bgcolor="#E7E7E7"><font class="ver8" color=6C6C6C><b><?=number_format($cnt['전남']['none'][$day])?></b></font></td><? } ?>
	<td style="text-align:right;padding-right:10px"><font class="ver8" color="6C6C6C"><b><?=number_format($cnt['전북']['m'][$day])?></b></font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class="ver8" color=EC4E00><b><?=number_format($cnt['전북']['w'][$day])?></b></font></td>
	<? if ($extra) { ?><td style="text-align:right;padding-right:10px" bgcolor="#E7E7E7"><font class="ver8" color=6C6C6C><b><?=number_format($cnt['전북']['none'][$day])?></b></font></td><? } ?>
	<td style="text-align:right;padding-right:10px"><font class="ver8" color="6C6C6C"><b><?=number_format($cnt['충남']['m'][$day])?></b></font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class="ver8" color=EC4E00><b><?=number_format($cnt['충남']['w'][$day])?></b></font></td>
	<? if ($extra) { ?><td style="text-align:right;padding-right:10px" bgcolor="#E7E7E7"><font class="ver8" color=6C6C6C><b><?=number_format($cnt['충남']['none'][$day])?></b></font></td><? } ?>
	<td style="text-align:right;padding-right:10px"><font class="ver8" color="6C6C6C"><b><?=number_format($cnt['충북']['m'][$day])?></b></font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class="ver8" color=EC4E00><b><?=number_format($cnt['충북']['w'][$day])?></b></font></td>
	<? if ($extra) { ?><td style="text-align:right;padding-right:10px" bgcolor="#E7E7E7"><font class="ver8" color=6C6C6C><b><?=number_format($cnt['충북']['none'][$day])?></b></font></td><? } ?>
	<td style="text-align:right;padding-right:10px"><font class="ver8" color="6C6C6C"><b><?=number_format($cnt['제주']['m'][$day])?></b></font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class="ver8" color=EC4E00><b><?=number_format($cnt['제주']['w'][$day])?></b></font></td>
	<? if ($extra) { ?><td style="text-align:right;padding-right:10px" bgcolor="#E7E7E7"><font class="ver8" color=6C6C6C><b><?=number_format($cnt['제주']['none'][$day])?></b></font></td><? } ?>
</tr>
<tr><td colspan=30 class=rndline></td></tr>
<? } ?>

<tr><td colspan=30 bgcolor=A3A3A3></td></tr>
<tr height=25 bgcolor="#C5C5C5">
	<td align="center" bgcolor="#EDEDED"><?=number_format($tot)?></td>
	<td style="text-align:right;padding-right:10px" bgcolor="white"><font class="ver8" color="6C6C6C"><b><?=number_format(@array_sum($cnt['서울']['m']))?></b></font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class="ver8" color="1259C3"><b><?=number_format(@array_sum($cnt['서울']['w']))?></b></font></td>
	<? if ($extra) { ?><td style="text-align:right;padding-right:10px" bgcolor="#E7E7E7"><font class="ver8" color=6C6C6C><b><?=number_format(@array_sum($cnt['서울']['none']))?></b></font></td><? } ?>
	<td style="text-align:right;padding-right:10px" bgcolor="white"><font class="ver8" color="6C6C6C"><b><?=number_format(@array_sum($cnt['경기']['m']))?></b></font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class="ver8" color="1259C3"><b><?=number_format(@array_sum($cnt['경기']['w']))?></b></font></td>
	<? if ($extra) { ?><td style="text-align:right;padding-right:10px" bgcolor="#E7E7E7"><font class="ver8" color=6C6C6C><b><?=number_format(@array_sum($cnt['경기']['none']))?></b></font></td><? } ?>
	<td style="text-align:right;padding-right:10px" bgcolor="white"><font class="ver8" color="6C6C6C"><b><?=number_format(@array_sum($cnt['경남']['m']))?></b></font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class="ver8" color="1259C3"><b><?=number_format(@array_sum($cnt['경남']['w']))?></b></font></td>
	<? if ($extra) { ?><td style="text-align:right;padding-right:10px" bgcolor="#E7E7E7"><font class="ver8" color=6C6C6C><b><?=number_format(@array_sum($cnt['경남']['none']))?></b></font></td><? } ?>
	<td style="text-align:right;padding-right:10px" bgcolor="white"><font class="ver8" color="6C6C6C"><b><?=number_format(@array_sum($cnt['경북']['m']))?></b></font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class="ver8" color="1259C3"><b><?=number_format(@array_sum($cnt['경북']['w']))?></b></font></td>
	<? if ($extra) { ?><td style="text-align:right;padding-right:10px" bgcolor="#E7E7E7"><font class="ver8" color=6C6C6C><b><?=number_format(@array_sum($cnt['경북']['none']))?></b></font></td><? } ?>
	<td style="text-align:right;padding-right:10px" bgcolor="white"><font class="ver8" color="6C6C6C"><b><?=number_format(@array_sum($cnt['전남']['m']))?></b></font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class="ver8" color="1259C3"><b><?=number_format(@array_sum($cnt['전남']['w']))?></b></font></td>
	<? if ($extra) { ?><td style="text-align:right;padding-right:10px" bgcolor="#E7E7E7"><font class="ver8" color=6C6C6C><b><?=number_format(@array_sum($cnt['전남']['none']))?></b></font></td><? } ?>
	<td style="text-align:right;padding-right:10px" bgcolor="white"><font class="ver8" color="6C6C6C"><b><?=number_format(@array_sum($cnt['전북']['m']))?></b></font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class="ver8" color="1259C3"><b><?=number_format(@array_sum($cnt['전북']['w']))?></b></font></td>
	<? if ($extra) { ?><td style="text-align:right;padding-right:10px" bgcolor="#E7E7E7"><font class="ver8" color=6C6C6C><b><?=number_format(@array_sum($cnt['전북']['none']))?></b></font></td><? } ?>
	<td style="text-align:right;padding-right:10px" bgcolor="white"><font class="ver8" color="6C6C6C"><b><?=number_format(@array_sum($cnt['충남']['m']))?></b></font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class="ver8" color="1259C3"><b><?=number_format(@array_sum($cnt['충남']['w']))?></b></font></td>
	<? if ($extra) { ?><td style="text-align:right;padding-right:10px" bgcolor="#E7E7E7"><font class="ver8" color=6C6C6C><b><?=number_format(@array_sum($cnt['충남']['none']))?></b></font></td><? } ?>
	<td style="text-align:right;padding-right:10px" bgcolor="white"><font class="ver8" color="6C6C6C"><b><?=number_format(@array_sum($cnt['충북']['m']))?></b></font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class="ver8" color="1259C3"><b><?=number_format(@array_sum($cnt['충북']['w']))?></b></font></td>
	<? if ($extra) { ?><td style="text-align:right;padding-right:10px" bgcolor="#E7E7E7"><font class="ver8" color=6C6C6C><b><?=number_format(@array_sum($cnt['충북']['none']))?></b></font></td><? } ?>
	<td style="text-align:right;padding-right:10px" bgcolor="white"><font class="ver8" color="6C6C6C"><b><?=number_format(@array_sum($cnt['제주']['m']))?></b></font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class="ver8" color="1259C3"><b><?=number_format(@array_sum($cnt['제주']['w']))?></b></font></td>
	<? if ($extra) { ?><td style="text-align:right;padding-right:10px" bgcolor="#E7E7E7"><font class="ver8" color=6C6C6C><b><?=number_format(@array_sum($cnt['제주']['none']))?></b></font></td><? } ?>
</tr>
<tr><td colspan=30 class=rndline></td></tr>
</table>
<p/>
<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">위 지역별 회원분석은 입금확인일(결제완료일) 기준이며, 주문취소금액을 제한 통계자료입니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01','#F7F7F7')</script>



<script>table_design_load();</script>

<? include "../_footer.php"; ?>