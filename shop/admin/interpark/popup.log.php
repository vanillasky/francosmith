<?
include "../_header.popup.php";
?>

<div class="title title_top">인터파크 처리로그</div>

<table class=tb>
<col class=cellC><col class=cellL>
<?
$query = "select * from ".INPK_CLAIM_ITEM_LOG." where itmsno='{$_GET[itmsno]}'";
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