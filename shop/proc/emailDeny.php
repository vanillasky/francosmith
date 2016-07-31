<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<title>이메일 수신거부</title>
<script type="text/javascript">
window.onload = function() {
	if (confirm('수신거부로 설정하시면 e-mail 전용 할인쿠폰과 특가상품, 이벤트 정보를 받아보실 수 없습니다. 수신거부 하시겠습니까?')) {
		document.sForm.submit();
	}
	else {
		window.close();
	}
}
</script>
</head>
<body>
<form name="sForm" method="post" action="./emailDeny.indb.php" target=ifrmHidden>
<input type="hidden" name="k" value="<?=$_GET['k']?>"/>
<input type="hidden" name="id" value="<?=$_GET['id']?>"/>
</form>
<iframe name="ifrmHidden" src='../blank.php' style="display:none;width:100%;height:600"></iframe>
</body>
</html>