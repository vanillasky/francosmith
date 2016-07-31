<?

### 올앳페이 기본 세팅값
$_pg		= array(
			'ssl'		=> 'NOSSL',
			'cert'		=> 'Y',
			//'bonus'		=> 'N',
			'sell'		=> 'N',
			'zerofee'	=> 'N',
			'receipt'	=> 'N',
			//'quota'		=> '0:2:3:4:5:6:7:8:9:10:11:12',
			);

$location = "결제모듈연동 > 올앳PG 설정";
include "../_header.popup.php";
include "../../conf/config.pay.php";

// 투데이샵 pg 설정값 불러오기
$todayShop = &load_class('todayshop', 'todayshop');

if (!$todayShop->auth()) {
	msg(' 서비스 신청안내는 고도몰 고객센터로 문의해주시기 바랍니다.', -1);
}
$tsPG = $todayShop->getPginfo();
unset($todayShop);
if ($tsPG['cfg']['settlePg'] != "allatbasic") $tsPG = array(); // 사용중이 아니라면 pg 정보 없앰

$tsPG['pg'] = @array_merge($_pg,$tsPG['pg']);

if($tsPG['cfg']['settlePg']!="allatbasic") $tsPG['pg'] = array(); //pg타입체크

if ($tsPG['cfg'][settlePg]=="allatbasic") $spot = "<b style='color:#ff0000;padding-left:10px'><img src=../img/btn_on_func.gif align=absmiddle></b>";
$checked['ssl'][$tsPG['pg']['ssl']] = $checked['sell'][$tsPG['pg']['sell']] = $checked['zerofee'][$tsPG['pg']['zerofee']] = $checked['cert'][$tsPG['pg']['cert']] = $checked['bonus'][$tsPG['pg']['bonus']] = "checked";
$checked['receipt'][$tsPG['pg']['receipt']] = "checked";

if ($tsPG['set']['use'][c]) $checked[c] = "checked";
if ($tsPG['set']['use'][o]) $checked[o] = "checked";
if ($tsPG['set']['use'][v]) $checked[v] = "checked";
if ($tsPG['set']['use'][h]) $checked[h] = "checked";

// 프리픽스값
$prefix = 'GC|GM|GP|GF';
?>
<script language=javascript>
var prefix = '<? echo $prefix;?>';
var arr=new Array('c','v','o','h');

function chkSettleKind(){
	var f = document.forms[0];

	var ret = false;
	for(var i=0;i < arr.length;i++)
	{
		if (document.getElementsByName('set[use]['+arr[i]+']').length == 0) continue;
		var sk = document.getElementsByName('set[use]['+arr[i]+']')[0].checked;
		if(sk == true)ret=true;
	}
	var robj =  new Array('pg[id]','pg[crosskey]');

	for(var i=0;i < robj.length;i++){
		if (document.getElementsByName(robj[i]).length == 0) continue;
		var obj = document.getElementsByName(robj[i])[0];
		if(ret){
			obj.style.background = "#ffffff";
			obj.readOnly = false;
		}else{
			obj.style.background = "#e3e3e3";
			obj.readOnly = true;
		}
	}
}

function chkFormThis(f){

	var ret = false;
	var sk = false;
	var p_id = document.getElementsByName('pg[id]')[0];
	var p_crosskey = document.getElementsByName('pg[crosskey]')[0];
	//var p_quota = document.getElementsByName('pg[quota]')[0];

	for(var i=0;i < arr.length;i++)
	{
		if (document.getElementsByName('set[use]['+arr[i]+']').length == 0) continue;
		sk = document.getElementsByName('set[use]['+arr[i]+']')[0].checked;
		if(sk == true)ret=true;
	}

	if(!p_id.value && ret){
		p_id.focus();
		alert('All@Pay ID는 필수항목입니다.');
		return false;
	}
	if(!p_crosskey.value && ret){
		p_crosskey.focus();
		alert('All@Pay CrossKey은 필수항목입니다.');
		return false;
	}
	/*if(!p_quota.value && ret){
		p_quota.focus();
		alert('일반할부기간은 필수항목입니다.');
		return false;
	}*/
	if(!chkPgid()){
		alert('All@Pay ID가 올바르지 않습니다.');
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

var oldId = "<?php echo $tsPG['pg']['id'];?>";
function openPrefix(){
	if(chkPgid()){
		alert("정상적인 All@Pay ID입니다.\n개별 승인 신청이 필요 없습니다.\n창을 닫고 All@Pay ID를 입력하세요!");
		return;
	}
	var obj = document.getElementById('prefix');
	var pgid = document.getElementById('pgid').value;
	var ifrm = document.getElementById('pgifrm');
	get_pginfo(pgid);
	obj.className = 'show';
}
function closePrefix(){
	var obj = document.getElementById('prefix');
	document.getElementById('pgid').value='';
	obj.className = 'hide';
}
function get_pginfo(pgid){
	var ajax = new Ajax.Request( "../../proc/pginfo.indb.php",
	{
		method: "post",
		parameters: "mode=getPginfo&pgtype=allatbasic&todayshoppg=y&pgid="+pgid,
		onComplete: function ()
		{
			var req = ajax.transport;
			if (req.status != 200) return;
			if (req.responseText =='') return;
			var ifrm = document.getElementById('pgifrm');
			ifrm.src = req.responseText;
		}
	} );
}
function chkPgid(){
	var obj = document.getElementById('pgid');
	var pattern = new RegExp('^('+prefix+')');
	if(pattern.test(obj.value) || (oldId == obj.value && oldId)){
		return true;
	}else if(obj.value){
		return false;
	}
	return true;
}

window.onload = function(){
	resizeFrame();
	chkPgid();
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
올앳PG 설정<span>신용카드 결제 및 기타결제방식은 반드시 전자결제서비스 업체와 계약을 맺으시기 바랍니다</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=6')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a>
</div>
<div id="allatbasic_banner"><script>panel('allatbasic_banner', 'pg');</script></div>
<form name="frmPGConfig" method="post" action="indb.config.pg.php" target="ifrmHidden" onsubmit="return chkFormThis(this)" />
<input type=hidden name=mode value="allatbasic">
<input type=hidden name=cfg[settlePg] value="allatbasic">

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td>올앳에서 제공하는 신용카드,계좌이체,가상계좌,핸드폰의 결제수단을 방문자(소비자)에게 제공하기 위해서</td></tr>
<tr><td>올앳에서 <b>메일로 받으신 All@Pay ID와 Form Key, CrossKey를 입력</b>하신후 본 페이지 하단의 저장버튼을 클릭해 주세요.</td></tr>
<tr><td>아직 올앳와 계약을 하지 않으셨다면</td></tr>
<tr><td style="padding-left:10">①<u>온라인신청 하신후</u></td></tr>
<tr><td style="padding-left:10">②<u>계약서류를 우편으로 올앳에 보내</u>주세요 <a href="../basic/pg.intro.php" target="_blank" style="color:#ffffff;font-weight:bold">[계약 상세안내]</a></td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>
<div style="padding-top:15"></div>

<table border=1 bordercolor=#e1e1e1 style="border-collapse:collapse" width=100%>
<col class=cellC><col class=cellL>
<tr>
	<td>PG사</td>
	<td class=noline><b>올앳 (All@Pay™ BASIC) <?=$spot?></b></td>
</tr>
<tr>
	<td>결제수단 설정</td>
	<td class=noline>
	<label><input type=checkbox name=set[use][c] <?=$checked[c]?> onclick="chkSettleKind()"> 신용카드</label>
	<label><input type=checkbox name=set[use][o] <?=$checked[o]?> onclick="chkSettleKind()"> 계좌이체</label>
	<!--label><input type=checkbox name=set[use][v] <?=$checked[v]?> onclick="chkSettleKind()"> 가상계좌</label-->
	<label><input type=checkbox name=set[use][h] <?=$checked[h]?> onclick="chkSettleKind()"> 핸드폰</label>
	&nbsp;&nbsp;&nbsp;<font class=extext><b>(반드시 올앳과 계약한 결제수단만 체크하세요)</b></font>
	</td>
</tr>
<tr class=ver8>
	<td><b>All@Pay ID</td>
	<td>
	<div style="float:left"><input type="text" name="pg[id]" class="lline" value="<?=$tsPG['pg'][id]?>" onkeyup="chkPgid()" onblur="chkPgid()" id="pgid"></div>
	<div style="float:left;padding:0 0 0 5" id="btPgId"><a href="javascript:openPrefix();"><img src="../img/pginfo.gif" alt="개별 승인 신청" /></a></div>
	<div style="clear:both" class="extext"><? echo str_replace('|',',', $prefix);?>로 시작되는 All@Pay ID만 직접 입력 가능합니다. (단, 기존 입력값은 무방합니다)</div>
	<div class="extext">고도몰 솔루션 이용자중 이전 버전을 사용하고 있어 위의 아이디로 시작하지 않는 경우에는 개별 승인 신청을 하셔야 합니다.</div>
	</td>
</tr>
<tr class=ver8>
	<td><b>All@Pay CrossKey</td>
	<td><input type=text name=pg[crosskey] class=lline value="<?=$tsPG['pg'][crosskey]?>"> <font class=extext>CrossKey를 넣으세요</td>
</tr>
<?
$pg_ssl = $sitelink->old_get_type();
?>
<input type=hidden name=pg[ssl] value="<?=$pg_ssl?>">
<!--tr>
	<td>일반할부기간</td>
	<td>
	<input type=text name=pg[quota] value="<?=$tsPG['pg'][quota]?>" class=lline>
	<span class=extext>ex) <?=$_pg[quota]?></span>
	</td>
</tr-->
<tr>
	<td>할부 사용 여부</td>
	<td class=noline>
	<input type=radio name=pg[sell] value="Y" <?=$checked[sell][Y]?>> 할부사용
	<input type=radio name=pg[sell] value="N" <?=$checked[sell][N]?>> 할부사용안함	
	</td>
</tr>
<tr>
	<td>무이자 여부</td>
	<td class=noline>
	<input type=radio name=pg[zerofee] value="N" <?=$checked[zerofee][N]?>> 일반결제
	<input type=radio name=pg[zerofee] value="Y" <?=$checked[zerofee][Y]?>> 무이자결제 <font class=extext><b>(무이자결제는 반드시 PG사와 계약체결 후에 사용해야 합니다!)</b></font></td>
</tr>
<tr>
	<td>카드결제 인증 여부</td>
	<td class=noline>
	<input type=radio name=pg[cert] value="Y" <?=$checked[cert][Y]?>> 사용함
	<input type=radio name=pg[cert] value="N" <?=$checked[cert][N]?>> 사용안함
	</td>
</tr>
<!--tr>
	<td>보너스 포인트</td>
	<td class=noline>
	<input type=radio name=pg[bonus] value="Y" <?=$checked[bonus][Y]?>> 사용
	<input type=radio name=pg[bonus] value="N" <?=$checked[bonus][N]?>> 사용안함
	</td>
</tr-->
</table>
<div style="padding-top:5"></div>
<div id=MSG02>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">PG사와 계약을 맺은 이후에는 메일로 받으신 실제 ID, Key를 넣으시면 됩니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">PG사의 결제정보 설정후 고객님께서 카드결제 테스트를 꼭 해보시기 바랍니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">간혹 PG사를 통해 카드승인된 값을 받지못하여 주문관리페이지에서 입금확인으로 자동변경되지 않을수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">반드시 주문관리페이지의 주문상태와 PG사에서 제공하는 관리자화면내의 카드승인내역도 동시에 확인해 주십시요.</font></td></tr>
</table>
</div>
<script>cssRound('MSG02')</script>

<div class=title>현금영수증 <!--span>설정된 PG사의 현금영수증을 사용하며, 별도 계약을 해야 함</span--> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=6')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>
<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class=cellC><col class=cellL>
<tr>
	<td>현금영수증</td>
	<td class=noline>
	<input type=radio name=pg[receipt] value="N" <?=$checked[receipt][N]?>> 사용안함
	<input type=radio name=pg[receipt] value="Y" <?=$checked[receipt][Y]?>> 사용
	<BR><font class=extext style="padding-left:5px">삼성올앳 현금영수증 이용은 삼성올앳 현금영수증 안내를 확인하시기 바랍니다. <a class="extext" style="font-weight:bold" href="http://www.allatpay.com/servlet/AllatBiz/svcinfo/si_receipt_apply.jsp?menu_id=idS16" target="_blank">[바로가기]</a></font>
	</td>
</tr>
</table><p>

<div id=MSG03>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">현금영수증</font>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">소비자는 2008. 7. 1일부터 현금영수증 발급대상금액이 5천원이상에서 1원이상으로 변경되어
5천원 미만의 현금거래도 현금영수증을 요청하여 발급 받을 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">현금영수증 사용 체크시 무통장, 계좌이체, 가상계좌 결제에 대해서 소비자가 신청한 현금영수증이 발급 됩니다</td></tr>
</table>
</div>
<script>cssRound('MSG03')</script>

<div class=button>
<input type=image src="../img/btn_save.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>

</form>
<script>chkSettleKind();</script>