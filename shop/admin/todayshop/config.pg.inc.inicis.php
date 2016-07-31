<?

### 이니시스 기본 세팅값
$_pg		= array(
			'zerofee'	=> 'no',
			'receipt'	=> 'N',
			'quota'		=> '선택:일시불:2개월:3개월:4개월:5개월:6개월:7개월:8개월:9개월:10개월:11개월:12개월',
			);

$location = "결제모듈연동 > 이니시스PG 설정";
include "../_header.popup.php";
include "../../conf/config.pay.php";

// 투데이샵 pg 설정값 불러오기
$todayShop = &load_class('todayshop', 'todayshop');
if (!$todayShop->auth()) {
	msg(' 서비스 신청안내는 고도몰 고객센터로 문의해주시기 바랍니다.', -1);
}
$tsPG = $todayShop->getPginfo();
unset($todayShop);

if ($tsPG['cfg']['settlePg'] != "inicis") $tsPG = array(); // 사용중이 아니라면 pg 정보 없앰

$tsPG['pg'] = @array_merge($_pg,$tsPG['pg']);

if($tsPG['cfg']['settlePg']!="inicis") $tsPG['pg'] = array(); //pg타입체크

if ($tsPG['cfg']['settlePg']=="inicis" && $tsPG['pg']['id']) $spot = "<b style='color:#ff0000;padding-left:10px'><img src=../img/btn_on_func.gif align=absmiddle></b>";
$checked['ssl'][$tsPG['pg']['ssl']] = $checked['zerofee'][$tsPG['pg']['zerofee']] = $checked['cert'][$tsPG['pg']['cert']] = $checked['bonus'][$tsPG['pg']['bonus']] = "checked";
$checked['receipt'][$tsPG['pg']['receipt']] = "checked";

if ($tsPG['set']['use'][c]) $checked[c] = "checked";
if ($tsPG['set']['use'][o]) $checked[o] = "checked";
if ($tsPG['set']['use'][v]) $checked[v] = "checked";
if ($tsPG['set']['use'][h]) $checked[h] = "checked";

if ($tsPG['cfg']['settlePg']=="inicis"){
	$dir = "../../todayshop/card/inicis/key/";

	if (is_dir($dir.$tsPG['pg']['id'])){
		$od = opendir($dir.$tsPG['pg']['id']);
		while ($rd=readdir($od)){
			if (!ereg("\.$",$rd)) $fls['pg'][] = $rd;
		}
		closedir($od);
	}
}

// php 5이상인 경우 inipay 유도 버튼
if (substr(phpversion(),0,1) >= 5) {
	$inipayBtn	= "<img src=\"../img/btn_inipayChange.gif\" border=\"0\" align=\"absmiddle\" class=\"hand\" alt=\"INIPay TX5 결제 모듈로 전환\" onclick=\"inipayChange();\">";
}
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
	var robj =  new Array('pg[id]');

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

	for(var i=0;i < arr.length;i++)
	{
		if (document.getElementsByName('set[use]['+arr[i]+']').length == 0) continue;
		sk = document.getElementsByName('set[use]['+arr[i]+']')[0].checked;
		if(sk == true)ret=true;
	}

	if(!p_id.value && ret){
		p_id.focus();
		alert('INIPay ID는 필수항목입니다.');
		return false;
	}

	if(!chkPgid()){
		alert('INIPay ID가 올바르지 않습니다.');
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
		alert("정상적인 INIPay ID입니다.\n개별 승인 신청이 필요 없습니다.\n창을 닫고 INIPay ID를 입력하세요!");
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
		parameters: "mode=getPginfo&pgtype=inicis&todayshoppg=y&pgid="+pgid,
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
	var pattern = /^(GODO|GDP|GOSO)/;
	if(pattern.test(obj.value)  || (oldId == obj.value && oldId)){
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

function inipayChange(){
	if(confirm('INIPay TX5 결제 모듈로 전환 하시겠습니까?\r\n전환을 하면 키화일은 복사가 됩니다.\r\n전환후 키화일 업로드 없이 나머저 설정 후 저장을 하시면 됩니다.') == true){
		var inicisKeyName	= document.getElementById('pgid').value;
		var ajax = new Ajax.Request( "./indb.config.pg.php",
		{
			method: "post",
			parameters: "mode=inipayKeyCopy&inicisKeyName="+inicisKeyName,
			onComplete: function ()
			{
				var req = ajax.transport;
				parent.chgifrm('config.pg.inc.inipay.php',3);
			}
		} );
	}
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
<div class="title title_top">이니시스PG 설정<!--span>신용카드 결제 및 기타결제방식은 반드시 전자결제서비스 업체와 계약을 맺으시기 바랍니다</span--> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=7')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a>
</div>
<div id="inicis_banner"><script>panel('inicis_banner', 'pg');</script></div>
<form name="frmPGConfig" method="post" action="indb.config.pg.php" target="ifrmHidden" enctype="multipart/form-data" onsubmit="return chkFormThis(this)" />
<input type=hidden name=mode value="inicis">
<input type=hidden name=cfg[settlePg] value="inicis">
<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td colspan="2">
이니시스에서 제공하는 신용카드,계좌이체,가상계좌,핸드폰의 결제수단을 방문자(소비자)에게 제공하기 위해서<BR>
이니시스에서 <b>메일로 받으신 압축파일을 풀어서 INIPay ID와 Key File 3개를 입력</b>하신후 본 페이지 하단의 저장버튼을 클릭해 주세요.<BR>
아직 이니시스와 계약을 하지 않으셨다면 ①<u>온라인신청 하신후</u> ②<u>계약서류를 우편으로 이니시스에 보내</u>주세요 <a href="../basic/pg.intro.php" target="_blank"><font color='#ffffff'><b>[계약 상세안내]<b/></font></a>
</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>
<div style="font:0;height:5"></div>
<table border=1 bordercolor=#e1e1e1 style="border-collapse:collapse" width=100%>
<col class=cellC><col class=cellL>
<tr>
	<td class=ver8><b>PG사</b></td>
	<td><b>이니시스 (INIPay V4.119) <?=$spot?></b> <?=$inipayBtn?></td>
</tr>
<tr>
	<td>결제수단 설정</td>
	<td class=noline>
	<label><input type=checkbox name=set[use][c] <?=$checked[c]?> onclick="chkSettleKind()"> 신용카드</label>
	<label><input type=checkbox name=set[use][o] <?=$checked[o]?> onclick="chkSettleKind()"> 계좌이체</label>
	<!--label><input type=checkbox name=set[use][v] <?=$checked[v]?> onclick="chkSettleKind()"> 가상계좌</label-->
	<label><input type=checkbox name=set[use][h] <?=$checked[h]?> onclick="chkSettleKind()"> 핸드폰</label>
	&nbsp;&nbsp;&nbsp;<font class=extext><b>(반드시 이니시스와 계약한 결제수단만 체크하세요)</b></font>
	</td>
</tr>
<tr>
	<td class=ver8><b>INIPay ID</b></td>
	<td>
	<div style="float:left"><input type="text" name="pg[id]" class="lline" value="<?=$tsPG['pg'][id]?>" onkeyup="chkPgid()" onblur="chkPgid()" id="pgid"></div>
	<div style="float:left;padding:0 0 0 5" id="btPgId"><a href="javascript:openPrefix();"><img src="../img/pginfo.gif" alt="개별 승인 신청" /></a></div>
	<div style="clear:both" class="extext">‘GODO,GDP,GOSO’로 시작되는 INIPay ID만 직접 입력 가능합니다. (단, 기존 입력값은 무방합니다)</div>
	<div class="extext">고도몰 솔루션 이용자중 이전 버전을 사용하고 있어 위의 아이디로 시작하지 않는 경우에는 개별 승인 신청을 하셔야 합니다.</div>
	</td>
</tr>
<? for ($i=1;$i<=3;$i++){ ?>
<tr>
	<td class=ver8><b>INIPay Key File #<?=$i?></b></td>
	<td class=ver8><input type=file name=pg[file_0<?=$i?>] class=lline> <?=$fls[pg][$i-1]?></td>
</tr>
<? } ?>
<tr>
	<td height=50>일반할부기간</td>
	<td>
	<input type=text name=pg[quota] value="<?=$tsPG['pg'][quota]?>" class=lline style="width:500px">
	<div class=extext  style="padding-top:5px">ex) <?=$_pg[quota]?></div>
	</td>
</tr>
<tr>
	<td>무이자 여부</td>
	<td class=noline>
	<input type=radio name=pg[zerofee] value="no" checked> 일반결제
	<input type=radio name=pg[zerofee] value="yes" <?=$checked[zerofee][yes]?>> 무이자결제 <font class=extext><b>(무이자결제는 반드시 PG사와 계약체결 후에 사용해야 합니다!)</b> (아래 '무이자 기간' 사용시 체크)</font></td>
</tr>
<tr>
	<td height=92>무이자 기간</td>
	<td><input type=text name=pg[zerofee_period] value="<?=$tsPG['pg'][zerofee_period]?>" class=lline style="width:500px">
	<div style="padding-top:7px"><font class=extext >* 카드사코드 :  01 (외환), 03 (롯데/(구)동양), 04 (현대), 06 (국민), 11 (BC), 12 (삼성), 13 (LG), 14 (신한)</div>
	<div style="padding-top:3px">ex) 비씨카드 3개월 / 6개월 할부와 삼성카드 3개월 무이자 적용시 ⇒ 11-3:6,12-3 라고 입력</div>
	<div style="padding-top:3px">ex) 모든카드에 대해서 3개월 / 6개월 무이자 적용시 ⇒ ALL-3:6 라고 입력</div>
	<div style="padding:3px 0 7px 0">* 무이자 기간을 사용하려면 반드시 위의 무이자결제를 체크하세요!</div></td>
</tr>
</table>
<div id=MSG02>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">PG사의 결제정보 설정후 고객님께서 카드결제 테스트를 꼭 해보시기 바랍니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">간혹 PG사를 통해 카드승인된 값을 받지못하여 주문관리페이지에서 입금확인으로 자동변경되지 않을수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">반드시 주문관리페이지의 주문상태와 PG사에서 제공하는 관리자화면내의 카드승인내역도 동시에 확인해 주십시요.</font></td></tr>
</table>
</div>
<script>cssRound('MSG02')</script>

<div class=title>현금영수증 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=7')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>
<table border=1 bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class=cellC><col class=cellL>
<tr>
	<td>현금영수증</td>
	<td class=noline>
	<input type=radio name=pg[receipt] value="N" <?=$checked[receipt][N]?>> 사용안함
	<input type=radio name=pg[receipt] value="Y" <?=$checked[receipt][Y]?>> 사용
	<BR><font class=extext style="padding-left:5px">이니시스 현금영수증 이용은 이니시스 현금영수증 안내를 확인하시기 바랍니다. <a class="extext" style="font-weight:bold" href="http://www.inicis.com/ini_22.jsp" target="_blank">[바로가기]</a></font>
	</td>
</tr>
</table><p>
</div>

<div id=MSG04>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">소비자는 2008.7.1일부터 현금영수증 발급대상금액이 5천원이상에서 1원이상으로 변경되어 5천원 미만의 현금거래도 현금영수증을 요청하여 발급 받을 수 있습니다.</td></tr>
</table>
</div>
<script>cssRound('MSG04')</script>


<div class=button>
<input type=image src="../img/btn_save.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>

</form>
<script>chkSettleKind();</script>