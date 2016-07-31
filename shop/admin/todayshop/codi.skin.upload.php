<?
include "../_header.popup.php";
?>
<div class="title title_top">스킨 업로드</div>
<form method="post" action="indb.skin.php" enctype="multipart/form-data">
<input type="hidden" name="mode" value="skinUpload">
<table border=1 bordercolor=#cccccc style="border-collapse:collapse">
<col class="cellC"><col class="cellL">
<tr>
	<td>스킨명</td>
	<td><input type="text" name="upload_skin_name" class="line" value="" maxlength="20" style="width:130px;ime-mode:disabled;"></td>
</tr>
<tr>
	<td>업로드</td>
	<td><input type="file" name="upload_skin" class="line" style="width:230px;"></td>
</tr>
</table>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td height="10"></td></tr>
<tr>
	<td align="center" class="noline"><input type="image" src="../img/btn_register.gif"></td>
</tr>
<tr><td height="10"></td></tr>
</table>
<div style="padding:6px 0px 0px 25px"><font class="extext">※ 해당 페이지는 디자인 리셀러 전용 페이지 입니다.</font></div>
<div style="padding:6px 0px 0px 25px"><font class="extext">※ 반드시 다운 받은 tar.gz 압축 화일만 가능합니다.</font></div>
<div style="padding:6px 0px 0px 25px"><font class="extext">※ 다른 파일을 올려 생기는 문제에 대해서는 책임을 지지 않습니다.</font></div>
<div style="padding:6px 0px 0px 25px"><font class="extext">※ 호스팅 업체에 따라서 업로드가 되지 않을 수 있습니다.</font></div>
<div style="padding:6px 0px 0px 25px"><font class="extext">※ 사용용량이 압축된 화일의 2배 이상이 남아 있어야 업로드가 가능합니다.</font></div>
<div style="padding:6px 0px 0px 25px"><font class="extext">※ 스킨명은 영문만 가능합니다.</font></div>
</form>
</body>
</html>