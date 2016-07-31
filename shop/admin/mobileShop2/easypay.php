<?
$pg_name = 'easypay';
### KCP 기본 세팅값
$_pg		= array(
			'id'	=> '',
			'receipt'	=> 'N',
			);
$_escrow	= array(
			'use_mobile'		=> 'N',
			'min'		=> 50000,
			);

$location = "결제모듈연동 > 이지페이 PG설정";
include "../_header.popup.php";
include "../../conf/config.pay.php";
if ($cfg[settlePg]=="easypay"){
	@include "../../conf/pg.$cfg[settlePg].php";
	@include "../../conf/pg_mobile.$cfg[settlePg].php";
	@include "../../conf/pg.escrow.php";
}

$pg = @array_merge($_pg,$pg);
$escrow = @array_merge($_escrow,$escrow);
if ($cfg[settlePg]=="easypay") $spot = "<b style='color:#ff0000;padding-left:10px'>[사용중]</b>";
$checked[ssl][$pg[ssl]] = $checked[zerofee][$pg_mobile[zerofee]] = $checked[cert][$pg[cert]] = $checked[bonus][$pg[bonus]] = "checked";
$checked[escrow]['use_mobile'][$escrow['use_mobile']] = $checked[escrow][comp][$escrow[comp]] = $checked[escrow]['min'][$escrow['min']] = "checked";
$checked[receipt][$pg[receipt]] = "checked";

if($cfg['settlePg'] != $pg_name){
	$pgStatus = 'menual';
}
else if($pg['pg-centersetting']=='Y'){ 
	$pgStatus = 'auto';
}
else{
	$pgStatus = 'menual';
}
$checked[displayEgg][$cfg[displayEgg]+0] = "checked";
?>
<script language=javascript>
var arr=new Array('c','v','h');
function chkSettleKind(){
	var f = document.forms[0];

	<?if($pgStatus == 'auto' || $pgStatus == 'disable'){?>
		return false;
	<?}?>

	var ret = false; var sk = false;
	for(var i=0;i < arr.length;i++)
	{
		sk = document.getElementsByName('set[use_mobile]['+arr[i]+']')[0].checked;
		if(sk == true)ret=true;
	}
	var robj =  new Array('pg[id]');
}
function chkFormThis(f){

	var ret = false;
	var sk = false;
	var p_id = document.getElementsByName('pg[id]')[0];
	<?if($pgStatus == 'menual'){?>
	for(var i=0;i < arr.length;i++)
	{
		sk = document.getElementsByName('set[use_mobile]['+arr[i]+']')[0].checked;
		if(sk == true)ret=true;
	}

	if(!p_id.value && ret){
		p_id.focus();
		alert('이지페이 ID는 필수항목입니다.');
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
	resizeFrame()
}
</script>
<div class="title title_top">
이지페이 PG 설정<span>신용카드 결제 및 기타결제방식은 반드시 전자결제서비스 업체와 계약을 맺으시기 바랍니다</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=29')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a>
</div>
<div id="easypay_banner"><script>panel('easypay_banner', 'pg');</script></div>
<form method=post action="indb.pg.php" enctype="multipart/form-data" onsubmit="return chkFormThis(this)">
<input type=hidden name=mode value="setPg">
<input type=hidden name=cfg[settlePg] value="easypay">
<?if($pgStatus == 'menual') {?>
<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td>easypay에서 제공하는 신용카드,계좌이체,가상계좌,핸드폰의 결제수단을 방문자(소비자)에게 제공하기 위해서</td></tr>
<tr><td>KCP에서 <b>메일로 받으신 ID를 입력</b>하신후 본 페이지 하단의 저장버튼을 클릭해 주세요.</td></tr>
<!--<tr><td>아직 KCP와 계약을 하지 않으셨다면</td></tr>
<tr><td style="padding-left:10">①<u>온라인신청 하신 후</u></td></tr>
<tr><td style="padding-left:10">②<u>계약서류를 우편으로 KCP에 보내</u>주세요 <a href="../basic/pg.intro.php" target="_blank"><font color="#ffffff"><b>[계약 상세안내]</b></font></a></td></tr>//-->
</table>
</div>
<script>cssRound('MSG01')</script>
<?}?>
<div style="padding-top:15px"></div>
<table border=1 bordercolor=#e1e1e1 style="border-collapse:collapse" width=100%>
<col class=cellC><col class=cellL>
<tr>
	<td>PG사</td>
	<td><b>이지페이 (Easypay V7.0 스마트폰) <?=$spot?></b></td>
</tr>
<tr>
	<td>결제수단 설정</td>
	<td class=noline>
	<? 
	$mobileMethodList = array('c'=>'신용카드', 'v'=>'가상계좌', 'h'=>'휴대폰 결제');
		foreach($mobileMethodList as $key=>$val) {
			unset($disabled[$key]);
			unset($labelColor[$key]);
			unset($checked[$key]);
			if ($set['use_mobile'][$key] == 'on') $checked[$key] = 'checked';
	
			if ($set['use_mobile_ck'][$key]!='on'){
				$disabled[$key] = 'disabled';	
				$labelColor[$key] = "style='color:#cccccc'";
			}

			if($pgStatus != 'auto') {
				unset($disabled);
				unset($labelColor);
			}
			echo "<label ".$labelColor[$key]."><input type='checkbox' name='set[use_mobile][".$key."]' ".$checked[$key]." ".$disabled[$key]." onclick='chkSettleKind()' /> ".$val."</label>";
		}
	?>
	<?if($pgStatus != 'menual'){?>
	<button class="default-btn" type="button" style="padding-top:5px" onclick="methodUpdate()">결제수단 새로고침</button>
	<br/><span class="extext">계약한 결제수단 중에서 선택하여 사용할 수 있습니다. 결제수단을 추가하려면 PG사 고객센터로 신청하십시오.</span>
	<?}?>

	<?if($pgStatus == 'menual'){?>&nbsp;&nbsp;&nbsp;<font class=extext><b>(반드시 이지페이와 계약한 결제수단만 체크하세요)</b></font><?}?>
	</td>
</tr>
<tr>
	<td class=ver8><b>이지페이<font color="#627dce">PG&nbsp;ID</font></td>
	<td>
	<?if($pgStatus == 'auto'){?>
		<div style="float:left"><b><?=$pg['id']?></b> <span class="extext"><b>자동설정 완료</b></span>
		</div>
	<?}
	else if($pgStatus == 'disable'){?>
		<span class="extext"><b>서비스를 신청하면  자동설정됩니다.</b></span>
	<?}
	else{?>
	<input type=text name="pg[id]" class="lline" value="<?=$pg[id]?>"  disabled="disabled">
	<?}?>
	</td>
</tr>

<tr>
	<td>할부기간</td>
	<td>
	<input type=text name=pg[quota] value="<?=$pg[quota]?>" class=lline disabled="disabled" >
	<div class=extext style="padding-top:4">결제자가 할부 결제시 선택한 할부개월 수 입니다. 00 부터 12 의 값을 가집니다.ex) 00:02:03:04:05:06:07:08:09:10:11:12   </div>
	</td>
</tr>
<tr>
	<td>무이자 여부</td>
	<td class=noline>
	<input type=radio name=pg[zerofee] value="no" checked disabled="disabled" disabled="disabled"> 일반결제
	<input type=radio name=pg[zerofee] value="yes" <?=$checked[zerofee][yes]?> disabled="disabled"> 무이자결제 (아래 기간 입력)
	 <font class=extext><b>(무이자결제는 반드시 PG사와 계약체결 후에 사용해야 합니다!)</b></font></td>
</tr>
<tr>
	<td>무이자 기간</td>
	<td>
	<input type=text name=pg[zerofee_period] value="<?=$pg[zerofee_period]?>" class=lline style="width:500px" disabled="disabled">
	<a href="javascript:popupLayer('../basic/popup.easypay.php',500,470)" style="color:#616161;" class=ver8><img src="../img/btn_carddate.gif" align=absmiddle></a>
	 
	</td>
</tr>
</table>
</div>

<div style="padding-top:15px"></div>

<div id=MSG02>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<?if($pgStatus == 'menual') {?>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">PG사와 계약을 맺은 이후에는 메일로 받으신 실제 ID를 넣으시면 됩니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">PG사의 결제정보 설정후 고객님께서 카드결제 테스트를 꼭 해보시기 바랍니다.</td></tr>
<?}else{?>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">전자결제 서비스를 신청하면 e나무 솔루션에 PG ID가 자동으로 설정됩니다.</td></tr>
<?}?>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">간혹 PG사를 통해 카드승인된 값을 받지못하여 주문관리페이지에서 입금확인으로 자동변경되지 않을수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">반드시 주문관리페이지의 주문상태와 PG사에서 제공하는 관리자화면내의 카드승인내역도 동시에 확인해 주십시오.</font></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">가상계좌 사용시 입금 통보를 쇼핑몰로 받기 위해서는 이지페이관리자에서 공통URL을 등록해주셔야 합니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">공통URL은 "http://<?=$_SERVER['HTTP_HOST']?><?=$cfg['rootDir']?>/order/card/easypay/easypay_noti.php" 입니다.</td></tr>
</table>
</div>
<script>cssRound('MSG02')</script>
<div class=title>현금영수증 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=29')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>
<table border=1 bordercolor=#e1e1e1 style="border-collapse:collapse" width=100%>
<col class=cellC><col class=cellL>
<tr>
	<td>현금영수증</td>
	<td class=noline>
	<div>
	<input type="radio" <?if($pg['receipt']=='Y'){echo "checked";}?> disabled />사용  
	<input type="radio" <?if($pg['receipt']=='N'){echo "checked";}?> disabled />미사용  
	<?if($pg['receipt']=='N'):?>
	미사용
	<?else:?>
	사용
	<?endif;?>
	<input type="hidden" name="pg[receipt]" value="<?=$pg[receipt]?>">
	</div>
	<font class=extext style="padding-left:5px">이지페이 현금영수증 이용은 이지페이 현금영수증 안내를 확인하시기 바랍니다. <a class="extext" style="font-weight:bold" href="http://www.easypay.co.kr/service_receipt.jsp" target="_blank">[바로가기]</a></font>
	</td>
</tr>
 
</table><p>

 
<div id=MSG03>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">현금영수증 사용여부 변경하기
</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><a href="/shop/admin/basic/easypay.php" class="extext" style="font-weight:bold" >[ 기본설정 > 통합전가결제걸정 ]</a> 페이지에서 해당 PG선택후 맨 아래에 "현금영수증" 사용여부 설정에서 변경 가능합니다. </td></tr>

</table>
</div>
<script>cssRound('MSG03')</script>

<div class=button>
<input type=image src="../img/btn_save.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>

</form>
<script>chkSettleKind();</script>