<?php
$pg_name = 'agspay';

### 올더게이트 기본 세팅값
$_pg_mobile		= array(
			'id'		=> '',
			'zerofee'	=> 'no',
			'receipt'	=> 'N',
			'quota'		=> '0:2:3:4:5:6:7:8:9:10:11:12',
			);

$location = '결제모듈연동 > 올더게이트PG 설정';
include '../_header.popup.php';
include '../../conf/config.pay.php';
if ($cfg[settlePg] == 'agspay'){
	@include '../../conf/pg.'.$cfg['settlePg'].'.php';
	$pg_mobile = $pg;
	@include "../../conf/pg.".$cfg['settlePg'].".php";
}

$pg = @array_merge($_pg_mobile,$pg);

if($cfg['settlePg']!="agspay") $pg_mobile = array(); //pg타입체크

//설정모드
if($cfg['settlePg'] != $pg_name){
	$pgStatus = 'disable';
}
else if($pg['pg-centersetting']=='Y'){ 
	$pgStatus = 'auto';
}
else{
	$pgStatus = 'menual';
}

if ($cfg[settlePg]=="agspay" && $pg_mobile['id']) $spot = "<b style='color:#ff0000;padding-left:10px'><img src=../img/btn_on_func.gif align=absmiddle></b>";
$checked[ssl][$pg_mobile[ssl]] = $checked[zerofee][$pg_mobile[zerofee]] = $checked[cert][$pg_mobile[cert]] = $checked[bonus][$pg_mobile[bonus]] = "checked";
$checked[receipt][$pg_mobile[receipt]] = "checked";

// 프리픽스값
$prefix = 'gda|gdfp|gdf';
?>
<script language="javascript">
<!--
var prefix = '<? echo $prefix;?>';
var arr = new Array('c','v','h');	//신용카드, 가상계좌, 휴대폰
var pgStatus = '<?=$pgStatus?>';
function chkSettleKind()
{
	var f = document.forms[0];

	var ret = false;
	for (var i=0; i < arr.length; i++) {
		var sk = document.getElementsByName('set[use_mobile]['+arr[i]+']')[0].checked;
		if (sk == true) ret = true;
	}

	if(pgStatus == 'auto' || pgStatus == 'disable'){
		return false;
	}

	var robj =  new Array('pg[id]','pg[quota]');

	for (var i=0; i < robj.length; i++) {
		var obj = document.getElementsByName(robj[i])[0];
		if (ret) {
			obj.style.background = '#ffffff';
			obj.readOnly = false;
		} else {
			obj.style.background = '#e3e3e3';
			obj.readOnly = true;
		}
	}
}

function chkFormThis(f)
{
	var ret = false;
	var sk = false;
	var p_id = document.getElementsByName('pg[id]')[0];
	var p_quota = document.getElementsByName('pg[quota]')[0];

	for (var i=0; i < arr.length; i++) {
		sk = document.getElementsByName('set[use_mobile]['+arr[i]+']')[0].checked;
		if (sk == true) ret = true;
	}
	
	if(pgStatus == 'menual'){
		if (!p_id.value && ret) {
			p_id.focus();
			alert('올더게이트 PGID는 필수항목입니다.');
			return false;
		}

		if(!chkPgid()){
			alert('올더게이트 PGID가 올바르지 않습니다.');
			return false;
		}
	}

	if (!p_quota.value && ret) {
		p_quota.focus();
		alert('일반할부기간은 필수항목입니다.');
		return false;
	}

	return chkForm(f);
}
var IntervarId;

function resizeFrame()
{
	var oBody = document.body;
	var oFrame = parent.document.getElementById('pgifrm');
	var i_height = oBody.scrollHeight + (oFrame.offsetHeight-oFrame.clientHeight);
	oFrame.style.height = i_height;

	if ( IntervarId ) clearInterval( IntervarId );
}

var oldId = "<?php echo $pg_mobile['id'];?>";
function openPrefix(){
	if(chkPgid()){
		alert("정상적인 올더게이트 PGID입니다.\n개별 승인 신청이 필요 없습니다.\n창을 닫고 올더게이트 PGID를 입력하세요!");
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
		parameters: "mode=getPginfo&pgtype=allthegate&mobilepg=y&pgid="+pgid,
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
	if (pgStatus == 'disable')
	{
		alert('사용 중인 PG가 아닙니다.');
		return;
	}
	ifrmHidden.location.href = '../basic/pgSettingUpdate.php';
}

window.onload = function(){
	resizeFrame();
}
//-->
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
올더게이트PG 설정<span>신용카드 결제 및 기타결제방식은 반드시 전자지불서비스 업체와 계약을 맺으시기 바랍니다</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=27')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a>
</div>

<form method="post" action="indb.pg.php" enctype="multipart/form-data" onsubmit="return chkFormThis(this)">
<input type="hidden" name="mode" value="setPg">
<input type="hidden" name="cfg[settlePg]" value="agspay">

<?if($pgStatus == 'menual'){?>
<!-- PG 설정 -->
<div id="MSG01">
	<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
	<tr><td>올더게이트에서 제공하는 신용카드,계좌이체,가상계좌,핸드폰의 결제수단을 방문자(소비자)에게 제공하기 위해서</td></tr>
	<tr><td>올더게이트에서 <b>메일로 받으신 올더게이트 PGID를 입력</b>하신후 본 페이지 하단의 저장버튼을 클릭해 주세요.</td></tr>
	<tr><td>아직 올더게이트와 계약을 하지 않으셨다면</td></tr>
	<tr><td style="padding-left:10">①<u>온라인신청 하신후</u></td></tr>
	<tr><td style="padding-left:10">②<u>계약서류를 우편으로 올더게이트에 보내</u>주세요 <a href="../basic/pg.intro.php" target="_blank" style="color:#ffffff;font-weight:bold">[계약 상세안내]</a></td></tr>
	</table>
</div>
<script>cssRound('MSG01')</script>
<?}?>


<div style="padding-top:15"></div>

<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class="cellC"><col class="cellL">
<tr>
	<td>PG사</td>
	<td>올더게이트 (AGSPay V4.0 for PHP) <?=$spot?></td>
</tr>
<tr>
	<td>결제수단 설정</td>
	<td class="noline">
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
			else{
				if(empty($pg['sub_cpid']) && $key=='h'){	//휴대폰결제인데 sub_cpid가 없는경우
					$disabled[$key] = 'disabled';	
					$labelColor[$key] = "style='color:#cccccc'";
				}
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
	<?if($pgStatus == 'menual'){?>&nbsp;&nbsp;&nbsp;<span class="extext"><b>(반드시 올더게이트과 계약한 결제수단만 체크하세요)</b></span><?}?>
	</td>
</tr>
<tr>
	<td>올더게이트 <font color="#627dce">PG&nbsp;ID</font></td>
	<td>
		<?if($pgStatus == 'auto'){?>
			<div style="float:left"><b><?=$pg['id']?></b> <span class="extext"><b>자동설정 완료</b></span>
			</div>
		<?}
		else{?>
			<div style="float:left"><input type="text" name="pg[id]" class="lline" value="<?=$pg['id']?>" onkeyup="chkPgid()" onblur="chkPgid()" id="pgid" disabled="disabled"></div>
			<div style="float:left;padding:0 0 0 5" id="btPgId"><a href="javascript:openPrefix();"><img src="../img/pginfo.gif" alt="개별 승인 신청" /></a></div>
			<div style="clear:both" class="extext"><? echo str_replace('|',',', $prefix);?>로 시작되는 올더게이트 PGID만 직접 입력 가능합니다. (단, 기존 입력값은 무방합니다)</div>
			<div class="extext">고도몰 솔루션 이용자중 이전 버전을 사용하고 있어 위의 아이디로 시작하지 않는 경우에는 개별 승인 신청을 하셔야 합니다.</div>
		<?}?>
	</td>
</tr>
<tr>
	<td>일반할부기간</td>
	<td>
	<input type="text" name="pg[quota]" value="<?=$pg_mobile['quota']?>" class="lline" disabled="disabled">
	<div class="extext" style="padding-top:5px">결제창에 표시되는 할부기간을 강제 지정하여 원하지 않는 할부 거래를 제한할 수 있습니다.<br/>ex) <?=$pg_mobile[quota]?></div>
	</td>
</tr>
<tr>
	<td>무이자 여부</td>
	<td class="noline">
	<label><input type="radio" name="pg[zerofee]" value="no" checked  disabled="disabled" /> 일반결제</label>
	<label><input type="radio" name="pg[zerofee]" value="yes" <?=$checked[zerofee][yes]?>  disabled="disabled" /> 무이자결제</label> <span class="extext"><b>(무이자결제는 반드시 PG사와 계약체결 후에 사용해야 합니다!)</b> (아래 '무이자 기간' 사용시 체크)</span>
	</td>
</tr>
<tr>
	<td>무이자 기간</td>
	<td>
	<input type="text" name="pg[zerofee_period]" value="<?=$pg_mobile['zerofee_period']?>" class="lline" style="width:500px" disabled="disabled">
	<a href="javascript:popupLayer('../basic/popup.agspay.php',450,500)"><img src="../img/btn_carddate.gif" align="absmiddle"></a>
	<div class="small extext">ex) 모든 할부거래를 무이자로 하고 싶을때에는 ALL로 설정<br/>ex) 국민,외환카드 특정개월수만 무이자를 하고 싶을경우 샘플(2:3:4:5:6개월) → 200-2:3:4:5:6,300-2:3:4:5:6</div>
	</td>
</tr>
<tr>
	<td>휴대폰 SUB_CPID</td>
	<td>
		<?if($pgStatus!='menual'){?>
			<? if(($pg['sub_cpid'])){ 
				echo "<b>".$pg['sub_cpid']."</b>&nbsp;<span class='extext'><b>자동설정 완료</b></span><br/>";
			 }?>
			<div class="small extext">휴대폰 결제를 신청하여 승인완료 메일을 받으면 위의 [결제수단 새로고침]을 클릭하십시오. 휴대폰 SUB_CPID가 자동으로 설정됩니다.</div>
		<?}
		else{?>
		<input type="text" name="pg[sub_cpid]" class="lline" value="<?=$pg['sub_cpid']?>">
		<div class="small extext">휴대폰 결제를 신청하여 메일로 받으신 휴대폰 SUB_CPID를 입력합니다.</div>
		<?}?>
	</td>
</tr>
</table>

<div style="padding-top:5px"></div>
<div id="MSG02">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<?if($pgStatus == 'menual'){?>
	<tr><td><img src="../img/icon_list.gif" align="absmiddle">PG사와 계약을 맺은 이후에는 메일로 받으신 실제 올더게이트 PGID를 넣으시면 됩니다.</td></tr>
	<tr><td><img src="../img/icon_list.gif" align="absmiddle">PG사의 결제정보 설정후 고객님께서 카드결제 테스트를 꼭 해보시기 바랍니다.</td></tr>
<?}else{?>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">전자결제 서비스를 신청하면 e나무 솔루션에 PG ID가 자동으로 설정됩니다. </td></tr>
<?}?>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">간혹 PG사를 통해 카드승인된 값을 받지못하여 주문관리페이지에서 입금확인으로 자동변경되지 않을수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">반드시 주문관리페이지의 주문상태와 PG사에서 제공하는 관리자화면내의 카드승인내역도 동시에 확인해 주십시요.</td></tr>
</table>
</div>
<script>cssRound('MSG02')</script>
<!-- //PG 설정 -->

<!-- 현금영수증 설정 -->
<div class=title>현금영수증 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=27')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>
<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class=cellC><col class=cellL>
<tr>
	<td>현금영수증</td>
	<td class=noline>
	<div>
	<?if($pg['receipt']=='N'):?>
	미사용
	<?else:?>
	사용
	<?endif;?>
	<input type="hidden" name="pg[receipt]" value="<?=$pg[receipt]?>">
	</div>
	<div class="extext" style="padding-left:5px">모바일샵에서 구매한 고객에게 현금영수증 제공 여부입니다.</div>
	<div class="extext" style="padding-left:5px">쇼핑몰 기본관리 > 통합 전자결제 설정 > 올더게이트에서 설정한</div>
	<div class="extext" style="padding-left:5px">현금영수증 사용여부 설정과 동일합니다. </div>
	<BR><span class=extext style="padding-left:5px">올더게이트 현금영수증 이용은 올더게이트 현금영수증 안내를 확인하시기 바랍니다. <a class="extext" style="font-weight:bold" href="http://www.allthegate.com/ags/add/add_08.jsp" target="_blank">[바로가기]</a></span>
	</td>
</tr>
</table>

<div style="padding-top:5px"></div>
<div id="MSG04">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle">소비자는 2008. 7. 1일부터 현금영수증 발급대상금액이 5천원이상에서 1원이상으로 변경되어 5천원 미만의 현금거래도 현금영수증을 요청하여 발급 받을 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">현금영수증 사용 체크시 무통장, 계좌이체, 가상계좌 결제에 대해서 소비자가 신청한 현금영수증이 발급 됩니다</td></tr>
</table>
</div>
<script>cssRound('MSG04')</script>
<!-- //현금영수증 설정 -->

<div class="button">
<input type="image" src="../img/btn_save.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>

</form>

<script>chkSettleKind();</script>