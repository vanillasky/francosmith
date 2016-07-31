<?

$location = "������ > ���ɺ� ȸ���м�";
include "../_header.php";

$r_yoil = array("��","��","ȭ","��","��","��","��");

$year = ($_POST[year]) ? $_POST[year] : date("Y");
$month = ($_POST[month]) ? sprintf("%02d",$_POST[month]) : date("m");

$selected[year][$year] = "selected";
$selected[month][$month] = "selected";

$date = $year."-".sprintf("%02d",$month);
$last = date("t",strtotime($date."-01"));

$query = "
select substring(regdt,1,10) rdt, sex,birth_year,count(*) as cnt from
	".GD_MEMBER."
where
	regdt  like '$date%'
	group by rdt,sex,birth_year
";
$res = $db->query($query);
$y = date("Y",time());
while ($data=$db->fetch($res)){	
	
	$age = $y - $data[birth_year];	
	if($age < 20) $tmp = 10;
	else if($age < 30) $tmp = 20;
	else if($age < 40) $tmp = 30;
	else if($age < 50) $tmp = 40;
	else if($age < 60) $tmp = 50;
	else if($age >= 60) $tmp = 60;

	$cnt[$tmp][$data[sex]][$data[rdt]] += $data[cnt];	
}
?>

<div class="title title_top">���ɺ� ȸ���м� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=data&no=21')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<form method="post">

<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>�Ⱓ����</td>
	<td>
	<select name="year">
	<? for ($i=0;$i<3;$i++){ $y = date("Y") - $i; ?>
	<option value="<?=$y?>" <?=$selected[year][$y]?>><?=$y?>
	<? } ?>
	</select>��
	<select name="month">
	<?
	for ($i=1;$i<=12;$i++){
		$tmp = sprintf("%02d",$i);
	?>
	<option value="<?=$i?>" <?=$selected[month][$tmp]?>><?=$i?>
	<? } ?>
	</select>��
	<input type="image" src="../img/btn_search_s.gif" style="border:0" align="absmiddle" hspace="10">
	</td>
</tr>
</table>

</form>
<p/>
<table width="100%" cellpadding="0" cellspacing="0">
<tr><td class="rnd" colspan="15"></td></tr>
<tr class="rndbg">
	<th width="16%"><font class="small"><b>�Ϻ����</b></font></th>
	<th colspan="2"><font class="small"><b>10��</th>
	<th colspan="2"><font class="small"><b>20��</th>
	<th colspan="2"><font class="small"><b>30��</th>
	<th colspan="2"><font class="small"><b>40��</th>
	<th colspan="2"><font class="small"><b>50��</th>
	<th colspan="2"><font class="small"><b>60�̻�</th>
</tr>
<tr height=25>
	<td align="center" bgcolor="#F7F7F7"><b>����</b></td>
	<td align="center"><b>��</b></td>
	<td align="center" bgcolor="#F7F7F7"><b>��</b></td>
	<td align="center"><b>��</b></td>
	<td align="center" bgcolor="#F7F7F7"><b>��</b></td>
	<td align="center"><b>��</b></td>
	<td align="center" bgcolor="#F7F7F7"><b>��</b></td>
	<td align="center"><b>��</b></td>
	<td align="center" bgcolor="#F7F7F7"><b>��</b></td>
	<td align="center"><b>��</b></td>
	<td align="center" bgcolor="#F7F7F7"><b>��</b></td>
	<td align="center"><b>��</b></td>
	<td align="center" bgcolor="#F7F7F7"><b>��</b></td>	
</tr>
<tr><td class=rnd colspan=15></td></tr>
<? for ($i=1;$i<=$last;$i++){
	$day = $date.'-'.sprintf("%02d",$i);
	$yoil = date("w",strtotime($day));
?>
<tr height=25>
	<td align="center" bgcolor="#F7F7F7"><font class="ver8" color="444444"><?=$day?> (<?=$r_yoil[$yoil]?>)</td>
	<td style="text-align:right;padding-right:10px"><font class="ver8" color="6C6C6C"><b><?=number_format($cnt['10']['m'][$day])?></b></font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class="ver8" color=EC4E00><b><?=number_format($cnt['10']['w'][$day])?></b></font></td>
	<td style="text-align:right;padding-right:10px"><font class="ver8" color="6C6C6C"><b><?=number_format($cnt['20']['m'][$day])?></b></font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class="ver8" color=EC4E00><b><?=number_format($cnt['20']['w'][$day])?></b></font></td><td style="text-align:right;padding-right:10px"><font class="ver8" color="6C6C6C"><b><?=number_format($cnt['30']['m'][$day])?></b></font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class="ver8" color=EC4E00><b><?=number_format($cnt['30']['w'][$day])?></b></font></td><td style="text-align:right;padding-right:10px"><font class="ver8" color="6C6C6C"><b><?=number_format($cnt['40']['m'][$day])?></b></font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class="ver8" color=EC4E00><b><?=number_format($cnt['40']['w'][$day])?></b></font></td><td style="text-align:right;padding-right:10px"><font class="ver8" color="6C6C6C"><b><?=number_format($cnt['50']['m'][$day])?></b></font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class="ver8" color=EC4E00><b><?=number_format($cnt['50']['w'][$day])?></b></font></td>
	<td style="text-align:right;padding-right:10px"><font class="ver8" color="6C6C6C"><b><?=number_format($cnt['60']['m'][$day])?></b></font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class="ver8" color=EC4E00><b><?=number_format($cnt['60']['w'][$day])?></b></font></td>
	
</tr>
<tr><td colspan=15 class=rndline></td></tr>
<? } ?>

<tr><td colspan=15 bgcolor=A3A3A3></td></tr>
<tr height=25 bgcolor="#C5C5C5">
	<td align="center" bgcolor="#EDEDED">�հ�</td>
	<td style="text-align:right;padding-right:10px" bgcolor="white"><font class="ver8" color="6C6C6C"><b><?=number_format(@array_sum($cnt['10']['m']))?></b></font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class="ver8" color="1259C3"><b><?=number_format(@array_sum($cnt['10']['w']))?></b></font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="white"><font class="ver8" color="6C6C6C"><b><?=number_format(@array_sum($cnt['20']['m']))?></b></font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class="ver8" color="1259C3"><b><?=number_format(@array_sum($cnt['20']['w']))?></b></font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="white"><font class="ver8" color="6C6C6C"><b><?=number_format(@array_sum($cnt['30']['m']))?></b></font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class="ver8" color="1259C3"><b><?=number_format(@array_sum($cnt['30']['w']))?></b></font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="white"><font class="ver8" color="6C6C6C"><b><?=number_format(@array_sum($cnt['40']['m']))?></b></font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class="ver8" color="1259C3"><b><?=number_format(@array_sum($cnt['40']['w']))?></b></font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="white"><font class="ver8" color="6C6C6C"><b><?=number_format(@array_sum($cnt['50']['m']))?></b></font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class="ver8" color="1259C3"><b><?=number_format(@array_sum($cnt['50']['w']))?></b></font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="white"><font class="ver8" color="6C6C6C"><b><?=number_format(@array_sum($cnt['60']['m']))?></b></font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class="ver8" color="1259C3"><b><?=number_format(@array_sum($cnt['60']['w']))?></b></font></td>
</tr>
<tr><td colspan=15 class=rndline></td></tr>
</table>
<p/>
<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�� ���ɺ�/���� ȸ���м��� �Ա�Ȯ����(�����Ϸ���) �����̸�, �ֹ���ұݾ��� ���� ����ڷ��Դϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG01','#F7F7F7')</script>



<script>table_design_load();</script>

<? include "../_footer.php"; ?>