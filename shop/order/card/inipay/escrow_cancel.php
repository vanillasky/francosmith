<?php
/**
 * �̴Ͻý� PG ����ũ�� ���� Ȯ�� ������
 * ���� ���ϸ� INIescrow_denyconfirm.html
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
<title>�̴Ͻý� ��ü ����ũ��(INIescrow) ����Ȯ��</title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<meta http-equiv="Cache-Control" content="no-cache" />
<meta http-equiv="Expires" content="0" />
<meta http-equiv="Pragma" content="no-cache" />

<script language="Javascript">
function f_check(){
	if(document.ini.tid.value == ""){
		alert("�ŷ���ȣ�� �������ϴ�.")
		return;
	}
	else if(document.ini.mid.value == ""){
		alert("���� ���̵� �������ϴ�.")
		return;
	}
	else if(document.ini.dcnf_name.value == ""){
		alert("���� Ȯ���� �̸��� �����Ͻʽÿ�.")
		return;
	}
	document.ini.submit();
}
</script>
</head>

<body>
<form name="ini" method="post" action="./INIescrow_denyconfirm.php">
<input type="hidden" name="ordno"			value="<?php echo $ordno;?>" />								<!-- �ֹ� ��ȣ - PG ó���ʹ� ���� ����� ���� �ɼ��� -->
<input type="hidden" name="mid"				value="<?php echo $escrow['id'];?>" />						<!-- * ����ũ�� ���̵� -->
<input type="hidden" name="tid"				value="<?php echo $data['escrowno'];?>" />					<!-- * ��ǰ���� �ŷ���ȣ(TID) -->
<input type="hidden" name="dcnf_name"		value="������" />											<!-- * ���Ű��� Ȯ���� -->
</form>
<script>f_check();</script>
</body>
</html>