<?
# 현대택배 http://www.hydex.net/ehydex/jsp/home/distribution/tracking/tracingView.jsp?InvNo=

$out = split_betweenStr($out,"<html>","</html>");
$out[0] = str_replace('/ehydex/','http://www.hydex.net/ehydex/',$out[0]);
?>

<table cellpadding="0" cellspacing="0" width="697" border="0">
<tr>
	<td><?=$out[0]?></td>
</tr>
</table>