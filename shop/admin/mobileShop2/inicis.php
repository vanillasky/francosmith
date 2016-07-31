<?
$pg_name = 'inicis';
### 이니시스 기본 세팅값
$_pg_mobile		= array(
			'zerofee'	=> 'no',
			'receipt'	=> 'N',
			'quota'		=> '선택:일시불:2개월:3개월:4개월:5개월:6개월:7개월:8개월:9개월:10개월:11개월:12개월',
			);

$location = "결제모듈연동 > 이니시스PG 설정";
include "../_header.popup.php";
include "../../conf/config.pay.php";
if ($cfg[settlePg]=="inicis"){
	@include "../../conf/pg.$cfg[settlePg].php";
	$pg_mobile = $pg;
	@include "../../conf/pg.".$cfg['settlePg'].".php";
}


if($cfg['settlePg']!="inicis") $pg_mobile = array(); //pg타입체크

if ($cfg[settlePg]=="inicis" && $pg_mobile['id']) $spot = "<b style='color:#ff0000;padding-left:10px'><img src=../img/btn_on_func.gif align=absmiddle></b>";
$checked[ssl][$pg_mobile[ssl]] = $checked[zerofee][$pg_mobile[zerofee]] = $checked[cert][$pg_mobile[cert]] = $checked[bonus][$pg_mobile[bonus]] = "checked";
$checked[receipt][$pg_mobile[receipt]] = "checked";

if($cfg['settlePg'] != $pg_name){
	$pgStatus = 'menual';
}
else if($pg['pg-centersetting']=='Y'){ 
	$pgStatus = 'auto';
}
else{
	$pgStatus = 'menual';
}

if ($cfg[settlePg]=="inicis"){

	$dir = "../../order/card/inicis/key/";

	if (is_dir($dir.$pg_mobile[id])){
		$od = opendir($dir.$pg_mobile[id]);
		while ($rd=readdir($od)){
			if (!ereg("\.$",$rd)) $fls[pg][] = $rd;
		}
	}

}

?>
<script language=javascript>

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
	var robj =  new Array('pg[id]');

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
	<?if($pgStatus == 'menual'){?>
	for(var i=0;i < arr.length;i++)
	{
		sk = document.getElementsByName('set[use_mobile]['+arr[i]+']')[0].checked;
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
	return;
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
		parameters: "mode=getPginfo&pgtype=inicis&mobilepg=y&pgid="+pgid,
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
	var pattern = /^(GODO)/;
	var pattern2 = /^(GDP)/;
	if(pattern2.test(obj.value) || pattern.test(obj.value) || (oldId == obj.value && oldId)){
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
<div class="small_ex">이니시스 모바일결제수단을 구매자에게 제공하기 위해서는 신청서에서 모바일 결제수단 이용을 체크하시면 됩니다.</div>
<div class="small_ex">신규 가맹점 : ①신규계약 진행 ②신청서에서 모바일 결제수단 사용 체크 후 제출</div>
<div class="small_ex">기존 가맹점 : ①신청서에서 모바일 결제수단 사용 체크 후 제출</div>
</div>
<script>cssRound('MSG01')</script>
<?}?>
<div style="font:0;height:5"></div>

<div class="title title_top">이니시스 모바일결제 설정<span>전자결제(PG)사 신청서에 모바일 결제수단 이용을 체크하여 구매자에게 신용카드 등의 모바일 결제수단을 제공할 수 있습니다.</span>
</div>
<form method=post action="indb.pg.php" enctype="multipart/form-data">
<input type=hidden name=mode value="setPg">
<input type=hidden name=cfg[settlePg] value="inicis">
<table border=1 bordercolor=#e1e1e1 style="border-collapse:collapse" width=100%>
<col class=cellC><col class=cellL>
<tr>
	<td class=ver8><b>PG사</b></td>
	<td><b>이니시스 (INIPay V4.110) <?=$spot?></b></td>
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

	<?if($pgStatus == 'menual'){?>&nbsp;&nbsp;&nbsp;<font class=extext><b>(반드시 이니시스와 계약한 결제수단만 체크하세요)</b></font><?}?>
	</td>
</tr>
<tr>
	<td><b>이니시스 <font color="#627dce">PG&nbsp;ID</font></b></td>
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
	<div style="float:left"><input type="text" name="pg[id]" class="lline" value="<?=$pg_mobile[id]?>" onkeyup="chkPgid()" onblur="chkPgid()" id="pgid" disabled="disabled"></div>
	<div style="float:left;padding:0 0 0 5" id="btPgId"><a href="javascript:openPrefix();"><img src="../img/pginfo.gif" alt="개별 승인 신청" /></a></div>
	<div style="clear:both" class="extext">GODO,GDP로 시작되는 INIPay ID만 직접 입력 가능합니다. (단, 기존 입력값은 무방합니다)</div>
	<div class="extext">고도몰 솔루션 이용자중 이전 버전을 사용하고 있어 위의 아이디로 시작하지 않는 경우에는 개별 승인 신청을 하셔야 합니다.</div>
	<?}?>
	</td>
</tr>
<?php for ($i=1; $i<=3; $i++){ ?>
<tr>
	<td><b>이니시스 <font color="#627dce">Key <?php echo $i;?></b></font></td>
	<td class="ver8"><?if($pgStatus == 'menual'){?><input type="file" name="pg[file_0<?php echo $i;?>]" class="lline" /><?}?> <?php echo $fls['pg'][$i-1];?>
	<?if($pgStatus == 'auto'){?><span class="extext"><b>자동설정 완료</b><?}?>
	</td>
</tr>
<?php } ?>
<tr>
	<td height=50>일반할부기간</td>
	<td>
	<input type=text name=pg[quota] value="<?=$pg_mobile[quota]?>" class=lline style="width:500px" disabled="disabled">
	<div class=extext  style="padding-top:5px">ex) <?=$_pg_mobile[quota]?></div>
	</td>
</tr>
<tr>
	<td>무이자 여부</td>
	<td class=noline>
	<input type=radio name=pg[zerofee] value="no" checked disabled="disabled"> 일반결제
	<input type=radio name=pg[zerofee] value="yes" <?=$checked[zerofee][yes]?> disabled="disabled"> 무이자결제 <font class=extext><b>(무이자결제는 반드시 PG사와 계약체결 후에 사용해야 합니다!)</b> (아래 '무이자 기간' 사용시 체크)</font></td>
</tr>
<tr>
	<td height=92>무이자 기간</td>
	<td><input type=text name=pg[zerofee_period] value="<?=$pg_mobile[zerofee_period]?>" class=lline style="width:500px" disabled="disabled">
	<div style="padding-top:7px"><font class=extext >* 카드사코드 :  01 (외환), 03 (롯데/(구)동양), 04 (현대), 06 (국민), 11 (BC), 12 (삼성), 13 (LG), 14 (신한)</div>
	<div style="padding-top:3px">ex) 비씨카드 3개월 / 6개월 할부와 삼성카드 3개월 무이자 적용시 ⇒ 11-3:6,12-3 라고 입력</div>
	<div style="padding-top:3px">ex) 모든카드에 대해서 3개월 / 6개월 무이자 적용시 ⇒ ALL-3:6 라고 입력</div>
	<div style="padding:3px 0 7px 0">* 무이자 기간을 사용하려면 반드시 위의 무이자결제를 체크하세요!</div></td>
</tr>
</table>
<div id=MSG02>
<div class="small_ex">이니시스는 기존의 발급 받으신 설정 정보를 모바일결제에서도 그대로 이용하게 됩니다.</div>
<div class="small_ex">그러므로 이니시스 신청서내 모바일결제수단 이용을 체크하신 쇼핑몰은</div>
<div class="small_ex">반드시 모바일샵에서 신용카드 등의 결제수단으로 정상적으로 결제가 이루어지는 테스트 해 주세요</div>
</div>
<script>cssRound('MSG02')</script>

<div class=title>현금영수증 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=7')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>
<table border=1 bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class=cellC><col class=cellL>
<tr>
	<td>현금영수증</td>
	<td class=noline>
	<div>
	<input type=radio name=pg[receipt] value="N" <?=$checked[receipt][N]?>> 사용안함
	<input type=radio name=pg[receipt] value="Y" <?=$checked[receipt][Y]?>> 사용(먼저 이니시스에 현금영수증 사용을 신청한 후 선택하세요)
	</div>
	<div class="extext">모바일샵에서 구매한 고객에게 현금영수증을 제공하시고자 하면</div>
	<div class="extext">① 이니시스 상점관리자페이지에서 별도 신청한 후</div>
	<div class="extext">② 현재페이지에서 현금영수증 사용을 선택하시고 저장하세요.</div>

	</td>
</tr>
</table><p>
</div>

<div class=button>
<input type=image src="../img/btn_save.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>

</form>
<script>chkSettleKind();</script>