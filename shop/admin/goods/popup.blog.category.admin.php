<?php
include "../_header.popup.php";
include_once("../../lib/blogshop.class.php");

$blogshop = new blogshop();

?>
<style>
a:link { color: #83807d; text-decoration: none }
a:visited { color: #83807d; text-decoration: none }
a:hover { color: #83807d; text-decoration: underline }
a:active { color: #ff6600; text-decoration: none }

.text01 p{ font-size: 11px; color: #847f74; font-family: "µ¸¿ò"; letter-spacing:-1px; margin:5px 0; }
.title01 { font-size: 12px; color: #004682; font-family: "µ¸¿ò"; letter-spacing:-1px; }
</style>
<table width="390" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="129"><a href="popup.blog.category.php"><img src="../img/tep01.gif" width="129" height="26" border="0" /></a></td>
		<td width="131"><a href="popup.blog.category.admin.php"><img src="../img/tep02_on.gif" width="131" height="26" border="0" /></a></td>
		<td width="130" background="../img/tep_bg.gif"></td>
	</tr>
</table>

<br><br>

<iframe src="http://blogshop.godo.co.kr/<?=$blogshop->config['id']?>/admin/goods/iframe_category?id=<?=$blogshop->config['id']?>&api_key=<?=$blogshop->config['api_key']?>" width="500" height="400" frameborder="0">
</iframe>