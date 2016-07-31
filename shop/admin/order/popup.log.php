<?
include "../_header.popup.php";
?>

<div class="title title_top">주문처리로그</div>

<table class=tb>
<col class=cellC><col class=cellL>
<?
$query = "select * from ".GD_ORDER_LOG." where ordno='{$_GET[ordno]}'";
$res = $db->query($query);
while ($data=$db->fetch($res)){
?>
<tr>
	<td><?=substr($data[regdt],5,-3)?></td>
	<td><?=$data[memo]?></td>
</tr>
<? } ?>
</table>

<script>table_design_load();</script>