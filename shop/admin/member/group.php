<?
$location = "ȸ������ > ȸ���׷����";
include "../_header.php";

$member_grp = Core::loader('member_grp');
$grp_ruleset = $member_grp->ruleset;

# ������ ���� ����
$adminAuth = 0;
?>

<script type="text/javascript">
function fnManualEvaluate() {
	popupLayer('./indb.php?mode=manual_evaluate',500,500);

}
</script>
<form method="post" name="frmGroup" action="./indb.php" target="ifrmHidden">
<input type="hidden" name="mode" value="ruleset">

	<div class="title title_top">ȸ���׷����<span>ȸ������ ���� �ٸ� �׷��� ����� ����ȭ�� ���������� ������ �� �ֽ��ϴ� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=member&no=4');"><img src="../img/btn_q.gif" border="0" hspace="2" align="absmiddle" /></a></div>
		<table class=tb>
		<col class=cellC><col class=cellL>
		<tr>
			<td>�ڵ�/���� ��</td>
			<td class="noline">
			<label><input type="radio" name="automaticFl" value="y" <?=$grp_ruleset['automaticFl'] == 'y' ? 'checked' : ''?> />�ڵ� ��</label>
			<span class="snote extext">�Ʒ� ��� �����Ͽ� �ڵ����� �򰡵˴ϴ�.</span>
			<br/>
			<label><input type="radio" name="automaticFl" value="n" <?=$grp_ruleset['automaticFl'] != 'y' ? 'checked' : ''?> />���� ��</label>
			<span class="snote extext">�����ڰ� [���ϱ�] ��ư�� Ŭ���Ͽ� �������� ���մϴ�.</span>
			</td>
		</tr>
		<tr>
			<td class="noline"><label><input type="radio" name="apprSystem" value="figure" <?=$grp_ruleset['apprSystem'] != 'point' ? 'checked' : ''?> />���� ��ġ��</label></td>
			<td>
				���űݾ�, ����Ƚ��, �����ı�Ƚ���� ��� �����ϴ� �� ����
				<div class="snote extext" style="padding-top:5px;">����) ���űݾ� 30���� �̻�, ����Ƚ�� 1ȸ �̻�, �����ı� 1ȸ �̻��� ȸ���� �÷�Ƽ�� ���</div>
			</td>
		</tr>
		<tr>
			<td  class="noline" rowspan="3"><label><input type="radio" name="apprSystem" value="point" <?=$grp_ruleset['apprSystem'] == 'point' ? 'checked' : ''?> />���� ������</label></td>
			<td>
				<label style="width:110px;">"����"�� ��Ī <img src="../img/btn_question.gif" style="cursor:pointer;" class="godo-tooltip" tooltip="A��� : ������� 0����Ʈ ~ 10����Ʈ<br/>B��� : ������� 11����Ʈ ~ 20����Ʈ<br/>C��� : ������� 21����Ʈ �̻�" /></label>
				<input type="text" name="apprPointTitle" value="<?=$grp_ruleset['apprPointTitle']?>" class="input_text"/> <span class="snote extext">��) ��� ����, ��� ����, ��</span>
			</td>
		</tr>
		<tr>
			<td>
				<label style="width:110px;">"����Ʈ"�� ��Ī <img src="../img/btn_question.gif" style="cursor:pointer;" class="godo-tooltip" tooltip="A��� : ������� 0����Ʈ ~ 10����Ʈ<br/>B��� : ������� 11����Ʈ ~ 20����Ʈ<br/>C��� : ������� 21����Ʈ �̻�" /></label>
				<input type="text" name="apprPointLabel" value="<?=$grp_ruleset['apprPointLabel']?>" class="input_text"/> <span class="snote extext">��) ��, ����Ʈ, Point, P</span>
			</td>
		</tr>
		<tr>
			<td>
				<div style="width:660px;">
				<table class=tb>
				<col class=cellC style="width:100px;"><col class=cellL style="width:280px;"><col class=cellR style="width:280px;">
				<tr>
					<td></td>
					<td style="color:#333333; background:#f6f6f6; font-weight:bold; text-align:center;">�� ��ü</td>
					<td style="color:#333333; background:#f6f6f6; font-weight:bold; text-align:center;">����ϼ� �߰�����</td>
				</tr>
				<tr>
					<td>
						<label  class="noline" style="width:110px;"><input type="checkbox" name="apprPointOrderPrice" value="1" <?=$grp_ruleset['apprPointOrderPrice'] == 1 ? 'checked' : ''?> /> ���űݾ�</label>
					</td>
					<td>
						<input type="text" name="apprPointOrderPriceUnit" value="<?=$grp_ruleset['apprPointOrderPriceUnit']?>" size="8" /> ���� &nbsp;&nbsp;
						<input type="text" name="apprPointOrderPricePoint" value="<?=$grp_ruleset['apprPointOrderPricePoint']?>" size="10" /> ����Ʈ</span>
					</td>
					<td style="text-align:left;">
						<input type="text" name="mobile_apprPointOrderPriceUnit" value="<?=$grp_ruleset['mobile_apprPointOrderPriceUnit']?>" size="8" /> ���� &nbsp;&nbsp;
						<input type="text" name="mobile_apprPointOrderPricePoint" value="<?=$grp_ruleset['mobile_apprPointOrderPricePoint']?>" size="10" /> ����Ʈ</span>
					</td>
				</tr>
				<tr>
					<td>
						<label class="noline" style="width:110px;"><input type="checkbox" name="apprPointOrderRepeat" value="1" <?=$grp_ruleset['apprPointOrderRepeat'] == 1 ? 'checked' : ''?> /> ����Ƚ��</label>
					</td>
					<td>
						<span style="display:inline-block; width:100px;">���� 1ȸ��</span> <input type="text" name="apprPointOrderRepeatPoint" value="<?=$grp_ruleset['apprPointOrderRepeatPoint']?>" size="10" class="input_text terms_p02"/> ����Ʈ
					</td>
					<td style="text-align:left;">
						<span style="display:inline-block; width:100px;">���� 1ȸ��</span> <input type="text" name="mobile_apprPointOrderRepeatPoint" value="<?=$grp_ruleset['mobile_apprPointOrderRepeatPoint']?>" size="10" class="input_text terms_p02"/> ����Ʈ
					</td>
				</tr>
				<tr>
					<td>
						<label class="noline" style="width:110px;"><input type="checkbox" name="apprPointReviewRepeat" value="1" <?=$grp_ruleset['apprPointReviewRepeat'] == 1 ? 'checked' : ''?> /> �����ı�</label>
					</td>
					<td>
						<span style="display:inline-block; width:100px;">���� �ı��</span> <input type="text" name="apprPointReviewRepeatPoint" value="<?=$grp_ruleset['apprPointReviewRepeatPoint']?>" size="10" class="input_text terms_p03"/> ����Ʈ
					</td>
					<td style="text-align:left;">
						<span style="display:inline-block; width:100px;">���� �ı��</span> <input type="text" name="mobile_apprPointReviewRepeatPoint" value="<?=$grp_ruleset['mobile_apprPointReviewRepeatPoint']?>" size="10" class="input_text terms_p03"/> ����Ʈ
					</td>
				</tr>
				<tr>
					<td>
						<label  class="noline" style="width:110px;"><input type="checkbox" name="apprPointLoginRepeat" value="1" <?=$grp_ruleset['apprPointLoginRepeat'] == 1 ? 'checked' : ''?> /> �α��� Ƚ��</label>
					</td>
					<td>
						<span style="display:inline-block; width:100px;">1ȸ/�� �α��δ�</span> <input type="text" name="apprPointLoginRepeatPoint" value="<?=$grp_ruleset['apprPointLoginRepeatPoint']?>" size="10" class="input_text terms_p04"/> ����Ʈ
					</td>
					<td style="text-align:left;">
						<span style="display:inline-block; width:100px;">1ȸ/�� �α��δ�</span> <input type="text" name="mobile_apprPointLoginRepeatPoint" value="<?=$grp_ruleset['mobile_apprPointLoginRepeatPoint']?>" size="10" class="input_text terms_p04"/> ����Ʈ
					</td>
				</tr>
				</table>
				</div>
			</td>
		</tr>
		<tr>
			<td>����Ⱓ</td>
			<td>
				<label class="noline"><input type="radio" name="calcPeriodFl" value="n" <?=$grp_ruleset['calcPeriodFl'] != 'y' ? 'checked' : ''?>/>�Ⱓ���� ����</label><br/>
				<label class="noline"><input type="radio" name="calcPeriodFl" value="y" <?=$grp_ruleset['calcPeriodFl'] == 'y' ? 'checked' : ''?>/>�Ⱓ���� ����</label>
				<select name="calcPeriodBegin" class="input_text" style="padding:2px 0; font-size:11px;">
				<option value="-1d" <?=$grp_ruleset['calcPeriodBegin'] == '-1d' ? 'selected' : ''?>>����(����)</option>
				<option value="-1w" <?=$grp_ruleset['calcPeriodBegin'] == '-1w' ? 'selected' : ''?>>1������</option>
				<option value="-2w" <?=$grp_ruleset['calcPeriodBegin'] == '-2w' ? 'selected' : ''?>>2������</option>
				<option value="-1m" <?=$grp_ruleset['calcPeriodBegin'] == '-1m' ? 'selected' : ''?>>�Ѵ���</option>
				</select> ����
				<select name="calcPeriodMonth" class="input_text" style="padding:2px;">
				<option value="1" <?=$grp_ruleset['calcPeriodMonth'] == '1' ? 'selected' : ''?>>1</option>
				<option value="2" <?=$grp_ruleset['calcPeriodMonth'] == '2' ? 'selected' : ''?>>2</option>
				<option value="3" <?=$grp_ruleset['calcPeriodMonth'] == '3' ? 'selected' : ''?>>3</option>
				<option value="6" <?=$grp_ruleset['calcPeriodMonth'] == '6' ? 'selected' : ''?>>6</option>
				</select> ������
			</td>
		</tr>
		<tr>
			<td class="noline">��޻�����</td>
			<td>
				<select name="calcCycleMonth" class="input_text" style="padding:2px;">
				<option value="1" <?=$grp_ruleset['calcCycleMonth'] == '1' ? 'selected' : ''?>>1</option>
				<option value="2" <?=$grp_ruleset['calcCycleMonth'] == '2' ? 'selected' : ''?>>2</option>
				<option value="3" <?=$grp_ruleset['calcCycleMonth'] == '3' ? 'selected' : ''?>>3</option>
				<option value="6" <?=$grp_ruleset['calcCycleMonth'] == '6' ? 'selected' : ''?>>6</option>
				</select> ��������
				�ش��<select name="calcCycleDay" class="input_text" style="padding:2px;">
				<? for ($i=1;$i<=31;$i++) { ?>
				<option value="<?=$i?>" <?=$grp_ruleset['calcCycleDay'] == $i ? 'selected' : ''?>><?=$i?></option>
				<? } ?></select>��
			</td>
		</tr>
		<tr>
			<td class="noline">�����Ⱓ</td>
			<td>
				��� �����Ϻ���
				<select name="calcKeep" class="input_text" style="padding:2px;">
				<option value="0" >����</option>
				<option value="1" <?=$grp_ruleset['calcKeep'] == '1' ? 'selected' : ''?>>1</option>
				<option value="2" <?=$grp_ruleset['calcKeep'] == '2' ? 'selected' : ''?>>2</option>
				<option value="3" <?=$grp_ruleset['calcKeep'] == '3' ? 'selected' : ''?>>3</option>
				<option value="6" <?=$grp_ruleset['calcKeep'] == '6' ? 'selected' : ''?>>6</option>
				</select> ����
			</td>
		</tr>
		</table>

		<? if ($grp_ruleset['automaticFl'] != 'y') { ?>
			<div class="button">
				<a href="javascript:void(0);" onClick="fnManualEvaluate();"><img src="../img/admin_btn_appraisal.gif"></a>
			</div>
		<? } ?>
		<div class="title title_top">ȸ���׷� ���� ���� ����<span>���������� ���콺 ������ ����Ǵ� ������ ��뿩�θ� �����մϴ�. <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=member&no=4');"><img src="../img/btn_q.gif" border="0" hspace="2" align="absmiddle" /></a></div>
		<table class=tb>
		<col class=cellC><col class=cellL>
		<tr>
			<td>��뿩�� ����</td>
			<td class="noline">
			<label><input type="radio" name="useMypageLayerBox" value="y" <?=$grp_ruleset['useMypageLayerBox'] == 'y' ? 'checked' : ''?> />�����</label>

			<label><input type="radio" name="useMypageLayerBox" value="n" <?=$grp_ruleset['useMypageLayerBox'] != 'y' ? 'checked' : ''?> />������</label>
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
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />�׷캰�� ���� �ٸ� ���� �������� �����Ű�� �� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />�����ڴ� �⺻������ ���θ��� ȸ���� �Ǹ� 100 ������ �׷췹���� ���Ե˴ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />�����ڸ� �߰��Ϸ��� ȸ�������� �ش�ȸ���� ������ �׷����� �����Ͻø� �˴ϴ�.</td></tr>
</table>
</div>
<script type="text/javascript" src="../godo_ui.js"></script>
<script>cssRound('MSG01');</script>

<? include "../_footer.php"; ?>
