<?

$location = "���¸��� ���̷�Ʈ ���� > �����å";
$scriptLoad.='<script src="./js/common.js"></script>';
include "../_header.php";

### ȯ�漳��
@include "../../conf/openmarket.php";

if (isset($omCfg) === false){
	$omCfg['ship_type'] = '0';
	$omCfg['ship_pay'] = 'Y';
}

$checked['ship_type'][$omCfg['ship_type']] = "checked";
$checked['ship_pay'][$omCfg['ship_pay']] = "checked";

if ($omCfg['ship_type'] == '0'){
	$omCfg['ship_price_0'] = $omCfg['ship_price'];
}
else if ($omCfg['ship_type'] == '5'){
	$omCfg['ship_price_5'] = $omCfg['ship_price'];
	$omCfg['ship_base_5'] = $omCfg['ship_base'];
}
else if ($omCfg['ship_type'] == '4'){
	$omCfg['ship_price_4'] = $omCfg['ship_price'];
	$omCfg['ship_base_4'] = $omCfg['ship_base'];
}

?>

<div class="title title_top">�����å <span>���¸��� �ǸŰ����� ��ǰ���۽�, �������� ���� ������ �����մϴ�.</div>
<div id="useMsg"><script>callUseable('useMsg');</script></div>

<form method="post" action="../openmarket/indb.php">
<input type="hidden" name="mode" value="set">
<table class="tb">
<col class="cellC"><col class="cellL">
<tr height=120>
	<td>��ۺ� ��å</td>
	<td>
	<table cellpadding="0" cellspacing="0">
	<col width="120">
	<tr height="25">
		<td><input type="radio" name="omCfg[ship_type]" value="3" class="null" <?=$checked['ship_type'][3]?> onclick="setShipDisabled();"> ����</td>
		<td></td>
	</tr>
	<tr height="25">
		<td><input type="radio" name="omCfg[ship_type]" value="0" class="null" <?=$checked['ship_type'][0]?> onclick="setShipDisabled();"> ����</td>
		<td><input type="text" name="omCfg[ship_price]" value="<?=$omCfg['ship_price_0']?>" size=8 class=right onkeydown="onlynumber()" disabled> �� ��ۺ� �ΰ�</td>
	</tr>
	<tr height="25">
		<td><input type="radio" name="omCfg[ship_type]" value="5" class="null" <?=$checked['ship_type'][5]?> onclick="setShipDisabled();"> ���ž׼���</td>
		<td>
		�� ���ž��� <input type="text" name="omCfg[ship_base]" value="<?=$omCfg['ship_base_5']?>" size=9 class=right onkeydown="onlynumber()" disabled> �� �̻��� �� ��ۺ� ����, �̸��� �� <input type="text" name="omCfg[ship_price]" value="<?=$omCfg['ship_price_5']?>" size=8 class=right onkeydown="onlynumber()" disabled> �� ��ۺ� �ΰ�
		</td>
	</tr>
	<tr height="25">
		<td><input type="radio" name="omCfg[ship_type]" value="4" class="null" <?=$checked['ship_type'][4]?> onclick="setShipDisabled();"> ���ŷ�����</td>
		<td>
		�� ���ŷ��� <input type="text" name="omCfg[ship_base]" value="<?=$omCfg['ship_base_4']?>" size=9 class=right onkeydown="onlynumber()" disabled> �� �̻��� �� ��ۺ� ����, �̸��� �� <input type="text" name="omCfg[ship_price]" value="<?=$omCfg['ship_price_4']?>" size=8 class=right onkeydown="onlynumber()" disabled> �� ��ۺ� �ΰ�
		</td>
	</tr>
	</table>
	</td>
</tr>
<tr height=35>
	<td>��ۺ� �������</td>
	<td>
		<input type="radio" name="omCfg[ship_pay]" value="Y" class="null" <?=$checked['ship_pay']['Y']?>> ����
		<input type="radio" name="omCfg[ship_pay]" value="N" class="null" <?=$checked['ship_pay']['N']?>> ����
	</td>
<tr height=60>
	<td>A/S ����<br>(�ȳ�����)</td>
	<td>
	<input name="omCfg[as_info]" style="width:500px;" class="line" maxlength="40" value="<?=htmlspecialchars($omCfg['as_info'])?>" onkeydown="chkLen(this, 40, 'vLength')" onkeyup="chkLen(this, 40, 'vLength')">
	(<span id="vLength">0</span>/40)
	<div class="small" style="color:#6d6d6d; padding-top:8px;">(A/S ����ó, �Ⱓ ���� �Է��ϼ���. ��/���� 40�� �̳��� �Է��ϼž� �մϴ�.)</div>
	<script>_ID('vLength').innerHTML = document.getElementsByName('omCfg[as_info]')[0].value.length;</script>
	</td>
</tr>
</table>

<div style="height:20px"></div>

<table cellpadding="0" cellspacing="0" width="650">
<tr><td align="center"><input type="image" src="../img/btn_confirm.gif" class="null"></td>
</tr></table>

<div style="height:20px"></div>

</form>

<script language="javascript"><!--
function setShipDisabled()
{
	obj = document.getElementsByName('omCfg[ship_type]');
	for (i = 0; i < obj.length; i++){
		isDisabled = (obj[i].checked == true ? false : true);
		inputObj = obj[i].parentNode.parentNode.getElementsByTagName('td')[1].getElementsByTagName('input');
		for (j = 0; j < inputObj.length; j++){
			inputObj[j].disabled = isDisabled;
			inputObj[j].style.backgroundColor = (isDisabled ? '#DDDDDD' : '#FFFFFF');
		}
	}
}

setShipDisabled();
--></script>

<? include "../_footer.php"; ?>