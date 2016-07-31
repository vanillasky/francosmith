<?

include "../_header.popup.php";

$data = $db->fetch("select * from ".GD_LOG_EMAIL." where sno='$_GET[sno]'");

?>

<table class=tb>
<tr>
	<td height=30 bgcolor=#f7f7f7 style="padding-left:10px">일시 : <b><?=$data[regdt]?></b></td>
</tr>
<tr>
	<td height=30 bgcolor=#f7f7f7 style="padding-left:10px">제목 : <b><?=$data[subject]?></b></td>
</tr>
<tr>
	<td colspan=2>
	<?=$data[body]?>
	</td>
</tr>
</table>

<script>table_design_load();</script>