<? ### ·ÎÁ¨ÅÃ¹è http://www.ilogen.com/iLOGEN.Web.New/TRACE/TraceView.aspx?gubun=slipno&slipno=

 $out = split_betweenStr($out,'<html xmlns="http://www.w3.org/1999/xhtml" lang="ko">','</html>');
 $out[0] = str_replace("../IMG/","http://www.ilogen.com/iLOGEN.Web.New/IMG/",$out[0]);
 $out[0] = str_replace("../flash/","http://www.ilogen.com/iLOGEN.Web.New/flash/",$out[0]);
 $out[0] = str_replace("../CSS/","http://www.ilogen.com/iLOGEN.Web.New/CSS/",$out[0]);
 $out[0] = str_replace("../Js/","http://www.ilogen.com/iLOGEN.Web.New/Js/",$out[0]);
 $out[0] = str_replace("TraceView.aspx","http://www.ilogen.com/iLOGEN.Web.New/TRACE/TraceView.aspx",$out[0]);
?>
<?=$out[0];?>