<?

$scriptLoad = '<script src="../interpark/js/popup.category.js"></script>';
include "../_header.popup.php";

?>

<script language="javascript"><!--
var spot = '<?=$_REQUEST[spot]?>';
--></script>

<div class="title title_top" style="margin-top:10px;">인터파크 카테고리 매칭<span>매칭할 인터파크 카테고리를 찾아 연결합니다.</span></div>

<form onsubmit="return ( getDispSrch(this) ? false : false );">
<table class=tb style="margin-bottom:10px;">
<col class=cellC><col class=cellL>
<tr>
	<td>찾을 카테고리명</td>
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
	<td align=center>선택</td>
	<td style="padding-left:210px">검색결과</td>
</tr>
</table>

<ul id="p_disp" onmousewheel="return iciScroll(this)" style="height:335px; background:white; overflow-y:scroll; border:2px solid #D7D7D7; border-top-width:1px; margin:0px; padding:0px;">
<table width=100% cellPadding=2 cellSpacing=1 border=0 cellpadding=0 cellspacing=3 bgcolor=#333333>
<col width="10%"><col width="90%">
<tr height=330 bgcolor=#FFFFFF>
	<td colspan=2 align=center><font color=6d6d6d>매칭할 카테고리를 검색한 후 선택하세요.</td>
</tr>
</table>
</ul>

<script>table_design_load();</script>
</body>
</html>