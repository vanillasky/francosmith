<? ### 우체국택배(https://service.epost.go.kr/trace.RetrieveDomRigiTraceList.comm?displayHeader=N&sid1=)
$url .= $deliverycode;
?>
<script language="javascript">
window.onload = function () {
	location.replace("<?=$url?>");
}
</script>
<br><b>Now Loading...</b><br>