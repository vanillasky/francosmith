<?
$location = "회원관리 > 회원그룹관리";
include "../_header.php";

$member_grp = Core::loader('member_grp');
$grp_ruleset = $member_grp->ruleset;

# 관리자 권한 설정
$adminAuth = 0;
?>

<script type="text/javascript">
function fnManualEvaluate() {
	popupLayer('./indb.php?mode=manual_evaluate',500,500);

}
</script>
<form method="post" name="frmGroup" action="./indb.php" target="ifrmHidden">
<input type="hidden" name="mode" value="ruleset">

	<div class="title title_top">회원그룹관리<span>회원별로 각각 다른 그룹을 만들어 차별화된 할인혜택을 제공할 수 있습니다 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=member&no=4');"><img src="../img/btn_q.gif" border="0" hspace="2" align="absmiddle" /></a></div>
		<table class=tb>
		<col class=cellC><col class=cellL>
		<tr>
			<td>자동/수동 평가</td>
			<td class="noline">
			<label><input type="radio" name="automaticFl" value="y" <?=$grp_ruleset['automaticFl'] == 'y' ? 'checked' : ''?> />자동 평가</label>
			<span class="snote extext">아래 등급 산정일에 자동으로 평가됩니다.</span>
			<br/>
			<label><input type="radio" name="automaticFl" value="n" <?=$grp_ruleset['automaticFl'] != 'y' ? 'checked' : ''?> />수동 평가</label>
			<span class="snote extext">관리자가 [평가하기] 버튼을 클릭하여 수동으로 평가합니다.</span>
			</td>
		</tr>
		<tr>
			<td class="noline"><label><input type="radio" name="apprSystem" value="figure" <?=$grp_ruleset['apprSystem'] != 'point' ? 'checked' : ''?> />실적 수치제</label></td>
			<td>
				구매금액, 구매횟수, 구매후기횟수를 모두 만족하는 평가 기준
				<div class="snote extext" style="padding-top:5px;">예시) 구매금액 30만원 이상, 구매횟수 1회 이상, 구매후기 1회 이상인 회원을 플래티넘 등급</div>
			</td>
		</tr>
		<tr>
			<td  class="noline" rowspan="3"><label><input type="radio" name="apprSystem" value="point" <?=$grp_ruleset['apprSystem'] == 'point' ? 'checked' : ''?> />실적 점수제</label></td>
			<td>
				<label style="width:110px;">"점수"의 명칭 <img src="../img/btn_question.gif" style="cursor:pointer;" class="godo-tooltip" tooltip="A등급 : 등급지수 0포인트 ~ 10포인트<br/>B등급 : 등급지수 11포인트 ~ 20포인트<br/>C등급 : 등급지수 21포인트 이상" /></label>
				<input type="text" name="apprPointTitle" value="<?=$grp_ruleset['apprPointTitle']?>" class="input_text"/> <span class="snote extext">예) 등급 기준, 등급 지수, 평가</span>
			</td>
		</tr>
		<tr>
			<td>
				<label style="width:110px;">"포인트"의 명칭 <img src="../img/btn_question.gif" style="cursor:pointer;" class="godo-tooltip" tooltip="A등급 : 등급지수 0포인트 ~ 10포인트<br/>B등급 : 등급지수 11포인트 ~ 20포인트<br/>C등급 : 등급지수 21포인트 이상" /></label>
				<input type="text" name="apprPointLabel" value="<?=$grp_ruleset['apprPointLabel']?>" class="input_text"/> <span class="snote extext">예) 점, 포인트, Point, P</span>
			</td>
		</tr>
		<tr>
			<td>
				<div style="width:660px;">
				<table class=tb>
				<col class=cellC style="width:100px;"><col class=cellL style="width:280px;"><col class=cellR style="width:280px;">
				<tr>
					<td></td>
					<td style="color:#333333; background:#f6f6f6; font-weight:bold; text-align:center;">샵 전체</td>
					<td style="color:#333333; background:#f6f6f6; font-weight:bold; text-align:center;">모바일샵 추가실적</td>
				</tr>
				<tr>
					<td>
						<label  class="noline" style="width:110px;"><input type="checkbox" name="apprPointOrderPrice" value="1" <?=$grp_ruleset['apprPointOrderPrice'] == 1 ? 'checked' : ''?> /> 구매금액</label>
					</td>
					<td>
						<input type="text" name="apprPointOrderPriceUnit" value="<?=$grp_ruleset['apprPointOrderPriceUnit']?>" size="8" /> 원당 &nbsp;&nbsp;
						<input type="text" name="apprPointOrderPricePoint" value="<?=$grp_ruleset['apprPointOrderPricePoint']?>" size="10" /> 포인트</span>
					</td>
					<td style="text-align:left;">
						<input type="text" name="mobile_apprPointOrderPriceUnit" value="<?=$grp_ruleset['mobile_apprPointOrderPriceUnit']?>" size="8" /> 원당 &nbsp;&nbsp;
						<input type="text" name="mobile_apprPointOrderPricePoint" value="<?=$grp_ruleset['mobile_apprPointOrderPricePoint']?>" size="10" /> 포인트</span>
					</td>
				</tr>
				<tr>
					<td>
						<label class="noline" style="width:110px;"><input type="checkbox" name="apprPointOrderRepeat" value="1" <?=$grp_ruleset['apprPointOrderRepeat'] == 1 ? 'checked' : ''?> /> 구매횟수</label>
					</td>
					<td>
						<span style="display:inline-block; width:100px;">구매 1회당</span> <input type="text" name="apprPointOrderRepeatPoint" value="<?=$grp_ruleset['apprPointOrderRepeatPoint']?>" size="10" class="input_text terms_p02"/> 포인트
					</td>
					<td style="text-align:left;">
						<span style="display:inline-block; width:100px;">구매 1회당</span> <input type="text" name="mobile_apprPointOrderRepeatPoint" value="<?=$grp_ruleset['mobile_apprPointOrderRepeatPoint']?>" size="10" class="input_text terms_p02"/> 포인트
					</td>
				</tr>
				<tr>
					<td>
						<label class="noline" style="width:110px;"><input type="checkbox" name="apprPointReviewRepeat" value="1" <?=$grp_ruleset['apprPointReviewRepeat'] == 1 ? 'checked' : ''?> /> 구매후기</label>
					</td>
					<td>
						<span style="display:inline-block; width:100px;">구매 후기당</span> <input type="text" name="apprPointReviewRepeatPoint" value="<?=$grp_ruleset['apprPointReviewRepeatPoint']?>" size="10" class="input_text terms_p03"/> 포인트
					</td>
					<td style="text-align:left;">
						<span style="display:inline-block; width:100px;">구매 후기당</span> <input type="text" name="mobile_apprPointReviewRepeatPoint" value="<?=$grp_ruleset['mobile_apprPointReviewRepeatPoint']?>" size="10" class="input_text terms_p03"/> 포인트
					</td>
				</tr>
				<tr>
					<td>
						<label  class="noline" style="width:110px;"><input type="checkbox" name="apprPointLoginRepeat" value="1" <?=$grp_ruleset['apprPointLoginRepeat'] == 1 ? 'checked' : ''?> /> 로그인 횟수</label>
					</td>
					<td>
						<span style="display:inline-block; width:100px;">1회/일 로그인당</span> <input type="text" name="apprPointLoginRepeatPoint" value="<?=$grp_ruleset['apprPointLoginRepeatPoint']?>" size="10" class="input_text terms_p04"/> 포인트
					</td>
					<td style="text-align:left;">
						<span style="display:inline-block; width:100px;">1회/일 로그인당</span> <input type="text" name="mobile_apprPointLoginRepeatPoint" value="<?=$grp_ruleset['mobile_apprPointLoginRepeatPoint']?>" size="10" class="input_text terms_p04"/> 포인트
					</td>
				</tr>
				</table>
				</div>
			</td>
		</tr>
		<tr>
			<td>산출기간</td>
			<td>
				<label class="noline"><input type="radio" name="calcPeriodFl" value="n" <?=$grp_ruleset['calcPeriodFl'] != 'y' ? 'checked' : ''?>/>기간제한 없음</label><br/>
				<label class="noline"><input type="radio" name="calcPeriodFl" value="y" <?=$grp_ruleset['calcPeriodFl'] == 'y' ? 'checked' : ''?>/>기간제한 있음</label>
				<select name="calcPeriodBegin" class="input_text" style="padding:2px 0; font-size:11px;">
				<option value="-1d" <?=$grp_ruleset['calcPeriodBegin'] == '-1d' ? 'selected' : ''?>>직전(어제)</option>
				<option value="-1w" <?=$grp_ruleset['calcPeriodBegin'] == '-1w' ? 'selected' : ''?>>1주일전</option>
				<option value="-2w" <?=$grp_ruleset['calcPeriodBegin'] == '-2w' ? 'selected' : ''?>>2주일전</option>
				<option value="-1m" <?=$grp_ruleset['calcPeriodBegin'] == '-1m' ? 'selected' : ''?>>한달전</option>
				</select> 부터
				<select name="calcPeriodMonth" class="input_text" style="padding:2px;">
				<option value="1" <?=$grp_ruleset['calcPeriodMonth'] == '1' ? 'selected' : ''?>>1</option>
				<option value="2" <?=$grp_ruleset['calcPeriodMonth'] == '2' ? 'selected' : ''?>>2</option>
				<option value="3" <?=$grp_ruleset['calcPeriodMonth'] == '3' ? 'selected' : ''?>>3</option>
				<option value="6" <?=$grp_ruleset['calcPeriodMonth'] == '6' ? 'selected' : ''?>>6</option>
				</select> 개월간
			</td>
		</tr>
		<tr>
			<td class="noline">등급산정일</td>
			<td>
				<select name="calcCycleMonth" class="input_text" style="padding:2px;">
				<option value="1" <?=$grp_ruleset['calcCycleMonth'] == '1' ? 'selected' : ''?>>1</option>
				<option value="2" <?=$grp_ruleset['calcCycleMonth'] == '2' ? 'selected' : ''?>>2</option>
				<option value="3" <?=$grp_ruleset['calcCycleMonth'] == '3' ? 'selected' : ''?>>3</option>
				<option value="6" <?=$grp_ruleset['calcCycleMonth'] == '6' ? 'selected' : ''?>>6</option>
				</select> 개월마다
				해당월<select name="calcCycleDay" class="input_text" style="padding:2px;">
				<? for ($i=1;$i<=31;$i++) { ?>
				<option value="<?=$i?>" <?=$grp_ruleset['calcCycleDay'] == $i ? 'selected' : ''?>><?=$i?></option>
				<? } ?></select>일
			</td>
		</tr>
		<tr>
			<td class="noline">유지기간</td>
			<td>
				등급 산정일부터
				<select name="calcKeep" class="input_text" style="padding:2px;">
				<option value="0" >없음</option>
				<option value="1" <?=$grp_ruleset['calcKeep'] == '1' ? 'selected' : ''?>>1</option>
				<option value="2" <?=$grp_ruleset['calcKeep'] == '2' ? 'selected' : ''?>>2</option>
				<option value="3" <?=$grp_ruleset['calcKeep'] == '3' ? 'selected' : ''?>>3</option>
				<option value="6" <?=$grp_ruleset['calcKeep'] == '6' ? 'selected' : ''?>>6</option>
				</select> 개월
			</td>
		</tr>
		</table>

		<? if ($grp_ruleset['automaticFl'] != 'y') { ?>
			<div class="button">
				<a href="javascript:void(0);" onClick="fnManualEvaluate();"><img src="../img/admin_btn_appraisal.gif"></a>
			</div>
		<? } ?>
		<div class="title title_top">회원그룹 정보 노출 설정<span>마이페이지 마우스 오버시 노출되는 고객정보 사용여부를 설정합니다. <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=member&no=4');"><img src="../img/btn_q.gif" border="0" hspace="2" align="absmiddle" /></a></div>
		<table class=tb>
		<col class=cellC><col class=cellL>
		<tr>
			<td>사용여부 설정</td>
			<td class="noline">
			<label><input type="radio" name="useMypageLayerBox" value="y" <?=$grp_ruleset['useMypageLayerBox'] == 'y' ? 'checked' : ''?> />사용함</label>

			<label><input type="radio" name="useMypageLayerBox" value="n" <?=$grp_ruleset['useMypageLayerBox'] != 'y' ? 'checked' : ''?> />사용안함</label>
			</td>
		</tr>
		</table>

		<div class="button">
			<input type=image src="../img/btn_register.gif">
		</div>

		<div style="padding-top:15px"></div>
</form>

<?
include "_groupForm.php";
?>
<div id="MSG01">
<table cellpadding="2" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />그룹별로 각기 다른 가격 할인율을 적용시키실 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />관리자는 기본적으로 쇼핑몰의 회원이 되며 100 레벨의 그룹레벨을 갖게됩니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />관리자를 추가하려면 회원가입후 해당회원을 관리자 그룹으로 지정하시면 됩니다.</td></tr>
</table>
</div>
<script type="text/javascript" src="../godo_ui.js"></script>
<script>cssRound('MSG01');</script>

<? include "../_footer.php"; ?>
