<?

include "../lib.php";

list( $disk_errno, $disk_msg ) = disk();

if ( $disk_errno == '001' ) $disk_img = "http://www.godo.co.kr/userinterface/img/disk_guide_add.gif";
else if ( $disk_errno == '002' ) $disk_img = "http://www.godo.co.kr/userinterface/img/disk_guide_date.gif";

?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<title>용량서비스</title>
<script src="../common.js"></script>
<link rel="styleSheet" href="../style.css">
<script language="javascript"><!--
function setCookie()
{
	var name	= 'blnCookie_disk';
	var value	= 'true';
	var path	= '/';
    var today	= new Date()
    var expires	= new Date(today.getTime() + 60*60*6*1000)

	var curCookie = name + "=" + escape( value ) +
		( ( expires ) ? "; expires=" + expires.toGMTString() : "" ) +
		( ( path ) ? "; path=" + path : "" );

	document.cookie = curCookie;
	setTimeout( "self.close()" );
}

function disk_apply(){
	opener.location.href="../basic/disk.pay.php";
	self.close();
}
--></script>
</head>
<body topmargin=0 leftmargin=0>


<div style="margin:10 0; text-align:center;"><a href="javascript:disk_apply();"><img src="<?=$disk_img?>" border=0></a></div>

<div style="margin:10 0; text-align:center;"><font class=small1 color="444444">다음부터 이창을 열지않음</font><input type=checkbox onClick="setCookie()"></div>


</body>
</html>