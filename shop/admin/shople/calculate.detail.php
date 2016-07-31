<?php
$scriptLoad='<link rel="styleSheet" href="./_inc/style.css">';
$location = "���� > ���긮��Ʈ";

include "../_header.php";
require_once ('./_inc/config.inc.php');


// �Ķ����
$year = isset($_GET['year']) ? $_GET['year'] : date('Y');
$month = isset($_GET['month']) ? $_GET['month'] : date('n');
$calc_type = isset($_GET['calc_type']) ? $_GET['calc_type'] : '';
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$page_num = isset($_GET['page_num']) ? $_GET['page_num'] : 10;

// ���� �Ǹ�����
$shople = Core::loader('shople');
$shopleCfg = $shople->cfg;

// �⵵�� ���� ���� ��������
$method = 'GET_CALCULATE';

$data = array(
	'year' => $year,
	'month' => $month
);
$rs = $shople->request($method,$param,$data);
$overview = $rs['body'][$month];

// ���� �� ����Ʈ
$method = 'GET_CALCULATELIST';
$param = array(
	// ����¡ ����
	'page' => $page,
	'page_num' => $page_num,
);
$data = array(
	'year' => $year,
	'month' => $month,
	'calc_type' => $calc_type
);

$rs = $shople->request($method,$param,$data);
$arRow = $rs['body'];

// ����¡
$pg = Core::loader('page',$page,$page_num);
$page_navi = $pg->getNavi($rs['records']);
?>

<div class="title title_top">���긮��Ʈ<span>���� Ȯ���Ǿ� ����ó���� ������ Ȯ���Ͻ� �� �ֽ��ϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=shople&no=9')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<form name=frmList>
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>�������</td>
	<td class="noline">
		<label><input type="radio" name="calc_type" value="" <?=$calc_type == '' ? 'checked' : ''?>>��ü</label>
		<label><input type="radio" name="calc_type" value="����" <?=$calc_type == '����' ? 'checked' : ''?>>����Ϸ�</label>
		<label><input type="radio" name="calc_type" value="����" <?=$calc_type == '����' ? 'checked' : ''?>>��������</label>
	</td>
</tr>
<tr>
	<td>�Ⱓ�˻�</td>
	<td>
		<select name="year">
			<? for ($i=2010,$max=date('Y');$i<=$max;$i++) { ?>
			<option value="<?=$i?>" <?=$i == $year ? 'selected' : '' ?>><?=$i?>��</option>
			<? } ?>
		</select>

		<select name="month">
			<? for ($i=1;$i<=12;$i++) { ?>
			<option value="<?=$i?>" <?=$i == $month ? 'selected' : '' ?>><?=$i?>��</option>
			<? } ?>
		</select>

	</td>
</tr>
</table>

<div class=button_top><input type=image src="../img/btn_search2.gif"></div>

<div style="padding-top:15px"></div>


</form>


<div class=title>������Ȳ</div>
<table class=tb>
<col width="25%">
<col width="25%">
<col width="25%">
<col width="25%">
<tr bgcolor="#F6F6F6" align="center" height="25">
	<td>�Ǹ�</td>
	<td>����Ȯ��</td>
	<td>����(+)</td>
	<td>��������(-)</td>
</tr>
<tr align="center" height="25">
	<td><?=number_format($overview['�Ǹ�']['cnt'])?>��</td>
	<td><?=number_format($overview['Ȯ��']['cnt'])?>��</td>
	<td><?=number_format($overview['����']['cnt'])?>��</td>
	<td><?=number_format($overview['����']['cnt'])?>��</td>
</tr>
</table>


	</thead>

<div class=title>���곻��</div>
<div class="pageInfo ver8" style="">�� <b><?=$pg->recode[total]?></b>��, <b><?=$pg->page[now]?></b> of <?=$pg->page[total]?> Pages</div>

<table width="100%" cellpadding="0" cellspacing="0" border="0" class="gd_grid">
<col width="60">
<col width="">
<col width="120">
<col width="130">
<col width="80">
<col width="80">
<col width="80">
<col width="80">
<col width="60">
<thead>
<tr>
	<th>��ȣ</th>
	<th>��ǰ��</th>
	<th>�ֹ���ȣ</th>
	<th>����Ȯ����</th>
	<th>�����ݾ�</th>
	<th>����ݾ�</th>
	<th>��������ݾ�</th>
	<th>������</th>
	<th>ó������</th>
</tr>
</thead>
<tbody>
<?
for ($i=0,$max=sizeof($arRow);$i<$max;$i++) {
	$row = $arRow[$i];
?>
<tr>
	<td class="numeric"><?=$row['rowNo']?></td>
	<td class="al"><?=$row['prdNm']?></td>
	<td class="numeric"><?=$row['ordNo']?></td>
	<td class="date"><?=$row['pocnfrmDt']?></td>
	<td class="numeric"><?=number_format($row['selPrc'])?></td>
	<td class="numeric blue"><?=($row['calc'] > 0 ? number_format($row['calc']) : '-')?></td>
	<td class="numeric red"><?=($row['calc'] < 0 ? number_format($row['calc']) : '-')?></td>
	<td class="date"><?=$row['calc_date']?></td>
	<td><?=($row['calc_stats'] ? $row['calc_stats'] : '��ó��')?></td>
</tr>
<? } ?>
</tbody>
</table>

<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td align=center><div class=pageNavi><font class=ver8><?=$page_navi?></font></div></td>
</tr></table>


<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle"/>������ ������ ����Ǹ�, 1�� ���� ���ϱ����� ����Ȯ�� �ֹ������� �������� ����˴ϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<? include "../_footer.php"; ?>
