<?

### KCP 기본 세팅값
$_pg		= array(
			'id'	=> '',
			'zerofee'	=> 'no',
			'receipt'	=> 'N',
			'quota'		=> '00,02,03,04,05,06,07,08,09,10,11,12',
			);

$location = "결제모듈연동 > KCP PG설정";
include "../_header.popup.php";
include "../../conf/config.pay.php";

// 투데이샵 pg 설정값 불러오기
$todayShop = &load_class('todayshop', 'todayshop');
if (!$todayShop->auth()) {
	msg(' 서비스 신청안내는 고도몰 고객센터로 문의해주시기 바랍니다.', -1);
}
$tsPG = $todayShop->getPginfo();
unset($todayShop);

if ($tsPG['cfg']['settlePg']!="kcp") $tsPG = array(); // 사용중이 아니라면 pg 정보 없앰

$tsPG['pg'] = @array_merge($_pg,$tsPG['pg']);

if ($tsPG['cfg'][settlePg]=="kcp") $spot = "<b style='color:#ff0000;padding-left:10px'>[사용중]</b>";
$checked['ssl'][$tsPG['pg']['ssl']] = $checked['zerofee'][$tsPG['pg']['zerofee']] = $checked['cert'][$tsPG['pg']['cert']] = $checked['bonus'][$tsPG['pg']['bonus']] = "checked";
$checked['receipt'][$tsPG['pg']['receipt']] = "checked";

if ($tsPG['set']['use'][c]) $checked[c] = "checked";
if ($tsPG['set']['use'][o]) $checked[o] = "checked";
if ($tsPG['set']['use'][v]) $checked[v] = "checked";
if ($tsPG['set']['use'][h]) $checked[h] = "checked";
?>
<script language=javascript>
var arr=new Array('c','v','o','h');
function chkSettleKind(){
	var f = document.forms[0];

	var ret = false; var sk = false;
	for(var i=0;i < arr.length;i++)
	{
		if (document.getElementsByName('set[use]['+arr[i]+']').length == 0) continue;
		sk = document.getElementsByName('set[use]['+arr[i]+']')[0].checked;
		if(sk == true)ret=true;
	}
	var robj =  new Array('pg[id]','pg[key]','pg[quota]');

	for(var i=0;i < robj.length;i++){
		if (document.getElementsByName(robj[i]).length == 0) continue;
		var obj = document.getElementsByName(robj[i])[0];
		if(ret){
			obj.style.background = "#ffffff";
			obj.readOnly = false;
		}else{
			obj.style.background = "#e3e3e3";
			obj.readOnly = true;
			obj.value = '';
		}
	}
}
function chkFormThis(f){

	var ret = false;
	var sk = false;
	var p_id = document.getElementsByName('pg[id]')[0];
	var p_key = document.getElementsByName('pg[key]')[0];
	var p_quota = document.getElementsByName('pg[quota]')[0];

	for(var i=0;i < arr.length;i++)
	{
		if (document.getElementsByName('set[use]['+arr[i]+']').length == 0) continue;
		sk = document.getElementsByName('set[use]['+arr[i]+']')[0].checked;
		if(sk == true)ret=true;
	}

	if(!p_id.value && ret){
		p_id.focus();
		alert('KCP Code는 필수항목입니다.');
		return false;
	}
	if(!p_key.value && ret){
		p_key.focus();
		alert('KCP Key는 필수항목입니다.');
		return false;
	}
	if(!p_quota.value && ret){
		p_quota.focus();
		alert('할부기간은 필수항목입니다.');
		return false;
	}

	return chkForm(f);
}
var IntervarId;

function resizeFrame()
{
    var oBody = document.body;
    var oFrame = parent.document.getElementById("pgifrm");
    var i_height = oBody.scrollHeight + (oFrame.offsetHeight-oFrame.clientHeight);
    oFrame.style.height = i_height;

    if ( IntervarId ) clearInterval( IntervarId );
}

window.onload = function(){
	resizeFrame();
}
</script>
<style>
.show {display:block}
.hide {display:none}
</style>
<div style="postion:relative">
<div id="prefix" style="position:absolute;" class="hide">
<iframe id="pgifrm" frameborder="0" width="554" height="366"></iframe>
</div>
</div>
<div class="title title_top">
KCP PG 설정<span>신용카드 결제 및 기타결제방식은 반드시 전자결제서비스 업체와 계약을 맺으시기 바랍니다</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=21')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a>
</div>
<div id="kcp_banner"><script>panel('kcp_banner', 'pg');</script></div>
<form name="frmPGConfig" method="post" action="indb.config.pg.php" target="ifrmHidden" onsubmit="return chkFormThis(this)" />
<input type=hidden name=mode value="kcp">
<input type=hidden name=cfg[settlePg] value="kcp">

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td>KCP에서 제공하는 신용카드,계좌이체,가상계좌,핸드폰의 결제수단을 방문자(소비자)에게 제공하기 위해서</td></tr>
<tr><td>KCP에서 <b>메일로 받으신 KCP Code와 KCP Key를 입력</b>하신후 본 페이지 하단의 저장버튼을 클릭해 주세요.</td></tr>
<tr><td>아직 KCP와 계약을 하지 않으셨다면</td></tr>
<tr><td style="padding-left:10">①<u>온라인신청 하신 후</u></td></tr>
<tr><td style="padding-left:10">②<u>계약서류를 우편으로 KCP에 보내</u>주세요 <a href="../basic/pg.intro.php" target="_blank"><font color="#ffffff"><b>[계약 상세안내]</b></font></a></td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>
<div style="padding-top:15px"></div>
<table border=1 bordercolor=#e1e1e1 style="border-collapse:collapse" width=100%>
<col class=cellC><col class=cellL>
<tr>
	<td>PG사</td>
	<td><b>KCP (ESCROW AX-HUB V6) <?=$spot?></b></td>
</tr>
<tr>
	<td>결제수단 설정</td>
	<td class=noline>
	<label><input type=checkbox name=set[use][c] <?=$checked[c]?> onclick="chkSettleKind()"> 신용카드<label>
	<label><input type=checkbox name=set[use][o] <?=$checked[o]?> onclick="chkSettleKind()"> 계좌이체<label>
	<!--label><input type=checkbox name=set[use][v] <?=$checked[v]?> onclick="chkSettleKind()"> 가상계좌<label-->
	<label><input type=checkbox name=set[use][h] <?=$checked[h]?> onclick="chkSettleKind()"> 휴대폰<label>
	&nbsp;&nbsp;&nbsp;<font class=extext><b>(반드시 KCP와 계약한 결제수단만 체크하세요)</b></font></td>
</tr>
<tr>
	<td class=ver8><b>KCP Code</td>
	<td>
	<div style="float:left"><input type=text name=pg[id] id="pgid" class=lline value="<?=$tsPG['pg'][id]?>"></div>
	</td>
</tr>
<tr>
	<td class=ver8><b>KCP Key</td>
	<td>
	<input type=text name=pg[key] class=lline value="<?=$tsPG['pg'][key]?>">
	</td>
</tr>
<tr>
	<td>할부기간</td>
	<td>
	<input type=text name=pg[quota] value="<?=$tsPG['pg'][quota]?>" class=lline>
	<div class=extext style="padding-top:4">결제자가 할부 결제시 선택한 할부개월 수 입니다. 00 부터 12 의 값을 가집니다.(예: 3개월의 경우 : 03 , 일시불의 경우 : 00) </div>
	<div class=extext style="padding-top:3">최대 할부개월수만을 입력해 주시기 바랍니다 (예: 6개월할부까지만을 적용시 06 만을 입력 => 일시불~6개월할부만이 노출) </div>
	</td>
</tr>
<tr>
	<td>무이자 여부</td>
	<td class=noline>
	<input type=radio name=pg[zerofee] value="no" checked> 일반결제
	<input type=radio name=pg[zerofee] value="yes" <?=$checked[zerofee][yes]?>> 무이자결제 <font class=extext><b>(무이자결제는 반드시 PG사와 계약체결 후에 사용해야 합니다!)</b></font></td>
</tr>
<tr>
	<td>무이자 기간</td>
	<td>
	<input type=text name=pg[zerofee_period] value="<?=$tsPG['pg'][zerofee_period]?>" class=lline style="width:500px">
	<a href="javascript:popupLayer('../basic/popup.kcp.php',500,470)" style="color:#616161;" class=ver8><img src="../img/btn_carddate.gif" align=absmiddle></a>
	<div class=extext  style="padding-top:4px">옆에 있는 '무이자기간코드생성' 버튼을 눌러 코드를 생성한후 복사하여 사용하세요</div>
	</td>
</tr>
</table>
</div>

<div style="padding-top:15px"></div>

<div id=MSG02>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">PG사와 계약을 맺은 이후에는 메일로 받으신 실제 ID, Key를 넣으시면 됩니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">PG사의 결제정보 설정후 고객님께서 카드결제 테스트를 꼭 해보시기 바랍니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">간혹 PG사를 통해 카드승인된 값을 받지못하여 주문관리페이지에서 입금확인으로 자동변경되지 않을수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">반드시 주문관리페이지의 주문상태와 PG사에서 제공하는 관리자화면내의 카드승인내역도 동시에 확인해 주십시요.</font></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">가상계좌 사용시 입금 통보를 쇼핑몰로 받기 위해서는 KCP관리자에서 공통URL을 등록해주셔야 합니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">공통URL은 "http://<?=$_SERVER['HTTP_HOST']?><?=$tsPG['cfg']['rootDir']?>order/card/kcp/common_return.php" 입니다. <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=21')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></td></tr>
</table>
</div>
<script>cssRound('MSG02')</script>

<div class=title>현금영수증 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=21')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>
<table border=1 bordercolor=#e1e1e1 style="border-collapse:collapse" width=100%>
<col class=cellC><col class=cellL>
<tr>
	<td>현금영수증</td>
	<td class=noline>
	<input type=radio name=pg[receipt] value="N" <?=$checked[receipt][N]?>> 사용안함
	<input type=radio name=pg[receipt] value="Y" <?=$checked[receipt][Y]?>> 사용
	<BR><font class=extext style="padding-left:5px">KCP 현금영수증 이용은 KCP 현금영수증 안내를 확인하시기 바랍니다. <a class="extext" style="font-weight:bold" href="http://kcp.co.kr/html/cash01.jsp" target="_blank">[바로가기]</a></font>
	</td>
</tr>
</table><p>

<div id=MSG03>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">소비자는 2008. 7. 1일부터 현금영수증 발급대상금액이 5천원이상에서 1원이상으로 변경되어
5천원 미만의 현금거래도 현금영수증을 요청하여 발급 받을 수 있습니다.</td></tr>
</table>
</div>
<script>cssRound('MSG03','#F7F7F7')</script>

<div class=button>
<input type=image src="../img/btn_save.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>

</form>
<script>chkSettleKind();</script>