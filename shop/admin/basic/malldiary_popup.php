<?include "../lib.php";

	$diary_popQuery = "
		select
			*
		from
			gd_diaryContent
		where
			diary_date = '$_GET[thisDay]'
	";
	$row = $db->fetch($diary_popQuery);

	$datey=substr($_GET['thisDay'],0,4);
	$datem=substr($_GET['thisDay'],4,2);
	$dated=substr($_GET['thisDay'],6,2);
?>
<html>
<head>
<title>오늘의 일정관리 알람서비스</title>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<script language="javascript" src="../prototype.js"></script>
<script language="javascript" src="../prototype_ext.js"></script>

<div style="padding-top:4"></div>
<table width="97%" border="0" cellpadding="0" cellspacing="0" align=center>
<form name="_malldiarypopupFm">
	<tr>
		<td style="font-family:돋움;font-size:8pt;color:#FFFFFF" align="center" colspan="2" height='27' bgcolor='656565'><b><?=date('Y');?>년 <?=date('m');?>월 <?=date('d');?>일 알람서비스</b></td>
	</tr>
	<tr>
		<td style="font-family:굴림;font-size:9pt;padding-left:8px;color:505050" width='50' height='27' bgcolor='F3F3F3'><b>제목</b></td>
		<td style="font-family:굴림;font-size:9pt;padding-left:8px;color:444444"><?=$row['diary_title']?></td>
	</tr>
	<tr><td colspan="2" height='1' bgcolor='DBDBDB'></td></tr>
	<tr>
		<td valign=top style="font-family:굴림;font-size:9pt;padding-left:8;padding-top:8;color:505050" width='50' height='25' bgcolor='F3F3F3'><b>내용</b></td>
		<td style="font-family:굴림;font-size:9pt;padding-left:8px;padding-top:8px;color:444444;" valign='top' height='150'><?=$row['diary_content']?></td>
	</tr>
	<tr><td colspan="2" height='1' bgcolor='DBDBDB'></td></tr>
	<tr><td class="noline" colspan="2" height='30' align="right" style="font-family:돋움;font-size:8pt;padding-right:0px;color:444444;letter-spacing:-1"><input type='checkbox' name="not_diarypop" value="y" onclick='closeWin();'>오늘 서비스창 닫기 &nbsp;&nbsp;<a href='javascript:self.close();'><img src="../img/btn_daily_close.gif" border=0 align=absmiddle></a></a></td></tr>
	<tr><td height=4 colspan=2></td></tr>
</form>
</table>
</body>
</html>

<script>
function closeWin(){
	if( document._malldiarypopupFm.not_diarypop.checked == true ) alram_Request();
	//self.close();
}


function alram_Request(){
	var ajax = new Ajax.Request(
			"./malldiary_proc.php?mode=setCookie",
			{
			method : 'get',
			onComplete : alram_setResponse
			}
		);
}

function alram_setResponse(req){
	var req_value = req.responseText;
	//alert(req_value);
	self.close();
}
</script>