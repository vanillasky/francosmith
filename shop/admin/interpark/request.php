<?

$location = "������ũ ���÷��� ���� > ���÷��� ������û / �����Ȳ";
$scriptLoad='<script src="./js/request.js"></script>';
include "../_header.php";

include_once "../../lib/json.class.php";
$json = new Services_JSON();
$param = array();
$param['shopUrl'] = $cfg[shopUrl];
$param['shopName'] = $cfg[compName];
$param['ceoName'] = $cfg[ceoName];
$param['compSerial'] = $cfg[compSerial];
$param['email'] = $cfg[adminEmail];
$param['phone'] = array($tmp[(count($tmp = explode("-",$cfg[compPhone])) - 3)], $tmp[(count($tmp) - 2)], $tmp[(count($tmp) - 1)]);
$param['fax'] = array($tmp[(count($tmp = explode("-",$cfg[compFax])) - 3)], $tmp[(count($tmp) - 2)], $tmp[(count($tmp) - 1)]);
$param = $json->encode($param);

?>

<form name=form onsubmit="return ( IRS.putMerchant() ? false : false );">
<input type=hidden name=godosno value="<?=$godo[sno]?>">
<input type=hidden name=shopName required label="��ȣ(���θ�)��">
<input type=hidden name=ceoName required label="��ǥ�ڸ�">


<div class="title title_top">���÷��� ������û�ϱ� <span>������ũ ���÷��� ���񽺸� ��û�մϴ�.</span></div>
<table class=tb>
<col class=cellC><col class=cellL width=330><col class=cellC><col class=cellL>
<tr>
	<td>�������̵�</td>
	<td colspan=3><?=sprintf("GODO%05d", $godo[sno])?></td>
</tr>
<tr>
	<td>��ȣ(���θ�)��</td>
	<td id=shopName0></td>
	<td>������</td>
	<td id=domain0></td>
</tr>
<tr>
	<td>ȸ���</td>
	<td><input type=text name=compName required label="ȸ���"> <input type=checkbox onclick="IRS.ctrl_field(this.checked)" checked class=null> ��ȣ(���θ�)��� �����մϴ�</td>
	<td>��ȭ</td>
	<td><input type=text name=phone[] style="width:40px;" required label="��ȭ" onkeydown="onlynumber()">��<input type=text name=phone[] style="width:40px;" required label="��ȭ" onkeydown="onlynumber()">��<input type=text name=phone[] style="width:40px;" required label="��ȭ" onkeydown="onlynumber()"></td>
</tr>
<tr>
	<td>��ǥ�ڸ�</td>
	<td id=ceoName0></td>
	<td>�ڵ���</td>
	<td><input type=text name=mobile[] style="width:40px;" onkeydown="onlynumber()">��<input type=text name=mobile[] style="width:40px;" onkeydown="onlynumber()">��<input type=text name=mobile[] style="width:40px;" onkeydown="onlynumber()"></td>
</tr>
<tr>
	<td>����ڹ�ȣ</td>
	<td><input type=text name=compSerial required label="����ڹ�ȣ"> <font class=small color=444444>��) 123-45-67890</font></td>
	<td>�ѽ�</td>
	<td><input type=text name=fax[] style="width:40px;" onkeydown="onlynumber()">��<input type=text name=fax[] style="width:40px;" onkeydown="onlynumber()">��<input type=text name=fax[] style="width:40px;" onkeydown="onlynumber()"></td>
</tr>
<tr>
	<td>�̸���</td>
	<td colspan=3><input type=text name=email class=lline required label="�̸���"> <font class=small color=444444>(���ε� �� ��༭�� �������׵��� �߼۵ǿ��� ��Ȯ�� �����ϼ���.)</font></td>
</tr>
<tr>
	<td>ī�װ�</td>
	<td colspan=3>
		<select name="cate[]" required label="ī�װ�" onchange="IRS.getShopCategory(1, this.value)"><option value="">-- ī�װ� ���� --</option></select>
		<select name="cate[]" required label="��������"><option value="">-- �������� ���� --</option></select>
	</td>
</tr>
<tr>
	<td>������ũ �ֹ�<br>��ۺ���</td>
	<td colspan=3>
		�� ���ž��� <input type=text name=delvCostCondition style="width:60px;text-align:center" onkeydown="onlynumber()"> �� �̻��� ��� ����, �̸��� ��� <input type=text name=delvCostBasic style="width:60px;text-align:center" onkeydown="onlynumber()"> �� ������ �δ�<br>
		<font class=small color=444444>��) �� ���ž��� 30,000�� �̻��� ��� ����, �̸��� ��� 2,500�� ������ �δ�</font>
	</td>
</tr>
</table>

<div class="button" id="avoidSubmit">
<input type=image src="../img/btn_confirm.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>

</form>


<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�� ���� ��û���� �����Ͻ� ������ ���������� ���� ���� �ڷ�θ� ���˴ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><b>������ũ ����ڰ� ������ �� �ִ� ����� ����ó�� �Է�</b>�Ͽ� �ֽñ� �ٶ��ϴ�.</td></tr>
<tr><td>&nbsp; ����� ����ó�� �ٸ� ��� ���� Ż�� �� ���� ������ ������ ���� �ҿ�˴ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><b>�ʼ� ������ �� Ȯ���Ͻð� �غ�</b>�Ͽ� �ֽñ� �ٶ��ϴ�.</td></tr>
<tr><td>&nbsp; ���� ��û�� �Ͻ� ���θ��� <b>������ũ����(���÷���)���� �ɻ縦 ���Ͽ� ���� �ʿ伭�� ��û ����</b>�� �帳�ϴ�.</td></tr>
<tr><td>&nbsp; ��Ȯ�� ������ �غ��Ͽ� �����ֽ��� ������ ���� �� ���� ������ �ҿ�� �� �ֽ��ϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>


<script language="javascript"><!--
var param = eval( '(<?=$param?>)' );
IRS.init_set();
--></script>


<? include "../_footer.php"; ?>