<? ### ½Å¼¼°è http://ptop.sedex.co.kr:8080/jsp/tr/detailSheet.jsp?iSheetNo=

$out = split_betweenStr($out,"<body style=margin:10 width=>","</body>");
//$out[0] = str_replace('width="515"','width=100%',$out[0]);
//$out[0] = str_replace('width="491"','width=100%',$out[0]);
?>
<base href="http://ptop.sedex.co.kr:8080/jsp/tr/">
<?=strip_tags($out[0], '<table>,<tr>,<td>,<p>,<font>,<b>');?>
</td></tr></table>
<base href="<?=$_SERVER[SERVER_NAME].dirname($_SERVER[PHP_SELF])?>/">