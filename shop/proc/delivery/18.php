<?
#No 18 우체국 등기 https://service.epost.go.kr/trace.RetrieveDomRigiTraceList.comm?displayHeader=N&sid1=
$url .= $deliverycode;
?>
<script language="javascript">
window.onload = function () {
	location.replace("<?=$url?>");
}
</script>
<br><b>Now Loading...</b><br>