<?
$pg_name = 'allatbasic';

### 올앳페이 기본 세팅값
$_pg_mobile		= array(
			'ssl'		=> 'NOSSL',
			'cert'		=> 'Y',
			'bonus'		=> 'N',
			'zerofee'	=> 'N',
			'receipt'	=> 'N',
			'quota'		=> '0:2:3:4:5:6:7:8:9:10:11:12',
			);

$location = "결제모듈연동 > 올앳PG 설정";
include "../_header.popup.php";
include "../../conf/config.pay.php";
if ($cfg[settlePg]=="allatbasic"){
	@include "../../conf/pg_mobile.$cfg[settlePg].php";
	@include "../../conf/pg.".$cfg['settlePg'].".php";
}

$pg_mobile = @array_merge($_pg_mobile,$pg_mobile);

if($cfg['settlePg']!="allatbasic") $pg_mobile = array(); //pg타입체크

if ($cfg[settlePg]=="allatbasic") $spot = "<b style='color:#ff0000;padding-left:10px'><img src=../img/btn_on_func.gif align=absmiddle></b>";
$checked[ssl][$pg_mobile[ssl]] = $checked[zerofee][$pg_mobile[zerofee]] = $checked[cert][$pg_mobile[cert]] = $checked[bonus][$pg_mobile[bonus]] = "checked";
$checked[receipt][$pg_mobile[receipt]] = "checked";

if($pg['pg-centersetting']=='Y'){ 
	$pgStatus = 'auto';
}
else{
	$pgStatus = 'menual';
}

// 프리픽스값
$prefix = 'GM|GP|GF';
?>
<script language=javascript>
var prefix = '<? echo $prefix;?>';
var arr=new Array('c','v','h');

function chkSettleKind(){
	var f = document.forms[0];

	<?if($pgStatus == 'auto' || $pgStatus == 'disable'){?>
		return false;
	<?}?>

	var ret = false;
	for(var i=0;i < arr.length;i++)
	{
		var sk = document.getElementsByName('set[use_mobile]['+arr[i]+']')[0].checked;
		if(sk == true)ret=true;
	}
	var robj =  new Array('pg[id]','pg[crosskey]','pg[quota]');

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
	var p_quota = document.getElementsByName('pg[quota]')[0];

	for(var i=0;i < arr.length;i++)
	{
		sk = document.getElementsByName('set[use_mobile]['+arr[i]+']')[0].checked;
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
			alert('올앳 KEY은 필수항목입니다.');
			return false;
		}
		if(!p_quota.value && ret){
			p_quota.focus();
			alert('일반할부기간은 필수항목입니다.');
			return false;
		}
		if(!chkPgid()){
			alert('올앳 PGID가 올바르지 않습니다.');
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

var oldId = "<?php echo $pg_mobile['id'];?>";
function openPrefix(){
	if(chkPgid()){
		alert("정상적인 올앳 PGID입니다.\n개별 승인 신청이 필요 없습니다.\n창을 닫고 올앳 PGID를 입력하세요!");
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
		parameters: "mode=getPginfo&pgtype=allatbasic&mobilepg=y&pgid="+pgid,
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
<?if($pgStatus == 'menual') {?>
	<div id=MSG01>
	<div class="small_ex">삼성올앳 모바일결제수단을 구매자에게 제공하기 위해서는 쇼핑몰(상점)의 삼성올앳 아이디를 추가로 발급 받으셔야 합니다.</div>
	<div class="small_ex">모바일결제용 아이디 발급 순서는 아래와 같습니다.</div>
	<div class="small_ex">신규 가맹점 : ①신규계약 진행 ②모바일결제용 아이디 추가 신청서 제출 (팩스: 02-3783-9833)</div>
	<div class="small_ex">기존 가맹점 : ①모바일결제용 아이디 추가 신청서 제출 (팩스: 02-3783-9833)</div>
	</div>
<script>cssRound('MSG01')</script>
<?}?>
<div style="padding-top:15"></div>

<div class="title title_top">
삼성올앳 모바일결제 설정<span>전자결제(PG)사로부터 제공받은 모바일결제 정보를 설정하여 구매자에게 신용카드 등의 모바일 결제수단을 제공할 수 있습니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=6')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a>
</div>

<form method=post action="indb.pg.php" onsubmit="return chkFormThis(this)">
<input type=hidden name=mode value="setPg">
<input type=hidden name=cfg[settlePg] value="allatbasic">

<table border=1 bordercolor=#e1e1e1 style="border-collapse:collapse" width=100%>
<col class=cellC><col class=cellL>
<tr>
	<td>PG사</td>
	<td class=noline><b>삼성올앳<!--(All@Pay™ Plus 2.0)--><?=$spot?></b></td>
</tr>
<tr>
	<td>모바일샵용<br/>결제수단 설정</td>
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

	<?if($pgStatus == 'menual'){?>&nbsp;&nbsp;&nbsp;<font class=extext><b>(반드시 올앳과 계약한 결제수단만 체크하세요)</b></font><?}?>
	</td>
</tr>

<tr>
	<td>올앳 <font color="#627dce">PG&nbsp;ID</font></td>
	<td>
	<?if($pgStatus == 'auto'){?>
		<div style="float:left"><b><?=$pg['id']?></b> <span class="extext"><b>자동설정 완료</b></span>
		</div>
	<?}
	else if($pgStatus == 'disable'){?>
		<span class="extext"><b>서비스를 신청하면  자동설정됩니다.</b></span>
	<?}
	else{?>
	<div style="float:left"><input type="text" name="pg[id]" class="lline" value="<?=$pg_mobile[id]?>" onkeyup="chkPgid()" onblur="chkPgid()" id="pgid"></div>
	<div style="float:left;padding:0 0 0 5" id="btPgId"><a href="javascript:openPrefix();"><img src="../img/pginfo.gif" alt="개별 승인 신청" /></a></div>
	<div style="clear:both" class="extext"><? echo str_replace('|',',', $prefix);?>로 시작되는 올앳 PGID만 직접 입력 가능합니다. (단, 기존 입력값은 무방합니다)</div>
	<div class="extext">고도몰 솔루션 이용자중 이전 버전을 사용하고 있어 위의 아이디로 시작하지 않는 경우에는 개별 승인 신청을 하셔야 합니다.</div>
	<?}?>
	</td>
</tr>
<tr>
	<td>올앳 <font color="#627dce">KEY</font></td>
	<td>
	<?if($pgStatus!='menual'){?>
	<?if(($pg['crosskey'])){ 
		echo "<b>".$pg['crosskey']."</b>&nbsp;<span class='extext'><b>자동설정 완료</b></span><br/>";
	 }?>
	<?}
	else{?>	
	<input type=text name=pg[crosskey] class=lline value="<?=$pg_mobile[crosskey]?>"> <font class=extext>CrossKey를 넣으세요
	<?}?>
	</td>
</tr>
<?
$pg_mobile_ssl = $sitelink->old_get_type();
?>
<input type=hidden name=pg[ssl] value="<?=$pg_mobile_ssl?>">
<tr>
	<td>일반할부기간</td>
	<td>
	<input type=text name=pg[quota] value="<?=$pg_mobile[quota]?>" class=lline>
	<span class=extext>ex) <?=$_pg_mobile[quota]?></span>
	</td>
</tr>
<tr>
	<td>무이자 여부</td>
	<td class=noline>
	<input type=radio name=pg[zerofee] value="N" <?=$checked[zerofee][N]?>> 일반결제
	<input type=radio name=pg[zerofee] value="Y" <?=$checked[zerofee][Y]?>> 무이자결제 <font class=extext><b>(무이자결제는 반드시 PG사와 계약체결 후에 사용해야 합니다!)</b></font></td>
</tr>
<tr>
	<td>인증 여부</td>
	<td class=noline>
	<input type=radio name=pg[cert] value="Y" <?=$checked[cert][Y]?>> 인증
	<input type=radio name=pg[cert] value="N" <?=$checked[cert][N]?>> 인증 사용않음
	</td>
</tr>
<tr>
	<td>보너스 포인트</td>
	<td class=noline>
	<input type=radio name=pg[bonus] value="Y" <?=$checked[bonus][Y]?>> 사용
	<input type=radio name=pg[bonus] value="N" <?=$checked[bonus][N]?>> 사용안함
	</td>
</tr>
</table>
<div style="padding-top:5"></div>
<div id="MSG02">
<?if($pgStatus == 'menual') {?>
<div class="small_ex">삼성올앳에 신청한 모바일결제수단 중 모바일샵에서 이용하고자하는 모바일결제수단을 체크 한 후</div>
<div class="small_ex">삼성올앳으로부터 제공받은 모바일결제용 ID와 CrossKey 정보를 입력하고 저장하세요.</div>
<div class="small_ex">설정 후 반드시 모바일샵에서 신용카드 등의 결제수단으로 정상적으로 결제가 이루어지는 테스트 해 주세요.</div>
<?}
else{?>
<div class="small_ex">전자결제 서비스를 신청하면 e나무 솔루션 PG ID가 자동으로 설정됩니다.</div>
<?}?>
<div style="padding-top:10px"></div>
<div class="small_ex"><strong> * 올앳 가상계좌 자동입금확인 설정</strong> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=6')"><img src="../img/btn_q.gif" border="0" align="absmiddle" /></a></div>
<div class="small_ex">올앳과 ‘가상계좌’ 결제수단이 계약되어 있는 상점이라면 ‘가상계좌입금 확인 설정’을 통해 편리하게 입금내역을 확인하실 수 있습니다.</div>
<div class="small_ex" style="padding-bottom:7px;">‘가상계좌 입금확인 설정’이란 고객이 가상계좌로 입금을 하게 되어 승인이 된 경우 입금내역승인결과값을 e나무 관리자 페이지로 보내어 해당주문건에 대하여 자동으로 ‘입금확인’ 처리가 되도록 할 수 있는 것입니다.</div>
<div class="small_ex">가상계좌 입금확인은 올앳 관리자에서 확인할 수 있지만 e나무 관리자에서 입금확인 정보를 자동으로 전송받으면 운영 시 편리하므로 연결 설정을  지원합니다.</div>
<div class="small_ex" style="padding-bottom:7px;">자세한 방법은 매뉴얼을 보고 따라해 주세요.</div>
<div class="small_ex">① 올앳의 ‘가상계좌’ 결제수단을 사용하는 쇼핑몰에게만 제공되므로 먼저 올앳에 가상계좌 신청이 되어 있는지 확인하시기 바랍니다.</div>
<div class="small_ex">② 올앳 관리자 로그인한 후, 마이페이지 >URL 전송  메뉴에서 상점ID(PG ID)를 선택, [가상계좌 입금확인 NOTI URL 신청]을 클릭하여 연결될 페이지 주소를 입력하고 저장합니다.</div>
<div class="small_ex small_ex_padding" style="font-weight: bold; color: #0174DF;">올앳 관리자에 입력할 주소 : http://쇼핑몰도메인/shop/order/card/allatbasic/allat_notiurl.php</div>
<div class="small_ex">③ 설정을 마치고 쇼핑몰 관리자 주문 페이지에서 자동 입금확인을 테스트해 보시기 바랍니다.</div>
<div class="small_ex small_ex_padding" style="font-weight: bold; color: #0174DF;">모바일샵용 PG ID 추가사용 시 입력할 주소 :  http://쇼핑몰도메인/shop/order/card/allatbasic/mobile/allat_mobile_notiurl.php</div>
<div class="small_ex">④ 설정을 마치고 쇼핑몰 관리자 주문 페이지에서 자동 입금확인을 테스트해 보시기 바랍니다.</div>
<div class="small_ex small_ex_padding">테스트 방법은 가상계좌로 주문을 한 후 해당 계좌로 입금을 한 뒤에 올앳 관리자 페이지에서의 입금여부와 e나무 관리자 페이지에서의 주문처리 상태가 ‘입금확인’으로 변경되었는지 확인하면 됩니다.</div>
</div>
<script>cssRound('MSG02')</script>

<div class=title>현금영수증 <!--span>설정된 PG사의 현금영수증을 사용하며, 별도 계약을 해야 함</span--> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=6')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>
<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class=cellC><col class=cellL>
<tr>
	<td>현금영수증</td>
	<td class="noline">
	<div>
	<input type=radio name=pg[receipt] value="N" <?=$checked[receipt][N]?> /> 사용안함
	<input type=radio name=pg[receipt] value="Y" <?=$checked[receipt][Y]?> /> 사용(먼저 삼성올앳에 현금영수증 사용을 신청한 후 선택하세요)
	</div>
	<div class="extext">모바일샵에서 구매한 고객에게 현금영수증을 제공하시고자 하면</div>
	<div class="extext">① 삼성올앳 모바일 아이디 추가 신청서 작성 시 현금영수증 신청을 신청한 후</div>
	<div class="extext">② 현재페이지에서 현금영수증 사용을 선택하시고 저장하세요.</div>
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