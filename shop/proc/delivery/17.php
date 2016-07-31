<? ### 사가와 익스프레스 http://www.sagawa-korea.co.kr/sub4/default2_2.asp?awbino=
$out = split_betweenStr($out,"<!-- 내용 -->","<!-- 내용 끝 -->");
$out[0] = str_replace('"image','"http://re.sc-logis.co.kr/image',$out[0]);
$out[0] = str_replace('style="padding-right:10px" align="right"','style="padding-right:10px" align="left"',$out[0]);
?>
<link rel="stylesheet" href="http://www.sagawa-korea.co.kr/style/style.css" type="text/css">
<base href="http://www.sagawa-korea.co.kr/sub4/">

<?=$out[0]?>