<?
	### (동원) 로엑스택배(구-아주택배)  http://www.loexe.co.kr/customer/cus_trace_02_apis.asp?searchMethod=I&invc_no=

	$out = split_betweenStr($out,'<!--실제 내용 테이블-->','<!--실제 내용 테이블-->');
?>

<style>
.yellow_03 {
padding-left:10px;
color:#696B00;
}
.padding_01 {padding-left:10px}
</style>

<base href="http://www.loexe.co.kr/" target="_blank">
<?=$out[0]?>
<script>
var img = document.getElementsByTagName('img');
for (i=0;i<img.length;i++) img[i].style.display = "none";
var tb = document.getElementsByTagName('table');
for (i=0;i<tb.length;i++) tb[i].style.width = "100%";
</script>
<base href="<?=$_SERVER[SERVER_NAME].dirname($_SERVER[PHP_SELF])?>/" target="_self">