<? ### 트라넷 (http://www.etranet.co.kr/branch/chase/listbody.html?a_gb=center&a_cd=4&a_item=0&fr_slipno=)
$out = split_betweenStr($out,'<!--운송장번호--------------------->','<!---footer----------------------------------------->');
?>
<base href="http://www.etranet.co.kr/branch/chase/">
<div id="godo_contents">
<p><table border=1 bordercolor=#cccccc style="border-collapse:collapse" width="100%">
<col style="background:#f7f7f7"><col><col style="padding-left:10px">
<?=$out[0]?>
</table>
</div>
<base href="<?=$_SERVER[SERVER_NAME].dirname($_SERVER[PHP_SELF])?>/" target="_self">

<script>
var img = document.getElementById('godo_contents').getElementsByTagName('img');
img[img.length-1].style.display = "none";
</script>