<html>
<head>
<title>올더게이트</title>
<style type="text/css">
<!--
body { font-family:"돋움"; font-size:9pt; color:#000000; font-weight:normal; letter-spacing:0pt; line-height:180%; }
td { font-family:"돋움"; font-size:9pt; color:#000000; font-weight:normal; letter-spacing:0pt; line-height:180%; }
.clsleft { padding:0 10px; text-align:left; }
-->
</style>
<script language=javascript>
<!--
function Request(form)
{
	////////////////////////////////////////////
	//  입력된 데이타의 유효성을 검사합니다.  //
	////////////////////////////////////////////
	if(form.id_no.value == "")
	{
		alert("주민등록번호를 입력하십시오.");
		return;
	}
	form.submit();
}
-->
</script>
</head>
<body topmargin="0" leftmargin="0" rightmargin="0" bottommargin="0">
<form name="frmAGS_escrow" method="post" action="./escrow_confirm_return.php">
<input type="hidden" name="ordno" value="<?php echo $_GET['ordno'];?>">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
	<td>&nbsp;</td>
</tr>
<tr>
	<td><hr></td>
</tr>
<tr>
	<td align="center"><b>올더게이트 에스크로 거래 구매확인 요청</b></td>
</tr>
<tr>
	<td>&nbsp;</td>
</tr>
<tr>
	<td align="center">
	<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="clsleft">☞ 주민등록번호 (13)</td>
		<td><input type="text" style="width:150px" name="id_no" maxlength="13" value=""></td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td><hr></td>
</tr>
<tr>
	<td align="center">
	<input type="button" value="요청" onclick="javascript:Request(frmAGS_escrow);">
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
</tr>
</table>
</form>
</body>
</html>