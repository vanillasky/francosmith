<?

$location = "������ > ������ �ֹ��м�";
include "../_header.php";

$r_yoil = array("��","��","ȭ","��","��","��","��");

$_param = array(
	'group' => 'sido',
	'columns' => 'sido',
);
$arr_sido = Core::loader('Zipcode')->get($_param);

$year = ($_POST[year]) ? $_POST['year'] : date("Y");
$month = ($_POST[month]) ? sprintf("%02d",$_POST[month]) : date("m");

$selected['year'][$year] = "selected";
$selected['month'][$month] = "selected";

$date = $year."-".sprintf("%02d",$month);
$last = date("t",strtotime($date."-01"));

$query = "
select substring(cdt,1,10) as odt,substring(address,1,2) area,sum(prn_settleprice) as price,count(*) as cnt from
	".GD_ORDER."
where
	orddt like '$date%'
	and zipcode != ''
	and step > 0
	and step2 = 0
	group by odt,area";
$res = $db->query($query);
while ($data=$db->fetch($res)){
	$tmp = trim($data['area']);

	$cnt[$tmp][$data[odt]] += $data['cnt'];
	$sum[$tmp][$data[odt]] += $data['price'];
	$totcnt += $data['cnt'];
	$totprice += $data['price'];
}

$colspan = $arr_sido->rowCount() + 1;
?>

<div class="title title_top">������ �ֹ��м� <span>�� ������ ������ �ֹ� �Ǽ��� �ֹ� �ݾ��� ��ȸ �մϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=data&no=15')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<form method="post">

<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>�Ⱓ����</td>
	<td>
	<select name="year">
	<? for ($i=0;$i<3;$i++){ $y = date("Y") - $i; ?>
	<option value="<?=$y?>" <?=$selected['year'][$y]?>><?=$y?>
	<? } ?>
	</select>��
	<select name="month">
	<?
	for ($i=1;$i<=12;$i++){
		$tmp = sprintf("%02d",$i);
	?>
	<option value="<?=$i?>" <?=$selected['month'][$tmp]?>><?=$i?>
	<? } ?>
	</select>��
	<input type=image src="../img/btn_search_s.gif" style="border:0" align="absmiddle" hspace=10 />
	</td>
</tr>
</table>

</form>

<table width="100%" cellpadding="0" cellspacing="0">
<tr><td height="30" align="right" style="padding-right:15"><font class="extext">* �Ʒ� �ڷ�� <b>�Ա�Ȯ����(�����Ϸ���)</b> �����̸�, <b>�ֹ���ұݾ��� ����</b> ����ڷ��Դϴ�.</td></tr>
</table>

<table width="100%" cellpadding="0" cellspacing="0">
<tr><td class="rnd" colspan="<?=$colspan?>"></td></tr>
<tr class="rndbg">
	<th width="10%"><font class="small"><b>�Ϻ����</b></font></th>
	<?foreach($arr_sido as $v){?>
	<th width="<?=(90/count($arr_sido))?>%"><font class="small"><b><?=$v['sido']?></b></font></th>
	<?}?>
</tr>
<tr><td class="rnd" colspan="50"></td></tr>
<tr height="25" class="small">
	<td align="center" bgcolor="#f3f3f3"><b>����</b></td>
	<?
	$i=0;
	foreach($arr_sido as $v){
		if($i%2 == 1) $bg = "#f3f3f3";
		else $bg = "#FFFFFF";
		$i++;
	?>
	<td align="center" bgcolor="<?=$bg?>"><b>�ݾ�(��)</b></td>
	<?}?>
</tr>
<tr><td class="rnd" colspan="<?=$colspan?>"></td></tr>
<? for ($j=1;$j<=$last;$j++){
	$day = $date.'-'.sprintf("%02d",$j);
	$yoil = date("w",strtotime($day));
?>

<tr height="25">
	<td align="center" bgcolor="#f3f3f3"><font class="ver8" color="444444"><?=$day?> (<?=$r_yoil[$yoil]?>)</td>
	<?
	$i=0;
	foreach($arr_sido as $v){
		if($i%2 == 1) $bg = "#f3f3f3";
		else $bg = "#FFFFFF";
		$i++;
	?>
	<td style="text-align:right;padding-right:5px" bgcolor="<?=$bg?>"><font class="ver8" color="EC4E00"><b><?=number_format($sum[$v['sido']][$day])?></b></font><font class="small1">(<?=number_format($cnt[$v['sido']][$day])?>)</font></td>
	<?}?>
</tr>
<tr><td colspan="<?=$colspan?>" class="rndline"></td></tr>
<? } ?>

<tr><td colspan="<?=$colspan?>" bgcolor="#A3A3A3"></td></tr>
<tr height=25 bgcolor="#C5C5C5">
	<td align="center" bgcolor="#EDEDED">�հ�</td>
	<?
	$i=0;
	foreach($arr_sido as $v){
		if($i%2 == 1) $bg = "#f3f3f3";
		else $bg = "#FFFFFF";
		$i++;
	?>
	<td style="text-align:right;padding-right:5px" bgcolor='<?=$bg?>'><font class="ver8" color="#1259C3"><b><?=number_format(@array_sum($sum[$v['sido']]))?></b></font><div>(<?=number_format(@array_sum($cnt[$v['sido']]))?>)</div></td>
	<?}?>
</tr>
<tr><td colspan="<?=$colspan?>" class="rndline"></td></tr>
<tr height="25">
	<td align="center" bgcolor="#EDEDED">����</td>
	<td style="text-align:right;padding-right:5px" colspan="<?=($colspan - 1)?>"><font class="ver8" color="#1259C3"><b><?=number_format($totprice)?></b></font><div>(<?=number_format($totcnt)?>)</div></td>
</tr>
<tr><td colspan="<?=$colspan?>" class="rndline"></td></tr>
</table>

<div style="padding-top:15px"></div>

<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />�� ������ �м��� �Ա�Ȯ����(�����Ϸ���) �����̸�, �ֹ���ұݾ��� ���� ����ڷ��Դϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>



<script>table_design_load();</script>

<? include "../_footer.php"; ?>
