<?

include "../_header.popup.php";
?>


<div class="title title_top">KCP ������ �Ⱓ ����</div>

<table cellpadding=1 cellspacing=0 border=0 class=small_tip bgcolor=F7F7F7 width=100%>
<tr><td height=7></td></tr>
<tr><td style="padding-left:10"><img src="../img/icon_list.gif" align="absmiddle"><font color=0074BA>�������Һδ� �ݵ�� (��)KCP�� ������ ���� �Ǵ� ����ϼž� �մϴ�.</td></tr>
<tr><td style="padding-left:10"><img src="../img/icon_list.gif" align="absmiddle">������ �ҺαⰣ�� 2 ~ 12�������� �����մϴ�.</td></tr>
<tr><td height=3></td></tr>
<tr><td style="padding-left:10"><img src="../img/icon_list.gif" align="absmiddle"><font color=0074BA>������ �Ⱓ�ڵ� �������</td></tr>
<tr><td style="padding-left:10"><font class=main>��</font> �Ｚī�� 6���� �����ڶ�� ���� ī��翡�� �Ｚī��縦 �����ϼ���.</td></tr>
<tr><td style="padding-left:10"><font class=main>��</font> �Ʒ� �Ⱓ���ÿ��� 6�� �����ϼ���.</td></tr>
<tr><td style="padding-left:10"><font class=main>��</font> �����ڱⰣ�ڵ���� ��ư�� ������ �Ʒ��� �ڵ尡 �����˴ϴ�.</td></tr>
<tr><td style="padding-left:10"><font class=main>��</font> �ٸ� ī��縦 �߰��Ϸ��� üũ��ư�� �����ϰ� ���Ͱ��� ������� �ٽ� �����մϴ�.</td></tr>
<tr><td height=7></td></tr>
</table>

<div style="padding-top:7"></div>

<form name="pfm" style="margin:0px;">
<table class=tb width=100%>
<col class=cellC width=20%><col class=cellL width=80%>
<tr>
	<td><font class=small color=292929>ī��� ����</td>
	<td class=noline><font color=444444>
	<input type="checkbox" name="card_comp" value="CCBC">��ī��<nobr>
	<input type="checkbox" name="card_comp" value="CCDI">����ī��<nobr>
	<input type="checkbox" name="card_comp" value="CCKE">��ȯī��<nobr>
	<input type="checkbox" name="card_comp" value="CCKM">����ī��<nobr><br/>
	<input type="checkbox" name="card_comp" value="CCLG">����ī��<nobr>
	<input type="checkbox" name="card_comp" value="CCLO">�Ե�ī��<nobr>
	<input type="checkbox" name="card_comp" value="CCSS">�Ｚī��<nobr>
	<input type="checkbox" name="card_comp" value="CCNH">NHī��<nobr><br/>
	<input type="checkbox" name="card_comp" value="CCHN">�ϳ�SKī��<nobr>
	</font>
	</td>
</tr>
<tr>
	<td><font class=small color=292929>�Ⱓ����(����)</td>
	<td class=noline><font class=ver7 color=444444>
	<input type="checkbox" name="mon" value="02">2 <nobr>
	<input type="checkbox" name="mon" value="03">3 <nobr>
	<input type="checkbox" name="mon" value="04">4 <nobr>
	<input type="checkbox" name="mon" value="05">5 <nobr>
	<input type="checkbox" name="mon" value="06">6 <nobr>
	<input type="checkbox" name="mon" value="07">7 <nobr>
	<input type="checkbox" name="mon" value="08">8 <nobr>
	<input type="checkbox" name="mon" value="09">9 <nobr>
	<input type="checkbox" name="mon" value="10">10 <nobr>
	<input type="checkbox" name="mon" value="11">11 <nobr>
	<input type="checkbox" name="mon" value="12">12 <nobr>
	</td>
</tr>
</table>
</form>

<div style="text-align:center; margin-top:10px;"><img src="../img/btn_carddate.gif" align="absmiddle" onclick="month_add()"></div>

<table cellpadding=1 cellspacing=0 border=0 width=100%>
<tr><td height=18></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><font class=small color=444444>������ ī���� �Ⱓ�� �����ϸ� �Ʒ��� �ڵ尡 �����˴ϴ�. ������ �� â�ݰ� ����ϼ���.</font></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><font class=small color=444444>KBī�� 3����, �Ｚī�� 6����, ����ī�� 12������ ��� <font color=red>11-3,51-6,61-12</font> �̷��� �˴ϴ�.</font></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><font class=small color=444444>��, '<font color=red>ī��������ȣ:������</font>'�� �ڵ���� �˴ϴ�.</font></td></tr>
<tr><td><div style="background-color:#000000; color:#09FF05; padding:5px; text-align:center; height:25;" id="result_code"></div></td></tr>
</table>


<script language="javascript"><!--
var fobj = document.pfm;

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

	if ( str_mon == '�Ⱓ�ڵ带 ������ �� �����ؼ� ����ϼ���.' ) str_mon = '';
	if ( tmp1.length && str_mon != '' ) str_mon += ',';
	if ( tmp1.length ) str_mon += tmp1.join( ',' );

	document.getElementById('result_code').innerText = str_mon;
}
--></script>



<script>table_design_load();</script>