<?
include "../lib.php";
$query = "SELECT sno, name, url, target FROM ".GD_CONTEXTMENU." WHERE m_no = '".$sess['m_no']."'";
$rs = $db->query($query);
?>


<div class="title title_top">메뉴 편집</div>

<form name="frmContextMenuForm">
<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td class=rnd colspan=12></td></tr>
<tr class=rndbg>
	<th width="100">메뉴명</th>
	<th>URL</th>
	<th width="60">&nbsp;</th>
</tr>
<tr><td class=rnd colspan=12></td></tr>
<? while ($row = $db->fetch($rs,1)) { ?>
<tr height=30 id="el-contextmenu-config-row-<?=$row['sno']?>">
	<td style="border-bottom:1px solid #DCD8D6"><?=$row['name']?></td>
	<td style="border-bottom:1px solid #DCD8D6"><?=$row['url']?></td>
	<td style="border-bottom:1px solid #DCD8D6"><a href="javascript:void(0);" onClick="nsGodoContextMenu.setup.mod('<?=$row['sno']?>');"><img src="../img/i_edit.gif"></a> <a href="javascript:void(0);" onClick="nsGodoContextMenu.setup.del('<?=$row['sno']?>');"><img src="../img/i_del.gif"></a></td>
</tr>
<? } ?>
</table>

</form>

<div class="context_menu_form_button-wrap">
	<a href="javascript:void(0);" onClick="nsGodoContextMenu.setup.close()"><img src="../img/btn_confirm.gif"></a>
</div>