<?

include "../_header.php";

if (!$sess && !$_COOKIE[guest_ordno]) go("../member/login.php?returnUrl=$_SERVER[PHP_SELF]");
?>
<html>
<head>
<title>세금계산서 인쇄</title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
</head>
<div id=dynamic></div>
<body oncontextmenu="return false">
<style type="text/css"><!--
@media print { .notprint {display: none;} } /* 인쇄시 불필요한 부분 비활성화 */
.small {
	font:8pt Dotum;
	letter-spacing:0px;
	padding-top:3px;
}
--></style>

<script language="javascript"><!--
window.onbeforeprint = function () // 세금계산서 출력시 실행
{
	var ele = eval("document.getElementsByName('taxtable')");
	for ( i=0; i<ele.length; i++ )
	{
		var jscript = document.createElement("script");
		jscript.src="../mypage/indb.php?mode=taxprint&sno=" + ele[i].taxsno;
		document.getElementById('dynamic').appendChild(jscript);
	}
}
--></script>

<DIV class="notprint" style="margin:0 40 20 40;">
<a href="javascript:window.print();"><img src="../admin/img/btn_print.gif" border="0" align="absmiddle"></a><br>
<font color=#5B5B5B>※ <span class=small>세금계산서 인쇄시 직인도 인쇄되려면 다음과 같이 설정되어 있어야 가능합니다.</span></font>
<dl class=small style="color:#5B5B5B; margin:0 0 0 20px;">
<dt>1) 인터넷 익스플로러 사용 시
<dd>: 브라우저 상단의 도구 메뉴 클릭 후, [인터넷옵션]-[고급]-[인쇄] 에서 [배경색 및 이미지 인쇄] 체크
<dt>2) 파이어폭스 사용 시
<dd>: 브라우저 상단의 파일 메뉴 클릭 후, [인쇄화면설정]-[용지 및 설정]-[옵션]에서 [배경 인쇄(색상 및 그림)] 체크
</dl>
</div>
<?

$_GET[taxarea] = 'blue';
$ordno = $_GET[ordno];
include "../admin/order/_paper.tax.php";

?>
</body>
</html>