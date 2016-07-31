<?php
include "../_header.popup.php";

include_once("../../lib/blogshop.class.php");

$blogshop = new blogshop();
$result = $blogshop->get_part();



?>
<style>
a:link { color: #83807d; text-decoration: none }
a:visited { color: #83807d; text-decoration: none }
a:hover { color: #83807d; text-decoration: underline }
a:active { color: #ff6600; text-decoration: none }

.text01 p{ font-size: 11px; color: #847f74; font-family: "µ¸¿ò"; letter-spacing:-1px; margin:5px 0; }
.title01 { font-size: 12px; color: #004682; font-family: "µ¸¿ò"; letter-spacing:-1px; }

</style>
<script type="text/javascript">
function select_part(part_no,title) {
	opener.document.getElementById("blog_part_no").value=part_no;
	opener.document.getElementById("blog_part_title").value=title;
	window.close();
}
</script>


<table width="390" height="26" border="0" cellpadding="0" cellspacing="1" bgcolor="d1d1d1">
	<tr>
	<? foreach($result as $v): ?>
		<? if($v['depth']==0): ?>

			<td width="130" align="center" bgcolor="f6f6f6" style="padding-top:3;" class="title01"><?=$v['title']?></td>
		<? endif;?>
	<? endforeach; ?>
	</tr>
</table>
<br/>
<table width="390" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<? foreach($result as $k=>$v): ?>
			<? if($v['depth']==0): ?>

				<? if($k!=0): ?>

					<td width="1" background="../img/dot_line.gif"></td>
				<? endif; ?>
				<td width="129" valign="top" style="padding-left:3;" class="text01">
			<? else: ?>
				<p><img src="../img/dot_blog.gif" /><a href="javascript:select_part('<?=$v['part_no']?>','<?=$v['title']?>')"><?=$v['title']?></a></p>
			<? endif;?>

		<? endforeach; ?>

	</tr>
</table>



