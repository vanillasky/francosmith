<?

$location = "������ > �������� �м�";
include "../_header.php";

$r_yoil = array("��","��","ȭ","��","��","��","��");

$year = ($_POST[year]) ? $_POST[year] : date("Y");
$month = ($_POST[month]) ? sprintf("%02d",$_POST[month]) : date("m");

$selected[year][$year] = "selected";
$selected[month][$month] = "selected";

$date = $year."-".sprintf("%02d",$month);
$last = date("t",strtotime($date."-01"));

$query = "
select substring(cdt,1,10) as odt,settlekind,sum(prn_settleprice) as price,count(*) as cnt from
	".GD_ORDER."
where
	substring(cdt,1,7) = '$date'
	and step > 0
	and step2 = 0
	group by odt,settlekind
";
$res = $db->query($query);
while ($data=$db->fetch($res)){
	$cnt[$data[settlekind]][$data[odt]] += $data[cnt];
	$sum[$data[settlekind]][$data[odt]] += $data[price];
}
?>

<div class="title title_top">�������� �м� <span>���� �ֹ�, �Ա�, ��ۺ� ������ ��ȸ�մϴ�</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=data&no=13')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

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
<tr><td class=rnd colspan=11></td></tr>
<tr class=rndbg>
	<th width="16%" ><font class=small><b>�Ϻ����</b></font></th>
	<th width="16%" colspan=2><font class=small><b>������</b></font></th>
	<th width="16%" colspan=2><font class=small><b>�ſ�ī��</b></font></th>
	<th width="16%" colspan=2><font class=small><b>������ü</b></font></th>
	<th width="16%" colspan=2><font class=small><b>�������</b></font></th>
	<th width="16%" colspan=2><font class=small><b>�ڵ���</b></font></th>
</tr>
<tr><td class=rnd colspan=11></td></tr>
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
</tr>

<tr><td class=rnd colspan=11></td></tr>
<? for ($i=1;$i<=$last;$i++){
	$day = $date.'-'.sprintf("%02d",$i);	
	$yoil = date("w",strtotime($day));
?>

<tr height=25>
	<td align=center bgcolor="#F7F7F7"><font class=ver8 color=444444><?=$day?> (<?=$r_yoil[$yoil]?>)</td>
	<td style="text-align:right;padding-right:10px"><font class=ver8 color=6C6C6C><b><?=number_format($cnt[a][$day])?></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class=ver8 color=EC4E00><b><?=number_format($sum[a][$day])?></td>
	<td style="text-align:right;padding-right:10px"><font class=ver8 color=6C6C6C><b><?=number_format($cnt[c][$day])?></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class=ver8 color=EC4E00><b><?=number_format($sum[c][$day])?></td>
	<td style="text-align:right;padding-right:10px"><font class=ver8 color=6C6C6C><b><?=number_format($cnt[o][$day])?></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class=ver8 color=EC4E00><b><?=number_format($sum[o][$day])?></td>
	<td style="text-align:right;padding-right:10px"><font class=ver8 color=6C6C6C><b><?=number_format($cnt[v][$day])?></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class=ver8 color=EC4E00><b><?=number_format($sum[v][$day])?></td>
	<td style="text-align:right;padding-right:10px"><font class=ver8 color=6C6C6C><b><?=number_format($cnt[h][$day])?></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class=ver8 color=EC4E00><b><?=number_format($sum[h][$day])?></td>
</tr>
<tr><td colspan=11 class=rndline></td></tr>
<? } ?>

<tr><td colspan=11 bgcolor=A3A3A3></td></tr>
<tr height=25 bgcolor="#C5C5C5">
	<td align=center bgcolor="#EDEDED">�հ�</td>
	<td style="text-align:right;padding-right:10px" bgcolor=white><font class=ver8 color=6C6C6C><b><?=number_format(@array_sum($cnt[a]))?></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class=ver8 color=1259C3><b><?=number_format(@array_sum($sum[a]))?></td>
	<td style="text-align:right;padding-right:10px" bgcolor=white><font class=ver8 color=6C6C6C><b><?=number_format(@array_sum($cnt[c]))?></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class=ver8 color=1259C3><b><?=number_format(@array_sum($sum[c]))?></td>
	<td style="text-align:right;padding-right:10px" bgcolor=white><font class=ver8 color=6C6C6C><b><?=number_format(@array_sum($cnt[o]))?></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class=ver8 color=1259C3><b><?=number_format(@array_sum($sum[o]))?></td>
	<td style="text-align:right;padding-right:10px" bgcolor=white><font class=ver8 color=6C6C6C><b><?=number_format(@array_sum($cnt[v]))?></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class=ver8 color=1259C3><b><?=number_format(@array_sum($sum[v]))?></td>
	<td style="text-align:right;padding-right:10px" bgcolor=white><font class=ver8 color=6C6C6C><b><?=number_format(@array_sum($cnt[h]))?></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class=ver8 color=1259C3><b><?=number_format(@array_sum($sum[h]))?></td>

</tr>
<tr><td colspan=11 class=rndline></td></tr>
</table>

<div style="padding-top:15px"></div>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�� �������ܺм��ڷ�� �Ա�Ȯ����(�����Ϸ���) �����̸�, �ֹ���ұݾ��� ���� ����ڷ��Դϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>



<script>table_design_load();</script>

<? include "../_footer.php"; ?>