<? ### ÈÑ¹Ì¸®ÅÃ¹è(300102808832)
$out = split_betweenStr($out,'<td><img src="../images2/title/03_st_sub_10.gif" width="80" height="18"></td>','</table>');
$out[0] = str_replace( '</tr>' . "\n" . '<tr>' . "\n" . '    <td class="td1">', "", $out[0] );
?>
<link rel="styleSheet" href="http://www.e-family.co.kr/css/familynet.css">
<p>
<?=$out[0]?>
</table>