<?php
include "../_header.popup.php";
include_once("../../lib/blogshop.class.php");

$blogshop = new blogshop();
$category = $blogshop->get_category();

?>
<style>
a:link { color: #83807d; text-decoration: none }
a:visited { color: #83807d; text-decoration: none }
a:hover { color: #83807d; text-decoration: underline }
a:active { color: #ff6600; text-decoration: none }

.text01 p{ font-size: 11px; color: #847f74; font-family: "돋움"; letter-spacing:-1px; margin:5px 0; }
.title01 { font-size: 12px; color: #004682; font-family: "돋움"; letter-spacing:-1px; }
</style>
<script>
function select_category(cate_no,catnm) {
	opener.document.getElementById("blog_cate_no").value=cate_no;
	opener.document.getElementById("blog_catnm").value=catnm;
	window.close();
}
</script>

<table width="390" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="129"><a href="popup.blog.category.php"><img src="../img/tep01_on.gif" width="129" height="26" border="0" /></a></td>
		<td width="131"><a href="popup.blog.category.admin.php"><img src="../img/tep02.gif" width="131" height="26" border="0" /></a></td>
		<td width="130" background="../img/tep_bg.gif"></td>
	</tr>
</table>
</p>
<table width="390" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="129" valign="top" style="padding-left:3;" class="text01">
		<? if(count($category)==0): ?>
			카테고리가 없습니다
		<? endif; ?>

		<? foreach((array)$category as $v): ?>


		<? if($v['depth']):?>
			<p><img src="../img/icon_2depth.gif" /><a href="javascript:select_category('<?=$v['cate_no']?>','<?=$v['catnm']?>')"><?=$v['catnm']?></a></p>

		<? else: ?>
			<p><img src="../img/dot_blog.gif" /><a href="javascript:select_category('<?=$v['cate_no']?>','<?=$v['catnm']?>')"><?=$v['catnm']?></a></p>
		<?endif;?>

		<? endforeach; ?>


	</tr>
</table>





