<?php
/**
 * �̴Ͻý� PG ����ũ�� ���� Ȯ�� ������
 * ���� ���ϸ� INIescrow_confirm.html
 * �̴Ͻý� PG ���� : INIpay V5.0 - ������ (V 0.1.1 - 20120302)
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
<title>�̴Ͻý� ��ü ����ũ��(INIescrow 1.0) ����Ȯ��</title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<meta http-equiv="Cache-Control" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
<meta http-equiv="Pragma" content="no-cache"/>

<link rel="stylesheet" href="css/group.css" type="text/css">
<style>
body, tr, td {font-size:10pt; font-family:����,verdana; color:#433F37; line-height:19px;}
table, img {border:none}

/* Padding ******/
.pl_01 {padding:1 10 0 10; line-height:19px;}
.pl_03 {font-size:20pt; font-family:����,verdana; color:#FFFFFF; line-height:29px;}

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
// �÷����� ��ġ(Ȯ��)
StartSmartUpdate();

function f_check(){
	if(document.ini.tid.value == ""){
		alert("�ŷ���ȣ�� �������ϴ�.")
		return;
	}
	if(document.ini.mid.value == ""){
		alert("�������̵�(mid)�� �������ϴ�.")
		return;
	}
}

var openwin;

function pay(frm)
{
  // �ʵ� üũ
  f_check();

	// MakePayMessage()�� ȣ�������ν� �÷������� ȭ�鿡 ��Ÿ����, Hidden Field
	// �� ������ ä������ �˴ϴ�. �÷������� ����� �ϴ� ���� �ƴ϶�, Hidden
	// Field�� ������ ä��� �����Ѵٴ� ��ǿ� �����Ͻʽÿ�.

	if(document.ini.clickcontrol.value == "enable")
	{
		if(document.INIpay==null||document.INIpay.object==null)
		{
			alert("�÷������� ��ġ �� �ٽ� �õ� �Ͻʽÿ�.");
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
�� ���� ��
 �Ʒ��� body TAG�� �����߿�
 onload="javascript:enable_click();" onFocus="javascript:focus_control()" �� �κ��� �������� �״�� ���.
 �Ʒ��� form TAG���뵵 �������� �״�� ���.
------------------------------------------------------------------------------------------------------->
<body bgcolor="#FFFFFF" text="#242424" leftmargin=0 topmargin=15 marginwidth=0 marginheight=0 bottommargin=0 rightmargin=0 onload="javascript:enable_click();" onFocus="javascript:focus_control()"><center>
<!-- ����Ȯ���� ���� �� : �̸� ���� �Ұ� -->
<!-- pay()�� "true"�� ��ȯ�ϸ� post�ȴ� -->
<form name="ini" method="post" action="INIescrow_confirm.php" onSubmit="return pay(this)">
<table width="632" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="85" background="img/card.gif" style="padding:0 0 0 64">
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="3%" valign="top"><img src="img/title_01.gif" width="8" height="27" vspace="5"></td>
          <td width="97%" height="40" class="pl_03"><font color="#FFFFFF"><b>INIESCROW ����Ȯ��</b></font></td>
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
                  <b>����Ȯ�� or ���Ű����� �ϱ� ���ؼ� �Ʒ� Ȯ�� ��ư�� �����ּ���.</b></td>
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
                        <input type="submit" value="Ȯ ��">
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

<input type="hidden" name="ordno"			value="<?php echo $ordno;?>" />					<!-- �ֹ� ��ȣ - PG ó���ʹ� ���� ����� ���� �ɼ��� -->
<input type="hidden" name="mid"				value="<?php echo $escrow['id'];?>" />			<!-- �������̵� -->
<input type="hidden" name="tid"				value="<?php echo $data['escrowno'];?>" />		<!-- ����� �ŷ��� �ŷ����̵� -->

<!-- �÷����ο��� ���� -->
<input type="hidden" name="paymethod"		value="" />
<input type="hidden" name="encrypted"		value="" />
<input type="hidden" name="sessionkey"		value="" />
<input type="hidden" name="version"			value="5000" />
<input type="hidden" name="clickcontrol"	value="" />

<!-- �÷����� �ʿ䰪 -->
<input type="hidden" name="acceptmethod"	value=" " />

</form>
</body>
</html>
