<?

include "../_header.popup.php";
?>

<div class="title title_top">�ô�����Ʈ ������ �Ⱓ ����</div>

<div id="MSG02">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><b>������ �ô�����Ʈ���� �������Һ� ���� ���.</b></td></tr>
<tR><td><img src="../img/icon_list.gif" align="absmiddle">������ �ҺαⰣ�� 2 ~ 12�������� ����.</td></tr>
<tR><td><img src="../img/icon_list.gif" align="absmiddle">�Ⱓ������ ���ī�忡 �ϰ������� �����ϰų�, �� ī��纰�� ���� ����.</td></tr>
<tR><td><img src="../img/icon_list.gif" align="absmiddle">BC(100), KB(200), NH(201), ��ȯ(300), �ϳ� SK(310), �Ｚ(400), ����(500), <br/>&nbsp; ����(800), �Ե�(900)</td></tr>
</table>
</div>
<script>cssRound('MSG02','#F7F7F7')</script>

<form name="pfm" style="margin:0px;">
<table class="tb" style="margin-top:10px;">
<col class="cellC" width=20%><col class="cellL" width=80%>
<tr>
	<td>���� ����</td>
	<td class=noline>
	<input type="radio" name="big_limit" onclick="code_add()" checked> ��� �Һΰŷ� ������
	<input type="radio" name="big_limit" onclick="code_add()"> �� ī��纰 ����
	</td>
</tr>
<tr>
	<td>ī���</td>
	<td class="noline">
		<table class="small" style="width:100%;">
		<tr>
			<td><input type="checkbox" name="card_comp" value="100" class="lgu_chbox"> BC(100) </td>
			<td><input type="checkbox" name="card_comp" value="200" class="lgu_chbox"> KB(200) </td>
			<td><input type="checkbox" name="card_comp" value="201" class="lgu_chbox"> NH(201) </td>
		</tr>
		<tr>
			<td><input type="checkbox" name="card_comp" value="300" class="lgu_chbox"> ��ȯ(300) </td>
			<td><input type="checkbox" name="card_comp" value="310" class="lgu_chbox"> �ϳ� SK(310) </td>
			<td><input type="checkbox" name="card_comp" value="400" class="lgu_chbox"> �Ｚ(400) </td>
		</tr>
		<tr>
			<td><input type="checkbox" name="card_comp" value="500" class="lgu_chbox"> ����(500) </td>
			<td><input type="checkbox" name="card_comp" value="800" class="lgu_chbox"> ����(800) </td>
			<td><input type="checkbox" name="card_comp" value="900" class="lgu_chbox"> �Ե�(900) </td>
		</tr>
		</table>
	</td>
</tr>
<tr>
	<td>�Ⱓ(����)</td>
	<td class="noline">
	<input type="checkbox" name="mon" value="2" class="lgu_chbox"> 2 
	<input type="checkbox" name="mon" value="3" class="lgu_chbox"> 3 
	<input type="checkbox" name="mon" value="4" class="lgu_chbox"> 4 
	<input type="checkbox" name="mon" value="5" class="lgu_chbox"> 5 
	<input type="checkbox" name="mon" value="6" class="lgu_chbox"> 6 
	<input type="checkbox" name="mon" value="7" class="lgu_chbox"> 7 
	<input type="checkbox" name="mon" value="8" class="lgu_chbox"> 8 
	<input type="checkbox" name="mon" value="9" class="lgu_chbox"> 9 
	<input type="checkbox" name="mon" value="10" class="lgu_chbox"> 10 
	<input type="checkbox" name="mon" value="11" class="lgu_chbox"> 11 
	<input type="checkbox" name="mon" value="12" class="lgu_chbox"> 12 
	</td>
</tr>
</table>
</form>

<div style="text-align:center; margin-top:10px;"><img src="../img/i_add.gif" align="absmiddle" onclick="month_add()"></div><p>

&#149; �Ⱓ�ڵ� <span class="small">(������ �ڵ带 �����ؼ� ����ϼ���.)</span>
<div style="background-color:#000000; color:#09FF05; padding:5px; text-align:center; height:25;" id="result_code">�Ⱓ�ڵ带 ������ �� �����ؼ� ����ϼ���.</div><p>


<script language="javascript"><!--
var fobj = document.pfm;
function code_add(){
	if ( fobj.big_limit[0].checked ) document.getElementById('result_code').innerText = 'ALL';
	else document.getElementById('result_code').innerText = '';

	// Ȱ��ȭ, ��Ȱ��ȭ ó��
	cnt = fobj.card_comp.length;
	for ( i=0; i < cnt; i++ ) fobj.card_comp[i].disabled = fobj.big_limit[0].checked;

	cnt = fobj.mon.length;
	for ( i=0; i < cnt; i++ ) fobj.mon[i].disabled = fobj.big_limit[0].checked;
}

function month_add(){
	cnt1 = fobj.card_comp.length;
	cnt2 = fobj.mon.length;

	var tmp1 = new Array();
	var itmp1 = 0;

	for ( i=0; i < cnt1; i++ ){
		if ( fobj.card_comp[i].checked == false ) continue;

		var tmp2 = new Array();
		var itmp2 = 0;
		for ( j=0; j < cnt2; j++ ){
			if ( fobj.mon[j].checked ) tmp2[ itmp2++ ] = fobj.mon[j].value;
		}

		if ( tmp2.length ) tmp1[ itmp1++ ] = fobj.card_comp[i].value + '-' + tmp2.join( ':' );
	}

	var str_mon = document.getElementById('result_code').innerText;

	if ( tmp1.length && str_mon != '' ) str_mon += ',';
	if ( tmp1.length ) str_mon += tmp1.join( ',' );

	document.getElementById('result_code').innerText = str_mon;
}

code_add();
--></script>

<script>table_design_load();</script>