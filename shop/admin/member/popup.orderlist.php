<?

include "../_header.popup.php";
include "../../lib/page.class.php";

$db_table = "".GD_ORDER."";

$where[] = "m_no = '$_GET[m_no]'";
$where[] = "step=4";
$where[] = "step2=0";

$pg = new Page($_GET[page],13);
$pg->setQuery($db_table,$where,"ordno desc");
$pg->exec();

$res = $db->query($pg->query);

?>

<div class="title title_top">���� �Ϸ� ����</div>

<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td class=rnd colspan=10></td></tr>
<tr class=rndbg>
	<th>��ȣ</th>
	<th>�ֹ���ȣ</th>
	<th>��������</th>
	<th>�����ݾ�</th>
	<th>�ֹ���</th>
	<th>�Ա���</th>
	<th>�����</th>
</tr>
<tr><td class=rnd colspan=10></td></tr>
<col width=50 align=center>
<col width=60 align=center span=10>
<? while ($data=$db->fetch($res)){ ?>
<tr height=25>
	<td><?=$pg->idx--?></td>
	<td><a href="javascript:popup('../order/popup.order.php?ordno=<?=$data[ordno]?>',800,600)"><?=$data[ordno]?></a></td>
	<td><?=$r_settlekind[$data[settlekind]]?></td>
	<td><?=number_format($data[settleprice])?>��</td>
	<td><?=substr($data[orddt],0,10)?></td>
	<td><?=substr($data[cdt],0,10)?></td>
	<td><?=substr($data[ddt],0,10)?></td>
</tr>
<tr><td colspan=10 class=rndline></td></tr>
<? } ?>
</table>

<div align=center style="padding:20px"><?=$pg->page[navi]?></div>