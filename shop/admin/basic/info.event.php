<?
$location = "�⺻���� > ȸ������ ���� ���� > ȸ������ ���� �̺�Ʈ";
include "../_header.php";

$info_cfg = $config->load('member_info');

if(!$info_cfg['event_use']) $info_cfg['event_use'] = 0;
$checked['event_use'][$info_cfg['event_use']] = " checked";
?>

<form method="post" action="indb.info.php">
<input type="hidden" name="mode" value="event">


<!-- ȸ������ ���� �̺�Ʈ -->
<div class="title title_top">
	ȸ������ ���� �̺�Ʈ
	<span>��/ȸ�� ���� ������ ���������� �̺�Ʈ�� ���� �� �� �ֽ��ϴ�.</span>
	<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=33')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a>
</div>

<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>��� ����</td>
	<td class="noline">
	<input type="radio" name="event_use" value="1"<?=$checked['event_use'][1]?> /> <label for="info_event_useyn1">���</label>
	<input type="radio" name="event_use" value="0"<?=$checked['event_use'][0]?> /> <label for="info_event_useyn0">��� ����</label>

	<div class="extext_t">��� ������ ��/ȸ�� ���� ������ �ش� ������ �������� �ڵ����� ���޵˴ϴ�.</div>
	</td>
</tr>
<tr>
	<td>�̺�Ʈ �Ⱓ ����</td>
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
		<option value="<?=$tmpi?>"<?=(($tmpi == $info_cfg['event_period_time_s']) ? " selected" : "")?>><?=$tmpi?>��</option>
		<? } ?>
	</select>
	-
	<input type="text" name="event_period_date_e" value="<?=$info_cfg['event_period_date_e']?>" onclick="calendar(event)" onkeydown="onlynumber()" class="cline" style="border:1px solid #cccccc; height:22px; padding-top:3px; padding-left:4px;text-align:center;" />
	<select name="event_period_time_e">
		<? for($i = 0; $i <= 23; $i++) { $tmpi = sprintf('%02d', $i); ?>
		<option value="<?=$tmpi?>"<?=(($tmpi == $info_cfg['event_period_time_e']) ? " selected" : "")?>><?=$tmpi?>��</option>
		<? } ?>
	</select>

	<div class="extext_t">������ �Ⱓ���� ������ ������ ��/ȸ�� ���� �������� ���޵˴ϴ�.</div>
	</td>
</tr>
<tr>
	<td>�����ݾ� ����</td>
	<td class="noline">
	������
	<input type="text" name="event_emoney" value="<?=$info_cfg['event_emoney']?>" class="rline" style="border:1px solid #CCCCCC; height:22px; padding-top:3px; padding-left:4px; text-align:right;" />
	�� ����

	<div class="extext_t">������ �ݾ��� ������ ������ ��/ȸ�� ���� ���������� ���޵˴ϴ�.</div>
	</td>
</tr>
</table>
<div class="extext_t">* ������ ���޳����� <a href="../member/batch.php?func=emoney" style="font-weight:bold;" class="extext">[ȸ������ > ȸ���� �����ݳ��� ]</a> ���� Ȯ�� �Ͻ� �� �ֽ��ϴ�.</div>


<div class="button">
	<input type="image" src="../img/btn_regist.gif">
	<a href="javascript:history.back();" onclick=";"><img src="../img/btn_cancel.gif" /></a>
</div>
</form>

<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��/ȸ�� ���� ������ �ش� ������ �������� �ڵ����� ���޵Ǵ� �̺�Ʈ�� ���� �� �� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�̺�Ʈ ������ ������ ������ ȸ�����Ը� ����Ǹ� ���� �Ⱓ���� �������� 1ȸ�� ���޵˴ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�̺�Ʈ ������ ���� �� ���� �� [���] �Ͻø� ���� �̺�Ʈ�� ������� ���� �� ������ �̺�Ʈ�� �� ���� �˴ϴ�.</td></tr>
</table>
</div>
<script>
	window.onload = function() {
		cssRound('MSG01');
	}
</script>
<? include "../_footer.php"; ?>