<?
 # 이노지스택배 http://www.innogis.net/Tracking/Tracking_view.asp?invoice=

 $out = split_betweenStr($out,'<html>','</html>');
 $out[0] = str_replace('<A HREF="http://service.epost.go.kr/trace.RetrieveRegiPrclDeliv.postal?sid1=6072203166959"><IMG SRC="images/상세보기.gif" WIDTH="68" HEIGHT="22" BORDER="0"></A>','',$out[0]);
 $out[0] = str_replace('<a href="http://www.innogis.co.kr"><span style="font-size:9pt;">www.innogis.co.kr</span></a>','www.innogis.co.kr',$out[0]);

?>
<html>
<?=$out[0]?>
</html>