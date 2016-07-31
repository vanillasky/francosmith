<?

### settlebank 기본 세팅값
$_pg		= array(
			'id'	=> '',
			'zerofee'	=> 'no',
			'receipt'	=> 'N',
			'quota'		=> '00,02,03,04,05,06,07,08,09,10,11,12',
			);
$_escrow	= array(
			'use'		=> 'N',
			'min'		=> 50000,
			);

$location = "결제모듈연동 > 세틀뱅크 PG설정";
include "../_header.popup.php";
include "../../conf/config.pay.php";
include "../../conf/pg.$cfg[settlePg].php";
@include "../../conf/pg.escrow.php";


$pg = @array_merge($_pg,$pg);
$escrow = @array_merge($_escrow,$escrow);

if ($cfg[settlePg]=="settlebank") $spot = "<b style='color:#ff0000;padding-left:10px'>[사용중]</b>";
$checked[ssl][$pg[ssl]] = $checked[zerofee][$pg[zerofee]] = $checked[cert][$pg[cert]] = $checked[bonus][$pg[bonus]] = "checked";
$checked[escrow]['use'][$escrow['use']] = $checked[escrow][comp][$escrow[comp]] = $checked[escrow]['min'][$escrow['min']] = "checked";
$checked[receipt][$pg[receipt]] = "checked";

if ($set['use'][c]) $checked[c] = "checked";
if ($set['use'][o]) $checked[o] = "checked";
if ($set['use'][v]) $checked[v] = "checked";
if ($set['use'][h]) $checked[h] = "checked";

if ($escrow[c]) $checked[method][c] = "checked";
if ($escrow[o]) $checked[method][o] = "checked";
if ($escrow[v]) $checked[method][v] = "checked";
$checked[displayEgg][$cfg[displayEgg]+0] = "checked";
?>
<script language=javascript>
var arr=new Array('c','v','o','h');
function chkSettleKind(){
	var f = document.forms[0];

	var ret = false; var sk = false;
	for(var i=0;i < arr.length;i++)
	{
		sk = document.getElementsByName('set[use]['+arr[i]+']')[0];
		
		if(sk.checked == true){
			ret=true;
		}
		
	}
}
function chkFormThis(f){

	var ret = false;
	var sk = false;

	for(var i=0;i < arr.length;i++)
	{
		sk = document.getElementsByName('set[use]['+arr[i]+']')[0].checked;
		if(sk == true)ret=true;
	}

	return chkForm(f);
}
var IntervarId;

function resizeFrame()
{

    var oBody = document.body;
    var oFrame = parent.document.getElementById("pgifrm");
    var i_height = oBody.scrollHeight + (oFrame.offsetHeight-oFrame.clientHeight);
    oFrame.style.height = i_height+"px";

    if ( IntervarId ) clearInterval( IntervarId );
}

function notChange()
{
	alert('PG중앙화사용 PG는 결제 수단 설정을 변경할수없습니다.');	
}

window.onload = function(){
	resizeFrame()
}
</script>
<div class="title title_top">
세틀뱅크 PG 설정<span>신용카드 결제 및 기타결제방식은 반드시 전자결제서비스 업체와 계약을 맺으시기 바랍니다</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=39')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a>
</div>
<div id="settlebank_banner"><script>panel('settlebank_banner', 'pg');</script></div>
<form method=post action="indb.pg.php" enctype="multipart/form-data" onsubmit="return chkFormThis(this)">
<input type=hidden name=mode value="settlebank">
<input type=hidden name=cfg[settlePg] value="settlebank">
<input type=hidden name=cfg[settlePgPopup] value="On">
<input type=hidden name=pg[quota] value="">
<input type=hidden name=pg[zerofee] value="no">
<input type=hidden name=pg[zerofee_period] value="">
<input type=hidden name=pg[receipt] value="">

<div style="padding-top:15px"></div>
<table border=1 bordercolor=#e1e1e1 style="border-collapse:collapse" width=100%>
<col class=cellC><col class=cellL>
<tr>
	<td>PG사</td>
	<td><b>세틀뱅크(S'Pay) <?=$spot?></b></td>
</tr>
<tr>
	<td>결제수단 설정</td>
	<td class=noline>
<?	$use_name = array('c'=>'신용카드','o'=>'계좌이체','v'=>'가상계좌','h'=>'휴대폰');
	foreach( $use_name as $k => $v) {
		if($set[use_ck][$k] == 'on'){
			echo "<input type=checkbox name='set[use][$k]' ".$checked[$k]." >"; 
			echo $use_name[$k];
		} else {
			echo "<input type=checkbox name='set[use][$k]' ".$checked[$k]." style='background:#e3e3e3;' onclick='notChange();return false;'>";
			echo '<font style="background-color:#e3e3e3;margin:20px 0;">'.$use_name[$k].'</font>';
		}
	}
?> 
		<span style="margin-left:15px">※ 세틀뱅크에 신청한 결제수단만 선택하여 사용할 수 있습니다.</span>

	</td>
</tr>
<tr>
	<td class=ver8><b>세틀뱅크 Mid</td>
	<td><?=$pg[id]?></td>
</tr>
<tr>
	<td class=ver8><b>세틀뱅크 Key Code</b></td>
	<td><?=$pg[key]?></td>
</tr>
<tr>
	<td>할부기간</td>
	<td>※ 계약서 작성시 신청한 대로 설정되며, 변경시에는 세틀뱅크 고객센터로 요청하여 주십시오.</td>
</tr>
<tr>
	<td>무이자 여부</td>
	<td class=noline>※ 계약서 작성시 신청한 대로 설정되며, 변경시에는 세틀뱅크 고객센터로 요청하여 주십시오.</td>
</tr>
<tr>
	<td>무이자 기간</td>
	<td>※ 계약서 작성시 신청한 대로 설정되며, 변경시에는 세틀뱅크 고객센터로 요청하여 주십시오.</td>
</tr>
</table>
</div>

<div style="padding-top:5px"></div>

<div class=title>현금영수증 </div>
<table border=1 bordercolor=#e1e1e1 style="border-collapse:collapse" width=100%>
<col class=cellC><col class=cellL>
<tr>
	<td>현금영수증</td>
	<td class=noline>※ 계약서 작성시 신청한 대로 설정되며, 변경시에는 세틀뱅크 고객센터로 요청하여 주십시오.</font>
	</td>
</tr>
</table><p>

<div id=MSG03>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">세틀뱅크는 PG사 ID와 키값(Key Code)이 자동으로 설정됩니다. </td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">전자결제 신청후 구비서류를 팩스로 발송하시고 솔루션 설정을 기다려주세요.</td></tr>
</table>
</div>
<script>cssRound('MSG03','#F7F7F7')</script>



<div class=button>
<input type=image src="../img/btn_save.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>

</form>
<script>chkSettleKind();</script>
