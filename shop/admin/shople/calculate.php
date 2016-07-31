<?php
$scriptLoad='<link rel="styleSheet" href="./_inc/style.css">';
$location = "���� > ���� ���긮��Ʈ";

include "../_header.php";
require_once ('./_inc/config.inc.php');

// �Ķ����
$year = isset($_GET['year']) ? $_GET['year'] : date('Y');

// ���� �Ǹ�����
$shople = Core::loader('shople');
$shopleCfg = $shople->cfg;

// �⵵�� ���� ���� ��������
$method = 'GET_CALCULATE';
$param = array();
$data = array(
	'year' => $year
);
$rs = $shople->request($method,$param,$data);
$sheet = $rs['body'];

// ���� ���� ��������
$method = 'GET_CALCULATEINFO';
$param = array();
$data = array();
$rs = $shople->request($method,$param,$data);
$shop_info = $rs['body'];
?>

<div class="title title_top">���� ���긮��Ʈ<span>���� ó���� ������ ������ Ȯ���Ͻ� �� �ֽ��ϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=shople&no=9')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>


<div class=title>��������</div>
<table class=tb>
<col class=cellC><col class=cellL><col class=cellC><col class=cellL><col class=cellC><col class=cellL>
<tr>
	<td>��ü��</td>
	<td><?=$shop_info['shop_name']?></td>

	<td>������</td>
	<td><?=$shop_info['calc_rate']?>%</td>

	<td>������</td>
	<td>�ſ� <?=$shop_info['calc_day']?>��</td>
</tr>
</table>






<div class=title>���곻��</div>

<div style="margin:0 0 10px 0;">
	<select name="year" onChange="location.href='<?=$_SERVER['PHP_SELF']?>?year='+this.value;">
		<? for ($i=2010,$max=date('Y');$i<=$max;$i++) { ?>
		<option value="<?=$i?>" <?=$i == $year ? 'selected' : '' ?>><?=$i?>��</option>
		<? } ?>
	</select>
</div>


<table width="100%" cellpadding="0" cellspacing="0" border="0" class="gd_grid">
<col width="80">
<col width="80">
<col width="150">
<col width="150">
<col>
<col width="150">
<col width="150">
<col width="70">

<thead>
<tr class="rndbg">
	<th>�Ǹſ�</th>
	<th>�ǸŰ�</th>
	<th>�Ǹűݾ�</th>
	<th>����Ȯ����</th>
	<th>����ݾ�</th>
	<th>�����ݾ�</th>
	<th>������ݾ�</th>
	<th>�󼼳���</th>
</tr>
</thead>
<tbody>
<?
$overview['�Ѹ����'] = $overview['�������'] = 0;
for ($i=1;$i<=12;$i++) {
	$row = isset($sheet[$i]) ? $sheet[$i] : $_default;

	// �� ����,����
	$overview['�Ѹ����'] += $row['�Ǹ�']['amount'];
	$overview['�������'] += $row['Ȯ��']['amount'] - $row['����']['amount'];
?>
<tr>
	<td class="numeric"><?=$year?>�� <?=sprintf('%02d',$i)?>��</td>
	<td class="numeric"><?=number_format($row['�Ǹ�']['cnt'])?></td>
	<td class="numeric"><?=number_format($row['�Ǹ�']['amount'])?>��</td>
	<td class="numeric"><?=number_format($row['Ȯ��']['cnt'])?></td>
	<td class="numeric"><?=number_format($row['����']['amount'])?>��</td>
	<td class="numeric"><?=number_format($row['����']['cnt'])?>�� / <span class="red"><?=number_format($row['����']['amount'] * -1)?>��</span></td>
	<td class="numeric blue"><?=number_format($row['����']['amount'] - $row['����']['amount'])?>��</td>
	<td><a href="./calculate.detail.php?year=<?=$year?>&month=<?=$i?>">����</td>
</tr>
<? } ?>
</tbody>
<tr bgcolor="#f7f7f7">
	<td colspan="20" class="overview bold ar">�� �����: <?=number_format($overview['�Ѹ����'])?>��</td>
</tr>
<tr bgcolor="#f7f7f7">
	<td colspan="20" class="overview bold ar">�� �����: <?=number_format($overview['�������'])?>��</td>
</tr>
</table>


<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle"/>������ ������ ����Ǹ�, 1�� ���� ���ϱ����� ����Ȯ�� �ֹ������� �������� ����˴ϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<? include "../_footer.php"; ?>
