<?

$location = "���ڼ��ݰ�꼭 ���� > ���ڼ��ݰ�꼭 �����ϱ�";
include "../_header.php";

if ( isset($_POST[agree]) === false && isset($_POST[agreeDacom]) === false ) // �̿��� ���Ǹ� ���Ѵ�.
{
	include dirname(__FILE__) . '/etax.requestAgree.php';
	exit;
}

include_once "../../lib/json.class.php";
$json = new Services_JSON();
$param = array();
$param['compName'] = $cfg[compName];
$param['ceoName'] = $cfg[ceoName];
$param['compSerial'] = $cfg[compSerial];
$param['service'] = $cfg[service];
$param['item'] = $cfg[item];
$param['email'] = $cfg[adminEmail];
$param['phone'] = array($tmp[(count($tmp = explode("-",$cfg[compPhone])) - 3)], $tmp[(count($tmp) - 2)], $tmp[(count($tmp) - 1)]);
$param['address'] = $cfg[address];
$param['return_url']	= "http://{$_SERVER[HTTP_HOST]}" . str_replace(basename($_SERVER[PHP_SELF]), "tax_indb.php?mode=request", $_SERVER[PHP_SELF]); # ��� ���� URL
$param = $json->encode($param);

?>
<script src="../tax.ajax.js"></script>

<form name="form" onsubmit="return WRS.request();">
<input type="hidden" name="godosno" value="<?=$godo[sno]?>">
<input type="hidden" name="userid" value="<?=sprintf("GODO%05d", $godo[sno])?>">

<div class="title title_top">���ڼ��ݰ�꼭 �����ϱ� <span>LG������ ���ڼ��ݰ�꼭 ���ý�21�� �����մϴ�.</span></div>
<table class="tb">
<col class="cellC"><col class="cellL"><col class="cellC"><col class="cellL">
<tr>
	<td>��Ź�� ���̵�</td>
	<td><?=sprintf("CGO_GODO%05d", $godo[sno])?> <span class=small style="margin-left:43px"><font color=#5B5B5B>(WebTax21 �α�������)</font></span></td>
	<td>��й�ȣ</td>
	<td><input type="password" name="password" required label="��й�ȣ"> <span class=small><font color=#5B5B5B>(WebTax21 �α�������)</font></span></td>
</tr>
<tr>
	<td>����ڹ�ȣ</td>
	<td colspan=3><input type="text" name="compSerial" required label="����ڹ�ȣ"> <span class=small>ex) 123-45-67890</span></td>
</tr>
<tr>
	<td>��ȣ��</td>
	<td><input type="text" name="compName" required label="��ȣ��"></td>
	<td>��ǥ�ڸ�</td>
	<td><input type="text" name="ceoName" required label="��ǥ�ڸ�"></td>
</tr>
<tr>
	<td>����</td>
	<td><input type="text" name="service" required label="����"></td>
	<td>����</td>
	<td><input type="text" name="item" required label="����"></td>
</tr>
<tr>
	<td>�̸���</td>
	<td colspan=3><input type="text" name="email" class="lline" required label="�̸���"></td>
</tr>
<tr>
	<td>��ȭ</td>
	<td><input type="text" name="phone[]" maxlength="4" style="width:40px;" required label="��ȭ" onkeydown="onlynumber()">��<input type="text" name="phone[]" maxlength="4" style="width:40px;" required label="��ȭ" onkeydown="onlynumber()">��<input type="text" name="phone[]" maxlength="4" style="width:40px;" required label="��ȭ" onkeydown="onlynumber()"></td>
	<td>�ڵ���</td>
	<td><input type="text" name="mobile[]" maxlength="4" style="width:40px;" required label="�ڵ���" onkeydown="onlynumber()">��<input type="text" name="mobile[]" maxlength="4" style="width:40px;" required label="�ڵ���" onkeydown="onlynumber()">��<input type="text" name="mobile[]" maxlength="4" style="width:40px;" required label="�ڵ���" onkeydown="onlynumber()"></td>
</tr>
<tr>
	<td>�ּ�</td>
	<td colspan=3><input type="text" name="address" style="width:60%" value="" required label="�ּ�"></td>
</tr>
</table>

<div class="button" id="avoidSubmit">
<input type="image" src="../img/btn_confirm.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>

</form>


<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_tip">
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�� ����� �׸��� �������� ������ �ּ���.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><b>��й�ȣ</b>�� LG������webtax21 Ȩ�������� �α����� �� ���˴ϴ�. ���Ͻô� ��й�ȣ�� ������ �ּ���.</td></tr>
</table>
</div>
<script>cssRound('MSG01','#F7F7F7')</script>


<script language="javascript"><!--
var param = eval( '(<?=$param?>)' );
WRS.init_set();
--></script>


<? include "../_footer.php"; ?>