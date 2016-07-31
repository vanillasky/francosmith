<?
$pg_name = 'allatbasic';

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
$_escrow	= array(
			'use'		=> 'N',
			'min'		=> 1,
			);

$location = "결제모듈연동 > 올앳PG 설정";
include "../_header.popup.php";
include "../../conf/config.pay.php";
if ($_GET['changePg'] == $pg_name){	//올앳->배이직 전환이면 카피된 올앳배이직 정보 보여줌.
	include "../../conf/pg.$pg_name.php";	
	include "../../conf/pg.escrow.php";
}
else if($cfg['settlePg'] == $pg_name){
	include "../../conf/pg.$cfg[settlePg].php";
	include "../../conf/pg.escrow.php";
}

$pg = @array_merge($_pg,$pg);
$escrow = @array_merge($_escrow,(array)$escrow);

if($cfg['settlePg']!= $pg_name && $_GET['changePg'] != $pg_name) $pg = array(); //pg타입체크

if ($cfg['settlePg']=="allatbasic") $spot = "<b style='color:#ff0000;padding-left:10px'><img src=../img/btn_on_func.gif align=absmiddle></b>";
$checked['ssl'][$pg['ssl']] = $checked['sell'][$pg['sell']] = $checked['zerofee'][$pg['zerofee']] = $checked['cert'][$pg['cert']] = $checked['bonus'][$pg['bonus']] = "checked";
$checked['escrow']['use'][$escrow['use']] = $checked['escrow']['comp'][$escrow['comp']] = $checked['escrow']['min'][$escrow['min']] = "checked";
$checked['receipt'][$pg['receipt']] = "checked";

if($pg['pg-centersetting']=='Y'){ 
	$pgStatus = 'auto';
}
else{
	$pgStatus = 'menual';
}

$checked['displayEgg'][$cfg['displayEgg']+0] = "checked";

// 프리픽스값
$prefix = 'GM|GP|GF';
?>
<script language=javascript>
var prefix = '<? echo $prefix;?>';
var arr=new Array('c','v','o','h');

function chkSettleKind(){
	var f = document.forms[0];

	var ret = false;
	for(var i=0;i < arr.length;i++)
	{
		var sk = document.getElementsByName('set[use]['+arr[i]+']')[0].checked;
		if(sk == true)ret=true;
	}
	var robj =  new Array('pg[id]','pg[crosskey]');

	<?if($pgStatus == 'auto' || $pgStatus == 'disable'){?>
		return false;
	<?}?>

	for(var i=0;i < robj.length;i++){
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
		sk = document.getElementsByName('set[use]['+arr[i]+']')[0].checked;
		if(sk == true)ret=true;
	}
	<?if($pgStatus == 'menual'){?>
	if(!p_id.value && ret){
		p_id.focus();
		alert('올앳 PGID는 필수항목입니다.');
		return false;
	}
	if(!p_crosskey.value && ret){
		p_crosskey.focus();
		alert('올앳 KEY는 필수항목입니다.');
		return false;
	}
	if(!chkPgid()){
		alert('올앳 PGID는 올바르지 않습니다.');
		return false;
	}
	<?}?>
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

var oldId = "<?php echo $pg['id'];?>";
function openPrefix(){
	if(chkPgid()){
		alert("정상적인 올앳 PGID 입니다.\n개별 승인 신청이 필요 없습니다.\n창을 닫고 올앳 PGID 를 입력하세요!");
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
		parameters: "mode=getPginfo&pgtype=allatbasic&pgid="+pgid,
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

function methodUpdate(){
	<?if ($pgStatus == 'disable'){?>
	alert('사용 중인 PG가 아닙니다.');
	return;
	<?}
	else{?>
	ifrmHidden.location.href = '../basic/pgSettingUpdate.php';
	<?}?>
}

window.onload = function(){
	resizeFrame();
	<?if($pgStatus == 'menual'){?>
		chkPgid();
	<?}?>
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
<form method="post" action="indb.pg.php" onsubmit="return chkFormThis(this)">
<input type="hidden" name="mode" value="allatbasic" />
<input type="hidden" name="cfg[settlePg]" value="allatbasic" />

<?if($pgStatus == 'menual') {?>
	<div id="MSG01">
	<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
	<tr><td>올앳에서 제공하는 신용카드,계좌이체,가상계좌,핸드폰의 결제수단을 방문자(소비자)에게 제공하기 위해서</td></tr>
	<tr><td>올앳에서 <b>메일로 받으신 올앳PGID와 올앳 KEY를 입력</b>하신후 본 페이지 하단의 저장버튼을 클릭해 주세요.</td></tr>
	<tr><td>아직 올앳와 계약을 하지 않으셨다면</td></tr>
	<tr><td style="padding-left:10">①<u>온라인신청 하신후</u></td></tr>
	<tr><td style="padding-left:10">②<u>계약서류를 우편으로 올앳에 보내</u>주세요 <a href="../basic/pg.intro.php" target="_blank" style="color:#ffffff;">[<u>계약 상세안내</u>]</a></td></tr>
	</table>
	</div>
	<script>cssRound('MSG01')</script>
<?}?>

<div style="padding-top:15"></div>

<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class="cellC"><col class="cellL">
<tr>
	<td>PG사</td>
	<td class="noline"><b>올앳 (All@Pay™ Basic) <?=$spot?></b></td>
</tr>
<tr>
	<td>결제수단 설정</td>
	<td class="noline">
	<? 
	$methodList = array('c'=>'신용카드', 'o'=>'계좌이체', 'v'=>'가상계좌', 'h'=>'휴대폰 결제');
		foreach($methodList as $key=>$val) {
			unset($disabled[$key]);
			unset($labelColor[$key]);
			unset($checked[$key]);
			if ($set['use'][$key] == 'on') $checked[$key] = 'checked';
	
			if ($set['use_ck'][$key]!='on'){
				$disabled[$key] = 'disabled';	
				$labelColor[$key] = "style='color:#cccccc'";
			}

			
			if($pgStatus != 'auto') {
				unset($disabled);
				unset($labelColor);
			}
			echo "<label ".$labelColor[$key]."><input type='checkbox' name='set[use][".$key."]' ".$checked[$key]." ".$disabled[$key]." onclick='chkSettleKind()' /> ".$val."</label>";
		}
	?>
	<?if($pgStatus != 'menual'){?>
	<button class="default-btn" type="button" style="padding-top:5px" onclick="methodUpdate()">결제수단 새로고침</button>
	<br/><span class="extext">계약한 결제수단 중에서 선택하여 사용할 수 있습니다. 결제수단을 추가하려면 PG사 고객센터로 신청하십시오.</span>
	<?}?>
	<?if($pgStatus == 'menual'){?>&nbsp;&nbsp;&nbsp;<span class="extext"><b>(반드시 올앳과 계약한 결제수단만 체크하세요)</b></span><?}?>
	</td>
</tr>

<tr>
	<td>올앳 <font color="#627dce">PG&nbsp;ID</font></td>
	<td>
		<?
		if($pgStatus == 'auto'){?>
			<div style="float:left"><b><?=$pg['id']?></b> <span class="extext"><b>자동설정 완료</b></span>
			</div>
		<?}
		else if($pgStatus == 'disable'){?>
			<span class="extext"><b>서비스를 신청하면  자동설정됩니다.</b></span>
		<?}
		else{?>
			
	<div style="float:left"><input type="text" name="pg[id]" class="lline" value="<?=$pg['id']?>" onkeyup="chkPgid()" onblur="chkPgid()" id="pgid" /></div>
	<div style="float:left;padding:0 0 0 5" id="btPgId"><a href="javascript:openPrefix();"><img src="../img/pginfo.gif" alt="개별 승인 신청" /></a></div>
	<div style="clear:both" class="extext"><? echo str_replace('|',',', $prefix);?>로 시작되는 올앳 PGID만 직접 입력 가능합니다. (단, 기존 입력값은 무방합니다)</div>
	<div class="extext">고도몰 솔루션 이용자중 이전 버전을 사용하고 있어 위의 아이디로 시작하지 않는 경우에는 개별 승인 신청을 하셔야 합니다.</div>
	<?}?>
	</td>
</tr>
<tr >
	<td>올앳 <font color="#627dce">KEY</font></td>
	<td>
		<?if($pgStatus!='menual'){?>
			<? if(($pg['crosskey'])){ 
				echo "<b>".$pg['crosskey']."</b>&nbsp;<span class='extext'><b>자동설정 완료</b></span><br/>";
			 }?>
			
		<?}
		else{?>	
		<input type="text" name="pg[crosskey]" class="lline" value="<?=$pg['crosskey']?>" /> <font class="extext">CrossKey를 넣으세요</font>
		<?}?>
	</td>
</tr>
<?
$pg_ssl = $sitelink->old_get_type();
?>
<input type="hidden" name="pg[ssl]" value="<?=$pg_ssl?>" />
<!--tr>
	<td>일반할부기간</td>
	<td>
	<input type=text name=pg[quota] value="<?=$pg[quota]?>" class=lline>
	<span class=extext>ex) <?=$_pg[quota]?></span>
	</td>
</tr-->
<tr>
	<td>할부 사용 여부</td>
	<td class="noline">
	<input type="radio" name="pg[sell]" value="Y" <?=$checked['sell']['Y']?> /> 할부사용
	<input type="radio" name="pg[sell]" value="N" <?=$checked['sell']['N']?> /> 할부사용안함	
	</td>
</tr>
<tr>
	<td>무이자 여부</td>
	<td class="noline">
	<input type="radio" name="pg[zerofee]" value="N" <?=$checked['zerofee']['N']?> /> 일반결제
	<input type="radio" name="pg[zerofee]" value="Y" <?=$checked['zerofee']['Y']?> /> 무이자결제 <font class="extext"><b>(무이자결제는 반드시 PG사와 계약체결 후에 사용해야 합니다!)</b></font></td>
</tr>
<tr>
	<td>카드결제 인증 여부</td>
	<td class="noline">
	<input type="radio" name="pg[cert]" value="Y" <?=$checked['cert']['Y']?> /> 사용함
	<input type="radio" name="pg[cert]" value="N" <?=$checked['cert']['N']?> /> 사용안함
	</td>
</tr>
<!--tr>
	<td>보너스 포인트</td>
	<td class=noline>
	<input type=radio name=pg[bonus] value="Y" <?=$checked[bonus][Y]?>> 사용
	<input type=radio name=pg[bonus] value="N" <?=$checked[bonus][N]?>> 사용안함
	</td>
</tr-->
<!--tr>
	<td>상점관리자모드</td>
	<td class=noline>
	<span><a href="https://www.allatpay.com/servlet/AllatBiz/login/login.jsp" target="_blank"><?php echo $pgNm;?> 상점 관리자모드 바로가기</a></span>
	<span><a href="http://www.allatpay.com/" target="_blank"><?php echo $pgNm;?> 사이트 바로가기</a></span>
	</td>
</tr-->
</table>
<div style="padding-top:5"></div>

<div id="MSG02">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
	<?if($pgStatus == 'menual') {?>
	<tr><td><img src="../img/icon_list.gif" align="absmiddle">PG사와 계약을 맺은 이후에는 메일로 받으신 실제 ID, Key를 넣으시면 됩니다..</td></tr>
	<?}else{?>
	<tr><td><img src="../img/icon_list.gif" align="absmiddle">전자결제 서비스를 신청하면 e나무 솔루션에 PG ID가 자동으로 설정됩니다. 전자결제 신청 후 계약서류를 우편으로 올앳에 보내주세요. <a href="pg.intro.php" target="_blank" style="color:#ffffff;">[<u>계약 상세안내</u>]</a>
	<?}?>
	<tr><td><img src="../img/icon_list.gif" align="absmiddle">할부, 무이자등의 옵션은 쇼핑몰 정책에 따라 설정하여 사용하십시오.</td></tr>
	<tr><td><img src="../img/icon_list.gif" align="absmiddle">PG사의 결제정보 설정후 고객님께서 카드결제 테스트를 꼭 해보시기 바랍니다. 간혹 PG사를 통해 카드승인된 값을 받지못하여 주문관리페이지에서 입금확인으로 자동변경되지 않을수 있습니다.<br/>반드시 주문관리페이지의 주문상태와 PG사에서 제공하는 관리자화면내의 카드승인내역도 동시에 확인해 주십시오.</td></tr>
	<tr><td class="pdv10"><strong> * 올앳 가상계좌 자동입금확인 설정</strong> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=6')"><img src="../img/btn_q.gif" border="0" align="absmiddle" /></a></td></tr>
	<tr><td>올앳과 ‘가상계좌’ 결제수단이 계약되어 있는 상점이라면 ‘가상계좌입금 확인 설정’을 통해 편리하게 입금내역을 확인하실 수 있습니다.</td></tr>
	<tr><td style="padding-bottom:7px;">‘가상계좌 입금확인 설정’이란 고객이 가상계좌로 입금을 하게 되어 승인이 된 경우 입금내역승인결과값을 e나무 관리자 페이지로 보내어 해당주문건에 대하여 자동으로 ‘입금확인’ 처리가 되도록 할 수 있는 것입니다.</td></tr>
	<tr><td>가상계좌 입금확인은 올앳 관리자에서 확인할 수 있지만 e나무 관리자에서 입금확인 정보를 자동으로 전송받으면 운영 시 편리하므로 연결 설정을  지원합니다.</td></tr>
	<tr><td style="padding-bottom:7px;">자세한 방법은 매뉴얼을 보고 따라해 주세요.</td></tr>
	<tr><td>① 올앳의 ‘가상계좌’ 결제수단을 사용하는 쇼핑몰에게만 제공되므로 먼저 올앳에 가상계좌 신청이 되어 있는지 확인하시기 바랍니다.</td></tr>
	<tr><td>② 올앳 관리자 로그인한 후, 마이페이지 >URL 전송  메뉴에서 상점ID(PG ID)를 선택, [가상계좌 입금확인 NOTI URL 신청]을 클릭하여 연결될 페이지 주소를 입력하고 저장합니다.</td></tr>
	<tr><td class="small_ex_padding extext" style="font-weight: bold; color: #0174DF;">올앳 관리자에 입력할 주소 : http://쇼핑몰도메인/shop/order/card/allatbasic/allat_notiurl.php</td></tr>
	<tr><td>③ 모바일샵용 PG ID(상점ID)를 추가로 사용하는 경우,  해당ID를 선택하고 주소를 입력하십시오(③번 등록방식과 동일)</td></tr>
	<tr><td class="small_ex_padding extext" style="font-weight: bold; color: #0174DF;">모바일샵용 PG ID 추가사용 시 입력할 주소 :  http://쇼핑몰도메인/shop/order/card/allatbasic/mobile/allat_mobile_notiurl.php</td></tr>
	<tr><td>④ 설정을 마치고 쇼핑몰 관리자 주문 페이지에서 자동 입금확인을 테스트해 보시기 바랍니다.</td></tr>
	<tr><td class="small_ex_padding">테스트 방법은 가상계좌로 주문을 한 후 해당 계좌로 입금을 한 뒤에 올앳 관리자 페이지에서의 입금여부와 e나무 관리자 페이지에서의 주문처리 상태가 ‘입금확인’으로 변경되었는지 확인하면 됩니다.</td></tr>
</table>
</div>
<script>cssRound('MSG02')</script>

<div class=title>에스크로 설정 <span>현금성 결제시 의무적으로 에스크로결제를 허용해야 합니다. 에스크로란?</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=6')"><img src="../img/btn_q.gif" border="0" align="absmiddle" /></a></div>
<input type="hidden" name="escrow[comp]" value="PG" />	<!-- 에스크로 기관설정 -->
<input type="hidden" name="escrow[min]" value="<?=$escrow[min]?>">

<div class="extext">아직 올앳 에스크로를 신청하지 않으셨다면 올앳 에스크로 특약서를 올앳으로 우편발송해 주세요.</div>


<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class="cellC"><col class="cellL">
<tr>
	<td>사용여부</td>
	<td class="noline">
	<input type="radio" name="escrow[use]" value="Y" <?=$checked['escrow']['use']['Y']?> /> 사용
	<input type="radio" name="escrow[use]" value="N" <?=$checked['escrow']['use']['N']?> /> 사용안함
	&nbsp;&nbsp;&nbsp;<font class=extext><b>(올앳 에스크로를 신청하셨다면 사용으로 체크하세요)</b></font>
	</td>
</tr>
<tr>
	<td>결제 수단</td>
	<td class="noline">
<?
		$methodEscrowList = array('o'=>'계좌이체', 'v'=>'가상계좌');
		foreach($methodEscrowList as $key=>$val) {
			unset($disabled[$key]);
			unset($labelColor[$key]);
			unset($checked[$key]);
			if ($escrow[$key] == 'on') $checked[$key] = 'checked';
			if ($escrow[$key.'_ck']!='on'){
				$disabled[$key] = 'disabled';	
				$labelColor[$key] = "style='color:#cccccc'";
			}

			if($pgStatus != 'auto') {
				unset($disabled);
				unset($labelColor);
			}
			echo "<label ".$labelColor[$key]."><input type='checkbox' name='escrow[".$key."]' ".$checked[$key]." ".$disabled[$key]."   /> ".$val."</label>";
		}
	?>
	</td>
</tr>
<tr>
	<td>구매 안전 표시<div style="padding-top:3"><a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=9')"><font class="extext_l">[표시이미지 보기]</font></a></div></td>
	<td class="noline">
	<input type="radio" name="cfg[displayEgg]" value="0" <?=$checked['displayEgg']['0']?> /> 메인하단과 결제수단 선택페이지에만 표시
	<input type="radio" name="cfg[displayEgg]" value="1" <?=$checked['displayEgg']['1']?> /> 전체페이지에 표시
	<input type="radio" name="cfg[displayEgg]" value="2" <?=$checked['displayEgg']['2']?> /> 표시하지 않음
	</td>
</tr>
</table>

<div style="padding-top:10"></div>

<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<tr><td>
<table cellpadding="15" cellspacing="0" border="0" bgcolor="white" width="100%">
<tr><td>
<div style="padding:0 0 5 0">* 구매안전서비스 표기 적용방법 (에스크로 사용시 위에서 구매안전표시를 체크하고, 아래 표기방법에 따라 반영하세요)</font></div>
<table width="100%" height="100" class="tb" style="border:1px solid #cccccc;" bgcolor="white">
<tr>
<td width="30%" style="border:1px solid #cccccc;padding-left:20">① [메인페이지 하단] 표기방법</td>
<td align="center" rowspan="2" style="border:1px solid #cccccc;padding:0 10 0 10"><a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=10')"><img src="../img/icon_sample.gif" align="absmiddle"></a></td>
<td width="70%" style="border:1px solid #cccccc;padding-left:40"><font class="extext"><a href="../design/codi.php?design_file=outline/footer/standard.htm" target="_blank"><font class="extext"><b>[디자인관리 > 전체레이아웃 디자인 > 하단디자인 > html소스 직접수정]</b></font></a> 을 눌러<br> 치환코드 <font class="ver8" color="000000"><b>{=displayEggBanner()}</b></font> 를 삽입하세요. <a href="../design/codi.php?design_file=outline/footer/standard.htm" target="_blank"><font class="extext_l">[바로가기]</font></a></font></td>
</tr>
<tr>
<td width="30%" style="border:1px solid #cccccc;padding-left:20">② [결제수단 선택페이지] 표기방법</td>
<td width="70%" style="border:1px solid #cccccc;padding-left:40">
<a href="../design/codi.php?design_file=order/order.htm" target="_blank"><font class="extext"><font class="extext_l">[디자인관리 > 기타페이지 디자인 > 주문하기 > order.htm]</font></a> 을 눌러<br> 치환코드 <font class=ver8 color=000000><b>{=displayEggBanner(1)}</b></font> 를 삽입하세요. <a href="../design/codi.php?design_file=order/order.htm" target="_blank"><font class="extext_l">[바로가기]</font></a></font></td>
</tr>
</table>
</td></tr>
</table>

<div style="padding-top:15"></div>

<table cellpadding="1" cellspacing="0" border="0" class="small_tip">
<tr><td><img src="../img/icon_list.gif" align="absmiddle" /><font class="extext"><b>구매안전서비스 가입 표기 의무화 안내 (2007년 9월 1일 시행)</b></font></td></tr>
<tr><td style="padding-top:4px">&nbsp;&nbsp;<font class="extext">① 표시·광고 또는 고지의 위치를 사이버몰 초기화면과 소비자의 결제수단 선택화면 두 곳으로 정함.</font></td></tr>
<tr><td style="padding-left:16"><font class="extext">- 사이버몰 초기화면 상법 제10조제1항의 사업자의 신원 등 표기사항 게재부분의 바로 좌측 또는 우측에 구매안전서비스 관련 사항을 표시하도록 함.</font></td></tr>
<tr><td style="padding-left:16"><font class="extext">- 소비자가 정확한 이해를 바탕으로 구매안전서비스 이용을 선택할 수 있도록, 결제수단 선택부분의 바로 위에 구매안전서비스 관련사항을 알기 쉽게 고지하여야  함.</font></td></tr>
<tr><td style="padding-top:4px">&nbsp;&nbsp;<font class="extext">② 표시·광고 또는 고지 사항으로 다음의 세 가지를 규정함.</font></td></tr>
<tr><td style="padding-left:16"><font class="extext">- 현금 등으로 결제시 소비자가 구매안전서비스의 이용을 선택할 수 있다는 사항</font></td></tr>
<tr><td style="padding-left:16"><font class="extext">- 통신판매업자 자신이 가입한 구매안전서비스의 제공사업자명 또는 상호</font></td></tr>
<tr><td style="padding-left:16"><font class="extext">- 소비자가 구매안전서비스 가입사실의 진위를 확인 또는 조회할 수 있다는 사항</font></td></tr>
<tr><td height="10"></td></tr>
</table>

<table cellpadding="1" cellspacing="0" border="0" class="small_tip">
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><font class=extext><b>구매안전서비스 의무 적용 확대 (2013년 11월 29일 시행)</b></font></td></tr>
<tr><td style="padding-top:4px">&nbsp;&nbsp;<font class=extext>① 개정 내용</font></td></tr>
<tr><td style="padding-left:16"><font class=extext>5만원 이하 거래에 대해서도 소비자의 권익을 보호하기 위하여 구매안전서비스 의무 적용 대상 확대 <br/>1회 결제 기준, 5만원 이상 → 5만원 이하의 소액 거래(모든 금액)</font></td></tr>
<tr><td style="padding-top:4px">&nbsp;&nbsp;<font class=extext>② 관련 법률</font></td></tr>
<tr><td style="padding-left:16"><font class=extext>전자상거래 등에서의 소비자보호에 관한 법률 <br/>[ 법률 제11841호, 공포일: 2013.5.28, 일부 개정 ]</font></td></tr>
<tr><td height="10"></td></tr>
</table>
</td></tr></table>

<div class=title>현금영수증 <!--span>설정된 PG사의 현금영수증을 사용하며, 별도 계약을 해야 함</span--> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=6')"><img src="../img/btn_q.gif" border="0" align="absmiddle" /></a></div>
<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class="cellC"><col class="cellL">
<tr>
	<td>현금영수증</td>
	<td class="noline">
	<input type="radio" name="pg[receipt]" value="N" <?=$checked['receipt']['N']?> /> 사용안함
	<input type="radio" name="pg[receipt]" value="Y" <?=$checked['receipt']['Y']?> /> 사용
	<BR><font class="extext" style="padding-left:5px">삼성올앳 현금영수증 이용은 삼성올앳 현금영수증 안내를 확인하시기 바랍니다. <a class="extext" style="font-weight:bold" href="http://www.allatpay.com/servlet/AllatBiz/svcinfo/si_receipt_apply.jsp?menu_id=idS16" target="_blank">[바로가기]</a></font>
	</td>
</tr>
</table><p>

<div id="MSG03">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />에스크로</font>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">구매안전서비스(에스크로 또는 전자보증)는 전자상거래 등에서의 소비자보호에 관한 법률 [ 법률 제11841호, 공포일: 2013.5.28, 일부 개정 ] 에 따라 
<br> &nbsp;&nbsp; 2013년 11월 29일 부터 ‘5만원 이상의 결제금액’ 에서 ‘모든 결제금액’으로 의무 적용이 확대 됩니다.</td></tr>

<tr><td height="8"></td></tr>

<tr><td><img src="../img/icon_list.gif" align="absmiddle" />현금영수증</font>
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />소비자는 2008. 7. 1일부터 현금영수증 발급대상금액이 5천원이상에서 1원이상으로 변경되어
5천원 미만의 현금거래도 현금영수증을 요청하여 발급 받을 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />현금영수증 사용 체크시 무통장, 계좌이체, 가상계좌 결제에 대해서 소비자가 신청한 현금영수증이 발급 됩니다</td></tr>
</table>
</div>
<script>cssRound('MSG03')</script>



<div class="button">
<input type="image" src="../img/btn_save.gif" />
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif" /></a>
</div>

</form>
<script>chkSettleKind();</script>