<?
include "../_header.popup.php";
include "../../lib/page.class.php";
$db_table = GD_DOPT;
$orderby = "sno";
$pg = new Page($_GET[page],$_GET[page_num]);
$pg->setQuery($db_table,$where,$orderby);

$pg->exec();
$res = $db->query($pg->query);
?>

<div class=title>�⺻ �ɼ� �����ϱ�<span>�Ʒ��� �ɼǿ� ��ϵǾ��� �ɼ��� �⺻�ɼ����� �����մϴ�.</span></div>
<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td class=rnd colspan=6></td></tr>
<tr class=rndbg>
	<th width=60>��ȣ</th>
	<th>����</th>
	<th>�����</th>
	<th>����</th>
	<th>����</th>
</tr>
<tr><td class=rnd colspan=6></td></tr>
<col  align=center width="60"><col ><col align=center  width="150"><col align=center  width="60"><col align=center  width="60"><col align=center  width="60">
<?while ($data=$db->fetch($res)){?>
<tr><td height=4 colspan=5></td></tr>
<tr height=25>
	<td><font class=ver8 color=616161><?=$pg->idx--?></td>
	<td><a href="popup.dopt_register.php?sno=<?=$data[sno]?>&mode=dopt_modify"><?=$data[title]?></a></td>
	<td><?=substr($data[regdt],0,10)?></td>
	<td><a href="popup.dopt_register.php?mode=dopt_modify&sno=<?=$data[sno]?>"><img src="../img/i_edit.gif" align="absmiddle" border="0"></td>
	<td><a href="indb.dopt.php?mode=dopt_del&sno=<?=$data[sno]?>" target="hiddenactfrm"><img src="../img/i_del.gif" align="absmiddle" border="0"></td>
</tr>
<tr><td height=4 colspan=6></td></tr>
<tr><td colspan=6 class=rndline></td></tr>
<?}?>
</table>
<div align="right" style="padding:10 10 0 0"><a href="popup.dopt_register.php?mode=dopt_register"><img src="../img/btn_optionbasket_new.gif" align="absmiddle" border="0"></a></div>
<div align=center class=pageNavi><font class=ver8><?=$pg->page[navi]?></font></div>
<script>table_design_load();</script>
<iframe name="hiddenactfrm" width="100%" height="0" frameborder=0></iframe>