<?

$scriptLoad = '<script src="../interpark/js/popup.category.js"></script>';
include "../_header.popup.php";

?>

<script language="javascript"><!--
var spot = '<?=$_REQUEST[spot]?>';
--></script>

<div class="title title_top" style="margin-top:10px;">������ũ ī�װ� ��Ī<span>��Ī�� ������ũ ī�װ��� ã�� �����մϴ�.</span></div>

<form onsubmit="return ( getDispSrch(this) ? false : false );">
<table class=tb style="margin-bottom:10px;">
<col class=cellC><col class=cellL>
<tr>
	<td>ã�� ī�װ���</td>
	<td>
	<input type=text name=srchName id=srchName value="">
	<input type=image src="../img/btn_search_s.gif" align=absmiddle hspace=10 class=null>
	</td>
</tr>
</table>
</form>

<table width=100% cellPadding=2 cellSpacing=1 border=0 cellpadding=0 cellspacing=3 bgcolor=#AABCCC>
<col width="10%"><col width="90%">
<tr height=21 bgcolor=#E6E6E6>
	<td align=center>����</td>
	<td style="padding-left:210px">�˻����</td>
</tr>
</table>

<ul id="p_disp" onmousewheel="return iciScroll(this)" style="height:335px; background:white; overflow-y:scroll; border:2px solid #D7D7D7; border-top-width:1px; margin:0px; padding:0px;">
<table width=100% cellPadding=2 cellSpacing=1 border=0 cellpadding=0 cellspacing=3 bgcolor=#333333>
<col width="10%"><col width="90%">
<tr height=330 bgcolor=#FFFFFF>
	<td colspan=2 align=center><font color=6d6d6d>��Ī�� ī�װ��� �˻��� �� �����ϼ���.</td>
</tr>
</table>
</ul>

<script>table_design_load();</script>
</body>
</html>