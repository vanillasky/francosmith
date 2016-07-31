<? ### 대한통운-미국상사  http://ex.korex.co.kr:7004/fis20/KIL_HttpCallExpTrackingInbound_Ctr.do?rqs_HAWB_NO=
$out = iconv("UTF-8","EUC-KR",$out);
$out = split_betweenStr($out,"<body>","</body>");
$out[0] = preg_replace('/(<a href="javascript:f_PopUrl\(\);">)/is', '', $out[0]);
$out[0] = preg_replace('/(<a href="javascript:f_PopUrl1\(\);">.+?)+(<\/a>)/is', '', $out[0]);
$out[0] = str_replace('src="/fis20/','src="http://ex.korex.co.kr:7004/fis20/',$out[0]);
?>
<link rel="stylesheet" type="text/css" href="http://ex.korex.co.kr:7004/fis20/fis/httpResource/css/international.css" media="all" />
<?=$out[0]?>