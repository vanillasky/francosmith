<?
$location = "기본설정 > 회원정보 변경 관리 > 회원정보 수정 이벤트";
include "../_header.php";

$info_cfg = $config->load('member_info');

if(!$info_cfg['event_use']) $info_cfg['event_use'] = 0;
$checked['event_use'][$info_cfg['event_use']] = " checked";
?>

<form method="post" action="indb.info.php">
<input type="hidden" name="mode" value="event">


<!-- 회원정보 수정 이벤트 -->
<div class="title title_top">
	회원정보 수정 이벤트
	<span>고객/회원 정보 수정시 적립금지급 이벤트를 설정 할 수 있습니다.</span>
	<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=33')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a>
</div>

<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>사용 설정</td>
	<td class="noline">
	<input type="radio" name="event_use" value="1"<?=$checked['event_use'][1]?> /> <label for="info_event_useyn1">사용</label>
	<input type="radio" name="event_use" value="0"<?=$checked['event_use'][0]?> /> <label for="info_event_useyn0">사용 안함</label>

	<div class="extext_t">사용 설정시 고객/회원 정보 수정시 해당 고객에게 적립금이 자동으로 지급됩니다.</div>
	</td>
</tr>
<tr>
	<td>이벤트 기간 설정</td>
	<td class="noline">
	<?
	$tmp = explode(' ',$info_cfg['event_start_date']);
	$info_cfg['event_period_date_s'] = preg_replace('/[^0-9]/','',$tmp[0]);
	$info_cfg['event_period_time_s'] = substr($tmp[1],0,2);

	$tmp = explode(' ',$info_cfg['event_end_date']);
	$info_cfg['event_period_date_e'] = preg_replace('/[^0-9]/','',$tmp[0]);
	$info_cfg['event_period_time_e'] = substr($tmp[1],0,2);
	?>
	<input type="text" name="event_period_date_s" value="<?=$info_cfg['event_period_date_s']?>" onclick="calendar(event)" onkeydown="onlynumber()" class="cline" style="border:1px solid #cccccc; height:22px; padding-top:3px; padding-left:4px;text-align:center;" />
	<select name="event_period_time_s">
		<? for($i = 0; $i <= 23; $i++) { $tmpi = sprintf('%02d', $i); ?>
		<option value="<?=$tmpi?>"<?=(($tmpi == $info_cfg['event_period_time_s']) ? " selected" : "")?>><?=$tmpi?>시</option>
		<? } ?>
	</select>
	-
	<input type="text" name="event_period_date_e" value="<?=$info_cfg['event_period_date_e']?>" onclick="calendar(event)" onkeydown="onlynumber()" class="cline" style="border:1px solid #cccccc; height:22px; padding-top:3px; padding-left:4px;text-align:center;" />
	<select name="event_period_time_e">
		<? for($i = 0; $i <= 23; $i++) { $tmpi = sprintf('%02d', $i); ?>
		<option value="<?=$tmpi?>"<?=(($tmpi == $info_cfg['event_period_time_e']) ? " selected" : "")?>><?=$tmpi?>시</option>
		<? } ?>
	</select>

	<div class="extext_t">설정된 기간동안 정보를 수정한 고객/회원 에게 적립금이 지급됩니다.</div>
	</td>
</tr>
<tr>
	<td>적립금액 설정</td>
	<td class="noline">
	적립금
	<input type="text" name="event_emoney" value="<?=$info_cfg['event_emoney']?>" class="rline" style="border:1px solid #CCCCCC; height:22px; padding-top:3px; padding-left:4px; text-align:right;" />
	원 지급

	<div class="extext_t">설정된 금액이 정보를 수정한 고객/회원 에게 적립금으로 지급됩니다.</div>
	</td>
</tr>
</table>
<div class="extext_t">* 적립금 지급내역은 <a href="../member/batch.php?func=emoney" style="font-weight:bold;" class="extext">[회원관리 > 회원별 적립금내역 ]</a> 에서 확인 하실 수 있습니다.</div>


<div class="button">
	<input type="image" src="../img/btn_regist.gif">
	<a href="javascript:history.back();" onclick=";"><img src="../img/btn_cancel.gif" /></a>
</div>
</form>

<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle">고객/회원 정보 수정시 해당 고객에게 적립금이 자동으로 지급되는 이벤트를 설정 할 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">이벤트 설정일 이전에 가입한 회원에게만 적용되며 설정 기간동안 적립금은 1회만 지급됩니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">이벤트 설정을 변경 및 수정 후 [등록] 하시면 이전 이벤트는 사라지고 변경 및 수정된 이벤트로 재 설정 됩니다.</td></tr>
</table>
</div>
<script>
	window.onload = function() {
		cssRound('MSG01');
	}
</script>
<? include "../_footer.php"; ?>