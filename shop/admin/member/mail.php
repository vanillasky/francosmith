<?

$location = "회원관리 > 메일보내기";
include "../_header.php";
include "../../lib/page.class.php";
?>
<script>
function sendMail(fm)
{
	if (fm.type.value=="select" && !isChked(document.getElementsByName('chk[]'))) return false;
	openLayer('objEmail','block');
	fm.target = "ifrmEmail";
	fm.action = "email.php?ifrmScroll=1";
	fm.submit();
}
function checkSendMail(fm)
{
	if(fm.receiveRefuseCount.value > 0){
		openLayerPopupReceiveRefuse('individualEmail');
	}
	else {
		sendMail(fm);
	}
}
</script>
<?
$tot = getMailCnt();
$freeEmail = 3000 - $tot;

include "member_list.php";
?>
<table bgcolor=F7F7F7 width=100%>
<tr>
	<td class=noline width=57% align=right>
	<select name=type onchange="javascript:getCountActReceiveRefuse('individualEmail');">
	<option value="select">선택한 회원들에게
	<option value="query">현재 검색리스트에 있는 모든 회원에게
	<option value="direct">특정회원에게 (직접입력)
	</select>
	</td>
	<td width=43% style="padding-left:10px">
	<a href="javascript:void(0)" onClick="javascript:checkSendMail(document.fmList);"><img src="../img/btn_mailtomember.gif" border=0></a>
	</td>
</tr>
</table>
<p>
<div id=objEmail style="display:none">
<iframe name=ifrmEmail style="width:100%;height:1050px" frameborder=0></iframe>
</div>

</form>

<script>window.onload = function(){ UNM.inner();};</script>

<? include "../_footer.php"; ?>