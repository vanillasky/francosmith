<?
	# 대신택배	http://home.daesinlogistics.co.kr/daesin/jsp/d_freight_chase/d_general_process2.jsp
	$out = split_betweenStr($out,"<HTML>","</HTML>");
?>

<table cellpadding="0" cellspacing="0" width="600" border="0">
<tr>
	<td><?=$out[0]?></td>
</tr>
</table>