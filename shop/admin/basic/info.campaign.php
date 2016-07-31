<?
$location = "기본설정 > 회원정보 변경 관리 > 비밀번호 변경안내 설정";
include "../_header.php";

$info_cfg = $config->load('member_info');

if(!$info_cfg['campaign_use']) $info_cfg['campaign_use'] = 0;
if(!$info_cfg['campaign_period_type']) $info_cfg['campaign_period_type'] = 'm';
if(!$info_cfg['campaign_period_value']) $info_cfg['campaign_period_value'] = 3;
if(!$info_cfg['campaign_next_term']) $info_cfg['campaign_next_term'] = 7;


$checked['campaign_use'][$info_cfg['campaign_use']] = " checked";
$checked['campaign_period_type'][$info_cfg['campaign_period_type']] = " checked";
?>

<script>
function fnToggleDateType(v) {
	var f = document.frmCampaign;

	if (v == 'm') {
		$('campaign_period_value_d').writeAttribute('disabled',true);
		$('campaign_period_value_m').writeAttribute('disabled',false);
	}
	else {
		$('campaign_period_value_d').writeAttribute('disabled',false);
		$('campaign_period_value_m').writeAttribute('disabled',true);
	}

}

</script>
<form name="frmCampaign" method="post" action="indb.info.php">
<input type="hidden" name="mode" value="campaign">

<!-- 회원정보 수정 이벤트 -->
<div class="title title_top">
	비밀번호 변경안내 설정
	<span>고객의 개인정보 보호를 위한 정기적인 비빌번호변경 안내를 설정합니다.</span>
	<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=32')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a>
</div>

<div style="font-weight: bold; color: #ff0000; margin-bottom: 10px;">※ 사용 설정 할 경우 관리자 계정 보호를 위하여 관리자 로그인 시에도 비밀번호 변경안내가 적용됩니다.</div>

<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>사용 설정</td>
	<td class="noline">
		<input type="radio" name="campaign_use" value="1" <?=$checked['campaign_use'][1]?> /> <label for="info_notice_useyn1">사용</label>
		<input type="radio" name="campaign_use" value="0" <?=$checked['campaign_use'][0]?> /> <label for="info_notice_useyn0">사용 안함</label>

		<div class="extext_t">사용 설정시 고객이 로그인 했을때 비밀번호 변경을 위한 페이지가 안내 되어 보여집니다.</div>
	</td>
</tr>
<tr>
	<td>변경안내 주기</td>
	<td class="noline">
		최종 변경일 기준

		<input type="radio" name="campaign_period_type" value="d" <?=$checked['campaign_period_type']['d']?> onclick="fnToggleDateType(this.value);">
		<select name="campaign_period_value_d" id="campaign_period_value_d">
		<? for ($i=1;$i<=100;$i++) { ?>
		<option value="<?=$i?>" <?=($checked['campaign_period_type']['d'] && $i == $info_cfg['campaign_period_value']) ? 'selected' : ''?>><?=$i?></option>
		<? } ?>
		</select> 일

		<input type="radio" name="campaign_period_type" value="m" <?=$checked['campaign_period_type']['m']?> onclick="fnToggleDateType(this.value);">
		<select name="campaign_period_value_m" id="campaign_period_value_m">
		<? for ($i=1;$i<=12;$i++) { ?>
		<option value="<?=$i?>" <?=($checked['campaign_period_type']['m'] && $i == $info_cfg['campaign_period_value']) ? 'selected' : ''?>><?=$i?></option>
		<? } ?>
		</select> 개월

		<div class="extext_t">설정된 기간을 주기로 비밀번호 변경이 실행되기 전까지 로그인 때마다 안내 됩니다.</div>
	</td>
</tr>
<tr>
	<td>'다음에 변경' 시<br />재안내 설정</td>
	<td class="noline">
	'다음에 변경하기' 선택시
	<select name="campaign_next_term">
		<? for ($i=1;$i<=100;$i++) { ?>
		<option value="<?=$i?>"<?=(($info_cfg['campaign_next_term'] == $i) ? " selected" : "")?>><?=$i?></option>
		<? } ?>
	</select>
	일 동안 비밀번호 변경 안내를 하지 않습니다.

	<div class="extext_t">'다음에 변경하기' 선택시 위에 설정된 기간동안 안내되지 않고,<br />
	설정기간이 지난 후 부터는 비밀번호 변경이 실행되기 전까지 로그인 때마다 재안내 됩니다.</div>
	</td>
</tr>
</table>

<div class="extext_t">* 안내문구 수정은 <a href="../design/codi.php?design_file=member/password_campaign.htm" style="font-weight:bold;" class="extext">[ 디자인관리 > 디자인페이지 > 회원 > 회원비밀번호 변경안내 ]</a> 페이지 에서 문구 및 디자인 수정이 가능합니다.</div>

<div class="button">
	<input type="image" src="../img/btn_regist.gif">
	<a href="javascript:history.back();" onclick=";"><img src="../img/btn_cancel.gif" /></a>
</div>
</form>

<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle">고객의 개인정보 보호를 위한 정기적인 비빌번호변경 안내기능을 사용 할 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">변경 안내주기를 설정 할 수 있습니다. 회원별 최종 비밀번호 수정일을 기준으로하여 설정된 기간 주기가 경과한 회원에게 로그인 때마다 비밀번호변경 페이지가 안내 됩니다.</td></tr>
</table>
</div>
<script>
	window.onload = function() {
		cssRound('MSG01');
		fnToggleDateType('<?=$info_cfg['campaign_period_type']?>');
	}
</script>
<? include "../_footer.php"; ?>




