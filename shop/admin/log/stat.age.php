<?

$location = "������ > ���ɺ� �ֹ��м�";
include "../_header.php";

$r_yoil = array("��","��","ȭ","��","��","��","��");

$year = ($_POST[year]) ? $_POST[year] : date("Y", G_CONST_NOW);
$month = ($_POST[month]) ? sprintf("%02d",$_POST[month]) : date("m", G_CONST_NOW);

$selected[year][$year] = "selected";
$selected[month][$month] = "selected";

$date = $year."-".sprintf("%02d",$month);
$last = date("t",strtotime($date."-01"));

$query = "
select substring(cdt,1,10) as odt,b.birth_year,sum(a.prn_settleprice) as price,count(*) as cnt from
	".GD_ORDER." a ,".GD_MEMBER." b
where
	a.m_no = b.m_no
	and a.orddt like '$date%'
	and b.birth_year != ''
	and a.step > 0
	and a.step2 = 0
	group by odt,b.birth_year";
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

	$cnt[$tmp][$data[odt]] += $data[cnt];
	$sum[$tmp][$data[odt]] += $data[price];
}
?>

<div class="title title_top">���ɺ� �ֹ��м� <span>�� ������ ���ɺ� �ֹ� �Ǽ��� �ֹ� �ݾ��� ��ȸ �մϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=data&no=14')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<form method=post>

<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>�Ⱓ����</td>
	<td>
	<select name=year>
	<? for ($i=0;$i<3;$i++){ $y = date("Y") - $i; ?>
	<option value="<?=$y?>" <?=$selected[year][$y]?>><?=$y?>
	<? } ?>
	</select>��
	<select name=month>
	<?
	for ($i=1;$i<=12;$i++){
		$tmp = sprintf("%02d",$i);
	?>
	<option value="<?=$i?>" <?=$selected[month][$tmp]?>><?=$i?>
	<? } ?>
	</select>��
	<input type=image src="../img/btn_search_s.gif" style="border:0" align=absmiddle hspace=10>
	</td>
</tr>
</table>

</form>

<table width=100% cellpadding=0 cellspacing=0>
<tr><td height=30 align=right style="padding-right:15"><font class=extext>* �Ʒ� �ڷ�� <b>�Ա�Ȯ����(�����Ϸ���)</b> �����̸�, <b>�ֹ���ұݾ��� ����</b> ����ڷ��Դϴ�.</td></tr>
</table>

<table width=100% cellpadding=0 cellspacing=0>
<tr><td class=rnd colspan=13></td></tr>
<tr class=rndbg>
	<th width="16%" ><font class=small><b>�Ϻ����</b></font></th>
	<th width="14%" colspan=2><font class=small><b>10��</b></font></th>
	<th width="14%" colspan=2><font class=small><b>20��</b></font></th>
	<th width="14%" colspan=2><font class=small><b>30��</b></font></th>
	<th width="14%" colspan=2><font class=small><b>40��</b></font></th>
	<th width="14%" colspan=2><font class=small><b>50��</b></font></th>
	<th width="14%" colspan=2><font class=small><b>60 �̻�</b></font></th>
</tr>
<tr><td class=rnd colspan=25></td></tr>
<tr height=25>
	<td align=center bgcolor="#F7F7F7"><b>����</b></td>
	<td align=center><b>�Ǽ�</b></td>
	<td align=center bgcolor="#F7F7F7"><b>�ݾ�</b></td>
	<td align=center><b>�Ǽ�</b></td>
	<td align=center bgcolor="#F7F7F7"><b>�ݾ�</b></td>
	<td align=center><b>�Ǽ�</b></td>
	<td align=center bgcolor="#F7F7F7"><b>�ݾ�</b></td>
	<td align=center><b>�Ǽ�</b></td>
	<td align=center bgcolor="#F7F7F7"><b>�ݾ�</b></td>
	<td align=center><b>�Ǽ�</b></td>
	<td align=center bgcolor="#F7F7F7"><b>�ݾ�</b></td>
	<td align=center><b>�Ǽ�</b></td>
	<td align=center bgcolor="#F7F7F7"><b>�ݾ�</b></td>
</tr>
<tr><td class=rnd colspan=13></td></tr>
<? for ($i=1;$i<=$last;$i++){
	$day = $date.'-'.sprintf("%02d",$i);	
	$yoil = date("w",strtotime($day));
?>

<tr height=25>
	<td align=center bgcolor="#F7F7F7"><font class=ver8 color=444444><?=$day?> (<?=$r_yoil[$yoil]?>)</td>
	<td style="text-align:right;padding-right:10px"><font class=ver8 color=6C6C6C><b><?=number_format($cnt[10][$day])?></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class=ver8 color=EC4E00><b><?=number_format($sum[10][$day])?></td>
	<td style="text-align:right;padding-right:10px"><font class=ver8 color=6C6C6C><b><?=number_format($cnt[20][$day])?></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class=ver8 color=EC4E00><b><?=number_format($sum[20][$day])?></td>
	<td style="text-align:right;padding-right:10px"><font class=ver8 color=6C6C6C><b><?=number_format($cnt[30][$day])?></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class=ver8 color=EC4E00><b><?=number_format($sum[30][$day])?></td>
	<td style="text-align:right;padding-right:10px"><font class=ver8 color=6C6C6C><b><?=number_format($cnt[40][$day])?></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class=ver8 color=EC4E00><b><?=number_format($sum[40][$day])?></td>
	<td style="text-align:right;padding-right:10px"><font class=ver8 color=6C6C6C><b><?=number_format($cnt[50][$day])?></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class=ver8 color=EC4E00><b><?=number_format($sum[50][$day])?></td>
	<td style="text-align:right;padding-right:10px"><font class=ver8 color=6C6C6C><b><?=number_format($cnt[60][$day])?></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class=ver8 color=EC4E00><b><?=number_format($sum[60][$day])?></td>
</tr>
<tr><td colspan=13 class=rndline></td></tr>
<? } ?>

<tr><td colspan=13 bgcolor=A3A3A3></td></tr>
<tr height=25 bgcolor="#C5C5C5">
	<td align=center bgcolor="#EDEDED">�հ�</td>
	<td style="text-align:right;padding-right:10px" bgcolor=white><font class=ver8 color=6C6C6C><b><?=number_format(@array_sum($cnt[10]))?></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class=ver8 color=1259C3><b><?=number_format(@array_sum($sum[10]))?></td>
	<td style="text-align:right;padding-right:10px" bgcolor=white><font class=ver8 color=6C6C6C><b><?=number_format(@array_sum($cnt[20]))?></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class=ver8 color=1259C3><b><?=number_format(@array_sum($sum[20]))?></td>
	<td style="text-align:right;padding-right:10px" bgcolor=white><font class=ver8 color=6C6C6C><b><?=number_format(@array_sum($cnt[30]))?></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class=ver8 color=1259C3><b><?=number_format(@array_sum($sum[30]))?></td>
	<td style="text-align:right;padding-right:10px" bgcolor=white><font class=ver8 color=6C6C6C><b><?=number_format(@array_sum($cnt[40]))?></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class=ver8 color=1259C3><b><?=number_format(@array_sum($sum[40]))?></td>
	<td style="text-align:right;padding-right:10px" bgcolor=white><font class=ver8 color=6C6C6C><b><?=number_format(@array_sum($cnt[50]))?></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class=ver8 color=1259C3><b><?=number_format(@array_sum($sum[50]))?></td>
	<td style="text-align:right;padding-right:10px" bgcolor=white><font class=ver8 color=6C6C6C><b><?=number_format(@array_sum($cnt[60]))?></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class=ver8 color=1259C3><b><?=number_format(@array_sum($sum[60]))?></td>
</tr>
<tr><td colspan=13 class=rndline></td></tr>
</table>

<div style="padding-top:15px"></div>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�� ���ɺ� �м��� �Ա�Ȯ����(�����Ϸ���) �����̸�, �ֹ���ұݾ��� ���� ����ڷ��Դϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>



<script>table_design_load();</script>

<? include "../_footer.php"; ?>
