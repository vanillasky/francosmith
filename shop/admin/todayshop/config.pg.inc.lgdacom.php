<?

### 데이콤 기본 세팅값
$_pg		= array(
			'id'	=> '',
			'zerofee'	=> 'no',
			'receipt'	=> 'N',
			'quota'		=> '0:2:3:4:5:6:7:8:9:10:11:12',
			);

$location = "결제모듈연동 > LG U+ PG설정";
include "../_header.popup.php";
include "../../conf/config.pay.php";

// 투데이샵 pg 설정값 불러오기
$todayShop = &load_class('todayshop', 'todayshop');
if (!$todayShop->auth()) {
	msg(' 서비스 신청안내는 고도몰 고객센터로 문의해주시기 바랍니다.', -1);
}
$tsPG = $todayShop->getPginfo();
unset($todayShop);

if ($tsPG['cfg']['settlePg']!="lgdacom") $tsPG = array(); // 사용중이 아니라면 pg 정보 없앰

if (!function_exists('curl_init')) {
	$msg = "LG U+ XPay은 서버에 CURL Library가 설치되어 있어야 가능합니다.\\n고도에 문의 하시거나, 독립형의 경우 호스팅업체에 문의 하십시요.\\nCURL Library가 없는경우 데이콤 Noteurl 방식으로 설정 됩니다. ";
	echo("<script>alert('".$msg."');parent.chgifrm('dacom.php',2);</script>");
}

$tsPG['pg'] = @array_merge($_pg,$tsPG['pg']);

if($tsPG['cfg']['settlePg']!="lgdacom") $tsPG['pg'] = array(); //pg타입체크

if ($tsPG['cfg']['settlePg']=="lgdacom") $spot = "<b style='color:#ff0000;padding-left:10px'>[사용중]</b>";
$checked['ssl'][$tsPG['pg']['ssl']] = $checked['zerofee'][$tsPG['pg']['zerofee']] = $checked['cert'][$tsPG['pg']['cert']] = $checked['bonus'][$tsPG['pg']['bonus']] = "checked";
$checked['receipt'][$tsPG['pg']['receipt']] = "checked";
$checked['skin'][$tsPG['pg']['skin']] = "checked";
$checked['serviceType'][$tsPG['pg']['serviceType']] = "checked";

if ($tsPG['set']['use']['c']) $checked['c'] = "checked";
if ($tsPG['set']['use']['o']) $checked['o'] = "checked";
if ($tsPG['set']['use']['v']) $checked['v'] = "checked";
if ($tsPG['set']['use']['h']) $checked['h'] = "checked";
?>
<script language=javascript>

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
	var robj =  new Array('pg[id]','pg[mertkey]','pg[quota]');

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
	var p_key =  document.getElementsByName('pg[mertkey]')[0];
	var p_quota = document.getElementsByName('pg[quota]')[0];
	for(var i=0;i < arr.length;i++)
	{
		if (document.getElementsByName('set[use]['+arr[i]+']').length == 0) continue;
		sk = document.getElementsByName('set[use]['+arr[i]+']')[0].checked;
		if(sk == true)ret=true;
	}

	if(!p_id.value && ret){
		p_id.focus();
		alert('LG U+ ID는 필수항목입니다.');
		return false;
	}
	if(!p_key.value && ret){
		p_key.focus();
		alert('LG U+ mertkey는 필수항목입니다.');
		return false;
	}
	if(!p_quota.value && ret){
		p_quota.focus();
		alert('일반할부기간은 필수항목입니다.');
		return false;
	}
	if(!chkPgid()){
		alert('LG U+ ID가 올바르지 않습니다.');
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
		alert("정상적인 LG U+ ID입니다.\n개별 승인 신청이 필요 없습니다.\n창을 닫고 LG U+ ID를 입력하세요!");
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
		parameters: "mode=getPginfo&pgtype=lgdacom&todayshoppg=y&pgid="+pgid,
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
	var pattern = /^(go|fp|fd|gs)[a-zA-Z0-9_]+/;
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
LG U+PG 설정<span>신용카드 결제 및 기타결제방식은 반드시 전자결제서비스 업체와 계약을 맺으시기 바랍니다</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=8')"><img src="../img/btn_q.gif" border=0 align="absmiddle"></a>
</div>
<div id="dacom_banner"><script>panel('dacom_banner', 'pg');</script></div>
<form name="frmPGConfig" method="post" action="indb.config.pg.php" target="ifrmHidden" onsubmit="return chkFormThis(this)" />
<input type="hidden" name="mode" value="lgdacom">
<input type="hidden" name="cfg[settlePg]" value="lgdacom">

<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td>LG U+에서 제공하는 신용카드,계좌이체,가상계좌,핸드폰의 결제수단을 방문자(소비자)에게 제공하기 위해서</td></tr>
<tr><td>LG U+에서 <b>메일로 받으신 LG U+ ID와 mertkey를 입력</b>하신후 본 페이지 하단의 저장버튼을 클릭해 주세요.</td></tr>
<tr><td>아직 LG U+과 계약을 하지 않으셨다면</td></tr>
<tr><td style="padding-left:10px">①<u>온라인신청 하신후</u></td></tr>
<tr><td style="padding-left:10px">②<u>계약서류를 우편으로 LG U+에 보내</u>주세요 <a href="../basic/pg.intro.php" target="_blank" style="color:#ffffff;font-weight:bold">[계약 상세안내]</a></td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>
<div style="padding-top:15px"></div>
<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class="cellC"><col class="cellL">
<tr>
	<td>PG사</td>
	<td><b>LG U+ (XPay 1.0 - 결제창2.0) <?=$spot?></b></td>
</tr>
<tr>
	<td>결제수단 설정</td>
	<td class="noline">
	<label><input type="checkbox" name="set[use][c]" <?=$checked['c']?> onclick="chkSettleKind();"> 신용카드<label>
	<label><input type="checkbox" name="set[use][o]" <?=$checked['o']?> onclick="chkSettleKind();"> 계좌이체<label>
	<!--label><input type="checkbox" name="set[use][v]" <?=$checked['v']?> onclick="chkSettleKind();"> 가상계좌<label-->
	<label><input type="checkbox" name="set[use][h]" <?=$checked['h']?> onclick="chkSettleKind();"> 휴대폰<label>
	&nbsp;&nbsp;&nbsp;<font class="extext"><b>(반드시 LG U+PG사와 계약한 결제수단만 체크하세요)</b></font></td>
</tr>
<tr>
	<td class="ver8"><b>LG U+ ID</td>
	<td>
	<div style="float:left"><input type="text" name="pg[id]" class="lline" value="<?=$tsPG['pg'][id]?>" id="pgid"></div>
	<div style="float:left;padding:0 0 0 5" id="btPgId"><a href="javascript:openPrefix();"><img src="../img/pginfo.gif" alt="개별 승인 신청" /></a></div>
	<div style="clear:both" class="extext">LG U+ ID는 ‘go,fp,fd,gs’로 시작되는 아이디만 입력 가능합니다. (단, 기존 입력값은 무방합니다)</div>
	<div class="extext">고도몰 솔루션 이용자중 이전 버전을 사용하고 있어 위의 아이디로 시작하지 않는 경우에는 개별 승인 신청을 하셔야 합니다.</div>
	</td>
</tr>
<tr>
	<td class="ver8"><b>LG U+ mertkey</td>
	<td>
	<input type="text" name="pg[mertkey]" class="lline" value="<?=$tsPG['pg']['mertkey']?>">
	</td>
</tr>
<tr>
	<td>일반할부기간</td>
	<td>
	<input type="text" name="pg[quota]" value="<?=$tsPG['pg']['quota']?>" class="lline">
	<span class="extext">ex) <?=$_pg['quota']?></span>
	</td>
</tr>
<tr>
	<td>무이자 여부</td>
	<td class="noline">
	<input type="radio" name="pg[zerofee]" value="no" checked> 일반결제
	<input type="radio" name="pg[zerofee]" value="yes" <?=$checked['zerofee']['yes']?>> 무이자결제 <font class="extext"><b>(무이자결제는 반드시 PG사와 계약체결 후에 사용해야 합니다!)</b></font>
	</td>
</tr>
<tr>
	<td>무이자 기간</td>
	<td>
	<input type="text" name="pg[zerofee_period]" value="<?=$tsPG['pg']['zerofee_period']?>" class="lline" style="width:500px">
	<a href="javascript:popupLayer('../basic/popup.dacom.php',500,470)" style="color:#616161;" class="ver8"><img src="../img/btn_carddate.gif" align="absmiddle"></a>
	<div class="extext" style="padding-top:4px">오른쪽에 있는 '무이자기간코드생성' 버튼을 눌러 코드를 생성한후 복사하여 사용하세요</div>
	</td>
</tr>
<tr>
	<td>결제창 색상</td>
	<td class="noline">
	<input type="radio" name="pg[skin]" value="red" <?=$checked['skin']['red']?>> Red
	<input type="radio" name="pg[skin]" value="blue" <?=$checked['skin']['blue']?>> Blue
	<input type="radio" name="pg[skin]" value="cyan" <?=$checked['skin']['cyan']?>> Cyan
	<input type="radio" name="pg[skin]" value="green" <?=$checked['skin']['green']?>> Green
	<input type="radio" name="pg[skin]" value="yellow" <?=$checked['skin']['yellow']?>> Yellow
	</td>
</tr>
<input type="hidden" name="pg[serviceType]" value="service">
<!--<tr>
	<td>서비스 타입</td>
	<td class="noline">
	<input type="radio" name="pg[serviceType]" value="service" <?=$checked['serviceType']['service']?>> service
	<input type="radio" name="pg[serviceType]" value="test" <?=$checked['serviceType']['test']?>> test
	</td>
</tr>-->
</table>

<div style="padding-top:15px"></div>

<div id="MSG02">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle">PG사와 계약을 맺은 이후에는 메일로 받으신 실제 ID, Key를 넣으시면 됩니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">PG사의 결제정보 설정후 고객님께서 카드결제 테스트를 꼭 해보시기 바랍니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">간혹 PG사를 통해 카드승인된 값을 받지못하여 주문관리페이지에서 입금확인으로 자동변경되지 않을수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">반드시 주문관리페이지의 주문상태와 PG사에서 제공하는 관리자화면내의 카드승인내역도 동시에 확인해 주십시요.</font></td></tr>
</table>

<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td height="20"></td></tr>
<tr><td><font class="def1" color="white"><b>※ LG U+ PG사 계약후 설정시 유의사항 (필독!)</b></font></td></tr>
<tr><td height=8></td></tr>
<tr><td><font class="def1" color="white">- 이곳에서 LG U+ PG 설정시 유의사항 -</b></font></td></tr>
<tr><td>① 계약후 메일로 받은 'LG U+ ID' 와 'LG U+ mertkey'를 상단입력란에 정확하게 입력하세요.</td></tr>
<tr><td>② LG U+PG사와 계약한 후 반드시 계약정보와 일치하도록 위 상단의 '결제수단설정'을 해주셔야 합니다.</td></tr>
<tr><td>(즉, 신용카드, 계좌이체만 계약체결했다면 반드시 두가지만 체크해야 합니다. 만일 가상계좌까지 체크하면 결제에러가 발생됩니다)</td></tr>
<tr><td height=8></td></tr>
<tr><td><font class="def1" color="white">- LG U+PG사에서 제공하는 관리자모드 설정시 유의사항 -</b></font> <a href="javascript:popup('http://guide.godo.co.kr/guide/php/ex_lgdacom_pg.html',830,680)"><img src="../img/btn_dacompg_sample.gif" align="absmiddle"></a></td></tr>
<tr><td>① LG U+ 관리자모드에 가서 '승인결과전송여부'와 '서버OS타입'을 아래와 같이 수정하세요.</td></tr>
<tr><td>'승인결과전송여부' 설정은  '전송(결제창2.0)' 으로 설정하시고,	'서버OS타입'은  'LINUX계열'로 설정을 수정해 주시기 바랍니다.</td></tr>
<tr><td>② 위 사항을 모두 수정하고 1시간 후에 쇼핑몰에서 신용카드결제 테스트를 해보셔야 수정된 결과가 반영되어 정상적으로 결제가 이루어집니다.</td></tr>
</table>
</div>
<script>cssRound('MSG02')</script>

<div class="title">현금영수증 <!--span>설정된 PG사의 현금영수증을 사용하며, 별도 계약 필요없음</span--> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=8')"><img src="../img/btn_q.gif" border=0 align="absmiddle"></a></div>
<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class="cellC"><col class="cellL">
<tr>
	<td>현금영수증</td>
	<td class="noline">
	<input type="radio" name="pg[receipt]" value="N" <?=$checked['receipt']['N']?>> 사용안함
	<input type="radio" name="pg[receipt]" value="Y" <?=$checked['receipt']['Y']?>> 사용
	<BR><font class="extext" style="padding-left:5px">LG U+ 현금영수증 이용은 LG U+ 현금영수증 안내를 확인하시기 바랍니다. <a class="extext" style="font-weight:bold" href="http://ecredit.lgdacom.net/renewal/html/AddiService/addser03.htm" target="_blank">[바로가기]</a></font>
	</td>
</tr>
</table><p>

<div id="MSG03">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle">현금영수증</font>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">소비자는 2008. 7. 1일부터 현금영수증 발급대상금액이 5천원이상에서 1원이상으로 변경되어
5천원 미만의 현금거래도 현금영수증을 요청하여 발급 받을 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">현금영수증 사용 체크시 무통장, 계좌이체, 가상계좌 결제에 대해서 소비자가 신청한 현금영수증이 발급 됩니다</td></tr>
</table>
</div>
<script>cssRound('MSG03')</script>

<div class="button">
<input type=image src="../img/btn_save.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>

</form>

<script>chkSettleKind();</script>