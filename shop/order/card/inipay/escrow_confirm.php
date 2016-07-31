<?php
/**
 * 이니시스 PG 에스크로 구매 확인 페이지
 * 원본 파일명 INIescrow_confirm.html
 * 이니시스 PG 버전 : INIpay V5.0 - 오픈웹 (V 0.1.1 - 20120302)
 */

include "../../../lib/library.php";
include "../../../conf/config.php";
include "../../../conf/pg.$cfg[settlePg].php";
include "../../../conf/pg.escrow.php";

$ordno = $_GET['ordno'];

$query = "
SELECT
	escrowno
FROM
	".GD_ORDER."
WHERE
	ordno = '$ordno'
";
$data = $db->fetch($query);
?>
<html>
<head>
<title>이니시스 자체 에스크로(INIescrow 1.0) 구매확인</title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<meta http-equiv="Cache-Control" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
<meta http-equiv="Pragma" content="no-cache"/>

<link rel="stylesheet" href="css/group.css" type="text/css">
<style>
body, tr, td {font-size:10pt; font-family:굴림,verdana; color:#433F37; line-height:19px;}
table, img {border:none}

/* Padding ******/
.pl_01 {padding:1 10 0 10; line-height:19px;}
.pl_03 {font-size:20pt; font-family:굴림,verdana; color:#FFFFFF; line-height:29px;}

/* Link ******/
.a:link  {font-size:9pt; color:#333333; text-decoration:none}
.a:visited { font-size:9pt; color:#333333; text-decoration:none}
.a:hover  {font-size:9pt; color:#0174CD; text-decoration:underline}

.txt_03a:link  {font-size: 8pt;line-height:18px;color:#333333; text-decoration:none}
.txt_03a:visited {font-size: 8pt;line-height:18px;color:#333333; text-decoration:none}
.txt_03a:hover  {font-size: 8pt;line-height:18px;color:#EC5900; text-decoration:underline}
</style>

<script language=javascript src="http://plugin.inicis.com/pay60_escrow.js"></script>
<script language="Javascript">
// 플러그인 설치(확인)
StartSmartUpdate();

function f_check(){
	if(document.ini.tid.value == ""){
		alert("거래번호가 빠졌습니다.")
		return;
	}
	if(document.ini.mid.value == ""){
		alert("상점아이디(mid)가 빠졌습니다.")
		return;
	}
}

var openwin;

function pay(frm)
{
  // 필드 체크
  f_check();

	// MakePayMessage()를 호출함으로써 플러그인이 화면에 나타나며, Hidden Field
	// 에 값들이 채워지게 됩니다. 플러그인은 통신을 하는 것이 아니라, Hidden
	// Field의 값들을 채우고 종료한다는 사실에 유의하십시오.

	if(document.ini.clickcontrol.value == "enable")
	{
		if(document.INIpay==null||document.INIpay.object==null)
		{
			alert("플러그인을 설치 후 다시 시도 하십시오.");
			return false;
		}
		else
		{

			if (MakePayMessage(frm))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}
	else
	{
		return false;
	}
}

function enable_click()
{
	document.ini.clickcontrol.value = "enable"
}

function disable_click()
{
	document.ini.clickcontrol.value = "disable"
}

function focus_control()
{
	if(document.ini.clickcontrol.value == "disable")
		openwin.focus();
}
</script>

</head>

<!-----------------------------------------------------------------------------------------------------
※ 주의 ※
 아래의 body TAG의 내용중에
 onload="javascript:enable_click();" onFocus="javascript:focus_control()" 이 부분은 수정없이 그대로 사용.
 아래의 form TAG내용도 수정없이 그대로 사용.
------------------------------------------------------------------------------------------------------->
<body bgcolor="#FFFFFF" text="#242424" leftmargin=0 topmargin=15 marginwidth=0 marginheight=0 bottommargin=0 rightmargin=0 onload="javascript:enable_click();" onFocus="javascript:focus_control()"><center>
<!-- 구매확인을 위한 폼 : 이름 변경 불가 -->
<!-- pay()가 "true"를 반환하면 post된다 -->
<form name="ini" method="post" action="INIescrow_confirm.php" onSubmit="return pay(this)">
<table width="632" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="85" background="img/card.gif" style="padding:0 0 0 64">
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="3%" valign="top"><img src="img/title_01.gif" width="8" height="27" vspace="5"></td>
          <td width="97%" height="40" class="pl_03"><font color="#FFFFFF"><b>INIESCROW 구매확인</b></font></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td align="center" bgcolor="6095BC"><table width="620" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td bgcolor="#FFFFFF" style="padding:8 0 0 56">
            <br>
            <table width="510" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="7"><img src="img/life.gif" width="7" height="30"></td>
                <td background="img/center.gif"><img src="img/icon03.gif" width="12" height="10">
                  <b>구매확인 or 구매거절을 하기 위해서 아래 확인 버튼을 눌러주세요.</b></td>
                <td width="8"><img src="img/right.gif" width="8" height="30"></td>
              </tr>
            </table>
            <br>
            <table width="510" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="510" colspan="2"  style="padding:0 0 0 23">
                  <table width="470" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td height="1" colspan="3" align="center"  background="img/line.gif"></td>
                    </tr>
                    <tr valign="bottom">
                      <td height="40" colspan="3" align="center">
                        <input type="submit" value="확 인">
                      </td>
                    </tr>
                  </table></td>
              </tr>
            </table>
            <br>
          </td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td><img src="img/bottom01.gif" width="632" height="13"></td>
  </tr>
</table>
</center>

<input type="hidden" name="ordno"			value="<?php echo $ordno;?>" />					<!-- 주문 번호 - PG 처리와는 전혀 상관이 없는 옵션임 -->
<input type="hidden" name="mid"				value="<?php echo $escrow['id'];?>" />			<!-- 상점아이디 -->
<input type="hidden" name="tid"				value="<?php echo $data['escrowno'];?>" />		<!-- 취소할 거래의 거래아이디 -->

<!-- 플러그인에서 설정 -->
<input type="hidden" name="paymethod"		value="" />
<input type="hidden" name="encrypted"		value="" />
<input type="hidden" name="sessionkey"		value="" />
<input type="hidden" name="version"			value="5000" />
<input type="hidden" name="clickcontrol"	value="" />

<!-- 플러그인 필요값 -->
<input type="hidden" name="acceptmethod"	value=" " />

</form>
</body>
</html>
