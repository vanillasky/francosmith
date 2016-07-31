<?
### WIZWA     http://www.wizwa.com/traking_ing.php?invoice_no=
$out = split_betweenStr($out,"<table width=\"80%\"  border=\"0\" align=\"center\" cellpadding=\"1\" cellspacing=\"1\" bordercolor=\"C1E2EB\" bgcolor=\"C1E2EB\">","<td height=50><table width=\"50%\"");

$out[0] = str_replace('images/','http://www.wizwa.com/images/',$out[0]);
$out[0] = str_replace('<a href="tracking.php" onfocus=this.blur();>','',$out[0]);
$out[0] = str_replace('<img src="http://www.wizwa.com/images/sub_menu/left4_1.gif" width="170" height="30" border="0"></a>','',$out[0]);
?>
<br>
<table width="100%">
<tr>
<td>
<table width="100%"  border="0" align="center" cellpadding="1" cellspacing="1" bordercolor="C1E2EB" bgcolor="C1E2EB">
<?=$out[0]?>
<td>&nbsp;</td>
</tr>
</table>