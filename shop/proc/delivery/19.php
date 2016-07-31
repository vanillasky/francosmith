<?
 ### 천일택배 http://www.cyber1001.co.kr/kor/taekbae/HTrace.jsp?transNo=
 $out = split_betweenStr($out,"<td style=\"padding-top:16px\"><img src=\"img/HTrace_title_1.gif\"></td>","<td class=\"txtTitle\">▶ 문의전화 </td>");
 $out[0] = str_replace("<input type=\"submit\" name=\"Submit\" value=\"조   회\" />","",$out[0]);
 $out[0] = str_replace("(운송장번호를 입력하세요)","",$out[0]);
?>
<table><tr>
<?=$out[0]?>
<td></td></tr></table>