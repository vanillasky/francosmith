<?

$location = "������ũ ���½�Ÿ�� ���� > ���÷��� ȯ�漳��";
include "../_header.php";

### ȯ�漳��
@include "../../conf/interpark.php";
$checked[ippSubmitYn][$inpkCfg[ippSubmitYn]] = "checked";

### Ư�̻���
ob_start();
@include "../../conf/interpark_spcaseEd.php";
$spcaseEd = ob_get_contents();
ob_end_clean();

?>

<div class="title title_top">���÷��� ȯ�漳�� <span>������ũ �������� �� ȯ���� ������ �� �ֽ��ϴ�.</div>

<div style="padding-top:5px"></div>


<div style="padding:8px 13px;background:#f7f7f7;border:3px solid #C6C6C6;margin-bottom:18px;" id="goodsInfoBox">
<div style="padding-top:2"><font color="#EA0095"><b>��������ũ�� ��������� ���� �� ������ �Ǿ�� �Ʒ� ���������� ���̰� �˴ϴ�.</b></font></div>
<div style="padding-top:2"><font color="#EA0095"><b>�����ݺ񱳵�Ͽ��� ���� ������ �Ǿ�� ���ݺ� ���޻���� ��ǰ������ �����մϴ�.</b></font></div>
<!--<div style="padding-top:2"><font  color=777777>������ũ�� ��������� ���� ���� ������ ��� ��ǰ���� �з��� ������ũ�з��� ��Ī���Ѿ߸� �մϴ�.</div>-->
</div>


<form method="post" action="../interpark/indb.php">
<input type="hidden" name="mode" value="set">
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>������ũ ��������</td>
	<td>
	<table>
	<col width="130">
	<tr><td colspan=2 height=5></td></tr>
	<tr>
		<td>�����ӵ�����</td>
		<td><font color=EA0095 face=verdana><b><?=($inpkCfg[domain] ? "<a href='http://{$inpkCfg[domain]}' target='_blank'><font color=EA0095>{$inpkCfg[domain]}</font></a>" : '��')?></b></font></td>
	</tr>
	<tr><td colspan=2 height=5></td></tr>
	<tr>
		<td>����ü��ȣ</td>
		<td><font color=EA0095 face=verdana><b><?=($inpkCfg[entrNo] ? $inpkCfg[entrNo] : '��')?></b></font></td>
	</tr>
	<tr><td colspan=2 height=5></td></tr>
	<tr>
		<td>�����ް���Ϸù�ȣ</td>
		<td><font color=EA0095 face=verdana><b><?=($inpkCfg[ctrtSeq] ? $inpkCfg[ctrtSeq] : '��')?></b></font></td>
	</tr>
	<tr><td colspan=2 height=5></td></tr>
	</table>

	<div style="color:#0074BA; padding:5 0 7 10px;" >
	(������ũ ���������� ������ũ�� ��ǰ�� ����� �� ���ǹǷ� �ݵ�� �ʿ��մϴ�.<br>
	'���񽺽���' �Ŀ��� ���������� �ݿ����� �ʾҴٸ� ������ũ�� �����ּ���.)
	</div>
	</td>
</tr>
<tr height=35>
	<td>���ݺ񱳵�Ͽ���</td>
	<td><input type="checkbox" name="inpkCfg[ippSubmitYn]" value="Y" <?=$checked[ippSubmitYn][Y]?> class=null> ������ũ�� ��ǰ�� ����� ��, ���ݺ� ���޻翡�� ��ǰ������ �ڵ����� �����մϴ�.</td>
</tr>
<tr>
	<td>Ư�̻���<br>(�ȳ�����)</td>
	<td>
	<textarea name=spcaseEd style="width:100%;height:250px" type=editor><?=htmlspecialchars($spcaseEd)?></textarea>
	<!-- �������� Ȱ��ȭ ��ũ��Ʈ -->
	<script src="../../lib/meditor/mini_editor.js"></script>
	<script>mini_editor("../../lib/meditor/");</script>

	<div style="color:#0074BA; padding:5 0 7 10px;" >
	(������ũ ��ǰ������������ "Ư�̻���" �ڳʿ� ��µǴ� �ȳ������Դϴ�.<br>
	��ǰ������ �ܿ� ���ǻ���, ���/��ȯ/��ǰ�ȳ� �� ������ ������ ������ �����մϴ�.)
	</div>
	</td>
</tr>
</table>

<div style="height:20px"></div>

<table cellpadding=0 cellspacing=0 width=650>
<tr><td align=center><input type=image src="../img/btn_confirm.gif" class=null></td>
</tr></table>

<div style="height:20px"></div>

</form>

<? include "../_footer.php"; ?>