<? ### »ï¼ºÅÃ¹èHTH(±¸)(600009727933 ) http://nexs.cjgls.com/web/detailform.jsp?slipno=
$tmp = explode("<html>",$out);
$out = $tmp[1];
$out=iconv("UTF-8", "euc-kr",$out);

$out = split_betweenStr($out,"<html>","</html>");
$out[0] = str_replace('img/','http://nexs.cjgls.com/web/img/',$out[0]);
?>

<base href="http://www.cjgls.co.kr/kor/service/">
<?=$out[0]?>
<base href="<?=$_SERVER[SERVER_NAME].dirname($_SERVER[PHP_SELF])?>/">