<?

include "../_header.popup.php";

include "../../lib/json.class.php";
$json = new Services_JSON();
$param = array();
$param['compName'] = $cfg[compName];
$param['ceoName'] = $cfg[ceoName];
$param['compSerial'] = $cfg[compSerial];
$param['service'] = $cfg[service];
$param['item'] = $cfg[item];
$param['email'] = $cfg[adminEmail];
$param['address'] = $cfg[address];
$param['smsAdmin'] = $cfg[smsAdmin];

$param = $json->encode($param);

?>
<div class="title title_top">�������ڼ��ݰ�꼭 �ۼ��ϱ�<span>����� �����û�մϴ�.</span></div>
<style><!--
#taxform TD { color:#666666; }
#taxform INPUT { FONT-SIZE: 9pt; COLOR: #333333; FONT-FAMILY: ����,Seoul,Verdana,Arial; height: 18px; border: 1px #B2B2B2 solid }
#taxform SELECT { FONT-SIZE: 9pt; COLOR: #545454; FONT-FAMILY: ����,Seoul,Verdana,Arial }
#taxform .box_white { border: 1px none; vertical-align:middle; }
#taxform .itemnm { background-color:#E4EBE3; text-align:center; }
#taxform .itemput { background-color:#FFFFFF; text-align:left; }
--></style>

<script src="../tax.sugi.js"></script>

<form name="form" onsubmit="return (TTM.register(this) ? false : false);">

<div style="width:620px;" id="taxform">
<table width="98%" border="0" cellpadding="1" cellspacing="1" bordercolorlight="#ac9e92" bordercolor="#FFFFFF" bgcolor="#B5C0B4" style="margin-bottom:2px;">
<col width="390"><col width="57">
<tr>
	<td rowspan="2" bgcolor="#FFFFFF">
	<table width="90%" border="0" cellspacing="0" cellpadding="0">
	<tr align="left" valign="middle">
		<td align="right"><input type="radio" class="box_white" name="TaxType" value="VAT" checked onClick="TCM.layout(this);"></td>
		<td>���ݰ�꼭</td>
		<td align="right"><input type="radio" class="box_white" name="TaxType" value="FRE" onClick="TCM.layout(this);"></td>
		<td>��꼭</td>
		<td align="right"><input type="radio" class="box_white" name="TaxType" value="RCP" onClick="TCM.layout(this);"></td>
		<td>������</td>
		<td>&nbsp;</td>
		<td>(�����ں�����)</td>
	</tr>
	</table>
	</td>
	<td class="itemnm" valign="middle">å �� ȣ </td>
	<td class="itemput" valign="middle"><input type="text" class="box_white" name="Volume" size="6" maxlength="35" tabindex="<?=++$tabindex?>"> �� </td>
	<td class="itemput" valign="middle"><input type="text" class="box_white" name="Number" size="6" maxlength="35" tabindex="<?=++$tabindex?>"> ȣ </td>
</tr>
<tr>
	<td class="itemnm" valign="middle">�Ϸù�ȣ</td>
	<td colspan="2" class="itemput" valign="middle"><input type="text" class="box_white" name="SerialNo" size="17" maxlength="35" tabindex="<?=++$tabindex?>"></td>
</tr>
</table>

<table width="98%" border="0" cellpadding="1" cellspacing="1" bordercolorlight="#ac9e92" bgcolor="#B5C0B4" style="margin-bottom:3px;">
<col><col><col width="101"><col><col><col><col><col width="100"><col><col width="100">
<tr>
	<td rowspan="7" bgcolor="#D7DDD7">��<br>��<br>��<br></td>
	<td class="itemnm">��Ϲ�ȣ</td>
	<td class="itemput" colspan="3"><input type="text" class="box_white" name="SupNo" required label="������ ��Ϲ�ȣ" size="30" maxlength="35"></td>
	<td rowspan="7" valign="middle" bgcolor="#D7DDD7" align="center">��<br>��<br>��<br>��<br>��</td>
	<td class="itemnm">��Ϲ�ȣ </td>
	<td class="itemput" colspan="3"><input type="text" class="box_white" name="BuyNo" required label="���޹޴��� ��Ϲ�ȣ" style="width:100%;" maxlength="35" tabindex="<?=++$tabindex?>">
	</td>
</tr>
<tr>
	<td class="itemnm">�� &nbsp;&nbsp;&nbsp;&nbsp;ȣ <br></td>
	<td class="itemput"><input type="text" class="box_white" name="SupComp" required label="������ ��ȣ" size="15" maxlength="35" readonly></td>
	<td class="itemnm">����</td>
	<td class="itemput"><input type="text" class="box_white" name="SupEmployer" required label="������ ����" size="10" maxlength="35"></td></td>
	<td class="itemnm">�� &nbsp;&nbsp;&nbsp;&nbsp;ȣ <br></td>
	<td class="itemput"><input type="text" class="box_white" name="BuyComp" required label="���޹޴��� ��ȣ" size="15" maxlength="35" tabindex="<?=++$tabindex?>"></td>
	<td class="itemnm">����</td>
	<td class="itemput"><input type="text" class="box_white" name="BuyEmployer" required label="���޹޴��� ����" style="width:100%;" maxlength="35" size="10" tabindex="<?=++$tabindex?>"></td>
</tr>
<tr>
	<td class="itemnm" height="17">�� &nbsp;&nbsp;&nbsp;�� </td>
	<td class="itemput" colspan="3"><input type="text" class="box_white" name="SupAddr" required label="������ �ּ�" size="35" maxlength="70"></td>
	<td class="itemnm">�� &nbsp;&nbsp;�� </td>
	<td class="itemput" colspan="3"><input type="text" class="box_white" name="BuyAddr" required label="���޹޴��� �ּ�" style="width:100%;" maxlength="70" tabindex="<?=++$tabindex?>"></td>
</tr>
<tr>
	<td class="itemnm">�� &nbsp;&nbsp;&nbsp;&nbsp;��</td>
	<td class="itemput"><input type="text" class="box_white" name="SupCond" required label="������ ����" size="15" maxlength="70"></td>
	<td class="itemnm">����</td>
	<td class="itemput"><input type="text" class="box_white" name="SupItem" required label="������ ����" size="15" maxlength="70"></td>
	<td class="itemnm">�� &nbsp;&nbsp;&nbsp;&nbsp;��</td>
	<td class="itemput"><input type="text" class="box_white" name="BuyCond" size="15" maxlength="70" tabindex="<?=++$tabindex?>"></td>
	<td class="itemnm">����</td>
	<td class="itemput"><input type="text" class="box_white" name="BuyItem" size="15" maxlength="70" tabindex="<?=++$tabindex?>"></td>
</tr>
<tr>
	<td class="itemnm">���μ�</td>
	<td class="itemput"><input type="text" class="box_white" name="SupSector" size="15" maxlength="35"></td>
	<td class="itemnm">����</td>
	<td class="itemput"><input type="text" class="box_white" name="SupEmployee" size="15" maxlength="35"></td>
	<td class="itemnm">���μ�</td>
	<td class="itemput"><input type="text" class="box_white" name="BuySector" size="15" maxlength="35" tabindex="<?=++$tabindex?>"></td>
	<td class="itemnm">����</td>
	<td class="itemput"><input type="text" class="box_white" name="BuyEmployee" size="15" maxlength="35" tabindex="<?=++$tabindex?>"></td>
</tr>
<tr>
	<td class="itemnm">�̸���</td>
	<td class="itemput" colspan="3"><input type="text" class="box_white" name="SupEmail" size="35" maxlength="50"></td>
	<td class="itemnm">�̸���</td>
	<td class="itemput" colspan="3"><input type="text" class="box_white" name="BuyEmail" style="width:100%;" maxlength="50" tabindex="<?=++$tabindex?>"></td>
</tr>
<tr>
	<td class="itemnm">�̵���ȭ</td>
	<td class="itemput" colspan="3"><input type="text" class="box_white" name="SupPhone" size="35" maxlength="35"></td>
	<td class="itemnm">�̵���ȭ</td>
	<td class="itemput" colspan="3"><input type="text" class="box_white" name="BuyPhone" style="width:100%;" maxlength="35" tabindex="<?=++$tabindex?>"></td>
</tr>
</table>
<!-- ��� �κ� -->

<table width="98%" border="0" cellspacing="1" cellpadding="0" bordercolorlight='#ac9e92' bgcolor="#B5C0B4" style='margin-bottom:3px;'>
<col width="100"><col width="520">
<tr bgcolor="#FFFFFF" align="center">
	<td bgcolor="#E4EBE3">�ݾ� �Է� ���</td>
	<td>
	<table bgcolor="#FFFFFF" border="0" width="100%">
	<col width="22%"><col width="20%"><col width="35%"><col width="8%"><col>
	<tr>
		<td><input type="radio" class="box_white" name="chkCal" value="0" checked="true" onClick="TCM.cal_state(0)">����/�ܰ��Է�</td>
		<td><input type="radio" class="box_white" name="chkCal" value="1" onClick="TCM.cal_state(1)">���ް����Է�</td>
		<td><input type="radio" class="box_white" name="chkCal" value="2" onClick="TCM.cal_state(2)">�հ�ݾ��Է� <input type="text" name="t_price" size="10"></td>
		<td><a href="javascript:TCM.cal_Sum()">[�Է�]</a></td>
		<td><input type="radio" class="box_white" name="chkCal" value="3" onClick="TCM.cal_state(3)">�����Է�</td>
		<input type="hidden" name="count_item" value="1">
	</tr>
	</table>
	</td>
</tr>
</table>

<table width="98%" border="0" cellpadding="1" cellspacing="1" bordercolorlight='#ac9e92' bgcolor="#B5C0B4" style="margin-bottom:2px;">
<col><col><col><col><col width="180">
<tr class="itemnm" id=display_title1 style='display:;' height="17">
	<td>�� �� �� �� </td>
	<td>������ </td>
	<td>�� �� �� �� </td>
	<td>�� �� </td>
	<td>�� �� </td>
</tr>
<tr class="itemnm" id=display_title2 style='display:none;' height="17">
	<td>�� �� �� �� </td>
	<td>������ </td>
	<td>�� �� �� ��</td>
	<td>&nbsp; </td>
	<td>�� �� </td>
</tr>
<tr bgcolor="#FFFFFF" align="center">
	<td style="font-weight:bold;">
	<select name="TaxYear">
	<? for ($i = 2007; $i < date('Y')+1; $i++){ ?><option value=<?=$i?><?=($i == date('Y') ? ' selected' : '')?>><?=$i?></option><? } ?>
	</select>
	/
	<select name="TaxMonth">
	<? for ($i = 1; $i < 13; $i++){ ?><option value=<?=sprintf("%02d", $i)?><?=($i == date('m') ? ' selected' : '')?>><?=sprintf("%02d", $i)?></option><? } ?>
	</select>
	/
	<select name="TaxDay">
	<? for ($i = 1; $i < 32; $i++){ ?><option value=<?=sprintf("%02d", $i)?><?=($i == date('d') ? ' selected' : '')?>><?=sprintf("%02d", $i)?></option><? } ?>
	</select>
	</td>
	<td><input type="text" name="blankCnt" size="5" value="10" onFocus=blur(); style="text-align:center;"></td>
	<td><input type="text" name="TotalMoa" size="15" maxlength="11" value="0" onFocus="this.value=extUncomma(this.value);" onBlur="TCM.calculate(this);" style="text-align:right;"></td>
	<td><font id="isTax"><input type="text" name="TotalTax" size="15" maxlength="10" value="0" onFocus="this.value=extUncomma(this.value);" onBlur="TCM.calculate(this);" style="text-align:right;"></font></td>
	<td></td>
</tr>
</table>

<table width="98%" border="0" cellpadding="2" cellspacing="1" bordercolorlight='#ac9e92' bgcolor="#B5C0B4">
<col width="20" span="2"><col width="150"><col width="40" span="2"><col width="101"><col width="100" span="2"><col width="40">
<tr class="itemnm" id=display_title3 style='display:;'>
	<td>�� </td>
	<td>�� </td>
	<td>ǰ �� </td>
	<td>�԰�</td>
	<td>����</td>
	<td>�� �� </td>
	<td>���ް��� </td>
	<td>�� �� </td>
	<td>���</td>
</tr>
<tr class="itemnm" id=display_title4 style='display:none;'>
	<td>�� </td>
	<td>�� </td>
	<td>ǰ �� </td>
	<td>�԰�</td>
	<td>����</td>
	<td>�� �� </td>
	<td>���ޱݾ�</td>
	<td>&nbsp; </td>
	<td>���</td>
</tr>
<? for ($i = 1; $i < 5; $i++){ ?>
<tr bgcolor="#FFFFFF">
	<td><input type="text" name="LinMonth<?=$i?>" size="2" maxlength="2" onBlur="this.value=TCM.chkDate(this,0);" style="text-align:center;" tabindex="<?=++$tabindex?>"></td>
	<td><input type="text" name="LinDay<?=$i?>" size="2" maxlength="2" onBlur="this.value=TCM.chkDate(this,1);" style="text-align:center;" tabindex="<?=++$tabindex?>"></td>
	<td><input type="text" name="LinItem<?=$i?>" size="22" maxlength="35" tabindex="<?=++$tabindex?>"></td>
	<td><input type="text" name="LinUnit<?=$i?>" size="4" maxlength="35" tabindex="<?=++$tabindex?>"></td>
	<td><input type="text" name="LinQty<?=$i?>" size="4" maxlength="15" onFocus="this.value=extUncomma(this.value); if(this.value==0) this.value='';" onBlur="TCM.calculate(this);" style="text-align:right;" tabindex="<?=++$tabindex?>"></td>
	<td><input type="text" name="LinPri<?=$i?>" size="14" maxlength="11" onFocus="this.value=extUncomma(this.value); if(this.value==0) this.value='';" onBlur="TCM.calculate(this);" style="text-align:right;" tabindex="<?=++$tabindex?>"></td>
	<td><input type="text" name="LinMoa<?=$i?>" size="13" maxlength="11" onFocus="this.value=extUncomma(this.value); if(this.value==0) this.value='';" onBlur="TCM.calculate(this);" style="text-align:right;" tabindex="<?=++$tabindex?>"></td>
	<td><font id="isTax_l<?=$i?>"><input type="text" name="LinTax<?=$i?>" size="13" maxlength="10" onFocus="this.value=extUncomma(this.value); if(this.value==0) this.value='';" onBlur="TCM.calculate(this);" style="text-align:right;" tabindex="<?=++$tabindex?>"></font></td>
	<td><input type="text" name="LinRemark<?=$i?>" size="4" maxlength="35" tabindex="<?=++$tabindex?>"></td>
</tr>
<? } ?>
</table>

<table width="98%" border="0" cellpadding="2" cellspacing="1" bordercolorlight='#ac9e92' bgcolor="#B5C0B4">
<col width="89" span="2"><col width="88" span="3">
<tr align="center" height="14" bgcolor="#E4EBE3">
	<td>�հ�ݾ� </td>
	<td>���� </td>
	<td>��ǥ </td>
	<td>���� </td>
	<td>�ܻ�̼���</td>
	<td rowspan="2" align="left" valign="middle" height="40" bgcolor="#efefef">�̱ݾ��� <select name=Indicator><option SELECTED value='T01'>����<option value='T02'>û�� </option></select>��</td>
</tr>
<tr height="20" bgcolor="#FFFFFF">
	<td><input type="text" name="MoaTax" size="13" maxlength="35" readonly onFocus="this.value=extUncomma(this.value); if(this.value==0) this.value='';" onBlur="this.value=extComma(this.value); if(this.value==0) this.value='';" style="text-align:right;" tabindex="<?=++$tabindex?>"></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
</tr>
</table>
</div>

<div class="button" id="avoidSubmit" style="margin:10px;">
<input type="image" src="../img/btn_confirm.gif">
<a href="javascript:parent.closeLayer()"><img src="../img/btn_cancel.gif"></a>
</div>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_tip>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">����Ʈ�� �־�߸� �����û�� �����ϸ� ������ 1point �����˴ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><font color="#EA0095">[����] ��������� ���� �������� �ԷµǴ� [�ۼ�����]�� �����ۼ����� �������� <b>30�� �̳���߸�</b> ����Ǿ����ϴ�.</font></td></tr>
</table>
</div>
<script>cssRound('MSG01','#F7F7F7')</script>

</form>

<script language="javascript"><!--
var param = eval( '(<?=$param?>)' );
TCM.init_set();
--></script>
<script>table_design_load();</script>