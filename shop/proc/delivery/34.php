<?
	# 나이트맨	http://knightman.cafe24.com/trace.php?order_number=

	$out=iconv("UTF-8", "euc-kr",$out);

	$out = split_betweenStr($out,"<html>","</html>");
	$out[0] = str_replace('img/','http://knightman.cafe24.com/img/',$out[0]);
?>

<table cellpadding="0" cellspacing="0" width="600" border="0">
<tr>
	<td><?=$out[0]?></td>
</tr>
</table>