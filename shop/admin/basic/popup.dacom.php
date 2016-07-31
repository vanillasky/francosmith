<?

include "../_header.popup.php";
?>


<div class="title title_top">데이콤 무이자 기간 생성</div>

<table cellpadding=1 cellspacing=0 border=0 class=small_tip bgcolor=F7F7F7 width=100%>
<tr><td height=7></td></tr>
<tr><td style="padding-left:10"><font class=small1 color=0074BA><b>[무이자할부 안내]</b></td></tr>
<tr><td style="padding-left:10"><img src="../img/icon_list.gif" align="absmiddle"><font class=small1 color=666666>무이자할부는 반드시 (주)데이콤과 별도로 협의 또는 계약하셔야 합니다.</font></td></tr>
<tr><td style="padding-left:10"><img src="../img/icon_list.gif" align="absmiddle"><font class=small1 color=666666>무이자 할부기간은 2 ~ 12개월까지 가능합니다.</font></td></tr>
<tr><td style="padding-left:10"><img src="../img/icon_list.gif" align="absmiddle"><font class=small1 color=666666><font color=red>붉은색 카드사</font>는 무이자 할부 불가 카드입니다.</font></td></tr>
<tr><td height=6></td></tr>
<tr><td style="padding-left:10"><font class=small1 color=0074BA><b>[무이자 기간코드 생성방법]</b></font></td></tr>
<tr><td style="padding-left:10"><font class=small1 color=666666>① 삼성카드 6개월 무이자라면 먼저 카드사에서 삼성카드사를 선택하세요.</font></td></tr>
<tr><td style="padding-left:10"><font class=small1 color=666666>② 아래 기간선택에서 6을 선택하세요.</font></td></tr>
<tr><td style="padding-left:10"><font class=small1 color=666666>③ 무이자기간코드생성 버튼을 누르면 아래에 코드가 생성됩니다.</font></td></tr>
<tr><td style="padding-left:10"><font class=small1 color=666666>④ 다른 카드사를 추가하려면 체크버튼을 해제하고 위와같은 방식으로 다시 생성합니다.</font></td></tr>
<tr><td height=7></td></tr>
</table>

<div style="padding-top:7"></div>

<form name="pfm" style="margin:0px;">
<table class="tb" style="width:100%;">
<col class="cellC" width="15%"></col>
<col class="cellL" width="85%"></col>
<tr>
	<td align="center"><font class="small" color="666666">카드사<br>선택</td>
	<td class="noline"><font class="small" color="444444">
		<table class="small" style="width:100%;">
		<tr>
			<td><input type="checkbox" name="card_comp" value="11" class="lgu_chbox">KB(11)</td>
			<td><input type="checkbox" name="card_comp" value="21" class="lgu_chbox">외환(21) </td>
			<td><input type="checkbox" name="card_comp" value="29" class="lgu_chbox">산은캐피탈(29)</td>
		</tr>
		<tr>
			<td><input type="checkbox" name="card_comp" value="31" class="lgu_chbox">비씨(31) </td>
			<td><input type="checkbox" name="card_comp" value="32" class="lgu_chbox">하나(32)</td>
			<td><input type="checkbox" name="card_comp" value="33" class="lgu_chbox">우리(구 평화VISA)(33)</td>
		</tr>
		<tr>
			<td><input type="checkbox" name="card_comp" value="34" class="lgu_chbox">수협(34)</td>
			<td><input type="checkbox" name="card_comp" value="35" class="lgu_chbox">전북(35)</td>
			<td><input type="checkbox" name="card_comp" value="36" class="lgu_chbox">씨티(36)</td>
		</tr>
		<tr>
			<td><input type="checkbox" name="card_comp" value="41" class="lgu_chbox">신한(구.LG카드 포함)(41)</td>
			<td><input type="checkbox" name="card_comp" value="42" class="lgu_chbox">제주(42)</td>
			<td><input type="checkbox" name="card_comp" value="46" class="lgu_chbox">광주(46)</td>
		</tr>
		<tr>
			<td><input type="checkbox" name="card_comp" value="51" class="lgu_chbox">삼성(51)</td>
			<td><input type="checkbox" name="card_comp" value="61" class="lgu_chbox">현대(61)</td>
			<td><input type="checkbox" name="card_comp" value="71" class="lgu_chbox">롯데(71)</td>
		</tr>
		<tr>
			<td><input type="checkbox" name="card_comp" value="91" class="lgu_chbox">NH(91)</td>
			<td><input type="checkbox" name="card_comp" value="4J" class="lgu_chbox"><font color=red>해외JCB(4J)</font> </td>
			<td><input type="checkbox" name="card_comp" value="4V" class="lgu_chbox"><font color=red>해외VISA(4V)</font></td>
		</tr>
		<tr>
			<td><input type="checkbox" name="card_comp" value="4M" class="lgu_chbox"><font color=red>해외MASTER(4M)</font></td>
			<td><input type="checkbox" name="card_comp" value="6D" class="lgu_chbox"><font color=red>해외DINERS(6D)</font></td>
			<td><input type="checkbox" name="card_comp" value="6I" class="lgu_chbox"><font color=red>해외DISCOVER(6I)</font></td>
		</tr> 
		</table>
	</td>
</tr>
<tr>
	<td align="center"><font class="small" color="666666">기간선택<br>(할부개월)</td>
	<td class="noline"><font class="ver7" color="444444">
	<input type="checkbox" name="mon" value="2" class="lgu_chbox">2 
	<input type="checkbox" name="mon" value="3" class="lgu_chbox">3 
	<input type="checkbox" name="mon" value="4" class="lgu_chbox">4 
	<input type="checkbox" name="mon" value="5" class="lgu_chbox">5 
	<input type="checkbox" name="mon" value="6" class="lgu_chbox">6 
	<input type="checkbox" name="mon" value="7" class="lgu_chbox">7 
	<input type="checkbox" name="mon" value="8" class="lgu_chbox">8 
	<input type="checkbox" name="mon" value="9" class="lgu_chbox">9 
	<input type="checkbox" name="mon" value="10" class="lgu_chbox">10 
	<input type="checkbox" name="mon" value="11" class="lgu_chbox">11 
	<input type="checkbox" name="mon" value="12" class="lgu_chbox">12 
	</td>
</tr>
</table>
</form>

<div style="text-align:center; margin-top:10px;"><img src="../img/btn_carddate.gif" align="absmiddle" onclick="month_add()"></div>

<table cellpadding=1 cellspacing=0 border=0 width=100%>
<tr><td height=18></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><font class=small color=444444>위에서 카드사와 기간을 선택하면 아래에 코드가 생성됩니다. 복사한 후 창닫고 사용하세요.</font></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><font class=small color=444444>KB카드 3개월, 삼성카드 6개월, 현대카드 12개월일 경우 <font color=red>11-3,51-6,61-12</font> 이렇게 됩니다.</font></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><font class=small color=444444>즉, '<font color=red>카드사고유번호-개월수</font>'가 코드명이 됩니다.</font></td></tr>
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

	if ( str_mon == '기간코드를 생성한 후 복사해서 사용하세요.' ) str_mon = '';
	if ( tmp1.length && str_mon != '' ) str_mon += ',';
	if ( tmp1.length ) str_mon += tmp1.join( ',' );

	document.getElementById('result_code').innerText = str_mon;
}
--></script>



<script>table_design_load();</script>