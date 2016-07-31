<?
include "../_header.popup.php";
?>
<div class=title>환불수수료 설정<span>환불수수료의 기본값을 설정합니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=2')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>
<form action='indb.php'  method=post onsubmit='return chkForm(this)'>
<input type=hidden name=mode value="cfgemoney">
<table class=tb width=100%>
<col class=cellC><col class=cellL>
<tr><td colspan=20 height=1 bgcolor=DDDDDD></td></tr>
<tr>
	<td>환불수수료</td>
	<td>
		<input type=hidden name=repayfee size=3 maxlength=3 value="" onkeydown="onlynumber()" class=line style='text-align=right'>
		<!--<div>주문금액의 <input type=text name=repayfee size=3 maxlength=3 value="<?=$cfg[repayfee]?>" onkeydown="onlynumber()" class=line style='text-align=right'> %&nbsp;<span class=small1><font color=#5B5B5B>(공란으로 두면 아래 기본수수료만 적용됩니다)</font></span></div>-->
		<div style="padding-top:2">기본수수료 <input type=text name=minrepayfee size=8 maxlength=8 value="<?=$cfg[minrepayfee]?>" class=line onkeydown="onlynumber()" style='text-align=right'> 원&nbsp;<span class=small1><font color=#5B5B5B>(공란으로 두면 기본수수료는 0원이 됩니다)</font></span></div>
		<input type=hidden name=minpos size=1 maxlength=1 value="" class=line onkeydown="onlynumber()" style='text-align=right'>
		<!--
		<div style="padding-top:2"><input type=text name=minpos size=1 maxlength=1 value="<?=$cfg[minpos]?>" class=line onkeydown="onlynumber()" style='text-align=right'> 자리 이하는 절삭 처리합니다.</div>
		<div style="padding-top:2"><span class=small><font color=#5B5B5B>ex) 1자리: 1원단위, 2자리: 10원단위, 3자리: 100원단위 (숫자만 입력) </font></span></div>-->
	</td>
</tr>
<tr><td colspan=20 height=1 bgcolor=DDDDDD></td></tr>
<!--
<tr>
	<td>사용자주문취소</td>
	<td class=noline>
		<input type='radio'  name='userCancel' value='1' <?=$checked[userCancel][1]?>> 사용 <input type='radio'  name='userCancel' value='0' <?=$checked[userCancel][0]?>> 비사용
		&nbsp;<span class=small><font color=#5B5B5B>사용에 체크시 주문접수단계에서 구매자가 주문취소 가능합니다.</font></span>
	</td>
</tr>
-->
</table>
<div class="button">
<input type=image src="../img/btn_save.gif">

</div>

</form>
<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_tip>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><font color=0074BA>환불수수료</font> : 환불시 발생되는 반송비용 및 기타 수수료 등을 정하실 수 있습니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01','#F7F7F7')</script>