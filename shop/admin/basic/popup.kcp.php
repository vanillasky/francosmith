<?

include "../_header.popup.php";
?>


<div class="title title_top">KCP 무이자 기간 생성</div>

<table cellpadding=1 cellspacing=0 border=0 class=small_tip bgcolor=F7F7F7 width=100%>
<tr><td height=7></td></tr>
<tr><td style="padding-left:10"><img src="../img/icon_list.gif" align="absmiddle"><font color=0074BA>무이자할부는 반드시 (주)KCP과 별도로 협의 또는 계약하셔야 합니다.</td></tr>
<tr><td style="padding-left:10"><img src="../img/icon_list.gif" align="absmiddle">무이자 할부기간은 2 ~ 12개월까지 가능합니다.</td></tr>
<tr><td height=3></td></tr>
<tr><td style="padding-left:10"><img src="../img/icon_list.gif" align="absmiddle"><font color=0074BA>무이자 기간코드 생성방법</td></tr>
<tr><td style="padding-left:10"><font class=main>①</font> 삼성카드 6개월 무이자라면 먼저 카드사에서 삼성카드사를 선택하세요.</td></tr>
<tr><td style="padding-left:10"><font class=main>②</font> 아래 기간선택에서 6을 선택하세요.</td></tr>
<tr><td style="padding-left:10"><font class=main>③</font> 무이자기간코드생성 버튼을 누르면 아래에 코드가 생성됩니다.</td></tr>
<tr><td style="padding-left:10"><font class=main>④</font> 다른 카드사를 추가하려면 체크버튼을 해제하고 위와같은 방식으로 다시 생성합니다.</td></tr>
<tr><td height=7></td></tr>
</table>

<div style="padding-top:7"></div>

<form name="pfm" style="margin:0px;">
<table class=tb width=100%>
<col class=cellC width=20%><col class=cellL width=80%>
<tr>
	<td><font class=small color=292929>카드사 선택</td>
	<td class=noline><font color=444444>
	<input type="checkbox" name="card_comp" value="CCBC">비씨카드<nobr>
	<input type="checkbox" name="card_comp" value="CCDI">현대카드<nobr>
	<input type="checkbox" name="card_comp" value="CCKE">외환카드<nobr>
	<input type="checkbox" name="card_comp" value="CCKM">국민카드<nobr><br/>
	<input type="checkbox" name="card_comp" value="CCLG">신한카드<nobr>
	<input type="checkbox" name="card_comp" value="CCLO">롯데카드<nobr>
	<input type="checkbox" name="card_comp" value="CCSS">삼성카드<nobr>
	<input type="checkbox" name="card_comp" value="CCNH">NH카드<nobr><br/>
	<input type="checkbox" name="card_comp" value="CCHN">하나SK카드<nobr>
	</font>
	</td>
</tr>
<tr>
	<td><font class=small color=292929>기간선택(개월)</td>
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
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><font class=small color=444444>위에서 카드사와 기간을 선택하면 아래에 코드가 생성됩니다. 복사한 후 창닫고 사용하세요.</font></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><font class=small color=444444>KB카드 3개월, 삼성카드 6개월, 현대카드 12개월일 경우 <font color=red>11-3,51-6,61-12</font> 이렇게 됩니다.</font></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><font class=small color=444444>즉, '<font color=red>카드사고유번호:개월수</font>'가 코드명이 됩니다.</font></td></tr>
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