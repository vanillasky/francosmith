<?
$location = "�⺻���� > ȸ������ ���� ���� > ��й�ȣ ����ȳ� ����";
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

<!-- ȸ������ ���� �̺�Ʈ -->
<div class="title title_top">
	��й�ȣ ����ȳ� ����
	<span>���� �������� ��ȣ�� ���� �������� �����ȣ���� �ȳ��� �����մϴ�.</span>
	<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=32')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a>
</div>

<div style="font-weight: bold; color: #ff0000; margin-bottom: 10px;">�� ��� ���� �� ��� ������ ���� ��ȣ�� ���Ͽ� ������ �α��� �ÿ��� ��й�ȣ ����ȳ��� ����˴ϴ�.</div>

<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>��� ����</td>
	<td class="noline">
		<input type="radio" name="campaign_use" value="1" <?=$checked['campaign_use'][1]?> /> <label for="info_notice_useyn1">���</label>
		<input type="radio" name="campaign_use" value="0" <?=$checked['campaign_use'][0]?> /> <label for="info_notice_useyn0">��� ����</label>

		<div class="extext_t">��� ������ ���� �α��� ������ ��й�ȣ ������ ���� �������� �ȳ� �Ǿ� �������ϴ�.</div>
	</td>
</tr>
<tr>
	<td>����ȳ� �ֱ�</td>
	<td class="noline">
		���� ������ ����

		<input type="radio" name="campaign_period_type" value="d" <?=$checked['campaign_period_type']['d']?> onclick="fnToggleDateType(this.value);">
		<select name="campaign_period_value_d" id="campaign_period_value_d">
		<? for ($i=1;$i<=100;$i++) { ?>
		<option value="<?=$i?>" <?=($checked['campaign_period_type']['d'] && $i == $info_cfg['campaign_period_value']) ? 'selected' : ''?>><?=$i?></option>
		<? } ?>
		</select> ��

		<input type="radio" name="campaign_period_type" value="m" <?=$checked['campaign_period_type']['m']?> onclick="fnToggleDateType(this.value);">
		<select name="campaign_period_value_m" id="campaign_period_value_m">
		<? for ($i=1;$i<=12;$i++) { ?>
		<option value="<?=$i?>" <?=($checked['campaign_period_type']['m'] && $i == $info_cfg['campaign_period_value']) ? 'selected' : ''?>><?=$i?></option>
		<? } ?>
		</select> ����

		<div class="extext_t">������ �Ⱓ�� �ֱ�� ��й�ȣ ������ ����Ǳ� ������ �α��� ������ �ȳ� �˴ϴ�.</div>
	</td>
</tr>
<tr>
	<td>'������ ����' ��<br />��ȳ� ����</td>
	<td class="noline">
	'������ �����ϱ�' ���ý�
	<select name="campaign_next_term">
		<? for ($i=1;$i<=100;$i++) { ?>
		<option value="<?=$i?>"<?=(($info_cfg['campaign_next_term'] == $i) ? " selected" : "")?>><?=$i?></option>
		<? } ?>
	</select>
	�� ���� ��й�ȣ ���� �ȳ��� ���� �ʽ��ϴ�.

	<div class="extext_t">'������ �����ϱ�' ���ý� ���� ������ �Ⱓ���� �ȳ����� �ʰ�,<br />
	�����Ⱓ�� ���� �� ���ʹ� ��й�ȣ ������ ����Ǳ� ������ �α��� ������ ��ȳ� �˴ϴ�.</div>
	</td>
</tr>
</table>

<div class="extext_t">* �ȳ����� ������ <a href="../design/codi.php?design_file=member/password_campaign.htm" style="font-weight:bold;" class="extext">[ �����ΰ��� > ������������ > ȸ�� > ȸ����й�ȣ ����ȳ� ]</a> ������ ���� ���� �� ������ ������ �����մϴ�.</div>

<div class="button">
	<input type="image" src="../img/btn_regist.gif">
	<a href="javascript:history.back();" onclick=";"><img src="../img/btn_cancel.gif" /></a>
</div>
</form>

<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���� �������� ��ȣ�� ���� �������� �����ȣ���� �ȳ������ ��� �� �� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���� �ȳ��ֱ⸦ ���� �� �� �ֽ��ϴ�. ȸ���� ���� ��й�ȣ �������� ���������Ͽ� ������ �Ⱓ �ֱⰡ ����� ȸ������ �α��� ������ ��й�ȣ���� �������� �ȳ� �˴ϴ�.</td></tr>
</table>
</div>
<script>
	window.onload = function() {
		cssRound('MSG01');
		fnToggleDateType('<?=$info_cfg['campaign_period_type']?>');
	}
</script>
<? include "../_footer.php"; ?>




