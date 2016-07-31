<?

$location = "통계/데이터관리 > 에이스카운터 연동 관리";
include "../_header.php";

## 글로벌변수 $acecounter를 읽어온다. 
@include "../../conf/config.acecounter.php"; 

if (!$acecounter['status_apply']) $acecounter['status_apply'] = 'N'; 
if (!$acecounter['status_use']) $acecounter['status_use'] = 'N'; 
if (!$acecounter['use']) $acecounter['use'] = 'N'; 

$checked['use'][$acecounter['use']] = "checked"; 

if ($acecounter['status_use']!='Y' || !$acecounter['id'] || !$acecounter['pass']) 
	$disbled['use'] = "disabled"; 

$view_c_button = true; 	// 이커머스 신청버튼 보이기
$view_m_button = true; 	// 몰버전 신청버튼 보이기 
if ($acecounter['ver_use'] == 'm') 		$view_m_button = false; 
if ($acecounter['ver_use'] == 'c') 		$view_c_button = false; 
if ($acecounter['ver_apply'] == 'm' || $acecounter['ver_apply'] == 'c')	{
	$view_m_button = false; 
 	$view_c_button = false; 
 	$view_check_button = true; 
 } else {
 	$view_check_button = false; 
}

if ($acecounter['status_use'] == 'Y') {
	$acecounter_status = "등록: 버전("; 
	#
	if ($acecounter['ver_use']=='m') $version_msg = "몰버전"; 
	else if ($acecounter['ver_use']=='c') $version_msg = "이커머스"; 
	else $version_msg = $acecounter['ver_apply']; 
	#
	$acecounter_status .= $version_msg.")";
} else {
	$acecounter_status = "미등록"; 
}

$acecounter_status .= " ";
if ($acecounter['status_apply'] == 'Y') {
	$acecounter_status .= " ==> 신청중: 버전("; 
	#
	if ($acecounter['ver_apply']=='m') $version_msg = "몰버전"; 
	else if ($acecounter['ver_apply']=='c') $version_msg = "이커머스"; 
	else $version_msg = $acecounter['ver_apply']; 
	#
	$acecounter_status .= $version_msg.")";
} 
?>

<script>
function chkForm2(fm)
{
	/*
	if (fm.sessTime.value && fm.sessTime.value<20){
		alert("회원인증 유지시간 제한시 20분 이상만 가능합니다");
		fm.sessTime.value = 20;
		fm.sessTime.focus();
		return false;
	}
	*/
}

function applyAceCounter(ver) 
{
	document.form.mode.value = 'apply'; 
	document.form.version.value = ver; 
	document.form.submit(); 
}

function getResultAceCounter() 
{
	document.form.mode.value = 'get_result'; 
	document.form.submit(); 
}

function getPayResult() 
{
	document.form.mode.value = 'pay_result'; 
	document.form.submit(); 
}
</script>

<form name=form method=post action="indb_acecounter.php" onsubmit="return chkForm2(this)">
<input type=hidden name='mode' value="acecounter">
<input type=hidden name='version' >

<div class="title title_top">에이스카운터 신청정보<span>에이스카운터 서비스 신청정보를 입력해주세요</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=data&no=23')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>현재상태</td>
	<td><?=$acecounter_status?></td>
</tr>
<? if ($acecounter['status_use'] != 'Y' || $acecounter['status_apply'] == 'Y' ) { ?>
<tr>
	<td>서비스 신청</td>
	<td>
		<? if ($acecounter['status_use'] != 'Y') { ?>
	 		<input type='button' value='몰버전 신청' onclick="applyAceCounter('m')" />
	 	<? } ?>
	 	<? if ($acecounter['status_apply'] == 'Y') { ?>
			<input type='button' value='신청결과 확인' onclick="getResultAceCounter()" />
		<? } ?>
	</td>
</tr>
<? } else { ?>
<tr>
	<td>서비스 버전변경</td>
	<td> 
		서비스버전 변경은 에이스카운터 관리자에서 신청하시면 됩니다.
	</td>
</tr>
<? } ?>
<tr>
	<td>GCODE</td>
	<td><? if (!$acecounter['gcode']) echo "없음"; else echo $acecounter['gcode']; ?></td>	
</tr>
<tr>
	<td>연동 사용여부</td>
	<td class="noline">
		<input type="radio" name="acecounter[use]" value="Y" <?=$checked['use']['Y']?> <?=$disbled['use']?> />사용 <input type="radio" name="acecounter[use]" value="N" <?=$checked['use']['N']?> <?=$disbled['use']?> />사용안함
		<span class="small"><font class="extext">사용안함 으로 지정하시면, 에이스카운터에 로그가 쌓이지 않습니다.(트래픽이 발생하지 않음.)</font></span>
	</td>
</tr>
<tr>
	<td>사용기간</td>
	<td>
	<? if ($acecounter['status_use'] == 'Y') {
		?>
		<?=$acecounter['start']?>~<?=$acecounter['end']?>
		<?
	}
	?>
	</td>
</tr>
<tr>
	<td>최종결제금액</td>
	<td>
		<? if (strlen($acecounter['recent_pay']) > 0) { echo number_format($acecounter['recent_pay'])." 원"; } ?>
		<input type='button' value='최근 결제금액 조회' onclick="getPayResult()" />
	</td>
</tr>
</table>


<div class="button">
<input type=image src="../img/btn_register.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>

</form>


<div id="MSG01">
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">처음 신청완료 시, 무료사용기간이 할당됩니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">GCODE 는 에이스카운터 서비스 신청후 자동으로 부여받는 연동에 필요한 값입니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">사용기간이 경과한 경우, 에이스카운터 에 추가결제를 통해 기간을 연장해야  합니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">사용기간이 경과한 경우, 웹로그가 정상적으로 저장되지 않을 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">사용기간이 경과하지 않은 경우에도, 웹로그 트래픽이 사용기준을 초과한 경우, 웹로그가 비정상적으로 쌓이거나 추가결제 금액이 발생할 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">에이스카운터 서비스 사용중에도, [연동사용여부]를 '사용안함'으로 선택/등록하면, 에이스카운터로 웹로그가 쌓이지 않습니다. ( 웹 로그 트래픽도 발생하지 않습니다.) </td></tr>
</table>
</div>
<script>
cssRound('MSG01')
</script>
<? include "../_footer.php"; ?>