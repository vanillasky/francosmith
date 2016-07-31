<?

$location = "������ > ������ ȸ���м�";
include "../_header.php";

$r_yoil = array("��","��","ȭ","��","��","��","��");

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
	if($data[zip] < 200) $tmp = '����';
	else if($data[zip] < 358) $tmp = '�泲';
	else if($data[zip] < 396) $tmp = '���';
	else if($data[zip] < 488) $tmp = '���';
	else if($data[zip] < 551) $tmp = '����';
	else if($data[zip] < 600) $tmp = '����';
	else if($data[zip] < 679) $tmp = '�泲';
	else if($data[zip] < 698) $tmp = '����';
	else if($data[zip] < 800) $tmp = '���';

	$cnt[$tmp][$data[sex]][$data[rdt]] += $data[cnt];
	$tot += $data[cnt];

	if ($data['sex'] == 'none') {
		$extra = true;
	}
}
?>

<div class="title title_top">������ ȸ���м� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=data&no=22')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

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

<?
// �÷���, �÷� ������
$cols = $extra ? 3 : 2;
$col_size =  $extra ? '7%' : '10%';
?>
<table width="100%" cellpadding="0" cellspacing="0">
<tr><td class="rnd" colspan="30"></td></tr>
<tr class="rndbg">
	<th width="16%"><font class=small><b>�Ϻ����</b></font></th>
	<th  colspan="<?=$cols?>"><font class=small><b>����</b></font></th>
	<th  colspan="<?=$cols?>"><font class=small><b>���</b></font></th>
	<th  colspan="<?=$cols?>"><font class=small><b>�泲</b></font></th>
	<th  colspan="<?=$cols?>"><font class=small><b>���</b></font></th>
	<th  colspan="<?=$cols?>"><font class=small><b>����</b></font></th>
	<th  colspan="<?=$cols?>"><font class=small><b>����</b></font></th>
	<th  colspan="<?=$cols?>"><font class=small><b>�泲</b></font></th>
	<th  colspan="<?=$cols?>"><font class=small><b>���</b></font></th>
	<th  colspan="<?=$cols?>"><font class=small><b>����</b></font></th>
</tr>
<tr height=25>
	<td align="center" bgcolor="#F7F7F7"><b>����</b></td>
	<td align="center"><b>��</b></td>
	<td align="center" bgcolor="#F7F7F7"><b>��</b></td>
	<? if ($extra) { ?><td align="center" bgcolor="#E7E7E7"><b>���Է�</b></td><? } ?>
	<td align="center"><b>��</b></td>
	<td align="center" bgcolor="#F7F7F7"><b>��</b></td>
	<? if ($extra) { ?><td align="center" bgcolor="#E7E7E7"><b>���Է�</b></td><? } ?>
	<td align="center"><b>��</b></td>
	<td align="center" bgcolor="#F7F7F7"><b>��</b></td>
	<? if ($extra) { ?><td align="center" bgcolor="#E7E7E7"><b>���Է�</b></td><? } ?>
	<td align="center"><b>��</b></td>
	<td align="center" bgcolor="#F7F7F7"><b>��</b></td>
	<? if ($extra) { ?><td align="center" bgcolor="#E7E7E7"><b>���Է�</b></td><? } ?>
	<td align="center"><b>��</b></td>
	<td align="center" bgcolor="#F7F7F7"><b>��</b></td>
	<? if ($extra) { ?><td align="center" bgcolor="#E7E7E7"><b>���Է�</b></td><? } ?>
	<td align="center"><b>��</b></td>
	<td align="center" bgcolor="#F7F7F7"><b>��</b></td>
	<? if ($extra) { ?><td align="center" bgcolor="#E7E7E7"><b>���Է�</b></td><? } ?>
	<td align="center"><b>��</b></td>
	<td align="center" bgcolor="#F7F7F7"><b>��</b></td>
	<? if ($extra) { ?><td align="center" bgcolor="#E7E7E7"><b>���Է�</b></td><? } ?>
	<td align="center"><b>��</b></td>
	<td align="center" bgcolor="#F7F7F7"><b>��</b></td>
	<? if ($extra) { ?><td align="center" bgcolor="#E7E7E7"><b>���Է�</b></td><? } ?>
	<td align="center"><b>��</b></td>
	<td align="center" bgcolor="#F7F7F7"><b>��</b></td>
	<? if ($extra) { ?><td align="center" bgcolor="#E7E7E7"><b>���Է�</b></td><? } ?>
</tr>
<tr><td class=rnd colspan=30></td></tr>
<? for ($i=1;$i<=$last;$i++){
	$day = $date.'-'.sprintf("%02d",$i);
	$yoil = date("w",strtotime($day));
?>
<tr height=25>
	<td align="center" bgcolor="#F7F7F7"><font class="ver8" color="444444"><?=$day?> (<?=$r_yoil[$yoil]?>)</td>
	<td style="text-align:right;padding-right:10px"><font class="ver8" color="6C6C6C"><b><?=number_format($cnt['����']['m'][$day])?></b></font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class="ver8" color=EC4E00><b><?=number_format($cnt['����']['w'][$day])?></b></font></td>
	<? if ($extra) { ?><td style="text-align:right;padding-right:10px" bgcolor="#E7E7E7"><font class="ver8" color=6C6C6C><b><?=number_format($cnt['����']['none'][$day])?></b></font></td><? } ?>
	<td style="text-align:right;padding-right:10px"><font class="ver8" color="6C6C6C"><b><?=number_format($cnt['���']['m'][$day])?></b></font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class="ver8" color=EC4E00><b><?=number_format($cnt['���']['w'][$day])?></b></font></td>
	<? if ($extra) { ?><td style="text-align:right;padding-right:10px" bgcolor="#E7E7E7"><font class="ver8" color=6C6C6C><b><?=number_format($cnt['���']['none'][$day])?></b></font></td><? } ?>
	<td style="text-align:right;padding-right:10px"><font class="ver8" color="6C6C6C"><b><?=number_format($cnt['�泲']['m'][$day])?></b></font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class="ver8" color=EC4E00><b><?=number_format($cnt['�泲']['w'][$day])?></b></font></td>
	<? if ($extra) { ?><td style="text-align:right;padding-right:10px" bgcolor="#E7E7E7"><font class="ver8" color=6C6C6C><b><?=number_format($cnt['�泲']['none'][$day])?></b></font></td><? } ?>
	<td style="text-align:right;padding-right:10px"><font class="ver8" color="6C6C6C"><b><?=number_format($cnt['���']['m'][$day])?></b></font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class="ver8" color=EC4E00><b><?=number_format($cnt['���']['w'][$day])?></b></font></td>
	<? if ($extra) { ?><td style="text-align:right;padding-right:10px" bgcolor="#E7E7E7"><font class="ver8" color=6C6C6C><b><?=number_format($cnt['���']['none'][$day])?></b></font></td><? } ?>
	<td style="text-align:right;padding-right:10px"><font class="ver8" color="6C6C6C"><b><?=number_format($cnt['����']['m'][$day])?></b></font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class="ver8" color=EC4E00><b><?=number_format($cnt['����']['w'][$day])?></b></font></td>
	<? if ($extra) { ?><td style="text-align:right;padding-right:10px" bgcolor="#E7E7E7"><font class="ver8" color=6C6C6C><b><?=number_format($cnt['����']['none'][$day])?></b></font></td><? } ?>
	<td style="text-align:right;padding-right:10px"><font class="ver8" color="6C6C6C"><b><?=number_format($cnt['����']['m'][$day])?></b></font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class="ver8" color=EC4E00><b><?=number_format($cnt['����']['w'][$day])?></b></font></td>
	<? if ($extra) { ?><td style="text-align:right;padding-right:10px" bgcolor="#E7E7E7"><font class="ver8" color=6C6C6C><b><?=number_format($cnt['����']['none'][$day])?></b></font></td><? } ?>
	<td style="text-align:right;padding-right:10px"><font class="ver8" color="6C6C6C"><b><?=number_format($cnt['�泲']['m'][$day])?></b></font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class="ver8" color=EC4E00><b><?=number_format($cnt['�泲']['w'][$day])?></b></font></td>
	<? if ($extra) { ?><td style="text-align:right;padding-right:10px" bgcolor="#E7E7E7"><font class="ver8" color=6C6C6C><b><?=number_format($cnt['�泲']['none'][$day])?></b></font></td><? } ?>
	<td style="text-align:right;padding-right:10px"><font class="ver8" color="6C6C6C"><b><?=number_format($cnt['���']['m'][$day])?></b></font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class="ver8" color=EC4E00><b><?=number_format($cnt['���']['w'][$day])?></b></font></td>
	<? if ($extra) { ?><td style="text-align:right;padding-right:10px" bgcolor="#E7E7E7"><font class="ver8" color=6C6C6C><b><?=number_format($cnt['���']['none'][$day])?></b></font></td><? } ?>
	<td style="text-align:right;padding-right:10px"><font class="ver8" color="6C6C6C"><b><?=number_format($cnt['����']['m'][$day])?></b></font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class="ver8" color=EC4E00><b><?=number_format($cnt['����']['w'][$day])?></b></font></td>
	<? if ($extra) { ?><td style="text-align:right;padding-right:10px" bgcolor="#E7E7E7"><font class="ver8" color=6C6C6C><b><?=number_format($cnt['����']['none'][$day])?></b></font></td><? } ?>
</tr>
<tr><td colspan=30 class=rndline></td></tr>
<? } ?>

<tr><td colspan=30 bgcolor=A3A3A3></td></tr>
<tr height=25 bgcolor="#C5C5C5">
	<td align="center" bgcolor="#EDEDED"><?=number_format($tot)?></td>
	<td style="text-align:right;padding-right:10px" bgcolor="white"><font class="ver8" color="6C6C6C"><b><?=number_format(@array_sum($cnt['����']['m']))?></b></font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class="ver8" color="1259C3"><b><?=number_format(@array_sum($cnt['����']['w']))?></b></font></td>
	<? if ($extra) { ?><td style="text-align:right;padding-right:10px" bgcolor="#E7E7E7"><font class="ver8" color=6C6C6C><b><?=number_format(@array_sum($cnt['����']['none']))?></b></font></td><? } ?>
	<td style="text-align:right;padding-right:10px" bgcolor="white"><font class="ver8" color="6C6C6C"><b><?=number_format(@array_sum($cnt['���']['m']))?></b></font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class="ver8" color="1259C3"><b><?=number_format(@array_sum($cnt['���']['w']))?></b></font></td>
	<? if ($extra) { ?><td style="text-align:right;padding-right:10px" bgcolor="#E7E7E7"><font class="ver8" color=6C6C6C><b><?=number_format(@array_sum($cnt['���']['none']))?></b></font></td><? } ?>
	<td style="text-align:right;padding-right:10px" bgcolor="white"><font class="ver8" color="6C6C6C"><b><?=number_format(@array_sum($cnt['�泲']['m']))?></b></font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class="ver8" color="1259C3"><b><?=number_format(@array_sum($cnt['�泲']['w']))?></b></font></td>
	<? if ($extra) { ?><td style="text-align:right;padding-right:10px" bgcolor="#E7E7E7"><font class="ver8" color=6C6C6C><b><?=number_format(@array_sum($cnt['�泲']['none']))?></b></font></td><? } ?>
	<td style="text-align:right;padding-right:10px" bgcolor="white"><font class="ver8" color="6C6C6C"><b><?=number_format(@array_sum($cnt['���']['m']))?></b></font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class="ver8" color="1259C3"><b><?=number_format(@array_sum($cnt['���']['w']))?></b></font></td>
	<? if ($extra) { ?><td style="text-align:right;padding-right:10px" bgcolor="#E7E7E7"><font class="ver8" color=6C6C6C><b><?=number_format(@array_sum($cnt['���']['none']))?></b></font></td><? } ?>
	<td style="text-align:right;padding-right:10px" bgcolor="white"><font class="ver8" color="6C6C6C"><b><?=number_format(@array_sum($cnt['����']['m']))?></b></font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class="ver8" color="1259C3"><b><?=number_format(@array_sum($cnt['����']['w']))?></b></font></td>
	<? if ($extra) { ?><td style="text-align:right;padding-right:10px" bgcolor="#E7E7E7"><font class="ver8" color=6C6C6C><b><?=number_format(@array_sum($cnt['����']['none']))?></b></font></td><? } ?>
	<td style="text-align:right;padding-right:10px" bgcolor="white"><font class="ver8" color="6C6C6C"><b><?=number_format(@array_sum($cnt['����']['m']))?></b></font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class="ver8" color="1259C3"><b><?=number_format(@array_sum($cnt['����']['w']))?></b></font></td>
	<? if ($extra) { ?><td style="text-align:right;padding-right:10px" bgcolor="#E7E7E7"><font class="ver8" color=6C6C6C><b><?=number_format(@array_sum($cnt['����']['none']))?></b></font></td><? } ?>
	<td style="text-align:right;padding-right:10px" bgcolor="white"><font class="ver8" color="6C6C6C"><b><?=number_format(@array_sum($cnt['�泲']['m']))?></b></font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class="ver8" color="1259C3"><b><?=number_format(@array_sum($cnt['�泲']['w']))?></b></font></td>
	<? if ($extra) { ?><td style="text-align:right;padding-right:10px" bgcolor="#E7E7E7"><font class="ver8" color=6C6C6C><b><?=number_format(@array_sum($cnt['�泲']['none']))?></b></font></td><? } ?>
	<td style="text-align:right;padding-right:10px" bgcolor="white"><font class="ver8" color="6C6C6C"><b><?=number_format(@array_sum($cnt['���']['m']))?></b></font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class="ver8" color="1259C3"><b><?=number_format(@array_sum($cnt['���']['w']))?></b></font></td>
	<? if ($extra) { ?><td style="text-align:right;padding-right:10px" bgcolor="#E7E7E7"><font class="ver8" color=6C6C6C><b><?=number_format(@array_sum($cnt['���']['none']))?></b></font></td><? } ?>
	<td style="text-align:right;padding-right:10px" bgcolor="white"><font class="ver8" color="6C6C6C"><b><?=number_format(@array_sum($cnt['����']['m']))?></b></font></td>
	<td style="text-align:right;padding-right:10px" bgcolor="#f7f7f7"><font class="ver8" color="1259C3"><b><?=number_format(@array_sum($cnt['����']['w']))?></b></font></td>
	<? if ($extra) { ?><td style="text-align:right;padding-right:10px" bgcolor="#E7E7E7"><font class="ver8" color=6C6C6C><b><?=number_format(@array_sum($cnt['����']['none']))?></b></font></td><? } ?>
</tr>
<tr><td colspan=30 class=rndline></td></tr>
</table>
<p/>
<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�� ������ ȸ���м��� �Ա�Ȯ����(�����Ϸ���) �����̸�, �ֹ���ұݾ��� ���� ����ڷ��Դϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG01','#F7F7F7')</script>



<script>table_design_load();</script>

<? include "../_footer.php"; ?>