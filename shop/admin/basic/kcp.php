<?
$pg_name = 'kcp';
### KCP 기본 세팅값
$_pg		= array(
			'id'	=> '',
			'zerofee'	=> 'no',
			'receipt'	=> 'N',
			'quota'		=> '00,02,03,04,05,06,07,08,09,10,11,12',
			);
$_escrow	= array(
			'use'		=> 'N',
			'min'		=> 0,
			);

$location = "결제모듈연동 > KCP PG설정";
include "../_header.popup.php";
include "../../conf/config.pay.php";
if ($cfg[settlePg]=="kcp"){
	include "../../conf/pg.$cfg[settlePg].php";
	include "../../conf/pg.escrow.php";
}

$pg = @array_merge($_pg,$pg);
$escrow = @array_merge($_escrow,$escrow);

if ($cfg[settlePg]=="kcp") $spot = "<b style='color:#ff0000;padding-left:10px'>[사용중]</b>";
$checked[ssl][$pg[ssl]] = $checked[zerofee][$pg[zerofee]] = $checked[cert][$pg[cert]] = $checked[bonus][$pg[bonus]] = "checked";
$checked[escrow]['use'][$escrow['use']] = $checked[escrow][comp][$escrow[comp]] = $checked[escrow]['min'][$escrow['min']] = "checked";
$checked[receipt][$pg[receipt]] = "checked";

if($pg['pg-centersetting']=='Y'){ 
	$pgStatus = 'auto';
}
else{
	$pgStatus = 'menual';
}

$checked[displayEgg][$cfg[displayEgg]+0] = "checked";
?>
<script language=javascript>
var arr=new Array('c','v','o','h');
function chkSettleKind(){
	var f = document.forms[0];

	<?if($pgStatus == 'auto' || $pgStatus == 'disable'){?>
		return false;
	<?}?>

	var ret = false; var sk = false;
	for(var i=0;i < arr.length;i++)
	{
		sk = document.getElementsByName('set[use]['+arr[i]+']')[0].checked;
		if(sk == true)ret=true;
	}
	var robj =  new Array('pg[id]','pg[key]','pg[quota]');

	for(var i=0;i < robj.length;i++){
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
	<?if($pgStatus == 'menual'){?>
	for(var i=0;i < arr.length;i++)
	{
		sk = document.getElementsByName('set[use]['+arr[i]+']')[0].checked;
		if(sk == true)ret=true;
	}

	if(!p_id.value && ret){
		p_id.focus();
		alert('KCP PGID는 필수항목입니다.');
		return false;
	}
	if(!p_key.value && ret){
		p_key.focus();
		alert('KCP Key는 필수항목입니다.');
		return false;
	}
	<?}?>

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
}
</script>
<div class="title title_top">
KCP PG 설정<span>신용카드 결제 및 기타결제방식은 반드시 전자결제서비스 업체와 계약을 맺으시기 바랍니다</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=21')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a>
</div>
<div id="kcp_banner"><script>panel('kcp_banner', 'pg');</script></div>
<form method=post action="indb.pg.php" enctype="multipart/form-data" onsubmit="return chkFormThis(this)">
<input type=hidden name=mode value="kcp">
<input type=hidden name=cfg[settlePg] value="kcp">
<?if($pgStatus == 'menual') {?>
<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td>KCP에서 제공하는 신용카드,계좌이체,가상계좌,핸드폰의 결제수단을 방문자(소비자)에게 제공하기 위해서</td></tr>
<tr><td>KCP에서 <b>메일로 받으신 KCP PGID와 KCP Key를 입력</b>하신후 본 페이지 하단의 저장버튼을 클릭해 주세요.</td></tr>
<tr><td>아직 KCP와 계약을 하지 않으셨다면</td></tr>
<tr><td style="padding-left:10">①<u>온라인신청 하신 후</u></td></tr>
<tr><td style="padding-left:10">②<u>계약서류를 우편으로 KCP에 보내</u>주세요 <a href="../basic/pg.intro.php" target="_blank"><font color="#ffffff">[<u>계약 상세안내</u>]</font></a></td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>
<?}?>

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
	<?if($pgStatus == 'menual'){?>&nbsp;&nbsp;&nbsp;<span class="extext"><b>(반드시 KCP와 계약한 결제수단만 체크하세요)</b></span><?}?>
	
	</td>
</tr>
<tr>
	<td>KCP <font color="#627dce">PG&nbsp;ID</font></td>
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
	<input type=text name=pg[id] class=lline value="<?=$pg[id]?>">
	<?}?>
	</td>
</tr>
<tr>
	<td>KCP <font color="#627dce">KEY</font></td>
	<td>
	<?if($pgStatus!='menual'){?>
			<? if(($pg['key'])){ 
				echo "<b>".$pg['key']."</b>&nbsp;<span class='extext'><b>자동설정 완료</b></span><br/>";
			 }?>
			
		<?}
		else{?>	
	<input type=text name=pg[key] class=lline value="<?=$pg[key]?>">
	<?}?>
	</td>
</tr>
<tr>
	<td>할부기간</td>
	<td>
	<input type=text name=pg[quota] value="<?=$pg[quota]?>" class=lline>
	<div class=extext style="padding-top:4">결제자가 할부 결제시 선택한 할부개월 수 입니다. 00 부터 12 의 값을 가집니다.(예: 3개월의 경우 : 03 , 일시불의 경우 : 00) </div>
	<div class=extext style="padding-top:3">최대 할부개월수만을 입력해 주시기 바랍니다 (예: 6개월할부까지만을 적용시 06 만을 입력 => 일시불~6개월할부만이 노출) </div>
	</td>
</tr>
<tr>
	<td>무이자 여부</td>
	<td class=noline>
	<input type=radio name=pg[zerofee] value="no" checked> 일반결제
	<input type=radio name=pg[zerofee] value="yes" <?=$checked[zerofee][yes]?>> 무이자결제 (아래 기간 입력)
	<input type=radio name=pg[zerofee] value="admin" <?=$checked[zerofee][admin]?>> 무이자결제 (KCP 상점 관리자 모드에서 설정) <font class=extext><b>(무이자결제는 반드시 PG사와 계약체결 후에 사용해야 합니다!)</b></font></td>
</tr>
<tr>
	<td>무이자 기간</td>
	<td>
	<input type=text name=pg[zerofee_period] value="<?=$pg[zerofee_period]?>" class=lline style="width:500px">
	<a href="javascript:popupLayer('../basic/popup.kcp.php',500,470)" style="color:#616161;" class=ver8><img src="../img/btn_carddate.gif" align=absmiddle></a>
	<div class=extext  style="padding-top:4px">옆에 있는 '무이자기간코드생성' 버튼을 눌러 코드를 생성한후 복사하여 사용하세요</div>
	</td>
</tr>
</table>
</div>

<div style="padding-top:15px"></div>

<div id=MSG02>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<?if($pgStatus == 'menual') {?>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">PG사와 계약을 맺은 이후에는 메일로 받으신 실제 ID, Key를 넣으시면 됩니다.</td></tr>
<?}else{?>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">전자결제 서비스를 신청하면 e나무 솔루션에 PG ID가 자동으로 설정됩니다. 전자결제 신청 후 계약서류를 우편으로 KCP에 보내주세요. 
<a href="pg.intro.php" target="_blank" style="color:#ffffff;">[<u>계약 상세안내</u>]</a></td></tr>
<?}?>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">할부 , 무이자 등의 옵션은 쇼핑몰 정책에 따라 설정하여 사용하십시오.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">PG사의 결제정보 설정후 고객님께서 카드결제 테스트를 꼭 해보시기 바랍니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">간혹 PG사를 통해 카드승인된 값을 받지못하여 주문관리페이지에서 입금확인으로 자동변경되지 않을수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">반드시 주문관리페이지의 주문상태와 PG사에서 제공하는 관리자화면내의 카드승인내역도 동시에 확인해 주십시오.</font></td></tr>
</table>

<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td height="20"></td></tr>
<tr><td><font class="def1" color="white"><b>※ KCP PG사 계약후 설정시 유의사항 (필독!)</b></font></td></tr>
<tr><td height=8></td></tr>
<tr><td><font class=def1 color=white>- KCP PG사와 '가상계좌' 결제수단이 계약되어 있는 경우 -</font> <a href="javascript:popup('http://guide.godo.co.kr/guide/php/ex_kcp_pg_url.html',720,510)"><img src="../img/btn_dacompg_sample.gif" border=0 align=absmiddle></a></td></tr>
<tr><td>① 가상계좌 사용 시 자동입금통보를 쇼핑몰로 받기 위해서는 KCP관리자에서 공통 URL을 등록해 주셔야 합니다.<br>
<b>공통 URL은 "http://<?=$_SERVER['HTTP_HOST']?><?=$cfg['rootDir']?>/order/card/kcp/common_return.php" 입니다.</b>
</td></tr>
<tr><td height=8></td></tr>
<tr><td><font class=def1 color=white>- 무이자 여부 체크 시 무이자 결제(KCP 상점 관리자 모드에서 설정)로 선택하신 경우 -</font> <a href="javascript:popup('http://guide.godo.co.kr/guide/php/ex_kcp_pg.html',900,680)"><img src="../img/btn_dacompg_sample.gif" border=0 align=absmiddle></a></td></tr>
<tr><td>① KCP에서 배포하는 소스 정보를 관리자페이지 > 디자인 > 주문 > 카드 > kcp.htm 약 201 line 부분<br>
&lt;input type='text' name='kcp_noint' value=&quot;{? _pg.zerofee == 'yes'}Y{:}N{/}&quot;&gt; 소스를<br>
&lt;input type='text' name='kcp_noint' value='{_pg.zerofeeFl}'&gt; 로 변경해 주셔야 합니다.
</td></tr>
</table>
</div>
<script>cssRound('MSG02')</script>



<div class=title>에스크로 설정 <span>현금성 결제시 의무적으로 에스크로결제를 허용해야 합니다. 에스크로란?</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=21')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>
<input type=hidden name=escrow[comp] value="PG">	<!-- 에스크로 기관설정 -->
<div class=extext>아직 KCP 에스크로를 신청하지 않으셨다면 <a href="http://admin.kcp.co.kr" target="_blank" class=extext><b>KCP 상점관리자(http://admin.kcp.co.kr)에서 신청</b></a>해 주세요.</div>
<table border=1 bordercolor=#e1e1e1 style="border-collapse:collapse" width=100%>
<col class=cellC><col class=cellL>
<tr>
	<td>사용여부</td>
	<td class=noline>
	<input type=radio name=escrow[use] value="Y" <?=$checked[escrow]['use'][Y]?>> 사용
	<input type=radio name=escrow[use] value="N" <?=$checked[escrow]['use'][N]?>> 사용안함
	&nbsp;&nbsp;&nbsp;<font class=extext><b>(KCP 에스크로를 신청하셨다면 사용으로 체크하세요)</b></font>
	</td>
</tr>
<tr>
	<td>결제 수단</td>
	<td class=noline>
<?
		$methodEscrowList = array('c'=>'신용카드', 'o'=>'계좌이체', 'v'=>'가상계좌');
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
	<td>사용 금액</td>
	<td>
	<input type="text" name="escrow[min]" value="<?=$escrow[min]?>" class="lline" onkeydown="onlynumber()" style="width:100px;">
	<div class="extext"  style="padding-top:4px">PG사마다 에스크로 결제가 모든 금액에 적용이 안될수도 있으므로, 반드시 계약맺은 PG사의 에스크로 계약내용을 꼭 확인하세요.</div>
	</td>
</tr>
<tr>
	<td>구매 안전 표시<div style="padding-top:3"><a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=14')"><font class=extext_l>[표시이미지 보기]</font></a></div></td>
	<td class=noline>
	<input type=radio name=cfg[displayEgg] value=0 <?=$checked[displayEgg][0]?>> 메인하단과 결제수단 선택페이지에만 표시
	<input type=radio name=cfg[displayEgg] value=1 <?=$checked[displayEgg][1]?>> 전체페이지에 표시
	<input type=radio name=cfg[displayEgg] value=2 <?=$checked[displayEgg][2]?>> 표시하지 않음
	</td>
</tr>
</table>


<div style="padding-top:10"></div>

<table border=1 bordercolor=#e1e1e1 style="border-collapse:collapse" width=100%>
<tr><td>
<table cellpadding=15 cellspacing=0 border=0 bgcolor=white width=100%>
<tr><td>
<div style="padding:0 0 5 0">* 구매안전서비스 표기 적용방법 (에스크로 사용시 위에서 구매안전표시를 체크하고, 아래 표기방법에 따라 반영하세요)</font></div>
<table width=100% height=100 class=tb style='border:1px solid #cccccc;' bgcolor=white>
<tr>
<td width=30% style='border:1px solid #cccccc;padding-left:20'>① [메인페이지 하단] 표기방법</td>
<td align=center rowspan=2 style='border:1px solid #cccccc;padding:0 10 0 10'><a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=14')"><img src="../img/icon_sample.gif" align=absmiddle></a></td>
<td width=70% style='border:1px solid #cccccc;padding-left:40'><font class=extext><a href='../design/codi.php?design_file=outline/footer/standard.htm' target=_blank><font class=extext><b>[디자인관리 > 전체레이아웃 디자인 > 하단디자인 > html소스 직접수정]</b></font></a> 을 눌러<br> 치환코드 <font class=ver8 color=000000><b>{=displayEggBanner()}</b></font> 를 삽입하세요. <a href='../design/codi.php?design_file=outline/footer/standard.htm' target=_blank><font class=extext_l>[바로가기]</font></a></font></td>
</tr>
<tr>
<td width=30% style='border:1px solid #cccccc;padding-left:20'>② [결제수단 선택페이지] 표기방법</td>
<td width=70% style='border:1px solid #cccccc;padding-left:40'>
<a href='../design/codi.php?design_file=order/order.htm' target=_blank><font class=extext><font class=extext_l>[디자인관리 > 기타페이지 디자인 > 주문하기 > order.htm]</font></a> 을 눌러<br> 치환코드 <font class=ver8 color=000000><b>{=displayEggBanner(1)}</b></font> 를 삽입하세요. <a href='../design/codi.php?design_file=order/order.htm' target=_blank><font class=extext_l>[바로가기]</font></a></font></td>
</tr>
</table>
</td></tr>
</table>

<div style="padding-top:15"></div>

<table cellpadding=1 cellspacing=0 border=0 class=small_tip>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><font class=extext><b>구매안전서비스 가입 표기 의무화 안내 (2007년 9월 1일 시행)</b></font></td></tr>
<tr><td style="padding-top:4px">&nbsp;&nbsp;<font class=extext>① 표시·광고 또는 고지의 위치를 사이버몰 초기화면과 소비자의 결제수단 선택화면 두 곳으로 정함.</font></td></tr>
<tr><td style="padding-left:16"><font class=extext>- 사이버몰 초기화면 상법 제10조제1항의 사업자의 신원 등 표기사항 게재부분의 바로 좌측 또는 우측에 구매안전서비스 관련 사항을 표시하도록 함.</font></td></tr>
<tr><td style="padding-left:16"><font class=extext>- 소비자가 정확한 이해를 바탕으로 구매안전서비스 이용을 선택할 수 있도록, 결제수단 선택부분의 바로 위에 구매안전서비스 관련사항을 알기 쉽게 고지하여야  함.</font></td></tr>
<tr><td style="padding-top:4px">&nbsp;&nbsp;<font class=extext>② 표시·광고 또는 고지 사항으로 다음의 세 가지를 규정함.</font></td></tr>
<tr><td style="padding-left:16"><font class=extext>- 현금 등으로 5만원 이상 결제시 소비자가 구매안전서비스의 이용을 선택할 수 있다는 사항</font></td></tr>
<tr><td style="padding-left:16"><font class=extext>- 통신판매업자 자신이 가입한 구매안전서비스의 제공사업자명 또는 상호</font></td></tr>
<tr><td style="padding-left:16"><font class=extext>- 소비자가 구매안전서비스 가입사실의 진위를 확인 또는 조회할 수 있다는 사항</font></td></tr>
<tr><td height=10></td></tr>
</table>

<table cellpadding=1 cellspacing=0 border=0 class=small_tip>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><font class=extext><b>구매안전서비스 의무 적용 확대 (2013년 11월 29일 시행)</b></font></td></tr>
<tr><td style="padding-top:4px">&nbsp;&nbsp;<font class=extext>① 개정 내용</font></td></tr>
<tr><td style="padding-left:16"><font class=extext>5만원 이하 거래에 대해서도 소비자의 권익을 보호하기 위하여 구매안전서비스 의무 적용 대상 확대 <br/>1회 결제 기준, 5만원 이상 → 5만원 이하의 소액 거래(모든 금액)</font></td></tr>
<tr><td style="padding-top:4px">&nbsp;&nbsp;<font class=extext>② 관련 법률</font></td></tr>
<tr><td style="padding-left:16"><font class=extext>전자상거래 등에서의 소비자보호에 관한 법률 <br/>[ 법률 제11841호, 공포일: 2013.5.28, 일부 개정 ]</font></td></tr>
<tr><td height=10></td></tr>
</table>
</td></tr></table>


<div class=title>현금영수증 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=21')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>
<table border=1 bordercolor=#e1e1e1 style="border-collapse:collapse" width=100%>
<col class=cellC><col class=cellL>
<tr>
	<td>현금영수증</td>
	<td class=noline>
	<input type=radio name=pg[receipt] value="N" <?=$checked[receipt][N]?>> 사용안함
	<input type=radio name=pg[receipt] value="Y" <?=$checked[receipt][Y]?>> 사용
	<div class=extext style="padding-left:5px">아직 KCP현금영수증을 신청하지 않으셨다면 <a href="http://admin.kcp.co.kr" target="_blank" class=extext><b>KCP 상점관리자(http://admin.kcp.co.kr)에서 신청</b></a>해 주세요.</div>
	</td>
</tr>
</table><p>


<div id=MSG03>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">구매안전서비스(에스크로 또는 전자보증)는 전자상거래소비자보호법 및 시행령 개정에 따라 2011년 7월 29일부터 5만원 이상 현금성 결제시 의무 시행됩니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">에스크로 사용범위 및 사용금액에 대한것은 신청한 PG사나 은행에 따라 다를 수 있으므로 협의를 하셔야 합니다.</td></tr>
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
